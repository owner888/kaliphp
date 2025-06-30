<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       https://doc.kaliphp.com
 */

namespace kaliphp\lib;

use kaliphp\log;
use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Analysers\DocBlockParser;

/**
 * 重写 TokenAnalyser 类的几个主要方法
 */
class cls_swg_analyser extends TokenAnalyser
{
    /**
     * The next non-whitespace, non-comment token.
     *
     * @param array   $tokens
     * @param Context $context
     *
     * @return string|array The next token (or false)
     */
    private function nextToken(&$tokens, $context)
    {
        while (true) {
            $token = next($tokens);
            if ( isset($token[0]) && $token[0] === T_WHITESPACE) {
                continue;
            }

            if ( 
                isset($token[1]) && 
                is_string($token[1]) && 
                preg_match('#\[swg\](?<context>.*)\*\s*\[/swg\]#isU', $token[1], $mat) 
            ) 
            {
                $token[1] = $this->parseUserTemplate($mat['context']);
                // return $token;
            }


            if ( isset($token[0]) && $token[0] === T_COMMENT) {
                $pos = strpos($token[1], '@OA\\');
                if ($pos) {
                    $line = $context->line ? $context->line + $token[2] : $token[2];
                    $commentContext = new Context(['line' => $line], $context);
                    log::warning('Annotations are only parsed inside `/**` DocBlocks, skipping ' . $commentContext);
                }
                continue;
            }
      
            return $token;
        }
    }

