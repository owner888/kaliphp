var selector = [
    {
        "id":"1",
        "name":"鼎盛互动",
        "type":"department",
        "is_child":1,
    }, {
        "id":"2",
        "name":"鼎盛互动2",
        "type":"department",
        "is_child":1,
    },{
        "id":"3",
        "name":"鼎盛互3",
        "type":"department",
        "is_child":1,
    },{
        "id":"4",
        "name":"鼎盛互动",
        "type":"department",
        "is_child":0,
    }
]
var childselector = [
    {
        "id":"5",
        "name":"张三",
        "type":"member",
        "is_child":1,
    }, {
        "id":"6",
        "name":"莉丝",
        "type":"member",
        "is_child":1,
    },{
        "id":"7",
        "name":"王五",
        "type":"member",
        "is_child":1,
    },{
        "id":"8",
        "name":"鼎盛互动22",
        "type":"department",
        "is_child":0,
    },{
        "id":"9",
        "name":"php",
        "type":"member"
    },{
        "id":"10",
        "name":"鼎盛互动22",
        "type":"department",
        "is_child":1,
    }
]
$(function(){
    var saveSingleArrDe = [];
    $("body").on("click",".selector-single-btn",function(){

        layer.open({
                type: 1,
                title: ["选择人员"],
                shade: 0.3,
                maxmin: true,
                shadeClose: false,
                area: ['600px','600px'],
                content: $(".selector-single-wrap"),
                btn: ['保存', '关闭'],
                btnAlign: 'c',
                yes:function(index, layero){
                    for(var i = 0;i<selectSingleArrDe.length;i++){
                        saveSingleArrDe.push(selectSingleArrDe[i].id);
                    }
                    $("input.selector-single-hid").val(saveSingleArrDe);
                    layer.close(index);
                    singleFn(selectSingleArrDe);
                },

                success: function () {
                    // $.ajax({
                    //     url: '?ct=department_room&ac=ajax_get_jobs',
                    //     type: 'post',
                    //     data: {"department_id":$("#department_id").val()},
                    //     dataType:"JSON",
                    //     success:function(data){
                    //         getDate(data);
                    //     }
                    // }).done(function (rs) {
                    //
                    // }).fail(function (e) {
                    //     //layer.alert('获取数据出错');
                    // })
                    getSingleDate(selector)

                }
            });
    })


    //获取一级数据
    function getSingleDate(data){
        var  singleStr = "";
        for(var i = 0;i<data.length;i++){
            if(data[i].is_child==0){
                singleStr += '<li class="first-item">' +
                    '<label ><i class="fa fa-plus-square-o no-child"></i>'+data[i].name+'</label>' +
                    '<a href="javascript:;" class="has-child no-child" data-id="'+data[i].id+'" >下级</a>' +
                    '<ul></ul></li>'
            }else{
                singleStr += '<li class="first-item">' +
                    '<label ><i class="fa fa-plus-square-o"></i>'+data[i].name+'</label>' +
                    '<a href="javascript:;" class="has-child" data-id="'+data[i].id+'" >下级</a>' +
                    '<ul></ul></li>'
            }

        }
        $("#selector-single").html(singleStr);

    }

    //获取下级数据
    function getChildSingleDate(data,pnmae,id){
        //获取展开前id
        console.log(id);
        var  childSingleStr= '';
        for(var i = 0;i<data.length;i++){
            if(data[i].type=="member"){
                childSingleStr += '<li class="check"><label><input type="checkbox" data-name="'+pnmae+" "+data[i].name+'" data-id="'+data[i].id+'" value="'+data[i].id+'">'+data[i].name+' </label></li>';
            }else{
                if(data[i].is_child==0){
                    childSingleStr += '<li class="item">' +
                        '<label ><i class="fa fa-plus-square-o no-child"></i>'+data[i].name+'</label>' +
                        '<a href="javascript:;" class="has-child no-child" data-id="'+data[i].id+'" >下级</a>' +
                        '<ul></ul></li>'
                }else{
                    childSingleStr += '<li class="item">' +
                        '<label ><i class="fa fa-plus-square-o"></i>'+data[i].name+'</label>' +
                        '<a href="javascript:;" class="has-child" data-id="'+data[i].id+'" >下级</a>' +
                        '<ul></ul></li>'
                }
            }


        }
        return childSingleStr;

    }

    //展开
    $("body").on("click",".has-child",openSingle);


    var selectSingleArrDe = [];

    function openSingle(){
        var _this = $(this),_parent = _this.closest("li");
        var id = _this.data("id");
        if(_this.hasClass("no-child")){
            layer.msg("无下级")
        }else{
            if(_parent.hasClass("open")){
                _parent.removeClass("open");
                _this.siblings("label").find("i").removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
            }else{
                _parent.siblings("li").removeClass("open");
                _parent.siblings("li").find("i").removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
                _parent.addClass("open");
                _this.siblings("label").find("i").removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
            }
        }

        var pnmae = $(this).siblings("label").text();
        _this.siblings("ul").html(getChildSingleDate(childselector,pnmae,id));
        editSingle();

    }

    //编辑时
    function editSingle(){
        var editIdArr  = $("input.selector-single-hid").val().split(',');
        console.log(editIdArr)
        var checkboxs = $("#selector-single").find('.check input[type="checkbox"]');
        var len = checkboxs.length;
        checkboxs.each(function(){
            var id = $(this).data("id");
            for(var key in editIdArr) {
                if(editIdArr[key]==id){
                    $(this).prop('checked',true);
                    $(this).attr("disabled","disabled");
                    break;
                }
            }
        })

    }

    //单选
    $("#selector-single").on("change",".check input",function(){
        var _this = $(this),
            id = _this.val(),
            name=_this.data("name");
        selectSingleArrDe = [];
        if(this.checked){
            objToArr(selectSingleArrDe,name,id);
            _this.parents("#selector-single").find("input").each(function(){
                if($(this).attr("disabled")!="disabled"){
                    $(this).prop("checked",false);
                }
            })
            _this.prop("checked",true)
        }
    })

    //del
    function arrRemove(arr,id){
        var index = -1;
        arr.forEach(function(item,i){
            if(item.id == id) index = i;
        });
        if(index!=-1)
            arr.splice(index, 1)

    }
    //push
    function objToArr(arr,name,id){
        var flag=false;
        var obj = {};
        obj.id = id;
        obj.name = name;
        for(var key in arr) {
            if(arr[key].id==id){
                flag=true;
                break;
            }
        }
        if(!flag){
            arr.push(obj);
        }
    }

})
