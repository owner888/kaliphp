<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
    <style type="text/css">
        .table_select > .btn{
            border-radius: 0px;
        }
    </style>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">
                <div class="widget-content p-b-none">
                    <form class="form-inline" id="search_form" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                       <div class="form-group">
                            <select name="is_pro" class="form-control">         
                                <{html_options options=$is_pro_options selected=$is_pro}>       
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="input-daterange" data-language="zh-CN">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" class="form-control" data-plugin="start" name="date1" value="<{$date1}>" placeholder="时间"/>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" data-plugin="end" name="date2" value="<{$date2}>"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white"><{lang key='search'}></button>
                        </div>
                    </form>
                </div>


                <div class="widget-content">
                    <div style="height: 600px; margin: 0 auto;">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<{include file='common/footer.tpl'}>

<script language="JavaScript">
$(document).ready(function() {  
   var chart = {
      type: 'column'
   };
   var title = {
      text: '每月平均降雨量'   
   };
   var subtitle = {
      text: 'Source: runoob.com'  
   };
   var xAxis = {
      categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      crosshair: true
   };
   var yAxis = {
      min: 0,
      title: {
         text: '降雨量 (mm)'         
      }      
   };
   var tooltip = {
      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
      pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
         '<td style="padding:0"><b>{point.y}</b></td></tr>',
      footerFormat: '</table>',
      shared: true,
      useHTML: true
   };
   var plotOptions = {
      column: {
         pointPadding: 0.2,
         borderWidth: 0,
         dataLabels: {
            enabled: true,
            format: '{point.y}'
        }
      }
   };  
   var credits = {
      enabled: false
   };
   
   var series= [{
        name: 'Tokyo',
            data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
        }, {
            name: 'New York',
            data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]
        }, {
            name: 'London',
            data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]
        }, {
            name: 'Berlin',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]
   }];     
      
   var json = <{$json}>;   
   json.chart = chart; 
   // json.title = title;   
   // json.subtitle = subtitle; 
   json.tooltip = tooltip;
   // json.xAxis = xAxis;
   // json.yAxis = yAxis;  
   // json.series = series;
   json.plotOptions = plotOptions;  
   json.credits = credits;
   $('#container').highcharts(json);
  
});
</script>
</script>
</body>
</html>
