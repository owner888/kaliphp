<?php
/**
 * KaliPHP is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    KaliPHP
 * @version    1.0.1
 * @author     KALI Development Team
 * @license    MIT License
 * @copyright  2010 - 2018 Kali Development Team
 * @link       http://kaliphp.com
 */

namespace kaliphp\lib;
use kaliphp\req;
use kaliphp\log;
use kaliphp\lang;

/**
 * 表单验证类
 *
 * required: "必选字段",
 * remote: "请修正该字段",
 * email: "请输入正确格式的电子邮件",
 * url: "请输入合法的网址",
 * date: "请输入合法的日期",
 * numeric: "请输入合法的数字",
 * integer: "只能输入整数",
 * decimal: "只能输入小数",
 * idcard: "请输入合法的身份证号",
 * creditcard: "请输入合法的信用卡号",
 * matches[param]: "请再次输入相同的值",
 * accept: "请输入拥有合法后缀名的字符串",
 * maxlength[param]: "长度不能大于 {param} 位",
 * minlength[param]: "长度不能小于 {param} 位",
 * exactlength[param]: "长度只能等于 {param} 位",
 * rangelength[minlen:maxlen]: "长度介于 {minlen} 和 {maxlen} 之间",
 * max[param]: "请输入一个最大为 {param} 的值",
 * min[param]: "请输入一个最小为 {param} 的值"
 * range[minnum:maxnum]: "请输入一个介于 {minnum} 和 {maxnum} 之间的值",
 * 参考：http://joe5456536.blog.163.com/blog/static/85374773201282485744194/
 * @version 0.1
 */
class cls_validate
{
    /**
	 * @var  array  contains references to all instantiations of cls_validate
     */
    protected static $_instances = array();

    /**
	 * validate data for the current form submission
	 *
	 * @var array
	 */
	protected $_field_data		= array();

	/**
	 * Array of validate errors
	 *
	 * @var array
	 */
	protected $_error_array		= array();

    /**
	 * Array of custom error messages
	 *
	 * @var array
	 */
	protected $_error_messages	= array();

	/**
	 * Custom data to validate
	 *
	 * @var array
	 */
	public $validate_data	= array();

    // --------------------------------------------------------------------

    /**
	 * 创建实例
	 *
	 * @param   string    $name    Identifier for this cls_validate
	 * @param   array     $config  Configuration array
	 * @return  cls_validate
	 */
    static function instance($name = 'default')
    {
        if (isset(self::$_instances[$name]))
        {
            return self::$_instances[$name];
        }

        $instance = new static();

        self::$_instances[$name] = $instance;

        return $instance;
    }

	// --------------------------------------------------------------------

    /**
	 * class constructor
	 *
	 * @param  string
	 * @param  array
	 */
	final private function __construct() { }

	// --------------------------------------------------------------------

    /**
     * 默认情况下，表单验证使用 $_POST 数组验证
     * 通过此方法可以代替 $_POST 数组验证
     * 因为单例的局限性，验证后，应该调用 reset_validate() 函数
     * 
     * @param array $data
     * @return void
     */
    public function set_data(array $data)
	{
		if ( ! empty($data))
		{
			$this->validate_data = $data;
		}

		return $this;
	}   

	// --------------------------------------------------------------------

    /**
	 * Set Rules
	 *
	 * @param	mixed	$field
	 * @param	string	$label
	 * @param	mixed	$rules
	 * @param	array	$errors
	 * @return	cls_validate
	 */
	public function set_rules($field, $label = '', $rules = array(), $errors = array())
    {
		// No reason to set rules if we have no POST data
		// or a validate array has not been specified
		if (req::method() !== 'POST' && empty($this->validate_data))
		{
			return $this;
		}

        if (is_array($field)) 
        {
            foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name
				$label = isset($row['label']) ? $row['label'] : $row['field'];

				// Add the custom error message array
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				// Here we go!
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
        }

        // No fields or no rules? Nothing to do...
		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			// BC: Convert pipe-separated rules string to an array
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		$label = ($label === '') ? $field : $label;

        $indexes = array();

		// Is the field name an array? If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

        // Build our master array
        $this->_field_data[$field] = array(
            'field'     => $field,
            'label'     => $label,
            'rules'     => $rules,
            'errors'    => $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
            'error'     => '',
        );

        //print_r($this->_field_data[$field]['rules']);

        return $this;
    }

