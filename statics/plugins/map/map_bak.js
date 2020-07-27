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
	//加载左侧组织机构树
	loadOrganTree();
	//标注出区委所在地
//	addMaker({
//		lng:106.656543,
//		lat:26.693848,
//		icon:base+"/static/res/map/center.png",
//		lable:"<div class='orgLabel'>杨柳社区党委</div>"
//	});
	//左侧菜单的点击切换
	$(".mapleftItem").click(function(){
		var _index =$(this).parent().children().index($(this));
		$(this).addClass("active").siblings(".active").removeClass("active");
		var $contents=$(".mapLeftTab").eq(_index);
		$contents.addClass("active").siblings(".active").removeClass("active");
		//判断该tab下是否已经加载了数据，如果没有加载，则动态加载
		if($contents.attr("hasLoad")=="0"){
			$contents.attr("hasLoad","1")
			var urlb=$contents.attr("dataurl").split("#");
			$("#mamleftSub_content_1").load(urlb[0]);
			$("#mamleftSub_content_2").load(urlb[1]);
		}
	});
	
	$(".nicescroll").niceScroll({
		touchbehavior:false
		,cursorcolor:"#ccc"
		,cursoropacitymax:1
		,cursorwidth:7
		,cursorborder:"0"
		,cursorborderradius:"10px"
		,background:"none"
		,autohidemode:"scroll"
	});
	
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
	var  mapStyle ={ 
		features: ["road", "building","water","land","point"],//隐藏地图上的poi
		style : "light"  //设置地图风格为高端黑
	};
	_baiduMap.setMapStyle(mapStyle);
	_baiduMap.centerAndZoom(new BMap.Point(118.846457,31.958485), 16);  // 初始化地图,设置中心点坐标和地图级别
	_baiduMap.addControl(new BMap.MapTypeControl());   //添加地图类型控件
	_baiduMap.setCurrentCity("贵阳");          // 设置地图显示的城市 此项是必须设置的
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
	loadOrganPoint(null,organCode);//加载组织机构定位
	loadProject(null,organCode);
	loadGrid(null,organCode);//加载志愿服务网格信息
	loadpartyGroup(organCode);//党代表工作室
}

/**
 * 加载组织定位
 */
function loadOrganPoint(pageNum,organCode){
//	window.console.log("开始加载组织机构数据");
	//每次加载200个
	$.ajax({
		type:"get",
		dataType:"json",
		data:{
			pageSize:2,
			pageNum:(pageNum?pageNum:1),
			organCode:organCode
		},
		url:base+"/member/map/organJson",
		success:function(json){
			$("#loading").fadeOut(1000);
			loadOrganPointCallBack(json,organCode);
		}
	});
}

/**
 * 请求党组织数据回调
 * @param {Object} json
 */
function loadOrganPointCallBack(json,organCode){
	if(json.code=="200"){
		var _pageNum=json.data.pageNum;
		var _totalPage=json.data.totalPage;
		if(_pageNum<_totalPage){
			loadOrganPoint(_pageNum+1);
		}
		//打点，第二个参数是告知是否已经加载完成了所有党组织
		addOrganPoint(json,_pageNum == _totalPage,organCode);
	}else{
		alert("加载党组织分布出现异常!");
	}
}

/**
 * 加载党组织定位列表
 * @param {Object} json
 */
function addOrganPoint(json,isLast,organCode){
	var pos=json.data.rows;
	for(var i=0;i<pos.length;i++){
		var maker=addMaker({
			lng:pos[i].lng,
			lat:pos[i].lat,
			data:pos[i],
			title:pos[i].title,
//			lable:"<div class='orgLabel'>"+pos[i].title+"</div>",
			icon:base+"/static/res/map/qi.png"
		});
		//把所有的maker放入map中，方便后期查询
		_organMaker[pos[i].lng+","+pos[i].lat]=maker;
		//给每个maker绑定点击事件
		maker.addEventListener("click",function(e){
			organMakerClick(this);
		});
	}
	if(isLast){
//		window.console.log("组织机构数据已经全部加载完成...");
		//当已加载完成,3秒后开启数据获取定时器
		window.setTimeout(function(){
			startLoadPopDataInterval(organCode);
		},3000);
	}
}
/**
 * 组织机构的maker点击事件
 * @param {Object} mak
 */
