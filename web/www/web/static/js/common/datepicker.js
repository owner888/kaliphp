/**
 * Created by dawson.
 * 基于 layui 和 jquery 的日期组件封装，页面必须要引入对应的 layui.css 和 layui.js/jquery.js
 * 页面使用示例(eg: member.index.tpl):
 * 
 * <div class="input-group" id="laydate-range">
        <span class="input-group-addon">
            <i class="fa fa-calendar" aria-hidden="true"></i>
        </span>
        <input type="text" class="form-control" id="laydate-start" name="date_sta" placeholder="Start date" value="<{request_em key='date_sta' default=$date_sta}>" />
        <span class="input-group-addon input-datepick-divid"> to </span>
        <input type="text" class="form-control" id="laydate-end" name="date_end" placeholder="End date" value="<{request_em key='date_end' default=$date_end}>" />
    </div>
 */

$(function(){
    layui.use(function() {
        const laydate = layui.laydate;
        const util = layui.util;

        laydate.render({
            elem: "#laydate-range",
            range: ['#laydate-start', '#laydate-end'],
            rangeLinked: true,
            shortcuts: [
            {
                text: "昨天",
                value: function(){
                    var now = new Date();
                    now.setDate(now.getDate() - 1);
                    return [now, now];
                }
            },
            { 
                text: "今天",
                value: function(){
                    return [Date.now(), Date.now()];
                } 
            },
            {
                text: "过去7天",
                value: function(){
                    var now = new Date();
                    return [now.setDate(now.getDate() - 7), now.setDate(now.getDate() + 6)];
                }
            },
            {
                text: "上个月",
                value: function(){
                    const today = new Date();
                    const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                    const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

                    return [firstDayOfLastMonth, lastDayOfLastMonth];
                }
            },
            {
                text: "本周",
                value: function(){
                    const today = new Date();
                    const thisSunday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay());
                    
                    return [thisSunday, today];
                }
            },
            {
                text: "上周",
                value: function(){
                    const today = new Date();
                    // 上周开始是从星期天算，今天日期减 7 再减去具体星期几，就到了上周的周日
                    const lastSunday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7 - today.getDay());

                    // 同理，上周开始的周日+ 6，就是上周最后一天，肯定是周六
                    const lastSaturday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay() - 1);

                    return [lastSunday, lastSaturday];
                }
            },
            ]
        });
    })
})