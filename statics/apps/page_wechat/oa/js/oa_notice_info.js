 apiready=function(){
	//data();//加载数据方法
 }
 function data(){
 	showProgress();//加载等待
 	var url = localStorage.url+"Api/Oa/get_notice_info";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	            "notice_id":api.pageParam.id,
	            "staff_no":localStorage.user_id
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.info.notice_title==null||ret.info.notice_title==""){
    				ret.info.notice_title='无标题'
    			}
    			var portrait=get_head(ret.info.staff_avatar,ret.info.add_staff);
				$("#title_content").before(portrait);
    			$("#notice_title").html(ret.info.notice_title+"<br/><p class='fw-n'>"+new Date(ret.info.add_time!=null?ret.info.add_time.replace(/-/g,"/"):"").format('yyyy-MM-dd')+"</p>");
    			$("#add_staff").html(ret.info.add_staff);
    			$("#date").html(ret.info.add_time);
    			if(ret.info.notice_content==null){
    				ret.info.notice_content="";
    			}
    			$("#notice_content").html(removeHTMLTag(ret.info.notice_content));
    			$("#upload_length").html(ret.info.file_list.length);
    			if(ret.info.file_list.length>0){
    				$("#info_upload").show();
    			}
    			var img="";
    			for(var i=0;i<ret.info.file_list.length;i++){
    				 if($("#whole").val()==""){
				       $("#whole").val(localStorage.url+ret.info.file_list[i].upload_path)
				    }else{
				       $("#whole").val($("#whole").val()+","+localStorage.url+ret.info.file_list[i].upload_path)
				    }
    				img=upold_img(ret.info.file_list[i].upload_path);
    				$("#info_upload_list").append(
//			         '<li class="aui-list-view-cell" onclick="openEnqueue(\''+localStorage.url+ret.info.file_list[i].upload_path+'\');openManagerView()">'+
//                          img+
//                          '<div class="aui-text-info aui-pull-right ellipsis-one aui-col-xs-8 ellipsis-one ml-15">'
//                          +ret.info.file_list[i].upload_source+
//                              '<input name="file_no" type="hidden" value="'+ret.info.file_list[i].upload_id+'" />'+             
//                          '</div>'+
//                      '</li>'
					'<li class="aui-list-view-cell" onclick="openEnqueue(\''+localStorage.url+ret.info.file_list[i].upload_path+'\');openManagerView()">'+
                         img+
                        '<span class="aui-text-info ellipsis-one aui-col-xs-12 ellipsis-one ml-15">'
                        	+ret.info.file_list[i].upload_source.substring(ret.info.file_list[i].upload_source.length,ret.info.file_list[i].upload_source.length-20)+
                            '<input name="file_no" type="hidden" value="'+ret.info.file_list[i].upload_id+'" />'+    
                        '</span>'+
                    '</li>'
    				)
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
    	$("#conter").show();
    });
 }
//切换下拉及加载数据
function dropDownSwitch(obj){
    if($(obj).attr("id") == "info_upload_fold"){
        list_id = "info_upload_list";
    }    
    //切换当前分类显示与隐藏
    $(obj).addClass("aui-fold-active");
    $(obj).children("i").html("&#xe6fe;");
    $(obj).siblings("li").removeClass("aui-fold-active");
    $(obj).siblings("li").children("i").html("&#xe61a;");    
    $("#"+list_id).parents(".aui-fold-content").siblings(".aui-fold-content").slideUp();//关闭非当前行分类
    $("#"+list_id).parents(".aui-fold-content").slideToggle(function(){
        if ($(this).is(':hidden')) {
            $(obj).removeClass("aui-fold-active");
            $(obj).children("i").html("&#xe61b;");
        }
    });
}
//下载全部附件
function foreach_file(){
	var file=$("#whole").val().split(",");
    //阻止事件冒泡
    $("#download").mousedown(function(event){
        event.stopPropagation();
    });
    if(file.length>1){
       for(var i=0;i<file.length;i++){
            openEnqueue(''+file[i]+'');
        }
        openManagerView() 
    }else{
        openEnqueue(''+file[0]+'');
        openManagerView()
    }
}
