/** by zhengzhen */
function showPopTip(args) {
	var type = args.type;
	var tip = args.tip;
	var timing = args.timing;
	var callback = args.callback;
	var tipLayer, tipLayerWidth;
	
	tipLayer = $('#J_TipLayer');
	if (tip != undefined) {
		tipLayer.children('span').text(tip);
	}
	$('#J_TransMaskLayer').show();
	tipLayer.show();
	switch(type) {
		case 'loading':
			tipLayer.css('padding-left', '42px').children('i').addClass('icon');
			break;
		default:
			tipLayer.css('padding-left', '12px').children('i').removeClass('icon');
			break;
	}
	
	tipLayerWidth = tipLayer.width();
	tipLayer.css('margin-left', '-' + tipLayerWidth / 2 + 'px');
	
	if (timing != undefined) {
		setTimeout(function(){ hidePopTip(callback); }, timing);
	}
	else if (callback != undefined) {
		callback();
	}
}

function hidePopTip(callback) {
	$('#J_TipLayer').hide();
	$('#J_TransMaskLayer').hide();
	if (callback != undefined) {
		callback();
	}
}

function changePopTip(args) {
	var type = args.type;
	var tip = args.tip;
	var timing = args.timing;
	var callback = args.callback;
	var tipLayer, tipLayerWidth;
	
	tipLayer = $('#J_TipLayer');
	tipLayer.children('span').text(tip);
	
	switch(type) {
		case 'loading':
			tipLayer.css('padding-left', '42px').children('i').addClass('icon');
			break;
		default:
			tipLayer.css('padding-left', '12px').children('i').removeClass('icon');
			break;
	}
	tipLayerWidth = tipLayer.width();
	tipLayer.css('margin-left', '-' + tipLayerWidth / 2 + 'px');
	
	if (timing != undefined) {
		setTimeout(function(){ hidePopTip(callback); }, timing);
	}
	else if (callback != undefined) {
		callback();
	}
}
