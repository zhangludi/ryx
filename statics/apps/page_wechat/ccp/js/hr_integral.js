apiready = function(){
}



//打开积分排名详情--------------------------------------------------------------------
function openInfo(winName,type){
	api.openWin({
        name: winName,
        url: 'header/'+winName+'.html',
        pageParam:{
        	type:type,
        }
    });
}

