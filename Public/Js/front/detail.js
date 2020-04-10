// 商品详情页功能类
//var base="http://smartplant.pandorabox.mobi/"
SNTouch.Detail = function(){
    this.init.apply(this,arguments);
};
$.extend(SNTouch.Detail.prototype,{
    init:function(){
        var me=this;
        me.initReviewsTabs = false;
        me.hasDetailLoad = false;
        me.slider();
        me.detailTabs();
        me.radioGroup();
       // me.share();
       // me.favo();
      //  me.pictrues();
        
        if($(".tuanNow").length>0){
            me.tuan();
        }
        //me.fold();
        // 数量微调调用
       // $(".sn-count").each(function(){
        //    SUI.UI.ProCounter({
       //         uid: $(this)
       //     });
       // });
        
    },
    slider:function(){
        //商品大图拖动
        SNTouch.Widget.SNCarousel({
            hook : ".J_detail_slider",
            slideBox: ".sliderBox",
            effect: "scroll",
            counter:".navIcon",
            cycle: 0,
            autoplay: 0,
            asyncLoad: true,
            touch:true

        });
    },
    radioGroup:function(){
        var me = this;
        $(".J_radioGroup").each(function(i,item){
            var radios=$(item).find("input[type='radio']");
            var labels=$(item).find("label");
            
            labels.on("click mousedown",function(e){
            	$(this).not(".disabled").addClass("cur").siblings("label").removeClass("cur");
            }); 
            
            radios.change(function(ev){
                labels.removeClass("cur");
                $(ev.currentTarget).parent("label").addClass("cur");
                
            });
        });
    },
    
   /* req:function(index){
    	page[index]++;
		// 获取异步评论
	    $.ajax({
	        url: evalurl.replace(/{pageNum}/g,(page[index]-1)).replace(/{typeFlg}/g,type[index]),
	        dataType:"json",
	        async: true,
	        cache : false,
	        timeout:100000,
	        success:function(data){
	        	if(data.errorCode==""){
	        		$("#evaluateNum").html("评价("+data.totalRecords+")");
	        		// 更新  好评、中评、差评数量
	        		var array = new Array(data.goodEvaluate,data.midEvaluate,data.badEvaluate);
	        		$(".reviewsTabs>li:eq(0)").find("a").html("好评("+data.goodEvaluate+")");
	        		$(".reviewsTabs>li:eq(1)").find("a").html("中评("+data.midEvaluate+")");
	        		$(".reviewsTabs>li:eq(2)").find("a").html("差评("+data.badEvaluate+")");
	        		var html = $("#reviews_tmpl").tmpl(data);
	        		if(array[index] == 0 ){
	        			$('.reviewsList' + index).children('ul').html($("<li class='detail-no-result'>暂无评论</li>"));
	        		}else{
	        			$('.reviewsList' + index).find('ul').append(html); 	            	
	        			//下一页展示
	        			var evaluateType = new Array("goodEvaluate","midEvaluate","badEvaluate");
	        			var total = 0 ;
	        			if(type[index] == 1){
	        				total = data.goodEvaluate;
	        			}else if(type[index] == 2){
	        				total = data.midEvaluate;
	        			}else{
	        				total = data.badEvaluate;
	        			}
	        			if(parseInt(5*(page[index]-1)) < parseInt(total)){
	        				$('.reviewsList' + index).find("a").html("点击加载更多");
	        				$('.reviewsList' + index).children("div").css("display","block");
	        			}else{
	        				$('.reviewsList' + index).children("div").css("display","none");
	        			}
	        		}
	        	}else{
	        		page[index]--;
	        		
	        	}
	            
	        },
	        error:function(jqXHR, textStatus, errorThrown){
	        	page[index]--;
	        }
	    });
    },*/

    reviewsTabs:function(){
        var me=this;
        me.initReviewsTabs = true;
        var reviewsTabs = $(".reviewsTabs>li");
        reviewsTabs.bind("tap click",function(ev){
            var ind = $(ev.currentTarget).index(),
            curList = $('.reviewsList' + ind);
            curList.show().siblings().hide();
            reviewsTabs.removeClass("cur");
            $(ev.currentTarget).addClass("cur");
            if(page[ind]==1){
            	// 获取异步评论
            	me.req(ind);
            }
            return false;
        });
        
        

        $('.J_moreReview').bind('click', function(){
        	//更新加载状态
        	var loadMoreHtml = "<div class=\"sn-html5-loading vm\" style=\"margin:15px auto 0px auto;\"><div class=\"blueball\">"+
			   "</div><div class=\"orangeball\"></div></div>";
        	$(this).find("a").html(loadMoreHtml);
        	//$(this).find("a").html("正在努力加载<img src=\"/RES/wap/product/images/loading.gif\" style=\"vertical-align:middle;\">");
        	me.req($(this).parent().index());
        });
        // 激活第一个标签
        reviewsTabs.eq(0).click();
    },
    
    detailTabs:function(){
        var me=this;
        var detailTabs = $(".detailTabs>li"),
        detailContents = $(".detailContents>li");
        detailTabs.bind("tap click",function(ev){
            var index = $(ev.currentTarget).index();
            detailTabs.removeClass("cur");
            $(ev.currentTarget).addClass("cur");
            detailContents.hide();
            detailContents.eq(index).show();
            if(index == 2&&!me.initReviewsTabs){
                me.reviewsTabs();
            }
            if(index == 1&&!me.hasDetailLoad){
            	me.productDesc(detailContents[index]);
            	me.hasDetailLoad = true;
            }
            
            
            return false;
        });
    },
    
    productDesc:function(desc){
    	if(sn.isBook == true){
    		var url = descurl.replace(/{productId}/g, sn.productId).replace(/&amp;/g, '&');
    		var $heights=parseInt(document.documentElement.scrollHeight)+1500;
    		var ua=navigator.userAgent;
    		var $iframe = '<iframe width="750" class="detail-iframe" frameborder="no" border="0" height="'+$heights+'" scrolling="0" src="'+ url +'"></iframe>';    		
            var ifm=document.querySelector(".detail-iframe");

    		$(desc).html('<div class="detail-info">' + $iframe + '</div>');
    		
    		if(/Android (\d+\.\d+)/.test(ua)){
    			ifm.contents().find("body").css({
    				"font-size":"36px"
    			});
    		}
    	}else{
    		$.ajax({
	    		url: descurl,
	    		type: "GET",
	    		async: true,
	            success:function(html){
	            	if(html.trim().length == 0){
	            		$(desc).html("暂无数据");
	            	}else{
	            	    $(desc).html(html);
	            	}
	                $(desc).find("table").css("width","100%");
	                $(desc).find("object,embed,img").css({"maxWidth":"100%","height":"auto"});
	            }
    		});
    		
    	}
    		
    	
    	
    	
    	
    },
    
    share:function(){
        var me = this;
        $(".shareBtn").bind("touchstart click",function(){
            $("body").append('<div class="mask"></div>');
            if($(".shareBox").length>0){
                $(".shareBox").show();
            }else{
                $("body").append($("#shareBox_tmpl").tmpl());
                $(".shareBox").find(".closeBtn").bind("touchstart click",function(){
                    $(".shareBox").hide();
                    $(".mask").remove();
                    
                });
                shareWb();
            }
            var arrPageSizes = getPageSize();
			// Get page scroll
			var arrPageScroll = getPageScroll();
			// Calculate top and left offset for  div object and show it
			$(".shareBox").css({
				top:arrPageScroll[1] + (arrPageSizes[3] / 3)
			});
            //分享开始
            function shareWb()
            {
            	$(".sina").attr("href",'http://v.t.sina.com.cn/share/share.php?url='+_url+'&appkey=400813291&title='+_t+'&pic='+_pict);
            	
            	$(".kaixin").attr("href",'http://www.kaixin001.com/rest/records.php?url=' + _url + '&style=11&content=' + _t + '&pic='+ _pict + '&stime=&sig=');
            	$(".douban").attr("href",'http://www.douban.com/recommend/?url='+_url+'&title='+_t+'&comment='+encodeURI(_t));
            	$(".renren").attr("href",'http://widget.renren.com/dialog/share?resourceUrl='+_url+'&title='+encodeURI(_url)+'&description='+encodeURI(_t));
            	var _appkey = encodeURI('65e3731f449e42a484c25c668160b355');
            	var _site =encodeURI('http://www.suning.com');
            	var _u = 'http://v.t.qq.com/share/share.php?title='+_t+'&url='+_url+'&appkey='+_appkey+'&site='+_site+'&pic='+_pict;
            	$(".tengxun").attr("href",_u);
            	$(".souhu").attr("href",'http://t.sohu.com/third/post.jsp?&url='+_url+'&title='+_t+'&content=utf-8&pic='+_pict);
            	var p = {
            			url:_url,
            			desc:'',/*默认分享理由(可选)*/
            			summary:'',/*摘要(可选)*/
            			title:_t, /*分享标题(可选)*/
            			site:'苏宁易购',/*分享来源 如：腾讯网(可选)*/
            			pics:_pict /*分享图片的路径(可选)*/
            			};
            	var s = [];
            	for(var i in p){
            		s.push(i + '=' + encodeURIComponent(p[i]||''));
            	}
            	$(".qzone").attr("href",'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?'+s.join('&'));
            };
            
            
            return false;
        });   
        
		$(window).scroll(function() {
			// Get page sizes
			var arrPageSizes = getPageSize();
			// Get page scroll
			var arrPageScroll = getPageScroll();
			// Calculate top and left offset for  div object and show it
			$(".shareBox").css({
				top:arrPageScroll[1] + (arrPageSizes[3] / 3)
			});
		});

		function getPageSize() {
			var xScroll, yScroll;
			if (window.innerHeight && window.scrollMaxY) {	
				xScroll = window.innerWidth + window.scrollMaxX;
				yScroll = window.innerHeight + window.scrollMaxY;
			} else if (document.body.scrollHeight > document.body.offsetHeight){
				xScroll = document.body.scrollWidth;
				yScroll = document.body.scrollHeight;
			} else { 
				xScroll = document.body.offsetWidth;
				yScroll = document.body.offsetHeight;
			}
			var windowWidth, windowHeight;
			if (self.innerHeight) {
				if(document.documentElement.clientWidth){
					windowWidth = document.documentElement.clientWidth; 
				} else {
					windowWidth = self.innerWidth;
				}
				windowHeight = self.innerHeight;
			} else if (document.documentElement && document.documentElement.clientHeight) { 
				windowWidth = document.documentElement.clientWidth;
				windowHeight = document.documentElement.clientHeight;
			} else if (document.body) { 
				windowWidth = document.body.clientWidth;
				windowHeight = document.body.clientHeight;
			}	
			
			if(yScroll < windowHeight){
				pageHeight = windowHeight;
			} else { 
				pageHeight = yScroll;
			}
			
			if(xScroll < windowWidth){	
				pageWidth = xScroll;		
			} else {
				pageWidth = windowWidth;
			}
			arrayPageSize = [pageWidth,pageHeight,windowWidth,windowHeight];
			return arrayPageSize;
		};
		function getPageScroll() {
			var xScroll, yScroll;
			if (self.pageYOffset) {
				yScroll = self.pageYOffset;
				xScroll = self.pageXOffset;
			} else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
				yScroll = document.documentElement.scrollTop;
				xScroll = document.documentElement.scrollLeft;
			} else if (document.body) {// all other Explorers
				yScroll = document.body.scrollTop;
				xScroll = document.body.scrollLeft;	
			}
			arrayPageScroll = [xScroll,yScroll];
			return arrayPageScroll;
	    }
    },
    tuan:function(){
        var me=this;
        var timeSpan = $(".tuanNow .J_dateTime");
        var tuanTime = +timeSpan.attr("data-tuanTime");
        var serverTime = +timeSpan.attr("data-now");
        var delay = (new Date()).valueOf()-serverTime;
        setTime();


        function setTime(){
            var time = (new Date()).valueOf();
            var gap = tuanTime - (time - delay);
            var s = Math.floor(gap/1000)%60,
                m = Math.floor(gap/1000/60)%60,
                h = Math.floor(gap/1000/60/60)%24,
                d = Math.floor(gap/1000/60/60/24);

            timeSpan.html(d+"天"+h+":"+m+":"+s);
            if(gap>0){
                setTimeout(function(){setTime();},1000);
            }
        }

    },
   /* favo:function(){
        var me=this;
        var checkFavorUrl = base+"/favorite/private/checkIsFavorite.do?shopId="+
		sn.supplierCode+"&partnumber="+sn.productCode;
        
        initFavor(1);
     
        function initFavor(count){
        	$.ajax({
                url: checkFavorUrl,
                dataType:"json",
                type:"post",
                async:false,
                success:function(data){
                		if(data.returnCode ==0){
                    		sn.isBookMarked = data.bookmarkFlag;
                    		if(sn.isBookMarked == 1){
                             	 $("#favo").removeClass("favoBtn").addClass("favoBtn-oned");
                             }else if(sn.isBookMarked ==0){
                             	 $("#favo").removeClass("favoBtn-oned").addClass("favoBtn");
                             }
                		}
                },
                error:function(data){
                	if(count<2){
                		count = count + 1 ;
                		initFavor(count);
                	}
                }
            });
        }
        

        // ajax 设置 同步，防止收藏混乱
        $("#favo").bind("touchstart click",function(){
			if(sn.isBookMarked.length == 0){
	            $.ajax({
	                url: checkFavorUrl,
	                dataType:"json",
	                type:"post",
	                async:false,
	                success:function(data, textStatus, jqXHR){
	                		if(data.idsIntercepted){
	                			var targetUrl = window.location.href;
	                			var service = "https://m.suning.com/mts-web/auth?targetUrl="+targetUrl;
	                			window.location.href = "https://passport.suning.com/ids/login?service="+encodeURIComponent(service)+"&loginTheme=wap_new";
	                		}else if(data.returnCode ==0){
		                		sn.isBookMarked = data.bookmarkFlag;
		                		doFavor();
	                		}else{
	                			SUI.Use("AlertBox",{
	        	                    type: "mini",
	        	                    msg: '收藏失败'
	        	                });
	                		}
	                		
	                },
	                error:function(data){
	                	SUI.Use("AlertBox",{
    	                    type: "mini",
    	                    msg: '收藏失败'
    	                });
	                }
	            });
            }else{
            	doFavor();
            }
        });
        
        
        
        // 增加或取消收藏
        function doFavor(){
	    	  var favorUrl = "";
	    	  var msg = "";
	          if(sn.isBookMarked == 1){
	          	favorUrl = base+"/favorite/private/deleteGoodsFavorite.do?shopId="+
	            				sn.supplierCode+"&partnumber="+sn.productCode+
	            				"&channel=WAP";
	          	msg = "取消收藏";
	          }else{
	          	favorUrl = base+"/favorite/private/addGoodsFavorite.do?shopId="+
	  				sn.supplierCode+"&partnumber="+sn.productCode+
	  				"&channel=WAP";
	          	msg = "收藏";
	          }
	    	  $.ajax({
	            url: favorUrl,
	            dataType:"json",
	            type:"post",
	            async:false,
	            success:function(data, textStatus, jqXHR){
	            	if(data.idsIntercepted){
            			var targetUrl = window.location.href;
            			var service = "https://m.suning.com/mts-web/auth?targetUrl="+targetUrl;
            			window.location.href = "https://passport.suning.com/ids/login?service="+encodeURIComponent(service)+"&loginTheme=wap_new";
            		}else if(data.errorCode){
	            		msg = msg + "失败";
	            	}else if(data.returnCode ==0){
	            		if(sn.isBookMarked == 1){
		                	sn.isBookMarked = 0;
		                	$("#favo").removeClass("favoBtn-oned").addClass("favoBtn");
		                }else{
		                	sn.isBookMarked = 1;
		                	$("#favo").removeClass("favoBtn").addClass("favoBtn-oned");
		                }
	            		msg = msg + '成功';
	            	}else{
	            		msg = msg + '失败';
	            	}
	                SUI.Use("AlertBox",{
	                    type: "mini",
	                    msg: msg
	                });
	               
	            },
                error:function(data){
                	SUI.Use("AlertBox",{
	                    type: "mini",
	                    msg: '收藏失败'
	                });
                }
	         });
        }
    },
    fold:function(){
        var me=this;
        $(".J_fold").each(function(i,o){
            var wHeight=$(o).height();
            var cHeight=$(o).find("p").height();
            if(cHeight>wHeight){
                $(o).addClass("fold");
                $(o).find("i").click(function(){
                    if($(o).hasClass("fold")){
                        $(o).addClass("unfold");
                        $(o).removeClass("fold");
                    }else{
                        $(o).addClass("fold");
                        $(o).removeClass("unfold");
                    }
                });
            }else if(cHeight>14){
        		$(o).find("i").show();
        		$(o).css({"height":".56rem"});
        	}else{
        		$(o).find("i").hide();
        		$(o).height(".28rem");
        	}
        });
    },
    initProductInfo:function(){
    	 // 初始化 产品价格、库存、活动、送达时间等信息
    	var cityCode = $.cookie("cityId");
    	var districtCode = $.cookie("districtId");
    	var ajaxProductUrl = base+"/product/"+sn.productCode+"/"+cityCode+"/"+districtCode+"/"+sn.supplierCode+".html";
    	 $.ajax({
             url: ajaxProductUrl,
             dataType:"json",
             cache:true,
             success:function(data, textStatus, jqXHR){
                 var html = $("#price_tmpl").tmpl(data);
                 $('.priceArea').html(html); 
                 var shipHtml = $("#ship_tmpl").tmpl(data);
                 $("#shipArea").html(shipHtml); 
            	 var hasStorage = data.productInfo.hasStorage;
            	 var isCShop = data.productInfo.isCShop;
            	 var isPublished = data.productInfo.isPublished;
            	 //禁用加入购物车按钮
            	 if((isPublished == 'false'&& isCShop !='1')||hasStorage == 'Z'|| hasStorage == 'N'){
            		 $("#buyBtn").find("span.gray-detail-disable").show()
            		 .end().find("a.buyNow").hide().end().find("a.appendToCart").hide();
            		
            	 }else{
            		 $("#buyBtn").find("a.buyNow").show().end().find("a.appendToCart")
            		 .show().end().find("span.gray-detail-disable").hide();
            		
            	 }
            	 //设置去参加的抢购、团购的地址
            	 if(data.promotion){
            		 var url = "";
            		 if(data.promotion.promoteFlag==1){
                		 //url = '${tuan.url}'.replace(/{tuangouActId}/g,data.promotion.activityId);
                		 url = "/tuan/gateway/"+data.promotion.activityId+".html";
                	 }else if(data.promotion.promoteFlag==2){
                		 //url = '${qiang.url}'.replace(/{qianggouActId}/g,data.promotion.activityId);
                		 url = "/qiang/"+data.promotion.activityId+".html";
                	 }
            		 $(".t-go").attr("href",url);
            	 }
            	 
            	 // 数据埋点：
            	 snga = self.snga||{};
            	 if(data.productInfo.hasStorage == 'Z'){
            		 snga.productStatus = 3 ;
            		 snga.shipOffset = -2;
            	 }else if(data.productInfo.hasStorage == 'N'){
            		 snga.productStatus = 2 ;
            		 snga.shipOffset = -1;
            	 }else{
            		 snga.productStatus = 1 ;
            		 snga.shipOffset = data.productInfo.shipOffset;
            	 }
             }
         });
    },*/
    pictrues:function(){
        var me=this;
	   
    	$(".scroller li").live('click', function(e){
    		var i = $(this).index();
            e.preventDefault();
            e.stopPropagation();
            $.ajax({
                success:function(){

                    _set_interface(i);
                }
            });
        });
	  
	    	
	
	    
        function _set_interface(_index){
            $("body").append('<div class="mask-layers"></div>');
            $(".silder-insert-new").show();
            $("body").children(".silder-insert-new").addClass("silder-new-box");

            $(".mask-layers").show();

            $(".mask-layers").height(SUI.require("position").getHeight());

            $(".silder-insert-new").children(".sliderBox").children(".slide_ul").css({"left":$(window).width()*(-1)*(_index)});

            SNTouch.Widget.SNCarousel({
                hook : ".silder-insert-new",
                slideBox: ".sliderBox",
                effect: "scroll",
                counter:".navIcon",
                cycle: 0,
                autoplay: 0,
                asyncLoad: true,
                touch:true

            });
            var dataSrc = $(".silder-insert-new").find("li:eq("+_index+")").find('img').attr("data-src");
            $(".silder-insert-new").find("li:eq("+_index+")").find('img').attr("src",dataSrc);
            $(".navgator-dot").children("li:eq("+_index+")").addClass("cur").siblings("li").removeClass("cur");
            setPos();
            
            $(window).resize(function(){
                setPos();
            });
            $("body").children(".silder-new-box").children("h3").find("a").bind('click',function(){
                $(".silder-insert-new").hide();
                $(".mask-layers").remove();
            });

            function setPos(){
                var _scrollTop= SUI.require("position").scrollTop(), obj=$(".slide_ul").find("li").eq(_index);
                var _tops=_scrollTop + $(window).height()/2 -  obj.height()/ 2,
                    _lefts=$(document).width()/2 - obj.width()/2;
                //$("#navCarousel").find(".navIcon").find("li").removeClass("cur").eq(_index).addClass("cur");
                $(".silder-new-box ").css({
                    "position":"absolute",
                    "width":"100%",
                    "top":_tops,
                    "left":0
                });
            }
        }



    }
});

