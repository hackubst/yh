$(function() {
    //加载页面时，如果window宽度小于1230，自动切换至固定模式
    if ($(window).width() < 1220) {
        acp.ui_ctrl_changeLayout("fixed"); //切换为固定
    }

    //导航栏控制左侧菜单的显示隐藏
    $(".j-togggle-sidebar").bind("click", function() {
        var $sidebar = $("#sidebar");

        if ($sidebar.is(":visible")) {
            $sidebar.hide();
            $("#main").css("marginLeft", 0);
        } else {
            $sidebar.show();
            $("#main").css("marginLeft", 200);
        }

        return false;
    });

    //左侧菜单的二级切换
    $(".j-togglemenu").bind("click", function() {
        var $self = $(this),
            $dds = $self.parent("dt").siblings("dd"),
            speed = 200;

        //切换箭头图标
        $self.find("i").toggleClass("ic-menuarrow-right ic-menuarrow-down");

        //切换二级菜单的可见性
        if ($dds.is(":visible")) {
            $dds.slideUp(speed);
        } else {
            $dds.slideDown(speed);
        }

        return false;
    });

    //导航的二级菜单切换
    $(".j-navsub-toggle").bind("click", function() {
        var $self = $(this), //当前点击导航的一级
            $selfSub = $(this).siblings(".nav-list-sub"), //当前对应导航的二级
            $sbls = $self.parent(".nav-list").siblings(".nav-list"), //相邻的导航
            $sblsMain = $sbls.find(".nav-list-main"), //对应的相邻导航的一级
            $sblsSub = $sbls.find(".nav-list-sub"); //对应的相邻导航的二级

        //重置相邻导航的样式
        $sblsMain.removeClass("selected");
        $sblsSub.hide();

        //模拟当前导航的toggle事件
        if ($selfSub.is(":visible")) {
            $self.removeClass("selected");
            $selfSub.hide();
        } else {
            $self.addClass("selected");
            $selfSub.show();
        }

        //点击body隐藏所有二级导航
        var clkBodyToHide = function() {
            $(".nav-list-main").removeClass("selected");
            $(".nav-list-sub").hide();
            $("body").unbind("click", clkBodyToHide);
        }

        //绑定点击body隐藏所有二级导航事件
        $("body").bind("click", clkBodyToHide)

        return false;
    });

    //布局方式切换
    $(".j-layoutCtrl").click(function(event) {
        var type = $(this).data("type");
        acp.ui_ctrl_changeLayout(type);
        return false;
    });

    //导航栏固定方式切换
    $(".j-navCtrl").click(function() {
        var type = $(this).data("type");
        acp.ui_ctrl_changeNavPos(type);
        return false;
    });

    //隐藏面包屑导航
    $(".j-closebrdc").bind("click", function() {
        $(this).parent(".breadcrumbs").fadeOut(500);
    });

    //监听页面的宽度，小于1220时启用固定模式
    $(window).resize(function() {
        if ($(this).width() < 1220) {
            acp.ui_ctrl_changeLayout("fixed"); //切换布局为固定模式
            acp.ui_ctrl_changeNavPos("default");//切换头部为默认模式
        }
    });

    //ie6 hack
    if ($.browser.msie && parseInt($.browser.version) <= 6) {
        $(".ie6hide").hide(); //IE6隐藏导航栏固定方式切换dom
        //IE6下图片上传列表的hack
        $(".fi-imgslist-item").hover(function() {
            var $self = $(this);
            $self.find(".del").show();
            $self.find(".change").show();
        }, function() {
            var $self = $(this);
            $self.find(".del").hide();
            $self.find(".change").hide();
        });
    }

});
//全局变量acp
acp = {
    //切换导航固定方式
    ui_ctrl_changeNavPos: function(type) {
        var $nav = $("#j-nav"),
            $container = $("#j-cont-ly");

        if (type == "" || type == undefined) {
            type = "default";
        }

        $.cookie("ui_navPosMod",type,{expires:30,path:'/'});//设置导航固定模式cookie

        switch (type) {
            case "default":
                $nav.removeClass("fixed");
                $container.removeClass("pdt40");
                break;
            case "fixed":
                $nav.addClass("fixed");
                $container.addClass("pdt40");
                break;
        }

        $(".j-navCtrl").find("i").addClass("white");
        $(".j-navCtrl[data-type=" + type + "]").find("i").removeClass("white");
    },
    //切换页面布局
    ui_ctrl_changeLayout: function(type) {
        var $layout = $(".layout");

        if (type == "" || type == undefined) {
            type = "fluid";
        }

        $.cookie("ui_layoutMod",type,{expires:30,path:'/'});//设置页面布局模式cookie

        switch (type) {
            case "fixed":
                $layout.addClass("fixed");
                break;
            case "fluid":
                $layout.removeClass("fixed");
                break;
        }

        $(".j-layoutCtrl").find("i").addClass("white");
        $(".j-layoutCtrl[data-type=" + type + "]").find("i").removeClass("white");
    },
    //显示表单验证出错状态
    form_ShowError: function(error, element) {
        var msg = error.text(),
            type = element.attr("type"); //表单类型

        if (type == "checkbox" || type == "radio") {
            element.parent().parent().siblings('.fi-help-text').text(msg).addClass('error').removeClass('hide');
        } else {
            element.siblings('.fi-help-text').text(msg).addClass('error').removeClass('hide');
        }
    },
    //显示表单验证出错状态
    form_HideError: function(element) {
        var type = element.attr("type"); //表单类型
        if (type == "checkbox" || type == "radio") {
            element.parent().parent().siblings('.fi-help-text').text("").removeClass('error').addClass('hide');
        } else {
            element.siblings('.fi-help-text').text("").removeClass('error').addClass('hide');
        }
    },
    /*
     * 更新进度条
     * @Author  chenjie
     * @param selector 进度条选择器
     * @param percent 要更新的进度
     */
    updateProgress:function(selector,percent){
        $(selector).find(".bar").css("width",percent+"%");
    },
    //选项卡表单验证时用来存储tabs的origin
    tabsFromOrigin:"test",
    //转换json对象成二维数组(for zgq)
    cvJsonToArray: function(obj) {
        var i = 0,
            len = obj.length,
            key,
            temp = [],
            arr = [];

        if (typeof obj == "string") {
            obj = $.parseJSON(obj);
            len = obj.length;
        }

        for (; i < len; i++) {
            temp.push(obj[i].name);
            temp.push(parseInt(obj[i].num));
            arr.push(temp.slice(0));
            temp.length = 0;
        }
        return arr;
    }
}

