//table切换
$('#tabBox2').find('li').click(function(){
	$('#list').html('');
	$('#tabBox2').find('li').removeClass('tabBox2-active').find('.active').hide();
	$(this).addClass('tabBox2-active').find('.active').show();
	num = $(this).attr('num');
	if(num==1){
		//文章笔记
		$("#list_article").removeClass('di-n');
		$("#list_video").addClass('di-n');		
		$("#list_notes").addClass('di-n');
		$("#list_exam").addClass('di-n');
	}else if(num==2){
		//视频笔记
		$("#list_video").removeClass('di-n');
		$("#list_article").addClass('di-n');
		$("#list_notes").addClass('di-n');
		$("#list_exam").addClass('di-n');
	}else if(num==3){
		//学习笔记
		$("#list_notes").removeClass('di-n');
		$("#list_video").addClass('di-n');
		$("#list_article").addClass('di-n');		
		$("#list_exam").addClass('di-n');
	}else if(num==4){
		//考试中心
		$("#list_exam").removeClass('di-n');
		$("#list_notes").addClass('di-n');
		$("#list_video").addClass('di-n');
		$("#list_article").addClass('di-n');		
		
	}
	
})

//考试分类筛选切换
$('#typeBox').find('li').click(function(){
    // $('#examlist').html('');
    $(this).addClass('type-active color-active');
    $(this).siblings().removeClass('type-active color-active');
})