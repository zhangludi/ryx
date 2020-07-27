apiready = function(){
	getBdCodeList()//获取组织类型接口
//	fun('0');//运行 （获取党建矩阵列表） 接口
//监听下滑事件
    window.addEventListener("scroll", function(event) {
        var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
        if(scrollTop>=50){
       	  $(".head_fix").removeClass('di-n');
        }else{
          $(".head_fix").addClass('di-n');
        }
		
    });
 //监听下滑事件结束
 	$('#keywork').keyup(function(){
		$('#list').html('');
		getHrPartyList();
	})
    
}
//创建获取党组织层级所传字段--------------------------------
var page = 1;
var winthNum = 0;

//获取组织类型接口------------------------------------------------------
function getBdCodeList(){
	var url = localStorage.url+"/index.php/Api/Public/get_bd_code_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {
	    //传输参数 key=code_group value=party_level_code
	    	values: { 
	            "code_group":"party_level_code",
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.data&&ret.data.length>0){
    				for(var i=0;i<ret.data.length;i++){
//	    				$("#tabBox2").append(
//	    					'<li id="'+ret.data[i].code_no+'" class="mt-5em mb-5em f-w" onclick="active(this)">'+
//	    						'<a name="#tab2">'+ret.data[i].code_name+'</a>'+
//	    						'<span class="active"></span>'+
//	    					'</li>'
//	    				)
						$("#list2").append(
	    					'<li class="mr-12em b-eb bg-color-whiter pull-left f-12em color-21 mb-12em p-max" id="'+ret.data[i].code_no+'" onclick="active(this)">'+ret.data[i].code_name+'</li>'
	    				)
	    				winthNum += $("#tabBox2 li").eq(i).outerWidth(true)+3
	    			}
	    			active($('#list2 li').eq(0));
	    			//$("#tabBox2").css('width',winthNum);
//	    			var current=$(".tabBox2-active").html();
	    			$('.current').html(ret.data[0].code_name);
    			}
			}
			else{
				api.toast({msg: ret.msg ,duration:3000,location: 'top'});
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


//获取党建矩阵列表接口-----------------------------------------
function getHrPartyList(type){
	var url = localStorage.url+"/index.php/Api/Hr/get_hr_party_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 1000,
	    data: {//传输参数1
	    	values: { 
	            "type":type,
	            "party_name":$('#keywork').val(),//关键字
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    		$('#list').html('');
    			if(ret.data&&ret.data.length>0){    			
    				//for(var i=0;i<ret.data.length;i++){
    				if($("#list li").length>=0){
    				$("#list li").remove();
    				for(var i=0;i<ret.data.length;i++){
	    				$("#list").append(
				'<li class="w-100b bb-1-eb h-48em lh-48em f-16em color-21 bg-color-whiter pl-16em" onclick="openInfo(\'hr_matrix_info_header\','+ret.data[i].party_no+')">'+ret.data[i].party_name+'</li>'
						
						)
	    			}
    			}else{
    			for(var i=0;i<ret.data.length;i++){
	    				$("#list").append(
		'<li class="w-100b bb-1-eb h-48em lh-48em f-16em color-21 bg-color-whiter pl-16em" onclick="openInfo(\'hr_matrix_info_header\','+ret.data[i].party_no+')">'+ret.data[i].party_name+'</li>'
						)
	    			}
    			}

    			}
			}
			else{
    			 if(page==1){
    			 	$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无内容</div>'
					);	
	    			api.hideProgress();//隐藏进度提示框
    			 }else if(page>1){
    			 	$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">已经到底啦~</div>'
					);	
	    			api.hideProgress();//隐藏进度提示框
    			 }
    			
    			api.hideProgress();//隐藏进度提示框
	    		/**无网络提示 */
//		    	api.toast({msg: ret.msg ,duration:3000,location: 'top'});
    		}
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

//打开党组织详情----------------------------------------
function openInfo(winName,type){
	api.openWin({
	    name: winName,
	    url: 'header/'+winName+'.html',
	    pageParam:{
	    	type:type,//党组织id
	    }
    });
}

//党组织层级tab切换---------------------------------------
function active(This){

	$('.current').html($(".tabBox2-active").html());
	$('#list2 li').removeClass('tabBox2-active');
	$(This).addClass('tabBox2-active');
	$('#list').html('');
	getHrPartyList($(This).attr('id'));//运行 （获取党建矩阵列表） 函数
	$(".current").html($(".tabBox2-active").html());//切换当前
};

$('#tabBox2').css('left',0);
var xxx = [];
//------------------------------------------------------------
var tabBox = document.getElementById("tabBox2");
tabBox.addEventListener("touchmove",function(evt){
	evt.preventDefault();
	var touch=evt.touches[0];
	var xx=parseInt(touch.clientX);
	xxx.push(xx);
	var leftNum = $('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2));
	var changeNum = (xxx[xxx.length-1]-xxx[xxx.length-2]);
	$('#tabBox2').css('left',leftNum-(-changeNum));
},false)
document.addEventListener("touchend",function(evt){
	xxx = [];
	if($('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2))>0){
		$('#tabBox2').css('left',0);
	}
	if($('#tabBox2').css('left').substring(0,($('#tabBox2').css('left').length-2))<$(window).width()-$('#tabBox2').outerWidth(true)){
		$('#tabBox2').css('left',($(window).width()-$('#tabBox2').outerWidth(true)));
	}
},false)

//关键字搜索----------------------------------------------------------
$('#keywork').keyup(function(){
	$('#list').html('');
	getHrPartyList();
})

//打开消息通知
function openMessage(winName){
	api.openWin({
	    name: winName,
	    url: '../../sp_oa/oa_message/header/'+winName+'.html',
	    pageParam:{
	    	
	    }
    });
}



