/**
 * 单图上传控件
 * @author  tale
 * @param  string div_id 控件id
 * @return void
 */
function upload_single_image(div_id)
{
    var module = $('#' + div_id).data('module');
    var dir = $('#' + div_id).data('dir');
    var _action = "/"+module+"/upload_image";
    if (dir) {
        _action += '/dir/' + dir;
    }
    // ajax上传图片
    new AjaxUpload("#" + div_id + "_uploader", {
        action: _action,
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

/**
 * 单图上传控件中的图片删除（只做了页面上删除，未做物理删除）
 * @author  tale
 * @param  string div_id 控件id
 * @return void
 */
function delete_single_image(div_id)
{
    $('#' + div_id).find('#J_ImgUrl').val('');
    $('#' + div_id).find('#J_Preview').removeAttr('src').parent().parent().addClass('hide');
    $('#' + div_id).find('#' + div_id + '_uploader').parent().removeClass('hide');
    $('#' + div_id).find('#add_li').show();
}

/**
 * 多图上传控件
 * @author  tale
 * @param  string div_id 控件id
 * @return void
 */
function upload_batch_image(div_id)
{
    var module = $('#' + div_id).data('module');
    var dir = $('#' + div_id).data('dir');
    var _action = "/"+module+"/upload_image";
    if (dir) {
        _action += '/dir/' + dir;
    }
    // ajax上传图片
    new AjaxUpload("#" + div_id + "_uploader", {
        action: _action,
        name: "imgFile",
        autoSubmit: true,
        responseType: "json",
        onComplete: function(file, response){
            console.log(response);
            if (response.status === 0) {
                alert(response.msg);
            } else if (response.status === 1) {
                var html = '<li class="fi-imgslist-item">'
                    + '<img src="' + response.img_url + '" alt="">'
                    + '<input type="hidden" name="'+div_id+'[]" value="' + response.img_url + '">'
                    + '<a href="javascript:;" onclick="delete_batch_image(this);" class="del J_del_pic" title="删除这张图"><i class="gicon-remove"></i></a>'
                    + '</li>';

                $(html).insertBefore("#" + div_id + " #J_add_pic");
            }
        }
    });
}

/**
 * 多图上传控件中的单图删除（只做了页面上删除，未做物理删除）
 * @author  tale
 * @param  string div_id 控件id
 * @return void
 */
function delete_batch_image(obj){
    $(obj).parent().remove();
}

// 批量初始化单图控件
$('.single_image_widget').each(function(){
    upload_single_image($(this).attr('id'));
});

// 批量初始化多图控件
$('.batch_image_widget').each(function(){
    upload_batch_image($(this).attr('id'));
});
