//帮助中心左侧菜单折叠事件
$(function(){
	$(".j-hpct-list").click(function(){
		var $self=$(this), 
			$sub=$self.parent(".hpct-ll-list").siblings("dd").find(".hpct-ll-list-sub"),
			$icon=$self.find(".hpcticon");

		if($sub.length>0){
			if($sub.is(":visible")){
				$sub.hide();
				$icon.removeClass("arrowDown").addClass("arrowRight");
			}
			else{
				$sub.show();
				$icon.removeClass("arrowRight").addClass("arrowDown");
			}
		}

		return false;
	});
});