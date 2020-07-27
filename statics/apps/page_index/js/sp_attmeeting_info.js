//轮播图
layui.use('carousel', function(){
  var carousel = layui.carousel;
  //建造实例
  carousel.render({
    elem: '#test1'
    ,width: '100%' //设置容器宽度
   // ,autoplay:false
    ,height:'331px'
    ,arrow: 'none' //始终显示箭头
    //,anim: 'updown' //切换动画方式
  });
});


$(".edu_assort li").click(function(){
	$(this).addClass("edu_assort_active");
	$(".edu_assort li").not($(this)).removeClass("edu_assort_active");
})

//三会一课切换

        $(".slideTxtBox").slide({
        	prevCell:".prev",
        	nextCell:".next",
      	 trigger:"click",
      	 endFun:function(i,c){
       		$(".slideTxtBox .hd ul li.on").removeClass('di-n').siblings().addClass('di-n');

					}
        });
        

		(function () {
		 	 $(".slideTxtBox .hd ul li.on").removeClass('di-n').siblings().addClass('di-n');
		})(jQuery);
		
$(".edu_assort li").click(function(){
	$(this).addClass("edu_assort_active");
	$(".edu_assort li").not($(this)).removeClass("edu_assort_active");
})

//点击查看更多
$(".left_block2 .more").click(function(){
	$(".left_block2 ul").removeClass('over-h');	
	$(".left_block2 ul").height('100%');
})

