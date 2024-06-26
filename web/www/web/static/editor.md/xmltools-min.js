﻿/**
 * vkBeautify - javascript plugin to pretty-print or minify text in XML, JSON, CSS and SQL formats.
 *  
 * Version - 0.99.00.beta 
 * Copyright (c) 2012 Vadim Kiryukhin
 * vkiryukhin @ gmail.com
 * http://www.eslinstructor.net/vkbeautify/
 * 
 * MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   Pretty print
 *
 *        vkbeautify.xml(text [,indent_pattern]);
 *        vkbeautify.json(text [,indent_pattern]);
 *        vkbeautify.css(text [,indent_pattern]);
 *        vkbeautify.sql(text [,indent_pattern]);
 *
 *        @text - String; text to beatufy;
 *        @indent_pattern - Integer | String;
 *                Integer:  number of white spaces;
 *                String:   character string to visualize indentation ( can also be a set of white spaces )
 *   Minify
 *
 *        vkbeautify.xmlmin(text [,preserve_comments]);
 *        vkbeautify.jsonmin(text);
 *        vkbeautify.cssmin(text [,preserve_comments]);
 *        vkbeautify.sqlmin(text);
 *
 *        @text - String; text to minify;
 *        @preserve_comments - Bool; [optional];
 *                Set this flag to true to prevent removing comments from @text ( minxml and mincss functions only. )
 *
 *   Examples:
 *        vkbeautify.xml(text); // pretty print XML
 *        vkbeautify.json(text, 4 ); // pretty print JSON
 *        vkbeautify.css(text, '. . . .'); // pretty print CSS
 *        vkbeautify.sql(text, '----'); // pretty print SQL
 *
 *        vkbeautify.xmlmin(text, true);// minify XML, preserve comments
 *        vkbeautify.jsonmin(text);// minify JSON
 *        vkbeautify.cssmin(text);// minify CSS, remove comments ( default )
 *        vkbeautify.sqlmin(text);// minify SQL
 *
 */