function organMakerClick(mak){
	moveto(mak.getPosition().lng,mak.getPosition().lat);//移动该点到中心
	showWindow({
		maker:mak,
		tpl:"organTpl",
		data:mak.data,//这里的this是maker，maker在打点的时候给绑定了数据
		width:570,
		height:430
	});
	loadActivityList(mak.data);//加载活动列表
}
/**
 * 加载党组织对应的最新5条活动数据
 * @param {Object} data
 */
function loadActivityList(data){
	$.ajax({
		type:"get",
		dataType:"json",
		url:base+"/member/map/activity",
		data:{
			organCode:data.organCode,
			size:5
		},
		success:function(json){
			var html=template("activityListTpl",json);
			$("#activityList").html(html);
		}
	});
}

/**
 * 加载最新的popdata数据
 * @param {Object} id
 */
function loadPopData(organCode){
//	window.console.log("开始加载pop数据");
	$.ajax({
		type:"get",
		dataType:"json",
		data:{
			id:_maxPopId,
			size:LOAD_POP_DATA_SIZE,
			organCode:organCode
		},
		url:base+"/member/map/pop/data",
		success:function(json){
			var _arr=json.data;
			//判断是否是第一次加载数据，如果是第一次加载数据，则全部放在末尾
			var isFirst=true;
			if(_pop.length>0){
				isFirst=false;
			}
			//定义一个最大id
			for(var i=0;i<_arr.length;i++){
					_pop.push(_arr[i]);//如果长度为0，在末尾追加
					//如果是第二次加载的数据，应该放在顶端
					if(!isFirst){
						_pop.unshift(_arr[i]);
					}
				//把最大的ID存储下来
				if(_arr[i].id>_maxPopId){
					_maxPopId=_arr[i].id;
				}
//				window.console.log("放进去顺序："+_arr[i].id);
			}
			//如果是第一次加载到数据，设置定时器循环显示气泡
			if(_pop.length>0 && _pop.length==_arr.length){
				startPopMoveInterval();
			}
		}
	});
}
/**
 * 自动移动泡泡
 */
function autoShowPop(){
	if(_pop.length==0){return}
	var data=_pop.shift();
	_pop.push(data);//取出第一个追加到末尾
	//弹出信息框
	//判断该点是否存在
	var obj=_organMaker[data.lng+","+data.lat];
//	window.console.log(_organMaker);
	if(obj){
		moveto(data.lng,data.lat);//移动该点到中心
		showWindow({
			maker:obj,
			tpl:"popTpl",
			data:data,
			width:460,
			height:210
		});
	}
}
/**
 * 加载党代表工作室
 * @param {Object} organCode
 */
function loadpartyGroup(organCode){
	$.ajax({
		type:"get",
		dataType:"json",
		data:{
			organCode:organCode
		},
		url:base+"/member/map/partyGroup",
		success:function(json){
			loadpartyGroupCallBack(json);
		}
	});
}
/**
 * 加载党代表工作室的回调事件
 */
function loadpartyGroupCallBack(json){
	for(var i=0;i<json.data.length;i++){
		var obj=json.data[i];
		//打点
		var m=addMaker({
			lng:obj.lng,
			lat:obj.lat,
			data:obj,
			title:obj.title,
			icon:base+"/static/res/map/gzs.png"
		});
		_partyGroup.push(m);
		//给每个maker绑定点击事件
		m.addEventListener("click",function(e){
			partyGroupMakerClick(this);
		});
	}
}
/**
 * 点击党代表工作室maker
 * @param {Object} mak
 */
function partyGroupMakerClick(mak){
	moveto(mak.getPosition().lng,mak.getPosition().lat);//移动该点到中心
	showWindow({
		maker:mak,
		tpl:"partygroupTpl",
		data:mak.data,//这里的this是maker，maker在打点的时候给绑定了数据
		width:570,
		height:430
	});
}


/**
 * 加载志愿服务网格
 * @param {Object} pageNum
 */
function loadGrid(pageNum,organCode){
	//每次加载200个
	$.ajax({
		type:"get",
		dataType:"json",
		data:{
			organCode:organCode,
			pageSize:300,
			pageNum:(pageNum?pageNum:1)
		},
		url:base+"/member/map/gridJson",
		success:function(json){
			loadGridCallBack(json,organCode);
		}
	});
}
/**
 * 请求网格后的回调
 * @param {Object} json
 */
