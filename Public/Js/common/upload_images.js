/****************图片上传js类*************/
/**
 * token 业务服务器生成 uptoken :根据accessKey，secretKey去获取uptoken，此代码写在GlobalAction 中
 * Qiniu_UploadUrl 提交的地址，为七牛服务端
 * image_domain 图片所在的域名 七牛服务器端
 * param div 图片选择中最开始的div的id名称
 * param file_id, 图片选取input里name=file的id名称
 * param width， 设置图片的宽度 ，默认为300
 * param height, 设置图片的高度 , 默认为300
 * author wzg
 */
function upload_image_base(div)
{
    $("#"+div+"_uploader").change(function() {

        var Qiniu_UploadUrl = "http://up.qiniu.com";
        var token = $("#uptoken").val()
        var image_domain = $("#image_domain").val();

        //普通上传
        var Qiniu_upload = function(f, token, key) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', Qiniu_UploadUrl, true);
            var formData, startDate;
            formData = new FormData();
            if (key !== null && key !== undefined) formData.append('key', key);
            formData.append('token', token);
            formData.append('file', f);
            var taking;
            $('#'+div).find('.preview').removeClass('hide');
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var nowDate = new Date().getTime();
                    taking = nowDate - startDate;
                    var x = (evt.loaded) / 1024;
                    var y = taking / 1000;
                    var uploadSpeed = (x / y);
                    var formatSpeed;
                    if (uploadSpeed > 1024) {
                        formatSpeed = (uploadSpeed / 1024).toFixed(2) + "Mb\/s";
                    } else {
                        formatSpeed = uploadSpeed.toFixed(2) + "Kb\/s";
                    }
                    var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                    console && console.log(percentComplete, ",", formatSpeed);
                }
            }, false);

            xhr.onreadystatechange = function(response) {
                if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText != "") {
                    var blkRet = JSON.parse(xhr.responseText);
                    console && console.log(blkRet);

                    var img_url = image_domain + "/"+ blkRet.key;
                    //var img_url = image_domain + "/"+ blkRet.key+"?imageView2/1/w/"+width+"/h/"+height;
                    //if (width || height) {
                    //    img_url += "?imageView2/1/";
                    //    if (width) img_url += "w
                    //}

                    $('#'+div).find('#J_Preview').attr('src', img_url);
                    $('#'+div).find('.preview').show();
                    $('#'+div).find('#J_ImgUrl').val(blkRet.key);
                    $('#'+div).find('#add_li').hide();

                    //$("#dialog").html(xhr.responseText).dialog();
                } else if (xhr.status != 200 && xhr.responseText) {

                }
            };
            startDate = new Date().getTime();
            xhr.send(formData);
        };
        if ($("#"+div+"_uploader")[0].files.length > 0 && token != "") {
            Qiniu_upload($("#"+div+"_uploader")[0].files[0], token, $("#key").val());
        } else {
            alert('请选择图片');
        }
    })
}

/**
 * 删除图片，只是不显示，并没有删除七牛中的保存的图片
 * @author wzg
 */
function delImage(div_id)
{
    var ajaxLoading = $('#' + div_id).find('#J_AjaxLoading');
    var preview = $('#' + div_id).find('#J_Preview');
    var param = {};
    var _id = $('#' + div_id).find('#J_ImgUrl').data('id');
    var imgUrl = $('#' + div_id).find('#J_ImgUrl').val();

    if (_id != '') {
        param.id = _id;
    }
    if (imgUrl != '') {
        param.img_url = imgUrl;
    }
    $('#' + div_id).find('#J_ImgUrl').attr('data-id', '').val(null);
    $('#' + div_id).find('#J_Del').off('click', delImage);
    preview.removeAttr('src').parent().parent().addClass('hide');
    $('#' + div_id).find('#uploader').parent().removeClass('hide');
    $('#' + div_id).find('#add_li').show();
    ajaxLoading.fadeOut();
}

function upload_file(div_id)
{
    // ajax上传图片
    new AjaxUpload("#" + div_id + "_uploader", {
        action: "/AcpArticleAjax/uploadHandler",
        name: "imgFile",
        responseType: "json",
        onSubmit: function(){
    	//alert('正在上传');
    	//preview处的图片改为loading图片
    	$('#' + div_id).find('.preview').removeClass('hide');
        },
        onChange: function(file, extension){
        	if (extension && /^(jpg|png|jpeg|gif)$/.test(extension)) {
        	    return true;
        	}
        	else {
        	    alert('暂不支持该图片格式！');
        	    return false;
        	}
        },
        onComplete: function(file, response){
        	console.log(response);
        	if (response.status === 0) {
        	    alert(response.msg);
        	}
        	else if (response.status === 1) {
        	    $('#' + div_id).find('#J_Preview').attr('src', response.img_url);
        	    $('#' + div_id).find('.preview').show();
        	    $('#' + div_id).find('#J_ImgUrl').val(response.img_url);
        	    $('#' + div_id).find('#add_li').hide();
        	}
        }
    });
}

function delImage(div_id)
{
    var ajaxLoading = $('#' + div_id).find('#J_AjaxLoading');
    var preview = $('#' + div_id).find('#J_Preview');
    var param = {};
    var _id = $('#' + div_id).find('#J_ImgUrl').data('id');
    var imgUrl = $('#' + div_id).find('#J_ImgUrl').val();

    if (_id != '') {
        param.id = _id;
    }
    if (imgUrl != '') {
        param.img_url = imgUrl;
    }
    $.ajax({
        url: '/AcpArticleAjax/delImage',
        type: 'post',
        data: param,
        dataType: 'json',
        beforeSend: function(){
    	ajaxLoading.show();
        },
        success: function(data){
        //  console.log(data);
    	if (data.status === 1) {
    	    $('#' + div_id).find('#J_ImgUrl').attr('data-id', '').val(null);
    	    $('#' + div_id).find('#J_Del').off('click', delImage);
    	    preview.removeAttr('src').parent().parent().addClass('hide');
    	    $('#' + div_id).find('#' + div_id + '_uploader').parent().removeClass('hide');
    	    $('#' + div_id).find('#add_li').show();
    	}
    	ajaxLoading.fadeOut();
        }
    });
}


