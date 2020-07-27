apiready = function(){
	edu_progress();//获取视频 课件 考试进度百分比
	welcome_you();//获取个人信息
	getEduMaterialVideo();//获取视频的接口
	$(".edu_special_tab li").eq(1).addClass("di-n");
	$(".edu_special_tab li").eq(3).addClass("di-n");
	$(".edu_special_tab li").eq(5).addClass("di-n");
	$(".edu_special_tab li").eq(7).addClass("di-n");
	//alert(api.pageParam.id)
	//监听事件
	api.addEventListener({
	    name: 'scrolltobottom',
	    extra:{
	        threshold:50   //设置距离底部多少距离时触发，默认值为0，数字类型
	    }
	}, function(ret, err) {
	    $(".top_bg").fadeOut(500);//隐藏背景
	   $(".edu_special_one").fadeOut(500);//隐藏白色写笔记
	   $(".edu_special_tit").fadeOut(500);//隐藏欢迎语
	   $(".edu_special_tab").animate({height:"0rem"},1000);//隐藏table切换
	   $(".edu_special_tab").removeClass("pt-12em").removeClass("pb-12em");
	   $(".edu_special_two").fadeIn(500);//显示白色header
	  $(".edu_special_con").animate({marginTop:"3.5rem"},1000);
	});
	//向下轻扫事件
	api.addEventListener({
	    name:'swipedown'
	}, function(ret, err){        
	    $(".top_bg").fadeIn();
	   $(".edu_special_one").fadeIn();
	   $(".edu_special_tit").fadeIn();
	   $(".edu_special_tab").fadeIn();
	   $(".edu_special_two").fadeOut();
	   $(".edu_special_tab").animate({height:"100%"},800);//隐藏table切换
	   $(".edu_special_tab").addClass("pt-12em").addClass("pb-12em");
	  $(".edu_special_con").animate({marginTop:"-2.75rem"},800);
	});
	//接收监听-提交学习笔记
  	api.addEventListener({
           name: 'myEvent_notes'
       }, function(ret, err) {
           if (ret.value.state == 'no') {
              getEduNotesList();
           }
       });

}
var is_exam='3';
var is_notes='';
//获取视频 课件 考试进度百分比
function edu_progress(){
//	showProgress();//加载等待
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_material_percent";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "communist_no":localStorage.user_id,
	            "topic_id":api.pageParam.id
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){  			
  				$('.video_progress').css("width",ret.data.video.learn_rate);//视频
  				$('.video_no').html(ret.data.video.learn);//视频已学习
  				$('.video_over').html(ret.data.video.residue);//视频剩余
  				$('.article_progress').css("width",ret.data.article.learn_rate);//课件
  				$('.article_no').html(ret.data.article.learn);//课件已学习
  				$('.article_over').html(ret.data.article.residue);//课件剩余
  				$('.exam_progress').css("width",ret.data.exam.learn_rate);//考试
  				$('.exam_no').html(ret.data.exam.learn);//考试已学习
  				$('.exam_over').html(ret.data.exam.residue);//考试剩余
  				$('.notes_progress').css("width",ret.data.notes.learn_rate);//笔记
  				$('.notes_no').html(ret.data.notes.learn);//笔记已学习
  				$('.notes_over').html(ret.data.notes.residue);//笔记剩余
  				
    		}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });

}

//获取个人信息
function welcome_you(){
//	showProgress();//加载等待
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_topic_data";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "communist_no":localStorage.user_id,
	            "topic_id":api.pageParam.id
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			$('#communist_con').html(ret.data);
    			$('#communist_con').fadeIn();
    		}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });

}

