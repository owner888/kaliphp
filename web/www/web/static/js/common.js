//列表样式控制脚本
var body = (document.compatMode && document.compatMode.toLowerCase() == "css1compat") ? document.documentElement : document.body;
function ResizeTable() {
    var sideWidth = document.getElementById("side-menu").clientWidth;
    if (document.getElementById('content-main') != null) {
        var tableDiv = document.getElementById('content-main');
        var warpDiv = document.getElementById("page-wrapper");
        tableDiv.style.height = '' + Math.max((body.clientHeight - tableDiv.offsetTop), 0) + "px";

        /* if(body.clientWidth<970){
             tableDiv.style.width = '' + Math.max((body.clientWidth - 70), 0) + "px";
             warpDiv.style.width = '' + Math.max((body.clientWidth - 70), 0) + "px";
         }else{
             warpDiv.style.width = '' + Math.max((body.clientWidth - 220), 0) + "px";
             tableDiv.style.width = '' + Math.max((body.clientWidth - 220), 0) + "px";
         }*/
    }
}

function pxparseFloat(x, y) {
    x = x.toString();
    var num = x;
    var data = num.split(".");
    var you = data[1].split(""); //将右边转换为数组 得到类似 [1,0,1]
    var sum = 0;  //小数部分的和
    for (var i = 0; i < data[1].length; i++) {
        sum += you[i] * Math.pow(y, -1 * (i + 1))
    }
    return parseInt(data[0], y) + sum;
}
function zhengze(x) {
    var str;
    x = parseInt(x);
    if (x <= 10) {
        str = new RegExp("^[+\\-]?[0-" + (x - 1) + "]*[.]?[0-" + (x - 1) + "]*$", "gi");
    } else {
        var letter = "";
        switch (x) {
            case 11: letter = "a"; break;
            case 12: letter = "b"; break;
            case 13: letter = "c"; break;
            case 14: letter = "d"; break;
            case 15: letter = "e"; break;
            case 16: letter = "f"; break;
            case 17: letter = "g"; break;
            case 18: letter = "h"; break;
            case 19: letter = "i"; break;
            case 20: letter = "j"; break;
            case 21: letter = "k"; break;
            case 22: letter = "l"; break;
            case 23: letter = "m"; break;
            case 24: letter = "n"; break;
            case 25: letter = "o"; break;
            case 26: letter = "p"; break;
            case 27: letter = "q"; break;
            case 28: letter = "r"; break;
            case 29: letter = "s"; break;
            case 30: letter = "t"; break;
            case 31: letter = "u"; break;
            case 32: letter = "v"; break;
            case 33: letter = "w"; break;
            case 34: letter = "x"; break;
            case 35: letter = "y"; break;
            case 36: letter = "z"; break;
        }
        str = new RegExp("^[+\\-]?[0-9a-" + letter + "]*[.]?[0-9a-" + letter + "]*$", "gi");
    }
    return str;
}
var n = 50;
var shurukuang = "";
var flag = "";
function px(y) {
    if ($("#input_value").val() != flag || y) {
        flag = $("#input_value").val();
        if ($("#input_num").selectedIndex < n) {
            $("#input_value").val("");
            $("#output_value").val("");
        } else {
            var px00 = $("#input_value").val();
            var px0 = px00.match(zhengze($("#input_num").val()));
            if (px0) {
                if (px0[0].indexOf(".") == -1) {
                    var px1 = parseInt(px0, $('#input_num').val());
                } else {
                    var px1 = pxparseFloat(px0, $('#input_num').val());
                }
                px1 = px1.toString($('#output_num').val());
                $("#output_value").val(px1);
                shurukuang = px00;
            } else {
                $("#input_value").val(shurukuang);
            }
        }
        n = $("#input_num").selectedIndex;
    }
    if ($("#input_value").val() == "") {
        $("#output_value").val("");
    }
}
function calculate(input1 = 0, input2 = 0, operator = '+', base = '16') {
    // 验证参数是否合法
    if (!operator || !base) {
        throw new Error("Invalid arguments");
    }

    // 将输入转换为指定进制 #FIXME 这里需要加入正则判断浮点型
    const num1 = parseInt(input1, base);
    const num2 = parseInt(input2, base);

    // 根据操作符执行对应的计算操作
    switch (operator) {
        case '+':
            return (num1 + num2).toString(base);
        case '-':
            return (num1 - num2).toString(base);
        case '*':
            return (num1 * num2).toString(base);
        case '/':
            return (num1 / num2).toString(base);
        default:
            throw new Error("Invalid operator");
    }
}
function getResult(){
    const result = calculate($('#tool-value1').val(), $('#tool-value2').val(), $('#tool-symbol').val(), $('#tool-hexlist').val());
    if(!isNaN(result)){
        $("#tool-result").val(result);
    }
}

