//by zhengzhen

var LODOP; //声明为全局变量
var tip = false;//提示

$(function(){
	$('#print_selected').on('click', printSelected);
});

//打印当前选择
function printSelected(){
	var param = $("#search_form").serialize();
	var _checked = [];
	$('.checkbox:checked').each(function() {
		_checked.push($(this).val());
	});
	if(_checked.length > 0){
		param += '&order_id=' + _checked.join(',');
		printit(param, 'selected');
	} else {
		$.jPops.alert({
			title:"提示",
			content:"请选择打印项！",
			okBtnTxt:"确定",
			callback:function(){
				return true;
			}
		});
	}
}

function printAll(start){
	var param = $("#search_form").serialize();
	
	start = (start != undefined) ? start : 1;
	param += '&start=' + start;
	printit(param, 'all');
}

function printit(param, type){
	var printStatus = false;
	param += '&print_action=' + type;
	$.ajax({
		url: '/AcpOrderAjax/batch_shipping_print',
		data: param,
		dataType: 'json',
		success: function(data){
			console.log(data);
		//	return false;
			
			if(data.status == 0){
				$.jPops.alert({
					title:"提示",
					content:data.msg,
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
				return false;
			}
			LODOP = getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
			if(!LODOP.PRINT_INITA(0, 0, data.other.MB_BG_WIDTH, data.other.MB_BG_HEIGHT, "【" + data.other.MB_NAME + "】快递单批量打印")){
				$.jPops.alert({
					title:"提示",
					content:"请连接打印机！",
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
			}
			LODOP.ADD_PRINT_SETUP_BKIMG('<img src="' + data.other.MB_BG + '" />');
			LODOP.SET_SHOW_MODE("BKIMG_IN_PREVIEW", 1);
			LODOP.SET_PRINT_PAGESIZE(1, data.other.MB_WIDTH, data.other.MB_HEIGHT, "");
			var itemName = '';
			for(var i in data.page){
				LODOP.NewPage();
				for(var j in data.page[i].item){
					itemName = 'item_' + j;
					if('' != data.page[i].item[j].S){
						LODOP.SET_PRINT_STYLE("FontSize", data.page[i].item[j].S);
					} else {
						LODOP.SET_PRINT_STYLE("FontSize", 9);
					}
					if('' != data.page[i].item[j].B){
						LODOP.SET_PRINT_STYLE("Bold", data.page[i].item[j].B);
					} else {
						LODOP.SET_PRINT_STYLE("Bold", 0);
					}
					if('' != data.page[i].item[j].I){
						LODOP.SET_PRINT_STYLE("Italic", data.page[i].item[j].I);
					} else {
						LODOP.SET_PRINT_STYLE("Italic", 0);
					}
					if('' != data.page[i].item[j].L){
						LODOP.SET_PRINT_STYLEA(itemName, "LetterSpacing", data.page[i].item[j].L);
					} else {
						LODOP.SET_PRINT_STYLEA(itemName, "LetterSpacing", 0);
					}
					LODOP.ADD_PRINT_TEXTA(itemName, data.page[i].item[j].Y, data.page[i].item[j].X, data.page[i].item[j].W, data.page[i].item[j].H, data.page[i].item[j].VALUE);
				}
			}
			LODOP.SET_PRINT_MODE("POS_BASEON_PAPER", true);
		//	LODOP.PRINT_SETUP();return false;
			
			//使用默认打印机打印
			if(!LODOP.SET_PRINTER_INDEXA(-1)){
				//如果未设置默认打印机，则弹出设置窗口
				if(LODOP.SELECT_PRINTER() == -1){
					_alert('打印取消！');
					return false;
				}
			}
			if(!tip){
				$.jPops.alert({
					title:"提示",
					content:"请确保打印纸的类型为【" + data.other.MB_NAME + "】的快递单！",
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
				tip = true;
			}
			printStatus = LODOP.PRINT();
			
			if(printStatus){
				if(type == 'all'){
					if(!data.other.IS_FINISH){
						_alert('>>>下一页>>>');
						printAll(data.other.START);
						return true;
					}
				}
				$.jPops.alert({
					title:"提示",
					content:"打印完毕！",
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
				tip = false;
			} else {
				return false;
			}
		}
	});
}

function _alert(txt, timing){
	var globalPop=$("#globalPop");
	globalPop.text(txt).show();
	gpWidth=globalPop.width();
	globalPop.css({"marginLeft":-gpWidth/2+"px"});
	if(timing == null){
		timing = 3000;
	}
	setTimeout(function(){
		globalPop.hide();
	},timing);
}