	// --------------------------------------------------------------------

	/**
	 * Set Error Message
	 *
	 * @param	array
	 * @param	string
	 * @return	cls_validate
	 */
	public function set_message($lang, $val = '')
	{
		if ( ! is_array($lang))
		{
			$lang = array($lang => $val);
		}

		$this->_error_messages = array_merge($this->_error_messages, $lang);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Run the Validator
	 *
	 * @return	bool
	 */
    public function run()
    {
        $validate_array = empty($this->validate_data)
            ? req::$posts
            : $this->validate_data;

		if (count($this->_field_data) === 0)
        {
            return true;
        }

        // 加载验证错误语言包
        lang::load("form_validate", LANG);

        // 把值赋值给 postdata
		foreach ($this->_field_data as $field => &$row)
		{
            if ($row['is_array'] === TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($validate_array, $row['keys']);
			}
            // 如果用户通过调用 set_data() 函数设置了验证数据，替换表单的数据
			elseif (isset($validate_array[$field]))
			{
				$this->_field_data[$field]['postdata'] = $validate_array[$field];
			}
		}

		foreach ($this->_field_data as $field => &$row)
        {
            // 如果没有设置验证规则，跳过
            if (empty($row['rules'])) 
            {
                continue;
            }

            $this->_execute($row, $row['rules'], $row['postdata']);
        }

        // Did we end up with any errors?
        $total_errors = count($this->_error_array);

        // 貌似没用
		empty($this->validate_data) && $this->_reset_post_array();

        return ($total_errors === 0);
    }

    // --------------------------------------------------------------------

	/**
	 * Prepare rules
	 *
	 * Re-orders the provided rules in order of importance, so that
	 * they can easily be executed later without weird checks ...
	 *
	 * "Callbacks" are given the highest priority (always called),
	 * followed by 'required' (called if callbacks didn't fail),
	 * and then every next rule depends on the previous one passing.
	 *
	 * @param	array	$rules
	 * @return	array
	 */
	protected function _prepare_rules($rules)
	{
		$new_rules = array();
		$callbacks = array();

		foreach ($rules as &$rule)
		{
			// Let 'required' always be the first (non-callback) rule
			if ($rule === 'required')
			{
				array_unshift($new_rules, 'required');
			}
			// 'isset' is a kind of a weird alias for 'required' ...
			elseif ($rule === 'isset' && (empty($new_rules) OR $new_rules[0] !== 'required'))
			{
				array_unshift($new_rules, 'isset');
			}
			// The old/classic 'callback_'-prefixed rules
			elseif (is_string($rule) && strncmp('callback_', $rule, 9) === 0)
			{
				$callbacks[] = $rule;
			}
			// Proper callables
			elseif (is_callable($rule))
			{
                //echo "is_callable\n";
				$callbacks[] = $rule;
			}
			// "Named" callables; i.e. array('name' => $callable)
			elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
			{
                //echo "is_callable1111\n";
				$callbacks[] = $rule;
			}
			// Everything else goes at the end of the queue
			else
			{
				$new_rules[] = $rule;
			}
		}

        //print_r($callbacks);
        //print_r($new_rules);
		return array_merge($callbacks, $new_rules);
    }

    // --------------------------------------------------------------------

	/**
	 * Traverse a multidimensional $_POST array index until the data is found
	 *
	 * @param	array
	 * @param	array
	 * @param	int
	 * @return	mixed
	 */
	protected function _reduce_array($array, $keys, $i = 0)
	{
		if (is_array($array) && isset($keys[$i]))
		{
			return isset($array[$keys[$i]]) ? $this->_reduce_array($array[$keys[$i]], $keys, ($i+1)) : NULL;
		}

		// NULL must be returned for empty fields
		return ($array === '') ? NULL : $array;
	}

    // --------------------------------------------------------------------
    /**
	 * Re-populate the _POST array with our finalized and processed data
	 *
	 * @return	void
	 */
	protected function _reset_post_array()
	{
		foreach ($this->_field_data as $field => $row)
		{
			if ($row['postdata'] !== NULL)
			{
				if ($row['is_array'] === FALSE)
				{
					isset($_POST[$field]) && $_POST[$field] = $row['postdata'];
				}
				else
				{
					// start with a reference
					$post_ref =& req::$posts;

					// before we assign values, make a reference to the right POST key
					if (count($row['keys']) === 1)
					{
						$post_ref =& $post_ref[current($row['keys'])];
					}
					else
					{
						foreach ($row['keys'] as $val)
						{
							$post_ref =& $post_ref[$val];
						}
					}

					$post_ref = $row['postdata'];
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Executes the validate routines
	 *
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	int
	 * @return	mixed
	 */
	protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        // 如果字段是数组
        if (is_array($postdata) && ! empty($postdata))
		{
			foreach ($postdata as $key => $val)
			{
				$this->_execute($row, $rules, $val, $key);
			}

			return;
		}

		$rules = $this->_prepare_rules($rules);
        //print_r($rules);
        foreach ($rules as $rule) 
        {
            $_in_array = FALSE;

			if ($row['is_array'] === TRUE && is_array($this->_field_data[$row['field']]['postdata']))
			{
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
				{
					continue;
				}

				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}
			else
			{
				$postdata = is_array($this->_field_data[$row['field']]['postdata'])
					? NULL
					: $this->_field_data[$row['field']]['postdata'];
			}

            // Is the rule a callback?
			$callback = $callable = FALSE;
			if (is_string($rule))
			{
				if (strpos($rule, 'callback_') === 0)
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}
			}
			elseif (is_callable($rule))
			{
				$callable = TRUE;
			}
			elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
			{
				// We have a "named" callable, so save the name
				$callable = $rule[0];
				$rule = $rule[1];
			}

			$param = FALSE;
			if ( ! $callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match))
			{
				$rule = $match[1];
				$param = $match[2];
			}

			// Ignore empty, non-required inputs with a few exceptions ...
			if (
				($postdata === NULL OR $postdata === '')
				&& $callback === FALSE
				&& $callable === FALSE
				&& ! in_array($rule, array('required', 'isset', 'matches'), TRUE)
			)
			{
				continue;
			}

            // 自定义了回调函数
			if ($callback OR $callable !== FALSE)
			{
                // 控制器中的函数
                if ($callback)
                {
                    $backtrace = debug_backtrace();
                    $handle_func = $backtrace[2];
                    array_shift($backtrace);
                    $class = $handle_func['class'];

                    if ( ! method_exists($class, $rule))
                    {
                        log::debug('Unable to find callback validate rule: '.$rule);
                        $result = FALSE;
                    }
                    else
                    {
                        $ctl_class = new $class;
                        $params = ($param !== FALSE) ? array($postdata, $param) : array($postdata);
                        $result = call_user_func_array(array($ctl_class, $rule), $params);
                        //$result = $ctl->$rule($postdata, $param);
                    }
                }
                // 匿名函数、模块函数
                else
                {
                    $params = ($param !== FALSE) ? array($postdata, $param) : array($postdata);
                    $result = is_array($rule)
                        //? $rule[0]->{$rule[1]}($postdata)
                        ? call_user_func_array(array($rule[0], $rule[1]), $params)
                        : $rule($postdata);

                    // Is $callable set to a rule name?
                    if ($callable !== FALSE)
                    {
                        $rule = $callable;
                    }
                }

				// Re-assign the result to the master data array
				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}
			}
            // PHP自带函数
			elseif ( ! method_exists($this, $rule))
			{
				if (function_exists($rule))
				{
					$result = ($param !== FALSE) ? $rule($postdata, $param) : $rule($postdata);

					if ($_in_array === TRUE)
					{
						$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
					}
					else
					{
						$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
					}
				}
				else
				{
                    log::debug('Unable to find validate rule: '.$rule);
					$result = FALSE;
				}
			}
            // 验证类函数
			else
			{
                $params = ($param !== FALSE) ? array($postdata, $param) : array($postdata);
                $result = call_user_func_array(array($this, $rule), $params);

				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}
            }

            if ($result === FALSE)
            {
                // 匿名函数
                if ( ! is_string($rule))
				{
					$line = lang::get('form_validate_not_set').'(Anonymous function)';
				}
				else
				{
					$line = $this->_get_error_message($rule, $row['field']);
				}

				// Build the error message
				$message = $this->_build_error_msg($line, $row['label'], $param);

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if ( ! isset($this->_error_array[$row['field']]))
                {
                    $this->_error_array[$row['field']] = $message;
                }

				return;
            }
        }
    }

