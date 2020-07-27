 /* 需要注意代码顺序 ,jQuery调整表格宽度*/
$(function(){
	$("th").each(function(index){
		var th=$(this);
		var style=th.attr('style');
		if(style!='undefined'){
			var strs= new Array(); //定义一数组
			strs=style.split(";");
			$.each(strs, function (index, item) {
				var bool = item.indexOf("width");
				if(bool>=0){
					th.find('.th-inner').attr('style',item);
				}
            });
		}
	})
})