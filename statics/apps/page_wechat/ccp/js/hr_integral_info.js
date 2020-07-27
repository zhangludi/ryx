apiready = function(){
//	alert(api.pageParam.type);
	$('#tabBox2 li').removeClass('tabBox2-active');
	$('#tabBox2 li').eq(api.pageParam.type-1).addClass('tabBox2-active');
}
var CalculateLength = $('#list').find('li').length;
for(var CalculateNum = 0;CalculateNum<CalculateLength;CalculateNum++){
	$('#list li').eq(CalculateNum).css('width',CalculateWidth($('#list li').eq(CalculateNum).find('.num').html()));
	if($('#list li').eq(CalculateNum).width()<70){
		$('#list li').eq(CalculateNum).css('width','3'+'rem');
		$('#list li').eq(CalculateNum).css(
			{'font-size':'0.5'+'rem',
			'line-height':'0.9'+'rem',}
		)
	}
}
function CalculateWidth(num){
	return (num/($('#list li').eq(0).find('.num').html())*100)+'%';
}

//$('#tabBox2 li').click(function(){
//	$('#tabBox2 li').removeClass('tabBox2-active');
//	$(this).addClass('tabBox2-active');
//	get_hr_communist_integral_list($(this).index()+1);
//})
