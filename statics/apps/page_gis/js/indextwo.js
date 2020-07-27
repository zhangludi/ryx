$(function(){
	y=document.documentElement.scrollHeight
	$('.sp_map_left').css("height",y);
	var x=1
	$(".sp_map_left_anmit").on("click",function(){
	$(".icon_fangx").toggle()
	$(".icon_fangx1").toggle()
	if(x==1){
		$(".sp_map_left").stop().animate({left:"-4rem"});
			x=2
		}else{
			$(".sp_map_left").stop().animate({left:"0rem"});
			x=1
		}
	})
	$(window).resize(function() {
		x=$('body').height()
		y=document.documentElement.scrollHeight
		console.log(x)
		console.log(y)
		$('.sp_map_left').css("height",y);
	})
})