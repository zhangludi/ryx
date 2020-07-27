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


/**————————————————————————————————ztree插件——————————————————————————————————————**/
 var zTree;
 var demoIframe;
    var setting = {
    	data: {
    		simpleData: {
    			enable: true
    		}
    	}
    };

    var zNodes = [		
      {id: 1, pId: 0, name: "济南市委", open: true},
      {id: 11, pId: 1, name: "济南历下区委",open: true},
      {id: 111, pId: 11, name: "建筑新村街道工委", file: "core/simpleData"},
      {id: 1111, pId: 111, name: "解放路社区社区", file: "core/noline"},
	  
	  {id: 2, pId: 0, name: "济南天桥区委"},
      {id:21, pId:2, name:"济南天桥", open:true},
	  
	  {id: 3, pId: 0, name: "济南天桥区委"},
	  {id:31, pId:3, name:"济南天桥", open:true},
	  {id: 4, pId: 0, name: "济南长清区委"},
	  {id:41, pId:4, name:"济南长清区委", open:true},
	  {id: 5, pId: 0, name: "济南历城区委"},
	  {id:51, pId:5, name:"济南历城区委", open:true},
    ];

    $(document).ready(function () {
      var t = $("#tree");
      t = $.fn.zTree.init(t, setting, zNodes);
      demoIframe = $("#testIframe");
      demoIframe.bind("load", loadReady);
      var zTree = $.fn.zTree.getZTreeObj("tree");
      zTree.selectNode(zTree.getNodeByParam("id", 101));

    });

    function loadReady() {
      var bodyH = demoIframe.contents().find("body").get(0).scrollHeight,
        htmlH = demoIframe.contents().find("html").get(0).scrollHeight,
        maxH = Math.max(bodyH, htmlH), minH = Math.min(bodyH, htmlH),
        h = demoIframe.height() >= maxH ? minH : maxH;
      if (h < 530) h = 530;
      demoIframe.height(h);
    }