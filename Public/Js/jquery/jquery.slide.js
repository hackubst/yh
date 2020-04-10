(function($){	
	$.fn.advPlugin=function(set){
		var defaults={	
			auto:true,   			
			speed:500,	 			
			timeInterval:1000,		
			width:500,				
			height:300,				
			events:'hover',			
			hasBtn:true,			
			direction:false			
		};	
		var ops=$.extend({},defaults,set);
		var obj=this;
		//0903新增页面如果调用多个一样的轮播
		$(this).data('binded',true);
		
		var num=0;
		var total=obj.find('#wheel_smallArea >li').length-1;
		var bigArea=obj.find('#wheel_bigArea');
		var smallArea=obj.find('#wheel_smallArea');
		var autoPlay;
		var leftBtn=obj.find('#wheel_leftBtn');	//左边的点击按钮
		var rightBtn=obj.find('#wheel_rightBtn');	//右边的点击按钮
		smallArea.find('li:first').addClass('hover');
		if(ops.direction){
			bigArea.css('margin-left',-ops.width);
		}else{
			bigArea.find('li').css('float','none');
			bigArea.css({'width':ops.width,'height':9999,'marginTop':-ops.height});	
		}
		bigArea.find('li:last').clone().addClass('clone cloneT').prependTo(bigArea);
		bigArea.find('li:first').next().clone().addClass('clone cloneB').appendTo(bigArea);
		function playParameterLRTB(moveLRTB){
			if(num==total){
					smallArea.find('li').removeClass('hover');
					smallArea.find('li:first').addClass('hover');
					if(moveLRTB){
						bigArea.stop().animate({'marginLeft':-((total+2)*ops.width)},ops.speed,function(){
							bigArea.css('marginLeft',-ops.width);
						});	
					}else{
						bigArea.stop().animate({'marginTop':-((total+2)*ops.height)},ops.speed,function(){
							bigArea.css('marginTop',-ops.height);
						});		
					}
					return num=0;
				}else{
					smallArea.find('li:eq('+num+')').removeClass('hover');
					smallArea.find('li:eq('+(num+1)+')').addClass('hover');
					if(moveLRTB){
						bigArea.stop().animate({'marginLeft':-ops.width*(num+2)},ops.speed);	
					}else{
						bigArea.stop().animate({'marginTop':-ops.height*(num+2)},ops.speed);		
					}
					return num++;	
				}	
		}
		if(ops.auto){
			obj.hover(function(){			   
				clearInterval(autoPlay);			   
			},function(){
				autoPlay=setInterval(play,ops.timeInterval);	
			});
		}
		function directionLRTB(moveLRTB,blooseAuto){
			if(ops.events=='click'){
				smallArea.find('li').click(function(){
					var thisindex=smallArea.find('li').index(this);
					$(this).addClass('hover');
					$(this).siblings().removeClass('hover');
					if(moveLRTB){
						bigArea.stop().animate({'marginLeft':-ops.width*(thisindex+1)},ops.speed);	
					}else{
						bigArea.stop().animate({'marginTop':-ops.height*(thisindex+1)},ops.speed);		
					}
					return num=thisindex;
				});	
			}else{
				smallArea.find('li').hover(function(){							
					var thisindex=smallArea.find('li').index(this);
					//alert(thisindex);
					$(this).addClass('hover');
					$(this).siblings().removeClass('hover');
					if(moveLRTB){
						bigArea.stop().animate({'marginLeft':-ops.width*(thisindex+1)},ops.speed);	
					}else{
						bigArea.stop().animate({'marginTop':-ops.height*(thisindex+1)},ops.speed);	
					}
					return num=thisindex;
				},function(){
					return;
				});
			}	
		}
		function lrBtn(){
			leftBtn.click(function(){		   
				if(num==0){
					smallArea.find('li').removeClass('hover');
					smallArea.find('li:last').addClass('hover');
					if(ops.direction){
						bigArea.stop().animate({'marginLeft':0},ops.speed,function(){
							bigArea.css('marginLeft',-ops.width*(total+1));																						
						});	
					}else{
						bigArea.stop().animate({'marginTop':0},ops.speed,function(){
							bigArea.css('marginTop',-ops.height*(total+1));																						
						});	
					}
					return num=total;
				}else{
					smallArea.find('li').removeClass('hover');
					smallArea.find('li:eq('+(num-1)+')').addClass('hover');				   
					if(ops.direction){
						bigArea.stop().animate({'marginLeft':-ops.width*num},ops.speed);
					}else{
						bigArea.stop().animate({'marginTop':-ops.height*num},ops.speed);	
					}
					return num--;	
				}
			});
			rightBtn.click(function(moveLRTB){
				if(num==total){
					smallArea.find('li').removeClass('hover');
					smallArea.find('li:first').addClass('hover');
					if(ops.direction){
						bigArea.stop().animate({'marginLeft':-(total+2)*ops.width},ops.speed,function(){
							bigArea.css('marginLeft',-ops.width);	//margin-left=0
						});	
					}else{
						bigArea.stop().animate({'marginTop':-(total+2)*ops.height},ops.speed,function(){
							bigArea.css('marginTop',-ops.height);	//margin-left=0
						});		
					}
					return num=0;
				}else{
					smallArea.find('li').removeClass('hover');
					smallArea.find('li:eq('+(num+1)+')').addClass('hover');				   
					if(ops.direction){
						bigArea.stop().animate({'marginLeft':-ops.width*(num+2)},ops.speed);	
					}else{
						bigArea.stop().animate({'marginTop':-ops.height*(num+2)},ops.speed);	
					}
					return num++;
				}						
			});
		}
		if(ops.hasBtn){
			lrBtn();	
		}
		directionLRTB(ops.direction,ops.auto);
		function play(){
			playParameterLRTB(ops.direction);
		}
		if(ops.auto){
			autoPlay=setInterval(play,ops.timeInterval);
		}
		
	}
	
})(jQuery);