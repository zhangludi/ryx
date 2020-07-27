var integral=0;//积分
apiready=function(){
	get_material_info();
}
//获取资料详情
function get_material_info(){
	showProgress();//加载等待
	var url = localStorage.url+"index.php/Api/Kb/get_material_info";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		material_id:api.pageParam.id,
	    		staff_no:localStorage.user_id
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.cat_list.material_thumb!==''&&ret.cat_list.material_thumb!==null){
    				$('#img').attr('src',localStorage.url+ret.cat_list.material_thumb);
    			}
    			$('#title').html(ret.cat_list.material_title);
    			$('#add_time').html(ret.cat_list.add_time);
    			$('#material_content').html(ret.cat_list.material_content);
    			$('#add_staff').html(ret.cat_list.add_staff);
    			integral=ret.cat_list.material_integral;
    			if(ret.cat_list.is_read=='0'){
    				$('#tip').html('恭喜获得'+integral+'积分');
    				set_Material_Log();
    			}
    			if(ret.cat_list.material_notes!==null&&ret.cat_list.material_notes!==''){
    				$('#material_notes').val(ret.cat_list.material_notes);
    			}
    			//附件
    			if(ret.cat_list.material_attach!==''&&ret.cat_list.material_attach!==null){
    				var arr=ret.cat_list.material_attach.split(',')
	    			$("#upload_length").html(arr.length);
	    			if(arr.length>0){
	    				$("#info_upload").show();
	    			}
	    			var img="";
	    			for(var i=0;i<arr.length;i++){
	    				 if($("#whole").val()==""){
					       $("#whole").val(localStorage.url+arr[i])
					    }else{
					       $("#whole").val($("#whole").val()+","+localStorage.url+arr[i])
					    }
	    				img=upold_img(arr[i]);
	    				$("#info_upload_list").append(
							'<li class="aui-list-view-cell" onclick="openEnqueue(\''+localStorage.url+arr[i]+'\');openManagerView()">'+
		                         img+
		                        '<span class="aui-text-info ellipsis-one aui-col-xs-12 ellipsis-one ml-15">'
		                        	+arr[i].substring(arr[i].length-10,arr[i].length)+
		                            '<input name="file_no" type="hidden"  />'+    
		                        '</span>'+
		                    '</li>'
	    				)
	    			}
    			}
			}
			else{
				alert(ret.msg,"提示");
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}
//增加积分
function set_Material_Log(){
	showProgress();//加载等待
	var url = localStorage.url+"index.php/Api/Kb/set_Material_Log";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		material_id:api.pageParam.id,
	    		staff_no:localStorage.user_id
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			$('#tips-1').show();
    			var timer=setTimeout(function(){
    				closeTips();
    				clearTimeout(timer);
    			},5000)
			}
			else{
				alert(ret.msg,"提示");
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}
//提交学习心得
function set_material_notes(){
	showProgress();//加载等待
	var url = localStorage.url+"index.php/Api/Kb/set_material_notes";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		material_id:api.pageParam.id,
	    		material_notes_staff:localStorage.user_id,
	    		material_notes_content:$('#material_notes').val()
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			alert('提交成功')
			}
			else{
				alert(ret.msg,"提示");
			}
			api.hideProgress();//隐藏进度提示框
    	}else{
    		api.hideProgress();//隐藏进度提示框
    		/**无网络提示 */
	    	api.toast({msg: '网络链接失败...',duration:3000,location: 'top'});
    	}
    });
}
function closeTips(){
	$('#tips-1').hide();
}
//轮播
var slide = new auiSlide({
    container:document.getElementById("aui-slide"),
    // "width":300,
    "height":125,
    "autoPlay": 5000,
    "speed":300,
    "pageShow":true,
    "pageStyle":'dot',
    "loop":true,
    'dotPosition':'center'
})
//打开新页面
function openNewWin(winName,type){
	api.openWin({
        name: winName,
        url: 'header/'+winName+'.html',
        pageParam:{
        	type:type
        }
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
