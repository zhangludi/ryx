apiready = function () {
	getConfig();//加载配置文件
	getEduTopicList();//获取专题课件
	//getEduNotesList();//获取学习笔记列表
	getEduNotesList();//获取学习实况
	ready_fun();
}

//专题课件
function getEduTopicList(){
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_topic_list";
	api.ajax({
		url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status){
    			if(ret.data&&ret.data.length>0){
	    			for(var i=0;i<ret.data.length;i++){
	    				var urlPath = ret.data[i].img_url;
	    				$('.swiper-wrapper').append(
						'<div class="swiper-slide">'+
						'<div class="shyk mt-20em over-h">'+	
						'<img class="w-100b h-180em over-h pl-5em pr-5em pt-5em bor-ra-5em" src="'+((urlPath!=null)?localStorage.url+urlPath:"../../../statics/images/images_edu/edu_two_learn.png")+'" class="main-img">'+
						'<div class="clearfix icon_normal pl-15em pr-15em pt-10em">'+
						'<div class="pull-left f-16em color-212121">'+clearNull(ret.data[i].topic_title,"无标题")+'</div>'+
						'<div class="pull-right">'+
								'<div class="pull-left">'+
									'<div class="circleChart pt-4em" id="circle1"></div>'+
								'</div>'+
								'<div class="pull-right f-12em color-9e pl-5em">'+
									'<div>已学习</div>'+
									'<div>50%</div>'+
								'</div>'+							
								'</div>'+			
								'</div>'+
							'</div>'+
						 '</div>'							
				    		)}
	    			
	    			//学习模块轮播图 js   
					var mySwiper = new Swiper('.swiper-container',{
						  slidesPerView : 'auto',
						  centeredSlides : true,
						  watchSlidesProgress: true,
						  pagination : '.swiper-pagination',
						  paginationClickable: true,
					
						  onProgress: function(swiper){
			//		        for (var i = 0; i < swiper.slides.length; i++){
			//		          var slide = swiper.slides[i];
			//		          var progress = slide.progress;
			//				  scale = 1 - Math.min(Math.abs(progress * 0.2), 1);
			//		        
			//		         es = slide.style;
			//				 es.opacity = 1 - Math.min(Math.abs(progress/2),1);
			//						es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = 'translate3d(0px,0,'+(-Math.abs(progress*150))+'px)';
			//		
			//		        }
					      },
					
					     onSetTransition: function(swiper, speed) {
					      	for (var i = 0; i < swiper.slides.length; i++) {
									es = swiper.slides[i].style;
									es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = speed + 'ms';
							}
					
					      }
			  });
			//学习模块轮播图end    			
	    	//圆形进度条效果
			$(".circleChart").circleChart({
				      color: "#ff404d",
					  backgroundColor: "#e2e2e2",//进度条之外颜色
					  background: true, // 是否显示进度条之外颜色
					  widthRatio: 0.2,//进度条宽度
					  size: 30,//圆形大小
				      value: 68,//进度条占比
				      startAngle: 180,//开始点
				      speed: 3000,
				      //text: true,//圆圈内文字
				      animation: "easeInOutCubic",
				      onDraw: function(el, circle) {
			       		 circle.text(Math.round(circle.value) + "%"); // 根据value修改text
			    	  }
			
			   });
			   //进度条效果结束			
	    		}
    		}else{
    			api.hideProgress();//隐藏进度提示框
	    		/**无数据提示 */
		    	$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无内容</div>'
					);	
    		}
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


//刷新页面
function exec(){
	location.reload();
}


//打开在线党校模块
//onclick="openLearn('edu_learn_header')"
function openLearn(winName,type){
	api.openWin({
	    name: winName,
	    url: '../edu_learn/header/'+winName+'.html',
	    pageParam:{
	    	"type":type
	    }
    });
}

//打开专题学习模块
//onclick="openSpecial('edu_special_header')"
function openSpecial(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_special/header/'+winName+'.html',
	    
    });
}

//打开考试中心模块
//onclick="openExam('edu_exam_header')"
function openExam(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_exam/header/'+winName+'.html',
	    
    });
}

//打开学习笔记模块
//onclick="openNotes('edu_notes_header')"
function openNotes(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_notes/header/'+winName+'.html',
	    
    });
}

//打开党员投稿模块
//onclick="openWriting('edu_writing_header')"
function openWriting(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_writing/header/'+winName+'.html',
	    
    });
}

//第一次进入页面获取学习笔记
function ready_fun(){
 //获取接口************************************************ 
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_notes_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "communist_no":localStorage.user_id,
	           // "keyword":$('#keyword').val(),
	            "page":page,
	            "pagesize":10,
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    		
    			if(ret.data&&ret.data.length>0){
	    			for(var i=0;i<ret.data.length;i++){	    			
		    			$('#testView').append(
		    				'<li class="over-h ml-12em" onclick="openInfo(\'edu_notes_info_header\','+ret.data[i].notes_id+')">'+
								'<div class="pull-left w-20b po-re pb-20em">'+
									'<p class="f-12em lh-24em color-9e">'+clearNull(ret.data[i].add_time,'0').substring(0,10)+'</p>'+
									'<img class="po-ab top-0 right-f13em wh-24em bor-ra-100b" src="../../../statics/images/images_edu/notes_time.png" alt="" />'+
								'</div>'+
								'<div class="pull-left bj_bj w-80b bl-1-dedede pt-8em pl-8em">'+
									'<p class="f-14em icon_medium color-212121 mb-5em pl-20em f-w">'+clearNull(ret.data[i].notes_title,'无标题')+'</p>'+
									'<p class="f-10em icon_medium fcolor-66 pb-8em pl-20em">['+clearNull(ret.data[i].topic_type,'其他')+']</p>'+
								'</div>'+
							'</li>'
		    			)}	
		    			$("#testView").fadeIn("slow");
		    	}
		    	$(".ske").addClass('di-n');
			}else{
			
			}			
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
	   
	   //获取接口结束******************************************************

}

