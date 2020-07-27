//分页字段
var page1 = 1;
var page2 = 1;
var status=0;
portrait="";
//页面初始化
apiready = function () {
	//get_notice_list();//获取数据
}
var page=1;
//加载数据


function openInfo(winName,id){
	//打开新页面
	api.openWin({
	    name: winName,
	    url: 'header/'+winName+'.html',
	    pageParam:{
	    	"id":id
	    }
    });
}
//刷新页面
function exec(){
	location.reload();

}