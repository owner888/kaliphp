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

/**
 * The cls_arr class provides a few nice functions for making
 * dealing with arrays easier
 *
 * @package     KaliPHP
 * @subpackage  Lib
 */
class cls_arr
{
    /**
     * Gets a dot-notated key from an array, with a default value if it does
     * not exist.
     *
     * @param   array   $array    The search array
     * @param   mixed   $key      The dot-notated key or array of keys
     * @param   string  $default  The default value
     * @return  mixed
     */
    public static function get($array, $key = null, $default = null, $filter_type = '', bool $throw_error = false)
    {
        if ( ! is_array($array) and !is_object($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or object or ArrayAccess object.');
        }

        if (is_null($key))
        {
            return $array;
        }

        if (is_array($key))
        {
            $return = array();
            foreach ($key as $k)
            {
                $return[$k] = static::get($array, $k, $default, $filter_type, $throw_error);
            }
            return $return;
        }

        is_object($key) and $key = (string) $key;

        // 一层key，直接返回结果
        if (array_key_exists($key, $array))
        {
            // object of type stdClass must change to array
            //if (is_object($array)) 
            //{
                //$array = (array)$array;
            //}

            //$array = $array[$key];

            // 不强制转化，性能更好
            $array = (is_object($array) and ! ($array instanceof \ArrayAccess)) ? $array->{$key} :
                $array[$key];

            return cls_filter::filter($array, $filter_type, $throw_error);
        }

        // 多层key，一层一层的修改 $array 的值，最终得出最后那个key对应的值
        foreach (explode('.', $key) as $key_part)
        {
            // object of type stdClass must change to array
            if (is_object($array)) 
            {
                $array = (array)$array;
            }

            if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false)
            {
                if ( ! is_array($array) or ! array_key_exists($key_part, $array))
                {
                    return $default;
                }
            }

            $array = $array[$key_part];
        }

        $array = cls_filter::filter($array, $filter_type, $throw_error);
        return $array;
    }

