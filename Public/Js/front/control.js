
function initialize_btn()
{
	var len = command_arr.length;
	for (var i = 1; i <= len; i++)
	{
		set_btn_css(i, command_arr[i - 1]);
	}
}

function send_command(btn_serial, command)
{
	$.ajax({
		url:"/FrontControl/send_command",
		type:"POST",
		data:{
			btn_serial: btn_serial,
			command: command,
			planter_id: planter_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					set_btn_css(btn_serial, command);
				}
				else
				{
					//alert("网络请求错误，请重试");
				}
			}
		}
	});	
}

function set_spray_time()
{
	$.ajax({
		url:"/FrontControl/set_spray_time",
		type:"POST",
		data:{
			ton: $("input[name=g-time]").val(),
			toff: $("input[name=g-interval]").val()
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
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

function set_btn_css(btn_serial, command)
{
	var clientWidth = document.body.clientWidth;
	switch (btn_serial)
	{
		case 3:
			if (command == 1)
			{
				$("#i6 a").css("color","#19c54f");
				if(clientWidth >=540)
					$("#i6 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -163px -321px no-repeat", "background-size":"450px" });
				else
					$("#i6 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -112px -215px no-repeat", "background-size":"300px" });

			}
			else if (command == 0)
			{
				$("#i6 a").css("color","#707070");
				if(clientWidth >=540)
					$("#i6 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -167px -252px no-repeat", "background-size":"450px" });
				else
					$("#i6 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -114px -169px no-repeat", "background-size":"300px" });

			}
			break;
		case 2:
			if (command == 1)
			{
				$("#i7 a").css("color","#f08519");
				if(clientWidth >=540)
					$("#i7 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -245px -321px no-repeat", "background-size":"450px" });
				else
					$("#i7 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -164px -215px no-repeat", "background-size":"300px" });

			}
			else if (command == 0)
			{
				$("#i7 a").css("color","#707070");
				if(clientWidth >=540)
					$("#i7 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -245px -256px no-repeat", "background-size":"450px" });
				else
					$("#i7 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -164px -172px no-repeat", "background-size":"300px" });

			}
			break;
		case 1:
			if (command == 1)
			{
				$("#i5 a").css("color","#00a2e9");
				if(clientWidth >=540)
					$("#i5 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -90px -318px no-repeat", "background-size":"450px" });
				else
					$("#i5 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -60px -213px no-repeat", "background-size":"300px" });
			}
			else if (command == 0)
			{
				$("#i5 a").css("color","#707070");
				if(clientWidth >=540)
					$("#i5 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -95px -249px no-repeat", "background-size":"450px" });
				else
					$("#i5 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -64px -167px no-repeat", "background-size":"300px" });
			}
			break;
		case 4:
			if (command == 1)
			{
				$("#i8 a").css("color","#e62129");
				if(clientWidth >=540)
					$("#i8 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -324px -317px no-repeat", "background-size":"450px" });
				else
					$("#i8 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -216px -213px no-repeat", "background-size":"300px" });
			}
			else if (command == 0)
			{
				$("#i8 a").css("color","#707070");
				if(clientWidth >=540)
					$("#i8 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -322px -254px no-repeat", "background-size":"450px" });
				else
					$("#i8 i").css({ "background":"url(/Public/Images/front/main_sprite_pad.png) -215px -171px no-repeat", "background-size":"300px" });
			}
			break;
		case 5:
			if (command == 1)
			{
				$("#waterFish").css("background-color","rgb(63, 164, 238)");
				$("#waterFish img").css("opacity","1");
			}
			else if (command == 0)
			{
				$("#waterFish").css("background-color","rgb(151, 156, 159)");
				$("#waterFish img").css("opacity","0.2");
			}
			break;
		default:
			break;
	}
}

