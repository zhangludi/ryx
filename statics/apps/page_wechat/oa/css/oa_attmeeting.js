apiready=function(){
	get_bd_type_list();
	oa_getBdBannerList();//获取banner
}
//获取banner图
function oa_getBdBannerList(id,num,type){
//id: 添加图片的盒子id; num: 模块对应banner编号; type: 显示多张轮播还是单张展示（1:、为轮播;其他、为单张）;
	var url = localStorage.url+"/index.php/Api/Publicize/get_bd_banner_list";
	api.ajax({
		url:url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: { 
				"location_id":7,
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){				
					if(ret.data&&ret.data.length>0){//图片列表存在并且有图片时
						for(var i=0;i<ret.data.length;i++){
							$('.swiper-wrapper').append(
						'<div class="swiper-slide swiper-slide-center none-effect">'+
							'<img src="'+localStorage.url+ret.data[i].ad_img+'">'+						
						'</div>'
							)
						}
						
						
					}
					
					var swiper = new Swiper('.swiper-container',{
							autoplay: false,
							speed: 1000,
							autoplayDisableOnInteraction: false,
							loop: true,
							centeredSlides: true,//设定为true时，active slide会居中，而不是默认状态下的居左。
							slidesPerView: 1.2,
							spaceBetween: 10,
							pagination: '.swiper-pagination',
							paginationClickable: true,
							onInit: function(swiper) {
							//初始化
								swiper.slides[2].className = "swiper-slide swiper-slide-active";
							},
							breakpoints: {
							//设置断点 //当宽度小于等于668 
				//				668: {
				//					slidesPerView: 1,
				//				}
							}
						});
				
			}
		}
	});
}



//获取会议类型
function get_bd_type_list(){	
	var url = localStorage.url+"/index.php/Api/Public/get_bd_type_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    	   communist_no: localStorage.user_id,//登录人员编号
	           type_group:"meeting_type",
	           type_no:[2000,2001, 2002, 2003, 2004],	 
 			   status:1,       
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){  
    		//获取会议类型
    			for(var i=0;i<ret.data.length;i++){
    			//有内容
    				if(ret.data[i].count==0){
    					
    				}else{
    					$('#type_list').append(
						'<div class="clearfix">'+
							'<div class="pull-left f-20em color-21 f-w">'+ret.data[i].type_name+'</div>'+
							'<div class="pull-right f-19 color-9e" onclick="openSignIn(\'oa_attmeeting_list_header\','+ret.data[i].type_no+')">查看更多</div>'+						
							'</div>'+	
						'</div>'+
						'<ul id="'+ret.data[i].type_no+'">'+		    				
			    		'</ul>'		
    					)
    				}
    			
    				
					get_oa_meeting_list(ret.data[i].type_no);
    			}	
			}
			else{
				$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无会议</div>'
					);	
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


//获取会议列表
function get_oa_meeting_list(type_noo){
	var url = localStorage.url+"/index.php/Api/Manage/get_oa_meeting_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            communist_no: localStorage.user_id,//登录人员编号
	            meeting_type:'',
	            status:1,//1:未开展 2:历史会议
	            page:1,//页数
	            pagesize:100//条数
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){     		
    			//获取会议列表
    			for(var k=0;k<ret.data.length;k++){      				
	    			if(ret.data[k].meeting_type==type_noo){	  
	    			//判断第一条和其他样式不同	1第一条
	    			if(ret.data[k].is_one==1){
	    			$("#"+type_noo).append(
		    			'<li class="bb-3em-de pb-10em mt-20em" onclick="openSignIn(\'oa_attmeeting_signIn_header\','+ret.data[k].meeting_no+')">'+
		    				'<img class="w-100b h-188em bor-ra-25em m-a" src="'+((ret.data[k].meetting_thumb!='')?localStorage.url+ret.data[k].meetting_thumb:'../../../statics/public/aui2/img/l4.png')+'">'+
		    				'<div class="color-21 f-18em mt-15em">'+InterceptField(ret.data[k].meeting_name,'无',10)+'</div>'+
		    				'<div class="clearfix mt-15em">'+
		    				'<div class="pull-left f-12em color-9e">'+
		    					'<span>'+((ret.data[k].party_name!=null)?ret.data[k].party_name:'无')+'</span>'+
		    					'<span class="ml-12em">'+ret.data[k].add_time+'</span>'+
		    				'</div>'+
		    				'<div class="pull-right f-12em color-ff3032">'+((ret.data[k].host_name!=null)?ret.data[k].host_name:'无')+'</div>'+
		    				'</div>'+
		    			'</li>'
		    			)
		    			
	    				
	    			}else{
		    		 	$("#"+type_noo).append(
		    		'<li class="clearfix pb-12em pt-12em" onclick="openSignIn(\'oa_attmeeting_signIn_header\','+ret.data[k].meeting_no+')">'+
		    			'<div class="pull-left w-97em h-73em">'+
		    			'<img class="bor-ra-2em w-97em h-73em" src="'+((ret.data[k].meetting_thumb!='')?localStorage.url+ret.data[k].meetting_thumb:'../../../statics/public/aui2/img/pic-list.png')+'">'+
		    			'</div>'+
		    			'<div class="pull-left over-h w-55b ml-13em bb-3em-e6 pb-12em">'+
		    				'<div class="pull-left mt-0em w-100b">'+
		    				' <div class="f-15em h-556em over-h color-21">'+InterceptField(ret.data[k].meeting_name,'无',10)+'</div>'+
		    				'<div class="color-9e f-12em">'+((ret.data[k].party_name!=null)?ret.data[k].party_name:'无')+'</div>'+
		    				' <div class="color-9e f-12em pull-left">'+ret.data[k].add_time+'</div>'+
		    				' <div class="color-a4 pull-right f-12em color-ff3032">'+((ret.data[k].host_name!=null)?ret.data[k].host_name:'无')+'</div>'+
		    				'</div>'+
		    			'</div>'+	    					    				
		    		'</li>'	
		    		)
		    			
	    			}

    					
	    			}
    			}
			}
			else{
				$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无会议</div>'
					);	
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


//打开新页面--------------------------------------------------------------------------------------
function openSignIn(winName,id) {
	api.openWin({
		name : winName,
		url : 'header/' + winName + '.html',
		pageParam:{
			id:id,
		}
	});
}

//刷新页面---------------------------------------------------------------------------------------
function exec(){
	location.reload();
}