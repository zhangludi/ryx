
$("#tabBox2").css('width',"2000px");









function openInfo(winName){
	api.openWin({
	    name: winName,
	    url: 'header/'+winName+'.html',
    });
}
//党组织层级tab切换---------------------------------------
function active(This){
	$('#tabBox2 li').removeClass('tabBox2-active');
	$(This).addClass('tabBox2-active');
};

$('#tabBox2').css('left',0);
var xxx = [];
//------------------------------------------------------------
var tabBox = document.getElementById("tabBox2");
tabBox.addEventListener("touchmove",function(evt){
	evt.preventDefault();
	var touch=evt.touches[0];
	var xx=parseInt(touch.clientX);
	xxx.push(xx);
	var leftNum = $('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2));
	var changeNum = (xxx[xxx.length-1]-xxx[xxx.length-2]);
	$('#tabBox2').css('left',leftNum-(-changeNum));
},false)
document.addEventListener("touchend",function(evt){
	xxx = [];
	if($('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2))>0){
		$('#tabBox2').css('left',0);
	}
	if($('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2))<$(window).width()-$('#tabBox2').outerWidth(true)){
		$('#tabBox2').css('left',($(window).width()-$('#tabBox2').outerWidth(true)));
	}
},false)