//执行区域
$(function(){
	var sn = self.sn||{};
	//通用方法
	SNTouch.init();
	var snDetail = new SNTouch.Detail();
	var initProduct = false;
	/*if($.cookie("cityId")){
		//snDetail.initProductInfo();
		 initProduct = true;
		 var hasStorage = sn.hasStorage;
	   	 var isCShop = sn.isCShop;
	   	 var isPublished = sn.isPublished;
	   	 //禁用加入购物车按钮
	   	 if((isPublished == 'false'&& isCShop !='1')||hasStorage == 'Z'|| hasStorage == 'N'){
	   		 $("#buyBtn").find("span.gray-detail-disable").show()
	   		 .end().find("a.buyNow").hide().end().find("a.appendToCart").hide();
	   	 }else{
	   		 $("#buyBtn").find("a.buyNow").show().end().find("a.appendToCart")
	   		 .show().end().find("span.gray-detail-disable").hide();
	   	 }
	}*/
	//snDetail.req(0);
	// 初始化购物车数量
	/*var num = (parseInt($.cookie('totalProdQtyv3'))||0);
	if(num>99){
		var cart = "<span><em>99<i>+</i></em></span>";
		$("#cartNum").html(cart);
	}else if(num>0){
		var cart = "<span><em>"+num+"</em></span>";
		$("#cartNum").html(cart);
	}
	
	
    SUI.load.opts.baseUrl = base + "/RES/wap/product/script/";
    var defaultCity={};
    defaultCity.distNo= $.cookie("districtId") || "11365";
    defaultCity.cityNo= $.cookie("cityId") || "9173";
    defaultCity.provinceCode= $.cookie("provinceCode") || '100';*/
    
    //cookie 过期时间
    var date = new Date();
    date.setTime(date.getTime() + (12*60*60*1000));
    
    var firstFlag = false;
   // if($.cookie("provinceCode")){
   // 	firstFlag = true;
   // }
    // 引入城市选择js
    
    // 调用方法
   /* SUI.load.require("getCity", function(getCity){
    getCity({
	        // 控件的id
	        uid: "#city1",
	        // 不经过地理位置选择的的城市信息,格式如下
	        defaultCity: defaultCity,
	        // 地理位置是否一天内只显示一次的标识参数， bool型
	        firstFlag: firstFlag,
	        //是否显示区
	        distShowFlag: true,
	        // 第一次地理位置获取城市执行的方法
	        getCityIdFirst: function(initCityHtml){
	            // 第一次打开ajax获取初始的城市信息
	            //var defaultCity = {"provinceName":"江苏","provinceCode":"100","cityNo":"9173","cityName":"南京","distNo":"11365","distName":"玄武区"};
	            initCityHtml(defaultCity);
	            
	        },
	        // 城市选中完毕执行方法 meta={cityNo: "9173", provinceCode: "100", distNo: "11365"}
	        done: function(meta){
	        	//$.cookie("cityId",meta.cityNo,{path: '/',domain:'.suning.com',expires: date});
	        //	$.cookie("provinceCode",meta.provinceCode,{path: '/',domain:'.suning.com',expires: date});
	        //	$.cookie("districtId",meta.distNo,{path: '/',domain:'.suning.com',expires: date});
	        	//if(initProduct == false){
	        		snDetail.initProductInfo();	        		
	        	//}else{
	        	//	initProduct=false;
	        	//}
	        }
    	});
    });*/
    
   
});

/**
 *  @商品详情导航列表固定
 */

(function(){
   new Object({
        _getEl: function(el){
            return document.querySelector(el);
        },
        _init: function(){
            var self = this,
                 el = self._getEl(".detailTabs"),
                 h = el.parentNode,
                 t = h.offsetTop;
            h.style.height = el.clientHeight  + "px";
            if(!self._supportSticky()){
                self._check(t);
                window.addEventListener("scroll", function(e) {
                    self._check(t);
                });
                window.addEventListener("orientationchange", function(e) {
                    self._check(t);
                });
            }
        },
       //检测是否支持属性
        _supportSticky: function(){
            var e = document.createElement("i"),
                n = "-webkit-sticky";
                e.style.position = n;
                var t = e.style.position;
                e = null;
                return t === n;
        },
       //滚动操作
        _check: function(h){
            this._getEl(".detailTabs").style.cssText =  window.scrollY > h ? "position:fixed;top:0;left:0;right:0;zIndex:998;" : "position:static";
        }
    })._init();

})();

