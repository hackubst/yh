$(function()
{
	var province_id = $('#pre_province_id').val();
	var city_id = $('#pre_city_id').val();
	var area_id = $('#pre_area_id').val();
	if (province_id) {
		//显示城市列表
		change_city(province_id, city_id);
	}
	if (city_id) {
		//显示地区列表
		change_area(city_id, area_id);
	}

	$('#province_id').change(function() {
		province_id = $(this).val();
		change_city(province_id);
	});

	$('#city_id').change(function() {
		city_id = $(this).val();
		change_area(city_id);
	});

	$('#area_id').change(function() {
		area_id = $(this).val();
	});
});

function change_city(province_id, city_id)
{
    $('#area_id').html("<option value='0'>选择区/县</option>");

    $.post('/Area/get_city_list', {
        "province_id": province_id
    }, function(data) {
        if (!data) {
            return false;
        }
        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">选择城市</option>';
        for (i = 0; i < length; i++) {
            str += '<option value="' + result[i]['city_id'] + '">' + result[i]['city_name'] + '</option>';
        }
        $('#city_id').html(str);
        if (city_id) {
            $('#city_id option[value=' + city_id + ']').attr('selected', 'selected');
            $('#c_mark').html($('#city_id').find('option:selected').text()); //更改文本显示
        }

    }, 'json');
}

function change_area(city_id, area_id)
{
    $.post('/Area/get_area_list', {
        "city_id": city_id
    }, function(data) {
        if (data == null) {
            $('#area_id').html('<option value="0">选择区/县</option>');
            $('#areaSelP').css('display', 'none');
            return;
        } else {
            $('#areaSelP').css('display', '');
        }

        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">选择区/县</option>';
        for (i = 0; i < length; i++) {
            str += '<option value="' + result[i]['area_id'] + '">' + result[i]['area_name'] + '</option>';
        }

        $('#area_id').html(str);
        if (area_id)
		{
            $('#area_id option[value=' + area_id + ']').attr('selected', 'selected');
            $('#a_mark').html($('#area_id').find('option:selected').text()); //更改文本显示
        }
    }, 'json');
}
//搜索写字楼表单
function build_search()
{
	var keyword = $('#keyword').val();
	
	if (!keyword)
	{
		$("#tan").html('对不起，请输入搜索内容！');
		tishi();
		$('#keyword').focus();
		return false;
	}
	var main = $("#going_list"); //主体元素  
	
	//保存
	$.post('/FrontAddress/get_building_list', {"keyword":keyword}, function(data, textStatus) 
		 {
			if (data != 'failure')
			{
				$('#going_list').empty();

				if(data == null){
					var html = '<div class="search_error">对不起，未搜到结果！</div>';
					main.append(html);
				}else{
					var len = data.length;
					for (i = 0; i < len; i++)
					{
						//var html = '<div class="keyres_line"><a class="keyres_block" data_id=' + data[i].building_id + '>' + data[i].building_name + '</a></div>';
						var html = '<div class="keyres_line" onclick=\'selectBuilding(' + data[i].building_id + ', "' + data[i].building_name + '");\'><a class="keyres_block">' + data[i].building_name + '</a></div>';
						main.append(html);
					}
					//activeClickEvent();
				}
				
			}else{
				//$('#search_error').show().text('对不起，未搜到结果！');
			}
		}, 'json');
}
//获取最近的写字楼
function get_nearby_build()
{

	var main = $("#going_list"); //主体元素  
	
	//保存
	$.post('/Front/getBuildingList', {"lon":lon,"lat":lat}, function(data, textStatus) 
		 {
			if (data != 'failure')
			{
				$('#going_list').empty();
				var len = data.length;
				for (i = 0; i < len; i++)
				{
					//var html = '<div class="keyres_line" onclick=\'selectBuilding(' + data[i].building_id + ', "' + data[i].building_name + '");\'><a class="keyres_block" data_id=' + data[i].building_id + '>' + data[i].building_name + '</a></div>';
					var html = '<div class="keyres_line" onclick=\'selectBuilding(' + data[i].building_id + ', "' + data[i].building_name + '");\'><a class="keyres_block">' + data[i].building_name + '</a></div>';
					main.append(html);
				}
				//activeClickEvent();
			}
		}, 'json');
}

function selectBuilding(building_id, building_name)
{
	$('#home_search,#screenIfm').hide();
	$('#select_building').text(building_name);
	$('#building_id').val(building_id);
}

function search_build(){
	get_nearby_build();
	$('#home_search,#screenIfm').show();
}

