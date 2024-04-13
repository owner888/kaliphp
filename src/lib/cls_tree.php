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
 * 通用的树型类，可以生成任何树型结构 
 *
 * @version $Id$  
 */
class cls_tree {

    public $str = '';

    /** 
     * 生成树型结构所需要的2维数组 
     * @var array 
     */  
    public $arr = array();  

    /** 
     * 生成树型结构所需修饰符号，可以换成图片 
     * @var array 
     */  
    public $icon = array('│','├','└');  
    public $nbsp = "&emsp;";  

    /** 
     * @access private 
     */  
    public $ret = '';  

    /** 
     * 构造函数，初始化类 
     * @param array 2维数组，例如： 
     * array( 
     *     1 => array('id'=>'1','parentid'=>0,'name'=>'一级栏目一'), 
     *     2 => array('id'=>'2','parentid'=>0,'name'=>'一级栏目二'), 
     *     3 => array('id'=>'3','parentid'=>1,'name'=>'二级栏目一'), 
     *     4 => array('id'=>'4','parentid'=>1,'name'=>'二级栏目二'), 
     *     5 => array('id'=>'5','parentid'=>2,'name'=>'二级栏目三'), 
     *     6 => array('id'=>'6','parentid'=>3,'name'=>'三级栏目一'), 
     *     7 => array('id'=>'7','parentid'=>3,'name'=>'三级栏目二') 
     * ) 
     */  
    public function __construct($arr=array())
    {  
        $this->arr = $arr;  
        $this->ret = '';  
        return is_array($arr);  
    }  

    /** 
     * 得到父级数组 
     * @param int 
     * @return array 
     */  
    public function get_parent($myid)
    {  
        $newarr = array();  
        if(!isset($this->arr[$myid])) return false;  
        $pid = $this->arr[$myid]['parentid'];  
        $pid = $this->arr[$pid]['parentid'];  
        if(is_array($this->arr))
        {  
            foreach($this->arr as $id => $a)
            {  
                if($a['parentid'] == $pid) $newarr[$id] = $a;  
            }  
        }  
        return $newarr;  
    }  

    /** 
     * 得到子级数组 
     * @param int 
     * @return array 
     */  
    public function get_child($myid)
    {  
        $a = $newarr = array();  
        if(is_array($this->arr))
        {  
            foreach($this->arr as $id => $a)
            {  
                if($a['parentid'] == $myid) $newarr[$id] = $a;  
            }  
        }  
        return $newarr ? $newarr : false;  
    }  

    /** 
     * 得到当前位置数组 
     * @param int 
     * @return array 
     */  
    public function get_pos($myid,&$newarr)
    {  
        $a = array();  
        if(!isset($this->arr[$myid])) return false;  
        $newarr[] = $this->arr[$myid];  
        $pid = $this->arr[$myid]['parentid'];  
        if(isset($this->arr[$pid]))
        {  
            $this->get_pos($pid,$newarr);  
        }  
        if(is_array($newarr))
        {  
            krsort($newarr);  
            foreach($newarr as $v)
            {  
                $a[$v['id']] = $v;  
            }  
        }  
        return $a;  
    }

