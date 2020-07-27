




















//选择题选项
	$('.opt').parents('li').click(function(){
		$('.opt').css('display','none');
		$(this).find('span .opt').css('display','block')
	})
//结束


//提交效果
	$('#submit').click(function(){
		$('.masking').css('height',$(window).height());
		$('.alert-div').css('display','block')
	})
//结束




