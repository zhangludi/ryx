/**————————————————————————————————适配——————————————————————————————————————**/
(function (doc, win) {
  var docEl = doc.documentElement,
    // 手机旋转事件,大部分手机浏览器都支持 onorientationchange 如果不支持，可以使用原始的 resize
      resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
      recalc = function () {
        //clientWidth: 获取对象可见内容的宽度，不包括滚动条，不包括边框
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        docEl.style.fontSize = 100*(clientWidth / 1920) + 'px';
      };
 
  recalc();
  //判断是否支持监听事件 ，不支持则停止
  if (!doc.addEventListener) return;
  //注册翻转事件
  win.addEventListener(resizeEvt, recalc, false);
 
})(document, window);

/**————————————————————————————————右侧(社区工作情况)进度条——————————————————————————————————————**/
layui.use('element', function(){
  var $ = layui.jquery
  ,element = layui.element;
});

/*——————————————————————————浏览器全屏判断——————————————————————————————————————————————*/
$(document).on('keydown', function (e) {
     var e = event || window.event || arguments.callee.caller.arguments[0];
     if(e && e.keyCode == 122){//捕捉F11键盘动作
       e.preventDefault();  //阻止F11默认动作
       var el = document.documentElement;
       var rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;//定义不同浏览器的全屏API　　　　　　//执行全屏
       if (typeof rfs != "undefined" && rfs) {
             rfs.call(el);
       } else if(typeof window.ActiveXObject != "undefined"){
             var wscript = new ActiveXObject("WScript.Shell");
             if (wscript!=null) {
                 wscript.SendKeys("{F11}");
             }
       }　　　　　　//监听不同浏览器的全屏事件，并件执行相应的代码
       document.addEventListener("webkitfullscreenchange", function() {//
           if (document.webkitIsFullScreen) {
                //全屏后要执行的代码
                $(".con_left").css('top','1rem');
				        $(".con_left_box2,.con_left_box3,.con_left_box4").addClass("mt-35em-cur");				
				        $(".con_center").css('top','0.2rem');
				        $(".netcom").css('top','59%');			        			        			        $(".circle_div").css('top','17%').css('width','6rem').css('height','6rem').css('marginLeft','-3rem');
				       $('head').append('<style>.circle .first:after{left:0.4rem;top:4rem} .circle .second:after{left:1.6rem;} .circle .third:after{left:5.45rem;} .circle .four:after{top:5.4rem;left:4.5rem}</style>');
							$(".circle .first p").css('left','0.15rem').css('top','3.8rem');
							$(".circle .second p").css('left','1.35rem');
							$(".circle .third p").css('left','5.2rem');
							$(".circle .four p").css('top','5.25rem').css('left','4.25rem');
				        $(".con_right").css('top','1rem');
				        $(".con_right_box2,.con_right_box3,.con_right_box4").addClass("mt-35em-cur");
           }else{
           	 //退出全屏后执行的代码
	        $(".con_left").css('top','0.3rem');
	        $(".con_left_box2,.con_left_box3,.con_left_box4").removeClass("mt-35em-cur");
	        $(".con_center").css('top','0.2rem');
	        $(".con_right").css('top','0.4rem');
	        $(".netcom").css('top','50%');
	        $(".circle_div").css('top','16%').css('width','5rem').css('height','5rem').css('marginLeft','-2.5rem');
	       $('head').append('<style>.circle .first:after{left:0.5rem;top:3.6rem} .circle .second:after{left:1.3rem;} .circle .third:after{left:4.6rem;} .circle .four:after{top:4.6rem;left:3.5rem}</style>');
	       		$(".circle .first p").css('left','0.25rem').css('top','3.45rem');
							$(".circle .second p").css('left','1.05rem');
							$(".circle .third p").css('left','4.4rem');
							$(".circle .four p").css('top','4.45rem').css('left','3.25rem');
	        $(".con_right_box2,.con_right_box3,.con_right_box4").removeClass("mt-35em-cur");
               
       　　}
       }, false);

       document.addEventListener("fullscreenchange", function() {
           if (document.fullscreen) {
                //全屏后要执行的代码
                $(".con_left").css('top','1rem');
				        $(".con_left_box2,.con_left_box3,.con_left_box4").addClass("mt-35em-cur");				
				        $(".con_center").css('top','0.2rem');
				        $(".netcom").css('top','59%');			        			        			        $(".circle_div").css('top','17%').css('width','6rem').css('height','6rem').css('marginLeft','-3rem');
				       $('head').append('<style>.circle .first:after{left:0.4rem;top:4rem} .circle .second:after{left:1.6rem;} .circle .third:after{left:5.45rem;} .circle .four:after{top:5.4rem;left:4.5rem}</style>');
							$(".circle .first p").css('left','0.15rem').css('top','3.8rem');
							$(".circle .second p").css('left','1.35rem');
							$(".circle .third p").css('left','5.2rem');
							$(".circle .four p").css('top','5.25rem').css('left','4.25rem');
				        $(".con_right").css('top','1rem');
				        $(".con_right_box2,.con_right_box3,.con_right_box4").addClass("mt-35em-cur");
           }else{
               //退出全屏后执行的代码
	        $(".con_left").css('top','0.3rem');
	        $(".con_left_box2,.con_left_box3,.con_left_box4").removeClass("mt-35em-cur");
	        $(".con_center").css('top','0.2rem');
	        $(".con_right").css('top','0.4rem');
	        $(".netcom").css('top','50%');
	        $(".circle_div").css('top','16%').css('width','5rem').css('height','5rem').css('marginLeft','-2.5rem');
	       $('head').append('<style>.circle .first:after{left:0.5rem;top:3.6rem} .circle .second:after{left:1.3rem;} .circle .third:after{left:4.6rem;} .circle .four:after{top:4.6rem;left:3.5rem}</style>');
	       		$(".circle .first p").css('left','0.25rem').css('top','3.45rem');
							$(".circle .second p").css('left','1.05rem');
							$(".circle .third p").css('left','4.4rem');
							$(".circle .four p").css('top','4.45rem').css('left','3.25rem');
	        $(".con_right_box2,.con_right_box3,.con_right_box4").removeClass("mt-35em-cur");
           }
       }, false);

       document.addEventListener("mozfullscreenchange", function() {
			if (document.mozFullScreen) {
                //全屏后要执行的代码
				$(".con_left").css('top','1rem');
				$(".con_left_box2,.con_left_box3,.con_left_box4").addClass("mt-35em-cur");				
				$(".con_center").css('top','0.2rem');
				$(".netcom").css('top','59%');			        			        			        $(".circle_div").css('top','17%').css('width','6rem').css('height','6rem').css('marginLeft','-3rem');
				$('head').append('<style>.circle .first:after{left:0.4rem;top:4rem} .circle .second:after{left:1.6rem;} .circle .third:after{left:5.45rem;} .circle .four:after{top:5.4rem;left:4.5rem}</style>');
				$(".circle .first p").css('left','0.15rem').css('top','3.8rem');
				$(".circle .second p").css('left','1.35rem');
				$(".circle .third p").css('left','5.2rem');
				$(".circle .four p").css('top','5.25rem').css('left','4.25rem');
				$(".con_right").css('top','1rem');
				$(".con_right_box2,.con_right_box3,.con_right_box4").addClass("mt-35em-cur");
			}else{
                //退出全屏后执行的代码
				$(".con_left").css('top','0.3rem');
				$(".con_left_box2,.con_left_box3,.con_left_box4").removeClass("mt-35em-cur");
				$(".con_center").css('top','0.2rem');
				$(".con_right").css('top','0.4rem');
				$(".netcom").css('top','50%');
				$(".circle_div").css('top','16%').css('width','5rem').css('height','5rem').css('marginLeft','-2.5rem');
				$('head').append('<style>.circle .first:after{left:0.5rem;top:3.6rem} .circle .second:after{left:1.3rem;} .circle .third:after{left:4.6rem;} .circle .four:after{top:4.6rem;left:3.5rem}</style>');
				$(".circle .first p").css('left','0.25rem').css('top','3.45rem');
				$(".circle .second p").css('left','1.05rem');
				$(".circle .third p").css('left','4.4rem');
				$(".circle .four p").css('top','4.45rem').css('left','3.25rem');
				$(".con_right_box2,.con_right_box3,.con_right_box4").removeClass("mt-35em-cur");
			}
       }, false);
       document.addEventListener("msfullscreenchange", function() {
           if (document.msFullscreenElement) {
               //全屏后要执行的代码
                $(".con_left").css('top','1rem');
				$(".con_left_box2,.con_left_box3,.con_left_box4").addClass("mt-35em-cur");				
				$(".con_center").css('top','0.2rem');
				$(".netcom").css('top','59%');			        			        			        $(".circle_div").css('top','17%').css('width','6rem').css('height','6rem').css('marginLeft','-3rem');
				$('head').append('<style>.circle .first:after{left:0.4rem;top:4rem} .circle .second:after{left:1.6rem;} .circle .third:after{left:5.45rem;} .circle .four:after{top:5.4rem;left:4.5rem}</style>');
				$(".circle .first p").css('left','0.15rem').css('top','3.8rem');
				$(".circle .second p").css('left','1.35rem');
				$(".circle .third p").css('left','5.2rem');
				$(".circle .four p").css('top','5.25rem').css('left','4.25rem');
				$(".con_right").css('top','1rem');
				$(".con_right_box2,.con_right_box3,.con_right_box4").addClass("mt-35em-cur");
			}else{
				   //退出全屏后执行的代码
				$(".con_left").css('top','0.3rem');
				$(".con_left_box2,.con_left_box3,.con_left_box4").removeClass("mt-35em-cur");
				$(".con_center").css('top','0.2rem');
				$(".con_right").css('top','0.4rem');
				$(".netcom").css('top','50%');
				$(".circle_div").css('top','16%').css('width','5rem').css('height','5rem').css('marginLeft','-2.5rem');
				$('head').append('<style>.circle .first:after{left:0.5rem;top:3.6rem} .circle .second:after{left:1.3rem;} .circle .third:after{left:4.6rem;} .circle .four:after{top:4.6rem;left:3.5rem}</style>');
				$(".circle .first p").css('left','0.25rem').css('top','3.45rem');
				$(".circle .second p").css('left','1.05rem');
				$(".circle .third p").css('left','4.4rem');
				$(".circle .four p").css('top','4.45rem').css('left','3.25rem');
				$(".con_right_box2,.con_right_box3,.con_right_box4").removeClass("mt-35em-cur");
		   }
       }, false)
    }
})