    public function dump($var, $echo=true, $label=null, $strict=true) 
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) 
        {
            if (ini_get('html_errors')) 
            {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } 
            else 
            {
                $output = $label . print_r($var, true);
            }
        }
        else 
        {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) 
            {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) 
        {
            echo($output);
            return null;
        }
        else
        {
            return $output;
        }
    }

    /**
     * 获取该父级所有的子级
     * @param $parent_id
     * @param $new_arr
     * @return array
     */
    public function get_all_child($parent_id)
    {
        static $tree;
        static $new_arr;
        if(!isset($new_arr))
        {
            $new_arr = $this->arr;
        }

        foreach($new_arr as $key=>$row)
        {
            if($row['parentid'] == $parent_id)
            {
                $tree[] = $row;
                unset($new_arr[$key]);
                $this->get_all_child($row['id'],$new_arr);
            }
        }

        return $tree;
    }

    /**
     * 获取该父级所有的子级id
     * @param $parent_id
     * @param $new_arr
     * @return array
     */
    public function get_all_child_ids($parent_id,&$new_arr)
    {
        static $ids;

        if(!isset($new_arr))
        {
            $new_arr = $this->arr;
        }

        foreach($new_arr as $key=>$row)
        {
            if($row['parentid'] == $parent_id) {
                $ids[] = $row['id'];
                unset($new_arr[$key]);
                $this->get_all_child($row['id'],$new_arr);
            }
        }

        return $ids;
    }

    /** 
     * 得到树型结构 
     * @param int ID，表示获得这个ID下的所有子级 
     * @param string 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>" 
     * @param int 被选中的ID，比如在做树型下拉框的时候需要用到 
     * @param string 前缀 
     * @return string 
     */  
    public function get_tree($myid, $str, $sel_id = 0, $dis_id = 0, $adds = '', $str_group = '')
    {  
        $number = 1;  
        $child = $this->get_child($myid);  
        if(is_array($child))
        {  
            $total = count($child);  
            $nstr = '';
            foreach($child as $id=>$value)
            {  
                $j = $k = '';  
                if($number==$total)
                {  
                    $j .= $this->icon[2];  
                }
                else
                {  
                    $j .= $this->icon[1];  
                    $k = $adds ? $this->icon[0] : '';  
                }  
                $spacer = $adds ? $adds.$j : '';  
                $selected = $id==$sel_id ? 'selected' : '';  
                $disabled = $dis_id!=0 && $id==$dis_id ? 'disabled' : '';  
                @extract($value);  
                $parentid == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");  
                $this->ret .= $nstr;  
                $nbsp = $this->nbsp;  
                if ($id != 0) 
                {
                    $this->get_tree($id, $str, $sel_id, $dis_id, $adds.$k.$nbsp,$str_group);  
                }
                $number++;  
            }  
        }  
        return $this->ret;  
    }  

    /** 
     * 同上一方法类似,但允许多选 
     */  
    public function get_tree_multi($myid, $str, $sel_id = 0, $adds = '')
    {  
        $number = 1;  
        $child = $this->get_child($myid);  
        if(is_array($child))
        {  
            $total = count($child);  
            $nstr = '';
            foreach($child as $id=>$a)
            {  
                $j=$k='';  
                if($number==$total)
                {  
                    $j .= $this->icon[2];  
                }
                else
                {  
                    $j .= $this->icon[1];  
                    $k = $adds ? $this->icon[0] : '';  
                }  
                $spacer = $adds ? $adds.$j : '';  

                $selected = $this->have($sel_id,$id) ? 'selected' : '';  
                @extract($a);  
                eval("\$nstr = \"$str\";");  
                $this->ret .= $nstr;  
                if ($id != 0) 
                {
                    $this->get_tree_multi($id, $str, $sel_id, $adds.$k.' ');  
                }
                $number++;  
            }  
        }  
        return $this->ret;  
    }  

    /** 
     * @param integer $myid 要查询的ID 
     * @param string $str   第一种HTML代码方式 
     * @param string $str2  第二种HTML代码方式 
     * @param integer $sel_id  默认选中 
     * @param integer $adds 前缀 
     */  
    public function get_tree_category($myid, $str, $str2, $sel_id = 0, $adds = '')
    {  
        $number = 1;  
        $child = $this->get_child($myid);  
        if(is_array($child))
        {  
            $total = count($child);  
            $nstr = '';
            foreach($child as $id=>$a)
            {  
                $j=$k='';  
                if($number==$total)
                {  
                    $j .= $this->icon[2];  
                }
                else
                {  
                    $j .= $this->icon[1];  
                    $k = $adds ? $this->icon[0] : '';  
                }  
                $spacer = $adds ? $adds.$j : '';  

                $selected = $this->have($sel_id, $id) ? 'selected' : '';  
                @extract($a);  
                if (empty($html_disabled)) 
                {  
                    eval("\$nstr = \"$str\";");  
                }
                else 
                {  
                    eval("\$nstr = \"$str2\";");  
                }  
                $this->ret .= $nstr;  
                if ($id != 0) 
                {
                    $this->get_tree_category($id, $str, $str2, $sel_id, $adds.$k.' ');  
                }
                $number++;  
            }  
        }  
        return $this->ret;  
    }  

    /** 
     * 同上一类方法，jquery treeview 风格，可伸缩样式（需要treeview插件支持） 
     * @param $myid 表示获得这个ID下的所有子级 
     * @param $effected_id 需要生成treeview目录数的id 
     * @param $str 末级样式 
     * @param $str2 目录级别样式 
     * @param $showlevel 直接显示层级数，其余为异步显示，0为全部限制 
     * @param $style 目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam' 
     * @param $currentlevel 计算当前层级，递归使用 适用改函数时不需要用该参数 
     * @param $recursion 递归使用 外部调用时为FALSE 
     */  
    public function get_treeview($myid,$effected_id='example',$str="<span class='file'>\$name</span>",$str2="<span class='folder'>\$name</span>",$showlevel = 0 ,$style='filetree ',$currentlevel = 1,$recursion=FALSE) 
    {  
        $child = $this->get_child($myid);  
        if(!defined('EFFECTED_INIT'))
        {  
            $effected = ' id="'.$effected_id.'"';  
            define('EFFECTED_INIT', 1);  
        } 
        else 
        {  
            $effected = '';  
        }  

        $placeholder =  '<ul><li><span class="placeholder"></span></li></ul>';  
        if(!$recursion) $this->str .='<ul'.$effected.'  class="'.$style.'">';
        if($child)
        {
            $nstr = '';
            foreach($child as $id=>$a)
            {
                @extract($a);
                if($showlevel > 0 && $showlevel == $currentlevel && $this->get_child($id)) $folder = 'hasChildren'; //如设置显示层级模式@2011.07.01
                $floder_status = isset($folder) ? ' class="'.$folder.'"' : '';
                $this->str .= $recursion ? '<ul><li'.$floder_status.' id=\''.$id.'\'>' : '<li'.$floder_status.' id=\''.$id.'\'>';
                $recursion = FALSE;
                if($this->get_child($id))
                {
                    eval("\$nstr = \"$str2\";");
                    $this->str .= $nstr;
                    if( $id != 0 && ($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) )
                    {
                        $this->get_treeview($id, $effected_id, $str, $str2, $showlevel, $style, $currentlevel+1, TRUE);
                    }
                    elseif($showlevel > 0 && $showlevel == $currentlevel)
                    {
                        $this->str .= $placeholder;
                    }
                }
                else
                {
                    eval("\$nstr = \"$str\";");
                    $this->str .= $nstr;
                }
                $this->str .=$recursion ? '</li></ul>': '</li>';
            }
        }

        if(!$recursion)  $this->str .='</ul>';  
        return $this->str;  
    }  

    /** 
     * 获取子栏目json 
     * Enter description here ... 
     * @param unknown_type $myid 
     */  
    public function creat_sub_json($myid, $str='') 
    {  
        $sub_cats = $this->get_child($myid);  
        $n = 0;  
        if(is_array($sub_cats)) foreach($sub_cats as $c) 
        {            
            $data[$n]['id'] = iconv(CHARSET,'utf-8',$c['catid']);  
            if($this->get_child($c['catid']))
            {  
                $data[$n]['liclass'] = 'hasChildren';  
                $data[$n]['children'] = array(array('text'=>' ','classes'=>'placeholder'));  
                $data[$n]['classes'] = 'folder';  
                $data[$n]['text'] = iconv(CHARSET,'utf-8',$c['catname']);  
            } 
            else 
            {                  
                if($str) 
                {  
                    @extract(array_iconv($c,CHARSET,'utf-8'));  
                    eval("\$data[$n]['text'] = \"$str\";");  
                } 
                else 
                {  
                    $data[$n]['text'] = iconv(CHARSET,'utf-8',$c['catname']);  
                }  
            }  
            $n++;  
        }  
        return json_encode($data);        
    }  

    private function have($list,$item)
    {  
        return(strpos(',,'.$list.',',','.$item.','));  
    }  
}  