//获取专题课件的接口
function getEduMaterial(){
//$(".header_list_active").animate({marginLeft:"-2rem"});
$(".edu-more").addClass("di-n");
$(".edu_biji").addClass("di-n");
$(".edu_kaoshi").addClass("di-n");
$(".edu_video").addClass("di-n");
$(".edu_kejian").removeClass("di-n");
$(".edu_special_tab li").eq(0).addClass("di-n");
$(".edu_special_tab li").eq(1).removeClass("di-n");
$(".edu_special_tab li").eq(2).addClass("di-n");
$(".edu_special_tab li").eq(3).removeClass("di-n");
$(".edu_special_tab li").eq(4).removeClass("di-n");
$(".edu_special_tab li").eq(5).addClass("di-n");
$(".edu_special_tab li").eq(6).removeClass("di-n");
$(".edu_special_tab li").eq(7).addClass("di-n");
$('#list>div').remove();
$("#list").css('display','none'); 
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_material";
	api.ajax({
		url:url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: { 
				'type':11,
				'pagesize':7,   //获取文章数量
				'page':1,
				'topic_id':api.pageParam.id
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){
				if(ret.data&&ret.data.length>0){
			
					//$('#articleBox').removeClass('di-n');
					//添加专题课件
					for(var i=0;i<ret.data.length;i++){	
						$('#list').append(
							'<div>'+
						'<li class="clearfix w-93b va-jz over-h bb-1-f0 pb-12em pt-12em ml-12em mr-12em" onclick="openInfo(\'edu_learn_info_header\','+ret.data[i].material_id+')">'+
				'<div class="pull-left mr-12em w-130em h-72em over-h">'+
					'<img class="w-130em h-72em bor-ra-2em" src="'+((ret.data[i].material_thumb!=null)?localStorage.url+ret.data[i].material_thumb:"../../../statics/images/images_edu/edu_special_dow1.png")+'">'+
				'</div>'+
				'<div class="pull-left w-100b">'+
					'<div class="f-14em color-21">'+InterceptField(ret.data[i].material_title,'无标题',25)+'</div>'+
					'<div class="clearfix">'+
						'<div class="pull-left color-a4 f-11em">'+clearNull(ret.data[i].add_staff,'0')+'</div>'+
						'<div class="pull-right color-a4 f-11em">'+((ret.data[i].add_time).substring(0,10))+'</div>'+
					'</div>'+
				'</div>'+
			'</li>'+		
			'</div>'	
						)		
					}
					$("#list").fadeIn("slow");
					
				}else{
				
				}
			}else{
					$(".edu-more").removeClass("di-n");
				//$('#articleBox').addClass('di-n');
			}
		}else{
			api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
		}
	});
}


//获取视频的接口
function getEduMaterialVideo(){
$(".edu-more").addClass("di-n");
$(".edu_video").removeClass("di-n");
$(".edu_biji").addClass("di-n");
$(".edu_kaoshi").addClass("di-n");
$(".edu_kejian").addClass("di-n");
$(".edu_special_tab li").eq(0).removeClass("di-n");
$(".edu_special_tab li").eq(1).addClass("di-n");
$(".edu_special_tab li").eq(2).removeClass("di-n");
$(".edu_special_tab li").eq(3).addClass("di-n");
$(".edu_special_tab li").eq(4).removeClass("di-n");
$(".edu_special_tab li").eq(5).addClass("di-n");
$(".edu_special_tab li").eq(6).removeClass("di-n");
$(".edu_special_tab li").eq(7).addClass("di-n");
	$('#list>div').remove();
	$("#list").css('display','none'); 
	var url = localStorage.url+"/index.php/Api/Edu/get_edu_material";
	api.ajax({
		url:url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: { 
				'type':21,
				'pagesize':5,   //获取视频的数量
				'page':1,
				'topic_id':api.pageParam.id
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){				
				if(ret.data&&ret.data.length>0){
				$(".ske").addClass("di-n");				
				
    				//添加专题视频
					for(var i=0;i<ret.data.length;i++){					
						$('#list').append(
						'<div>'+
						'<li class="clearfix w-93b va-jz over-h bb-1-f0 pb-12em pt-12em ml-12em mr-12em" onclick="openInfo(\'edu_learn_videoInfo_header\','+ret.data[i].material_id+')">'+
				'<div class="pull-left mr-12em w-130em h-72em over-h">'+
					'<img class="w-130em h-72em bor-ra-2em" src="'+((ret.data[i].material_thumb!=null)?localStorage.url+ret.data[i].material_thumb:"../../../statics/images/images_edu/edu_special_video1.png")+'">'+
				'</div>'+
				'<div class="pull-left w-100b">'+
					'<div class="f-14em color-21">'+InterceptField(ret.data[i].material_title,'无标题',25)+'</div>'+
					'<div class="clearfix">'+
						'<div class="pull-left color-a4 f-11em">'+clearNull(ret.data[i].add_staff,'0')+'</div>'+
						'<div class="pull-right color-a4 f-11em">'+((ret.data[i].add_time).substring(0,10))+'</div>'+
					'</div>'+
				'</div>'+
			'</li>'+
						
'</div>'	
						)
					}
					$("#list").fadeIn("slow");
				}
			}else{
			
				$(".edu-more").removeClass("di-n");
				$(".ske").addClass("di-n");
			}
		}else{
			api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
		}
	});
}