//全局计算器对象
var Calculator = (function () {
    var cal = {
        //计算器按键编码
        keyCodes: {
            0: '0',
            1: '1',
            2: '2',
            3: '3',
            4: '4',
            5: '5',
            6: '6',
            7: '7',
            8: '8',
            9: '9',
            10: '.',
            11: '±',
            12: '=',
            13: '+',
            14: '-',
            15: '*',
            16: '/',
            17: '%',
            18: '√',
            19: 'x2',
            20: '1/x',
            21: '(',
            22: ')',
            23: 'yroot',
            24: 'n!',
            25: 'Exp',
            26: '^',
            27: 'sin',
            28: 'cos',
            29: 'tan',
            30: 'powten',
            31: 'log',
            32: 'sinh',
            33: 'cosh',
            34: 'tanh',
            35: 'π',
            36: '↑',
            37: 'CE',
            38: 'C',
            39: 'Back',
            //以下是程序员型特有的按键
            40: 'A',
            41: 'B',
            42: 'C',
            43: 'D',
            44: 'E',
            45: 'F',
            46: '&',
            47: '|',
            48: '~'
        },
        //映射用于显示的操作符，比如计算时用*，而显示时x更好
        operatorFacade: {
            13: '+',
            14: '-',
            15: '×',
            16: '÷',
            17: '%',
            23: 'yroot',
            26: '^',
            46: '&',
            47: '|'
        },
        //当前计算器的类型1 --> 标准型, 2-->科学型， 3-->程序员型，默认标准型
        type: 1,
        //计算器类型前缀，用于从页面获取元素
        typePrefix: {
            1: "std-",
            2: "sci-",
            3: "pro-"
        },
        // 计算器的外层 dom
        container: 'second',
        //记录每个类型的计算器的事件监听是否已经绑定,key:typpe数值，value:默认标准型是true(已加载)
        hasInited: {
            1: true,
            2: false,
            3: false
        },
        //常量
        constants: {
            //鼠标悬停时的颜色
            mouseHoverColor: "#CFCFCF",
            //计算器第一行和下面其它行的颜色是不同的，这个是第一行的背景颜色
            firstMouseOutColor: "#F2F2F2",
            //剩余各行的背景颜色
            mouseOutColor: "#E6E6E6"
        },
        cache: {
            //输入内容显示元素
            showInput: null,
            //上一步计算结果显示区域
            preStep: null,
            //显示四种进制数值的span，只在程序员型有效
            scaleSpans: null
        },
        /**
         * 获取cache.showInput的内容
         * @return String
         */
        getShowInput: function () {
            return cal.cache.showInput.innerHTML;
        },
        /**
         * 设置showInput的值
         * @param value
         */
        setShowInput: function (value) {
            cal.cache.showInput.innerHTML = value;
        },
        /**
         * 获取cache.preStep的内容
         * @return String
         */
        getPreStep: function () {
            return cal.cache.preStep.innerHTML;
        },
        setPreStep: function (value) {
            cal.cache.preStep.innerHTML = value;
        },
        //操作数栈
        operandStack: [],
        //运算符栈
        operatorStack: [],
        //上一次输入是否是二元运算符，如果是并且再次输入二元运算符，那么忽略此次输入
        isPreInputBinaryOperator: false,
        //上次按键是否是一元操作
        isPreInputUnaryOperator: false,
        //等号不可以连按
        isPreInputEquals: false,
        //如果为true，那么接下来输入的数字需要覆盖在showInput上，而不是追加
        //上一次计算的结果(=)
        preResult: 0,
        //当前使用的进制(只在程序员中有效),默认10进制(DEC)
        currentScale: 10,
        isOverride: false,
        //int校验
        intPattern: /^-?\d+$/,
        //小数校验
        floatPattern: /^-?\d+\.\d+$/,
        //科学计数法校验
        scientificPattern: /^\d+\.\d+e(\+|-)\d+$/,
        //校验16进制数字
        hexPattern: /^[0-9A-F]+$/,
        //辅助判断运算符的优先级
        operatorPriority: {
            ")": 0,
            "|": 1,
            "&": 2,
            "+": 3,
            "-": 3,
            "*": 4,
            "%": 4,
            "/": 4,
            "^": 5,
            "yroot": 5,
            "(": 6
        },
        /**
         * 初始化缓存对象(cal.cache)
         */
        initCache: function () {
            var prefix = cal.typePrefix[cal.type];
            cal.cache.showInput = document.getElementById(prefix + "show-input");
            cal.cache.preStep = document.getElementById(prefix + "pre-step");
            if (cal.type == 3) {
                cal.cache.scaleSpans = document.getElementById("pro-scales").getElementsByTagName("span");
            }
        },
        //各种事件监听函数
        listeners: {
            /**
             * 鼠标悬停在按键上的变色效果
             */
            mouseHoverListener: function (e) {
                var event = e || window.event;
                event.currentTarget.style.backgroundColor = cal.constants.mouseHoverColor;
            },
            /**
             * 鼠标从上排符号中移出的变色效果
             */
            firstMouseOutListener: function (e) {
                var event = e || window.event;
                event.currentTarget.style.backgroundColor = cal.constants.firstMouseOutColor;
            },
            /**
             * 鼠标从下排数字、符号中移出的变色效果
             */
            mouseOutListener: function (e) {
                var event = e || window.event;
                event.currentTarget.style.backgroundColor = cal.constants.mouseOutColor;
            },
            /**
             * 按键按下事件监听
             */
            keyPressListener: function (e) {
                var event = e || window.event;
                cal.handleKey(event.currentTarget.value);
            },
            /**
             * 显示/隐藏计算器类型选择栏
             */
            toggleTypeBarListener: function () {
                var bar = document.getElementById(cal.typePrefix[cal.type] + "type-bar");
                if (bar.style.display === "block") {
                    bar.style.display = "none";
                } else {
                    bar.style.display = "block";
                }
            },
            /**
             * 切换计算器类型监听器
             */
            switchTypeListener: function (e) {
                var event = e || window.event;
                cal.switchType(parseInt(event.currentTarget.value));
            },
            /**
             * 切换进制(程序员专用)
             */
            switchScaleListener: function (e) {
                var event = e || window.event;
                var scales = document.getElementById("pro-scales").getElementsByTagName("div"),
                    //此处应该使用currentTarget属性，因为target属性在绑定事件的元素有子元素的情况下会返回子元素
                    scale = parseInt(event.currentTarget.getAttribute("scale")),
                    oldScale = cal.currentScale;
                //切换选中样式
                for (var i = 0, l = scales.length; i < l; ++i) {
                    scales[i].removeAttribute("class");
                }
                event.currentTarget.setAttribute("class", "scale-active");
                var lis, btns;
                if (scale === 16) {
                    //处理上排6个16进制数字
                    cal.listeners._initFirstRowListeners();
                    if (oldScale < 10) {
                        cal.listeners._initSecondRowListeners();
                    }
                } else if (scale === 10) {
                    if (oldScale === 16) {
                        lis = document.getElementById("pro-top-symbol").getElementsByTagName("li");
                        cal.disableButtons(lis, cal.listeners.firstMouseOutListener);
                    } else {
                        cal.listeners._initSecondRowListeners();
                    }
                } else if (scale === 8) {
                    if (oldScale > 8) {
                        lis = document.getElementById("pro-top-symbol").getElementsByTagName("li");
                        cal.disableButtons(lis, cal.listeners.firstMouseOutListener);
                        //禁用8和9
                        btns = cal.getElementsByAttribute("li", "oct-disable", document.getElementById("pro-num-symbol"));
                        cal.disableButtons(btns, cal.listeners.mouseOutListener);
                    } else {
                        cal.listeners._initSecondRowListeners();
                    }
                } else if (scale === 2) {
                    if (oldScale === 16) {
                        lis = document.getElementById("pro-top-symbol").getElementsByTagName("li");
                        cal.disableButtons(lis, cal.listeners.firstMouseOutListener);
                    }
                    //禁用2-9
                    btns = cal.getElementsByAttribute("li", "bin-disable", document.getElementById("pro-num-symbol"));
                    cal.disableButtons(btns, cal.listeners.mouseOutListener);
                }
                cal.currentScale = scale;
            },
            /**
             * 初始化第一排操运算符事件监听
             * @private
             */
            _initFirstRowListeners: function () {
                var lis = document.getElementById(cal.typePrefix[cal.type] + "top-symbol").getElementsByTagName("li");
                cal.rebuildButtons(lis, cal.listeners.firstMouseOutListener);
            },
            /**
             * 初始化第二排运算符事件监听
             * @private
             */
            _initSecondRowListeners: function () {
                var lis = document.getElementById(cal.typePrefix[cal.type] + "num-symbol").getElementsByTagName("li");
                cal.rebuildButtons(lis, cal.listeners.mouseOutListener);
                if (cal.type === 3) {
                    //程序员型的小数点是禁用的
                    cal.disableButtons([document.getElementById("pro-point")], cal.listeners.mouseOutListener);
                }
            }
        },
        //初始化事件监听器
        initListeners: function () {
            var prefix = cal.typePrefix[cal.type];
            //设置上排运算符事件监听,如果是程序员型，因为默认是10进制，而上排是16进制数字，所以不需要设置事件监听
            if (cal.type < 3) {
                cal.listeners._initFirstRowListeners();
            }
            //设置下面一栏数字、四则运算事件监听
            cal.listeners._initSecondRowListeners();
            //显示/隐藏计算器类型选择侧边栏
            cal.addEvent(document.getElementById(prefix + "show-bar"), "click", cal.listeners.toggleTypeBarListener);
            //为侧边栏下的li绑定切换类型事件
            var bar = document.getElementById(prefix + "type-bar");
            lis = bar.getElementsByTagName("li");
            var li;
            for (var i = 0, l = lis.length; i < l; ++i) {
                li = lis[i];
                //非当前类型才有必要绑定事件
                if (li.className !== "active") {
                    cal.addEvent(li, "click", cal.listeners.switchTypeListener);
                }
            }
            //加载程序员型特有的
            if (cal.type === 3) {
                var scales = document.getElementById("pro-scales").getElementsByTagName("div"),
                    scale;
                for (i = 0, l = scales.length; i < l; ++i) {
                    scale = scales[i];
                    cal.addEvent(scale, "click", cal.listeners.switchScaleListener);
                }
            }

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains(cal.container)) {
                    window.addEventListener('paste', cal.pasteNumericValue);
                } else {
                    window.removeEventListener('paste', cal.pasteNumericValue);
                }
            });
        },
        /**
         * 相应按键按下事件
         * @param value 按键的value值(即其keyCode)
         */
        handleKey: function (value) {
            var keyCode = parseInt(value);
            //如果是一个数字或者小数点，直接显示出来
            if (keyCode < 11 || (keyCode > 39 && keyCode < 46)) {
                cal.showInput(cal.keyCodes[keyCode]);
                if (cal.type === 3) {
                    //如果是程序员型，那么需要同步显示4中进制的值
                    cal.showScales(cal.getShowInput());
                }
            } else {
                switch (keyCode) {
                    //正负号
                    case 11:
                        cal.unaryOperate(function (oldValue) {
                            oldValue += "";
                            if (oldValue === "0") {
                                return [oldValue];
                            }
                            if (oldValue.charAt(0) === '-') {
                                return [oldValue.substring(1)];
                            } else {
                                return ["-" + oldValue];
                            }
                        });
                        break;
                    //开根下
                    case 18:
                        cal.unaryOperate(function (si) {
                            return [Math.sqrt(si), "sqrt"];
                        });
                        break;
                    //平方
                    case 19:
                        cal.unaryOperate(function (si) {
                            return [Math.pow(si, 2), "sqr"];
                        });
                        break;
                    //取倒数
                    case 20:
                        cal.unaryOperate(function (si) {
                            return [si === 0 ? "0不能作为被除数" : 1 / si, "1/"];
                        });
                        break;
                    //阶乘
                    case 24:
                        cal.unaryOperate(function (si) {
                            if (si < 0) {
                                si = (0 - si);
                            }
                            if (cal.isFloat(si + "")) {
                                si = Math.floor(si);
                            }
                            return [cal.fact(si), "fact"];
                        });
                        break;
                    //Exp 转为科学计数法表示
                    case 25:
                        cal.unaryOperate(function (si) {
                            return [si.toExponential(7)];
                        });
                        break;
                    //sin
                    case 27:
                        cal.unaryOperate(function (si) {
                            return [Math.sin(si), "sin"];
                        });
                        break;
                    //cos
                    case 28:
                        cal.unaryOperate(function (si) {
                            return [Math.cos(si), "cos"];
                        });
                        break;
                    //tan
                    case 29:
                        cal.unaryOperate(function (si) {
                            return [Math.tan(si), "tan"];
                        });
                        break;
                    //10的x次方
                    case 30:
                        cal.unaryOperate(function (si) {
                            return [Math.pow(10, si), "powten"];
                        });
                        break;
                    //log
                    case 31:
                        cal.unaryOperate(function (si) {
                            //js的Math.log是e的对数，Windows计算器是10的对数，此处参考Windows
                            return [Math.log10(si), "log"];
                        });
                        break;
                    //sinh(双曲正弦函数)
                    case 32:
                        cal.unaryOperate(function (si) {
                            return [Math.sinh(si), "sinh"];
                        });
                        break;
                    //cosh(双曲余弦函数)
                    case 33:
                        cal.unaryOperate(function (si) {
                            return [Math.cosh(si), "cosh"];
                        });
                        break;
                    //tanh(双曲余切函数)
                    case 34:
                        cal.unaryOperate(function (si) {
                            return [Math.tanh(si), "tanh"];
                        });
                        break;
                    //π
                    case 35:
                        cal.unaryOperate(function (si) {
                            return [Math.PI];
                        });
                        break;
                    //按位取反(~)
                    case 48:
                        cal.unaryOperate(function (si) {
                            var result = eval("~" + si);
                            //显示四种进制的数值
                            cal.showScales(result);
                            return [result];
                        });
                        break;
                    //二元运算符开始
                    //加、减、乘、除、取余，运算比较简单，直接利用eval即可求值
                    case 13:
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                    //x的y次方
                    case 26:
                    //开任意次方根
                    case 23:
                    //And Or
                    case 46:
                    case 47:
                        if (cal.isPreInputBinaryOperator) {
                            break;
                        }
                        cal.isPreInputBinaryOperator = true;
                        cal.isOverride = true;
                        cal.binaryOperate(cal.keyCodes[keyCode], cal.operatorFacade[keyCode]);
                        break;
                    case 12:
                        cal.calculate();
                        break;
                    //ce
                    case 37:
                        cal.ce();
                        break;
                    //c
                    case 38:
                        cal.clear();
                        break;
                    //back
                    case 39:
                        cal.back();
                        break;
                    // (
                    case 21:
                        cal.setPreStep(cal.getPreStep() + " (");
                        cal.operatorStack.push("(");
                        break;
                    // )
                    case 22:
                        cal.rightTag();
                        break;
                    //向上箭头，把上次计算结果显示出来
                    case 36:
                        cal.setShowInput(cal.preResult);
                        break;
                }
            }
        },
        /**
         * 执行一元运算 比如取倒数、平方
         * @param operation 具体运算回调函数
         * 会向operation传递一个参数si，为用户当前的输入，同时operation函数应该返回一个数组，数组的第一个
         * 元素是计算的结果，第二个元素示例sqrt，第二个参数可选
         */
        unaryOperate: function (operation) {
            var si = cal.getShowInput(),
                result;
            if (cal.isInteger(si)) {
                result = operation(parseInt(si));
            } else if (cal.isFloat(si) || cal.isScientific(si)) {
                result = operation(parseFloat(si));
            }
            if (result != null) {
                cal.setShowInput(cal.checkLength(result[0]));
                if (result.length > 1) {
                    //显示prestep有两种情况:
                    //第一种就是这是第一次(指连续调用的第一次)调用一元函数，此时直接接在末尾即可
                    if (!cal.isPreInputUnaryOperator) {
                        cal.setPreStep(cal.getPreStep() + " " + result[1] + "(" + si + ")");
                        cal.isPreInputUnaryOperator = true;
                    } else {
                        //第二种就是这不是第一次，那么应该截取最后一个空格之后的内容进行替换
                        //比如1 + 3 + sqrt(100)，那么应该从最后一个空格后替换为此次操作的内容
                        var pi = cal.getPreStep();
                        pi = pi.substring(0, pi.lastIndexOf(" "));
                        pi += (" " + result[1] + "(" + si + ")");
                        cal.setPreStep(pi);
                    }
                }
                //一元运算结束后应该覆盖
                cal.isOverride = true;
            }
            cal.isPreInputBinaryOperator = false;
        },
        /**
         * 二元操作(+ - * / %)
         * @param operator 操作符
         * @param facade 运算符门面，用于显示在preStep中
         */
        binaryOperate: function (operator, facade) {
            //如果是程序员型，那么需要重置scalesSpan
            if (cal.type === 3) {
                cal.resetScales();
            }
            var si = cal.getShowInput(),
                pi = cal.getPreStep();
            if (cal.isNumber(si)) {
                //压操作数栈
                cal.operandStack.push(si);
                //设置preStep有三种情况:第一种上一步不是一元操作，那么需要设置si，第二种是一元操作，那么由于一元操作会把
                //函数表达式(比如sqrt(100))设置到preStep，所以不需要再次设置si
                //第三种就是如果最后一位是右括号，那么也不需要设置si
                cal.setPreStep(cal.getPreStep() + ((cal.isPreInputUnaryOperator || pi.charAt(pi.length - 1) === ")") ?
                    (" " + facade) : (" " + si + " " + facade)));
                var preOp = cal.operatorStack.pop();
                if (preOp != null) {
                    var op = cal.operatorPriority[operator],
                        pp = cal.operatorPriority[preOp];
                    //如果当前运算符优先级更高，那么只需压栈不需要计算
                    if (op > pp) {
                        cal.operatorStack.push(preOp);
                    }
                    //两者的优先级相等并且高于3(加减)，那么只需要计算一步
                    else if (op > 3 && op === pp) {
                        cal.operatorStack.push(preOp);
                        cal.travelStack(1);
                    } else {
                        cal.operatorStack.push(preOp);
                        cal.setShowInput(cal.checkLength(cal.travelStack(null, op)));
                    }
                }
                cal.operatorStack.push(operator);
            }
            cal.isPreInputUnaryOperator = false;
            cal.isPreInputEquals = false;
        },
        /**
         * 按下=时计算最终结果
         */
        calculate: function () {
            if (!cal.isPreInputEquals) {
                var si = cal.getShowInput(),
                    result;
                if (cal.isNumber(si)) {
                    cal.operandStack.push(si);
                    result = cal.checkLength(cal.travelStack());
                    cal.setShowInput(result);
                    cal.preResult = result;
                    cal.setPreStep("&nbsp;");
                    //程序员型需要把计算结果的四种进制值显示出来
                    if (cal.type === 3) {
                        cal.showScales(result);
                    }
                    cal.isOverride = true;
                }
                cal._reset();
                cal.isPreInputEquals = true;
            }
        },
        /**
         * 访问运算栈，返回计算结果
         * @param level 计算的层数，如果不指定，那么遍历整个栈
         * @param minPri(最小/截止优先级) 此参数针对下面的情况:
         * 2 + 2 X 3 X 2 ^ 2 X 2，由于最后一个运算符是X，优先级比^低，所以触发了对操作栈的遍历，但是不能全部遍历，应该遍历到第一个X停止
         * 如果不停止得到的将是错误的26 X 2 = 52，正确结果是2 + 24 X 2 = 50
         * @return Number
         * @private
         */
        travelStack: function (level, minPri) {
            var op, f, s,
                //result取操作数栈栈顶，因为防止在下列情况9 X (6 + 时出现undefined
                result = cal.operandStack[cal.operandStack.length - 1],
                l = level || cal.operatorStack.length,
                p = minPri || 0;
            for (var i = 0; i < l; ++i) {
                op = cal.operatorStack.pop();
                //遇到minPri或左括号立即停止，左括号也需要再次压入，因为只有一个右括号才能抵消一个左括号
                if (cal.operatorPriority[op] < p || op === "(") {
                    cal.operatorStack.push(op);
                    break;
                }
                s = cal.operandStack.pop();
                f = cal.operandStack.pop();
                result = cal._stackHelper(f, s, op);
                cal.operandStack.push(result);
            }
            return result;
        },
        /**
         * 输入了一个右括号
         */
        rightTag: function () {
            var si = cal.getShowInput();
            if (cal.isNumber(si)) {
                cal.setPreStep(cal.getPreStep() + (" " + si + " )"));
                cal.operandStack.push(si);
                //遍历计算操作栈，直至遇到左括号
                var op = cal.operatorStack.pop(),
                    f, s, result;
                while (op !== "(" && op != null) {
                    s = cal.operandStack.pop();
                    f = cal.operandStack.pop();
                    result = cal._stackHelper(f, s, op);
                    cal.operandStack.push(result);
                    op = cal.operatorStack.pop();
                }
                //此处应该直接把小括号的计算内容弹出，因为此结果显示在了showInput中，而再次执行二元操作时会先有一个压栈的操作，
                // 并且执行=时也是根据showInput内容计算的
                cal.setShowInput(cal.checkLength(cal.operandStack.pop()));
            }
        },
        /**
         * 辅助进行一次栈运算
         * @param f 第一个操作数
         * @param s 第二个操作数
         * @param op 运算符
         * @return 返回运算结果
         * @private
         */
        _stackHelper: function (f, s, op) {
            var result;
            if (op === "^") {
                result = Math.pow(f, s);
            } else if (op === "yroot") {
                result = Math.pow(f, 1 / s);
            }
            //+ - X / %5中操作
            else {
                //如果是程序员型，那么需要考虑进制的问题
                if (cal.type === 3) {
                    var scale = cal.currentScale,
                        fi, si;
                    if (scale === 10) {
                        result = eval(f + op + s);
                    } else if (scale === 16) {
                        fi = parseInt(f, 16);
                        si = parseInt(s, 16);
                        result = eval(fi + op + si).toString(16);
                    } else if (scale === 8) {
                        fi = parseInt(f, 8);
                        si = parseInt(s, 8);
                        result = eval(fi + op + si).toString(8);
                    } else {
                        fi = parseInt(f, 2);
                        si = parseInt(s, 2);
                        result = eval(fi + op + si).toString(2);
                    }
                } else {
                    result = eval(f + op + s);
                }
            }
            return result;
        },
        /**
         * 确保结果长度不大于13,如果超出，以科学计数法形式显示(小数点后7位)
         * @param value 需要检查的结果
         */
        checkLength: function (value) {
            var valueStr = value + "";
            if (cal.isFloat(valueStr)) {
                valueStr = valueStr.replace(/0+$/, "");
            }
            return valueStr.length > 12 ? value.toExponential(7) : valueStr;
        },
        //CE
        ce: function () {
            cal.setShowInput("0");
            if (cal.type === 3) {
                cal.resetScales();
            }
        },
        //C
        clear: function () {
            cal.setShowInput("0");
            cal.setPreStep("&nbsp;");
            cal._reset();
            if (cal.type === 3) {
                cal.resetScales();
            }
        },
        /**
         * 清空四个进制的值
         * @private
         */
        resetScales: function () {
            for (var i = 0; i < 4; i++) {
                cal.cache.scaleSpans[i].innerHTML = "0";
            }
        },
        back: function () {
            var oldValue = cal.cache.showInput.innerText;
            cal.setShowInput(oldValue.length < 2 ? "0" : oldValue.substring(0, oldValue.length - 1));
        },
        /**
         * 当计算器类型是程序员时，需要同步显示四种进制的值
         * @param num 需要显示的数字
         */
        showScales: function (num) {
            var result = cal.calculateScales(num),
                spans = cal.cache.scaleSpans;
            for (var i = 0; i < 4; ++i) {
                spans[i].innerHTML = result[i];
            }
        },
        /**
         * 根据当前进制分别计算出四种进制的值
         * @param num 需要计算的值
         * @return Array 共4个元素，依次为16、10、8、2进制的值
         */
        calculateScales: function (num) {
            var scale = cal.currentScale,
                result = [],
                i;
            if (scale === 10) {
                i = parseInt(num);
                result[0] = i.toString(16);
                result[1] = i;
                result[2] = i.toString(8);
                result[3] = i.toString(2);
            } else if (scale === 16) {
                //先转成10进制，然后再转成其它进制
                i = parseInt(num, 16);
                result[0] = num;
                result[1] = i;
                result[2] = i.toString(8);
                result[3] = i.toString(2);
            } else if (scale === 8) {
                i = parseInt(num, 8);
                result[0] = i.toString(16);
                result[1] = i;
                result[2] = num;
                result[3] = i.toString(2);
            } else {
                i = parseInt(num, 2);
                result[0] = i.toString(16);
                result[1] = i;
                result[2] = i.toString(8);
                result[3] = num;
            }
            return result;
        },
        /**
         * 校验字符串是否是数字
         * @param str
         * @return 是返回true
         */
        isNumber: function (str) {
            return cal.isInteger(str) || cal.isFloat(str) || cal.isScientific(str) || cal.isHex(str);
        },
        /**
         * 校验是否是整数
         * @param str
         */
        isInteger: function (str) {
            return str.match(cal.intPattern);
        },
        /**
         * 校验是否是小数
         * @param str
         */
        isFloat: function (str) {
            return str.match(cal.floatPattern);
        },
        /**
         * 是否是科学计数法
         * @param str
         */
        isScientific: function (str) {
            return str.match(cal.scientificPattern);
        },
        /**
         * 是否是16进制数字
         * @param str
         */
        isHex: function (str) {
            return str.match(cal.hexPattern);
        },
        /**
         * 显示输入的内容
         * 用于相应数字/小数点按键
         * @param value 按键的内容，不是keyCode
         */
        showInput: function (value) {
            var oldValue = cal.getShowInput();
            var newValue = oldValue;
            if (cal.isOverride) {
                //既然是覆盖，那么如果直接输入.那么肯定是0.x
                if (value === ".") {
                    newValue = "0.";
                } else {
                    newValue = value;
                }
            } else if (oldValue.length < 13) {
                if (oldValue === "0") {
                    if (value === ".") {
                        newValue = "0.";
                    } else {
                        newValue = value;
                    }
                } else {
                    newValue += value;
                }
            }
            cal.setShowInput(newValue);
            cal.isOverride = false;
            cal.isPreInputBinaryOperator = false;
            cal.isPreInputUnaryOperator = false;
            cal.isPreInputEquals = false;
        },
        /**
         * 切换计算器类型
         * @param type int 要切换到的类型
         */
        switchType: function (type) {
            //关闭选择栏
            var oldPrefix = cal.typePrefix[cal.type];
            document.getElementById(oldPrefix + "type-bar").style.display = "none";
            //切换面板
            document.getElementById(oldPrefix + "main").style.display = "none";
            document.getElementById(cal.typePrefix[type] + "main").style.display = "block";
            cal.type = type;
            if (!cal.hasInited[type]) {
                cal.initListeners();
                cal.hasInited[type] = true;
            }
            cal.initCache();
            cal._reset();
        },
        /**
         * 重置各个标志变量以及操作栈
         * @private
         */
        _reset: function () {
            cal.operandStack = [];
            cal.operatorStack = [];
            cal.isPreInputBinaryOperator = false;
            cal.isPreInputUnaryOperator = false;
            cal.isPreInputEquals = false;
        },
        /**
         * 工具方法，为element添加事件处理函数
         * @param element 需要添加事件的dom元素
         * @param name name事件名称(不含on)
         * @param handler 事件处理函数
         */
        addEvent: function (element, name, handler) {
            if (window.addEventListener) {
                element.addEventListener(name, handler);
            } else if (window.attachEvent) {
                element.attachEvent("on" + name, handler);
            }
        },
        /**
         * 工具方法，为element移除特定的事件监听
         * @param element 需要移除事件监听的dom元素
         * @param name 事件名，没有"on"
         * @param handler 需要移除的处理函数
         */
        removeEvent: function (element, name, handler) {
            if (window.removeEventListener) {
                element.removeEventListener(name, handler);
            } else if (window.detachEvent) {
                element.detachEvent("on" + name, handler);
            }
        },
        /**
         * 根据元素的属性进行查找，只要存在此属性即可
         * @param tag 目标标签名
         * @param attr
         * @param root 开始查找的起始节点，可选，默认document
         */
        getElementsByAttribute: function (tag, attr, root) {
            var parent = root || document,
                result = [];
            var arr = parent.getElementsByTagName(tag),
                a;
            for (var i = 0, l = arr.length; i < l; ++i) {
                a = arr[i];
                if (a.getAttribute(attr) != null) {
                    //这个写法...
                    result[result.length] = a;
                }
            }
            return result;
        },
        /**
         * 阶乘
         * @param n 操作数 int
         * @return
         */
        fact: (function () {
            //缓存
            var cache = [1];

            function factorial(n) {
                var result = cache[n - 1];
                if (result == null) {
                    result = 1;
                    for (var i = 1; i <= n; ++i) {
                        result *= i;
                    }
                    cache[n - 1] = result;
                }
                return result;
            }
            return factorial;
        })(),
        /**
         * 禁用按键，只有程序员型才会用到
         * @param lis 按钮集合
         * @param mouseOutListener function 鼠标移出时采用哪个监听函数，取决于按钮的位置(上排/下排)
         */
        disableButtons: function (lis, mouseOutListener) {
            var li;
            for (var i = 0, l = lis.length; i < l; ++i) {
                li = lis[i];
                li.setAttribute("class", "disable-btn");
                cal.removeEvent(li, "click", cal.listeners.keyPressListener);
                cal.removeEvent(li, "mouseout", mouseOutListener);
                cal.removeEvent(li, "mouseover", cal.listeners.mouseHoverListener);
            }
        },
        /**
         * 重新设置按键
         * @param lis 按钮集合
         * @param mouseOutListener function 鼠标移出时采用哪个监听函数，取决于按钮的位置(上排/下排)
         */
        rebuildButtons: function (lis, mouseOutListener) {
            var li;
            for (var i = 0, l = lis.length; i < l; ++i) {
                li = lis[i];
                li.removeAttribute("class");
                cal.addEvent(li, "click", cal.listeners.keyPressListener);
                cal.addEvent(li, "mouseout", mouseOutListener);
                cal.addEvent(li, "mouseover", cal.listeners.mouseHoverListener);
            }
        },
        /**
         * 获取剪切板的数据， 需要 https 才能正常使用浏览器 api
         * @see https://web.dev/async-clipboard/
         **/
        pasteNumericValue: function (base = 10) {
            let value = 0;
            if (navigator.clipboard) {
                navigator.clipboard.readText().then((text) => {
                    value = parseInt(text.trim(), base);
                    if (isNaN(value)) {
                        value = 0;
                    }
                });

                navigator.permissions.query({
                    name: 'clipboard-read'
                }).then(result => {
                    if(result.state === 'prompt' || result.state === 'granted'){
                        navigator.clipboard.readText().then(text => {
                            cal.setShowInput(cal.checkLength(text))
                            console.warn('paste=>:', text)
                        }).catch(err => {
                            console.error('读取剪贴板失败: ', err);
                        });
                    }
        
                    result.onchange = () => {
                        cal.setShowInput(cal.checkLength(text))
                        console.warn('paste=>:', text)
                    };
                });
            } else if (window.clipboardData) {
                value = parseInt(window.clipboardData.getData("Text").trim(), base);
                if (isNaN(value)) {
                    value = 0;
                }
                cal.setShowInput(cal.checkLength(value))
                console.warn('paste=>:', value)
            } else {
                !!layer && layer.alert('浏览器不支持粘贴 API (需要 https)', { shadeClose: true });
            }
        }
    };
    return cal;
})();