    /**
     * Set an array item (dot-notated) to the value.
     *
     * @param   array   $array  The array to insert it into
     * @param   mixed   $key    The dot-notated key to set or array of keys
     * @param   mixed   $value  The value
     * @return  void
     */
    public static function set(&$array, $key, $value = null)
    {
        if (is_null($key))
        {
            $array = $value;
            return;
        }

        if (is_array($key))
        {
            foreach ($key as $k => $v)
            {
                static::set($array, $k, $v);
            }
        }
        else
        {
            $keys = explode('.', $key);

            while (count($keys) > 1)
            {
                $key = array_shift($keys);

                if ( ! isset($array[$key]) or ! is_array($array[$key]))
                {
                    $array[$key] = array();
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;
        }
    }

    /**
     * Unsets dot-notated key from an array
     *
     * @param   array   $array    The search array
     * @param   mixed   $key      The dot-notated key or array of keys
     * @return  mixed
     */
    public static function del(&$array, $key)
    {
        if (is_null($key))
        {
            return false;
        }

        if (is_array($key))
        {
            $return = array();
            foreach ($key as $k)
            {
                $return[$k] = static::del($array, $k);
            }
            return $return;
        }

        $key_parts = explode('.', $key);

        if ( ! is_array($array) or ! array_key_exists($key_parts[0], $array))
        {
            return false;
        }

        $this_key = array_shift($key_parts);

        if ( ! empty($key_parts))
        {
            $key = implode('.', $key_parts);
            return static::del($array[$this_key], $key);
        }
        else
        {
            unset($array[$this_key]);
        }

        return true;
    }

    //使用array_keys搜索指定的值再循环unset）
    public static function del_by_value(array &$array, $value)
    {
        $values = is_array($value) ? $value : (array)$value;
        foreach ($values as $value) 
        {
            $keys = array_keys($array, $value);
            if(!empty($keys))
            {
                foreach ($keys as $key) 
                {
                    unset($array[$key]);
                }
            }
        }

        sort($array);

        return true;
    }    

    // 删除数组中值为0的
    public static function del_zero_value(array &$array)
    {
        return array_filter($array, function($val) { return $val != 0; });
    }   

    /**
     * Pluck an array of values from an array.
     *
     * @param  array   $array  collection of arrays to pluck from
     * @param  string  $key    key of the value to pluck
     * @param  string  $index  optional return array index key, true for original index
     * @return array   array of plucked values
     */
    public static function pluck($array, $key, $index = null)
    {
        $return = array();
        $get_deep = strpos($key, '.') !== false;

        if ( ! $index)
        {
            foreach ($array as $i => $a)
            {
                $return[] = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        }
        else
        {
            foreach ($array as $i => $a)
            {
                $index !== true and $i = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$index} : $a[$index];
                $return[$i] = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        }

        return $return;
    }

    /**
     * 随机返回数组的键 
     * 
     * @param array $array The search array 
     * 
     * @return mixed
     */
    public static function rand_key(array $array)
    {
        return array_rand($array);
    }

    /**
     * 随机返回数组的值 
     * 
     * @param array $array The search array 
     * 
     * @return mixed
     */
    public static function rand_value(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * array_key_exists with a dot-notated key from an array.
     *
     * @param   array   $array    The search array
     * @param   mixed   $key      The dot-notated key or array of keys
     * @return  mixed
     */
    public static function key_exists($array, $key)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        is_object($key) and $key = (string) $key;

        if ( ! is_string($key))
        {
            return false;
        }

        if (array_key_exists($key, $array))
        {
            return true;
        }

        foreach (explode('.', $key) as $key_part)
        {
            if (($array instanceof \ArrayAccess and isset($array[$key_part])) === false)
            {
                if ( ! is_array($array) or ! array_key_exists($key_part, $array))
                {
                    return false;
                }
            }

            $array = $array[$key_part];
        }

        return true;
    }

    /**
     * Converts a multi-dimensional associative array into an array of key => values with the provided field names
     *
     * @param   array   $assoc      the array to convert
     * @param   string  $key_field  the field name of the key field
     * @param   string  $val_field  the field name of the value field
     * @return  array
     * @throws  \InvalidArgumentException
     */
    public static function assoc_to_keyval($assoc, $key_field, $val_field)
    {
        if ( ! is_array($assoc) and ! $assoc instanceof \Iterator)
        {
            throw new \InvalidArgumentException('The first parameter must be an array.');
        }

        $output = array();
        foreach ($assoc as $row)
        {
            if (isset($row[$key_field]) and isset($row[$val_field]))
            {
                $output[$row[$key_field]] = $row[$val_field];
            }
        }

        return $output;
    }

    /**
     * Converts an array of key => values into a multi-dimensional associative array with the provided field names
     *
     * @param   array   $array      the array to convert
     * @param   string  $key_field  the field name of the key field
     * @param   string  $val_field  the field name of the value field
     * @return  array
     * @throws  \InvalidArgumentException
     */
    public static function keyval_to_assoc($array, $key_field, $val_field)
    {
        if ( ! is_array($array) and ! $array instanceof \Iterator)
        {
            throw new \InvalidArgumentException('The first parameter must be an array.');
        }

        $output = array();
        foreach ($array as $key => $value)
        {
            $output[] = array(
                $key_field => $key,
                $val_field => $value,
            );
        }

        return $output;
    }

    /**
     * Converts the given 1 dimensional non-associative array to an associative
     * array.
     *
     * The array given must have an even number of elements or null will be returned.
     *
     *     cls_arr::to_assoc(array('foo','bar'));
     *
     * @param   string      $arr  the array to change
     * @return  array|null  the new array or null
     * @throws  \BadMethodCallException
     */
    public static function to_assoc($arr)
    {
        if (($count = count($arr)) % 2 > 0)
        {
            throw new \BadMethodCallException('Number of values in to_assoc must be even.');
        }
        $keys = $vals = array();

        for ($i = 0; $i < $count - 1; $i += 2)
        {
            $keys[] = array_shift($arr);
            $vals[] = array_shift($arr);
        }
        return array_combine($keys, $vals);
    }

    /**
     * Checks if the given array is an assoc array.
     *
     * @param   array  $arr  the array to check
     * @return  bool   true if its an assoc array, false if not
     */
    public static function is_assoc($arr)
    {
        if ( ! is_array($arr))
        {
            throw new \InvalidArgumentException('The parameter must be an array.');
        }

        $counter = 0;
        foreach ($arr as $key => $unused)
        {
            if ( ! is_int($key) or $key !== $counter++)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Flattens a multi-dimensional associative array down into a 1 dimensional
     * associative array.
     *
     * @param   array   $array   the array to flatten
     * @param   string  $glue    what to glue the keys together with
     * @param   bool    $reset   whether to reset and start over on a new array
     * @param   bool    $indexed whether to flatten only associative array's, or also indexed ones
     * @return  array
     */
    public static function flatten($array, $glue = ':', $reset = true, $indexed = true)
    {
        static $return = array();
        static $curr_key = array();

        if ($reset)
        {
            $return = array();
            $curr_key = array();
        }

        foreach ($array as $key => $val)
        {
            $curr_key[] = $key;
            if (is_array($val) and ($indexed or array_values($val) !== $val))
            {
                static::flatten($val, $glue, false, $indexed);
            }
            else
            {
                $return[implode($glue, $curr_key)] = $val;
            }
            array_pop($curr_key);
        }
        return $return;
    }

    /**
     * Flattens a multi-dimensional associative array down into a 1 dimensional
     * associative array.
     *
     * @param   array   $array  the array to flatten
     * @param   string  $glue   what to glue the keys together with
     * @param   bool    $reset  whether to reset and start over on a new array
     * @return  array
     */
    public static function flatten_assoc($array, $glue = ':', $reset = true)
    {
        return static::flatten($array, $glue, $reset, false);
    }

    /**
     * Reverse a flattened array in its original form.
     *
     * @param   array   $array  flattened array
     * @param   string  $glue   glue used in flattening
     * @return  array   the unflattened array
     */
    public static function reverse_flatten($array, $glue = ':')
    {
        $return = array();

        foreach ($array as $key => $value)
        {
            if (stripos($key, $glue) !== false)
            {
                $keys = explode($glue, $key);
                $temp =& $return;
                while (count($keys) > 1)
                {
                    $key = array_shift($keys);
                    $key = is_numeric($key) ? (int) $key : $key;
                    if ( ! isset($temp[$key]) or ! is_array($temp[$key]))
                    {
                        $temp[$key] = array();
                    }
                    $temp =& $temp[$key];
                }

                $key = array_shift($keys);
                $key = is_numeric($key) ? (int) $key : $key;
                $temp[$key] = $value;
            }
            else
            {
                $key = is_numeric($key) ? (int) $key : $key;
                $return[$key] = $value;
            }
        }

        return $return;
    }

    /**
     * Filters an array on prefixed associative keys.
     *
     * @param   array   $array          the array to filter.
     * @param   string  $prefix         prefix to filter on.
     * @param   bool    $remove_prefix  whether to remove the prefix.
     * @return  array
     */
    public static function filter_prefixed($array, $prefix, $remove_prefix = true)
    {
        $return = array();
        foreach ($array as $key => $val)
        {
            if (preg_match('/^'.$prefix.'/', $key))
            {
                if ($remove_prefix === true)
                {
                    $key = preg_replace('/^'.$prefix.'/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Recursive version of PHP's array_filter()
     *
     * @param   array     $array    the array to filter.
     * @param   callback  $callback the callback that determines whether or not a value is filtered
     * @return  array
     */
    public static function filter_recursive($array, $callback = null)
    {
        foreach ($array as &$value)
        {
            if (is_array($value))
            {
                $value = $callback === null ? static::filter_recursive($value) : static::filter_recursive($value, $callback);
            }
        }

        return $callback === null ? array_filter($array) : array_filter($array, $callback);
    }

    /**
     * Removes items from an array that match a key prefix.
     *
     * @param   array   $array  the array to remove from
     * @param   string  $prefix  prefix to filter on
     * @return  array
     */
    public static function remove_prefixed($array, $prefix)
    {
        foreach ($array as $key => $val)
        {
            if (preg_match('/^'.$prefix.'/', $key))
            {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Filters an array on suffixed associative keys.
     *
     * @param   array   $array          the array to filter.
     * @param   string  $suffix         suffix to filter on.
     * @param   bool    $remove_suffix  whether to remove the suffix.
     * @return  array
     */
    public static function filter_suffixed($array, $suffix, $remove_suffix = true)
    {
        $return = array();
        foreach ($array as $key => $val)
        {
            if (preg_match('/'.$suffix.'$/', $key))
            {
                if ($remove_suffix === true)
                {
                    $key = preg_replace('/'.$suffix.'$/', '', $key);
                }
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Removes items from an array that match a key suffix.
     *
     * @param   array   $array   the array to remove from
     * @param   string  $suffix  suffix to filter on
     * @return  array
     */
    public static function remove_suffixed($array, $suffix)
    {
        foreach ($array as $key => $val)
        {
            if (preg_match('/'.$suffix.'$/', $key))
            {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Filters an array by an array of keys
     *
     * @param   array  $array   the array to filter.
     * @param   array  $keys    the keys to filter
     * @param   bool   $remove  if true, removes the matched elements.
     * @return  array
     */
    public static function filter_keys($array, $keys, $remove = false): array
    {
        $return = array();
        foreach ($keys as $key)
        {
            if (array_key_exists($key, $array))
            {
                $remove or $return[$key] = $array[$key];
                if($remove)
                {
                    unset($array[$key]);
                }
            }
        }
        return $remove ? $array : $return;
    }
    
    /**
     * 删除指定值
     * @param    array     $array
     * @param    mixed     $value
     * @return   array       
     */
    public static function filter_value(array $array, $value)
    {
        $value = (array) $value;
        foreach($array as $k => $v)
        {
            if ( in_array($v, $value) ) 
            {
                unset($array[$k]);
            }
        }

        return $array;
    }

    /**
     * 二维数组方式过滤keys 
     * 
     * @param   array  $arrays  the array list to filter.
     * @param   array  $keys    the keys to filter
     * @param   bool   $remove  if true, removes the matched elements.
     * 
     * @return void
     */
    public static function filter_keys_list($arrays, $keys, $remove = false): array
    {
        foreach ($arrays as $key => $array) 
        {
            $arrays[$key] = self::filter_keys($array, $keys, $remove);
        }
        return $arrays;
    }

    /**
     * Insert value(s) into an array, mostly an array_splice alias
     * WARNING: original array is edited by reference, only boolean success is returned
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $value     the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   int          $pos       the numeric position at which to insert, negative to count from the end backwards
     * @return  bool         false when array shorter then $pos, otherwise true
     */
    public static function insert(array &$original, $value, $pos)
    {
        if (count($original) < abs($pos))
        {
            trigger_error('Position larger than number of elements in array in which to insert.');
            return false;
        }

        array_splice($original, $pos, 0, $value);

        return true;
    }

    /**
     * Insert value(s) into an array, mostly an array_splice alias
     * WARNING: original array is edited by reference, only boolean success is returned
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $values    the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   int          $pos       the numeric position at which to insert, negative to count from the end backwards
     * @return  bool         false when array shorter then $pos, otherwise true
     */
    public static function insert_assoc(array &$original, array $values, $pos)
    {
        if (count($original) < abs($pos))
        {
            return false;
        }

        $original = array_slice($original, 0, $pos, true) + $values + array_slice($original, $pos, null, true);

        return true;
    }

    /**
     * Insert value(s) into an array before a specific key
     * WARNING: original array is edited by reference, only boolean success is returned
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $value     the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   string|int   $key       the key before which to insert
     * @param   bool         $is_assoc  whether the input is an associative array
     * @return  bool         false when key isn't found in the array, otherwise true
     */
    public static function insert_before_key(array &$original, $value, $key, $is_assoc = false)
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false)
        {
            trigger_error('Unknown key before which to insert the new value into the array.');
            return false;
        }

        return $is_assoc ? static::insert_assoc($original, $value, $pos) : static::insert($original, $value, $pos);
    }

    /**
     * Insert value(s) into an array after a specific key
     * WARNING: original array is edited by reference, only boolean success is returned
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $value     the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   string|int   $key       the key after which to insert
     * @param   bool         $is_assoc  whether the input is an associative array
     * @return  bool         false when key isn't found in the array, otherwise true
     */
    public static function insert_after_key(array &$original, $value, $key, $is_assoc = false)
    {
        $pos = array_search($key, array_keys($original));

        if ($pos === false)
        {
            trigger_error('Unknown key after which to insert the new value into the array.');
            return false;
        }

        return $is_assoc ? static::insert_assoc($original, $value, $pos + 1) : static::insert($original, $value, $pos + 1);
    }

    /**
     * Insert value(s) into an array after a specific value (first found in array)
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $value     the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   string|int   $search    the value after which to insert
     * @param   bool         $is_assoc  whether the input is an associative array
     * @return  bool         false when value isn't found in the array, otherwise true
     */
    public static function insert_after_value(array &$original, $value, $search, $is_assoc = false)
    {
        $key = array_search($search, $original);

        if ($key === false)
        {
            trigger_error('Unknown value after which to insert the new value into the array.');
            return false;
        }

        return static::insert_after_key($original, $value, $key, $is_assoc);
    }

    /**
     * Insert value(s) into an array before a specific value (first found in array)
     *
     * @param   array        $original  the original array (by reference)
     * @param   array|mixed  $value     the value(s) to insert, if you want to insert an array it needs to be in an array itself
     * @param   string|int   $search    the value after which to insert
     * @param   bool         $is_assoc  whether the input is an associative array
     * @return  bool         false when value isn't found in the array, otherwise true
     */
    public static function insert_before_value(array &$original, $value, $search, $is_assoc = false)
    {
        $key = array_search($search, $original);

        if ($key === false)
        {
            trigger_error('Unknown value before which to insert the new value into the array.');
            return false;
        }

        return static::insert_before_key($original, $value, $key, $is_assoc);
    }

    /**
     * Sorts a multi-dimensional array by it's values.
     *
     * @access  public
     * @param   array   $array       The array to fetch from
     * @param   string  $key         The key to sort by
     * @param   string  $order       The order (asc or desc)
     * @param   int     $sort_flags  The php sort type flag
     * @return  array
     */
    public static function sort($array, $key, $order = 'asc', $sort_flags = SORT_REGULAR)
    {
        if ( ! is_array($array))
        {
            throw new \InvalidArgumentException('cls_arr::sort() - $array must be an array.');
        }

        if (empty($array))
        {
            return $array;
        }

        foreach ($array as $k => $v)
        {
            $b[$k] = static::get($v, $key);
        }

        switch ($order)
        {
        case 'asc':
            asort($b, $sort_flags);
            break;

        case 'desc':
            arsort($b, $sort_flags);
            break;

        default:
            throw new \InvalidArgumentException('cls_arr::sort() - $order must be asc or desc.');
            break;
        }

        foreach ($b as $key => $val)
        {
            $c[] = $array[$key];
        }

        return $c;
    }

    /**
     * Sorts an array on multiple values, with deep sorting support.
     *
     * @param   array  $array        collection of arrays/objects to sort
     * @param   array  $conditions   sorting conditions
     * @param   bool   $ignore_case  whether to sort case insensitive
     * @return  array
     */
    public static function multisort($array, $conditions, $ignore_case = false)
    {
        $temp = array();
        $keys = array_keys($conditions);

        foreach($keys as $key)
        {
            $temp[$key] = static::pluck($array, $key, true);
            is_array($conditions[$key]) or $conditions[$key] = array($conditions[$key]);
        }

        $args = array();
        foreach ($keys as $key)
        {
            $args[] = $ignore_case ? array_map('strtolower', $temp[$key]) : $temp[$key];
            foreach($conditions[$key] as $flag)
            {
                $args[] = $flag;
            }
        }

        $args[] = &$array;

        call_user_func_array('array_multisort', $args);
        return $array;
    }

    public static function is_sort($array, $order = 'asc')
    {
        if ( ! is_array($array))
        {
            throw new \InvalidArgumentException('cls_arr::is_sort() - $array must be an array.');
        }

        $array = array_values($array);

        if ( $order == 'asc' ) 
        {
            $asc_array = $array;
            sort($asc_array);
            return $asc_array === $array;
        }
        else 
        {
            $desc_array = $array;
            arsort($desc_array);
            $desc_array = array_values($desc_array);
            return  $desc_array === $array;
        }
    }

    /**
     * Find the average of an array
     *
     * @param   array   $array  the array containing the values
     * @return  number          the average value
     */
    public static function average($array)
    {
        // No arguments passed, lets not divide by 0
        if ( ! ($count = count($array)) > 0)
        {
            return 0;
        }

        return (array_sum($array) / $count);
    }

    /**
     * Replaces key names in an array by names in $replace
     *
     * @param   array           $source   the array containing the key/value combinations
     * @param   array|string    $replace  key to replace or array containing the replacement keys
     * @param   string          $new_key  the replacement key
     * @return  array                     the array with the new keys
     */
    public static function replace_key($source, $replace, $new_key = null)
    {
        if(is_string($replace))
        {
            $replace = array($replace => $new_key);
        }

        if ( ! is_array($source) or ! is_array($replace))
        {
            throw new \InvalidArgumentException('cls_arr::replace_key() - $source must an array. $replace must be an array or string.');
        }

        $result = array();

        foreach ($source as $key => $value)
        {
            if (array_key_exists($key, $replace))
            {
                $result[$replace[$key]] = $value;
            }
            else
            {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Merge 2 arrays recursively, differs in 2 important ways from array_merge_recursive()
     * - When there's 2 different values and not both arrays, the latter value overwrites the earlier
     *   instead of merging both into an array
     * - Numeric keys that don't conflict aren't changed, only when a numeric key already exists is the
     *   value added using array_push()
     *
     * @return  array
     * @throws  \InvalidArgumentException
     */
    public static function merge()
    {
        $array  = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if ( ! is_array($array))
        {
            throw new \InvalidArgumentException('cls_arr::merge() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr)
        {
            if ( ! is_array($arr))
            {
                throw new \InvalidArgumentException('cls_arr::merge() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v)
            {
                // numeric keys are appended
                if (is_int($k))
                {
                    array_key_exists($k, $array) ? $array[] = $v : $array[$k] = $v;
                }
                elseif (is_array($v) and array_key_exists($k, $array) and is_array($array[$k]))
                {
                    $array[$k] = static::merge($array[$k], $v);
                }
                else
                {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }

    /**
     * Merge 2 arrays recursively, differs in 2 important ways from array_merge_recursive()
     * - When there's 2 different values and not both arrays, the latter value overwrites the earlier
     *   instead of merging both into an array
     * - Numeric keys are never changed
     *
     * @return  array
     * @throws  \InvalidArgumentException
     */
    public static function merge_assoc()
    {
        $array  = func_get_arg(0);
        $arrays = array_slice(func_get_args(), 1);

        if ( ! is_array($array))
        {
            throw new \InvalidArgumentException('cls_arr::merge_assoc() - all arguments must be arrays.');
        }

        foreach ($arrays as $arr)
        {
            if ( ! is_array($arr))
            {
                throw new \InvalidArgumentException('cls_arr::merge_assoc() - all arguments must be arrays.');
            }

            foreach ($arr as $k => $v)
            {
                if (is_array($v) and array_key_exists($k, $array) and is_array($array[$k]))
                {
                    $array[$k] = static::merge_assoc($array[$k], $v);
                }
                else
                {
                    $array[$k] = $v;
                }
            }
        }

        return $array;
    }

    /**
     * Prepends a value with an associative key to an array.
     * Will overwrite if the value exists.
     *
     * @param   array           $arr     the array to prepend to
     * @param   string|array    $key     the key or array of keys and values
     * @param   mixed           $value   the value to prepend
     */
    public static function prepend(&$arr, $key, $value = null)
    {
        $arr = (is_array($key) ? $key : array($key => $value)) + $arr;
    }

    /**
     * Recursive in_array
     *
     * @param   mixed  $needle    what to search for
     * @param   array  $haystack  array to search in
     * @param   bool   $strict
     * @return  bool   whether the needle is found in the haystack.
     */
    public static function in_array_recursive($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $value)
        {
            if ( ! $strict and $needle == $value)
            {
                return true;
            }
            elseif ($needle === $value)
            {
                return true;
            }
            elseif (is_array($value) and static::in_array_recursive($needle, $value, $strict))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the given array is a multidimensional array.
     *
     * @param   array  $arr       the array to check
     * @param   bool   $all_keys  if true, check that all elements are arrays
     * @return  bool   true if its a multidimensional array, false if not
     */
    public static function is_multi($arr, $all_keys = false)
    {
        $values = array_filter($arr, 'is_array');
        return $all_keys ? count($arr) === count($values) : count($values) > 0;
    }

    /**
     * Searches the array for a given value and returns the
     * corresponding key or default value.
     * If $recursive is set to true, then the cls_arr::search()
     * function will return a delimiter-notated key using $delimiter.
     *
     * @param   array   $array     The search array
     * @param   mixed   $value     The searched value
     * @param   string  $default   The default value
     * @param   bool    $recursive Whether to get keys recursive
     * @param   string  $delimiter The delimiter, when $recursive is true
     * @param   bool    $strict    If true, do a strict key comparison
     * @return  mixed
     */
    public static function search($array, $value, $default = null, $recursive = true, $delimiter = '.', $strict = false)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        if ( ! is_null($default) and ! is_int($default) and ! is_string($default))
        {
            throw new \InvalidArgumentException('Expects parameter 3 to be an string or integer or null.');
        }

        if ( ! is_string($delimiter))
        {
            throw new \InvalidArgumentException('Expects parameter 5 must be an string.');
        }

        $key = array_search($value, $array, $strict);

        if ($recursive and $key === false)
        {
            $keys = array();
            foreach ($array as $k => $v)
            {
                if (is_array($v))
                {
                    $rk = static::search($v, $value, $default, true, $delimiter, $strict);
                    if ($rk !== $default)
                    {
                        $keys = array($k, $rk);
                        break;
                    }
                }
            }
            $key = count($keys) ? implode($delimiter, $keys) : false;
        }

        return $key === false ? $default : $key;
    }

    /**
     * 通过表达式查询
     * 
     * @param array $array          要查询的数组
     * @param string $expression    查询表达式
     * @return void
     *
     * exp:
       $data = array (
           array ( "name" => "bill", "age" => 40 ),
           array ( "name" => "john", "age" => 30 ),
           array ( "name" => "jack", "age" => 50 ),
           array ( "name" => "john", "age" => 25 )
       );
       print_r( cls_arr::arr_search($data, "age>=30") );
       print_r( cls_arr::arr_search($data, "name=='john'") );
       print_r( cls_arr::arr_search($data, "age>25 and name=='john'") );
       print_r( cls_arr::arr_search($data, "age>40 and name=='bill,jack'") );
     */
    public static function arr_search(array $array, string $expression) 
    {
        if ( !$array ) 
        {
            return $array;
        }

        $result = array();
        //$expression = preg_replace_callback( "/([^\s]+?)([=<>!]{1,})/" , function($match) {
            //return "\$a['{$match[1]}'] {$match[2]} ";
        //}, $expression );
        //foreach ( $array as $a ) if ( eval ( "return $expression;" ) ) $result [] = $a ;
        $expression = preg_replace_callback( "/([^\s]+?)([=<>!]{1,})[A-Za-z0-9,']+/" , function($match) {
            $item_arr = explode($match[2], $match[0]);
            if ( $match[2] == '==' && strpos($match[0], ',') !== false )
            {
                $item_arr = explode($match[2], $match[0]);
                if (is_string($item_arr[1]))
                {
                    $item_arr[1] = str_replace('\'', '', $item_arr[1]);
                    $item_arr[1] = array_map(function($item){return "'".$item."'";}, explode(',', $item_arr[1]));
                    $item_arr[1] = implode(',', $item_arr[1]);
                }
                return 'in_array($a[\''.$item_arr[0].'\'],['.$item_arr[1].'])';
            }
            return "\$a['{$match[1]}'] {$match[2]} {$item_arr[1]}";
        }, $expression );

        if ( count($array) == count($array, COUNT_RECURSIVE) ) 
        {
            $a = $array;
            if ( eval ( "return $expression;" ) ) $result = $a ;
        } 
        else 
        {
            foreach ( $array as $a ) if ( eval ( "return $expression;" ) ) $result [] = $a ;
        }
        return $result ;
    }

    /**
     * Returns only unique values in an array. It does not sort. First value is used.
     *
     * @param   array  $arr       the array to dedup
     * @return  array   array with only de-duped values
     */
    public static function unique($arr)
    {
        // filter out all duplicate values
        return array_filter($arr, function($item)
        {
            // contrary to popular belief, this is not as static as you think...
            static $vars = array();

            if (in_array($item, $vars, true))
            {
                // duplicate
                return false;
            }
            else
            {
                // record we've had this value
                $vars[] = $item;

                // unique
                return true;
            }
        });
    }

    /**
     * Calculate the sum of an array
     *
     * @param   array   $array  the array containing the values
     * @param   string  $key    key of the value to pluck
     * @return  number          the sum value
     */
    public static function sum($array, $key)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        return array_sum(static::pluck($array, $key));
    }

    /**
     * Returns the array with all numeric keys re-indexed, and string keys untouched
     *
     * @param   array  $arr       the array to reindex
     * @return  array  re-indexed array
     */
    public static function reindex($arr)
    {
        // reindex this level
        $arr = array_merge($arr);

        foreach ($arr as $k => &$v)
        {
            is_array($v) and $v = static::reindex($v);
        }

        return $arr;
    }

    /**
     * Get the previous value or key from an array using the current array key
     *
     * @param   array    $array      the array containing the values
     * @param   string   $key        key of the current entry to use as reference
     * @param   bool     $get_value  if true, return the previous value instead of the previous key
     * @param   bool     $strict     if true, do a strict key comparison
     *
     * @return  mixed  the value in the array, null if there is no previous value, or false if the key doesn't exist
     */
    public static function prev_by_key($array, $key, $get_value = false, $strict = false)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false)
        {
            // key does not exist
            return false;
        }

        // check if we have a previous key
        elseif ( ! isset($keys[$index-1]))
        {
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index-1]] : $keys[$index-1];
    }

    /**
     * Get the next value or key from an array using the current array key
     *
     * @param   array    $array      the array containing the values
     * @param   string   $key        key of the current entry to use as reference
     * @param   bool     $get_value  if true, return the next value instead of the next key
     * @param   bool     $strict     if true, do a strict key comparison
     *
     * @return  mixed  the value in the array, null if there is no next value, or false if the key doesn't exist
     */
    public static function next_by_key($array, $key, $get_value = false, $strict = false)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // get the keys of the array
        $keys = array_keys($array);

        // and do a lookup of the key passed
        if (($index = array_search($key, $keys, $strict)) === false)
        {
            // key does not exist
            return false;
        }

        // check if we have a previous key
        elseif ( ! isset($keys[$index+1]))
        {
            // there is none
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index+1]] : $keys[$index+1];
    }

    /**
     * Get the previous value or key from an array using the current array value
     *
     * @param   array    $array      the array containing the values
     * @param   string   $value      value of the current entry to use as reference
     * @param   bool     $get_value  if true, return the previous value instead of the previous key
     * @param   bool     $strict     if true, do a strict key comparison
     *
     * @return  mixed  the value in the array, null if there is no previous value, or false if the key doesn't exist
     */
    public static function prev_by_value($array, $value, $get_value = true, $strict = false)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false)
        {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no previous one, bail out
        if ( ! isset($keys[$index-1]))
        {
            return null;
        }

        // return the value or the key of the array entry the previous key points to
        return $get_value ? $array[$keys[$index-1]] : $keys[$index-1];
    }

    /**
     * Get the next value or key from an array using the current array value
     *
     * @param   array    $array      the array containing the values
     * @param   string   $value      value of the current entry to use as reference
     * @param   bool     $get_value  if true, return the next value instead of the next key
     * @param   bool     $strict     if true, do a strict key comparison
     *
     * @return  mixed  the value in the array, null if there is no next value, or false if the key doesn't exist
     */
    public static function next_by_value($array, $value, $get_value = true, $strict = false)
    {
        if ( ! is_array($array) and ! $array instanceof \ArrayAccess)
        {
            throw new \InvalidArgumentException('First parameter must be an array or ArrayAccess object.');
        }

        // find the current value in the array
        if (($key = array_search($value, $array, $strict)) === false)
        {
            // bail out if not found
            return false;
        }

        // get the list of keys, and find our found key
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        // if there is no next one, bail out
        if ( ! isset($keys[$index+1]))
        {
            return null;
        }

        // return the value or the key of the array entry the next key points to
        return $get_value ? $array[$keys[$index+1]] : $keys[$index+1];
    }

    /**
     * Return the subset of the array defined by the supplied keys.
     *
     * Returns $default for missing keys, as with cls_arr::get()
     *
     * @param   array    $array    the array containing the values
     * @param   array    $keys     list of keys (or indices) to return
     * @param   mixed    $default  value of missing keys; default null
     *
     * @return  array  An array containing the same set of keys provided.
     */
    public static function subset(array $array, array $keys, $default = null)
    {
        $result = array();

        foreach ($keys as $key)
        {
            static::set($result, $key, static::get($array, $key, $default));
        }

        return $result;
    }

    /**
     * 二维数组去重
     * 
     * @param array $arr
     * @param mixed $key
     * @return array
     */
    public static function array_unset_repeat( array $arr, $key )
    {
        // 建立一个目标数组
        $res = array();
        foreach ($arr as $value) 
        {
            // 查看有没有重复项
            if (isset($res[$value[$key]])) 
            {
                // 有：销毁
                unset($value[$key]);
            } 
            else 
            {
                $res[$value[$key]] = $value;
            }
        }
        return $res;
    }

    /**
     * 判断是否为多维数组
     * @param    mixed      $arr 
     * @param    int        1包含计算算属性中的是否有数组 2.只计算数据
     * @return   boolean       
     */
    public static function is_multiple_arr($arr, int $mode = 1): bool
    {
        if ( !is_array($arr) || count($arr) <= 0 ) 
        {
            return false;
        }

        if ( 1 == $mode ) 
        {
            $is_multi = count($arr) == count($arr, COUNT_RECURSIVE); 
        }
        else
        {
            $is_multi = true;
            foreach($arr as $v)
            {
                if ( !is_array($v) ) 
                {
                    $is_multi = false;
                    break;
                }
            }
        }

        return $is_multi;
    }

    /**
     * 根据group_fields合并2个数组，有则相加，没有补全
     * @param    array      $arr_a       
     * @param    array      $arr_b       
     * @param    array      $group_fields  合并字段
     * @param    array|null $fields        为null,会自动获取$arr_a $arr_b 所有key
     * @return   array        
     */
    public static function sum_group_by_fields(array $arr_a, array $arr_b, array $group_fields, ?array $fields = null)
    {
        if ( !$fields ) 
        {
            foreach(['arr_a', 'arr_b'] as $g)
            {
                 foreach(${$g} as $k => $v)
                 {
                    $fields = array_merge($fields ?? [], array_keys($v));
                 }
            }
        }
        else
        {
            $fields = array_merge($fields, $group_fields);
        }

        if ( !$fields ) 
        {
            return [];
            // throw new \Exception('Ivalidate Fields parameters.');
        }

        $fields   = array_unique($fields);
        $new_data = [];
        foreach(['arr_a', 'arr_b'] as $g)
        {
            foreach(${$g} as $k => $v)
            {
                $_index = [];
                foreach($group_fields as $f)
                {
                    $_index[] = $v[$f] ?? '';
                }

                $new_v = [];
                foreach($fields as $f)
                {
                    $new_v[$f] = $v[$f] ?? (in_array($f, $group_fields) ? '' : 0);
                }

                $index = implode(':', $_index);
                if ( !isset($new_data[$index]) ) 
                {
                    $new_data[$index] = $new_v;
                }
                else
                {
                    foreach($fields as $f)
                    {
                        if ( in_array($f, $group_fields) )
                        {
                            $new_data[$index][$f] = $new_v[$f];
                        }
                        else if ( is_numeric($new_v[$f]) && is_numeric($new_data[$index][$f]) ) 
                        {
                            $new_data[$index][$f] += $new_v[$f];
                        }
                        else
                        {
                            $new_data[$index][$f] = $new_data[$index][$f] ?: $new_v[$f];
                        }
                    }
                }
            }
        }

        return array_values($new_data);
    }

}