//学习笔记的接口   视频笔记=1  课件笔记=2 全部或其他为空
function getEduNotesList(){
//$(".header_list_active").animate({marginLeft:"4rem"});
$(".edu_biji").removeClass("di-n");
$(".edu-more").addClass("di-n");
$(".edu_kaoshi").addClass("di-n");
$(".edu_kejian").addClass("di-n");
$(".edu_video").addClass("di-n");
$(".edu_special_tab li").eq(0).addClass("di-n");
$(".edu_special_tab li").eq(1).removeClass("di-n");
$(".edu_special_tab li").eq(2).removeClass("di-n");
$(".edu_special_tab li").eq(3).addClass("di-n");
$(".edu_special_tab li").eq(4).removeClass("di-n");
$(".edu_special_tab li").eq(5).addClass("di-n");
$(".edu_special_tab li").eq(6).addClass("di-n");
$(".edu_special_tab li").eq(7).removeClass("di-n");
$('#list>div').remove();
$("#list").css('display','none'); 
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
				"pagesize":6,
				"is_notes":is_notes,
			}
		}
	},function(ret,err){
		if(ret){
			if(ret.status==1){
				if(ret.data&&ret.data.length>0){
					for(var i=0;i<ret.data.length;i++){
						$('#list').append(
						'<div class="clearfix pl-12em pr-12em w-100b po-re" onclick="openScene_infowin(\'edu_special_twostudies_scene_info_header\','+ret.data[i].notes_id+')">'+
						'<div class="pull-left pl-12em w-20b pt-0em mt-5mem color-21">'+((ret.data[i].add_time).substring(0,10))+'</div>'+
						'<div class="edu_line pull-left h-100b hr-sr-de po-ab left-80em top-0em"></div>'+
						'<div class="edu-quan bg-color-ff4733 box-sha-3em w-12em h-12em po-ab top-0em left-75em bor-ra-50b"></div>'+
					'<div class="pull-right h-70em bor-ra-25em border-3em clearfix w-70b pl-8em va-jz mt-12mem mb-10em">'+
					'<div class="pull-left w-65b">'+
					'<div class="f-14em color-12">'+(InterceptField(ret.data[i].notes_title,'无',15))+'</div>'+
					'<div class="f-10em color-a4">'+(InterceptField(ret.data[i].notes_type,'无',10))+'</div>'+

						'</div>'+
						'<div class="pull-right">'+
						'<img class="w-70em h-52em bor-ra-25em over-h" src="'+((ret.data[i].notes_thumb!=null)?localStorage.url+ret.data[i].notes_thumb:"../../../statics/images/images_edu/edu_special_learn1.png")+'">'+
						'</div>'+
						'</div>'+
						'</div>'
						)
						//判断日常笔记  绿色
						if(ret.data[i].richang==1){
							$(".edu-quan").addClass("bg-color-02ca10");
							$(".edu-quan").css("box-shadow", "0rem 0.1rem 0.3rem 0rem rgba(2, 202, 16, 0.62)");
						}
						//判断类型 material_vedio空 文章  不空为视频
						if(ret.data[i].material_vedio==''){
							//文章 红色
						}else{
							//视频 蓝色
							$(".edu-quan").addClass("bg-color-376bf5");
							$(".edu-quan").css("box-shadow", "0rem 0.1rem 0.3rem 0rem rgba(55, 107, 245, 0.62)");
						}
						
					}
				$("#list").fadeIn("slow");
				}
			}else{
				$(".edu-more").removeClass("di-n");
			}
		}else{
			api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
		}
	});
}