function loadGridCallBack(json,organCode){
	if(json.code=="200"){
		var _pageNum=json.data.pageNum;
		var _totalPage=json.data.totalPage;
		if(_pageNum<_totalPage){
			loadGrid(_pageNum+1,organCode);
		}
		//打点
		addOrganGrid(json);
	}else{
		alert("加载党组织网格出现异常!");
	}
}

/**
 * 加载网格
 * @param {Object} json
 */
function addOrganGrid(json){
	var areas=json.data.rows;
	for(var i=0;i<areas.length;i++){
		addGrid(areas[i].area);
		//打点
		var m=addMaker({
			lng:areas[i].lng,
			lat:areas[i].lat,
//			lable:areas[i].title,
			data:areas[i],
			title:areas[i].title,
			icon:base+"/static/res/map/grid.png"
		});
		_organGridPoint.push(m);
		//注册点击试讲
		//给每个maker绑定点击事件
		m.addEventListener("click",function(e){
			gridMakerClick(this);
		});
	}
}
/**
 * grid的maker点击事件
 * @param {Object} mak
 */
function gridMakerClick(mak){
	moveto(mak.getPosition().lng,mak.getPosition().lat);//移动该点到中心
	showWindow({
		maker:mak,
		tpl:"gridTpl",
		data:mak.data,//这里的this是maker，maker在打点的时候给绑定了数据
		width:570,
		height:475
	});
	loadGridMemberList(mak.data);
}
/**
 * gird的点击事件，加载该grid的志愿者信息
 * @param {Object} data
 */
function loadGridMemberList(data){
	$("#gridMemberlist").page({
		url:base+"/member/map/gridMembers"
		,param:{
			gridId:data.id
			,pageSize:5
		}
	});
}
/**
 * 在地图上显示网格
 * @param {Object} points
 */
function addGrid(points){
	if(!points){
		return ;
	}
	var _arr=points.split(";");
	var _pointArray=new Array();
	for(var i=0;i<_arr.length;i++){
		var _poArr=_arr[i].split(",");
		_pointArray.push(new BMap.Point(_poArr[0],_poArr[1]));
	}
	var polygon = new BMap.Polygon(_pointArray,styleOptions);
    _baiduMap.addOverlay(polygon);
    //放到缓存中
    _organGrid.push(polygon);
}
/**
 * 点击grid的label名称显示完成的grid信息
 * @param {Object} id
 */
function showgridLabelAll(id){
	if($("#"+id).height()==110){
		$("#"+id).height(0).width("auto");
	}else{
		$("#"+id).height(110).width(300);
	}
}

/**
 * 移动到指定的位置，位于地图中心位置
 * @param {Object} lng
 * @param {Object} lat
 */
function moveto(lng,lat){
	_baiduMap.panTo(new BMap.Point(lng,lat)); 
}
/**
 * 使用infobox来显示窗口
 * 可以定制窗口样式
 * @param {Object} c
 */
