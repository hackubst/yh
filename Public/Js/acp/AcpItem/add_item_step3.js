KindEditor.ready( function (K) {
    var editor1 = K.create( 'textarea[name="contents"]' , {
        uploadJson : '/AcpItem/upload_desc_pic' ,
        width : '770px' ,
        height : '500px' ,
        minWidth : '500px' ,
        minHeight : '300px' ,
        fillDescAfterUploadImage : false,
        allowFileManager : false,
        afterCreate : function () {
            var self = this;
            K.ctrl(document, 13 , function () {
                self.sync();
                K( '#form_desc' ).submit();
            });
            K.ctrl(self.edit.doc, 13 , function () {
                self.sync();
                K( '#form_desc' ).submit();
            });
        },
        afterUpload : function(url) {
            $('#form_addItem').append('<input type="hidden" name="item_txt_photo[]" value="' + url + '" />');
        },
        afterBlur:function(){
            this.sync();
        }
    });
});

$(function(){
    $("#form_desc").submit(function(){
        if ($("#contents").val() === '') {
            $.jPops.alert({
                title:"提示",
                content:"" + item_name + "详情不能为空！",
                okBtnTxt:"确定",
                callback:function(){
                    return true;
                }
            });
            return false;
        }
        return true;
    });
});

