
$(function(){
    //密码验证
    $.validator.addMethod("alnum", function(value,element) {
        var lv = 0;
        if (value.match(/[A-Z]/g)) {
            lv++;
        }
        if (value.match(/[a-z]/g)) {
            lv++;
        }
        if (value.match(/[0-9]/g)) {
            lv++;
        }
        if (lv < 3) {
            return false;
        } else {
            return true;
        }
    }, '只能包括英文字母和数字');


    //选择必须选择一个
    function chekcone(msg){
        $.validator.addMethod("checkOne", function(value,element) {
            if(value.length==0){
                if($(element).parents(".form-group").siblings(".checkonewrap").find(".checkone").val().length==0){
                    return false;
                }else{
                    $(element).parents(".form-group").siblings(".checkonewrap").removeClass("has-error");
                    $(element).parents(".form-group").siblings(".checkonewrap").find("span").remove();
                    return true;

                }
            }else{
                $(element).parents(".form-group").siblings(".checkonewrap").removeClass("has-error");
                $(element).parents(".form-group").siblings(".checkonewrap").find("span").remove();
                return true;

            }
        }, msg);
        chekcone("sdf");
    };
})
$.validator.setDefaults({
    highlight: function(e) {;
        $(e).closest(".form-group").removeClass("has-success").addClass("has-error");
        $(e).parents("form").find("button[type=submit]").attr("disabled",false);
    },
    success: function(e) {
        e.closest(".form-group").removeClass("has-error").addClass("has-success");
        //$(e).siblings("span").remove();
    },
    errorElement: "span",
    errorPlacement: function(e, r) {
        e.appendTo(r.is(":radio") || r.is(":checkbox") ? r.parent().parent().parent() : r.parent());
        if($(r).attr("data-plugin")=="tokenfield"){
            e.appendTo(r.parent().parent())
        }
    },
    errorClass: "help-block m-b-none",
    validClass: "help-block m-b-none",
    submitHandler: function(form) {
        console.log(1)
        $(form).find("button[type=submit]").attr("disabled",true);
        form.submit();
    }
}),
    $().ready(function() {


        $("#commentForm").validate();

        var e = "<i class='fa fa-times-circle'></i> ";
        $("#commentForm1").validate({
            rules: {
                username:{
                    required: !0,
                    minlength: 2
                },
                password:{
                    minlength:6,
                    alnum:true
                }
            }
        })
    });