$.fn.toggler = function( fn, fn2 )
{
    var args = arguments,guid = fn.guid || $.guid++,i=0,
    toggler = function( event ) {
      var lastToggle = ( $._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
      $._data( this, "lastToggle" + fn.guid, lastToggle + 1 );
      event.preventDefault();
      return args[ lastToggle ].apply( this, arguments ) || false;
    };
    toggler.guid = guid;
    while ( i < args.length ) {
      args[ i++ ].guid = guid;
    }
    return this.click( toggler );
};

$(function()
{
	//灌溉
	var start0 = command_arr[0];
	var end0 = start0 ? 0 : 1;
	$("#i5 a").toggler(
		function(){
			send_command(1, end0);
		},
		function(){
			send_command(1, start0);
		}
	);

	
	//灯光
	var start1 = command_arr[1];
	var end1 = start1 ? 0 : 1;
	$("#i7 a").toggler(
		function(){
			send_command(2, end1);
		},
		function(){
			send_command(2, start1);
		}
	);
	//风扇
	var start2 = command_arr[2];
	var end2 = start2 ? 0 : 1;
	$("#i6 a").toggler(
		function(){
			send_command(3, end2);
		},
		function(){
			send_command(3, start2);
		}
	);
	//加热
	var start3 = command_arr[3];
	var end3 = start3 ? 0 : 1;
	$("#i8 a").toggler(
		function(){
			send_command(4, end3);
		},
		function(){
			send_command(4, start3);
		}
	);
});

function set_state(state)
{
	$.ajax({
		url:"/FrontControl/set_state",
		type:"POST",
		data:{
			state: state,
			planter_id: planter_id,
			seed_id: seed_id,
			planter_seed_id: planter_seed_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
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

//保存diy修改状态结果
function save_state(tem, dam, light, current_seed_state_id)
{
	$.ajax({
		url:"/FrontControl/save_state",
		type:"POST",
		data:{
			tem: tem,
			dam: dam,
			light: light,
			seed_state_id: current_seed_state_id,
			planter_id: planter_id,
			seed_id: seed_id,
			state: current_state,
			is_diy: is_diy,
			planter_seed_id: planter_seed_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					//返回成功，代码写这里
					//link = '/FrontControl/main_home/seed_id/' + seed_id;
					//alert('恭喜您，种植成功！');
					//location.href = link;
					var save_msg="恭喜您，保存成功";
					$("#tan").html(save_msg);
					tishi(); 
				}
				else
				{
					var save_msg="抱歉，保存失败！";
					$("#tan").html(save_msg);
					tishi(); 
				}
			}
		}
	});
}

function plant_new_seed(planter_id, seed_id, seed_state_id, state)
{
	$.ajax({
		url:"/FrontControl/plant_new_seed",
		type:"POST",
		data:{
			planter_id: planter_id,
			seed_id: seed_id,
			seed_state_id: seed_state_id,
			state: state,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					//返回成功，代码写这里
					link = '/FrontControl/main_home/planter_seed_id/' + d;
					alert('恭喜您，种植成功！');
					location.href = link;
				}
				else
				{
				}
			}
		}
	});
}

function getMaxState()
{
	len = seed_state_list.length;
	return seed_state_list[len - 1]['state'];
}

function getNextState(i)
{
	len = seed_state_list.length;
	for (var j = 0; j < len; j++)
	{
		if (seed_state_list[j]['state'] == i)
		{
			return seed_state_list[j + 1]['state'];
		}
	}
}

function getPrevState(i)
{
	len = seed_state_list.length;
	for (var j = 0; j < len; j++)
	{
		if (seed_state_list[j]['state'] == i)
		{
			return seed_state_list[j - 1]['state'];
		}
	}
}

//底部导航切换按钮事件
function btn_click(num)
{
	var numb = num;
	if(numb == 0)
	{
		$("#main_menu_switch").hide();
		$("#main_menu_shop").show();
	}
	else if(numb == 1)
	{
		$("#main_menu_shop").hide();
		$("#main_menu_switch").show();
	} 
}

function set_mode(mode)
{
	$.ajax({
		url:"/FrontControl/set_mode",
		type:"POST",
		data:{
			mode: mode,
			planter_id: planter_id,
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					var obj = $('.mwui-switch-btn');
					var btn = $(obj).find("span");
					var change = btn.attr("change");
					//改变CSS样式
					if(mode == 0) { 
						$(obj).find("input").val("0");
						//$(obj).css("background","#E6E6E6");
						btn.attr("change", btn.html()); 
						btn.html(change);
					}else if (mode == 1){
						$(obj).find("input").val("1");
						//$(obj).css("background","#5FC849");
						btn.attr("change", btn.html()); 
						btn.html(change);
					}  
					btn.toggleClass('off'); 
				}
				else
				{
					//alert("网络请求错误，请重试");
				}
			}
		}
	});	
}
