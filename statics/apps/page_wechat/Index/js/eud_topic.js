apiready = function () {
	getConfig();//加载配置文件	
}

var slide = new auiSlide({
    container:document.getElementById("aui-slide"),
    // "width":300,
    "height":200,
    "speed":300,
    "autoPlay": 3000, //自动播放
    "pageShow":true,
    "pageStyle":'dot',
    "loop":true,
    'dotPosition':'center',
    
})
var slide3 = new auiSlide({
    container:document.getElementById("aui-slide3"),
    // "width":300,
    "height":240,
    "speed":500,
    "autoPlay": 3000, //自动播放
    "loop":true,
    "pageShow":true,
    "pageStyle":'line',
    'dotPosition':'center'
})


//刷新页面
function exec(){
	location.reload();
}


//打开在线党校模块
function openLearn(winName,type){
	api.openWin({
	    name: winName,
	    url: '../edu_learn/header/'+winName+'.html',
	    pageParam:{
	    	"type":type
	    }
    });
}

//打开专题学习模块
function openSpecial(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_special/header/'+winName+'.html',
	    
    });
}

//打开考试中心模块
function openExam(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_exam/header/'+winName+'.html',
	    
    });
}

//打开学习笔记模块
function openNotes(winName){
	api.openWin({
	    name: winName,
	    url: '../edu_notes/header/'+winName+'.html',
	    
    });
}
