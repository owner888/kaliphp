var modified = false;
var objApp = window.external;
var wizEditor;
var docTitle = "";


function getEditor() {
    var objDatabase = null;
    var objDocument = null;
    var plainPasteMode = false;             // 纯文本粘贴模式
    var filesDirName = "index_files/";      // 本地文件目录名，不可更改
    //var code = loadDocument();

    //分清是不是快捷键保存，解决为知4.5版本自动保存问题
    var wizVerisonGreaterThan45 = null;
    var wantSaveKey = false;
    var wantSaveTime = null;
    try {
        wizVerisonGreaterThan45 = objApp.Window.CurrentDocumentBrowserObject != null;
    }
    catch (err) {
    }

    ////////////////////////////////////////////////
    // 配置编辑器功能
    wizEditor = editormd("editormd", {
        toolbar : true,
        customToolbar: true, // 扩展配置，用于不计算toobar高度
        watch : false,
        //value           : code,
        path            :  "/static/Editor.md/lib/",
        htmlDecode      : "style,script,iframe|on*",  // 开启HTML标签解析，为了安全性，默认不开启
        codeFold        : true,              // 代码折叠，默认关闭
        tex             : false,              // 开启科学公式TeX语言支持，默认关闭
        flowChart       : true,              // 开启流程图支持，默认关闭
        sequenceDiagram : true,              // 开启时序/序列图支持，默认关闭
        toc             : false,              // [TOC]自动生成目录，默认开启
        tocm            : false,             // [TOCM]自动生成下拉菜单的目录，默认关闭
        tocTitle        : "",                // 下拉菜单的目录的标题
        tocDropdown     : false,             // [TOC]自动生成下拉菜单的目录，默认关闭
        taskList            : true,
        saveHTMLToTextarea  : true,     // 保存 HTML 到 Textarea
        autoFocus:false,
        imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "?ct=upload&ac=upload",
        imageUpload         : true,
        onload : function() {
  
        },
    });
}
