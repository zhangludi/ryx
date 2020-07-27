jQuery.ui = {
	//弹出框
	alert:function(s,ok) {          
	    var _alert_dialog= dialog({
			id:"id-alert",
			title:"系统提示:",
			width:400,
			height:35,
			padding:25,
			fixed:true,
			cancel:false,
	    	content:'<table class="artdialogtable"><tr><td><div class="artdialogicons"></div></td><td  class="artdialogtxt">'+s+'</td></tr></table>',
	    	cancelValue: '取  消',
	    	okValue: '确  认',
	    	ok: function () {
	    		if(ok){
	    			ok();
	    		}
	    	}
		}).showModal();   
	},
	confirm:function(s,ok,cancel){
		dialog({
			id:"id-confirm",
			title:"操作确认:",
			width:400,
			height:35,
			padding:25,
			fixed:true,
			quickClose: true,
	    	content:'<table class="artdialogtable"><tr><td><div class="artdialogicons artdialogicons-confirm"></div></td><td  class="artdialogtxt">'+s+'</td></tr></table>',
	    	cancelValue: '取  消',
	    	okValue: '确  认',
	    	ok: function () {
	    		if(ok){
	    			ok();
	    		}
	    	},
		    cancel: function () {
		    	if(cancel){
		    		cancel();
		    	}
		    }
		}).showModal();
	},tip:function(content,time,ele){
		if(!time){
			time=1;
		}
		var dialog_tip=dialog({
			id:'id-tips',
			content:"<span class='red'>"+content+"</span>",
			padding:10
		}).show();
		if(ele){
			dialog_tip.show(ele);
		}else{
			dialog_tip.show();
		}
		setTimeout(function () {
			dialog_tip.close().remove();
		}, time*1000);
	},loading:function(show,type){
		if(type==null || type ==undefined){
			type=1;
		}
		if(show == null || show == true){
			if(type==1){
				$("#loadings").show();
			}
			$("#webloading").show();
		}else{
			$("#webloading").hide(10);
			$("#loadings").hide();
		}
	},prom:function(callback,tip,len,lesslen){//回调、提示文字、可输入文字长度、最小长度
		if(!len){
			len=100;
		}
		if(!lesslen){
			lesslen=5;
		}
		if(!tip){
			tip="请输入您的意见：";
		}
		dialog({
			id:"id-prom",
			title:"信息输入:",
			width:600,
			padding:25,
			fixed:true,
			padding:0,
			quickClose: true,
	    	content:'<div id="dialog-texarea-msg" class="dialog-texarea-msg">'+tip+'</div><textarea onkeyup="dialogwordCount(this,'+len+','+lesslen+')" id="dialog-teaxarea" class="dialog-teaxarea"></textarea><div class="ui-dialog-textcount">您还可以输入<span id="udt-wordcount" class="red">'+len+'/'+len+'</span><span id="udt-lesscount"> ,  最少输入'+lesslen+'字</span></div>',
	    	cancelValue: '取  消',
	    	okValue: '确  认',
	    	ok: function () {
	    		var nowLen=$("#dialog-teaxarea").val().length;
	    		if(nowLen<lesslen){
	    			$.ui.tip("至少输入"+lesslen+"字！");
	    			return false;
	    		}
	    		if(callback){
	    			callback($("#dialog-teaxarea").val());
	    		}
	    	},
		    cancel: function () {}
		}).showModal();
		$("#dialog-teaxarea").focus();
	}
}

