/**
 * 验证 xml
 */
function validateXML() {
    try {
        var e = $('#editor-xml').val();
        layer && layer.msg('XML验证成功');
        return $.parseXML(e);
    } catch (t) {
        layer.msg('XML验证错误');
    }
}
/**
 * XML查看，点击展开所有，默认中文
 */
function xmlTreeView() { 
    var e = $('#editor-xml').val();
    var instance = new X2JS();
    var t = instance.xml_str2json(e);
    
    var editor = document.getElementById("output-json");
    var outputEditorForTree = new JSONEditor(editor, {
        mode: "view",
        language: 'zh-CN',
        onError: function(e) {
            console.error("E2 ->" + e.toString())
        },
    })

    outputEditorForTree.setText(JSON.stringify(t));
    outputEditorForTree.expandAll();
}