//获取专题考试内容 //模拟考试3  没考1  考过2
function getEduExamList(){
//$(".header_list_active").animate({marginLeft:"1rem"});
$(".edu-more").addClass("di-n");
$(".edu_biji").addClass("di-n");
$(".edu_kejian").addClass("di-n");
$(".edu_video").addClass("di-n");
$(".edu_special_tab li").eq(0).addClass("di-n");
$(".edu_special_tab li").eq(1).removeClass("di-n");
$(".edu_special_tab li").eq(2).removeClass("di-n");
$(".edu_special_tab li").eq(3).addClass("di-n");
$(".edu_special_tab li").eq(4).addClass("di-n");
$(".edu_special_tab li").eq(5).removeClass("di-n");
$(".edu_special_tab li").eq(6).removeClass("di-n");
$(".edu_special_tab li").eq(7).addClass("di-n");
$('#list>div').remove();
$("#list").css('display','none'); 
	var url = localStorage.url+"/Api/Edu/get_edu_exam_list";
	api.ajax({
		url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		'communist_no':localStorage.user_id,
	           // 'topic_id':api.pageParam.id,
	            'is_exam':is_exam,
	            'page':1,//条数
				'pagesize':10,//页数
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    		
    		$(".edu_kaoshi").removeClass("di-n");
    			if(ret.data&&ret.data.length>0){
    				//$('#examBox').removeClass('di-n');
	    			for(var i=0;i<ret.data.length;i++){
	    				if(is_exam==1){
	    				$('#list').append(
	    				'<div>'+
						'<li class="clearfix w-93b va-jz over-h bb-1-f0 pb-12em pt-12em ml-12em mr-12em" onclick="openExamList(\'edu_exam_official\','+ret.data[i].exam_id+','+ret.data[i].exam_time+')">'+
				'<div class="pull-left mr-12em w-130em h-72em over-h">'+
					'<img class="w-130em h-72em bor-ra-2em" src="'+((ret.data[i].exam_thumb!=null)?localStorage.url+ret.data[i].exam_thumb:"../../../statics/images/images_edu/edu_special_video1.png")+'">'+
				'</div>'+
				'<div class="pull-left w-100b">'+
					'<div class="f-14em color-21">'+InterceptField(ret.data[i].exam_title,'无标题',25)+
					'</div>'+
					'<div class="clearfix">'+
'<div class="pull-left color-a4 f-11em">'+'时长'+clearNull(ret.data[i].exam_time,'0')+'/'+ret.data[i].questions_num+'大题'+'</div>'+
						'<div class="pull-right color-a4 f-11em">'+((ret.data[i].exam_date).substring(0,10))+'</div>'+
					'</div>'+
				'</div>'+
			'</li>'+
						
'</div>'
		  )
	    				}else if(is_exam==3){
	    				$('#list').append(
	    				'<div>'+
						'<li class="clearfix w-93b va-jz over-h bb-1-f0 pb-12em pt-12em ml-12em mr-12em" onclick="openExamList(\'edu_exam_info\','+ret.data[i].exam_id+')">'+
				'<div class="pull-left mr-12em w-130em h-72em over-h">'+
					'<img class="w-130em h-72em bor-ra-2em" src="'+((ret.data[i].exam_thumb!=null)?localStorage.url+ret.data[i].exam_thumb:"../../../statics/images/images_edu/edu_special_video1.png")+'">'+
				'</div>'+
				'<div class="pull-left w-100b">'+
					'<div class="f-14em color-21">'+InterceptField(ret.data[i].exam_title,'无标题',25)+
					'</div>'+
					'<div class="clearfix">'+
'<div class="pull-left color-a4 f-11em">'+'时长'+clearNull(ret.data[i].exam_time,'0')+'/'+ret.data[i].questions_num+'大题'+'</div>'+
						'<div class="pull-right color-a4 f-11em">'+((ret.data[i].exam_date).substring(0,10))+'</div>'+
					'</div>'+
				'</div>'+
			'</li>'+
						
'</div>'
		  )
	    				}else if(is_exam==2){
		  //$('#list div').remove();
		  	$('#list').append(
	    				'<div>'+
						'<li class="clearfix w-93b va-jz over-h bb-1-f0 pb-12em pt-12em ml-12em mr-12em" onclick="openExamList(\'edu_exam_attend\','+ret.data[i].exam_id+','+ret.data[i].exam_time+')">'+
				'<div class="pull-left mr-12em w-130em h-72em over-h">'+
					'<img class="w-130em h-72em bor-ra-2em" src="'+((ret.data[i].exam_thumb!=null)?localStorage.url+ret.data[i].exam_thumb:"../../../statics/images/images_edu/edu_special_video1.png")+'">'+
				'</div>'+
				'<div class="pull-left w-100b clearfix">'+
					'<div class="f-14em pull-left color-21">'+InterceptField(ret.data[i].exam_title,'无标题',25)+
					'</div>'+
					'<div class="f-8em text-center edu-kaoshi-bg pull-right  color-ff4040">'+InterceptField(ret.data[i].score,'无标题',25)+'分'+
					'</div>'+
					'<div class="clearfix">'+
						'<div class="pull-left color-a4 f-11em">'+'时长'+clearNull(ret.data[i].exam_time,'0')+'/'+ret.data[i].questions_num+'大题'+'</div>'+
						'<div class="pull-right color-a4 f-11em">'+((ret.data[i].exam_date).substring(0,10))+'</div>'+
					'</div>'+
				'</div>'+
			'</li>'+
						
'</div>'
		  )
		  }
		  
	    			}
	    			$("#list").fadeIn("slow");
	    		}
    			api.hideProgress();//隐藏进度提示框
    		}else{
    			$(".edu_kaoshi").removeClass("di-n");
    			$(".edu-more").removeClass("di-n");
    		}
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

$(".daily_notes").click(function(){
	$(".daily_notes_mask").removeClass("di-n");
})
//关闭遮罩层
$(".daily_mask_close").click(function(){
	api.closeWin({});
});

//提交日常笔记接口
function setEduNotes(){
	//获取上传列表id
	var notice_attach="";
	$("input[name='file_no']").each(function(){
	if(notice_attach==""){
		notice_attach=notice_attach+$(this).val()
	}else{
		notice_attach=notice_attach+","+$(this).val()
		}
	})
	var url = localStorage.url+"/index.php/Api/Edu/set_edu_notes";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "communist_no":localStorage.user_id,//提交人编号
	            "notes_title":$('#notes_title').val(),//笔记标题
	            "notes_type":3,//笔记类型
	            "topic_type":api.pageParam.id,//专题类型
	            "notes_content":$('#notes_content').val(),//笔记内容
	            "notes_thumb":notice_attach,//图片
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){    			
    			//发送监听
		         api.sendEvent({
		               name: 'myEvent_notes',
		               extra: {
		                   state: 'no'
		               }
		           });
				$(".daily_notes_mask").addClass("di-n");
				//alert('提交成功');
				//api.closeWin({});
			}
			else{
				api.toast({msg: ret.msg,duration:3000,location: 'top'});
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


//刷新页面---------------------------------------------------------------------------------
function exec(){
	location.reload();
}
$('#tabBox2').find('li').click(function(){
	$('#list').html('');
	$('#tabBox2').find('li').removeClass('tabBox2-active').find('.active').hide();
	$(this).addClass('tabBox2-active').find('.active').show();
	is_exam = $(this).attr('num');
	//page=1;
	getEduExamList();
})
$('#tabBox3').find('li').click(function(){
	$('#list').html('');
	$('#tabBox3').find('li').removeClass('tabBox2-active').find('.active').hide();
	$(this).addClass('tabBox2-active').find('.active').show();
	is_notes = $(this).attr('num');
	//page=1;
	getEduNotesList();
})
$('#tabBox4').find('li').click(function(){
	$('#list').html('');
	$('#tabBox4').find('li').removeClass('tabBox2-active').find('.active').hide();
	$(this).addClass('tabBox2-active').find('.active').show();
	is_num = $(this).attr('num');
	if(is_num==1){
		getEduMaterialVideo();
	}else if(is_num==2){
		getEduMaterial();
	}else if(is_num==3){
		getEduExamList();
	}else{
		getEduNotesList();
	}
})

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
//进入考试详情
function openExamList(winName, id, time) {
	api.openWin({
		name: winName,
		//url: './' + winName + '.html',
		url: '../edu_exam/' + winName + '.html',
		pageParam: {
			id:id,
			time:time
		}
	});
}
//打开学习笔记页面-------------------------------------------------------------
function openScene_infowin(winName,id){
	api.openWin({
		name: winName,
		url: 'header/' + winName + '.html',
		pageParam:{
			id:id,
		}
	})
}

//打开考试详情页面------------------------------------------------------------
function openExam_infowin(winName,type,no){
	api.openWin({
		name: winName,
		url: '../edu_exam/header/' + winName + '.html',
		pageParam:{
			type:type,
			no:no
		}
	})
}

//打开专题课件与专题视频详情页面------------------------------------------------------------
function openInfo(winName,type){
	api.openWin({
		name: winName,
		url: '../edu_learn/header/' + winName + '.html',
		pageParam:{
			type:type,
		}
	})
}
//打开写笔记页面------------------------------------------------------------
function open_daily_notes(winName,type){
	api.openWin({
		name: winName,
		url: '../edu_notes/header/' + winName + '.html',
		pageParam:{
			type:type,
		}
	})
}