/**
 * 全选
 * @user  Zy
 * @param optionname 标签名称，默认为“checkIds[]”
 */

    function selall(optionname) {
        optionname = typeof(optionname) == "undefined" ? 'checkIds[]' : optionname;
        var my_object = document.getElementsByName(optionname);
        for (i = 0; i < my_object.length; i++) {
            my_object[i].checked = true;
        }
    }

    /**
     * 取消全选
     * @user  Zy
     * @param optionname 标签名称，默认为“checkIds[]”
     */

    function clearall(optionname) {
        optionname = typeof(optionname) == "undefined" ? 'checkIds[]' : optionname;
        var my_object = document.getElementsByName(optionname);
        for (i = 0; i < my_object.length; i++) {
            my_object[i].checked = false;
        }
    }


    /**
     * ACP后台所有的省级联动JS
     * @returns void
     */
    $(function() {
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

function change_city(province_id, city_id) {
    $('#area_id').html("<option value='0'>--选择地区--</option>");
    $.post('/Area/get_city_list', {
        "province_id": province_id
    }, function(data) {
        if (!data) {
            return false;
        }
        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">--选择城市--</option>';
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

function change_area(city_id, area_id) {
    $.post('/Area/get_area_list', {
        "city_id": city_id
    }, function(data) {
        if (data == null) {
            $('#area_id').html('<option value="0">--选择地区--</option>');
            $('#div_area').css('display', 'none');
            return;
        } else {
            $('#div_area').css('display', '');
        }

        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">--选择地区--</option>';
        for (i = 0; i < length; i++) {
            str += '<option value="' + result[i]['area_id'] + '">' + result[i]['area_name'] + '</option>';
        }

        $('#area_id').html(str);
        if (area_id) {
            $('#area_id option[value=' + area_id + ']').attr('selected', 'selected');
            $('#a_mark').html($('#area_id').find('option:selected').text()); //更改文本显示
        }
    }, 'json');
}
