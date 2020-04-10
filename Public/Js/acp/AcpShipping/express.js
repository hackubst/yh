// 快递单模板编辑器
$(function(){
	//如果浏览器版本低于IE8，提示用户
	if($.browser.msie && parseInt($.browser.version) < 8){
		$("#hack_fixie67").show();
	}
	//初始化拖动，缩放事件，移除事件
	$(".textarea-item").each(function(){
		var $self=$(this),
			$textarea=$self.find("textarea");
		//resizable
		$self.resizable({
			start:function(){
				$textarea.focus();
			}
		});
		//draggable
		$self.draggable({
		 	handle: ".textarea-item-move",
		 	drag:function(){
		 		$textarea.focus();
		 	}
		});
		//移除事件
		$self.find(".textarea-item-del").click(function(){
			$self.hide();
			exp.slt_opts.val("");
		});
		//控制焦点
		$textarea.focus(function(){
			$self.addClass("focus");
		})
		.blur(function(){
			$self.removeClass("focus");
		});
	});

	//控制字体加粗/斜体的选中样式
	$(".ckb-font").click(function(){
		$(this).toggleClass("checked");
	});

	//express 命名空间
	var exp=exp ? exp : {};

	exp.slt_opts=$("#slt_opts");//编辑控制项
	exp.slt_fts=$("#slt_fontsize");//字体大小select
	exp.slt_lts=$("#slt_letterSpacing");//字体间距select
	exp.pos_top=$("#ipt_posTop");//top属性
	exp.pos_left=$("#ipt_posLeft");//left属性
	exp.ckb_fontbold=$("#ckb_fontbold");//字体加粗
	exp.ckb_fontitalic=$("#ckb_fontitalic");//字体斜体

	//编辑项切换
	exp.slt_opts.change(function(){
		var target="#"+$(this).val(),
			$target=$(target),
			$textarea=$target.find("textarea");
		$target.show();
		$textarea.focus();
	});

	//字体大小切换
	exp.slt_fts.change(function(){
		var target=exp.slt_opts.val();
		$(".textarea-item>textarea[name="+target+"]").css("fontSize",parseInt($(this).val()));
	});

	//切换字体间距
	exp.slt_lts.change(function(){
		var target=exp.slt_opts.val();
		$(".textarea-item>textarea[name="+target+"]").css("letterSpacing",parseInt($(this).val()));
	});

	//更改位置top
	exp.pos_top.keyup(function(){
		var target=exp.slt_opts.val();
		$(".textarea-item>textarea[name="+target+"]")
		.parent(".textarea-item")
		.css("top",parseInt($(this).val()));
	});

	//更改位置left
	exp.pos_left.keyup(function(){
		var target=exp.slt_opts.val();
		$(".textarea-item>textarea[name="+target+"]")
		.parent(".textarea-item")
		.css("left",parseInt($(this).val()));
	});

	//切换字体加粗
	exp.ckb_fontbold.click(function(){
		var target=exp.slt_opts.val();
		if($(this).hasClass("checked")){
			$(".textarea-item>textarea[name="+target+"]").css("fontWeight","bold");
		}
		else{
			$(".textarea-item>textarea[name="+target+"]").css("fontWeight","normal");
		}
	});

	//切换字体斜体
	exp.ckb_fontitalic.click(function(){
		var target=exp.slt_opts.val();
		if($(this).hasClass("checked")){
			$(".textarea-item>textarea[name="+target+"]").css("fontStyle","italic");
		}
		else{
			$(".textarea-item>textarea[name="+target+"]").css("fontStyle","normal");
		}
	});

	//编辑项聚焦后自动载入参数到设置区域
	$(".textarea-item>textarea").focus(function(){
		var $self=$(this);

		exp.updateSetting({
			name:$self.attr("name"),
			fontSize:parseInt($self.css("fontSize")),
			letterSpacing:parseInt($self.css("letterSpacing")),
			left:parseInt($self.parent(".textarea-item").css("left")),
			top:parseInt($self.parent(".textarea-item").css("top")),
			bold:$self.css("fontWeight"),
			italic:$self.css("fontStyle")
		});
	});

	//生成参数
	$("#btn_confirm").click(function(){
	//	var opts=[];//参数

		if ('' == $('#J_ImgUrl').val()) {
			$.jPops.alert({
				title:"提示",
				content:'请先上传快递单底图！',
				okBtnTxt:"确定",
				callback:function(){
					return true;
				}
			});
			return false
		}
		
		if($(".textarea-item:visible").length<=0){
			$.jPops.alert({
				title:"提示",
				content:'请添加打印项！',
				okBtnTxt:"确定",
				callback:function(){
					return true;
				}
			});
			return false;
		}

		//遍历页面上存在的编辑项，并把参数传入opts
		$(".textarea-item").each(function(){
			var $self=$(this);

			//容器里存在这个选项时，再输出
			if(!$self.is(":visible")){
				return;
			}

			var	self_id=$self.attr("id"),
				$textarea=$self.find("textarea[name="+self_id+"]"),
				item = {
					"id":self_id,
					"width":$self.css("width"),
					"height":$self.css("height"),
					"top":$self.css("top"),
					"left":$self.css("left"),
					"fontSize":$textarea.css("fontSize"),
					"letterSpacing":$textarea.css("letterSpacing"),
					"bold":$textarea.css("fontWeight"),
					"italic":$textarea.css("fontStyle"),
					"value":($textarea.val() != $textarea.data('tip_value')) ? $textarea.val() : ''
				};

		//	opts.push(JSON.stringify(item));
			$('#J_FormExpress').append('<input type="hidden" name="print_items_params[]" value="' + encodeURI(JSON.stringify(item)) + '" />');
		});

	//	console.log(opts);
		$('#J_FormExpress').submit();

		return false;
	});

	//更新设置区域参数
	exp.updateSetting=function(opts){
		if(!opts){
			return;
		}
		for(var key in opts){
			var val=opts[key];
			switch(key){
				case "name":this.slt_opts.val(val);break;
				case "fontSize":this.slt_fts.val(val);break;
				case "letterSpacing":this.slt_lts.val(val);break;
				case "left":this.pos_left.val(val);break;
				case "top":this.pos_top.val(val);break;
				case "bold":
					if(parseInt(val)==400 || val=="normal"){
						this.ckb_fontbold.removeClass("checked");
					}
					else{
						this.ckb_fontbold.addClass("checked");
					}
					break;
				case "italic":
					if(val=="italic"){
						this.ckb_fontitalic.addClass("checked");
					}
					else{
						this.ckb_fontitalic.removeClass("checked");
					}
					break;
			}
		}
	}
	
});