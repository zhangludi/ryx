/*---------------------------------分类筛选--------------------------------------------------*/	
$(".exam_all_Intro li").click(function(){
	$(this).addClass("exam_all_Intro_active");
	$(".exam_all_Intro li").not($(this)).removeClass("exam_all_Intro_active");
})
/*---------------------------------底部分页--------------------------------------------------*/	
 /*容器1参数*/
  var obj_1={
	obj_box:'.page_1',//翻页容器
	total_item:72//条目总数
	/*per_num:10,//每页条目数
	current_page:8//当前页*/
  };  
  page_ctrl(obj_1);