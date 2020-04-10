function setDefaultCity()
{
	var map = new BMap.Map("container"); //初始化地图
	var opts = {type: BMAP_NAVIGATION_CONTROL_LARGE}; //初始化地图控件
	map.addControl(new BMap.NavigationControl(opts));  
	var point = new BMap.Point(lon, lat); //初始化地图中心点
	//var marker = new BMap.Marker(point); //初始化地图标记
	//marker.enableDragging(); //标记开启拖拽
	var gc = new BMap.Geocoder();
	//获取地址信息
	gc.getLocation(point, function(result){
		address = result.addressComponents.city + result.addressComponents.district;

		//获取地区ID
		$.ajax(
		{
			url:"/FrontAddress/get_area_id",
			type:"POST",
			dataType:"json",
			data:{
				city: result.addressComponents.city,
				area: result.addressComponents.district,
			},
			timeout:10000,
			success:function(d){
				if(d) {
					if(d.area_id != 0)
					{
						//返回成功，代码写这里
						ip_area_id = d.area_id;
						if (default_area == 0)
						{
							$('#cur_city').html(address);
							$('#cur_city').attr('href', '/FrontMall/mall_list/area_id/' + ip_area_id);
						}
					}
					else
					{
					}
				}
			}
		});
		//console.log(result);
	})
}

function log_city_change(ip_area_id, default_area, area_id)
{
	$.ajax(
	{
		url:"/FrontAddress/log_city_change",
		type:"POST",
		dataType:"json",
		data:{
			ip_area_id: ip_area_id,
			default_area: default_area,
			area_id: area_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d.area_id != 0)
				{
					//返回成功，代码写这里
				}
				else
				{
				}
			}
		}
	});
}
