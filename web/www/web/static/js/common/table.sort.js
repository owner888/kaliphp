/**
 * Created by dawson.
 * 页面使用示例(eg: gcam_bookmark.index.tpl):
 * 
 * <form id="search-form">
        <input type="hidden" name="order_name" value="<{request_em key='order_name'}>" id="order_name" />
        <input type="hidden" name="order_desc" value="<{request_em key='order_desc'}>" id="order_desc" />
 * </form>

 * 在需要 sort 的表格头设置：
    <th><a href="javascript:;" class="list-sort" rel="name"><{lang key='common_name'}><i class="fa fa-sort sort"></i></a></th>
 */

$(function(){
    // 从 url 拿到两个参数，找到排序那一列，给 icon 做样式，这里如果是多列排序查询，会有问题，需要修改order_name 成多个 #FIXME
    const { order_desc = '', order_name = '' } = qs;
    if(!!order_desc && !!order_name){
        $('.list-sort[rel="'+order_name+'"]').find('.sort').removeClass().addClass(`fa fa-sort-${order_desc} sort`);
    }
    $(".list-sort").on('click', function() {
        let relValue = $(this).attr('rel');
        let seq = 'desc';
        if(qs.order_name === relValue){
            seq = !qs.order_desc ? 'desc' : (qs.order_desc === 'desc') ? 'asc' : (qs.order_desc === 'asc') ? '' : 'desc';
        }
        $('#order_name').val(seq === '' ? '' : relValue);
        $('#order_desc').val(seq);

        // 表单提交
        $('#search-form').submit();
    })
})