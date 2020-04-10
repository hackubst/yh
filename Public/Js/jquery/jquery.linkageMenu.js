/** 
 * jQuery Linkage Menu 
 * 
 * Copyright 2014, sunyingyuan 
 * QQ: 1586383022 
 * Email: yingyuansun@163.com 
 * 
 * 二级联动菜单 
 * 目前只能支持二级联动，并且只能通过AJAX实时从后台取数据，如果需要前台静态数据，请联系作者，暂时没有需求，有时间再添加 
 * 简单使用方法： 
 * HTML代码： 
 * <select id="selectOne"> 
 *      <option>一级菜单默认显示名称</option> 
 * </select> 
 * <select id="selectTwo"> 
 *      <option>二级菜单默认显示名称</option> 
 * </select> 
 * 
 * JS代码: 
 * 引入jQuery和jquery.linkageMenu.js后 
 * $(function(){ 
 *          $.linkageMenu({ 
 *              'selectOneId': 'selectOne',//一级菜单Id 
 *              'selectTwoId': 'selectTwo',//二级菜单Id 
 *              'selectOneVal': '{"key1":"value1", "key2":"value2"}',//一级菜单option值 
 *              'paramName' : 'selectOneValue',//请求url的参数key 
 *              'getSelectTwoValUrl': 'http://localhost:3000/users'//通过一级菜单的value获取二级菜单的值的url 
 *          }); 
 * }); 
 */  
(function ($) {  
    $.linkageMenu = function (options) {  
        //默认参数  
        var settings = $.extend({  
            'selectOneId': 'selectOne',//一级菜单Id  
            'selectTwoId': 'selectTwo',//二级菜单Id  
            'selectOneVal': '',//一级菜单option值  
            'selectTwoVal': '{"key1":"value2","key2":"value2"}',//预留字段，供插件以后扩展  
            'paramName' : 'selectOneValue',//请求url的参数key  
            'getSelectTwoValUrl': ''//得到二级菜单的value的url  
        }, options);  
        var $s1 = $("#" + settings.selectOneId);  
        var $s2 = $("#" + settings.selectTwoId);  
        var selectOneValJSON = $.parseJSON(settings.selectOneVal);  
        //alert(selectOneValJSON.key1);  
        //JSON.parse(options.selectOneVal); //由JSON字符串转换为JSON对象  
        //一级菜单初始化  
        $.each(selectOneValJSON, function (key, val) {  
            appendOptionTo($s1, key, val);  
        });  
        //一级菜单改变的时候，二级菜单的变化  
        $s1.change(function () {  
            $s2.html("");  
            var s1SelectedVal = $s1.val();  
            //ajax异步获取二级菜单数据  
            $.ajax({  
                type: "GET",  
                url: settings.getSelectTwoValUrl,  
                data: settings.paramName + "=" + s1SelectedVal,  
                success: function (select2Val) {  
                    var selectTwoValJSON = $.parseJSON(select2Val);  
                    $.each(selectTwoValJSON, function (key, val) {  
                        appendOptionTo($s2, key, val);  
                    });  
                }  
            });  
        });  
    }  
})(jQuery);  
/** 
 * Tools Methods : appendOptionTo 
 * @param $obj : The selected object jquery，一般为需要添加option的select对象 
 * @param key : option的key，一般为设置的Id 
 * @param val ; option的val，同时一般也作为显示的值，在这里我们默认为显示的value和option的value是同一个值 
 * @param defaultSelectVal ; 设置默认选中的值，一般为初始化的情况下，默认选中的value 
 */  
function appendOptionTo($obj, key, val, defaultSelectVal) {  
    var $opt = $("<option>").text(key).val(val);  
    if (val == defaultSelectVal) {  
        $opt.attr("selected", "selected");  
    }  
    $opt.appendTo($obj);  
}