////模拟数据库  
//$data=array(  
    //array('id'=>1,'pid'=>0,'name'=>'一级栏目一'),  
    //array('id'=>2,'pid'=>0,'name'=>'一级栏目二'),  
    //array('id'=>3,'pid'=>1,'name'=>'二级栏目一'),  
    //array('id'=>4,'pid'=>3,'name'=>'三级栏目一'),  
    //array('id'=>5,'pid'=>4,'name'=>'四级栏目一'),  
//);  

////转换数据  
//$tree_data = array();  
//foreach ($data as $key=>$value)
//{  
    //$tree_data[$value['id']] = array(  
        //'id'=>$value['id'],  
        //'parentid'=>$value['pid'],  
        //'name'=>$value['name']  
    //);  
//}  

/** 
 * 输出树形结构 
 */  
//$str="<tr>  
    //<td><input type='checkbox' name='list[\$id]' value='\$id'></td>  
    //<td>\$id</td>  
    //<td>\$spacer\$name</td>  
    //<td><a href='add.php?id=\$id'>添加</a></td>  
    //<td><a href='del.php?id=\$id'>删除</a></td>  
    //<td><a href='update.php?id=\$id'>修改</a></td>  
    //</tr>";  

//$tree = new cls_tree();  
//$tree->init($tree_data);  
//echo "<table>";  
//echo $tree->get_tree(0, $str);  
//echo "</table>";  


//echo "<br/>";  
//echo "<br/>";  
//echo "<br/>";  
//echo "<br/>";  

/** 
 * 输出下拉列表 
 */  
//$str="<option value=\$id \$selected>\$spacer\$name</option>";  
//$tree = new cls_tree();  
//$tree->init($tree_data);  
//echo "<select>";  
//echo $tree->get_tree(0, $str, 2);  
//echo "</select>";
