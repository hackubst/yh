/**
 * acp后台商品管理的公共js
 */

/**
 * 展示待上传的图片
 * @user  Zy
 * @param file  input type为file的对象
 * @param class_name  需要展示的图片class名，ie中无用
 * @param width ie中图片宽度
 * @param height ie中图片高度
 * 使用时为:
 *   $("input[type='file']").change(function(){
 *    ViewImage(this,'view');
 *   });
 */
function ViewImage(file,class_name,width,height)
{
    var viewImg  = $('.'+class_name);
    var imgWidth  = width || 100;
    var imgHeight  = height || 100;

    if (file["files"] && file["files"][0])
    {
        var reader = new FileReader();
        reader.onload = function(evt){
            viewImg.attr({src : evt.target.result});
        }
        reader.readAsDataURL(file.files[0]);
    }
    else
    {

        var ieImageDom = document.createElement("div");

        $(ieImageDom).css({
            width: imgWidth,
            height: imgHeight
        }).attr({"class":class_name});

        viewImg.parent().append(ieImageDom);
        viewImg.remove();
        file.select();
        path = document.selection.createRange().text;
        $(ieImageDom).css({"filter": "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled='true',sizingMethod='scale',src=\"" + path + "\")"});
    }
}

/**
 * 单个操作
 * @user  Zy
 * @param selector 选择器
 * @param content 弹出层内容
 * @param url 请求的url地址
 * @param action 操作名称
 */
function single_action(selector, content, url, action) {
    $(selector).click(function(){
        var _this = this;
        $.jPops.confirm({
            title:"提示",
            content: content,
            okBtnTxt:"确定",
            cancelBtnTxt:"取消",
            callback:function(r){
                if(r){
                    var item_id = $(_this).siblings(":hidden[name='item_id']").val();
                    $.post(url, {id: item_id, action: action}, function(data){
                        $.jPops.message({
                            title: "提示",
                            content: data,
                            callback: function() {
                                //location.reload();
                            }
                        });
                    }, 'json')
                }
                return true;
            }
        });
    });
}

/**
 * 批量操作
 * @user  Zy
 * @param selector 选择器
 * @param content 弹出层内容
 * @param content_not_select 没选择商品时，弹出层的内容
 * @param url 请求的url地址
 * @param action 操作名称
 */
function batch_action(selector, content, content_not_select, url, action) {
    $(selector).click(function(){
        var chk_value = [];
        $(':checked[name="checkIds[]"]').each(function(){
            chk_value.push($(this).val());
        });

        if (chk_value.length == 0) {
            $.jPops.message({
                title:"提示",
                content:content_not_select
            });
            return false;
        } else {
            $.jPops.confirm({
                title: "提示",
                content: content,
                okButton:"确定",
                cancelButton:"取消",
                callback: function(r) {
                    if(r){
                        $.post(url, {arr_id: chk_value, action: action}, function(data){
                            $.jPops.message({
                                title: "提示",
                                content: data,
                                callback: function() {
                                    location.reload();
                                }
                            });
                        }, 'json')
                    }
                    return true;
                }
            })

        }
        return false;
    });
}