function showWindow(c){
//	window.console.log("打开显示窗口");
	if(_popwindow){
		_popwindow.close();
	}
	_popwindow = new BMapLib.InfoBox(
		_baiduMap,
		template(c.tpl,c.data),
		{
		boxStyle:{width:c.width+"px",height:c.height+"px"},
		offset:new BMap.Size(0,35)
		,closeIconUrl:base+"/static/res/common/img/close.png"
		,closeIconMargin: "5px 5px 0 0"
		,enableAutoPan: false
		,align: INFOBOX_AT_TOP
	});
	_popwindow.open(c.maker);
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
/**
 * 组织机构树配置
*/
var setting = {
    view: {
        showLine: true
    },
    data: {
        simpleData: {
            enable: true
        }
    },callback: {
		onClick: treeOnClick
	}
};
/**
 * 加载组织机构树，初始化
*/
function loadOrganTree(){
    $.ajax({
        type: "post",
        url: base + "/member/map/organTree",
        dataType: "json",
        success: function (result) {
            var nodes = new Array();
            for (var i = 0; i < result.data.length; i++) {
                var _organ = result.data[i];
                var _obj = {
                    id:_organ.id,
                    pId:_organ.pid,
                    lat:_organ.lat,
                    lng:_organ.lng,
                    organCode:_organ.organCode,
                    name: _organ.organName,
                    icon:_organ.depth==1?base + "/static/res/common/img/treedh.png":base + "/static/res/common/img/treeicon.png",
                    open:(_organ.depth)==1?true:false
                };
                nodes.push(_obj);
            }
            organTree = $.fn.zTree.init($("#organTree"), setting, nodes);
        }
    });
}
/**
 * 左侧组织树的点击事件
 * 点击之后加载该组织下级的相关数据
 */
function treeOnClick(event, treeId, treeNode){
	//判断是否定位，如果定位移动视图范围到中心
	if(treeNode.lng){
		moveto(treeNode.lng,treeNode.lat);//移动该点到中心
	}
	
	$.ui.tip("已切换至"+treeNode.name+"下数据",3);
	initData(treeNode.organCode);
}
//网格显示或加载
function zyfw(ele){
	if($(ele).hasClass("active")){
		for(var i=0;i<_organGrid.length;i++){
			_organGrid[i].hide();
		}
		for(var i=0;i<_organGridPoint.length;i++){
			_organGridPoint[i].hide();
		}
	}else{
		for(var i=0;i<_organGrid.length;i++){
			_organGrid[i].show();
		}
		for(var i=0;i<_organGridPoint.length;i++){
			_organGridPoint[i].show();
		}
	}
	$(ele).toggleClass("active");
}

function loadProject(pageNum,organCode){
	//每次加载200个
	$.ajax({
		type:"get",
		dataType:"json",
		data:{
			pageSize:300,
			pageNum:(pageNum?pageNum:1),
			organCode:organCode
		},
		url:base+"/member/map/project",
		success:function(json){
			loadProjectCallBack(json,organCode);
		}
	});
}
/**
 * 请求志愿服务后的回调
 * @param {Object} json
 */
function loadProjectCallBack(json,organCode){
	if(json.code=="200"){
		var _pageNum=json.data.pageNum;
		var _totalPage=json.data.totalPage;
		if(_pageNum<_totalPage){
			loadProject(_pageNum+1,organCode);
		}
		//打点
		addProject(json);
	}else{
		alert("加载志愿服务出现异常!");
	}
}

function addProject(json){
	var pos=json.data.rows;
	for(var i=0;i<pos.length;i++){
		var _data=pos[i];
		var maker=addMaker({
			lng:pos[i].lng,
			lat:pos[i].lat,
			data:_data,
			lable:"",
			title:pos[i].title,
			icon:base+"/static/res/map/p.png"
		});
		
		_project.push(maker);
		maker.addEventListener("click",function(e){
			window.open(base+"/member/project/detail?id="+this.data.id);
		});
	}
}
function hdms(ele){
	if($(ele).hasClass("active")){
		clearPopMoveIntervals();
	}else{
		startPopMoveInterval();
	}
	$(ele).toggleClass("active");
}
function zyfwdis(ele){
	
	if($(ele).hasClass("active")){
		for(var i=0;i<_project.length;i++){
			_project[i].hide();
		}
	}else{
		for(var i=0;i<_project.length;i++){
			_project[i].show();
		}
	}
	$(ele).toggleClass("active");
}
function ddbgzs(ele){
	
	if($(ele).hasClass("active")){
		for(var i=0;i<_partyGroup.length;i++){
			_partyGroup[i].hide();
		}
	}else{
		for(var i=0;i<_partyGroup.length;i++){
			_partyGroup[i].show();
		}
	}
	$(ele).toggleClass("active");
}

/**
 * 开始移动地图
 */
function startPopMoveInterval(){
	autoShowPop();//先显示
	POP_INTERVAL=window.setInterval("autoShowPop()",MOVE_TIME_SPLIT);
}
/**
 * 暂停移动地图
 */
function clearPopMoveIntervals(){
	//关闭窗口
	if(_popwindow){
//		_popwindow.close();
	}
	window.clearInterval(POP_INTERVAL);
}
/**
 * 定时加载最新数据
*/
function startLoadPopDataInterval(organCode){
	loadPopData(organCode);
	LOAD_POP_INTERVAL=window.setInterval(function(){
		loadPopData(organCode);
	},LOAD_DATA_SPLIT);
}

function clearLoadPopDataInterval(){
	window.clearInterval(LOAD_POP_INTERVAL);
}

function showCountsDiv(wid){
	$("#mapcountsbox").width(wid+1);
	$("#mapcountsbox").css({"margin-left":"-"+(wid/2)+"px"}).animate({top:"72px"},500);
}
