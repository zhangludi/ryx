apiready = function () {
	getConfig();//加载配置文件	
	getBdBannerList("bannerList",4,1);//获取banner
	getLifeVolunteerList();
	getBbsPostCat();//调用（获取热门论坛列表）
}

var slide = new auiSlide({
    container:document.getElementById("aui-slide"),
    // "width":300,
    "height":200,
    "speed":300,
    "autoPlay": 3000, //自动播放
    "pageShow":true,
    "pageStyle":'dot',
    "loop":true,
    'dotPosition':'center',
    
})
var slide3 = new auiSlide({
    container:document.getElementById("aui-slide3"),
    // "width":300,
    "height":240,
    "speed":500,
    "autoPlay": 3000, //自动播放
    "loop":true,
    "pageShow":true,
    "pageStyle":'line',
    'dotPosition':'center'
})


//刷新页面
function exec(){
	location.reload();
}


//打开精准扶贫
function openHelpwin(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_hr/hr_poorhelp/header/'+winName+'.html',
	    pageParam:{
	    	
	    }
    });
}

//打开村务论坛
function openBbs(winName,id){
	api.openWin({
	    name: winName,
	    url: '../../sp_life/life_bbs/header/'+winName+'.html',
	   pageParam:{
	    	id:id,	   	
	    }
    });   
  
}

//打开更多页面--------------------------------------------------------------
function openlearn_type_list(winName,type){
	api.openWin({
		name: winName,
		url: 'header/' + winName + '.html',
		pageParam:{
			id:api.pageParam.id,
			type:type,
		}
	})
}
//打开党务动态------------------------------------------------------------------
function openPartyWork(winName,type){
	api.openWin({
	    name: winName,
	    url: '../../sp_cms/cms_partyWork/header/'+winName+'.html',
	    pageParam:{
	    	type:type,
	    }
    });
}
//打开三务公开
function openThreeservices(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_life/life_threeservices/header/'+winName+'.html'
    });
}

//打开O2O
function openProblem(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_life/life_problem/header/'+winName+'.html',
	    pageParam:{
	    	
	    }
    });
}

//打开调查问卷
function openSurvey(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_life/life_survey/header/'+winName+'.html',
	    pageParam:{
	    	
	    }
    });
}

//打开投票
	function openVote(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_vote/header/'+winName+'.html',
	    });
	}

//获取志愿者服务列表接口--------------------------------------------------------------------
function getLifeVolunteerList(){
	var url = localStorage.url+"/index.php/Api/Life/get_life_volunteer_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            communist_no:localStorage.user_id,//登录人编号
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.data&&ret.data.length>0){
	    			for(var i=0;i<4;i++){
		    			$('#list').append(
								'<div class="pull-left w-168em h-150em box-3-df m-3em p-5em" onclick="openInfo(\'life_volunteer_info_header\','+ret.data[i].activity_id+','+ret.apply_status+')">'+
									'<img class="w-100b h-100em mb-8em bor-ra-5" src='+((ret.data[i].activity_thumb)?(localStorage.url+ret.data[i].activity_thumb):("../../../statics/images/images_life/life_recommend_bg.jpg"))+' alt="" />'+
									'<p class="f-12em lh-12em color-33 text-align f-w mb-10em">'+InterceptField(ret.data[i].activity_title,'无',10)+'</p>'+
									'<p class="f-12em lh-12em color-b3 text-align">'+InterceptField(ret.data[i].activity_description,'无',10)+'</p>'+
								'</div>'
		    			)
		    		}
	    		}
			}
			else{
				$("#list #more").remove();
				$("#list").append(
					'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无列表</div>'
				);	
    			api.hideProgress();//隐藏进度提示框
			}
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

//打开为你推荐详情-------------------------------------
function openInfo(winName,id){
	api.openWin({
	    name: winName,
	    url: '../life_volunteer/header/'+winName+'.html',
	    pageParam:{
	    	id:id,
	    }
    });
}
//轮播js
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
//村务论坛接口
function getBbsPostCat(){
	var url = localStorage.url+"/index.php/Api/Life/get_bbs_post_cat";
	api.ajax({
		url:url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: {
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){
				if(ret.data&&ret.data.length>0){
				//	getBbsPostList(ret.data[0].cat_id);
					for(var i=0;i<ret.data.length;i++){
						if(i<7){
							$('#list1').append(
								'<div class="life_cw pt-10em pull-left mr-12em mb-12em" onclick="openBbs(\'life_bbs_header\','+ret.data[i].cat_id+')">'+'<img class="w-20em h-17em m-a" src="../../../statics/images/images_life/life_icon.png">'+'<p class="w-100em f-12em color-21 text-align mt-0em">'+ret.data[i].cat_name+'</p>'+'</div>'
							)
						}else{
							$('#list1').append(
								'<div class="life_cw pt-10em pull-left mr-12em mb-12em" onclick="opt(this,'+ret.data[i].cat_id+')">'+
				'<img class="w-20em h-17em m-a" src="../../../statics/images/images_life/life_icon.png">'+
				'<p class="w-100em color-21 f-12em text-align mt-0em">'+ret.data[i].cat_name+'</p>'+
			'</div>'
							)
							return;
						}
						//$('#region li').eq(0).addClass('opt');
						//$('.topNum').css('top',$('#region').parent('div').css('height'));
					}
					
				}
			}else{
			 	
    		}
		}else{
			api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
		}
	});
}

			  
//打开调查问卷
	function openSurvey(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_survey/header/'+winName+'.html',
		    pageParam:{
		    	
		    }
	    });
	}
//打开投票
	function openVote(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_vote/header/'+winName+'.html',
	    });
	}
//打开留言建议
	function openMessage(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_message/header/'+winName+'.html',
		    pageParam:{
		    	
		    }
	    });
	}
//打开随手拍
	function openProblem(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_problem/header/'+winName+'.html',
		    pageParam:{
		    	
		    }
	    });
	}
//打开通讯录

function openPhone(){
	api.openSlidLayout({
		type:'left',
		slidPane:{
			name: 'com_phoneBook_right_header',
	        url: 'widget://html/sp_com/com_phonebook/header/com_phonebook_right_header.html',	        
		},
		fixedPane:{
			name: 'com_phonebook_left_header', 
		    url: 'widget://html/sp_com/com_phonebook/header/com_phonebook_left_header.html', 		   
		},
	}, function(ret, err) {
		
	});
}
//打开三务公开
	function openThreeservices(winName){
		api.openWin({
		    name: winName,
		    url: '../../sp_life/life_threeservices/header/'+winName+'.html'
	    });
	}
//打开扶贫政策更多列表面-----------------------------------------------------
function openList(winName,type){
	api.openWin({
        name: winName,
        url: '../../sp_hr/hr_poorhelp/header/'+winName+'.html',
        pageParam:{
        	type:type,
        }
    });
}