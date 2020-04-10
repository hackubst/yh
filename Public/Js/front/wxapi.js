var images = {
    localIds: [],
    serverId: [],
    html: []
};

var wx_upload_container_id = '';

function wx_upload_success_callback()
{
    var content = images.html.join(',');

    $('#'+wx_upload_container_id).find('#preview').removeClass('hide');
    $('#'+wx_upload_container_id).find('#J_Preview').attr('src', content);
    $('#'+wx_upload_container_id).find('#preview').show();
    $('#'+wx_upload_container_id).find('#add_li').hide();
    $('#'+wx_upload_container_id).find('#J_ImgUrl').val(images.serverId.join(','));

    var wx_upload_handler = document.getElementById(wx_upload_container_id);
    if (wx_upload_handler) {
        wx_upload_handler.removeEventListener('touchstart', handle_touch_event, false);
    }

}

function wx_upload_delImage()
{
    var preview = $('#' + wx_upload_container_id).find('#J_Preview');

    $('#'+wx_upload_container_id).find('#J_ImgUrl').val(null);
    $('#'+wx_upload_container_id).find('#J_Del').off('click', wx_upload_delImage);
    $('#'+wx_upload_container_id).find('#pic_uploader').parent().removeClass('hide');
    $('#'+wx_upload_container_id).find('#add_li').show();

    preview.removeAttr('src').parent().parent().addClass('hide');

    var wx_upload_handler = document.getElementById(wx_upload_container_id);
    if (wx_upload_handler) {
        wx_upload_handler.addEventListener('touchstart', handle_touch_event, false);
    }

    images.html =[];
}

function wx_upload_error_callback() {
    alert('微信服务器繁忙，请稍后再试');
}

var wx_image_uploader = {
    container_id     : '',
    upload_limit     : 1,
    success_callback : wx_upload_success_callback,
    error_callback   : wx_upload_error_callback,
    images_info      : images
}

wx.config({
    //debug: true,
    debug: false,
    appId: appId,
    timestamp: timestamp,
    nonceStr: nonceStr,
    signature: signature,
   jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'chooseImage',
        'uploadImage',
        'previewImage'
      ]
});

var handle_touch_event = function (e) { 
    wx.chooseImage({
        count: wx_image_uploader.upload_limit, // 默认9
          sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
       // sourceType: ['album'], // 可以指定来源是相册还是相机，默认二者都有
        success: function (res) {
            images.localIds = res.localIds;
            var i = 0, length = images.localIds.length;
            if (length == 0) {
                alert('upload fail, please try again');
                return;
            }
            function upload() {
                wx.uploadImage({
                    localId: images.localIds[i],
                    success: function (res) {
                        //alert('已上传：' + i + '/' + length);
                        images.serverId.push(res.serverId);
                        images.html.push(images.localIds[i]);
                        i++;
                        if (i < length) {
                            upload();
                        } else { 
                            var callback = wx_image_uploader.success_callback;
                            if (callback) callback();
                        }
                    },
                    fail: function (res) {
                        //alert(JSON.stringify(res));
      var callback = wx_image_uploader.error_callback;
      if (callback) callback();
                    }
                });
            }
            upload();
        }
    });

}

wx.ready(function () {
  // 1 判断当前版本是否支持指定 JS 接口，支持批量判断
    wx.checkJsApi({
      jsApiList: [
        'getNetworkType',
        'previewImage',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'chooseImage',
        'uploadImage',
        'onMenuShareWeibo'
      ],
      success: function (res) {
        //alert('support: ' + JSON.stringify(res));
      }
    });
//alert('title = ' + title + ', desc = ' + desc + ', link = ' + link + ', img = ' + img + ', appId = ' + appId + ', timestamp = ' + timestamp + ', nonceStr = ' + nonceStr + ', signature = ' + signature);
  var shareData = {
    title: title,
      desc: desc,
      link: link,
      imgUrl: img,
      trigger: function (res) {
        //alert('用户点击发送给朋友');
      },
      success: function (res) {
        //alert('已分享');
      },
      cancel: function (res) {
        //alert('已取消');
      },
      fail: function (res) {
        //alert('call fail: ' + JSON.stringify(res));
      }
  };
  wx.onMenuShareAppMessage(shareData);
  wx.onMenuShareTimeline(shareData);
  wx.onMenuShareQQ(shareData);
  wx.onMenuShareWeibo(shareData);

  //上传图片接口
});
wx.error(function (res) {
  //alert('error: ' + res.errMsg);
});
