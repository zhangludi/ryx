/*---------------------------------test2年份日历组件--------------------------------------------------*/
laydate.render({
 	elem: '#test2'
 	,lang: 'cn'
  	,position: 'static'
    ,type: 'month' 
    ,format: 'M月' //日期组合方式 可任意组合
	,isInitValue: true //是否允许填充初始值，默认为 true
	,show: true //直接显示
	,position: 'static'
  	,showBottom: false//是否显示底部栏
 	 //一、控件初始打开的回调
 	  ,ready: function(date){ 	 	
 	  	//lay('#testView').html(1);//切换日期时显示的值	
 	  	var next_year=$(".laydate-set-ym span:first-child").html();
 	  	$(".next_year").html(parseInt(next_year)+1+"年");
 	  	
 		 }
 	//二、日期时间被切换后的回调
	,change: function(value, date, endDate){
		var next_year=$(".laydate-set-ym span:first-child").html();
 	  	$(".next_year").html(parseInt(next_year)+1+"年");

	}
 
});

/*---------------------------------test3整月日历组件--------------------------------------------------*/

laydate.render({
 	elem: '#test3'
 	,lang: 'cn'
  	,position: 'static'
 	//,min: -4 //5天前
  //	,max: 0 //7天后
    ,type: 'date' 
    ,format: 'M月d日' //日期组合方式 可任意组合
     //传入Date对象给初始值
	//,value: '2018-08-18' //参数即为：2018-08-20 20:08:08 的时间戳
	,isInitValue: true //是否允许填充初始值，默认为 true
	,show: true //直接显示
	,position: 'static'
  	,showBottom: false//是否显示底部栏
 	 //一、控件初始打开的回调
 	  ,ready: function(date){ 	 	 	  	
 	  	//得到初始的日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
 	  	//显示周几
 	  	var this_week=date.month+'/'+date.date+'/'+date.year; 	  
		 var day = new Date(Date.parse(this_week)); 
		 var today = new Array('周日','周一','周二','周三','周四','周五','周六');  
		 var week = today[day.getDay()];  
		 //显示几月几号 
		 var how_months=date.month+'月'+date.date+'日';
 	  	$(".this_week").html(how_months+'('+week+')');
 		 }
 	//二、日期时间被切换后的回调
	,change: function(value, date, endDate){
		$("#test2 #layui-laydate1").remove();		
		var date_month=date.month+'月';
		console.log(date_month);//当前月份
	/////////////////////////////////////////////////////////////初始化test2
	laydate.render({
 	elem: '#test2'
 	,lang: 'cn'
  	,position: 'static'
    ,type: 'month'    
    ,format: 'M月' //日期组合方式 可任意组合
	,isInitValue: true //是否允许填充初始值，默认为 true
	,value: date_month
	,show: true //直接显示
	,position: 'static'
  	,showBottom: false//是否显示底部栏
 	 //一、控件初始打开的回调
 	  ,ready: function(date){ 	 	
 	  	//lay('#testView').html(1);//切换日期时显示的值	
 	  	var next_year=$(".laydate-set-ym span:first-child").html();
 	  	$(".next_year").html(parseInt(next_year)+1+"年");
 	  	
 	  	
 		 }
 	//二、日期时间被切换后的回调
	,change: function(value, date, endDate){
		var next_year=$(".laydate-set-ym span:first-child").html();
 	  	$(".next_year").html(parseInt(next_year)+1+"年");

	}
 
});

///////////////////////////////////////////////////////////////////初始化test2结束
	   
	 }
	});
	
	
	
/*---------------------------------其它--------------------------------------------------*/	

$(".edu_assort li").click(function(){
	$(this).addClass("edu_assort_active");
	$(".edu_assort li").not($(this)).removeClass("edu_assort_active");
})
