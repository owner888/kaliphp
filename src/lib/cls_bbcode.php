<?php
namespace kaliphp\lib;

/**
 * bbcode解析成html
 * $text = "
 * [url=http://xxxx.com alt=aaa]xxxx.com[/url]
 * img width=10 height=20 alt=test]1.png[/img]";
 * echo cls_bbcode::parse($text);
 */
class cls_bbcode
{
    /**
     * 支持的标签和标签属性，需要的话自己扩展，不需要的注释掉就好了
     * @var array
     */
    public static $tags = [
        'b'      => [],
        'size'   => [],
        'color'  => [],
        'center' => [],
        'quote'  => [],
        'p'      => ['class' => 'string'],
        'url'    => ['alt' => 'string', 'target' => 'string', 'class' => 'string'],
        'img'    => [
            'width'  => 'int', 'height' => 'int', 'alt' => 'string', 
            'target' => 'string', 'class' => 'string'
        ],
    ];

    /**
     * 解析bbcode
     * @param  string $string 
     * @return string
     */
    public static function parse(string $string):string 
    {
        $pattern  = '`\[(?<tag>'. implode('|', array_keys(self::$tags)) .')';
        $pattern .= '=?(?<value>[^\s]+)?(?<tag_attr>[^\]]*)\](?<innertext>.+?)\[/\1\]`U';
        while ( preg_match_all($pattern, $string, $mats) )
        {
            foreach ($mats[0] as $key => $match) 
            {
                list($tag, $value, $tag_attr, $innertext) = [
                    $mats['tag'][$key], 
                    $mats['value'][$key], 
                    $mats['tag_attr'][$key], 
                    $mats['innertext'][$key]
                ];

                //没有
                if ( !$value && $tag_attr && $tag_attr[0] == '=' ) 
                {
                    list($value, $tag_attr) = @explode(' ', $tag_attr, 2);
                    $value = substr($value, 1);
                }
        
                $replacement = '';
                $value       = $value ? htmlspecialchars($value, ENT_QUOTES) : '';
                $attr_string = self::_parse_tag_attr($tag, $tag_attr); 
                switch ($tag) 
                {
                    case 'b': 
                    case 'i': 
                    case 'p': 
                        $replacement = "<{$tag} {$attr_string}>{$innertext}</{$tag}>"; 
                        break;
                    case 'size': 
                        $value       = min(6, max(1, $value));
                        $replacement = "<h{$value}>{$innertext}<h{$value}>"; 
                        break;
                    case 'color': 
                        $replacement = "<span style=\"color: {$value};\">{$innertext}</span>"; 
                        break;
                    case 'center': 
                        $replacement = "<div class=\"centered\">{$innertext}</div>"; 
                        break;
                    case 'quote': 
                        $replacement = "<blockquote>{$innertext}</blockquote>" . $value ? "<cite>{$value}</cite>" : ''; 
                        break;
                    case 'url':
                        $replacement = '<a href="' . ($value ?? $innertext) . "\" {$attr_string}>{$innertext}</a>"; 
                        break;
                    case 'img':
                        $replacement = "<img src=\"{$innertext}\" {$attr_string}/>";
                        break;
                }

                $replacement && $string = str_replace($match, $replacement, $string);
            }
        } 

        $string = str_replace('\n', '<br />', $string);
        return $string;
    }

    /**
     * 获取bbcode属性
     * @param  string       $tag        
     * @param  string       $attr_string
     * @param  bool|boolean $format     
     * @return mixed          
     */
    private static function _parse_tag_attr(string $tag, ?string $attr_string, bool $format = true)
    {
        $ret = [];
        if ( 
            $attr_string             &&
            isset(self::$tags[$tag]) &&
            preg_match_all('`(?<key>[^\s\=]+)=(?<value>[^\s\=]+)`', $attr_string, $mats) 
        ) 
        {
            foreach ($mats['key'] as $key => $val) 
            {
                if ( isset(self::$tags[$tag][$val]) ) 
                {
                    $ret[htmlspecialchars($val, ENT_QUOTES)] = cls_filter::filter(
                        $mats['value'][$key], 
                        self::$tags[$tag][$val]
                    );
                }
            }
        }

        if ( $format ) 
        {
            $tmp = [];
            foreach ($ret as $key => $val) 
            {
                $tmp[] = "{$key}=\"{$val}\"";
            }

            $ret = implode(' ', $tmp);
        }

        return $ret;
    }
}