    // --------------------------------------------------------------------

	/**
	 * Get the error message for the rule
	 *
	 * @param 	string $rule 	The rule name
	 * @param 	string $field	The field name
	 * @return 	string
	 */
	protected function _get_error_message($rule, $field)
	{
		// check if a custom message is defined through validate config row.
		if (isset($this->_field_data[$field]['errors'][$rule]))
		{
			return $this->_field_data[$field]['errors'][$rule];
		}
		// check if a custom message has been set using the set_message() function
		elseif (isset($this->_error_messages[$rule]))
		{
			return $this->_error_messages[$rule];
		}
        // 自定义回调函数
		elseif (FALSE !== ($line = lang::get('form_validate_'.$rule, FALSE)))
		{
			return $line;
		}
		// DEPRECATED support for non-prefixed keys, lang file again
		elseif (FALSE !== ($line = lang::get($rule, FALSE)))
		{
			return $line;
		}

		return lang::get('form_validate_not_set').'('.$rule.')';
	}
    
	// --------------------------------------------------------------------

	/**
	 * Build an error message using the field and param.
	 *
	 * @param	string	The error message line
	 * @param	string	A field's human name
	 * @param	mixed	A rule's optional parameter
	 * @return	string
	 */
	protected function _build_error_msg($line, $field = '', $param = '')
    {
		if (strpos($line, '%s') !== FALSE)
		{
			return sprintf($line, $field, $param);
		}

		return str_replace(array('{field}', '{param}'), array($field, $param), $line);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Error Message
	 *
	 * Gets the error message associated with a particular field
	 *
	 * @param	string	$field	Field name
	 * @param	string	$prefix	HTML start tag
	 * @param 	string	$suffix	HTML end tag
	 * @return	string
	 */
	public function error($field = '', $prefix = '', $suffix = '')
	{
        if (empty($field)) 
        {
            $errors = array_values($this->_error_array);
            return isset($errors[0]) ? $errors[0] : '';
        }

		if (empty($this->_field_data[$field]['error']))
		{
			return '';
		}

		return $prefix.$this->_field_data[$field]['error'].$suffix;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Array of Error Messages
	 *
	 * Returns the error messages as an array
	 *
	 * @return	array
	 */
	public function error_array()
	{
		return $this->_error_array;
	}

	/* -------------------------------------------------------------------------------
	 * The validate methods
	 * ------------------------------------------------------------------------------- */

	/**
	 * Special empty method because 0 and '0' are non-empty values
	 *
	 * @param   mixed  $val
	 * @return  bool
	 */
	public function _empty($val)
	{
		return ($val === false or $val === null or $val === '' or $val === array());
	}

	/**
	 * Required
	 *
	 * Value may not be empty
	 *
	 * @param   mixed  $val
	 * @return  bool
	 */
	public function required($val)
	{
		return ! $this->_empty($val);
	}

    // --------------------------------------------------------------------

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @param	string
	 * @param	string	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		return (bool) preg_match($regex, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @param	string	$str	string to compare against
	 * @param	string	$field
	 * @return	bool
	 */
	public function matches($str, $field)
	{
		return isset($this->_field_data[$field], $this->_field_data[$field]['postdata'])
			? ($str === $this->_field_data[$field]['postdata'])
			: FALSE;
	}

	// --------------------------------------------------------------------

    /**
	 * Max Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function maxlength($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return $val >= mb_strlen($str);
	}

    /**
	 * Min Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function minlength($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return $val <= mb_strlen($str);
	}

    // --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function exactlength($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return mb_strlen($str) === (int) $val;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks whether numeric input has a minimum value
	 *
	 * @param   string|float|int  $val
	 * @param   float|int         $min
	 * @return  bool
	 */
	public function min($val, $min)
	{
		return is_numeric($val) ? ($val >= $min) : FALSE;
	}

    // --------------------------------------------------------------------

	/**
	 * Checks whether numeric input has a maximum value
	 *
	 * @param   string|float|int  $val
	 * @param   float|int         $max
	 * @return  bool
	 */
	public function max($val, $max)
    {
		return is_numeric($val) ? ($val <= $max) : FALSE;
    }

    // --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}
    
	// --------------------------------------------------------------------

	/**
	 * Valid URL
	 *
	 * @param	string	$val
	 * @return	bool
	 */
	public function url($val)
	{
		return filter_var($val, FILTER_VALIDATE_URL);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @param	string  $val
	 * @return	bool
	 */
	public function email($val)
	{
		return filter_var($val, FILTER_VALIDATE_EMAIL);
	}
    
	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @param	string
	 * @param	string	'ipv4' or 'ipv6' to validate a specific IP format
	 * @return	bool
	 */
	public function ip($val)
	{
		return filter_var($val, FILTER_VALIDATE_IP);
	}

	// --------------------------------------------------------------------

    /**
	 * Valid Id card
	 *
	 * @param	string  $val
	 * @return	bool
	 */
	public function idcard($id)
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match($regx,$id))
        {
            return false;
        }
        if(15==strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else           //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth))  //检查生日日期是否正确
            {
                return false;
            }
            else
            {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ( $i = 0; $i < 17; $i++ )
                {
                    $b = (int) $id[$i];
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n  = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id,17, 1))
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }
    }

	// --------------------------------------------------------------------

    /**
	 * Valid Date
	 *
	 * @param	string  $val
	 * @return	bool
	 */
	public function date($val)
    {
        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})$/s", $val);
    }

