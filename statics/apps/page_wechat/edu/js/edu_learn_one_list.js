var page=1;
apiready=function(){
	get_material_list();
	openPullRefresh('get_material_list("pull")');
	evenScrolltobottom('get_material_list()')
}
//获取资料详情
function get_material_list(pull){
	if(pull=='pull'){
		page=1;
		$('#list').html('');
	}
	showProgress();//加载等待
	var url = localStorage.url+"index.php/Api/Kb/get_material_list";
	api.ajax({
	    url:url,
	    method: 'post',
	    timeout: 100,
	    data: {//传输参数
	    	values: { 
	    		material_cat_id:api.pageParam.cat_id,
				page:page,
				pagesize:10
	        }
	    }
    },function(ret,err){
    	if(ret){
    		if(ret.status==1){
    			if(ret.cat_list!==null&&ret.cat_list!==''){
    				for(var i=0;i<ret.cat_list.length;i++){
    					if(ret.cat_list[i].material_list){
    						if(page==1){
    							if(api.pageParam.type=='video'){
	    							$('#list').append(
		    							'<ul class="aui-list-view mb-0">'+
									        '<li class="aui-list-view-cell">'+
									            '<i class="aui-iconfont aui-icon-edit aui-bg-info"></i>'+ret.cat_list[i].material_cat_name+
									        '</li>'+
									    '</ul>'+
									    '<ul id="'+ret.cat_list[i].material_cat_id+'" class="aui-list-view aui-grid-view">'+
									    '</ul>'
		    						)
	    						}else{
	    							$('#list').append(
		    							'<ul class="aui-list-view mb-0">'+
									       ' <li class="aui-list-view-cell">'+
									            '<i class="aui-iconfont aui-icon-edit aui-bg-info"></i>'+ret.cat_list[i].material_cat_name+
									        '</li>'+
									    '</ul>'+
									    '<ul id="'+ret.cat_list[i].material_cat_id+'" class="aui-list-view">'+
									    '</ul>'
		    						)
	    						}
    						}
    						$('#more').remove();
    						if(ret.cat_list[i].material_list.length==0){
    							if(api.pageParam.type=='video'){
    								$('#'+ret.cat_list[i].material_cat_id).append(
	    								'<li class="aui-user-view-cell aui-text-center fsize-16 w-100b" id="more">没有更多数据</li>'
	    							)
    							}else{
    								$('#'+ret.cat_list[i].material_cat_id).append(
	    								'<li class="aui-user-view-cell aui-text-center fsize-16" id="more">没有更多数据</li>'
	    							)
    							}
    							
    						}else{
    							for(var j=0;j<ret.cat_list[i].material_list.length;j++){
	    							if(ret.cat_list[i].material_list[j].material_vedio==null||ret.cat_list[i].material_list[j].material_vedio==''){
			    						if(api.pageParam.type=='art'){
			    							$('#'+ret.cat_list[i].material_cat_id).append(
				    							'<li onclick="openNewWin(\'edu_learn_artinfo_header\',this)" id="'+ret.cat_list[i].material_list[j].material_id+'" class="aui-list-view-cell aui-img">'+
										            '<img class="aui-img-object aui-pull-left" src="'+(ret.cat_list[i].material_thumb==null?'../../../statics/dangjian/public/image/dangjian_banner_1.png':localStorage.url+ret.cat_list[i].material_list[j].material_thumb)+'">'+
										            '<div class="aui-img-body">'+ret.cat_list[i].material_list[j].material_title+'<p class="aui-ellipsis-2">'+ret.cat_list[i].material_list[j].material_description+'</p>'+
										            '</div>'+
										        '</li>'
				    						);
			    						}
			    					}else{
			    						if(api.pageParam.type=='video'){
			    							$('#'+ret.cat_list[i].material_cat_id).append(
												'<li id="'+ret.cat_list[i].material_list[j].material_id+'" class="aui-list-view-cell aui-img aui-col-xs-6" onclick="openNewWin(\'edu_learn_info_header\',this)">'+
										        	'<div class="aui-img-object po-re">'+
										        		'<img class="w-100b h-100"  src="'+(ret.cat_list[i].material_list[j].material_thumb==null?'../../../statics/dangjian/public/image/dangjian_banner_1.png':localStorage.url+ret.cat_list[i].material_list[j].material_thumb)+'">'+
										        		'<div class="po-ab w-100b h-100b top-0 left-0 z-index-max bg-video"></div>'+
										        	'</div>'+
										            '<div class="aui-img-body">'+
										                '<div class="aui-text-left  aui-ellipsis-1">'+ret.cat_list[i].material_list[j].material_title+'</div>'+
										                '<div class="aui-text-left fsize-12 c-99 aui-ellipsis-2">'+ret.cat_list[i].material_list[j].material_description+'</div>'+
										            '</div>'+
										        '</li>'
											)
			    						}
			    					}
	    						}
    						}
    					}
    				}
    			}
    			page++;
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
//打开新页面
function openNewWin(winName,obj,type,cat_id){
	api.openWin({
        name: winName,
        url: 'header/'+winName+'.html',
        pageParam:{
        	id:$(obj).attr('id'),
        	type:type,
        	cat_id:cat_id
        }
    });
}