! function() {
	function e(e) {
		var t = "    ";
		if (isNaN(parseInt(e))) t = e;
		else switch (e) {
			case 1:
				t = " ";
				break;
			case 2:
				t = "  ";
				break;
			case 3:
				t = "   ";
				break;
			case 4:
				t = "    ";
				break;
			case 5:
				t = "     ";
				break;
			case 6:
				t = "      ";
				break;
			case 7:
				t = "       ";
				break;
			case 8:
				t = "        ";
				break;
			case 9:
				t = "         ";
				break;
			case 10:
				t = "          ";
				break;
			case 11:
				t = "           ";
				break;
			case 12:
				t = "            "
		}
		var o = ["\n"];
		for (ix = 0; ix < 100; ix++) o.push(o[ix] + t);
		return o
	}

	function t() {
		this.step = "\t", this.shift = e(this.step)
	}

	function o(e, t) {
		return t - (e.replace(/\(/g, "").length - e.replace(/\)/g, "").length)
	}

	function i(e, t) {
		return e.replace(/\s{1,}/g, " ").replace(/ AND /gi, "~::~" + t + t + "AND ").replace(/ BETWEEN /gi, "~::~" + t + "BETWEEN ").replace(/ CASE /gi, "~::~" + t + "CASE ").replace(/ ELSE /gi, "~::~" + t + "ELSE ").replace(/ END /gi, "~::~" + t + "END ").replace(/ FROM /gi, "~::~FROM ").replace(/ GROUP\s{1,}BY/gi, "~::~GROUP BY ").replace(/ HAVING /gi, "~::~HAVING ").replace(/ IN /gi, " IN ").replace(/ JOIN /gi, "~::~JOIN ").replace(/ CROSS~::~{1,}JOIN /gi, "~::~CROSS JOIN ").replace(/ INNER~::~{1,}JOIN /gi, "~::~INNER JOIN ").replace(/ LEFT~::~{1,}JOIN /gi, "~::~LEFT JOIN ").replace(/ RIGHT~::~{1,}JOIN /gi, "~::~RIGHT JOIN ").replace(/ ON /gi, "~::~" + t + "ON ").replace(/ OR /gi, "~::~" + t + t + "OR ").replace(/ ORDER\s{1,}BY/gi, "~::~ORDER BY ").replace(/ OVER /gi, "~::~" + t + "OVER ").replace(/\(\s{0,}SELECT /gi, "~::~(SELECT ").replace(/\)\s{0,}SELECT /gi, ")~::~SELECT ").replace(/ THEN /gi, " THEN~::~" + t).replace(/ UNION /gi, "~::~UNION~::~").replace(/ USING /gi, "~::~USING ").replace(/ WHEN /gi, "~::~" + t + "WHEN ").replace(/ WHERE /gi, "~::~WHERE ").replace(/ WITH /gi, "~::~WITH ").replace(/ ALL /gi, " ALL ").replace(/ AS /gi, " AS ").replace(/ ASC /gi, " ASC ").replace(/ DESC /gi, " DESC ").replace(/ DISTINCT /gi, " DISTINCT ").replace(/ EXISTS /gi, " EXISTS ").replace(/ NOT /gi, " NOT ").replace(/ NULL /gi, " NULL ").replace(/ LIKE /gi, " LIKE ").replace(/\s{0,}SELECT /gi, "SELECT ").replace(/\s{0,}UPDATE /gi, "UPDATE ").replace(/ SET /gi, " SET ").replace(/~::~{1,}/g, "~::~").split("~::~")
	}
	t.prototype.xml = function(t, o) {
		var i = t.replace(/>\s{0,}</g, "><").replace(/</g, "~::~<").replace(/\s*xmlns\:/g, "~::~xmlns:").replace(/\s*xmlns\=/g, "~::~xmlns=").split("~::~"),
			a = i.length,
			r = !1,
			n = 0,
			l = "",
			s = 0,
			u = o ? e(o) : this.shift;
		for (s = 0; s < a; s++) i[s].search(/<!/) > -1 ? (l += u[n] + i[s], r = !0, (i[s].search(/-->/) > -1 || i[s].search(/\]>/) > -1 || i[s].search(/!DOCTYPE/) > -1) && (r = !1)) : i[s].search(/-->/) > -1 || i[s].search(/\]>/) > -1 ? (l += i[s], r = !1) : /^<\w/.exec(i[s - 1]) && /^<\/\w/.exec(i[s]) && /^<[\w:\-\.\,]+/.exec(i[s - 1]) == /^<\/[\w:\-\.\,]+/.exec(i[s])[0].replace("/", "") ? (l += i[s], r || n--) : i[s].search(/<\w/) > -1 && -1 == i[s].search(/<\//) && -1 == i[s].search(/\/>/) ? l = l += r ? i[s] : u[n++] + i[s] : i[s].search(/<\w/) > -1 && i[s].search(/<\//) > -1 ? l = l += r ? i[s] : u[n] + i[s] : i[s].search(/<\//) > -1 ? l = l += r ? i[s] : u[--n] + i[s] : i[s].search(/\/>/) > -1 ? l = l += r ? i[s] : u[n] + i[s] : i[s].search(/<\?/) > -1 || i[s].search(/xmlns\:/) > -1 || i[s].search(/xmlns\=/) > -1 ? l += u[n] + i[s] : l += i[s];
		return "\n" == l[0] ? l.slice(1) : l
	}, t.prototype.json = function(e, t) {
		t = t || this.step;
		return "undefined" == typeof JSON ? e : "string" == typeof e ? JSON.stringify(JSON.parse(e), null, t) : "object" == typeof e ? JSON.stringify(e, null, t) : e
	}, t.prototype.css = function(t, o) {
		var i = t.replace(/\s{1,}/g, " ").replace(/\{/g, "{~::~").replace(/\}/g, "~::~}~::~").replace(/\;/g, ";~::~").replace(/\/\*/g, "~::~/*").replace(/\*\//g, "*/~::~").replace(/~::~\s{0,}~::~/g, "~::~").split("~::~"),
			a = i.length,
			r = 0,
			n = "",
			l = 0,
			s = o ? e(o) : this.shift;
		for (l = 0; l < a; l++) /\{/.exec(i[l]) ? n += s[r++] + i[l] : /\}/.exec(i[l]) ? n += s[--r] + i[l] : (/\*\\/.exec(i[l]), n += s[r] + i[l]);
		return n.replace(/^\n{1,}/, "")
	}, t.prototype.sql = function(t, a) {
		var r = t.replace(/\s{1,}/g, " ").replace(/\'/gi, "~::~'").split("~::~"),
			n = r.length,
			l = [],
			s = 0,
			u = this.step,
			c = 0,
			d = "",
			p = 0,
			g = a ? e(a) : this.shift;
		for (p = 0; p < n; p++) l = p % 2 ? l.concat(r[p]) : l.concat(i(r[p], u));
		for (n = l.length, p = 0; p < n; p++) {
			c = o(l[p], c), /\s{0,}\s{0,}SELECT\s{0,}/.exec(l[p]) && (l[p] = l[p].replace(/\,/g, ",\n" + u + u)), /\s{0,}\s{0,}SET\s{0,}/.exec(l[p]) && (l[p] = l[p].replace(/\,/g, ",\n" + u + u)), /\s{0,}\(\s{0,}SELECT\s{0,}/.exec(l[p]) ? d += g[++s] + l[p] : /\'/.exec(l[p]) ? (c < 1 && s && s--, d += l[p]) : (d += g[s] + l[p], c < 1 && s && s--)
		}
		return d = d.replace(/^\n{1,}/, "").replace(/\n{1,}/g, "\n")
	}, t.prototype.xmlmin = function(e, t) {
		return (t ? e : e.replace(/\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>/g, "").replace(/[ \r\n\t]{1,}xmlns/g, " xmlns")).replace(/>\s{0,}</g, "><")
	}, t.prototype.jsonmin = function(e) {
		return "undefined" == typeof JSON ? e : JSON.stringify(JSON.parse(e), null, 0)
	}, t.prototype.cssmin = function(e, t) {
		return (t ? e : e.replace(/\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+\//g, "")).replace(/\s{1,}/g, " ").replace(/\{\s{1,}/g, "{").replace(/\}\s{1,}/g, "}").replace(/\;\s{1,}/g, ";").replace(/\/\*\s{1,}/g, "/*").replace(/\*\/\s{1,}/g, "*/")
	}, t.prototype.sqlmin = function(e) {
		return e.replace(/\s{1,}/g, " ").replace(/\s{1,}\(/, "(").replace(/\s{1,}\)/, ")")
	}, window.vkbeautify = new t
}();
var previouskey, toolstype = "json",
	isFullScreen = !1,
	userId = 0;

function preInitEditors() {
	if ("json" == toolstype) createJsonEditor(), cleanJSONEditor("all"), setDataInInputEditor(getFromLocalStorage());
	else if ("xml" == toolstype || "xml-viewer" == toolstype) createXMLEditor();
	else if ("yaml" == toolstype) createYAMLEditor();
	else if ("html" == toolstype) createHTMLEditor();
	else if ("escaper" == toolstype) createEscaperEditor();
	else if ("encoder" == toolstype) createEncoderEditor();
	else if ("jsontree" == toolstype) createJsonTreeEditor();
	else if ("excel" == toolstype) createExcelOutputEditor();
	else if ("css" == toolstype) createCSSEditor();
	else if ("langconvert" == toolstype) createDatatoLangEditor();
	else if ("json5" == toolstype) createJSON5Editor();
	else if ("prettier" == toolstype) createPrettierEditor();
	else if ("csv" == toolstype) createCSVEditor();
	else {
		if ("samples" != toolstype) return;
		createSampleEditor()
	}
}

function postInitEditors() {
	$(document).ajaxSend((function(e, t, o) {
		$("#pluswrap").removeClass("hide")
	})), $(document).ajaxComplete((function(e, t, o) {
		$("#pluswrap").addClass("hide")
	})), isDataUrlAvailable(), handleCtrlV(), checkIfUserLoggedinlogin(), $("#fileopen").click((function() {
		openFile()
	})), $("#default_file").change((function() {
		formdata = new FormData, $(this).prop("files").length > 0 && (file = $(this).prop("files")[0], formdata.append("userfile", file), readSelectedFile())
	}))
}

function checkIfUserLoggedinlogin() {
	var e = getCookie("loggedinuser");
	"" != e ? (userId = getCookie("loggedinuserid"), $("#loggedUserName").text(e), $("#loginDropdown").show(), $("#loginDiv").hide()) : ($("#loginDropdown").hide(), $("#loginDiv").show(), userId = 0)
}

function getCookie(e) {
	for (var t = e + "=", o = document.cookie.split(";"), i = 0; i < o.length; i++) {
		for (var a = o[i];
			" " == a.charAt(0);) a = a.substring(1);
		if (0 == a.indexOf(t)) return (t = a.substring(t.length, a.length)).replace(/\+/g, " ")
	}
	return ""
}

function defaultAction() {
	$("#defaultaction").click()
}

function preLogin() {
	storePreviousPageURL(), window.open("/login", "_self")
}

function storePreviousPageURL() {
	localStorage && localStorage.setItem("urlBeforeLogin", window.location.pathname)
}

function preLogout() {
	storePreviousPageURL(), window.open("/logout", "_self")
}

function postLoginLogout() {
	var e = window.location.pathname;
	if (localStorage) {
		var t = localStorage.getItem("urlBeforeLogin");
		t && e !== t && window.open(t, "_self")
	}
}

function isAceEditor(e) {
	return $(e.container).hasClass("ace_editor")
}

function getOverlayClass(e) {
	return isAceEditor(e) ? "overlayaceeditor" : void 0 !== e && e instanceof JSONEditor ? "overlayjsoneditor" : void 0
}

function addOverlay(e) {
	"input" == e ? ($("#inputdiv").addClass("overlay"), $("#inputFullScreen").hide(), $("#inputCloseScreen").show(), fullscreenEditor = "input", $("#inputeditor").addClass(getOverlayClass(inputEditor)), inputEditor.resize()) : "output" == e && ($("#outputdiv").addClass("overlay"), $("#outputFullScreen").hide(), $("#outputCloseScreen").show(), fullscreenEditor = "output", $("#outputeditor").addClass(getOverlayClass(outputEditor)), outputEditor.resize()), isFullScreen = !0, $("body").css("overflow", "hidden")
}

function removeOverlay(e) {
	"input" == e ? ($("#inputdiv").removeClass("overlay"), $("#inputFullScreen").show(), $("#inputCloseScreen").hide(), $("#inputeditor").removeClass(getOverlayClass(inputEditor)), inputEditor.resize()) : "output" == e && ($("#outputdiv").removeClass("overlay"), $("#outputFullScreen").show(), $("#outputCloseScreen").hide(), $("#outputeditor").removeClass(getOverlayClass(outputEditor)), outputEditor.resize()), $("body").css("overflow", ""), isFullScreen = !1
}

function updateFullScreenIcons(e) {
	isFullScreen ? "input" == e ? ($("#inputFullScreen").hide(), $("#inputCloseScreen").show(), fullscreenEditor = "input") : "output" == e && ($("#outputFullScreen").hide(), $("#outputCloseScreen").show(), fullscreenEditor = "output") : "input" == e ? ($("#inputFullScreen").show(), $("#inputCloseScreen").hide()) : "output" == e && ($("#outputFullScreen").show(), $("#outputCloseScreen").hide())
}

function getCookie(e) {
	for (var t = e + "=", o = document.cookie.split(";"), i = 0; i < o.length; i++) {
		for (var a = o[i];
			" " == a.charAt(0);) a = a.substring(1);
		if (0 == a.indexOf(t)) return (t = a.substring(t.length, a.length)).replace(/\+/g, " ")
	}
	return ""
}

function copyToClipboard(e) {
	var t = $("<textarea>");
	$("body").append(t), t.val(e).select(), document.execCommand("copy"), t.remove(), $("#flymessage").toggleClass("in"), setTimeout((function() {
		$("#flymessage").removeClass("in")
	}), 2e3)
}

function setMessage(e, t, o) {
	void 0 === o && (o = !0), $("#msgDiv").html("");
	var i = "";
	i = "success" == e ? "/" == window.location.pathname ? '<div class="alert alert-info" style="margin-top: 0px;margin-bottom: 5px;">' : '<div class="alert alert-info" >' : '<div class="alert alert-danger" >', i += '<a href="#" class="close" data-dismiss="alert" aria-label="close" id="errorClose">&times;</a>', i += "<label>" + t + "</label></div>", $("#msgDiv").html(i), o && setTimeout((function() {
		$("#errorClose").click()
	}), 3e3)
}

function decodeSpecialCharacter(e) {
	return e.replace(/\&amp;/g, "&").replace(/\&gt;/g, ">").replace(/\&lt;/g, "<").replace(/\&quot;/g, '"')
}

function setModelTitle(e) {
	"Formatted JSON" == e ? addOutputIconstoEditor(!0) : addOutputIconstoEditor()
}

function hideMessage() {
	$("#msgDiv").html(""), $("#outputeditor .menu").show()
}

function loadUrl() {
	var e = $("#path").val();
	e.trim().length > 5 && ($("#loadFileClose").click(), load(e))
}

function load(e) {
	$.ajax({
		type: "post",
		url: "//codebeautify.com/URLService",
		dataType: "text",
		data: {
			path: e
		},
		success: function(e) {
			null != e && null != e && 0 != e.trim().length && setExternalURLData(e)
		},
		error: function(e, t, o) {
			setMessage("error", "Failed to load data=" + t), cleanJSONEditor("all")
		}
	})
}

function getDataFromUrlId(e) {
	$.ajax({
		type: "post",
		url: "/service/getDataFromID",
		dataType: "json",
		data: {
			urlid: e,
			toolstype: toolstype
		},
		success: function(e) {
			null != e && null != e && 0 != e.length ? setDataView(e) : alert("This URL does not Exist.")
		},
		error: function(e, t, o) {
			setMessage("error", "Failed to load data=" + o), cleanJSONEditor("editor"), cleanJSONEditor()
		}
	})
}

function setExternalURLData(e) {
	"json" == toolstype ? setJSONDataFromResponse(e) : "xml" == toolstype ? setXMLDataFromResponse(e) : "yaml" == toolstype ? yamlInputeditor.setValue(e) : inputEditor.setValue(e)
}

function setDataView(e) {
	$("#title").val(e.title), $("#tags").val(e.tags), $("#desc").val(e.desc), "json" == toolstype ? setJSONDataFromResponse(e.content, e.lastaction) : "xml" == toolstype ? setXMLDataFromResponse(e.content, e.lastaction) : "yaml" == toolstype ? yamlInputeditor.setValue(e.content) : inputEditor.setValue(e.content), userId = e.user_id, $("#id").val(e.id)
}

function setJSONDataFromResponse(e, t) {
	try {
		var o = $.parseJSON(e);
		inputEditor.setText(JSON.stringify(o, null, 2)), null != t ? updateJSONOutput(t) : defaultAction()
	} catch (t) {
		setMessage("error", "Invalid JSON Data: " + t), inputEditor.setText(e)
	}
}

function setXMLDataFromResponse(e, t) {
	try {
		inputEditor.setValue(e), null != t ? updateXMLOutput(t) : defaultAction()
	} catch (e) {
		setMessage("error", "Invalid XML Data: " + e)
	}
}

function openFile() {
	$("input[type=file]").click()
}

function readSelectedFile() {
	$("#loadFileClose").click();
	$("#viewname").val();
	var e = "/service/uploadFile";
	"excel-to-html" == $("#viewname").val() && (e = "SheetReader/uploadFile"), $.ajax({
		url: e,
		type: "POST",
		data: formdata,
		processData: !1,
		contentType: !1,
		success: function(e) {
			"error" != e ? "excel-to-html" == $("#viewname").val() ? converExcelToHtml(e) : (setDataInInputEditor(e), defaultAction()) : setMessage("error", "Error in Loading File.")
		}
	})
}

function download() {
	var e = outputEditor.getText();
	if (0 != e.trim().length) {
		if ("function" != typeof saveAs) return void $.loadScript("dist/js/vendor/FileSaver.min.js", download);
		var t = outputEditor.getMode();
		if ("tree" === t || "form" === t || "view" === t) {
			var o = $.parseJSON(e);
			e = JSON.stringify(o, null, 2)
		}
		var i = new Blob(["" + e], {
			type: "text/plain;charset=utf-8"
		});
		filename = "jsonformatter.txt", "jsonToxml" == last_action && (filename = "jsonformatter.xml"), saveAs(i, filename)
	} else setMessage("error", "Sorry Result is Empty")
}

function clearInput(e) {
	"saveDialog" == e ? ($("#desc").val(""), $("#title").val(""), $("#tags").val("")) : $("#path").val("")
}

function handleCtrlV() {
	$(document).keyup((function(e) {
		e.ctrlKey && 86 == e.keyCode || 17 == previouskey && 86 == e.keyCode ? (defaultAction(), previouskey = -1) : previouskey = e.keyCode
	}))
}

function Action_Save() {
	if (0 == ("xml" == toolstype ? inputEditor.getValue() : "yaml" == toolstype ? yamlInputeditor.getValue() : "html" == toolstype ? inputEditor.getValue() : inputEditor.getText()).trim().length) showErrorDialog("Sorry,Input is Empty");
	else {
		var e = $("#dataUrl").val();
		0 != userId && 0 != e.trim().length ? $("#btnUpdate").show() : ($("#btnUpdate").hide(), clearInput("saveDialog")), $("#openSave").click()
	}
}

function save(e) {
	var t, o = $("#title").val();
	null != o && 0 != o.length ? ($("#loadSaveClose").click(), t = "xml" == toolstype ? inputEditor.getValue() : "yaml" == toolstype ? yamlInputeditor.getValue() : "html" == toolstype ? inputEditor.getValue() : inputEditor.getText(), $.ajax({
		url: "/service/save",
		dataType: "text",
		type: "post",
		async: !1,
		data: {
			content: t,
			title: $("#title").val(),
			id: $("#id").val(),
			user_id: userId,
			desc: $("#desc").val(),
			tags: $("#tags").val().trim(),
			viewname: $("#viewname").val().trim(),
			lastaction: last_action,
			toolstype: toolstype
		},
		success: function(t) {
			if ("error" != t) {
				var o = null;
				setMessage("success", "Data saved successfully - <a class='white' href ='" + (o = "index" == $("#viewname").val().trim() ? "https://" + location.host + "/" + t : "https://" + location.host + "/" + $("#viewname").val().trim() + "/" + t) + "'>" + o + "</a>", !1), null != e && shareLink(o)
			} else alert("Please validate the input and try again.")
		},
		error: function(e, t, o) {
			setMessage("error", "Error in data saving")
		}
	})) : alert("Title is required")
}

function update() {
	var e;
	$("#loadSaveClose").click(), e = "xml" == toolstype ? inputEditor.getValue() : "yaml" == toolstype ? yamlInputeditor.getValue() : "html" == toolstype ? inputEditor.getValue() : inputEditor.getText(), $.ajax({
		url: "/service/update",
		dataType: "text",
		type: "post",
		data: {
			id: $("#id").val(),
			content: e,
			title: $("#title").val(),
			desc: $("#desc").val(),
			tags: $("#tags").val().trim()
		},
		success: function(e) {
			setMessage("success", "Data updatd successfully")
		},
		error: function(e, t, o) {
			setMessage("error", "Error in data updating")
		}
	})
}

function shareLink(e) {
	"google" == getProvider() ? window.open("https://plus.google.com/share?url=" + e, "_blank") : window.open("https://www.facebook.com/share.php?u=" + e, "_blank")
}
$((function() {
	if ($(window).scrollTop(0), "recentLinks" == (toolstype = 'xml')) {
		var e = $("#clickedLink").val() + "Link";
		$("#linkDiv a.disabled").removeClass("disabled").addClass("active"), $("#linkDiv a#" + e).removeClass("active").addClass("disabled")
	}
	0 != $("#inputeditor").length && preInitEditors()
})), jQuery.loadScript = function(e, t) {
	$.ajaxSetup({
		cache: !0
	}), jQuery.ajax({
		url: e,
		dataType: "script",
		success: t,
		async: !0
	}), $.ajaxSetup({
		cache: !1
	})
}, $(document).keyup((function(e) {
	27 == e.keyCode && removeOverlay(fullscreenEditor)
})), $(".navbar-collapse ul li a").click((function() {
	$(".navbar-toggle:visible").click()
}));
var old, online = function(e) {
	var t = (new Date).getTime() / 1e3;
	return e && e.access_token && e.expires > t
};

function isDataUrlAvailable() {
	if (0 != $("#dataUrl").length) {
		var e = $("#dataUrl").val();
		0 != e.trim().length && (6 == e.trim().length ? getDataFromUrlId(e) : load(e))
	}
}

function saveToLocalStorage(e) {
	localStorage && (localStorage.setItem($("#viewname").val(), ""), localStorage.setItem($("#viewname").val(), e))
}

function getFromLocalStorage() {
	if (localStorage) {
		var e = localStorage.getItem($("#viewname").val());
		if (null != e && "URL is not valid." != e) return e
	}
	return ""
}

function showErrorDialog(e) {
	$("#errorMsg").text(e), $("#openError").click()
}

function htmlOutput(e) {
	var t = e;
	if (void 0 === e && (t = "html-editor" == $("#viewname").val() ? $("#summernote").summernote("code") : inputEditor.getValue()), t.trim().length > 0) {
		var o = document.getElementById("result1").contentWindow.document;
		old != t && (old = t, o.open(), o.write(old), o.close()), $("html, body").animate({
			scrollTop: 0
		}, 10)
	}
}

function getCSVTOTSV(e) {
	return e.split(",").join("\t")
}

function toHTML(e) {
	if (0 != e.trim().length) {
		rows = "", thead = "<tr>";
		var t = Papa.parse(e),
			o = t.data,
			i = o.slice(1, o.length);
		i.sort((function(e, t) {
			return t.length - e.length
		})), 0 == i.length && (i = t.data);
		var a = 0;
		for (a = 0; a < i[0].length; a++) a < o[0].length ? thead += "<th>" + o[0][a] + "</th>" : thead += "<th>COLUMN" + (a + 1) + "</th>";
		thead += "</tr>";
		for (var r = 1; r < o.length; r++) {
			for (rows += "<tr>", a = 0; a < i[0].length; a++) a < o[r].length ? rows += "<td>" + o[r][a] + "</td>" : rows += "<td>&nbsp</td>";
			rows += "</tr>"
		}
		return htmlOutput("<table border=1><thead>\n" + thead + "</thead><tbody>\n" + rows + "</tbody></table>", ext), old
	}
	openErrorDialog("Sorry Input is Empty")
}

function init() {
	adsBlocked((function(e) {
		e ? $("#ablocker-big").show() : $("#ablocker-big").hide()
	}))
}

function adsBlocked(e) {
	var t = new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", {
		method: "HEAD",
		mode: "no-cors"
	});
	fetch(t).then((function(e) {
		return e
	})).then((function(t) {
		console.log(t), e(!1)
	})).catch((function(t) {
		console.log(t), e(!0)
	}))
}
$((function() {
	postInitEditors(), init()
}));
var inputEditor, outputEditor, outputEditorForTree, xmlConvertTitle, last_action = "beautify",
	fullscreenEditor = "";

function createXMLEditor() {
	0 != $("#inputeditor").length && ((inputEditor = ace.edit("inputeditor")).getSession().setMode("ace/mode/xml"), inputEditor.getSession().setUseWrapMode(!0), inputEditor.setOptions({
		fontSize: "12pt"
	}), addXMLInputIconstoEditor(), inputEditor.on("change", (function() {
		saveToLocalStorage(inputEditor.getValue())
	})), inputEditor.setValue(getFromLocalStorage()), (outputEditor = ace.edit("outputeditor")).getSession().setMode("ace/mode/xml"), outputEditor.getSession().setUseWrapMode(!0), outputEditor.setOptions({
		fontSize: "12pt"
	}), outputEditor.setValue(""), addXMLOutputIconstoEditor())
}

function createLazyJSONEditor() {
	if ("function" == typeof JSONEditor) {
		if (0 != $("#outputeditor1").length) {
			var e = document.getElementById("outputeditor1"),
				t = (outputEditorForTree = new JSONEditor(e, {
					mode: "view",
					onError: function(e) {
						showErrorDialog("E2 ->" + e.toString())
					}
				}, null)).menu.getElementsByClassName("poweredBy");
			$(t).hide(), xmlTreeView()
		}
	} else $.loadScript("/jsoneditor-xmltree.js", createLazyJSONEditor)
}

function setDataInInputEditor(e) {
	inputEditor.setValue(e)
}

function clearXMLEditor(e) {
	"inputxmleditor" == e ? inputEditor.setValue("") : "all" == e ? (inputEditor.setValue(""), outputEditor.setValue("")) : outputEditor.setValue("")
}

function addXMLInputIconstoEditor() {
	var e = $(".rightmenu");
	$(e).replaceWith('<div class="btn-group btn-group-sm right"><div class="cursor-pointer btn-sm fa fa-check" title="XML验证" onclick="validateXML();"></div><div class="cursor-pointer btn-sm fa fa-times" title="Clear" onclick="clearXMLEditor(\'inputxmleditor\')"></div><div id="inputcopy" title="复制" class="cursor-pointer btn-sm btn-shrink fa fa-files-o"></div><div id="inputFullScreen" title="全屏" onclick="addOverlay(\'input\');" class="cursor-pointer btn-sm btn-fullscreen fa fa-arrows-alt"></div><div id="inputCloseScreen" title="Close" onclick="removeOverlay(\'input\');" style="display:none" class="cursor-pointer btn-sm btn-fullscreen fa fa-window-close"></div></div>'), $(e).show(), $("#inputcopy").click((function() {
		copyToClipboard(inputEditor.getValue())
	})), inputEditor.focus()
}

function addXMLOutputIconstoEditor(e) {
	var t = $(".outputrightmenu");
	$(t).after('<div id="outputToolBar" class="btn-group btn-group-sm right"><div class="cursor-pointer tree-rotate-180 btn-sm fa fa-tree" title="Tree view" onclick="xmlTreeView(),showEditor();"></div><div class="cursor-pointer btn-sm fa fa-times" title="Clear" onclick="clearXMLEditor()"></div><div id="outputcopy" title="复制" class="cursor-pointer btn-sm btn-shrink fa fa-files-o"></div><div id="outputFullScreen" title="全屏" onclick="addOverlay(\'output\');" class="cursor-pointer btn-sm btn-fullscreen fa fa-arrows-alt"></div><div id="outputCloseScreen" title="Close" onclick="removeOverlay(\'output\');" style="display:none" class="cursor-pointer btn-sm btn-fullscreen fa fa-window-close"></div></div>'), e ? $(t).show() : $(t).hide(), $("#outputcopy").click((function() {
		copyToClipboard(outputEditor.getValue())
	}))
}

function loadXMLSampleData() {
	0 != $("#inputeditor").length && inputEditor.setValue(vkbeautify.xml(getXMLSampleData())), defaultAction()
}

function loadRSSSampleData() {
	$.ajax({
		type: "post",
		url: "//codebeautify.com/URLService",
		dataType: "text",
		data: {
			path: "http://rss.cnn.com/rss/edition_world.rss"
		},
		success: function(e) {
			try {
				0 != $("#inputeditor").length && inputEditor.setValue(vkbeautify.xml(e))
			} catch (e) {}
		},
		error: function(e) {}
	}), defaultAction()
}

function getXMLSampleData() {
	return '<?xml version="1.0" encoding="UTF-8" ?><employees><employee><id>1</id><firstName>Tom</firstName><lastName>Cruise</lastName><photo>https://verytoolz.com/img/tom-cruise.jpg</photo></employee><employee><id>2</id><firstName>Maria</firstName><lastName>Sharapova</lastName><photo>https://verytoolz.com/img/Maria-Sharapova.jpg</photo></employee><employee><id>3</id><firstName>Robert</firstName><lastName>Downey Jr.</lastName><photo>https://verytoolz.com/img/Robert-Downey-Jr.jpg</photo></employee></employees>'
}

function xmlBeautify() {
	inputEditor.getSession().setMode("ace/mode/xml"), outputEditor.getSession().setMode("ace/mode/xml");
	var e = inputEditor.getValue();
	if (0 == e.trim().length) return !1;
	hideMessage();
	try {
		outputEditor.setValue(vkbeautify.xml(e)), $("#outputheading").text("Formatted XML")
	} catch (e) {
		validateXML()
	}
	last_action = "beautify"
}

function xmlTreeView() {
	var e = inputEditor.getValue();
	if (0 == e.trim().length) return !1;
	hideMessage();
	try {
		if (null == outputEditorForTree || !outputEditorForTree instanceof JSONEditor) return void createLazyJSONEditor();
		var t = new X2JS;
		outputEditorForTree.setText(JSON.stringify(t.xml_str2json(e))), $("#outputheading").text("XML Tree"), $(".jsoneditor-search").css("right", "10px")
	} catch (e) {
		console.log(e)
	}
	last_action = "xmlviewer"
}

function xmlMinify() {
	inputEditor.getSession().setMode("ace/mode/xml"), outputEditor.getSession().setMode("ace/mode/xml");
	var e = inputEditor.getValue();
	if (0 == e.trim().length) return !1;
	hideMessage();
	try {
		outputEditor.getSession().setUseWrapMode(!0), outputEditor.setValue(vkbeautify.xmlmin(e)), $("#outputheading").text("XML Minify")
	} catch (e) {
		validateXML()
	}
	last_action = "beautify"
}

function rsstojson() {
	xmltojson("RSS to JSON")
}

function xmltojson(e = "XML to JSON") {
	isXmlData = !1, outputEditor.getSession().setMode("ace/mode/json"), "RSS to JSON" === e || "XML to JSON" === e ? xmlConvertTitle = e : e = xmlConvertTitle;
	var t = inputEditor.getValue();
	if (t.trim().length > 0) try {
		if ("function" != typeof X2JS) return void $.loadScript("/xml2json.min.js", xmltojson);
		var o = new X2JS;
		outputEditor.setValue(vkbeautify.json(JSON.stringify(o.xml_str2json(t)))), $("#outputheading").text(e), last_action = "xmltojson"
	} catch (e) {
		setMessage("error", e)
	}
}

function xmlTocsv() {
	outputEditor.getSession().setMode("ace/mode/text");
	var e = inputEditor.getValue();
	if (e.trim().length > 0) try {
		var t = (new X2JS).xml_str2json(e),
			o = jsonToCsv(t, ",", !0, !1, !1);
		outputEditor.setValue(o), $("#outputheading").text("XML to CSV"), last_action = "xmltocsv"
	} catch (e) {
		setMessage("(XML is not valid) : error", e)
	}
}

function xmlTotsv() {
	outputEditor.getSession().setMode("ace/mode/text");
	var e = inputEditor.getValue();
	if (e.trim().length > 0) try {
		var t = (new X2JS).xml_str2json(e),
			o = jsonToCsv(t, ",", !0, !1, !1);
		outputEditor.setValue(getCSVTOTSV(o)), $("#outputheading").text("XML to TSV"), last_action = "xmltocsv"
	} catch (e) {
		setMessage("(XML is not valid) : error", e)
	}
}

function processXMLToYaml() {
	outputEditor.getSession().setMode("ace/mode/yaml");
	var e = inputEditor.getValue();
	if (e.trim().length > 0) try {
		var t = (new X2JS).xml_str2json(e),
			o = YAML.stringify(t);
		outputEditor.setValue(o), $("#outputheading").text("XML to YAML"), last_action = "xmltocsv"
	} catch (e) {
		setMessage("error", e)
	}
}

function xmlToyaml(e) {
	"function" != typeof X2JS && $.loadScript("/xml2json.min.js"), "object" != typeof YAML ? $.loadScript("/yaml.min.js", (function() {
		processXMLToYaml(e)
	})) : processXMLToYaml(e)
}

function plainJSONinXMLtoJSON() {
	if (validateXML()) {
		hideMessage(), xmltojson();
		var e = outputEditor.getValue(),
			t = $.parseJSON(e);
		localStorage.setItem("jsondata", JSON.stringify(t, null, 2)), window.open("/jsonview", "_blank").focus()
	}
}

function downloadinXMLTool() {
	if ("function" == typeof saveAs) {
		var e = outputEditor.getValue();
		if (0 != e.trim().length) {
			var t = new Blob(["" + e], {
				type: "text/plain;charset=utf-8"
			});
			filename = "data.xml", "xmltojson" == last_action && (filename = "json.txt"), saveAs(t, filename)
		} else setMessage("error", "Sorry Result is Empty")
	} else $.loadScript("dist/js/vendor/FileSaver.min.js", downloadinXMLTool)
}

function validateXML() {
	outputEditor.getSession().setMode("ace/mode/text");
	try {
		var e = inputEditor.getValue();
		return $.parseXML(e), setMessage("success", "VALID XML"), outputEditor.setValue("Valid XML"), !0
	} catch (e) {
		return setMessage("error", e), outputEditor.setValue(e.message), !1
	}
}

function updateXMLOutput(e) {
	null != inputEditor && 0 != inputEditor.getValue().trim().length && ("beautify" == e ? xmlBeautify() : "minify" == e ? minifyJSON() : "validateJSON" == e ? validateXML() : "xmltojson" == e && xmltojson())
}

function printXML() {
	if (validateXML()) {
		hideMessage(), xmlBeautify();
		var e = $("<div>").text(outputEditor.getValue()).html();
		localStorage.setItem("xmldata", e), $("<iframe>").attr("src", "/xmlprint").appendTo("body")
	}
}

function plainXML() {
	xmlBeautify(), localStorage.setItem("xmldata", outputEditor.getValue()), window.open("/xmlview", "_blank").focus()
}

function showEditor(e = "tree") {
	"ace" == e ? ($("#outputeditorDiv").removeClass("hide"), $("#outputeditor1Div").addClass("hide")) : ($("#outputeditor1Div").removeClass("hide"), $("#outputeditorDiv").addClass("hide"))
}

function plainXMLtoTSV() {
	xmlTotsv();
	var e = outputEditor.getValue();
	localStorage.setItem("data", e), window.open("/tsvview", "_blank").focus()
}

function xmlToJava() {
	outputEditor.getSession().setMode("ace/mode/java");
	var e = inputEditor.getValue();
	if (e.trim().length > 0) try {
		var t = new X2JS,
			o = createJavaObject(vkbeautify.json(JSON.stringify(t.xml_str2json(e))));
		outputEditor.setValue(o), last_action = "xmlToJava", $("#outputheading").text("XML to JAVA"), last_action = "xmltojson"
	} catch (e) {
		setMessage("error", e)
	}
}

function plainXMLtoJava() {
	xmlToJava(), localStorage.setItem("data", outputEditor.getValue()), window.open("/javaview", "_blank").focus()
}