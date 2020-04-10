
function init_page(opt){
    var options = {
        page        : 1, // 初始页
        empty       : true, // 初始化时是否清空容器
        container   : '', // 父容器
        url         : window.location.href, // 请求链接
        data        : {}, // 附带参数
        parse       : function(_data){}, // 解析每个列表项的回调函数
    };
    $.extend(options, opt || {}); //合并配置项

    var _p          = options.page; // 分页码
    var is_continue = true; // 获取数据为空后，则终止之后的获取
    var _lock       = false; // 同时只会有一个请求。避免短时间内触发多个异步请求

    if (options.empty)
    {
        $(options.container).html('');
    }

    // 返回回调，调用一次则获取当前下一页的内容
    return function(end) {

        // 合并参数
        var _data = $.extend({p : _p}, options.data || {});

        if (is_continue) {
            // ajax 请求列表数据
            $.ajax({
                url         : options.url,
                data        : _data,
                type        : 'POST',
                dataType    : 'json',
                timeout     : 10*1000,
                beforeSend  : function(){
                    if (_lock)
                    {
                        return false;
                    }
                    _lock = true;
                },
                success     : function(data) {
                    if (data) {
                        _p++;
                        var html = '';
                        for(var k in data) {
                            html += options.parse(data[k]);
                        }
                        $(options.container).append(html);
                    } else {
                        is_continue = false;
                    }
                    if ($.isFunction(end))
                    {
                        end(is_continue);
                    }
                },
                complete    : function(){
                    _lock = false;
                }
            });
        }
    };
}