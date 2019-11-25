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
    var saveArrDe = [];
    $("body").on("click",".selector-btn",function(){

        layer.open({
                type: 1,
                title: ["选择人员"],
                shade: 0.3,
                maxmin: true,
                shadeClose: false,
                area: ['780px','600px'],
                content: $(".selector-wrap"),
                btn: ['保存', '关闭'],
                btnAlign: 'c',
                yes:function(index, layero){
                    for(var i = 0;i<selectArrDe.length;i++){
                        saveArrDe.push(selectArrDe[i].id);
                    }
                    $("input.selector-hid").val(saveArrDe);
                    layer.close(index);
                    selectorFn(selectArrDe);
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
                    getDate(selector)

                }
            });
    })


    //获取一级数据
    function getDate(data){
        var  firststr = "";
        for(var i = 0;i<data.length;i++){
            if(data[i].is_child==0){
                firststr += '<li class="first-item">' +
                    '<label ><i class="fa fa-plus-square-o no-child"></i>'+data[i].name+'</label>' +
                    '<a href="javascript:;" class="has-child2 no-child" data-id="'+data[i].id+'" >下级</a>' +
                    '<ul></ul></li>'
            }else{
                firststr += '<li class="first-item">' +
                    '<label ><i class="fa fa-plus-square-o"></i>'+data[i].name+'</label>' +
                    '<a href="javascript:;" class="has-child2" data-id="'+data[i].id+'" >下级</a>' +
                    '<ul></ul></li>'
            }

        }
        $("#selector").html(firststr);

    }

    //获取下级数据
    function getChildDate(data,pnmae,id){
        //获取展开前id
        console.log(id);
        var  childstr = '<li class="checkall"><label><input type="checkbox" data-name="" data-id="" value=""> 全选</label></li>';
        for(var i = 0;i<data.length;i++){
            if(data[i].type=="member"){
                childstr += '<li class="check"><label><input type="checkbox" data-name="'+pnmae+" "+data[i].name+'" data-id="'+data[i].id+'" value="'+data[i].id+'">'+data[i].name+' </label></li>';
            }else{
                if(data[i].is_child==0){
                    childstr += '<li class="item">' +
                        '<label ><i class="fa fa-plus-square-o no-child"></i>'+data[i].name+'</label>' +
                        '<a href="javascript:;" class="has-child2 no-child" data-id="'+data[i].id+'" >下级</a>' +
                        '<ul></ul></li>'
                }else{
                    childstr += '<li class="item">' +
                        '<label ><i class="fa fa-plus-square-o"></i>'+data[i].name+'</label>' +
                        '<a href="javascript:;" class="has-child2" data-id="'+data[i].id+'" >下级</a>' +
                        '<ul></ul></li>'
                }
            }


        }
        return childstr;

    }


    //展开
    $("body").on("click",".has-child2",open);


    var selectArrDe = [];

    function open(){
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
        _this.siblings("ul").html(getChildDate(childselector,pnmae,id));

        edit();

    }



    function edit(){
        var editArr = $("input.selector-hid").val().split(',');
        console.log(editArr)
        var checkboxs = $("#selector").find('.check input[type="checkbox"]');
        var len = checkboxs.length;
        checkboxs.each(function(){
            var id = $(this).data("id");
            for(var key in editArr) {
                if(editArr[key]==id){
                    $(this).prop('checked',true).trigger("change");
                    break;
                }
            }
        })

    }

    //全选操作
    $("#selector").on('change',".checkall input",function(){
        var _this = $(this);
        var checkboxs = _this.closest("li").siblings("li.check").find('[type=checkbox]');

        if(this.checked){
            checkboxs.each(function(i){
                if(!this.checked){
                    $(this).prop('checked',true);
                    var name = $(this).data("name");
                    var id = $(this).data("id");
                    objToArr(selectArrDe,name,id);
                }
            })
        }else{
            checkboxs.each(function(){
                var id = $(this).val();
                if(this.checked){
                    $(this).prop('checked',false);
                }
            })
        }

        leftCreate(this,selectArrDe);
    });

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

    //单选
    $("#selector").on("change",".check input",function(){
        var _this = $(this),
            id = _this.val(),
            name=_this.data("name");

        if(this.checked){
            objToArr(selectArrDe,name,id);

            //判断是否全选
            var checkboxs = _this.closest("li").siblings("li.check").find('[type=checkbox]');
            var len = checkboxs.length,falg=0;
            checkboxs.each(function(){
                if(this.checked){
                    falg++;
                }
            })
            if(len==falg){
                _this.closest("li").siblings("li.checkall").find("input").prop('checked',true);
            }
        }else{
            arrRemove(selectArrDe,id);
            _this.closest("li").siblings(".checkall").find("input").prop('checked',false);
        }
        leftCreate(this,selectArrDe);
    })

    //左侧添加
    function leftCreate(e,arr){
        var _this = $(e);
        var leftstr = "";
        $.each(arr,function(i,item){
            leftstr += '<li class="left-item" data-name="'+item.name+'" data-id="'+item.id+'"><span >'+item.name+'</span> <i class="fa fa-close"></i></li>'
        })

        $(".selector-right ul").html(leftstr);
    }

    //左侧删除
    $(".selector-right").on("click",".fa-close",function(){
        var _this = $(this);
        var id = _this.parents("li").data("id");
        _this.parents("li").remove();
        arrRemove(selectArrDe,id);
        $("#selector").find("input[data-id="+id+"]").prop('checked',false).trigger('change');
    })

})
