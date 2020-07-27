//table切换
$('#tabBox2').find('li').click(function(){
	$('#list').html('');
	$('#tabBox2').find('li').removeClass('tabBox2-active').find('.active').hide();
	$(this).addClass('tabBox2-active').find('.active').show();
	num = $(this).attr('num');
	if(num==1){
		$("#mine_list_mess").removeClass('di-n');
		$("#mine_party_live").addClass('di-n');
		$("#mine_list_tell").addClass('di-n');		
	}else if(num==2){
		$("#mine_party_live").addClass('di-n');
		$("#mine_list_mess").addClass('di-n');
		$("#mine_list_tell").removeClass('di-n');		
	}else if(num==3){
		$("#mine_party_live").removeClass('di-n');
		$("#mine_list_mess").addClass('di-n');
		$("#mine_list_tell").addClass('di-n');		
		
	}
	
});
