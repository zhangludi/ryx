apiready = function(){
}

//打开谁是党员列表--------------------------------------------------------
function openPhonebook(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_com/com_phonebook/header/'+winName+'.html',
	    pageParam:{
	    	
	    }
    });
}