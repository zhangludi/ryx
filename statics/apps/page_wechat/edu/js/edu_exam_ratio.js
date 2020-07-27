apiready=function(){
	get_cms_exam_info();
}
//获取考试详情
function get_cms_exam_info(){
	showProgress();//加载等待
	var url = localStorage.url+"index.php/Api/Cms/get_cms_exam_info";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		"staff_no":localStorage.user_id,
	            "exam_id":api.pageParam.id,
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    		  	$('#exam_title').html(ret.exam_list.exam_title);//考试题目赋值
    		  	$('#staff_name').html(ret.exam_list.staff_name);//考试人名字赋值
    		  	$('#exam_point').html(ret.exam_list.exam_point);//总分赋值
    		  	$('#exam_date').html(ret.exam_list.exam_date);//考试时间赋值
    		  	$('#point_amount').html(ret.exam_list.point_amount);//总分赋值
    		  	$('#integral_amount').html(ret.exam_list.integral_amount);//积分赋值
    		  	$('#exam_time').html(ret.exam_list.exam_time);//时长
    		  	for(var i=0;i<ret.exam_list.exam_questions_data.length;i++){
    		  		if(ret.exam_list.exam_questions_data[i].questions_type=='单选'){
    		  			var arr=ret.exam_list.exam_questions_data[i].questions_item.split(',')
    		  			$('#list').append(
    		  				'<div class="bg-color-white mt-16">'+
								'<div class="fsize-12 uinn">'+
									'<div>'+
										'<span class="color-6"><span class="dot-number">'+(i+1)+'</span><span class="fcolor-red3 ml-5">【单选题】</span><span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_title+'<span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_point+'分</span></span>'+
									'</div>'+
									'<div>'+
										'<div class="aui-form" id="'+ret.exam_list.exam_questions_data[i].questions_id+'">'+
											
										'</div>'+
									'</div>'+
								'</div>'+
								'<div class="clearfix"></div>'+
								'<div class="fsize-14 ml-1b">'+
									'<span class="fcolor-66">已选:<span class="mr-10 hassel-f">'+ret.exam_list.exam_questions_data[i].my_question_item+'</span></span><span class="fcolor-66">正确答案:<span class="fcolor-red">'+ret.exam_list.exam_questions_data[i].questions_answer+'</span></span>'+
								'</div>'+
							'</div>'
    		  			)
    		  			for(var j=0;j<arr.length;j++){
    		  				$('#'+ret.exam_list.exam_questions_data[i].questions_id).append(
    		  					'<div class="so">'+
									'<input class="aui-radio ll" type="radio" name="demo'+i+'">'+
									'<span class="color-999 rr">'+arr[j]+'</span>'+
								'</div>'+
								'<div class="clearfix"></div>'
    		  				)
    		  			}
    		  		}else if(ret.exam_list.exam_questions_data[i].questions_type=='多选'){
    		  			$('#list').append(
    		  				'<div class="bg-color-white mt-16">'+
								'<div class="fsize-12 uinn">'+
									'<div>'+
										'<span class="color-6"><span class="dot-number">'+(i+1)+'</span><span class="fcolor-red3 ml-5">【多选题】</span><span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_title+'<span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_point+'分</span></span>'+
									'</div>'+
									'<div>'+
										'<div class="aui-form" id="'+ret.exam_list.exam_questions_data[i].questions_id+'">'+
											
										'</div>'+
									'</div>'+
								'</div>'+
								'<div class="clearfix"></div>'+
								'<div class="fsize-14 ml-1b">'+
									'<span class="fcolor-66">已选:<span class="mr-10 hassel-f">'+ret.exam_list.exam_questions_data[i].my_question_item+'</span></span><span class="fcolor-66">正确答案:<span class="fcolor-red">'+ret.exam_list.exam_questions_data[i].questions_answer+'</span></span>'+
								'</div>'+
							'</div>'
    		  			)
    		  			for(var j=0;j<arr.length;j++){
    		  				$('#'+ret.exam_list.exam_questions_data[i].questions_id).append(
    		  					'<div class="so">'+
									'<input class="aui-checkbox ll" type="checkbox" name="demo'+i+'">'+
									'<span class="color-999 rr">'+arr[j]+'</span>'+
								'</div>'+
								'<div class="clearfix"></div>'
    		  				)
    		  			}
    		  		}else if(ret.exam_list.exam_questions_data[i].questions_type=='判断'){
    		  			var arr=ret.exam_list.exam_questions_data[i].questions_item.split(',')
    		  			$('#list').append(
    		  				'<div class="bg-color-white mt-16">'+
								'<div class="fsize-12 uinn">'+
									'<div>'+
										'<span class="color-6"><span class="dot-number">'+(i+1)+'</span><span class="fcolor-red3 ml-5">【判断题】</span><span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_title+'<span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_point+'分</span></span>'+
									'</div>'+
									'<div>'+
										'<div class="aui-form" id="'+ret.exam_list.exam_questions_data[i].questions_id+'">'+
											
										'</div>'+
									'</div>'+
								'</div>'+
								'<div class="clearfix"></div>'+
								'<div class="fsize-14 ml-1b">'+
									'<span class="fcolor-66">已选:<span class="mr-10 hassel-f">'+ret.exam_list.exam_questions_data[i].my_question_item+'</span></span><span class="fcolor-66">正确答案:<span class="fcolor-red">'+ret.exam_list.exam_questions_data[i].questions_answer+'</span></span>'+
								'</div>'+
							'</div>'
    		  			)
    		  			for(var j=0;j<arr.length;j++){
    		  				$('#'+ret.exam_list.exam_questions_data[i].questions_id).append(
    		  					'<div class="so">'+
									'<input class="aui-radio ll" type="radio" name="demo'+i+'">'+
									'<span class="color-999 rr">'+arr[j]+'</span>'+
								'</div>'+
								'<div class="clearfix"></div>'
    		  				)
    		  			}
    		  		}else if(ret.exam_list.exam_questions_data[i].questions_type=='问答'){
    		  			$('#list').append(
    		  				'<div class="fsize-12 uinn bg-color-white mt-3">'+
								'<div class="color-6">'+
									'<span><span class="dot-number">5</span><span class="fcolor-red3 ml-5 mr-5">【问答题】</span>'+ret.exam_list.exam_questions_data[i].questions_title+'</span>'+
									'<span class="ml-5">'+ret.exam_list.exam_questions_data[i].questions_point+'分</span>'+
								'</div>'+
								'<textarea>已回答：'+ret.exam_list.exam_questions_data[i].my_question_item+'</textarea>'+
								'<div class="fcolor-66">正确答案:'+ret.exam_list.exam_questions_data[i].questions_answer+'</div>'+
							'</div>'
    		  			)
    		  		}
    		  	}
    		  	$('input,textarea').attr('disabled',true);
			}
			else{
				alert(ret.msg,"提示");
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}
//打开新页面
function openNewWin(winName) {
	api.openWin({
		name : winName,
		url : 'header/' + winName + '.html',
	});
}