// 递归菜单的 json 返回一维数组
function recursionArray(array) {
    let arrcache = [];
    const Recursion = (array) => {
        array.forEach((el) => {
            arrcache.push(el);
            el.children && el.children.length > 0 ? Recursion(el.children) : "";
        });
    };
    Recursion(array);
    return arrcache;
}

function handleItemClick({ url, id, name }) {
    if(!url || !id || !name){
        return console.warn('params missing')
    }
    const target = $('.J_menuTab[data-id="'+ url +'"]');

    // 已经打开这个 tab
    if(target.length > 0){
        target.addClass('active').siblings('.J_menuTab').removeClass('active');
    } else {
        $(".J_menuTab").removeClass("active");
        const tab = '<a href="javascript:;" class="active J_menuTab" data-id="' + url + '" data-index="' + id + '" data-reload="true">' + name + ' <i class="fa fa-times-circle"></i></a>';
        $(".J_menuTabs .page-tabs-content").append(tab);
    }

    // 有打开过就显示，没有打开过就新建
    const iframe = $('.J_iframe[data-id="'+id+'"]');
    if(iframe.length > 0){
        iframe.show().siblings('.J_iframe').hide();
    }else{
        var newIframe = '<iframe class="J_iframe" name="iframe' + id + '" width="100%" height="100%" src="' + url + '" frameborder="0" data-id="' + id + '" seamless></iframe>';
        $(".J_mainContent").find("iframe.J_iframe").hide().parents(".J_mainContent").append(newIframe);
    }

    adjustTabsPosition('.J_menuTab[data-id="'+ url +'"]');
    $("#side-menu").find("li").removeClass("active");
    $("#side-menu").find("ul").removeClass("in");
    $(".sidebar-collapse").find(".nav"+id).addClass("active").parent("ul").addClass("in").parent("li").addClass("active").parent("ul").addClass("in").parent("li").addClass("active").parent("ul").addClass("in").parent("li").addClass("active").parent("ul").addClass("in").parent("li").addClass("active").parent("ul").addClass("in").parent("li").addClass("active");
    $(".sidebar-collapse").find(".nav"+id).siblings("li").find("ul").slideUp(400);
}

