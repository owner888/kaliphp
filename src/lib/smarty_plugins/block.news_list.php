<?php
/**
 * 自定义列表模板标签
 *
 * <table width="820" border="1" cellspacing="0" cellpadding="0">  
 *     <{news_list assign='row' name='test'}>  
 *     <tr>  
 *         <th scope="col"><{$_bindex.test}></th>  
 *         <th scope="col"><{$row.uid}></th>  
 *         <th scope="col"><{$row.username}></th>  
 *     </tr>  
 *     <{/news_list}>  
 * </table>
 *
 */
function smarty_block_news_list($params, $content, &$smarty, &$repeat)
{ 
    extract($params);

    if(!isset($assign))
    {
        $assign = 'row';
    }

    // 注册一个block的索引，照顾smarty的版本
    if(method_exists($smarty,'get_template_vars'))
    {
        $_bindex = $smarty->get_template_vars('_bindex');
    }
    else
    {
        $_bindex = $smarty->getVariable('_bindex')->value;
    }

    if(!$_bindex)
    {
        $_bindex = array();
    }

    if($name)
    {
        if(!isset($_bindex[$name]))
        {
            $_bindex[$name] = 1;
        }
        else
        {
            $_bindex[$name] ++;
        }
    }
    $smarty->assign('_bindex', $_bindex);

    // 获得一个本区块的专属数据存储空间
    $dataindex = md5(__FUNCTION__ . md5(serialize($params)));
    $dataindex = substr($dataindex,0,16);
    // 将使用tpl::$blocksdata[$dataindex]来存储
    // 填充数据
    if(!isset(tpl::$blocksdata[$dataindex]))
    {
        #************************************************************************
        #主要数据填充区
        $rs = db::get_all("select * from call_admin limit 10");
        tpl::$blocksdata[$dataindex] = $rs;

        #填充区完成
        #************************************************************************
    }

    // 如果没有数据，直接返回null,不必再执行了
    if(!tpl::$blocksdata[$dataindex])
    {
        $repeat = false;
        return '';
    }

    // 取一条数据出栈，并把它指派给$assign，重复执行开关置位1
    if(list($key, $item) = each(tpl::$blocksdata[$dataindex]))
    {
        $smarty->assign($assign, $item);
        $repeat = true;
    }

    // 如果已经到达最后，重置数组指针，重复执行开关置位0
    if(!$item)
    {
        reset(tpl::$blocksdata[$dataindex]);
        $repeat = false;
        if($name)
        {
            unset($_bindex[$name]);
            $smarty->assign('_bindex', $_bindex);
        }
    }

    // 打印内容
    print $content;
}
