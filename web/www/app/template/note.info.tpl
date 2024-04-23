<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>

<body>
<div id="editormd-view">
    <textarea id="append-test" style="display:none;">
        <{$info.content}>
    </textarea>
</div>
<{include file='common/footer.tpl'}>
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