let cachekeySearch = {};
function cacheEventListenEnter(event) {
    if (event.key === "Enter") {
        handleItemClick(cachekeySearch);
    }
}

function openSearch() {
    layer.closeAll();

    const layIdx = layer.open({
        title: ['Search'],
        type: 1,
        shade: 0.3,
        maxmin: true,
        shadeClose: true,
        offset: '300px',
        content: $('#layer-search'),
        success: function(){
            document.addEventListener("keydown", cacheEventListenEnter);
        },
        end: function(){
            document.removeEventListener("keydown", cacheEventListenEnter);
        }
    })

    layer.style(layIdx, {
        width: '580px',
    });

    // 拿到所有菜单的数据过滤 url
    const menu = (function(MenuData) {
        try{
            return recursionArray(MenuData).map((el) => {
                return {
                    id: el.id,
                    url: el.url,
                    name: el.name
                }
            }).filter(el => !!el.url);
        }catch(e){
            console.error(e)
            return [];
        }
    })(MenuData || []);

    const selectTpl = MenuSubList.map(el => {
        return `<optgroup label="${el.name}">
                    ${el.children.map(item => `<option value='${JSON.stringify({
                        url: item.url,
                        id: item.id,
                        name: item.name,
                    })}'>${item.name}</option>`).join('')}
                </optgroup>`
    }).join('');

    const mainTpl = `<div class="layui-form layui-form-suggest">
                        <select lay-search="" lay-filter="global-select-filter">
                            <option value="">Please select or search (default case insensitive)</option>
                            ${selectTpl}
                        </select>
                    </div>`;
    $('#suggest-wrapper').html(mainTpl);

    layui.form.render();

    layui.form.on('select(global-select-filter)', function(data){
        cachekeySearch = JSON.parse(data.value)
        handleItemClick(cachekeySearch);
    });
}

function closeSearch() {
    layer.closeAll();
}

function adjustTabsPosition(n) {
    var prevAllWidth = calculateWidth($(n).prevAll()); // 按钮宽度
    var nextAllWidth = calculateWidth($(n).nextAll()); // 按钮宽度
    var contentTabsChildrenWidth = calculateWidth($(".content-tabs").children().not(".J_menuTabs")); // 两个按钮+刷新+下拉功能
    var totalWidth = $(".content-tabs").outerWidth(true) - contentTabsChildrenWidth;// 计算滚动区的真实宽度
    var marginLeftVal = 0;

    if (calculateWidth($(".page-tabs-content").children()) <= totalWidth) {
        marginLeftVal = 0;
    } else {
        if (nextAllWidth <= (totalWidth - $(n).outerWidth(true) - $(n).next().outerWidth(true))) {
            if ((totalWidth - $(n).next().outerWidth(true)) > nextAllWidth) {
                marginLeftVal = prevAllWidth;
                var currentTab = n;
                while ((marginLeftVal - $(currentTab).outerWidth()) > (calculateWidth($(".page-tabs-content").children()) - totalWidth)) {
                    marginLeftVal -= $(currentTab).prev().outerWidth();
                    currentTab = $(currentTab).prev();
                }
            }
        } else {
            if (prevAllWidth > (totalWidth - $(n).outerWidth(true) - $(n).prev().outerWidth(true))) {
                marginLeftVal = prevAllWidth - $(n).prev().outerWidth(true);
            }
        }
    }
    $(".page-tabs-content").animate({
        marginLeft: 0 - marginLeftVal + "px"
    }, "fast");
}

function calculateWidth(elements) {
    var width = 0;
    elements.each(function() {
        width += $(this).outerWidth(true);
    });
    return width;
}

$(function () {
    $(window).resize(function () {
        ResizeTable()
    });

    $("#tool-value1, #tool-value2").on("keyup", function(){
        getResult();
    });
    $("#tool-hexlist, #tool-symbol").on("change", function(){
        getResult();
    });

    $('[name="input_"]').on('click', function () {
        $('#input_num').val($(this).val());
        px(1);
    });
    $('[name="output_"]').on('click', function () {
        $('#output_num').val($(this).val());
        px(1);
    });
    $("#input_num").on('change', function () {
        $("#input_area input").removeAttr("checked");
        var val = $(this).val();
        $("#input_area input[value=" + val + "]").attr("checked", "checked");
        $('#input_value').val("");
        $('#output_value').val("");
    });
    $("#output_num").on('change', function () {
        $("#output_area input").removeAttr("checked");
        var val = $(this).val();
        $("#output_area input[value=" + val + "]").attr("checked", "checked");
        px(1);
    });

    $('#tools-btn').on('click', function () {
        const layIdx = layer.open({
            title: [''],
            type: 1,
            shade: 0.3,
            maxmin: true,
            shadeClose: true,
            offset: '30px',
            content: $('#layer-tools')
        })

        layer.style(layIdx, {
            width: '680px',
        });
    })

    $("#tab-calculator > .nav-item").on('click', function(){
        const target = $(this).attr('role');
        $(this).siblings().find('a').removeClass('active');
        $(this).find('a').addClass('active');

        $('.item-tab-content').hide();
        $(`#${target}`).show();
    })
})

window.onload = function () {

    ResizeTable();
    window.onresize = ResizeTable;

    /*全屏显示*/
    var flag = true;

    document.getElementById("screen-btn").onclick = function () {

        var elem = document.getElementById("wrapper");
        if (flag) {
            requestFullScreen(elem);
            flag = false
        } else {
            exitFull();
            flag = true
        }

    };

    function requestFullScreen(element) {
        var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
        if (requestMethod) {
            requestMethod.call(element);
        } else if (typeof window.ActiveXObject !== "undefined") {
            var wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
    }
    function exitFull() {
        // 判断各种浏览器，找到正确的方法
        var exitMethod = document.exitFullscreen || //W3C
            document.mozCancelFullScreen || //Chrome等
            document.webkitExitFullscreen || //FireFox
            document.webkitExitFullscreen; //IE11
        if (exitMethod) {
            exitMethod.call(document);
        }
        else if (typeof window.ActiveXObject !== "undefined") {//for Internet Explorer
            var wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
    }
    /*全屏显示*/

    try{
        Calculator.initCache();
        Calculator.initListeners();
    }catch(err){
        console.error(err)
    }
}

function setLocal(local){
    Cookies.set('language', local);

    setTimeout(function() {
        window.reload();
    }, 800);
}