    private function parseUserTemplate($template)
    {
        $context = [];
        preg_match_all('#@\s*(?<keyword>(path|param|return|desc|tags|title)+)(?<args>((?!\*\s*@).)+)#ism', $template, $mat);
        // 先把关键字拿到
        foreach ($mat['keyword'] as $k => $v)
        {
            $mat['args'][$k] = str_replace(['"', "'"], '', $mat['args'][$k]);
            if ( in_array($v, ['param', 'return']) ) 
            {
                $context[$v][] = $mat['args'][$k];
            }
            else
            {
                $context[$v] = $mat['args'][$k];
            }
        }

        //模版
        $tpls = [
            'path'     => '*@OA\\%s(path="%s", %s',
            // POST 参数模版
            'param'    => <<<EOT
                *    @OA\Property(
                *        property="%s",
                *        type="%s",
                *        description="%s"
                *        %s
                *    ),
EOT,
            // GET 参数模版
            'param_get' => <<<EOT
                *   @OA\Parameter(name="%s",
                *     in="query",
                *     description="%s",
                *     %s
                *     @OA\Schema(type="%s")
                *   ),
EOT,
            // 返回值模版
            'return'    => <<<EOT
                *     @OA\Response(
                *         response="%s",
                *         description="(%s) %s"
                *     ),
EOT,
        ];

        $group    = null;
        $swg_data = ["/**"];
        if ( isset($context['path']) ) 
        {
            preg_match('#(?<method>[^\s]+)\s+(?<url>[^\s]+)\s*(?<need_login>[^\s\*]+)?#', $context['path'], $m);
            $need_login = !isset($m['need_login']) || trim($m['need_login']) != 'false';
            $method     = ucfirst(strtolower($m['method']));
            $swg_data[] = sprintf(
                $tpls['path'], $method, $m['url'], 
                $need_login ? 'security={{"token": {}}},' : ''
            );
        }

        // 所属标签
        if ( isset($context['tags']) ) 
        {
            $swg_data[] = '* tags={"' .$context['tags']. '"},';
        }

        if ( isset($context['title']) ) 
        {
            $swg_data[] = sprintf('summary="%s",', trim($context['title']));
        }

        if ( isset($context['desc']) ) 
        {
            $swg_data[] = sprintf('description="%s",', '<pre>' . $context['desc'] . '</pre>');
        }

        // 分析参数
        if ( isset($context['param']) ) 
        {
            if ( 'Post' == $method ) 
            {
                $swg_data[] = <<<EOT
                *     @OA\RequestBody(
                *         @OA\MediaType(
                *             mediaType="application/json",
                *             @OA\Schema(
EOT;
            }

            $required = [];
            $pattern  = '#(?<type>[^\s]+)\s+\$?(?<name>[^\s]+)\s*(?<desc>[^@]*)#';
            foreach ($context['param'] as $item)
            {
                if ( preg_match($pattern, $item, $m) ) 
                {
                    @list($var, $def) = explode(':', $m['name'], 2);
                    $extr = [];
                    $extr[] = sprintf('default="%s"', $def ?? '');

                    $extr_pattern = '#(?<key>[^\s=]+)=["\']?(?<val>[^\s=]+)["\']?#';
                    if ( 
                        isset($m['desc']) && 
                        preg_match_all($extr_pattern, $m['desc'], $od) 
                    ) 
                    {
                        foreach ($od['key'] as $key => $field)
                        {
                            if ( 'Post' == $method && 'required' == $field ) 
                            {
                                $required[] = $var;
                            }

                            $extr[] = sprintf('%s="%s"', $field, $od['val'][$key]);
                        }
                    }

                    $desc = trim(preg_replace($extr_pattern, '', $m['desc']));
                    if ( 'Get' == $method ) 
                    {
                        $swg_data[] = sprintf(
                            $tpls['param_get'], $var, $desc, 
                            $extr ? implode(',', $extr) . ',' : '', $m['type']
                        );
                    }
                    else
                    {
                        $swg_data[] = sprintf(
                            $tpls['param'], $var, $m['type'], $desc, 
                            $extr ? ',' . implode(',', $extr) : ''
                        );
                    }
                }
            }
       
            if ( 'Post' == $method ) 
            {
                // post 的 required 写法不一样
                if ( $required ) 
                {
                    $swg_data[] = sprintf('* required={"%s"},', implode('","', $required));
                }

                $swg_data[] = <<<EOT
                *             )
                *         )
                *     ),
EOT;
            }

        }
        // var_dump($swg_data);exit;
        $swg_data[] = <<<EOT
        *     @OA\Response(
        *         response=200,
        *         description="code 为0表示响应成功, 非0表示响应失败",
        *         @OA\JsonContent(ref="#/components/schemas/response"),
        *     ),
EOT;
    
        // 分析返回值
        if ( $context['return'] ) 
        {
            $pattern = '#(?<type>[^\s]+)\s+\$?(?<name>[^\s]+)\s+(?<desc>.*)#ism';
            foreach ($context['return'] as $item)
            {
                if ( preg_match($pattern, $item, $m) ) 
                {
                    if ( 'format' == $m['type'] ) 
                    {
                        // 暂时先不用
                        // $is_create_data = true;
                        // $rules = cls_data_format::data([], trim($m['desc']), 'swagger', $is_create_data);
                        // if ( $rules ) 
                        // {
                        //     $m['desc'] = util::json_encode($rules);
                        // }
                    }

                    $m['desc'] = str_replace('*', ' ', $m['desc']);
                    $m['desc'] = str_replace(PHP_EOL.'    ', PHP_EOL, $m['desc']);
                    $swg_data[] = sprintf(
                        $tpls['return'], 
                        $m['name'], 
                        $m['type'], 
                        '<pre>'.str_replace(["\"",'    '], ["'", '&nbsp;&nbsp;'], $m['desc']) . '</pre>'
                    );
                }
            }
        }

        $swg_data[] = <<<EOT
        * )
        */
EOT;
        
