apiready = function(){
};

function cc(name,id){
	if(id!=='undefined'){
		$('#branch_hide').val(id);
	}
	if(name!=='undefined'){
		$('#branch').val(name);
	}
}
function login(){
	if($("#branch_hide").val()){
		if($("#real_name").val()=="spdemo"){
			if($('#IDCard').val()=="sp4006770389"){
				openLeft();
				localStorage.login = 1;//修改登录状态
			}else(alert('身份证输入错误'))
		}else{alert('无此用户')}
	}else{
		alert('请选择支部')
	}
}


//打开新页面
function openNewWin(winName){
	api.openWin({
        name: winName,
        url: 'header/'+winName+".html"
    });
}

//信鸽推送
function openTencentPush(){
	// 注册设备并绑定用户账号
	var tencentPush = api.require('tencentPush');
	
	var resultCallback = function(ret, err){
	    if(ret.status){
	        //alert("注册成功，token为："+ret.token);
	    }
	};
	
	// 需要绑定的账号，若为"*"表示解除之前的账号绑定
	var params = {account:localStorage.user_id};
	tencentPush.registerPush(params, resultCallback);
	
	//监听状态栏点击
	api.addEventListener({
    name:'appintent'
	},function(ret,err){
	    if(api.systemType == 'ios'){
//	        var customCotent = JSON.parse(ret.appParam["tag.tpush.NOTIFIC"].substring(ret.appParam["tag.tpush.NOTIFIC"].indexOf("{"),ret.appParam["tag.tpush.NOTIFIC"].indexOf("}")+1));
//	        
//	        openPushNewWin(customCotent.winName,customCotent.id);
	    } else {alert(JSON.stringify(ret.appParam));
//	        var customCotent = JSON.parse(ret.appParam["tag.tpush.NOTIFIC"].substring(ret.appParam["tag.tpush.NOTIFIC"].indexOf("{"),ret.appParam["tag.tpush.NOTIFIC"].indexOf("}")+1));
//	        
//	        openPushNewWin(customCotent.winName,customCotent.id);
	    }
	
	});
}

//通过推送打开新的页面
function openPushNewWin(winName,id){
	var wName = winName.split("/");
		wName = wName[wName.length-1].substring(0,wName[wName.length-1].indexOf("."));
	api.openWin({
        name: wName,
        url: winName,
        pageParam:{
        	"type":"已收文件",//公文处理中用到
        	"id":id
	    }
    });
}

//打开左侧侧滑

function openLeft(){
	api.openSlidLayout({
		type:'left',
		slidPane:{
			name:'bd_index',
			url:'../../sp_bd/bd_index/bd_index.html',
		},
		fixedPane:{
			name:'bd_home_left',
			url:'../../sp_bd/bd_home/bd_home_left.html',
		},
	}, function(ret, err) {
		
	});
}
