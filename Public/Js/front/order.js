function set_order_state(order_id, order_status)
{
	$.post('/FrontOrder/set_order_state', {"order_id": + order_id, "order_status": order_status}, function(data, textStatus) 
	{
		if (data != 'failure')
		{
			alert('恭喜您，操作成功！');
			location.reload();
		}
		else
		{
			alert('对不起，操作失败');
		}
	});
}

function goPay(order_id)
{
	location.href = '/FrontOrder/pay_order/order_id/' + order_id;
}

function realtime_express_query()
{
	if (map_called)
	{
		return false;
	}
	map_called = true;;
	// 百度地图API功能
	map = new BMap.Map("real_map");
	map.enableScrollWheelZoom();
	map.centerAndZoom(new BMap.Point(lon, lat), 17);

	//向地图中添加缩放控件
	var ctrl_nav = new BMap.NavigationControl({ anchor: BMAP_ANCHOR_TOP_LEFT, type: BMAP_NAVIGATION_CONTROL_LARGE });
	map.addControl(ctrl_nav);

	for (var k = 0; k < route_path.length; k ++)
	{
		var p = new BMap.Point(route_path[k]['lon'], route_path[k]['lat']);
		var icon_path = IMG_DOMAIN + (route_path[k]['remark'] == undefined ? '/Public/Images/front/user_logo.png' : '/Public/Images/front/merchant_logo.png');
		var myIcon = new BMap.Icon(icon_path, new BMap.Size(30,40),{anchor : new BMap.Size(27, 13)});
		var marker = new BMap.Marker(p, {icon:myIcon});
		var content = route_path[k]['remark'] == undefined ? '我家' : route_path[k]['remark'];
		map.addOverlay(marker);
		addClickHandler(content, marker, k);
	}

	var icon_path = IMG_DOMAIN + '/Public/Images/front/foot_man.png';
	var myIcon = new BMap.Icon(icon_path, new BMap.Size(45,60),{anchor : new BMap.Size(27, 13)});
	car = new BMap.Marker(new BMap.Point(lon,lat), {icon:myIcon});
	map.addOverlay(car);
	label = new BMap.Label('努力配送中...',{offset:new BMap.Size(-45,-32),position:new BMap.Point(lon,lat)});
	map.addOverlay(label);
	setInterval(getPoint, 5000);

	// 实例化一个驾车导航用来生成路线
	var drv = new BMap.WalkingRoute(map);
	for (var j = 0; j < route_path.length; j ++)
	{
		if (j > 0)
		{
			drv.search(new BMap.Point(route_path[j - 1]['lon'], route_path[j - 1]['lat']), new BMap.Point(route_path[j]['lon'], route_path[j]['lat']));
		}
		viewport[j] = new BMap.Point(route_path[j]['lon'], route_path[j]['lat']);
	}
	var arrPois = new Array();
	drv.setSearchCompleteCallback(function(){
		if (drv.getStatus() == BMAP_STATUS_SUCCESS)
		{
			arrPois = drv.getResults().getPlan(0).getRoute(0).getPath();
			var tmp_arrPois = arrPois;
			/*if (start_point)
			{
				tmp_arrPois = new Array();
				tmp_arrPois[0] = start_point;
				for (var k = 0; k < arrPois.length; k ++)
				{
					tmp_arrPois[k + 1] = arrPois[k];
				}
			}*/
			//tmp_arrPois[tmp_arrPois.length] = new BMap.Point(route_path[index]['lon'], route_path[index]['lat']);

			start_point = arrPois[arrPois.length - 1];
			index ++;
			console.log(tmp_arrPois);
			console.log(start_point);
			map.addOverlay(new BMap.Polyline(tmp_arrPois, {strokeColor: '#111'}));
			map.setViewport(tmp_arrPois);
			setTimeout(function(){
				map.setViewport(viewport);
			},1000);
		}
	});

	function $(element){
		return document.getElementById(element);
	}
}

function addClickHandler(content,marker, k){
	marker.addEventListener("click",function(e){
		openInfo(content,e,k);
	});
}

