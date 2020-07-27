$(function() {
	setRem();
	var x = document.documentElement.clientHeight;
	$(".sp_sign_box_left").css('height',x);
	$(".sp_sign_box_right").css('height',x);

	$(".sign_btn").on("click",function(){
		var element = document.getElementById("code").value
		if(element==''){
			alert('请输入正确的验证码')
			return false
		}
	})
})
//  设置rem
function setRem() {
	var innerWidth = window.innerWidth;
	var remUnit = innerWidth * 100 / 1902;
	$("html").css("font-size", remUnit + "px");
}
