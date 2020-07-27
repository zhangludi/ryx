var _baiduMap;//地图对象
var _popwindow;//弹出窗口对象
var MOVE_TIME_SPLIT=7000;//移动时间间隔
var LOAD_DATA_SPLIT=20000;//加载数据的间隔时间
var POP_INTERVAL;//移动定时器
var LOAD_POP_INTERVAL;//加载数据定时器
var LOAD_POP_DATA_SIZE=10;//加载数据数量

var styleOptions = {
    strokeColor:"red",    //边线颜色。
    fillColor:"#fff",      //填充颜色。当参数为空时，圆形将没有填充效果。
    strokeWeight:1,       //边线的宽度，以像素为单位。
    strokeOpacity:1,	   //边线透明度，取值范围0 - 1。
    fillOpacity:0.5,      //填充的透明度，取值范围0 - 1。
    strokeStyle: 'solid' //边线的样式，solid或dashed。
};

function initStatic(){
	_baiduMap.clearOverlays();//清空所有的覆盖物
	_organMaker={};//组织机构的maker
	_organGrid=new Array();//grid区域
	_organGridPoint=new Array();//gridmaker
	_project=new Array();//志愿服务项目maker
	_maxPopId=null;//最大的数据id
	_pop=new Array();//弹出的数据
	_partyGroup=new Array();//党代表工作室makker
}

$(function(){
	//初始化地图
	initmap();
	//标注出区委所在地
//	addMaker({
//		lng:104.897856,
//		lat:26.600358,
//		icon:"/demo_dj/statics/layouts/sp_layout/images/flag-red.png",
//		lable:"<div class='orgLabel'>杨柳社区党委</div>"
//	});
	
	//关闭左侧
	$("#closeLeft").click(function(){
		if($(this).hasClass("openleft")){
			$("#mapLeft").animate({left:"0"},200);
			$(this).removeClass("openleft");
		}else{
			$("#mapLeft").animate({left:"-260px"},200);
			$(this).addClass("openleft");
		}
	});
})

/**
 * 初始化百度地图
 */
function initmap(){
	_baiduMap = new BMap.Map("mapbox", {enableMapClick:false});    // 创建Map实例
	_baiduMap.centerAndZoom(new BMap.Point(104.897856,26.600358), 16);  // 初始化地图,设置中心点坐标和地图级别
	_baiduMap.addControl(new BMap.MapTypeControl());   //添加地图类型控件
	_baiduMap.setCurrentCity("六盘水市钟山区");          // 设置地图显示的城市 此项是必须设置的
	_baiduMap.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
	//初始化完成之后加载数据
	initData(null);
	_baiduMap.addEventListener("tilesloaded",function(){});
}

/**
 * 初始化数据
 * @param {Object} organCode
 */
function initData(organCode){
	if(POP_INTERVAL){//清空自动移动定时器
		clearPopMoveIntervals();
	}
	if(LOAD_POP_INTERVAL){//清空数据加载定时器
		clearLoadPopDataInterval();
	}
	initStatic();//初始化变量
}




/**
 * 
 * @param {Object} conf
 * 
 * lat:纬度
 * lng:经度
 * lable:标签名称
 * icon:定位图标
 * click:点击事件
 */
function addMaker(conf){
	var marker = new BMap.Marker(new BMap.Point(conf.lng,conf.lat));
	marker["data"]=conf.data;
	if(conf.lable){
		var label = new BMap.Label(conf.lable,{offset:new BMap.Size(20,-10)});
		marker.setLabel(label);
	}
	//icon
	if(conf.icon){
		marker.setIcon(new BMap.Icon(conf.icon, new BMap.Size(64,64)));
	}
	if(conf.title){
		marker.setTitle(conf.title);
	}
	
	_baiduMap.addOverlay(marker);
	
	return marker;
}
