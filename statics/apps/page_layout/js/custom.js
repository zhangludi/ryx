/**
Core script to handle the entire theme and core functions
**/


// 客户管理详情页面，联系人头像的切换效果
$(".p5-client-cricle").hover(function(){
	$(this).fadeTo(50, 0.3).parent().hover(function(){},
	  	function(){
		  	$(this).find(".p5-client-test").animate({ 
			   top:90,
			   opacity:'0',
			  }, 10 )
		  	$(this).find(".p5-client-cricle").fadeTo(10, 1)
		  })
	$(this).next(".p5-client-test").animate({ 
	   top:0,
	   opacity:'1',
	  }, 50 )
})


// 首页左侧导航的选中状态
var hrd=$(".sub-menu>li.nav-item")
 hrd.parent("ul").find("li").click(function(){
   $(this).addClass("link").siblings("li").removeClass("link active")
})


// 左侧一级列表，右侧表格局刷，jq写的页面
$(".p5-local-left>li").click(function(){
    var num=$(this).index();
    $(".p5-local-right .iteam").eq(num).addClass("active").siblings("li").removeClass("active")
    $(".p5-local-left>li").removeClass("active")
    $(this).addClass("active")
})
// bug管理填入表单模糊查询的下拉框
// var bugMenu=$(".ui-widget-content li")

// if (bugMenu=true) {
//     $(".p5-bug-search input").addClass("active")
    
// }else(bugMenu=false){
//     $(".p5-bug-search input").removeClass("active")
// }

// 去掉select的下拉搜索
// $(document).ready(function() { 
//     $(".select-search-no").select2({
//         minimumResultsForSearch: -1
//     }); 

// });

// 获取当前窗口的高度
// var wheight=window.innerHeight
var wheight=window.screen.availHeight
// var wheight = document.documentElement.clientHeight  
// alert(wheight);
// 动态面板自适应高度
var sc=$(".p5-scroller")
var st=$(".p5-table-scroller")
var srt=$(".p5-right-table-scroller")
/*$(function(){  
    var is360 = false;  
    var isIE = false;  
    if (window.navigator.appName.indexOf("Microsoft") != -1){  
        isIE= true;  
    }  
    if(isIE&&(window.navigator.userProfile+'')=='null'){  
        is360 = true;  
    }  
    if(is360){  
        alert ('360浏览器');  
    }else if(isIE){  
        alert('IE浏览器');  
    }  
})*/
if(window.external&&window.external.twGetRunPath&&window.external.twGetRunPath().toLowerCase().indexOf("360se")>-1){
   alert('本站不支持360浏览器访问，请更换其他浏览器！');
}

// 火狐浏览器兼容
 if (navigator.userAgent.indexOf('Firefox') >= 0){
    // 动态面板自适应高度
    $.each(sc, function(){
        var topHeight=$(this).offset().top
        $(this).css('height',wheight-topHeight-280);
    });
	// table用的动态面板自适应表格高度
	$.each(st, function(){
	        var topHeight=$(this).offset().top
	        $(this).css('height',wheight-topHeight-165);
	    });
    // 动态面板右侧表格自适应高度
    $.each(srt, function(){
            var topHeight=$(this).offset().top
            $(this).css('height',wheight-topHeight-280);
        });
// Opera浏览器兼容
}else if (navigator.userAgent.indexOf('Opera') >= 0){
    // 动态面板自适应高度
    $.each(sc, function(){
        var topHeight=$(this).offset().top
        $(this).css('height',wheight-topHeight-200);
    });
	// table用的动态面板自适应表格高度
	$.each(st, function(){
	        var topHeight=$(this).offset().top
	        $(this).css('height',wheight-topHeight-150);
	    });
    // 动态面板右侧表格自适应高度
    $.each(srt, function(){
            var topHeight=$(this).offset().top
            $(this).css('height',wheight-topHeight-281);
        });
// 其它浏览器兼容
}else{
    // 动态面板自适应高度
    $.each(sc, function(){
        var topHeight=$(this).offset().top
        $(this).css('height',wheight-topHeight-205);
    });
	// table用的动态面板自适应表格高度
	$.each(st, function(){
	        var topHeight=$(this).offset().top
	        $(this).css('height',wheight-topHeight-155);
	    });
    // 动态面板右侧表格自适应高度
    $.each(srt, function(){
            var topHeight=$(this).offset().top
            $(this).css('height',wheight-topHeight-206);
        });
}
 

 
// 客户管理页面表格自适应高度getElementById()getElementsByName
var clHeight=document.getElementById("client_table");
var scHeight=$("#client_table").parents(".scroller").height();
if(clHeight != null){
    clHeight.setAttribute("data-height",scHeight);
}
// 动态面板右侧表格
// var clHeight1=document.getElementById("client_table1")
// var scHeight1=$("#client_table1").parents(".scroller").height()
// console.log(clHeight1.getAttribute("data-height"));
// clHeight1.setAttribute("data-height",scHeight1 );
// 第三个表格
// var clHeight2=document.getElementById("client_table2")
// var scHeight2=$("#client_table2").parents(".scroller").height()
// console.log(clHeight2.getAttribute("data-height"));
// clHeight2.setAttribute("data-height",scHeight2 );

 // alert(st.height()) 

// console.log(crlHeight.getAttribute("data-height"));
// crlHeight.setAttribute("data-height",srcHeight );





// 获取浏览器可见区域
// var wheight = document.documentElement.clientHeight 
// $(".J_mainContent iframe").each(function(){
//     $(this).load(function(){
//         $(this).height(0);
//         var mainheight = $(this).contents().find("body").height();
//         alert(mainheight) 
//         if (mainheight>wheight){
//             $(this).css('height',mainheight);
//         } else{
//             if(mainheight<=wheight){
//                 $(this).css('height',wheight - 80); 
//             }
//         }
        
//     })  
// });


 


