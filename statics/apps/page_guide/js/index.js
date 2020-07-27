(function (doc, win) {
  var docEl = doc.documentElement,
    // 手机旋转事件,大部分手机浏览器都支持 onorientationchange 如果不支持，可以使用原始的 resize
      resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
      recalc = function () {
        //clientWidth: 获取对象可见内容的宽度，不包括滚动条，不包括边框
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        docEl.style.fontSize = 90*(clientWidth / 1920) + 'px';
      };
 
  recalc();
  //判断是否支持监听事件 ，不支持则停止
  if (!doc.addEventListener) return;
  //注册翻转事件
  win.addEventListener(resizeEvt, recalc, false);
 
})(document, window);

//图片经过
$(".index_bus_wechat_first img").mouseover(function(){
	$(".ewm").fadeIn();	
});
$(".index_bus_wechat_first img").mouseout(function(){
	$(".ewm").fadeOut();	
});