//显示实时物流查询的地图
function openInfo(content,e,k)
{
	var p = e.target;
	var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
	var opts = {
		width : 200,     // 信息窗口宽度
		height: 100,     // 信息窗口高度
		title : k == (route_path.length - 1) ? '收货地点' : (k == -1) ? '亲，马上到' : route_path[k]['shop_name'] , // 信息窗口标题
		enableMessage:true,//设置允许信息窗发送短息
		message:"亲耐滴，晚上一起吃个饭吧？戳下面的链接看下地址喔~"
	}

	var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象 
	map.openInfoWindow(infoWindow,point); //开启信息窗口
}

//获取镖师当前坐标
function getPoint()
{
	$.ajax({
		url:"/FrontOrder/get_location",
		type:"POST",
		dataType:"json",
		data:{
			order_id: order_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if (d.code == 0)
				{
					var tmp_lon = d.lon == undefined ? '' : d.lon;
					tmp_lon = tmp_lon ? tmp_lon : (parseFloat(route_path[0]['lon']) - parseFloat(route_path[1]['lon'])) / 1000 * i + lon;
					var tmp_lat = d.lat == undefined ? '' : d.lat;
					tmp_lat = tmp_lat ? tmp_lat : (parseFloat(route_path[0]['lat']) - parseFloat(route_path[1]['lat'])) / 1000 * i + lat;
					var p = new BMap.Point(tmp_lon, tmp_lat);
console.log(tmp_lon + ',' + tmp_lat);

					//加label
					label.setPosition(p);

					car.setPosition(p);
					console.log('镖师当前坐标：' + p);
					addClickHandler('正在配送，请耐心等待...', car, -1);
					//中心点调整
					if (i % 1000 == 0)
					{
						map.centerAndZoom(p, 15);
						console.log(i);
					}
					i++;
				}
			}
		}
	});
}

//订单加价
function addFreight(price, payway_id, pay_password, pay_tag)
{
	$.ajax({
		url:"/FrontOrder/add_freight",
		type:"POST",
		dataType:"json",
		data:{
			order_id: order_id,
			price: price,
			payway_id: payway_id,
			pay_password: pay_password,
		},
		timeout:10000,
		success:function(d){
			if(d) {
//alert(d.code + ', ' + d.msg);
				console.log(d);
				if (pay_tag == 'wallet' && d.code == 0)
				{
					alert('加价成功');
					$('#pay_tan_fix,#screenIfm').hide();
				}
				else if (pay_tag == 'wxpay')
				{
					jsApiParameters = d.msg;
					callpay();
				}
				else
				{
					var error = '';
					switch(d.code)
					{
						case -2:
							error="对不起，当前订单状态无法执行该操作";	
							break;
						case -1:
							error=d.msg;	
							break;
						case 1:
							error="对不起，余额不足，请使用其他方式支付";	
							break;
						case 2:
							error="密码错误";	
							break;
						case 3:
							error="请输入密码";	
							break;	
						case 4:
							error="订单不存在";	
							break;	
						case 'failure':
							error="支付失败";	
							break;
					}
					$("#tan").html(error);
					tishi();
				}
			}
		}
	});
}

//抢单状态
function get_rob_info()
{
	$.ajax({
		url:"/FrontOrder/getRobInfo",
		type:"POST",
		dataType:"json",
		data:{
			order_id: order_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				console.log(d);
				if (d.code == 0)
				{
					$('.od_grab_num').html(d.pushed_num);
					//alert(d.pushed_num);
					if (d.state != 0)
					{
						clearInterval(timer);
						$('.od_f_info').html(d.realname + '已接单');
						$('.od_f_name').html(d.realname);
						$('#score_avg').html(d.score_avg);
						$('.od_f_tel').attr('href', 'tel:' + d.mobile);
						$('#foot_man_logo').attr('src', d.head_photo);
						$('.od_grab_cont').hide();
						$('.od_footman_cont').show();
location.reload();
					}
				}
			}
		}
	});
}
