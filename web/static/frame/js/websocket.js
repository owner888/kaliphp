var heartbeat_timer = 0;
var last_health = -1;

// 配置建议
// health_timeout对应 swoole 的 heartbeat_idle_time
// check_interval对应 swoole 的 heartbeat_check_interval
// 建议 health_timeout 为 check_interval 的两倍多一点。
// 这个两倍是为了进行容错，允许丢一个包
// 而多一点是考虑到网络的延时。
// 你可以跟据实际的业务来调整这个容错率（允许丢几个包）。
// 连接最大的空闲时间 （如果最后一个心跳包的时间与当前时间之差超过这个值，则认为该连接失效）
var health_timeout = 30000;
// 定时检测在线列表的时间
var check_interval = 10000;

$(document).ready(function() {
    window.ws = ws_conn(WEBSOCKET_URL);
});

function keepalive(ws) {
    var time = new Date();
    if (last_health != -1 && (time.getTime() - last_health > health_timeout)) {
        console.log("服务器没有响应.");
        //此时即可以认为连接断开，可以设置重连或者关闭
        //ws.close();
    } else {
        console.log("连接正常.");
        if (ws.bufferedAmount == 0) {
            ws.send('~H#C~');
        }
    }
}

// websocket function
function ws_conn(to_url) {
    console.log(to_url)
    to_url = to_url || "";
    if (to_url == "") {
        return false;
    }

    console.log("Connecting...");
    clearInterval(heartbeat_timer);

    var ws = new WebSocket(to_url);
    ws.onopen = function() {
        console.log("Connect Successful...");
        //测试Websocket MVC框架
        //strJSON = JSON.stringify({ ct: "websocket", ac: "onopen", name: "kaka" })
        //ws.send(strJSON);
        heartbeat_timer = setInterval(function() { keepalive(ws) }, check_interval);
    }

    ws.onerror = function() {
        console.log("Error...");
        clearInterval(heartbeat_timer);
        $("body").toastr({
            type:'error',                     
            title:'系统消息',               
            text:'消息服务器无法连接，请联系管理员', 
        });
    }

    ws.onclose = function() {
        console.log("Close...");
        clearInterval(heartbeat_timer);
        $("body").toastr({
            type:'error',                     
            title:'系统消息',               
            text:'消息服务器连接已关闭，请联系管理员', 
        });

    }

    ws.onmessage = function(e) {

        // 收到服务端发过来的心跳包，更新最后健康时间
        var time = new Date();
        if (e.data == ('~H#S~')) {
            last_health = time.getTime();
            return;
        }

        console.log("Message...");

        data = decode(e.data, WEBSOCKET_KEY);
        obj = JSON.parse(data);
        if (obj.Data.url || obj.Data.url != '') {
            obj.Data.url = decodeURIComponent(obj.Data.url);
        }
        console.log(obj);

        // 系统消息
        if (obj.Event == "sysmessage" && obj.Data.text != '') {
            $("body").notifyFn({
                title: obj.Data.title,              // 导航标题
                text: obj.Data.text,                // 消息内容
                time: obj.Data.time,                // 消息时间
                url: obj.Data.url,                  // 消息链接
                reload: true                        // 是否要刷新页面
            });

            $("body").toastr({
                type:'info',                        // 提示类型 “success-成功” “info-提示” “warning-警告” error-错误
                title:obj.Data.title,               // 标题信息 可不填
                text:obj.Data.text,                 // 内容信息
                time:obj.Data.time,                 // 时间
                url:obj.Data.url,                   // 点击跳转连接，为空时不跳转
                reload:true,                        // 是否要刷新页面    
            });

        } 
        // 广播消息
        else if (obj.Event == "broadcast") {}
    }


    return ws;
}
