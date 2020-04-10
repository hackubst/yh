/*
 **
 **
 */
$(function() {
    /*
     **页面刚加载需要的参数
     */
	 
    //判断顶级菜单个数，大于等于4，添加按钮隐藏
    var mgnavlen = $("#sortable li").length;
    if (mgnavlen < 4) {
        $(".alipay-nav .alipay-nav-add").show();
    } else {
        $(".alipay-nav .alipay-nav-add").hide();
    }
	//页面刚开始加载，生成菜单
	Refresh();
	
    //获取当前显示的二级菜单的ID，赋值给隐藏域
    var firstid = $(".alipay-meun .alipay-meun-c:first").attr("id");
    var firstdata = $(".alipay-meun .alipay-meun-c:first").attr("data");
    $("#alipay-meun-id").val(firstid);
    $("#alipay-meun-id").attr("data", firstdata);
	
	
	$(".alipay-meun .alipay-meun-c:first .alipay-meun-type a").each(function() {
        if( $(this).attr("class") == "hover")
		{$("#alipay-meun-id").attr("radiodata",$(this).attr("data"));}
    });

    /*
     **
     */
	//移动顶级菜单排序
	$("#sortable").sortable({revert: true,axis: 'x',handle: '.move',stop: function() {Refresh();}});
	$("#sortable").sortable( 'disable');
	//移动二级菜单排序
	$(".ali-meun-c ul").sortable({
		revert: true,axis: 'y',
		sort: function(event,ui){
			$(".ali-meun-c").css("border-color","#000");
			$(ui.item).css("border-color","#7a94b2");
		},
		stop: function() {
			$(".ali-meun-c").css("border-color","#fff");
			$(".ali-meun-c ul li").css("border-color","#fff");
			Refresh();
		}
	});
    $(".ali-meun-c ul").sortable( 'disable');
    
    //添加一级菜单
	var alldata=5,erjiid=1;
    $("#alipay-nav-edit").click(function(){
		var yistr='<li data="'+alldata+'" class="hover j-alnav"><span class="ali-nav-t j-alltext" style="display:none;">新建菜单</span><span class="ali-nav-i j-alledit" style="display:block;"><input type="text" onkeyup="CheckLength(this,12)" value="新建菜单" obj_name="" name="ali-nav-yiji" /></span><i class="move"></i><i class="del"></i></li>';

		if($("#alipay-button").find("span.edit").css("display") == "none")
		{
			show_globalPop("菜单在编辑状态下，请点击“完成”后再添加菜单！");
		}
		else{
			$("#sortable li").removeClass("hover");
			$("#alipay-meun .j-menudata").css("display","none");
			
			$("#sortable .alipay-nav-add").before(yistr);
			
			$.ajax({
				type:'POST',
				url:"/McpPlatForm/js_get_first",
				data:{id:alldata},
				beforeSend: function(){show_pagemask();},
				complete: function(){hidden_pagemask();},
				dataType:"json",
				success: function(data){var ejstr=data.data;$("#alipay-meun").append(ejstr);}
			});
			$("#alipay-meun-"+alldata+"").css("display","block");
			$("#alipay-meun-id").val("alipay-meun-"+alldata+"").attr("data", alldata);
			
			$("#alipay-button").find("span.edit").css("display","none");
			$("#alipay-button").find("span.fulfil").css("display","block");
			
			var len = $("#sortable li").length;
			if (len >= 3) {
				$("#alipay-nav-edit").hide();
			}
			
			$("#alipay-meun-id").attr("radiodata","have");
			
			alldata++;
			Refresh();
		}
    });
    //删除一级菜单
	$("#sortable li i.del").live("click",function(){
		var thisdata=$(this).parents(".j-alnav").attr("data");
		var thisindex=$(this).parents(".j-alnav").index();
		
		var hidata=$("#alipay-meun-id").attr("data");
		if(thisdata == hidata)
		{
			if(confirm("当前菜单为编辑状态，是否确定要删除？"))
			{
				//移除顶级菜单
				$(this).parents(".j-alnav").remove();
				//移除对应二级菜单
				$(".alipay-meun .alipay-meun-c").each(function() {
					var sdata = $(this).attr("data");
					if (sdata == thisdata) {$(this).remove();}
				});
				//移除当前，上一个显示
				if(thisindex!=0){
					$("#sortable li").removeClass("hover").eq(thisindex-1).addClass("hover");
					var ondata=$("#sortable li").eq(thisindex-1).attr("data");
		
					$(".alipay-meun .j-menudata").each(function() {
						var sdata = $(this).attr("data");
						if (sdata == ondata) {
							$(this).show();
							var onid=$(this).attr("id");
							$("#alipay-meun-id").val(onid).attr("data", ondata);
						} else {
							$(this).hide();
						}
					});
				}else{
					$("#sortable li").removeClass("hover").first().addClass("hover");
					var nextdata=$("#sortable li:first").attr("data");
					
					$(".alipay-meun .j-menudata").each(function() {
						var sdata = $(this).attr("data");
						var nextid=$(this).attr("id");
						if (sdata == nextdata) {
							$(this).show();
							$("#alipay-meun-id").val(nextid).attr("data", nextdata);
						} else {
							$(this).hide();
						}
					});
				}
				//计算顶级菜单个数
				var len = $("#sortable li").length;
				if (len < 3) {
					$(".alipay-nav .alipay-nav-add").show();
				}
				$("#alipay-button span.edit").show();
				$("#alipay-button span.fulfil").hide();
			}
		}

		//保存到数据库
		var getdata=getMenuJson();
		var josndata=JSON.stringify(getdata);
		$.ajax({
			type:'POST',
			url:"/AcpConfig/save_platform",
			data:{platform_info:josndata},
			dataType:"json",
			beforeSend: function(){
				show_pagemask();
			},
			complete: function(){
				hidden_pagemask();
			},
			success: function(data){
				show_globalPop("菜单删除成功！");
			}
		});

		Refresh();
	});
    //添加二级菜单
    $(".ali-navmeun-add a").live("click", function() {
		var dqid=$("#alipay-meun-id").val();
		var dqlilen=$("#"+dqid+"").find(".ali-meun-c ul li").length;

        // 删除无子级菜单
        $(this).parents(".alipay-meun-c").find(".alipay-meun-no").find(".text").html('')
        $(this).parents(".alipay-meun-c").find(".alipay-meun-no").find(".ali-meun-lihidden").val("")
        $(this).parents(".alipay-meun-c").find(".ali-meun-c-xsnr-text").html('');
		if(dqlilen >= 4)
		{
			$.ajax({
				type:'POST',
				url:"/AcpConfig/js_get_tow",
				data:{id:erjiid},
				dataType:"json",
				beforeSend: function(){
					show_pagemask();
				},
				complete: function(){
					hidden_pagemask();
				},
				success: function(data){
                    console.log(data)
					// if(data.code == "200"){
					// 	$("#"+dqid+"").find(".ali-meun-c ul").append(data.data);
					// }
				}
			});
			$("#"+dqid+"").find(".ali-navmeun-add").hide();
		}
		else{
			 $.ajax({
				type:'POST',
				url:"/AcpConfig/js_get_tow",
				data:{id:erjiid},
				dataType:"json",
				beforeSend: function(){
					show_pagemask();
				},
				complete: function(){
					hidden_pagemask();
				},
				success: function(data){
                    console.log(data);
					if(data.code == "200"){
						$("#"+dqid+"").find(".ali-meun-c ul").append(data.data);
					}
				}
			});
		}
		
		$("#"+dqid+"").find(".j-alltext").css("display", "none");
        $("#"+dqid+"").find(".j-alledit").css("display", "block");
		
		$("#"+dqid+"").find(".ali-meun-c ul").sortable({
			revert: true,
			axis: 'y',
			sort: function(event,ui){
				$(".ali-meun-c").css("border-color","#000");
				$(ui.item).css("border-color","#7a94b2");
			},
			stop: function() {
				$(".ali-meun-c").css("border-color","#fff");
				$(".ali-meun-c ul li").css("border-color","#fff");
			}
		});
		Refresh();
		erjiid++;
    });
    //删除二级菜单
	$(".alipay-meun-c .ali-meun-c-icon i.del").live("click",function(){
		if(confirm("确定要删除当前二级菜单吗？"))
        var i_meun=$(this).parents(".alipay-meun-c")
        var li_len = $(this).parents(".ui-sortable").find(".j-nav-erji").length;
		{$(this).parents(".j-nav-erji").remove();}

        // 增加有子级菜单
        if (li_len == 1) {
            var html='<input type="text" onchange="change_value(this);" class="text" name="val" value="">'
            $(i_meun).find(".alipay-meun-no").find(".text").html(html)
        }
		Refresh();

	});
	//顶级菜单点击，显示相应的二级菜单
    $("#sortable li span.ali-nav-t").live("click", function() {
        var editdis = $("#alipay-button span.edit").css("display");
        if (editdis == "block") {
            $("#sortable li").removeClass("hover");
            $(this).parent().addClass("hover");
            var fdata = $(this).parent().attr("data");
			
			//判断有无二级菜单
			
			
            $(".alipay-meun .alipay-meun-c").each(function() {
                var sdata = $(this).attr("data");
                var thisid = $(this).attr("id");
				
				var aaa=$(this).find(".alipay-meun-no").css("display");
				
                if (sdata == fdata) {
                    $(this).show();
					if(aaa == "none")
					{
						//$("input[name=ali-mty-r]:eq(1)").attr("checked",'checked');
						//$("input[name=ali-mty-r]:eq(0)").removeAttr("checked");
						$("#alipay-meun-id").attr("radiodata","have");
					}
					else{
						//$("input[name=ali-mty-r]:eq(1)").removeAttr("checked");
						//$("input[name=ali-mty-r]:eq(0)").attr("checked",'checked');
						$("#alipay-meun-id").attr("radiodata","no");
					}
                    $("#alipay-meun-id").val(thisid).attr("data", fdata);
										
                } 
				else {
                    $(this).hide();
                }
            });
        }
    })
    //鼠标经过顶级菜单，显示工具条
    $("#sortable li").live("hover",function() {
		var edit=$("#alipay-button .edit").css("display");
		if(edit == "none"){$(this).find("i").css("display","block");}
    });
	$("#sortable li").live("mouseleave",function() {
        $(this).find("i").css("display","none");
    });
	//鼠标经过二级菜单，显示工具条
	$(".ali-meun-c ul li").live("hover",function(){
		var edit=$("#alipay-button .edit").css("display");
		if(edit == "none")
		{$(this).find(".ali-meun-c-icon i").css("display","inline-block");}
	});
	$(".ali-meun-c ul li").live("mouseleave",function() {
        $(this).find(".ali-meun-c-icon i").css("display","none");
    });
    //切换有无二级菜单
    $('.alipay-meun-c .alipay-meun-type a').live("click",function() {
        var radiodata = $(this).attr("data");
        var thisid = $("#alipay-meun-id").val();
		$(this).parent().find("a").removeClass("hover");
		$(this).addClass("hover");

        if (radiodata == "no") {
            $("#" + thisid + "").find(".alipay-meun-no").show();
            $("#" + thisid + "").find(".alipay-meun-have").hide();
			$("#alipay-meun-id").attr("radiodata","no");
        } else {
            $("#" + thisid + "").find(".alipay-meun-no").hide();
            $("#" + thisid + "").find(".alipay-meun-have").show();
			$("#alipay-meun-id").attr("radiodata","have");
        }
    });
    //编辑
    $("#alipay-button span.edit").click(function() {
        var thisid = $("#alipay-meun-id").val();
        var thisdata = $("#alipay-meun-id").attr("data");
        var thislilen = $("#" + thisid + "").find(".ali-meun-c ul li").length;
        //二级菜单
        $("#" + thisid + "").find(".j-alltext").hide();
        $("#" + thisid + "").find(".j-alledit").show();
        if (thislilen >= 5) {
            $("#" + thisid + "").find(".ali-navmeun-add").hide();
        } else {
            $("#" + thisid + "").find(".ali-navmeun-add").show();
        }
        //一级菜单
        $("#sortable li").each(function() {
            var data = $(this).attr("data")
            if (thisdata == data) {
                $(this).find(".j-alltext").css("display", "none");
                $(this).find(".j-alledit").css("display", "block");
            }
        });
		$(this).hide();
        $("#alipay-button span.fulfil").show();
		
		//启用一级菜单排序
		$("#sortable").sortable( 'enable');
		//启用二级菜单排序
		$(".ali-meun-c ul").sortable( 'enable');
    });
    //一级菜单修改
    $('input:text[name="ali-nav-yiji"]').live("blur", function() {
        var thisval = $(this).val();
        var parsid = $(this).parents(".j-alnav").find(".j-alltext")
        parsid.html(thisval);
    });
	//二级菜单名修改
	$('input:text[name="ali-meun-name-erji"]').live("blur", function() {
        var thisval = $(this).val();
        $(this).parents(".j-meunparent").find(".j-alltext").html(thisval);
		$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").attr("ejname",thisval);
    });
    //选择是WAP还是电话号码还是图文消息
    $(".ali-m-se-type").live("change", function() {
        var parid = $(this).parents(".j-meunparent").find(".j-alltext");
        var parsid = $(this).parents(".j-nav-erji").find(".ali-meun-c-xsnr .ali-meun-c-xsnr-edit");
        var thisval = $(this).val();
		var thistext=$(this).find("option:selected").text();
		parid.html(thistext);
		$(this).parent().find("span").html(thistext);
		
		$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").attr("ejtype",thisval);
		
        if (thisval == "view") {
            parsid.find(".textp-wap").show();
            parsid.find(".textp-tel,.textp-auto,.textp-custom").hide();
			
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.replace").show();
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.unbind").hide();
			
        } else if (thisval == "media_id") {
            parsid.find(".textp-tel").show();
            parsid.find(".textp-wap,.textp-auto,.textp-custom").hide();
			
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.replace").hide();
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.unbind").show();
        } else if (thisval == "click") {
            parsid.find(".textp-auto").show();
            parsid.find(".textp-tel,.textp-wap,.textp-custom").hide();
			
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.replace").hide();
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.unbind").show();
        } else if (thisval == "4") {
            parsid.find(".textp-custom").show();
            parsid.find(".textp-tel,.textp-wap,.textp-auto").hide();
			
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.replace").hide();
			$(this).parents(".j-nav-erji").find(".ali-meun-c-tem span.unbind").show();
        }
    });
    //选择WAP网址
    $(".ali-meun-c-xsnr-edit .textp-wap span.replace").live("click", function() {
		var thisval=$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").val();
			bojname=$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").attr("ejobjname");
		
		$.ajax({
			type:'POST',
			url:"/McpPlatForm/Get_user_model",
			dataType:"json",
			beforeSend: function(){
				show_pagemask();
			},
			complete: function(){
				hidden_pagemask();
			},
			success: function(data){
				if(data.code == "200"){
					$("#mg-linkshow").find(".mg-plateshow-c .waplink").html("").append(data.data);
					
					if(bojname == "FrontCustUrlAction")
					{
						$("#frontcusturl").parent().find("input").val(thisval);
					}
					else
					{
						$(".waplink .waplk .waplk_meun a").each(function() {
							var thisobjname=$(this).attr("obj_name");
							var thiskey=$(this).attr("key");
	
							if(bojname == thisobjname && thiskey == thisval)
							{
									$(this).addClass("hover");
							}
						});
					}
					
					
				}
			}
		});
		
		//弹出层设置
		var thiswidth=$(document.body).width();
        var thisheight=$(document.body).height();
        $("#mg-plateshow-bg").css({height:""+thisheight+"",width:""+thiswidth+""}).show();
        $("#mg-linkshow").css("left",""+(thiswidth-1230)/2+"px").show();
		//设置当前二级li的ID
		var fID=$(this).parents(".j-nav-erji").attr("id");
		$("#alipay-meun-id").attr("erjiid",fID);
		//获取当前tabID
		var thistabid=$(this).parents(".j-alimenutab").attr("id");
		$("#alipay-meun-id").attr("tabid",thistabid);
		
    });
	//选择链接
	$(".waplink .waplk .waplk_meun a").live("click",function(){
		//选中效果
		$(".waplink .waplk .waplk_meun a").removeClass("hover");
		$(this).addClass("hover");
		//获取选中链接值
		var thisid=$(this).attr("cid");
		//var thisname=$(this).attr("name");
		var thispagename=$(this).attr("page_name");
		var thiskey=$(this).attr("key");
		var thisobjname=$(this).attr("obj_name");
		
		
		//获取当前操作的二级菜单ID 
		var thistabid=$("#alipay-meun-id").attr("tabid");
		var thiserjiid=$("#alipay-meun-id").attr("erjiid");
		//判断有无子菜单
		var thisdata=$("#alipay-meun-id").attr("radiodata");
		if(thisdata == "have")
		{
			//修改显示值
			$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-xsnr .j-alltext').html(thiskey);
			$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-xsnr .ali-meun-c-xsnr-edit .textp-wap span.text').html(thiskey);
			//赋值隐藏域
			$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejpagename",thispagename);
			$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejobjname",thisobjname);
			$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').val(thiskey);
			
			$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-tem span.replace').show();
			$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-tem span.unbind').hide();
		}
		else{ 
			//修改显示值
			$("#"+thistabid+"").find('.ali-meun-c-xsnr .j-alltext').html(thiskey);
			$("#"+thistabid+"").find('.ali-meun-c-xsnr .ali-meun-c-xsnr-edit .textp-wap span.text').html(thiskey);
			//赋值隐藏域
			$("#"+thistabid+"").find('input.ali-meun-lihidden').attr("ejpagename",thispagename);
			$("#"+thistabid+"").find('input.ali-meun-lihidden').attr("ejobjname",thisobjname);
			$("#"+thistabid+"").find('input.ali-meun-lihidden').val(thiskey);
			
			$("#"+thistabid+"").find('.ali-meun-c-tem span.replace').show();
			$("#"+thistabid+"").find('.ali-meun-c-tem span.unbind').hide();
		}
		//请求当前默认模版
		$.ajax({
			type:'POST',
			url:"/McpPlatForm/get_cur_page_template",
			data:{page_name:thispagename},
			dataType:"json",
			beforeSend: function(){
				show_pagemask();
			},
			complete: function(){
				hidden_pagemask();
			},
			success: function(data){
				if(data)
				{
					if(thisdata == "have")
					{
						$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-tem span.text').html(data.template_name);
						$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejplatid",data.template_id);
					}
					else{
						$("#"+thistabid+"").find('.ali-meun-c-tem span.text').html(data.template_name);
						$("#"+thistabid+"").find('input.ali-meun-lihidden').attr("ejplatid",data.template_id);
					}
				}
			}
		});
	});
	//自定义URL
	$("#frontcusturl").live("click",function(){
		var thisval=$(this).parent().find("input").val();
		var thisobjname=$(this).parent().find("input").attr("obj_name");
		var str=thisval.replace(/[ ]/g,"");		
		if(str.indexOf("http://") >=0)
		{
			$(".waplink .waplk .waplk_meun a").removeClass("hover");
		
			//获取当前操作的二级菜单ID 
			var thistabid=$("#alipay-meun-id").attr("tabid");
			var thiserjiid=$("#alipay-meun-id").attr("erjiid");
			//判断有无子菜单
			var thisdata=$("#alipay-meun-id").attr("radiodata");
			if(thisdata == "have")
			{
				//修改显示值
				$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-xsnr .j-alltext').html(thisval);
				$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-xsnr .ali-meun-c-xsnr-edit .textp-wap span.text').html(thisval);
				
				$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').val(thisval);
				$("#"+thistabid+"").find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejobjname",thisobjname);
				
				$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-tem span.replace').hide();
				$("#"+thistabid+"").find('#'+thiserjiid+' .ali-meun-c-tem span.unbind').show();
				showclose();
			}
			else
			{
				//修改显示值
				$("#"+thistabid+"").find('.ali-meun-c-xsnr .j-alltext').html(thisval);
				$("#"+thistabid+"").find('.ali-meun-c-xsnr .ali-meun-c-xsnr-edit .textp-wap span.text').html(thisval);
				
				$("#"+thistabid+"").find('input.ali-meun-lihidden').val(thisval);
				$("#"+thistabid+"").find('input.ali-meun-lihidden').attr("ejobjname",thisobjname);
				
				$("#"+thistabid+"").find('.ali-meun-c-tem span.replace').hide();
				$("#"+thistabid+"").find('.ali-meun-c-tem span.unbind').show();
				showclose();
			}
		}
		else
		{
			show_globalPop("自定义链接格式错误，请按正确格式书写！");
			var thisval=$(this).parent().find("input").val("");
		}
		
	});
	
    //选择电话号码
    $('input:text[name="ali-m-input-tel"]').live("blur", function() {
        var thisval = $(this).val();
		var str=thisval.replace(/[ ]/g,"");	
		var Regular=/^(1[3,5,8,7]{1}[\d]{9})|(((400)-(\d{3})-(\d{4}))|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{3,7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)$/;
		
		if(Regular.test(str))
		{
			 var parsid = $(this).parents(".j-meunparent").find(".j-alltext");
			$(this).parents(".j-nav-erji").find('input.ali-meun-lihidden').val(thisval);
			parsid.html(thisval);
		}
		else{
			show_globalPop("格式错误，请按正确格式书写！例如：电话：0571-8888888；手机：13688888888；400：400-123456");
		}
    });
	
    //选择图文信息
    $(".ali-meun-c-xsnr-edit .textp-auto span.replace").live("click", function() {
		//弹出层设置
		var thiswidth=$(document.body).width();
        var thisheight=$(document.body).height();
        $("#mg-plateshow-bg").css({height:""+thisheight+"",width:""+thiswidth+""}).show();
        $("#mg-graphicshow").css("left",""+(thiswidth-1230)/2+"px").show();
		
		//获取当前操作的二级菜单ID 
		var thistabid=$(this).parents(".j-alimenutab").attr("id");
		$("#alipay-meun-id").attr("tabid",thistabid);
		
		var thiserjiid=$(this).parents(".j-nav-erji").attr("id");
		$("#alipay-meun-id").attr("erjiid",thiserjiid);

		//选中图文信息
		//var iframedom=$(window.frames["iframgraphic"].document).find(".column .column_item");
		var iframedom=$("#iframgraphic").contents().find(".column .column_item");
		iframedom.click(function(){
			var thisid=$(this).attr("cid");
			//var thisedit=$(this).attr("edit");
			var thistitle=$(this).find(".msg_title h4").html();
			
			var tabid=$("#alipay-meun-id").attr("tabid");
			var erjiid=$("#alipay-meun-id").attr("erjiid");
			var thisdata=$("#alipay-meun-id").attr("radiodata");
			if(thisdata == "have")
			{
				$('#'+tabid+'').find('#'+thiserjiid+' .ali-meun-c-xsnr .textp-auto span.text').html(thistitle);
				$('#'+tabid+'').find('#'+thiserjiid+' .ali-meun-c-xsnr .ali-meun-c-xsnr-text').html(thistitle);
				//$('#'+tabid+'').find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejedit",thisedit);//快速维护标签赋值
				$('#'+tabid+'').find('#'+thiserjiid+' input.ali-meun-lihidden').val(thisid);
			}
			else{
				$('#'+tabid+'').find(".ali-meun-c-xsnr .textp-auto span.text").html(thistitle);
				$('#'+tabid+'').find(".ali-meun-c-xsnr .ali-meun-c-xsnr-text").html(thistitle);
				//$('#'+tabid+'').find("input.ali-meun-lihidden").attr("ejedit",thisedit);//快速维护标签赋值
				$('#'+tabid+'').find("input.ali-meun-lihidden").val(thisid);
			}
			
			showclose();
		});
    });	
	//选择自定义页面
	$(".ali-meun-c-xsnr-edit .textp-custom span.replace").live("click", function() {
		//弹出层设置
		var thiswidth=$(document.body).width();
        var thisheight=$(document.body).height();
        $("#mg-plateshow-bg").css({height:""+thisheight+"",width:""+thiswidth+""}).show();
        $("#mg-customshow").css("left",""+(thiswidth-1230)/2+"px").show();
		//获取当前操作的二级菜单ID 
		var thistabid=$(this).parents(".j-alimenutab").attr("id");
		$("#alipay-meun-id").attr("tabid",thistabid);
		
		var thiserjiid=$(this).parents(".j-nav-erji").attr("id");
		$("#alipay-meun-id").attr("erjiid",thiserjiid);
		
		//选中择自定义页面信息
		//var iframedom=$(window.frames["iframcustom"].document).find(".appmsg");
		var iframedom=$("#iframcustom").contents().find(".appmsg");
		iframedom.click(function(){
			var thisid=$(this).find(".appmsg_content").attr("cid");
			var thisurl=$(this).find(".appmsg_content").attr("curl");
			//var thisedit=$(this).find(".appmsg_content").attr("edit");
			var thistitle=$(this).find(".appmsg_content .appmsg_title a").html();
			
			var tabid=$("#alipay-meun-id").attr("tabid");
			var erjiid=$("#alipay-meun-id").attr("erjiid");
			var thisdata=$("#alipay-meun-id").attr("radiodata");
			if(thisdata == "have")
			{
				$('#'+tabid+'').find('#'+thiserjiid+' .ali-meun-c-xsnr .textp-custom span.text').html(thistitle);
				$('#'+tabid+'').find('#'+thiserjiid+' .ali-meun-c-xsnr .ali-meun-c-xsnr-text').html(thistitle);
				$('#'+tabid+'').find('#'+thiserjiid+' input.ali-meun-lihidden').val(thisurl);
				//$('#'+tabid+'').find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejedit",thisedit);//快速维护标签赋值
				$('#'+tabid+'').find('#'+thiserjiid+' input.ali-meun-lihidden').attr("ejdataid",thisid);
			}
			else{
				$('#'+tabid+'').find(".ali-meun-c-xsnr .textp-custom span.text").html(thistitle);
				$('#'+tabid+'').find(".ali-meun-c-xsnr .ali-meun-c-xsnr-text").html(thistitle);
				$('#'+tabid+'').find("input.ali-meun-lihidden").val(thisurl);
				//$('#'+tabid+'').find("input.ali-meun-lihidden").attr("ejedit",thisedit);//快速维护标签赋值
				$('#'+tabid+'').find("input.ali-meun-lihidden").attr("ejdataid",thisid);
			}
			showclose();
		});
    });
	
    //完成--取消
    $("#alipay-button span.fulfil").live("click",function() {
        var thisid = $("#alipay-meun-id").val();
        var thisdata = $("#alipay-meun-id").attr("data");
        
       
		var erji="";
		
		$('#'+thisid+'').find(".alipay-meun-have .ali-meun-c ul li").each(function() {
			var erjival=$(this).find(".ali-meun-c-name .j-alltext").html();
			var erjistr=erjival.replace(/[ ]/g,"");	
			if(erjistr == "")
			{erji = "false";return false;}
			else{erji = "true";}
		});
		
        $("#sortable li").each(function() {
            var data = $(this).attr("data")
            if (thisdata == data) {
				var thisval=$(this).find(".j-alltext").html();
				var str=thisval.replace(/[ ]/g,"");	

				if(str == "" || erji == "false")
				{
					show_globalPop("菜单名不能为空！");
				}
				else
				{
					 //一级菜单
					$(this).find(".j-alltext").css("display", "block");
					$(this).find(".j-alledit").css("display", "none");
					//二级菜单
					$("#" + thisid + "").find(".j-alledit").hide();
					$("#" + thisid + "").find(".j-alltext").show();
					$("#" + thisid + "").find(".ali-navmeun-add").hide();
					//保存到数据库
					var getdata=getMenuJson();
					var josndata=JSON.stringify(getdata);
					$.ajax({
						type:'POST',
						url:"/AcpConfig/save_platform",
						data:{platform_info:josndata},
						dataType:"json",
						beforeSend: function(){
							show_pagemask();
						},
						complete: function(){
							hidden_pagemask();
						},
						success: function(data){
							show_globalPop(data.msg);
						}
					});
			
					//“编辑”按钮显示，“完成”按钮隐藏
					$("#alipay-button span.edit").show();
					$("#alipay-button span.fulfil").hide();
					
					Refresh();
					//禁用一级菜单排序
					$("#sortable").sortable( 'disable');
					//禁用二级菜单排序
					$(".ali-meun-c ul").sortable( 'disable');
				}
            }
        });
    });
    //选择模板-模版弹出层
    $(".ali-meun-c-tem span.replace").live("click",function(){
		//获取当前二级菜单ID，赋值给隐藏域
		var thiserji=$(this).parents(".j-nav-erji").attr("id");
		$("#alipay-meun-id").attr("erjiid",thiserji);
		//获取当前tabID
		var thistabid=$(this).parents(".j-alimenutab").attr("id");
		$("#alipay-meun-id").attr("tabid",thistabid);
		
		var thisplanid=$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").attr("ejplatid");
		
		var thistype=$(this).parents(".j-nav-erji").find("input.ali-meun-lihidden").attr("ejpagename");//获取当前页面类型
		
		$.ajax({
			type:'POST',
			url:"/McpPlatForm/Get_page_template",
			data:{page_name:thistype},
			dataType:"json",
			beforeSend: function(){
				show_pagemask();
			},
			complete: function(){
				hidden_pagemask();
			},
			success: function(data){
				if(data){
					var datajosn={},child=[];
					datajosn=data;
					var html="",len = datajosn.length;		
					for (var i = 0; i < len; i++) {
						var thisid=datajosn[i].id;
						if( thisid == thisplanid)
						{
							html+='<li id="'+datajosn[i].id+'" class="hover" modelid="'+datajosn[i].model_page_id+'" name="'+ datajosn[i].name +'"><div class="mg-pla-c"><div class="mg-pla-cc"><div class="mg-pla-ccimg"><img src="' + datajosn[i].path_img + '" width="196" height="300"/></div><div class="mg-pla-cctitlte">'+ datajosn[i].name +'</div></div></div><div class="mg-pla-b">已启用</div></li>';
						}
						else
						{
							html+='<li id="'+datajosn[i].id+'" modelid="'+datajosn[i].model_page_id+'" name="'+ datajosn[i].name +'"><div class="mg-pla-c"><div class="mg-pla-cc"><div class="mg-pla-ccimg"><img src="' + datajosn[i].path_img + '" width="196" height="300"/></div><div class="mg-pla-cctitlte">'+ datajosn[i].name +'</div></div></div><div class="mg-pla-b">未启用</div></li>';
						}
						
					}
					$("#mg-plateshow").find(".mg-plateshow-c .mg-plateshow-l ul").html("").append(html);
				}
			}
		});
		
		//弹出弹出层
        var thiswidth=$(document.body).width();
        var thisheight=$(document.body).height();
        $("#mg-plateshow-bg").css({height:""+thisheight+"",width:""+thiswidth+""}).show();
        $("#mg-plateshow").css("left",""+(thiswidth-1220)/2+"px").show();

    })
	//选中模版
	$(".mg-plateshow-l ul.mg-plateshow-ul li").live("click",function(){
        $(".mg-plateshow-l ul li").removeClass("hover");
        $(this).addClass("hover");
		var thisid=$(this).attr("id");//获取当前模版ID
		var thismobpage=$(this).attr("modelid");//获取当前模版页面
		var thisname=$(this).attr("name");//获取当前模版名称
		//获取当前二级菜单ID
		var dqejid=$("#alipay-meun-id").attr("erjiid");
		var tabid=$("#alipay-meun-id").attr("tabid");
		//判断有无子菜单
		var thisdata=$("#alipay-meun-id").attr("radiodata");
		
		//ajax更新到服务器
		$.ajax({
			type:'POST',
			url:"/McpPlatForm/save_page_template",
			data:{id:thisid,model_page_id:thismobpage},
			dataType:"json",
			beforeSend: function(){
				show_pagemask();
			},
			complete: function(){
				hidden_pagemask();
			},
			success: function(data){
				show_globalPop(data.msg);
				$('#'+dqejid+'').find(".ali-meun-c-tem span.text").html(thisname);
				$("#mg-platehid-id").attr("modelid","").val("");
				$("#mg-platehid-id").attr("platename","");
			}
		});
		
		if(thisdata == "have"){
			$("#"+tabid+"").find('#'+dqejid+' input.ali-meun-lihidden').attr("ejplatid",thisid);
		}
		else{
			$("#"+tabid+"").find('input.ali-meun-lihidden').attr("ejplatid",thisid);
		}
		
		//隐藏弹出层
		$("#mg-plateshow-bg").hide();
		$("#mg-plateshow").hide();
    });
	$("#mg-plateshow-close").click(function(){
		//获取当前二级菜单ID
		var dqejid=$("#alipay-meun-id").attr("erjiid");
		//获取当前模版ID，模版对应页面，模版名称
		var id=$("#mg-platehid-id").val();
		var model_page_id=$("#mg-platehid-id").attr("modelid");
		var planame=$("#mg-platehid-id").attr("platename");
		//隐藏弹出层
		$("#mg-plateshow-bg").hide();
		$("#mg-plateshow").hide();
		//ajax更新到服务器
		if(id != "" || model_page_id != "")
		{
			$.ajax({
				type:'POST',
				url:"/McpPlatForm/save_page_template",
				data:{id:id,model_page_id:model_page_id},
				dataType:"json",
				beforeSend: function(){
					show_pagemask();
				},
				complete: function(){
					hidden_pagemask();
				},
				success: function(data){
					show_globalPop(data.msg);
					$('#'+dqejid+'').find(".ali-meun-c-tem span.text").html(planame);
					$("#mg-platehid-id").attr("modelid","").val("");
					$("#mg-platehid-id").attr("platename","");
				}
			});
		}
		
	});
    //关闭链接等ifrem弹出层
	function showclose(){
		$("#mg-plateshow-bg").hide();
        $(".mg-plateshow").hide();
		
		$("#mg-plateshow").find(".mg-plateshow-ul").html("");
		$("#mg-linkshow").find(".waplink").html("");
	}
    $(".mg-plateshow-t i").click(function(){
       showclose();
		//var ff=$("#iframgraphic").contents().find("#iframe_graphic").val();
    });
    
    //管理
	
	//菜单同步到微信
	$("#releaseali").click(function(){
		if (confirm("当前操作会覆盖微信设置菜单，是否继续？")) {
			$.ajax({
				type:'POST',
				url:"/AcpConfig/add_platform",
				data:{act:"add_platform"},
				dataType:"json",
				beforeSend: function(){
					show_pagemask();
				},
				complete: function(){
					hidden_pagemask();
				},
				success: function(data){
					if(data.code == "200"){

						show_globalPop(data.msg);
					}
					else{
						show_globalPop(data.msg);
					}
				}
			});
		}
		
	});
    //刷新右边
    function Refresh() {
        var html = "",data = getMenuJson(),i = 0,j = 0,len = 0,j_len = 0;
        len = data.length;
        var data_name=[];
        var name_len;

        for(i=0;i<len;i++){
            if (data_name.indexOf(data[i].name) === -1) {
              data_name.push(data[i].name);
          }
        }
        name_len=data_name.length;
        var aw=272/name_len;
        var bw=(272/name_len)-1;

        console.log(data_name);

        for(k=0;k<name_len;k++){
            html+='<div class="ali-ms-c" style="width:'+aw+'px;"><div class="ali-ms-ct" style="width:'+bw+'px;" data="'+k+'"><span>' + data_name[k] + '</span></div><div class="ali-ms-cc">';

            for(h = 0; h < len; h++){
                if(data[h].type==2){
                    if(data[h].name==data_name[k]){
                        html+='<span>' + data[h].type_name + '</span>';
                        // html+='';
                        continue;
                    }else{
                        continue;
                    }
                }else{

                } 
                console.log("123")
            }
          html+='</div><div class="ali-ms-ci"></div></div>';
            console.log(html);
        }
		$("#mg-ali-ms").html("").append(html);
        // for (i = 0; i < len; i++) {
        //     // html+='<div class="ali-ms-c" style="width:'+aw+'px;"><div class="ali-ms-ct" style="width:'+bw+'px;" data="'+i+'"><span>' + data[i].name + '</span></div>'; //添加一级菜单
        //     var child = data[i].sub_button; 
        //     if(child!=null){
        //         html+='<div class="ali-ms-cc">';
        //         for (j = 0, j_len = child.length; j < j_len; j++) {
        //             html+='<span>' + child[j].name + '</span>';
        //         }
        //         html+='</div><div class="ali-ms-ci"></div></div>';
        //     }
        //     else{
        //         html+='</div>';
        //     }
        // }
		$("#mg-ali-ms").html("").append(html);
    }
	//右边一级菜单绑定点击事件
	 $("#mg-ali-ms").find(".ali-ms-ct").live("click", function() {
		var thisdata=$(this).attr("data");
		var len=$(".ali-ms-c").length;
		var thishtml=$(this).parent().find(".ali-ms-cc").html();
		if(thishtml == "")
		{
			$(this).parent().find(".ali-ms-cc").remove();
			$(this).parent().find(".ali-ms-ci").remove();
		}
		
		$("#mg-ali-ms .ali-ms-c:first").find(".ali-ms-cc").css("left","5px");
		if(len == 4)
		{
			$("#mg-ali-ms .ali-ms-c:last").find(".ali-ms-cc").css("left","-64px");
		}
		else if(len == 3){
			
			$("#mg-ali-ms .ali-ms-c:eq(1)").find(".ali-ms-cc").css("left","-20px");
			$("#mg-ali-ms .ali-ms-c:eq(1)").find(".ali-ms-ci").css("left","37px");
			
			$("#mg-ali-ms .ali-ms-c:last").find(".ali-ms-cc").css("left","-42px");
			$("#mg-ali-ms .ali-ms-c:last").find(".ali-ms-ci").css("left","40px");
		}
		else if(len == 2){
			$("#mg-ali-ms .ali-ms-c:first").find(".ali-ms-ci").css("left","60px");
			$("#mg-ali-ms .ali-ms-c:last").find(".ali-ms-cc").css("left","4px");
			$("#mg-ali-ms .ali-ms-c:last").find(".ali-ms-ci").css("left","64px");
		}
		

		$(this).siblings("div.ali-ms-cc").animate({bottom: "40px"}, 300 );
		$(this).siblings("div.ali-ms-ci").animate({bottom: "35px"}, 300 );
		$(this).parent().siblings("div").find(".ali-ms-cc").animate({bottom: "-170px"}, 300 );
		$(this).parent().siblings("div").find(".ali-ms-ci").animate({bottom: "-170px"}, 300 );
		
		
		$(this).parent().find("div.ali-ms-cc span:last").css("background","none");
	});
})

var thistype="";
//遍历菜单获得json
function getMenuJson() {
  //   var data = [],a=0;
  //   $("#sortable li").each(function() {
		// var thisdata=$(this).attr("data"),
		// 	id = 0,//分类ID
		// 	name = $(this).find(".j-alltext").html(),//名字
		// 	type = "",//1网址 2电话 3消息 4自定义页
		// 	value = "",//根据TYPE填写，消息为消息ID，自定义页为自身ID
		// 	b = 0,
		// 	menu = null,
		// 	sec_menu = [];//二级菜单
			
		// //var thistype="";
		// $(".alipay-meun-c").each(function() {
		//    if ($(this).attr("data") == thisdata) {
		// 		$(this).find(".alipay-meun-type a").each(function() {
		// 			if($(this).attr("class")=="hover")
		// 			{
		// 				thistype=$(this).attr("data");
		// 			}
		// 		});
		// 		if(thistype=="no")
		// 		{
		// 						type = $(this).find(".alipay-meun-no input.ali-meun-lihidden").attr("ejtype");
		// 					   value = $(this).find(".alipay-meun-no input.ali-meun-lihidden").val();//WAP：链接  电话：电话号码  消息回复：消息ID  自定义页：URL
		// 		}
		// 		else if(thistype=="have")
		// 		{

		// 			$(this).find("li").each(function() {
		// 				var 			name = $(this).find("input.ali-meun-lihidden").attr("ejname"),//名字
		// 								type = $(this).find("input.ali-meun-lihidden").attr("ejtype"),//类型：1 2 3 4
		// 							   value = $(this).find("input.ali-meun-lihidden").val();//WAP：链接  电话：电话号码  消息回复：消息ID  自定义页：URL
						
		// 				var type_name = type == 'view' ? 'url' : (type == 'click' ? 'key' : 'media_id');
		// 				var meun_sec = {
		// 					//"pid": b ,//排序ID
		// 					"name": name ,
		// 					"type": type ,
		// 					type_name: value,
		// 				};
						
		// 				sec_menu.push(meun_sec);
		// 			});
		// 		}
		// 	}
		// 	b++;
		// });
	
		// if(thistype=="have")
		// {
		// 	menu = {
		// 		//"id": id,//分类ID
		// 		//"pid": a,//排序ID
		// 		"name": name,//名字
		// 		"sub_button": sec_menu,//二级菜单
		// 	};
		// }
		// else if (thistype == 'no')
		// {
		// 	var type_name = type == 'view' ? 'url' : (type == 'click' ? 'key' : 'media_id');
		// 	type = $("#alimeunno"+thisdata).find("input.ali-meun-lihidden").attr("ejtype");
		// 	value = $("#alimeunno"+thisdata).find("input.ali-meun-lihidden").val();//WAP：链接  电话：电话号码  消息回复：消息ID  自定义页：URL
		// 	menu = {
		// 		"name": name,//名字
		// 		"type": type ,//1网址 2电话 3消息 4自定义页的
		// 		type_name: value,
		// 	};
		// }
		// // console.log(thistype);
		// // console.log(menu);
		// data.push(menu);
		// a++;
  //   })
    var data=[];
	var menu_serial = 0;	//一级菜单序号
    $("#sortable li").each(function() {
		menu_serial ++;
        var thisdata=$(this).attr("data"),
            name = $(this).find(".j-alltext").html(),//名字
            value = "",//子菜单内容
            ejname ='',//子菜单名称
            type = "view";//1网址 2电话 3消息 4自定义页
            $(".alipay-meun-c").each(function(){
                if($(this).attr("data") == thisdata){//判断tab操作区域
                    if($("#alimeunh"+thisdata).find('li').length>0){//如果有子级菜单
                        $(this).find("li").each(function() {//操作子集菜单
                             ejname = $(this).find("input.ali-meun-lihidden").attr("ejname");
                             ejtype = $(this).find("input.ali-meun-lihidden").attr("ejtype");
                             value = $(this).find("input.ali-meun-lihidden").val();//获取填写值
                             var meun_sec = {
                                 "menu_serial": menu_serial,
                                 "name": name ,
                                 "type": 2 ,//2是有子级菜单
                                 "type_name": ejname,
                                 "type_value": ejtype,
                                 "value":value
                             };
                             console.log(meun_sec.name)
                             data.push(meun_sec);
                        })
                    }else{
                        ejname=$("#alimeunno"+thisdata).find("input.ali-meun-lihidden").attr("ejname");
                        ejtype=$("#alimeunno"+thisdata).find("input.ali-meun-lihidden").attr("ejtype");
                        value = $(this).find("input.ali-meun-lihidden").val();//获取填写值
                        var meun_first = {
							 "menu_serial": menu_serial,
                             "name": name ,
                             "type": 1 ,//1是无子级菜单
                             "type_name": ejname,
                             "type_value": ejtype,
                             "value":value
                         };
                         data.push(meun_first);
                    }
                }
            })
    })

    console.log(data);

    return data;
}

//限定input字符个数
function CheckLength(txtObj,length) {
    var str = txtObj.value;
    var len = str.length;
    var temp="";
    var reLen = 0;
    for (var i = 0; i < len; i++) {    
        if (str.charCodeAt(i) >255 ) {
            // 全角   
            reLen += 3;
        } else {
           reLen++;
        }
        if (reLen < length+1){
           temp+=str.charAt(i);
        }else{
           txtObj.value= temp;
        }
        
    }
	if(reLen == 0)
	{show_globalPop("菜单名不能为空！");}
}

//显示遮罩并出现进行中的转圈图标

function show_pagemask() {
    try {
        document.body.removeChild(document.getElementById('pagemaskDiv'));
    } catch (e) {}

    //mask遮罩层 
    var newMask = document.createElement("div");
    newMask.id = "pagemaskDiv";
    newMask.style.position = "absolute";
    newMask.style.zIndex = "99999";
    _scrollWidth = Math.max(document.body.scrollWidth, document.documentElement.scrollWidth);
    _scrollHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
    //_scrollHeight2 = Math.max(document.body.offsetHeight,document.documentElement.scrollHeight); 
    newMask.style.width = _scrollWidth + "px";
    newMask.style.height = _scrollHeight + "px";
    newMask.style.top = "0px";
    newMask.style.left = "0px";
    newMask.style.background = "#33393C";
    newMask.style.filter = "alpha(opacity=40)";
    newMask.style.opacity = "0.40";
    newMask.style.display = '';
    // document.body.appendChild(newMask);

    try {
        document.body.removeChild(document.getElementById('maskloading'));
    } catch (e) {}

    //加载图标
    var objDiv = document.createElement("DIV");
    objDiv.id = "maskloading";
    objDiv.style.width = "130px";
    objDiv.style.height = "140px";
    objDiv.style.left = (_scrollWidth - 100) / 2 + "px";
    objDiv.style.top = (((window.screen.availHeight - 220) / 2) + document.documentElement.scrollTop) + "px";
    objDiv.style.position = "absolute";
    objDiv.style.zIndex = "999999"; //加了这个语句让objDiv浮在newMask之上 
    objDiv.style.display = ""; //让objDiv预先隐藏 
    objDiv.innerHTML = '<img src="/Public/Images/loading.gif" border="0" />';

    newMask.appendChild(objDiv);
    document.body.appendChild(newMask);
}


//隐藏遮罩和转圈图标

function hidden_pagemask() {
    try {
        document.getElementById("pagemaskDiv").style.display = "none";
        document.getElementById("maskloading").style.display = "none";
    }
    catch(e){}
}

// 修改值
function change_value(obj)
{
	var val = $(obj).val();
	var obj2 = $(obj).parent().parent().parent().parent().find('.j-alltext');
    $(obj).parents(".j-nav-erji").find(".ali-meun-lihidden").val(val);
    console.log(obj2);
    console.log(val);
	$(obj2).html(val);
}

//显示全局信息弹出窗
function show_globalPop(str, timing) {
    var $ele = $("#globalPop"),
        width_globalPop = $ele.width() / 2,
        height_globalPop = $(window).scrollTop() + $(window).height() / 2;

    if (!timing) {
        timing = 2000;
    }

    $ele.text(str);

    if (height_globalPop == 0) {
        height_globalPop = "50%";
    }

    $("#globalPop").css({
        "marginLeft": -width_globalPop,
        "top": height_globalPop
    }).fadeIn();

    setTimeout(function() {
        $("#globalPop").fadeOut();
    }, timing);
}