//日历插件*******************************************
//执行一个laydate实例
var page=1;
laydate.render({
 	elem: '#test2'
 	,lang: 'cn'
  	,position: 'static'
 	,min: -4 //5天前
  	,max: 0 //7天后
    ,type: 'date' //默认，可不填
    ,format: 'M月d日' //日期组合方式 可任意组合
     //传入Date对象给初始值
	//,value: new Date(1534766888000) //参数即为：2018-08-20 20:08:08 的时间戳
	//,isInitValue: false //是否允许填充初始值，默认为 true
	// ,trigger: 'click' //如果绑定的元素非输入框，则默认事件为：click 采用click弹出
	,show: true //直接显示
	,position: 'static'
  	,showBottom: false//是否显示底部栏
 	 //,theme: '#393D49'//主题颜色
 	 //,calendar: true//是否显示公历节日
 	 // ,mark :''//标记重要日子
 	 /*以下是回调函数*/
 	 //一、控件初始打开的回调
 	  ,ready: function(date){
 	 	
 	  	//lay('#testView').html(1);//切换日期时显示的值	
 	    $(".layui-laydate-header").addClass('di-n');//隐藏header
 	  	$('.layui-laydate-content>table>thead>tr').hide();//隐藏周一到周日
 	  	//隐藏5天之外
 	  		if($(".layui-laydate-content tr td").hasClass('laydate-disabled')){
					$('.laydate-disabled').addClass('di-n');					
				}
 	  		//遍历td 添加月份
			$("table td").each(function(index,item){				
		    if($(this).hasClass("laydate-day-next")){
			    $(this).html((date.month+1)+'月'+$(this).text()+'日');//下月
			  }else if($(this).hasClass("laydate-day-prev")){
			      $(this).html((date.month-1)+'月'+$(this).text()+'日');//上月
			  }else{
			  	 $(this).html(date.month+'月'+$(this).text()+'日');//当前月份
			  }		    
		  });
		  ready_fun();//获取学习笔记
		
	   
 		 }
 	//二、日期时间被切换后的回调
	,change: function(value, date, endDate){
	$("#testView").hide();
	$(".ske").removeClass('di-n');//骨架屏
	//$(".layui-laydate .layui-this")
	 if($('#testView li').length>0){
		  $('#testView li').remove();
		 }
		 	//监听日期被切换
		 	//lay('#testView').html(value);//切换日期时显示的值		 	
		 	$(".layui-laydate-header").addClass('di-n');//隐藏header
 	  	$('.layui-laydate-content>table>thead>tr').hide();//隐藏周一到周日
 	  		if($(".layui-laydate-content tr td").hasClass('laydate-disabled')){
					$('.laydate-disabled').addClass('di-n');//隐藏5天之外
				}
				//遍历td 添加月份
			$("table td").each(function(index,item){				
		    if($(this).hasClass("laydate-day-next")){
			    $(this).html((date.month+1)+'月'+$(this).text()+'日');//下月
			  }else if($(this).hasClass("laydate-day-prev")){
			      $(this).html((date.month-1)+'月'+$(this).text()+'日');//上月
			  }else{
			  	 $(this).html(date.month+'月'+$(this).text()+'日');//当前月份
			  }		    
		  });

		 ready_fun();//获取学习笔记
		 	
	   
	  
	  }
			
			
	  
	});
//日历插件结束
//学习实况的接口
function getEduNotesList(){
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_notes_list";
	api.ajax({
		url:url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: { 
				'topic_id':api.pageParam.id,
				"communist_no":localStorage.user_id,
				"page":1,
				"pagesize":6
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){
				if(ret.data&&ret.data.length>0){
					$('#liveBox').removeClass('di-n');
					//添加学习实况
					for(var i=0;i<ret.data.length;i++){
						$('#notes_list').append(
							'<li class="va-jz over-h bb-1-e6e6e6 pb-12em pt-12em" onclick="openScene_infowin(\'edu_special_twostudies_scene_info_header\','+ret.data[i].notes_id+')">'+
								'<div class="pull-left w-97em h-73em">'+
									'<img class="w-97em h-73em bor-ra-2em" src='+((ret.data[i].notes_thumb!=null)?(localStorage.url+ret.data[i].notes_thumb):"../../../statics/images/images_edu/edu_special_learn1.png")+'>'+
								'</div>'+
								'<div class="pull-left over-h w-217em ml-13em icon_medium">'+									
				                    '<div class="pull-left w-217em mt-0em">'+
				                        '<div class="f-14em color-12 f-w">'+InterceptField(ret.data[i].notes_title,'无标题',15)+'</div>'+
				                        '<div class="color-a4 f-11em pt-4em">'+clearNull(ret.data[i].add_time,'0')+'</div>'+
				                        '<div class="color-a4 mt-4em f-12em">'+clearNull(ret.data[i].notes_type,'0')+'</div>'+
				                    '</div>'+
				               '</div>'+
							'</li>'

							
						)
					}
				
				}
			}else{
				$('#liveBox').addClass('di-n');
			}
		}else{
			api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
		}
	});
}
//打开详情页---------------------------------------------------------------
function openInfo(winName,type){
	api.openWin({
	    name: winName,
	    url: '../../../edu_notes/header/'+winName+".html",
	    pageParam:{
	    	type:type,
	    }
    });
}
//打开学习实况页面
function openScene_infowin(winName,id){
	api.openWin({
		name: winName,
		url: '../edu_special/header/' + winName + '.html',
		pageParam:{
			id:id,
		}
	})
}
