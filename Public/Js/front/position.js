/*定位用户位置*/
$(function() {
  //定位当当前位置
  if (navigator.geolocation) {
      $("#positionBtn").click(function() {
          //$("#pos_text").html("定位获取中...");
          gpsLocation();
      })
  }
  // 定位
  function gpsLocation() {
      var geolocation = new BMap.Geolocation();
      var gc = new BMap.Geocoder();
      var loationFlag = 0;
      geolocation.getCurrentPosition(function(r) {
          if (this.getStatus() == BMAP_STATUS_SUCCESS) {
              var pt = r.point;
              var message = "";
              gc.getLocation(pt,function(rs) {
                  var addComp = rs.addressComponents;
         /* console.log(pt.lng);
				  console.log(pt.lat);
				  console.log(rs);*/
                  cur_lon = pt.lng;
                  cur_lat = pt.lat;
                  //var address = addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber;
                  var address = addComp.city + addComp.district + addComp.street + addComp.streetNumber;
                  //console.log(address);
                  set_build();
                  save_lon_sesion();
                  //$('#pos_text').html(address);
                  loationFlag = 1;                  
        				  $("#positionBtn").click(function(){
        					 //window.location.href = "/FrontMall/mall_list";
        				  });                  									
              });
          } else {
              $("#pos_text").html("定位失败");
              $("#positionBtn").click(function() {
				          $("#pos_text").html("定位获取中...");
				          gpsLocation();
				      });
          }
      },{enableHighAccuracy:true});
      setTimeout(function(){
          if(loationFlag == 0){
	          	var error="网络状况不佳或未打开GPS，请检查后重新定位！";
							$("#tan").html(error);
							tishi();
              $("#pos_text").html("定位到当前位置");
          }
      },1000 * 10);
  }
  //if(is_posid){
    //gpsLocation();
  //}
});
