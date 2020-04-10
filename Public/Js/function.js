//设置ID为st的背景色为color_v
function sbar(st, color_v) 
{
	var id = document.getElementById(st);
	
	id.style.backgroundColor = color_v;
}

function set_obj_content(obj, v)
{
	document.getElementById(obj).innerHTML = v;
}

//只允许数字键，onkeypress="return only_num();"
function only_num()
{
	if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;
}

//只允许数字键和点，onkeypress="return only_num_dot();"
function only_num_dot()
{
	if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 46) event.returnValue = false;
}

//只允许数字键和减号，onkeypress="return only_num_jh();"
function only_num_jh()
{
	if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 45) event.returnValue = false;
}

//验证是不是整数
function is_int(v)
{
	var o = parseInt(v);
	var m = o.toString();
	var n = v.toString();
	
	if (m === n)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//改变图片域的图片
function change_pic(tar, v, w, h, swf, all_f, ext)
{
	var my_v = v.toUpperCase();
	var obj = document.getElementById(tar);
	
	if (my_v == '')
	{
		obj.innerHTML = "";
		return true;
	}
	
	if (ext != undefined) {
		if (my_v.indexOf(ext) == -1) {
			obj.innerHTML = "<font style='color:red;'><b>错误</b>:只允许上传" + ext + "格式的文件！</font>";
			return false;
		} else {
			obj.innerHTML = v;
			return true;
		}
	}
	
	if (my_v.indexOf('.PNG') == -1 && my_v.indexOf('.GIF') == -1 && my_v.indexOf('.JPG') == -1)
	{
		if (swf == true)
		{
			if (my_v.indexOf('.SWF') == -1)
			{
				if (all_f == true) {
					var can_ext = new RegExp(".(7z|aiff|asf|avi|bmp|csv|doc|fla|flv|gif|gz|gzip|jpeg|jpg|mid|mov|mp3|mp4|mpc|mpeg|mpg|ods|odt|pdf|png|ppt|pxd|qt|ram|rar|rm|rmi|rmvb|rtf|sdc|sitd|swf|sxc|sxw|tar|tgz|tif|tiff|txt|vsd|wav|wma|wmv|xls|xml|zip|html|htm|xml|js|css)$", 'i');
					if (can_ext.test(v)) {
						obj.innerHTML = v;
						return true;
					}
					else {
						obj.innerHTML = "<font style='color:red;'><b>错误</b>:您要上传的文件不允许上传！</font>";
					}
				} else {
					obj.innerHTML = "<font style='color:red;'><b>错误</b>:只允许上传PNG、GIF、JPG、SWF格式的文件！</font>";
				}
			}
			else
			{
				obj.innerHTML = '<embed src="' + v + '" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="' + w + '" height="' + h + '" wmode=opaque></embed>';
				return true;
			}
		}
		else
		{
			if (all_f == true) {
				var can_ext = new RegExp(".(7z|aiff|asf|avi|bmp|csv|doc|fla|flv|gif|gz|gzip|jpeg|jpg|mid|mov|mp3|mp4|mpc|mpeg|mpg|ods|odt|pdf|png|ppt|pxd|qt|ram|rar|rm|rmi|rmvb|rtf|sdc|sitd|swf|sxc|sxw|tar|tgz|tif|tiff|txt|vsd|wav|wma|wmv|xls|xml|zip|html|htm|xml|js|css)$", 'i');
				if (can_ext.test(v)) {
					obj.innerHTML = v;
					return true;
				}
				else {
					obj.innerHTML = "<font style='color:red;'><b>错误</b>:您要上传的文件不允许上传！</font>";
				}
			} else {
				obj.innerHTML = "<font style='color:red;'><b>错误</b>:只允许上传PNG、GIF、JPG格式的图片！</font>";
			}
		}		
		return false;
	}else{
		obj.innerHTML = '<img src="' + v + '" id="change_pic_obj_id_' + tar + '">';
		resize_pic("change_pic_obj_id_" + tar, w, h);
		return true;
	}
}

//重置图片大小
function resize_pic(obj, w, h)
{
	var img = document.getElementById(obj);
	
	if (w > 0) img.width = w;
	if (h > 0) img.height = h;
}

//清除表单项中的内容，主要用于清除typ=file类型的
function clean_input_value(obj)
{
    document.getElementById(obj).focus();
    document.execCommand("selectall");
    document.execCommand("Delete");
}

//函数名：chkemail     
//功能介绍：检查是否为Email Address     
//参数说明：要检查的字符串     
//返回值：false：不是 true：是     
function chkemail(a)
{
	if (a.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
	{
		return true;
	}
	else
	{
		return false;	
	}
} 

function selall(optionname)
{
	try{
		var my_object = document.getElementsByName(optionname);
		for ( i = 0; i < my_object.length; i++ )
		{
			var e = my_object[i];
			e.checked = true;
		}
	}
	catch(e)
	{}
}

function clearall(optionname)
{
	try{
		var my_object = document.getElementsByName(optionname);
		for ( i = 0; i < my_object.length; i++ )
		{
			var e = my_object[i];
			e.checked = false;
		}
	}
	catch(e)
	{}
}

function seloth(optionname)
{
	try{
		var my_object = document.getElementsByName(optionname);
		for ( i = 0; i < my_object.length; i++ )
		{
			var e = my_object[i];
			if ( e.checked == true )
			{
				e.checked = false;
			} else {
				e.checked = true;
			}
		}
	}
	catch(e)
	{}
}

function selcur(formname, curoption)
{
	var curoption3 = curoption.split('[');
	var curoption2 = curoption3[0];
	var my_object2 = eval("document." + formname + "." + curoption2);
	
	if ( my_object2.length > 1 )
	{
		var my_object = eval("document." + formname + "." + curoption);
		
		if ( my_object.checked == true )
		{
			my_object.checked = false;
		} else {
			my_object.checked = true;
		}
	}
	else 
	{
		if ( my_object2.checked == true )
		{
			my_object2.checked = false;
		} else {
			my_object2.checked = true;
		}
	}
}

function do_post(formname, optionid, myurl, varid, message)
{
	try{
		var my_object = eval("document." + formname + "." + optionid);
		var sel_v = '';
		var is_first = false;
		
		if ( my_object.length > 1 )
		{
			for ( i = 0; i < my_object.length; i++ )
			{
				var e = my_object[i];
				if ( e.checked == true )
				{
					if ( sel_v == '' )
					{
						is_first = true;
						sel_v = e.value;
					} else {
						sel_v += ',' + e.value;
					}
				}
			}
			
			if ( is_first == true )
			{
				window.location = myurl + '-' + varid + '-' + sel_v + '.html';
			} else {
				alert(message);
			}
		}
		else
		{
			if ( my_object.checked == true )
			{
				is_first = true;
				sel_v = my_object.value;
			}
	
			if ( is_first == true )
			{
				window.location = myurl + '-' + varid + '-' + sel_v + '.html';
			} else {
				alert(message);
			}
		}
	}
	catch(e)
	{
		alert(message);
	}
}

function openwin(myurl,myname,w,h,sc)
{
	window.open(myurl,myname,'height=' + h + ',scrollbars=' + sc + ',width=' + w);
}

function writepage(content)
{
	document.write(content);
}

function format_num(ValueString, nAfterDotNum)
{
	var ValueString, nAfterDotNum ;
	var resultStr,nTen;
	ValueString = ""+ValueString+"";
	strLen = ValueString.length;
	dotPos = ValueString.indexOf(".",0);
	if (dotPos == -1)
	{
		resultStr = ValueString+".";
		for (i=0;i<nAfterDotNum ;i++)
		{
			resultStr = resultStr+"0";
		}
		return resultStr;
	}
	else
	{
		if ((strLen - dotPos - 1) >= nAfterDotNum )
		{
			nAfter = dotPos + nAfterDotNum  + 1;
			nTen =1;
			for(j=0;j<nAfterDotNum ;j++)
			{
				nTen = nTen*10;
			}
		
			resultStr = Math.round(parseFloat(ValueString)*nTen)/nTen;
			return resultStr;
		}
		else{
			resultStr = ValueString;
			for (i=0;i<(nAfterDotNum  - strLen + dotPos + 1);i++){
				resultStr = resultStr+"0";
			}
			return resultStr;
		}
	}
}


function delcfm()
{
    if(!confirm("确认要删除？"))
    {
        window.event.returnValue = false;
        return false;
    }
}

function docfm(str)
{
	if (str == '')
	{
		str = '确认要删除？'
	}
    if(!confirm(str))
    {
        window.event.returnValue = false;
        return false;
    }
}

function pageWidth()
{
	if(document.body.clientWidth)
	{
		var m_w = document.body.clientWidth;
	}
	else if(document.body.offsetWidth)
	{
		var m_w = document.body.offsetWidth
	}
	else if(document.body.scrollWidth)
	{
		var m_w = document.body.scrollWidth;
	}
	else
	{
		var m_w = window.innerWidth != null? window.innerWidth: document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth:document.body != null? document.body.clientWidth:null;
	}
	
	if(m_w < 300)
	m_w = 500;

	return m_w;
}


function pageHeight()
{	
	if(document.body.clientHeight)
	{
		var m_h = document.body.clientHeight;
	}
	else if(document.body.offsetHeight)
	{
		var m_h = document.body.offsetHeight;
	}
	else if(document.body.scrollHeight)
	{
		var m_h = document.body.scrollHeight;
	}
	else
	{
		var m_h = window.innerHeight != null? window.innerHeight: document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight:document.body != null? document.body.clientHeight:null;
	}
	
	if(m_h < 200)
	m_h = 600;
	
	return m_h;
}

function add_option(selectId, txt, val)
{
	var objOption = new Option(txt, val);
	document.getElementById(selectId).options.add(objOption);
}

function clean_option(selectId)
{
	document.getElementById(selectId).options.length = 0;
}

function selected_option(selectId, sId)
{
	document.getElementById(selectId).options[sId].selected = true;
}

function selected_option_v(selectId, v)
{
	for (i=0; i<document.getElementById(selectId).options.length; i++)
	{
		if (document.getElementById(selectId).options[i].value == v) {
			document.getElementById(selectId).options[i].selected = true;
			break;
		}
	}
}

function get_select_value(select_name) {
	var obj = document.getElementById(select_name);
	
	try {
		return obj.options[obj.selectedIndex].value;
	}
	catch(e) {
		try {
			var m_v = obj.value;
			return m_v;
		}
		catch(e) {}
	}
	
	return '';
}

/*
比较两个日期
当date1 > date2时返回'>';
当date1 = date2时返回'=';
当date1 < date2时返回'<';
异常返回false
*/
function datecompare(date1, date2)
{ 
	var d1 = new Date(date1.replace(/\-/g, "\/")); 
	var d2 = new Date(date2.replace(/\-/g, "\/")); 
	
	var flag = true; 
	
	if (d1 == "NaN" || d2 == "NaN") flag = false; //不是日期 
	
	if (flag != false && d1.getFullYear() > d2.getFullYear())
	{ 
		flag = '>'; 
	} 
	
	if (flag != false && d1.getFullYear() == d2.getFullYear() && d1.getMonth() > d2.getMonth())
	{ 
		flag = '>'; 
	} 
	
	if (flag != false && d1.getFullYear() == d2.getFullYear() && d1.getMonth() == d2.getMonth() && d1.getDate() > d2.getDate())
	{ 
		flag = '>'; 
	}
	
	if (flag != false && flag != '>')
	{
		if (flag != false && d1.getFullYear() == d2.getFullYear() && d1.getMonth() == d2.getMonth() && d1.getDate() == d2.getDate())
		{
			flag = '='; 
		}
		else
		{
			flag = '<'; 
		}
	}
	
	return flag; 
} 

String.prototype.trim = function()
{
    // 用正则表达式将前后空格
    // 用空字符串替代。
    return this.replace(/(^\s*)|(\s*$)/g, "");
}

function showMenu(num)
{
    for(var x = 1; x <= 10; x++)
    {
        try {
			 document.getElementById("menu" + x).style.display = "none";
		}
		catch(e) { }
    }
    if(num)
    {
        try {
			document.getElementById("menu" + num).style.display = "block";
		}
		catch(e) { }
    }
}

function show_hiden(tar) {
	var id = document.getElementById(tar);
	
	if (id.style.display == 'none') {
		id.style.display = '';
	} else {
		id.style.display = 'none';
	}
}

function show_mobj(tar) {
	var id = document.getElementById(tar);
	
	id.style.display = '';
}

function hidden_mobj(tar) {
	var id = document.getElementById(tar);
	
	id.style.display = 'none';
}

//增加表列，row_id为行的ID；cell_index为插入的位置，0表示第一行；cell_content为单元格中的内容
function table_add_cell(row_id, cell_index, cell_content) {
	/*document.getElementById(row_id).cells.length 用来获得表格的列数*/
	var cell = document.getElementById(row_id).insertCell(cell_index);
	cell.innerHTML = cell_content;
}

//删除表列，row_id为行的ID；cell_index为删除的位置，0表示第一行
function table_del_cell(row_id, cell_index){
	document.getElementById(row_id).deleteCell(cell_index);
}

//判断浏览器
function user_browser(){
	var ua = navigator.userAgent;
	ua = ua.toLowerCase();
	var match_v = /(webkit)[ \/]([\w.]+)/.exec(ua) || /(opera)(?:.*version)?[ \/]([\w.]+)/.exec(ua) || /(msie) ([\w.]+)/.exec(ua) || !/compatible/.test(ua) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(ua) || [];
	
	var cur_browser = '';
	
	//如果需要获取浏览器版本号：match_v[2]
	switch(match_v[1]){
	case "msie":
		if (parseInt(match_v[2]) === 6)
			cur_browser = 'ie6';
		else if (parseInt(match_v[2]) === 7)
			cur_browser = 'ie7';
		else if (parseInt(match_v[2]) === 8)
			cur_browser = 'ie8';
		break;
	case "webkit":
		cur_browser = 'webkit';
		break;
	case "opera":
		cur_browser = 'opera';
		break;
	case "mozilla":
		cur_browser = 'ff';
		break;
	default:
		break;
	}

	return cur_browser;
}

function get_radio_value(input_name) {
	var obj = document.getElementsByName(input_name);
	
	try {
		for (i = 0; i < obj.length; i++){  //遍历Radio  
			if (obj[i].checked){
				return obj[i].value;//获取Radio的值
			} 
		}
	}
	catch(e) {
		try {
			var m_v = obj.value;
			return m_v;
		}
		catch(e) {}
	}
	
	return '';
}

//根据值选中radio选项
function selected_radio_v(obj, v)
{
	var temp = document.getElementsByName(obj);
	for (var i = 0; i < temp.length; i++)
	{
		if(temp[i].value == v) {
			temp[i].checked = true;
			break;
		}
	}
}

//检查checkbox是否已勾选
function check_checkbox_checked(obj)
{
	var temp = document.getElementsByName(obj);
	for (var i = 0; i < temp.length; i++)
	{
		if(temp[i].checked)
			return true;
	}
	
	return false;
}

//显示遮罩并出现进行中的转圈图标
function show_pagemask() {
	try {
		show_mobj('pagemaskDiv');
	}
	catch (e) 
	{
		//mask遮罩层 
		var newMask=document.createElement("div"); 
		newMask.id="pagemaskDiv"; 
		newMask.style.position="absolute"; 
		newMask.style.zIndex="1000"; 
		_scrollWidth=Math.max(document.body.scrollWidth,document.documentElement.scrollWidth); 
		_scrollHeight=Math.max(document.body.scrollHeight,document.documentElement.scrollHeight); 
		//_scrollHeight2 = Math.max(document.body.offsetHeight,document.documentElement.scrollHeight); 
		newMask.style.width=_scrollWidth+"px"; 
		newMask.style.height=_scrollHeight+"px"; 
		newMask.style.top="0px"; 
		newMask.style.left="0px"; 
		newMask.style.background="#33393C"; 
		newMask.style.filter="alpha(opacity=40)"; 
		newMask.style.opacity="0.40"; 
		newMask.style.display=''; 
		document.body.appendChild(newMask);
	}
	
	try {
		show_mobj('maskloading');
	}
	catch (e) 
	{
		//加载图标
		var objDiv=document.createElement("DIV"); 
		objDiv.id="maskloading"; 
		objDiv.style.width="130px"; 
		objDiv.style.height="140px"; 
		objDiv.style.left=(_scrollWidth-130)/2+"px"; 
		objDiv.style.top=(window.screen.height-300)/2+"px"; 
		objDiv.style.position="absolute"; 
		objDiv.style.zIndex="1001"; //加了这个语句让objDiv浮在newMask之上 
		objDiv.style.display=""; //让objDiv预先隐藏 
		objDiv.innerHTML='<img src="/Public/images/loading.gif" border="0" />'; 
		document.body.appendChild(objDiv);
	}
}

//隐藏遮罩和转圈图标
function hidden_pagemask() {
	hidden_mobj('pagemaskDiv');
	hidden_mobj('maskloading');
}

//清空iframe的src
function clean_iframe(tar) {
	document.getElementById(tar).src = '';
}

//模拟点击
function click_obj(obj) {
	var myLink = document.getElementById(obj);//定位元素为"myLink"
	myLink.click();//模拟click动作
}

//更换指定ID的class值
function changeMclass(obj, classn) {
	document.getElementById(obj).className = classn;
}