//保存个人资料
function save_user_info()
{
	var realname = $('#realname').val();
	//var mobile = $('#mobile').val();
	var email = $('#email').val();
	var province_id = $('#province_id').val();
	var city_id = $('#city_id').val();
	var area_id_need = $('#areaSelP').css('display');
	area_id_need = area_id_need == 'block' ? true : false;
	var area_id = $('#area_id').val();
    var address = $("#address1").val();
	//var mobile_reg = /^1[34578]\d{9}$/gi;
	var email_reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
	if (!realname)
	{
		$("#tan").html('对不起，请填写姓名！');
		tishi();
		$('#realname').focus();
		return false;
	}
	if (province_id == 0)
	{
		$("#tan").html('对不起，请选择省份！');
		tishi();
		$('#province_id').focus();
		return false;
	}

	if (city_id == 0)
	{
		$("#tan").html('对不起，请选择城市！');
		tishi();
		$('#city_id').focus();
		return false;
	}

	if (area_id_need && area_id == 0)
	{
		$("#tan").html('对不起，请选择区/县！');
		tishi();
		$('#area_id').focus();
		return false;
	}

	//if (email)
	//{
    //    //$("#tan").html('对不起，请填写邮箱！');
    //    //tishi();
    //    //$('#email').focus();
    //    //return false;
    //    if (!email_reg.test(email))
    //    {
    //        $("#tan").html('对不起，请填写正确的邮箱！');
    //        tishi();
    //        $('#email').focus();
    //        return false;
    //    }
	//}
	
	province_id = province_id ? province_id : 0;
	city_id = city_id ? city_id : 0;
	area_id = area_id ? area_id : 0;

    // if (!address)
	//{
	//	$("#tan").html('对不起，请填写联系地址！');
	//	tishi();
	//	$('#address1').focus();
	//	return false;
	//}
    //if (!baby_birthday)
	//{
	//	$("#tan").html('对不起，请填写生日');
	//	tishi();
	//	$('#baby_birthday').focus();
	//	return false;
	//}

    //if (street_id == 0)
	//{
	//	$("#tan").html('对不起，请选择街道！');
	//	tishi();
	//	$('#street_id').focus();
	//	return false;
	//}
	//保存
	$.ajax(
	{
		url:"/FrontUser/save_user_info",
		type:"POST",
		data:{
			realname: realname,
			email: email,
			province_id: province_id,
			city_id: city_id,
			area_id: area_id,
			address:address
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					alert('恭喜您，修改成功！');
					location.href = '/FrontUser/personal_center';
				}
				else
				{
				}
			}
		}
	});
}
function save_address()
{
	//验证表单
	var realname = $('#realname').val();
	var mobile = $('#mobile').val();
	var address = $('#detail_address').val();
	var province_id = $('#province_id').val();
	var city_id = $('#city_id').val();
	var area_id = $('#area_id').val();
  	var mobile_reg = /^1[34578]\d{9}$/gi;
	if (!realname)
	{
		$("#tan").html('对不起，请填写收货人姓名！');
		tishi();
		$('#realname').focus();
		return false;
	}

	if (!mobile)
	{
		$("#tan").html('对不起，请填写手机号码！');
		tishi();
		$('#mobile').focus();
		return false;
	}

	if (!mobile_reg.test(mobile))
	{
		$("#tan").html('对不起，请填写正确的手机号码！');
		tishi();
		$('#mobile').focus();
		return false;
	}

	if (!province_id)
	{
		$("#tan").html('对不起，请选择省份！');
		tishi();
		return false;
	}

	if (!city_id)
	{
		$("#tan").html('对不起，请选择城市！');
		tishi();
		return false;
	}


	if (!address)
	{
		$("#tan").html('对不起，请填写详细地址！');
		tishi();
		$('#address').focus();
		return false;
	}

	//保存
	$.ajax(
	{
		url:"/FrontAddress/save_address",
		type:"POST",
		data:{
			address_id: $('#address_id').val(),
			realname: realname,
			mobile: mobile,
			province_id: province_id,
			city_id: city_id,
			area_id: area_id,
			address: address
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					//返回成功，代码写这里;1添加2编辑
	
						opt = opt == 1 ? '添加' : '修改';
						alert('恭喜您，收货地址' + opt + '成功！','/FrontAddress/address_list?jump='+jump);
						window.location.href = "/FrontAddress/address_list?jump="+jump;
					
				}
				else
				{
						opt = opt == 1 ? '添加' : '修改';

						alert('对不起，收货地址' + opt + '失败！');

				}
			}
		}
	});
}
//start-微信地址管理
//调用收货地址接口
function wx_address()
{
	WeixinJSBridge.invoke(
		'editAddress',
		//{$parameters},
		function(res)
		{
			if(res.err_msg == "edit_address:ok" )
			{
				var realname = res.userName;
				var mobile = res.telNumber;
				var area_string = res.proviceFirstStageName + res.addressCitySecondStageName + res.addressCountiesThirdStageName;
				var address = res.addressDetailInfo;
				var area_id = res.nationalCode;

				save_wx_address(realname, mobile, area_id, address, area_string);
			}
		}
	);
}

