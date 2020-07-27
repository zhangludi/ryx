apiready = function (){
	get_dept_list();
}
//联系人选择后赋值
function evaluate(type_id,staff_name,staff_no){
	var staff_name=staff_name.split(",");
	var staff_no=staff_no.split(",");
	for(var i=0;i<staff_name.length;i++){
		$("#"+type_id).after(
			'<li class="aui-list-view-cell aui-img">'+
			    '<span class="staff_no" id="'+staff_no[i]+'" class="aui-img-body aui-text-info">'+staff_name[i]+'</span>'+
			    '<span class="aui-pull-right aui-text-danger" onclick="javascript:$(this).parent().remove()">删除</span>'+
			'</li>'
		)
	}
}

//页面初始化
function save(){
	 //获取上传列表id
	 var notice_attach="";
	 $("input[name='file_no']").each(function(){
		if(notice_attach==""){
			notice_attach=notice_attach+$(this).val()
		}
		else{
			notice_attach=notice_attach+","+$(this).val()
		}
	 })
	 if($("#notice_title").val()==""||$("#notice_content").val()==""){
	 	$aui.alert({title:'提示',content:'请填写数据',buttons:['确定']},function(ret){});
	 	//按钮解禁
		api.execScript({
			name:api.winName,
            script: 'releaseClick();'
        });
	 	return;
	 }
	showProgress();//加载等待
	var url = localStorage.url+"Api/Oa/save_notice_info";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "add_staff":localStorage.user_id,
	            "notice_title":$("#notice_title").val(),
	            "notice_content":$("#notice_content").val(),
	            "dept_no":$("#dept_no").val(),
	            "notice_attach":notice_attach
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			$aui.alert({
    				title:'发布成功',
    				content:ret.msg,
    				buttons:['确定']},
    				function(ret){
						//当为true时说明从快捷键进入，直接跳详情
    					if(localStorage.openList=="true"){
    						//打开新页面
							api.openWin({
							    name: 'notice_list',
							    url: 'header/notice_listHeader.html',
							});
							localStorage.openList=false;
						}
						else{
							api.execScript({
			    				name: 'notice_listHeader',
			   				    frameName: 'notice_list',
				                script: 'exec();'
		               		});
		           			api.closeWin({});
	           			}    			
	           	});
			}
			else{
				//按钮解禁
				api.execScript({
					name:api.winName,
		            script: 'releaseClick();'
		        });
				$aui.alert({title:'提示',content:ret.msg,buttons:['确定']},function(ret){});
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		//按钮解禁
			api.execScript({
				name:api.winName,
	            script: 'releaseClick();'
	        });
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}

//加载部门
function get_dept_list(type){
	showProgress();//加载等待
	var url = localStorage.url+"Api/Hr/get_dept_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			var dept_no="";//获取全部id
    			var data_json = "[";
    			var data1 = ret.list;//一级
    			for(var i=0;i<data1.length;i++){
    				var data2 = data1[i].list;//二级
    				for(var j = 0; j<data2.length; j++){
    					var data3 = data2[j].list;//三级
    					data_json+="{\"text\":\""+data2[j].dept_name+"\",\"value\":\""+data2[j].dept_no+"\",\"children\":["
    					dept_no+=data2[j].dept_no+","//id赋值
    					for(var k = 0; k<data3.length; k++){
    						data_json+="{\"text\":\""+data3[k].dept_name+"\",\"value\":\""+data3[k].dept_no+"\"},"
    						dept_no+=data3[k].dept_no+","//id赋值
    					}
    					if(data3.length > 0){
    						data_json = data_json.substring(0,data_json.length-1);
    					}
    					data_json+="]},";
    				}
    			}
    			data_json = data_json.substring(0,data_json.length-1);
    			data_json+="]"
    			//获取全部id
    			dept_no=dept_no.substring(0,dept_no.length-1);//截取id
    			$("#dept_no").val(dept_no);
    			//end
    			if(type=="选择"){
    				openMulti($("#department"),jQuery.parseJSON(data_json));
    			}
    			
			}
			else{
				$aui.alert({
    				title:'提示',
    				content:ret.msg,
    				buttons:['确定']},
    				function(ret){
    			});
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}
//监听text高度
function monitor_height(obj){
	if(obj.scrollHeight>104){
		$(obj).attr("style","height: "+obj.scrollHeight+"px");
	}
}


























function openPerson(winName){
	api.openWin({
	    name: winName,
	    url: 'header/'+winName+'.html'
    });
}
