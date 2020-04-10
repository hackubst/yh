$(function() {

    $('#form1').ajaxForm({
        beforeSubmit: show_mask,
        success: complete,
        dataType: 'json'
    });

    function show_mask() {
        $.jPops.showLoading();
    }

    function complete(data) {
        if (data.status == 1) {
            location.href = data.url;
        } else {
            fleshVerify();
            $.jPops.hideLoading();
            showError(data.info, $("#" + data.dom_name))
            $('#' + data.dom_name).focus().select();
        }
    }

    $("#form1").validate({
        // debug: true,
        rules: {
            user: {
                required: true
            },
            pass: {
                required: true
            },
            vdcode: {
                required: true
            }
        },
        messages: {
            user: {
                required: "请输入用户名"
            },
            pass: {
                required: "请输入密码"
            },
            vdcode: {
                required: "请输入验证码"
            }
        },
        errorPlacement: showError,
        success: hideError
    });
});

function fleshVerify() {
    //重载验证码
    var time = new Date().getTime();
    document.getElementById('verifyImg').src = app + '/Public/verify/' + time;
}

function showError(error, element) {
    try {
        var msg = error.text();
    } catch (e) {
        var msg = error;
    }
    element.addClass("error");
    element.siblings('.fi-help-text').text(msg).addClass('error').removeClass('hide');
}

function hideError(element) {
    element.removeClass("error");
    element.siblings('.fi-help-text').text("").removeClass('error').addClass('hide');
}