	// --------------------------------------------------------------------

    /**
	 * Valid Phone
	 *
	 * @param	string  $val
	 * @return	bool
	 */
	public function phone($val)
    {
        $regex = "/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/";
        return (bool)preg_match($regex, $val);
    }

	// --------------------------------------------------------------------
    
    /**
     * 中文
     *
     * @param string $str
     * @param integer $len
     * @return boolean
     */
    public static function chinese($str, $encode="gbk")
    {
        switch ($encode){
        case "utf-8":
            $regx = "/^[\x{4e00}-\x{9fa5}]+$/u";
            break;
        default:
            $regx = "/^[".chr(0xa1)."-".chr(0xff)."]+$/";
            break;
        }
        return preg_match($regx, $str);
    }

	// --------------------------------------------------------------------

    /**
     * 用户名
     *
     * @param string $user_name
     * @return bool
     */
    public static function username($username)
    {
        return !preg_match('/[^a-z0-9\x{4e00}-\x{9fa5}_\-]/iu', $username)
            && strlen($username) >= 0 && mb_strlen($username, 'UTF-8') <= 30;
    }

	// --------------------------------------------------------------------

    // 密码强度检查
    public function password_strong($password)
    {
        $lv = 0;
        if (preg_match('/[A-Z]/', $password) > 0) 
        {
            $lv++;
        }
        if (preg_match('/[a-z]/', $password) > 0) 
        {
            $lv++;
        }
        if (preg_match('/[0-9]/', $password) > 0) 
        {
            $lv++;
        }

        if ($lv < 3) 
        {
            return false;
        }

        return true;
    }

	// --------------------------------------------------------------------

	/**
	 * Reset validate vars
	 *
	 * Prevents subsequent validate routines from being affected by the
	 * results of any previous validate routine due to the CI singleton.
	 *
	 * @return	cls_validate
	 */
	public function reset_validate()
	{
		$this->_field_data = array();
		$this->_error_array = array();
		$this->_error_messages = array();
		return $this;
	}

}