        return implode("\n", $swg_data);
    }

    /**
     * Parse a use statement.
     *
     * @param (int|mixed)[]|string $token
     */
    private function parseUseStatement(array &$tokens, &$token, Context $parseContext): array
    {
        $normalizeAlias = function ($alias): string {
            $alias = ltrim($alias, '\\');
            $elements = explode('\\', $alias);

            return array_pop($elements);
        };

        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;
        $nsToken = array_merge([T_STRING, T_NS_SEPARATOR], $this->php8NamespaceToken());
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            $isNameToken = in_array($token[0], $nsToken);
            if (!$explicitAlias && $isNameToken) {
                $class .= $token[1];
                $alias = $token[1];
            } elseif ($explicitAlias && $isNameToken) {
                $alias .= $token[1];
            } elseif ($token[0] === T_AS) {
                $explicitAlias = true;
                $alias = '';
            } elseif ($token === ',') {
                $statements[$normalizeAlias($alias)] = $class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } elseif ($token === ';') {
                $statements[$normalizeAlias($alias)] = $class;
                break;
            } else {
                break;
            }
        }

        return $statements;
    }

    /**
     * Shared implementation for parseFile() & parseContents().
     *
     * @param array $tokens The result of a token_get_all()
     */
    protected function fromTokens(array $tokens, Context $parseContext): Analysis
    {
        $generator = $this->generator ?: new Generator();
        $analysis = new Analysis([], $parseContext);
        $docBlockParser = new DocBlockParser($generator->getAliases());

        reset($tokens);
        $token = '';

        $aliases = $generator->getAliases();

        $parseContext->uses = [];
        // default to parse context to start with
        $schemaContext = $parseContext;

        $classDefinition = false;
        $interfaceDefinition = false;
        $traitDefinition = false;
        $enumDefinition = false;
        $comment = false;

        $line = 0;
        $lineOffset = $parseContext->line ?: 0;

        while ($token !== false) {
            $previousToken = $token;
            $token = $this->nextToken($tokens, $parseContext);

            if (is_array($token) === false) {
                // Ignore tokens like "{", "}", etc
                continue;
            }

            if (defined('T_ATTRIBUTE') && $token[0] === T_ATTRIBUTE) {
                // consume
                $this->parseAttribute($tokens, $token, $parseContext);
                continue;
            }

            if ($token[0] === T_DOC_COMMENT) {
                if ($comment) {
                    // 2 Doc-comments in succession?
                    $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
                }
                $comment = $token[1];
                $line = $token[2] + $lineOffset;
                continue;
            }

            if (in_array($token[0], [T_ABSTRACT, T_FINAL])) {
                // skip
                $token = $this->nextToken($tokens, $parseContext);
            }

            if ($token[0] === T_CLASS) {
                // Doc-comment before a class?
                if (is_array($previousToken) && $previousToken[0] === T_DOUBLE_COLON) {
                    // php 5.5 class name resolution (i.e. ClassName::class)
                    continue;
                }

                $token = $this->nextToken($tokens, $parseContext);

                if (is_string($token) && ($token === '(' || $token === '{')) {
                    // php7 anonymous classes (i.e. new class() { public function foo() {} };)
                    continue;
                }

                if (is_array($token) && ($token[1] === 'extends' || $token[1] === 'implements')) {
                    // php7 anonymous classes with extends (i.e. new class() extends { public function foo() {} };)
                    continue;
                }

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $interfaceDefinition = false;
                $traitDefinition = false;
                $enumDefinition = false;

                $schemaContext = new Context(['class' => $token[1], 'line' => $token[2]], $parseContext);
                if ($classDefinition) {
                    $analysis->addClassDefinition($classDefinition);
                }
                $classDefinition = [
                    'class' => $token[1],
                    'extends' => null,
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                $token = $this->nextToken($tokens, $parseContext);

                if ($token[0] === T_EXTENDS) {
                    $schemaContext->extends = $this->parseNamespace($tokens, $token, $parseContext);
                    $classDefinition['extends'] = $schemaContext->fullyQualifiedName($schemaContext->extends);
                }

                if ($token[0] === T_IMPLEMENTS) {
                    $schemaContext->implements = $this->parseNamespaceList($tokens, $token, $parseContext);
                    $classDefinition['implements'] = array_map([$schemaContext, 'fullyQualifiedName'], $schemaContext->implements);
                }

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-class and reset $schemaContext
            }

            if ($token[0] === T_INTERFACE) { // Doc-comment before an interface?
                $classDefinition = false;
                $traitDefinition = false;
                $enumDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['interface' => $token[1], 'line' => $token[2]], $parseContext);
                if ($interfaceDefinition) {
                    $analysis->addInterfaceDefinition($interfaceDefinition);
                }
                $interfaceDefinition = [
                    'interface' => $token[1],
                    'extends' => null,
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                $token = $this->nextToken($tokens, $parseContext);

                if ($token[0] === T_EXTENDS) {
                    $schemaContext->extends = $this->parseNamespaceList($tokens, $token, $parseContext);
                    $interfaceDefinition['extends'] = array_map([$schemaContext, 'fullyQualifiedName'], $schemaContext->extends);
                }

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-interface and reset $schemaContext
            }

            if ($token[0] === T_TRAIT) {
                $classDefinition = false;
                $interfaceDefinition = false;
                $enumDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['trait' => $token[1], 'line' => $token[2]], $parseContext);
                if ($traitDefinition) {
                    $analysis->addTraitDefinition($traitDefinition);
                }
                $traitDefinition = [
                    'trait' => $token[1],
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-trait and reset $schemaContext
            }

            if (defined('T_ENUM') && $token[0] === T_ENUM) {
                $classDefinition = false;
                $interfaceDefinition = false;
                $traitDefinition = false;

                $token = $this->nextToken($tokens, $parseContext);

                if (!is_array($token)) {
                    // PHP 8 named argument
                    continue;
                }

                $schemaContext = new Context(['enum' => $token[1], 'line' => $token[2]], $parseContext);
                if ($enumDefinition) {
                    $analysis->addEnumDefinition($enumDefinition);
                }
                $enumDefinition = [
                    'enum' => $token[1],
                    'properties' => [],
                    'methods' => [],
                    'context' => $schemaContext,
                ];

                if ($comment) {
                    $schemaContext->line = $line;
                    $this->analyseComment($analysis, $docBlockParser, $comment, $schemaContext);
                    $comment = false;
                    continue;
                }

                // @todo detect end-of-trait and reset $schemaContext
            }

            if ($token[0] === T_STATIC) {
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_VARIABLE) {
                    // static property
                    $propertyContext = new Context(
                        [
                            'property' => substr($token[1], 1),
                            'static' => true,
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $propertyContext);
                        $comment = false;
                    }
                    continue;
                }
            }

            if (in_array($token[0], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR])) { // Scope
                [$type, $nullable, $token] = $this->parseTypeAndNextToken($tokens, $parseContext);
                if ($token[0] === T_VARIABLE) {
                    // instance property
                    $propertyContext = new Context(
                        [
                            'property' => substr($token[1], 1),
                            'type' => $type,
                            'nullable' => $nullable,
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($interfaceDefinition) {
                        $interfaceDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['properties'][$propertyContext->property] = $propertyContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $propertyContext);
                        $comment = false;
                    }
                } elseif ($token[0] === T_FUNCTION) {
                    $token = $this->nextToken($tokens, $parseContext);
                    if ($token[0] === T_STRING) {
                        $methodContext = new Context(
                            [
                                'method' => $token[1],
                                'line' => $line,
                            ],
                            $schemaContext
                        );

                        if ($classDefinition) {
                            $classDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($interfaceDefinition) {
                            $interfaceDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($traitDefinition) {
                            $traitDefinition['methods'][$token[1]] = $methodContext;
                        }
                        if ($comment) {
                            $this->analyseComment($analysis, $docBlockParser, $comment, $methodContext);
                            $comment = false;
                        }
                    }
                }
                continue;
            } elseif ($token[0] === T_FUNCTION) {
                $token = $this->nextToken($tokens, $parseContext);
                if ($token[0] === T_STRING) {
                    $methodContext = new Context(
                        [
                            'method' => $token[1],
                            'line' => $line,
                        ],
                        $schemaContext
                    );

                    if ($classDefinition) {
                        $classDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($interfaceDefinition) {
                        $interfaceDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($traitDefinition) {
                        $traitDefinition['methods'][$token[1]] = $methodContext;
                    }
                    if ($comment) {
                        $this->analyseComment($analysis, $docBlockParser, $comment, $methodContext);
                        $comment = false;
                    }
                }
            }

            if (in_array($token[0], [T_NAMESPACE, T_USE]) === false) {
                // Skip "use" & "namespace" to prevent "never imported" warnings)
                if ($comment) {
                    // Not a doc-comment for a class, property or method?
                    $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
                    $comment = false;
                }
            }

            if ($token[0] === T_NAMESPACE) {
                $parseContext->namespace = $this->parseNamespace($tokens, $token, $parseContext);
                $aliases['__NAMESPACE__'] = $parseContext->namespace;
                $docBlockParser->setAliases($aliases);
                continue;
            }

            if ($token[0] === T_USE) {
                $statements = $this->parseUseStatement($tokens, $token, $parseContext);
                foreach ($statements as $alias => $target) {
                    if ($classDefinition) {
                        // class traits
                        $classDefinition['traits'][] = $schemaContext->fullyQualifiedName($target);
                    } elseif ($traitDefinition) {
                        // trait traits
                        $traitDefinition['traits'][] = $schemaContext->fullyQualifiedName($target);
                    } else {
                        // not a trait use
                        $parseContext->uses[$alias] = $target;

                        $namespaces = $generator->getNamespaces();
                        if (null === $namespaces) {
                            $aliases[strtolower($alias)] = $target;
                        } else {
                            foreach ($namespaces as $namespace) {
                                if (strcasecmp(substr($target . '\\', 0, strlen($namespace)), $namespace) === 0) {
                                    $aliases[strtolower($alias)] = $target;
                                    break;
                                }
                            }
                        }
                        $docBlockParser->setAliases($aliases);
                    }
                }
            }
        }

        // cleanup final comment and definition
        if ($comment) {
            $this->analyseComment($analysis, $docBlockParser, $comment, new Context(['line' => $line], $schemaContext));
        }
        if ($classDefinition) {
            $analysis->addClassDefinition($classDefinition);
        }
        if ($interfaceDefinition) {
            $analysis->addInterfaceDefinition($interfaceDefinition);
        }
        if ($traitDefinition) {
            $analysis->addTraitDefinition($traitDefinition);
        }
        if ($enumDefinition) {
            $analysis->addEnumDefinition($enumDefinition);
        }

        return $analysis;
    }
    /**
     * Parse namespaced string.
     *
     * @param array|string $token
     */
    private function parseNamespace(array &$tokens, &$token, Context $parseContext): string
    {
        $namespace = '';
        $nsToken = array_merge([T_STRING, T_NS_SEPARATOR], $this->php8NamespaceToken());
        while ($token !== false) {
            $token = $this->nextToken($tokens, $parseContext);
            if (!in_array($token[0], $nsToken)) {
                break;
            }
            $namespace .= $token[1];
        }

        return $namespace;
    }

    /**
     * Parse type of variable (if it exists).
     */
    private function parseTypeAndNextToken(array &$tokens, Context $parseContext): array
    {
        $type = Generator::UNDEFINED;
        $nullable = false;
        $token = $this->nextToken($tokens, $parseContext);

        if ($token[0] === T_STATIC) {
            $token = $this->nextToken($tokens, $parseContext);
        }

        if ($token === '?') { // nullable type
            $nullable = true;
            $token = $this->nextToken($tokens, $parseContext);
        }

        $qualifiedToken = array_merge([T_NS_SEPARATOR, T_STRING, T_ARRAY], $this->php8NamespaceToken());
        $typeToken = array_merge([T_STRING], $this->php8NamespaceToken());
        // drill down namespace segments to basename property type declaration
        while (in_array($token[0], $qualifiedToken)) {
            if (in_array($token[0], $typeToken)) {
                $type = $token[1];
            }
            $token = $this->nextToken($tokens, $parseContext);
        }

        return [$type, $nullable, $token];
    }

    /**
     * Parse comment and add annotations to analysis.
     */
    private function analyseComment(Analysis $analysis, DocBlockParser $docBlockParser, string $comment, Context $context): void
    {
        $analysis->addAnnotations($docBlockParser->fromComment($comment, $context), $context);
    }

    /**
     * @return int[]
     */
    private function php8NamespaceToken(): array
    {
        return defined('T_NAME_QUALIFIED') ? [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED] : [];
    }
}
