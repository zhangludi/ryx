(function($) {
	turnMethods = {
		init: function(options) {
			var _id=$(this).attr("id");
			var _url=options.url;
			if(options.param){
				var _param=options.param;
			}else{
				var _param={};
			}
			if(!_param.pageNum){
				_param.pageNum=1;
			}
			if(!_param.pageSize){
				_param.pageSize=20;
			}
			
			$(this).data("myid",_id);
			$(this).data("url",_url);
			$(this).data("param",_param)
			$.ui.loading(true,0);
			$(this).load(
				randomUrl(_url),
				_param,
				function(){
					$.ui.loading(false,0);
					binding(_id);
					//是否有回调
					if(options.callback){
						options.callback()
					}
				}
			);
			return this;
		},
		reload:function(){
			var _url=$(this).data("url");
			var _id=$(this).data("myid");
			var _param=$(this).data("param");
			$.ui.loading(true,0);
			$(this).load(
				randomUrl(_url),
				_param,
				function(){
					$.ui.loading(false,0);
					binding(_id);
				}
			);
		},
		next:function(){
			var _url=$(this).data("url");
			var _id=$(this).data("myid");
			$(this).data("param").pageNum=$(this).data("param").pageNum+1;
			$.ui.loading(true,0);
			$(this).load(
				randomUrl(_url),
				$(this).data("param"),
				function(){
					$.ui.loading(false,0);
					binding(_id);
				}
			);
		},
		prev:function(){
			var _url=$(this).data("url");
			var _pageNum=$(this).data("pageNum");
			$(this).data("param").pageNum=$(this).data("param").pageNum-1;
			$.ui.loading(true,0);
			$(this).load(
				randomUrl(_url),
				$(this).data("param"),
				function(){
					$.ui.loading(false,0);
					binding($(this).data("myid"));
				}
			);
		},
		go:function(pageNum){
			var _url=$(this).data("url");
			$(this).data("param").pageNum=pageNum;
			$.ui.loading(true,0);
			$(this).load(
				randomUrl(_url),
				$(this).data("param"),
				function(){
					$.ui.loading(false,0);
					binding($(this).data("myid"));
				}
			);
		},
		param:function(params){
			var newparam=$.extend($(this).data("param"),params);
			$(this).data("param",newparam);
			$.ui.loading(true,0);
			$(this).load(
				randomUrl($(this).data("url")),
				newparam,
				function(){
					$.ui.loading(false,0);
					binding($(this).data("myid"));
				}
			);
		}
	}
	
	function binding(id){
			var _id="#"+id;
			//更新数据
			$(_id+" .pageNext").click(function(){
				$(_id).page("next");
			});
			$(_id+" .pagePrev").click(function(){
				$(_id).page("prev");
			});
			$(_id+" .reload").click(function(){
				$(_id).page("reload");
			});
			$(_id+" .gopage").click(function(){
				var _gopage=$(this).attr("data-gopage");
				$(_id).page("go",_gopage);
			});
			//更新顶部的显示数据
			$_pageData=$(_id).find(".page-datas");
			var _total=$_pageData.attr("data-total");
			var _totalpage=$_pageData.attr("data-totalPage");
			$(_id+"_toppage").html('共<span class="red">'+_total+'</span>条数据');
			$(_id+"_toppage2").html('当前第<span class="red">'+$_pageData.attr("data-pagenum")+'/'+_totalpage+'</span>页');
	}
	
	function randomUrl(_url){
			var _url;
			if(_url.indexOf("?")!=-1){
				_url=_url+"&r="+Math.random();
			}else{
				_url=_url+"?r="+Math.random();
			}
			return _url;
	}
	
	function dec(that, methods, args) {
		if (!args[0] || typeof(args[0])=='object'){
			return methods.init.apply(that, args);
		}else if (methods[args[0]]){
			return methods[args[0]].apply(that, Array.prototype.slice.call(args, 1));
		}else{
			throw turnError(args[0] + ' is not a method or property');
		}
	}
	
	function turnError(message) {

	  function TurnJsError(message) {
	    this.name = "TurnJsError";
	    this.message = message;
	  }
	
	  TurnJsError.prototype = new Error();
	  TurnJsError.prototype.constructor = TurnJsError;
	  return new TurnJsError(message);
	
	}
	
	//page load方法
	$.extend($.fn, {
	  page: function() {
	    return dec($(this[0]), turnMethods, arguments);
	  }
	});

})(jQuery);




























//// 创建一个闭包
//(function($) {
//// 插件的定义 
//$.fn.page = function(options) {
//		if(typeof options=='string'){
//			options={"url":options};
//		}
//  	$this = $(this);
//  	var opts = $.extend({}, $.fn.page.defaults, options);
//  	var id=$(this).attr("id");
//  	//加载页面
//  	var gopage=function(){
//  		$("#"+id).load(opts.url,function(){
//  			binding();
//  		});
//  	};
//  	
//  	//重新加载
//  	var reload=function(){
//  		
//  	};
//  	//绑定事件
//  	var binding=function(){
//  			//下一页
//	    		$("#"+id+" .pageNext").click(function(){
//	    			
//	    		});
//	    		//上一页
//	    		$("#"+id+" .prevNext").click(function(){
//	    			
//	    		});
//	    		//跳转到指定页
//	    		$("#"+id+" .goPage").click(function(){
//	    			
//	    		});
//	    		//跳转到指定页
//	    		$("#"+id+" .firstPage").click(function(){
//	    			
//	    		});
//	    		//lastPage
//	    		$("#"+id+" .firstPage").click(function(){
//	    			
//	    		});
//  	};
//  	gopage();
//			//返回当前对象
//			var INSTANCE={
//				option:opts,
//				reload:function(){reload();}
//			};
//			return INSTANCE;//返回当前对象
//};  
//	  // 私有函数：debugging    
//	  function debug($obj) {
//	    if (window.console && window.console.log)    
//	    window.console.log('hilight selection count: ' + $obj.size());    
//	  };
//	// 插件的defaults    
//	$.fn.page.defaults = {};
//
//// 闭包结束    
//})(jQuery);