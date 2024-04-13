<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$app_name}></title>
    <link href="static/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/css/animate.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <link href="static/editormd/css/editormd.preview.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
</head>

<body>
<div id="editormd-view">
    <textarea id="append-test" style="display:none;">
        <{$info.content}>
    </textarea>
</div>
<script src="static/editormd/lib/marked.min.js"></script>
<script src="static/editormd/lib/prettify.min.js"></script>
<script src="static/editormd/lib/raphael.min.js"></script>
<script src="static/editormd/lib/underscore.min.js"></script>
<script src="static/editormd/lib/sequence-diagram.min.js"></script>
<script src="static/editormd/lib/flowchart.min.js"></script>
<script src="static/editormd/lib/jquery.flowchart.min.js"></script>
<script src="static/editormd/editormd.min.js"></script>
<script type="text/javascript">
    $(function() {
        var editormdView;
        
        editormdView = editormd.markdownToHTML("editormd-view", {
            htmlDecode      : "style,script,iframe",  // you can filter tags decode
            emoji           : true,
            taskList        : true,
            tex             : true,  // 默认不解析
            flowChart       : true,  // 默认不解析
            sequenceDiagram : true,  // 默认不解析
        });
    });
</script>
</body>
</html>
