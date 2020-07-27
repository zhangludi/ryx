
$(document).ready(function(){
    $(".header").load("./header.html");
    $(".footer").load("./footer.html");
    $(".slide_r_news").load("./ipam_home_slide_news.html");
    $(".career_slide").load("./ipam_home_career_slide.html");
    $(".news_slide").load("./ipam_home_news_slide.html");
    $(".special_slide").load("./ipam_home_special_slide.html");
    $(".study_slide").load("./ipam_home_study_slide.html");
    $(".userinfo_slide").load("./ipam_home_userinfo_slide.html");
    $(".meeting_slide").load("./ipam_home_meeting_slide.html");
});

// 轮播
var n = 0;
var index = 0;

$('.video_btn_left').click(function () {	
	$('.video_btn_right').css({"background": "#ff8638"});
	var ow=$(".special_video_list ul li").width();//一个li的宽度 180
	var len=$(".special_video_list ul li").length-3;//li的个数
	var ow_len=-(ow*len);
	if(n<=ow_len){
		$('.video_btn_left').css({"background": "#ccc"});
		return false;
	}else{
		$('.video_btn_left').css({"background": "#ff8638"});
	}
	
    n -= 210
    index++
    $('.special_video_list ul').animate({'left':n})
    $('.special_video_list ul li')
    .eq(index)
    .css('margin-top','1%')
    .find('p')
    .hide()
    .parent()
    .siblings()
    .css('margin-top','0')
    .find('p')
    .show()

})
$('.video_btn_right').click(function () {	
	$('.video_btn_left').css({"background": "#ff8638"});
	if($('.special_video_list ul').css('left')=='0px'){
		$('.video_btn_right').css({"background": "#ccc"});
		return false;
	}else{
		$('.video_btn_right').css({"background": "#ff8638"});
	}
	
    n += 210
    index--
    $('.special_video_list ul').animate({'left':n})
    $('.special_video_list ul li')
    .eq(index)
    .css('margin-top','1%')
    .find('p')
    .hide()
    .parent()
    .siblings()
    .css('margin-top','0')
    .find('p')
    .show()
})



/*********************************首页学习笔记js*****************************************************/
$(function () {
        initCalendar('hmonthdivtime', evn);//初始化日历组件
        //小圆点
//      $(".week-day-b li").append(
//      	'<span class="small_dots"></span>'
//      )
	
});
function evn(aa) {
    //alert(aa);
}





      