function calladdr()
{
	if (typeof WeixinJSBridge == "undefined"){
		if( document.addEventListener ){
			document.addEventListener('WeixinJSBridgeReady', wx_address, false);
		}else if (document.attachEvent){
			document.attachEvent('WeixinJSBridgeReady', wx_address); 
			document.attachEvent('onWeixinJSBridgeReady', wx_address);
		}
	}else{
		wx_address();
	}
}
	
function save_wx_address(realname, mobile, area_id, address, area_string)
{
    //保存
    $.ajax(
    {   
        url:"/FrontAddress/save_wx_address",
        type:"POST",
        dataType:"json",
        data:{
            realname: realname,
            mobile: mobile,
            area_id: area_id,
            address: address,
            area_string: area_string,
        },  
        timeout:10000,
        success:function(d){
            if(d) {
                if(d != 'failure')
                {   
			//将当前地址信息赋值到地址管理页
			var province_id = d.province_id;
			var city_id = d.city_id;
			var area_id = d.area_id;
			area_string = area_string + ' ' + address;
			//先添加一条新的地址数据到地址管理页
			//var html = '<div class="address" id="address' + d + '">';
			//html += $('#editAddBtn').html();
			//html += '</div>';
			var html =	'<div class="address" id="address' + d.user_address_id + '"> <ul> <input type="hidden" id="user_address_id" value="' + d.user_address_id + '"> <input type="hidden" id="pre_province_id" value="' + province_id + '"> <input type="hidden" id="pre_city_id" value="' + city_id + '"> <input type="hidden" id="pre_area_id" value="' + area_id + '"> <input type="hidden" id="pre_address" value="' + address + '"> <li id="addr_detail"> ' + area_string + ' </li> <li> <strong id="pre_realname">' + realname + '</strong> <label id="pre_mobile">' + mobile + '</label> </li> <li class="edit"> <a href="javascript:;" class="edit_link"> 编辑 </a> </li> </ul> </div>';
			$('#address_id').val(d.user_address_id);
			$('.address_list').append(html);
			$(".address ul").click(function()
			{
				$(this).addClass("selected").parent().siblings().find("ul").removeClass("selected");
				var temp = $(this).html();
				$("#editAddBtn").html(temp);
			});

			//若订单信息页选择的地址为当前地址，更新之
			var cur_address_id = $('#editAddBtn').find('#user_address_id').val();
			if (cur_address_id == $('#address_id').val())
			{
				$('#editAddBtn').html($(obj).html());
			}
			$("#edit_addr_page").hide();
			$("#addr_page").show();

                }   
                else
                {   
                }   
            }   
        }   
    }); 
}
//end-微信地址管理
//计算运费
function cal_express_fee(city_id, total_weight, total_amount)
{
	//提交订单
	$.ajax({
		url:"/FrontOrder/cal_express_fee",
		type:"POST",
		dataType:"json",
		data:{
			city_id: city_id,
			total_weight: total_weight,
			total_amount: total_amount,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					//将城市、运费信息更新到订单信息页
					$('#city').html(d.city);
					$('#express_fee').html(d.express_fee);
				}
				else
				{
				}
			}
		}
	});
}
//意见提交
function ToFeedback()
{
    //验证表单
	var message = $('#suggest_msg').val();
    if (!message)
		{
			$("#tan").html('请填写您的建议');
			tishi();
			$('#message').focus();
			return false;
		}
	
	//保存
	$.ajax(
	{
		url:"/FrontUser/save_suggest_info",
		type:"POST",
		data:{
			message: message,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					$("#tan").html('恭喜您，信息提交成功');
					tishi();
				}
				else
				{
					$("#tan").html('抱歉，信息提交失败');
					tishi();
				}
			}
		}
	});
}

function get_lon_lat() {
	var geolocation = new BMap.Geolocation();
	var gc = new BMap.Geocoder();
	var loationFlag = 0;
	geolocation.getCurrentPosition(function(r) {
		if (this.getStatus() == BMAP_STATUS_SUCCESS){
			var pt = r.point;
			var message = "";
			gc.getLocation(pt,function(rs){
				var addComp = rs.addressComponents;
				/* console.log(pt.lng);
				   console.log(pt.lat);
				   console.log(rs);*/
//alert(pt.lng + ', ' + pt.lat);
				lon = pt.lng;
				lat = pt.lat;
			});
		}
	},{enableHighAccuracy:true});
}
