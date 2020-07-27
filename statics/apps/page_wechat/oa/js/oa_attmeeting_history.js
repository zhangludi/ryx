//页面初始化
apiready = function () {
//alert(api.pageParam.id)
	//get_oa_meeting_list();//获取数据
    //openTab();//tab多页签切换
   // evenScrolltobottom("get_oa_meeting_list()");//上拉加载数据
  // get_oa_meeting_list2('2001');
    get_bd_type_list();//获取会议类型
    openPullRefresh('exec()');//下拉刷新
}
//页面刷新
function exec(){
	location.reload();
}
//获取会议类型
var winthNum = 0;
function get_bd_type_list(){	
	var url = localStorage.url+"/index.php/Api/Public/get_bd_type_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	           communist_no: localStorage.user_id,//登录人员编号
	           type_group:"meeting_type",
	           type_no:[2000,2001, 2002, 2003, 2004,2005,2006],	 
 			   status:1,            
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){  
    		//获取会议类型
    			for(var i=0;i<ret.data.length;i++){
						$("#tabBox2").append(
	    					'<li id="'+ret.data[i].type_no+'" class="mt-5em mb-5em" onclick="active(this)">'+
	    						'<a name="#tab2">'+ret.data[i].type_name+'</a>'+
	    						'<span class="active"></span>'+
	    					'</li>'
	    				)
	    				winthNum += $("#tabBox2 li").eq(i).outerWidth(true)+3;
	    				
					//get_oa_meeting_list(ret.data[i].type_no);
					if(api.pageParam.id==ret.data[i].type_no){
	    				active1($('#tabBox2 li').eq(i));
	    			}
	    			
    			}	
    				active($('#tabBox2 li').eq(0));
	    			$("#tabBox2").css('width',winthNum);
	    			
	    			
			}
			else{
				
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

//获取会议列表
function get_oa_meeting_list2(type_noo){
	var url = localStorage.url+"/index.php/Api/Manage/get_oa_meeting_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            communist_no: localStorage.user_id,//登录人员编号
	            meeting_type:type_noo,
	            status:2,//1:未开展 2:历史会议
	            page:1,//页数
	            pagesize:100//条数
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){     		
    			//获取会议列表
    			for(var k=0;k<ret.data.length;k++){      				
	    			if(ret.data[k].meeting_type==type_noo){	  
	    			$("#list").append(
		    		'<li class="clearfix pb-12em pt-12em" onclick="openSignIn(\'oa_attmeeting_signIn_header\','+ret.data[k].meeting_no+',\'历史会议\')">'+
		    			'<div class="pull-left w-97em h-73em">'+
		    			'<img class="bor-ra-2em w-97em h-73em" src="'+((ret.data[k].meetting_thumb!='')?localStorage.url+ret.data[k].meetting_thumb:'../../../statics/public/aui2/img/pic-list.png')+'">'+
		    			'</div>'+
		    			'<div class="pull-left over-h w-55b ml-13em bb-3em-e6 pb-12em">'+
		    				'<div class="pull-left mt-0em w-100b">'+
		    				' <div class="f-15em h-556em over-h color-21">'+InterceptField(ret.data[k].meeting_name,'无',10)+'</div>'+
		    				'<div class="color-9e f-12em">'+((ret.data[k].party_name!=null)?ret.data[k].party_name:'无')+'</div>'+
		    				' <div class="color-9e f-12em pull-left">'+ret.data[k].add_time+'</div>'+
		    				' <div class="color-a4 pull-right f-12em color-ff3032">'+((ret.data[k].host_name!=null)?ret.data[k].host_name:'无')+'</div>'+
		    				'</div>'+
		    			'</div>'+	    					    				
		    		'</li>'	
		    		)

    					
	    			}
    			}
			}
			else{
				$("#list #more").remove();
					$("#list").append(
						'<div class="aui-user-view-cell aui-text-center clearfix color-99" id="more">暂无会议</div>'
					);	
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}


var page=1;
//加载数据  旧
function get_oa_meeting_list(pull,tab){
	showProgress();//加载等待
	if(tab != undefined){//标签切换赋值，加载数据
		tab_id = tab;
	}
	//判断是否为下拉刷新，初始page
	if(pull == "pull"){
		$('#'+tab_id).html('');
		if(tab_id == "tab1"){
			meeting_type = 2001;
			page1 = 1;
		}else if(tab_id == "tab2"){
			meeting_type = 2002;
			page2 = 1;
		}else if(tab_id == "tab3"){
			meeting_type = 2003;
			page3 = 1;
		}else if(tab_id == "tab4"){
			meeting_type = 2004;
			page4 = 1;
		}else if(tab_id == "tab5"){
			meeting_type = 2005;
			page5 = 1;
		}else if(tab_id == "tab6"){
			meeting_type = 2006;
			page6 = 1;
		}
		page=1;
	}
	else{
		if(tab_id == "tab1"){
			meeting_type = 2001;
			page = page1;
		}else if(tab_id == "tab2"){
			meeting_type = 2002;
			page = page2;
		}else if(tab_id == "tab3"){
			meeting_type = 2003;
			page = page3;
		}else if(tab_id == "tab4"){
			meeting_type = 2004;
			page = page4;
		}else if(tab_id == "tab5"){
			meeting_type = 2005;
			page = page5;
		}else if(tab_id == "tab6"){
			meeting_type = 2006;
			page = page6;
		}
		
	}
	var url = localStorage.url+"/index.php/Api/Manage/get_oa_meeting_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "communist_no":localStorage.user_id,//党员编号
	            "meeting_type":meeting_type,//会议类型
	           	"status":2,//历史会议
	            "page":page,//页数
	            "pagesize":10//条数
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.data!=null){
					for(var i=0;i<ret.data.length;i++){
						if(ret.data[i].meeting_type==meeting_type){
							$("#"+tab_id).append(
								'<li class="bb-1-df pt-15em" onclick="openSignIn(\'oa_attmeeting_signIn_header\','+ret.data[i].meeting_no+',\'历史会议\')">'+
						            '<div class="over-h">'+
						            	'<img class="w-118em h-98em bor-ra-3 pull-left mr-12em" src="'+((ret.data[i].meetting_thumb!=null)?localStorage.url+ret.data[i].meetting_thumb:'../../../statics/public/aui2/img/pic-list.png')+'">'+
						                '<div class="w-220em pull-left">'+
						                    '<div class="h-18em mt-8em">'+
						                        '<div class="pull-left f-16em color-33 lh-18em f-w">'+InterceptField(ret.data[i].meeting_name,'无',10)+'</div>'+
						                    '</div>'+
						                    '<div class="f-12em color-fe9776 mt-8em lh-12em">'+ret.data[i].meeting_start_time+'</div>'+
						                    '<div class="f-12em color-33 mt-8em lh-12em">'+InterceptField(ret.data[i].meeting_addr,'无',15)+'</div>'+
						                    '<div class="f-10em color-33 mt-8em lh-10em">'+ret.data[i].memo.substring(0,40)+'</div>'+
						                '</div>'+
						            '</div>'+
						            '<div class="mt-10em f-12em lh-15em pb-15em color-33 over-h pt-10em">'+
						                '<p class="pull-left w-33b text-align br-1-df">主持:<span class="ml-5em color-99">'+(ret.data[i].host_name!=null?ret.data[i].host_name:'无')+'</span></p>'+
						                '<p class="pull-left w-33b text-align br-1-df">部门:<span class="ml-5em color-99">'+(ret.data[i].party_name!=null?ret.data[i].party_name:'无')+'</span></p>'+
						                '<p class="pull-left w-33b text-align">人数:<span class="ml-5em color-99">'+(ret.data[i].communist_num!=null?ret.data[i].communist_num:'无')+'人</span></p>'+
						            '</div>'+
						        '</li>'
							)
						}
					}
    				
    			
    				//判断标签分类追加
					if(tab_id == "tab1"){
						page1++;
					}else if(tab_id == "tab2"){
						page2++;
					}else if(tab_id == "tab3"){
						page3++;
					}else if(tab_id == "tab4"){
						page4++;
					}else if(tab_id == "tab5"){
						page5++;
					}else if(tab_id == "tab6"){
						page6++;
					}
    			
    			}

    			
			}
			else{
				if(page==1){
					$("#"+tab_id+" #more").remove();
					$("#"+tab_id).append(
						'<li class="pt-10em aui-text-center color-99" id="more">暂无会议</li>'
					);		
				}else if(page>1){
					$("#"+tab_id+" #more").remove();
					$("#"+tab_id).append(
						'<li class="pt-10em aui-text-center color-99" id="more">已经到底啦~</li>'
					);		
				}
				
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

//打开新页面
function openSignIn(winName,id,history) {
	api.openWin({
		name : winName,
		url : 'header/' + winName + '.html',
		pageParam:{
			id:id,
			history:history
		}
	});
}


//党组织层级tab切换---------------------------------------
function active1(This){
	$('#tabBox2 li').removeClass('tabBox2-active');
	$(This).addClass('tabBox2-active');
	//$('#list').html('');	
};
function active(This){
	$('#tabBox2 li').removeClass('tabBox2-active');
	$(This).addClass('tabBox2-active');
	$('#list').html('');
	get_oa_meeting_list2($(This).attr('id'));//运行 （获取党建矩阵列表） 函数
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



