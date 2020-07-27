jQuery("#demo1").slide({ mainCell:".bd ul",effect:"top",autoPlay:true,triggerTime:0 });


$(".edu_assort li").click(function(){
	$(this).addClass("edu_assort_active");
	$(".edu_assort li").not($(this)).removeClass("edu_assort_active");
})

