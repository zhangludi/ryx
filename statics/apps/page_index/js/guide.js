$(function(){
	$(".sp_guide_right_tab_box:eq(0)").find('img').attr('src', '../../../statics/apps/page_index/images/gn.png');
	$(".sp_guide_right_tab_box:eq(1)").find('img').attr('src', '../../../statics/apps/page_index/images/pt1.png');
	$(".sp_guide_right_tab_box:eq(2)").find('img').attr('src', '../../../statics/apps/page_index/images/wz1.png');
	$(".sp_guide_right_tab_box:eq(0)")
			   .css('background', 'url(../../../statics/apps/page_index/images/jx1.png) no-repeat')
			   .css('background-size', '100% 100%')
			   .css('color', '#fff')
	$(".sp_guide_right_tab_box").on("mouseover",function(){
		$(this).css('background', 'url(../../../statics/apps/page_index/images/jx1.png) no-repeat')
			   .css('background-size', '100% 100%')
			   .css('color', '#fff')
			   .siblings('div')
			   .css('background', 'url(../../../statics/apps/page_index/images/jx2.png) no-repeat')
			   .css('background-size', '100% 100%')
			   .css('color', '#000')
	})
	$(".sp_guide_right_tab_box:eq(0)").on("mouseover",function(){
		$(".sp_guide_right_tab_box:eq(0)").find('img').attr('src', '../../../statics/apps/page_index/images/gn.png');
		$(".sp_guide_right_tab_box:eq(1)").find('img').attr('src', '../../../statics/apps/page_index/images/pt1.png');
		$(".sp_guide_right_tab_box:eq(2)").find('img').attr('src', '../../../statics/apps/page_index/images/wz1.png');
		$(".sp_qiehuan").eq(0).show()
		$(".sp_qiehuan").eq(0).siblings('.sp_qiehuan').hide()
	})
	$(".sp_guide_right_tab_box:eq(1)").on("mouseover",function(){
		$(".sp_guide_right_tab_box:eq(1)").find('img').attr('src', '../../../statics/apps/page_index/images/pt.png');
		$(".sp_guide_right_tab_box:eq(0)").find('img').attr('src', '../../../statics/apps/page_index/images/gn1.png');
		$(".sp_guide_right_tab_box:eq(2)").find('img').attr('src', '../../../statics/apps/page_index/images/wz1.png');
		$(".sp_qiehuan").eq(1).show()
		$(".sp_qiehuan").eq(1).siblings('.sp_qiehuan').hide()
	})
	$(".sp_guide_right_tab_box:eq(2)").on("mouseover",function(){
		$(".sp_guide_right_tab_box:eq(2)").find('img').attr('src', '../../../statics/apps/page_index/images/wz.png');
		$(".sp_guide_right_tab_box:eq(1)").find('img').attr('src', '../../../statics/apps/page_index/images/pt1.png');
		$(".sp_guide_right_tab_box:eq(0)").find('img').attr('src', '../../../statics/apps/page_index/images/gn1.png');
		$(".sp_qiehuan").eq(2).show()
		$(".sp_qiehuan").eq(2).siblings('.sp_qiehuan').hide()
	})
})