;
/* js全局配置文件 */
var loginObj = {
	// 获取验证码
	getverifCode : function() {

	},
	init : function() {
		$("#login-btn").bind('click', function() {
			loginObj.valiData();
		});
		if ($.cookie('loginId') != "null") {
			$("#loginId").val($.cookie('loginId'));
		} else {
			$("#loginId").val("");
		}
		if ($.cookie('password') != "null") {
			$("#password").val($.cookie('password'));
		} else {
			$("#password").val("");
		}
	},
	valiData : function() {
		var mobile = $("#loginId").val();
		if (mobile == "") {
			loginObj.showError($("#loginId"), "请填写注册手机号！");
			return;
		} else {
			loginObj.hideError($("#loginId"));
		}
		/*if (mobile.match(/^1[3-9]\d{9}$/) == null) {
			loginObj.showError($("#loginId"), "请填写正确的手机号！");
			return;
		} else {
			loginObj.hideError($("#loginId"));
		}*/
		var pass = $("#password").val();
		if (pass == "") {
			loginObj.showError($("#password"), "请填写密码！");
			return;
		} else {
			loginObj.hideError($("#password"));
		}
		/*if (pass.match(/^[\da-zA-z]{6,12}$/) == null) {
			loginObj.showError($("#password"), "请填写正确的密码！");
			return;
		} else {
			loginObj.hideError($("#password"));
		}*/
		var verificationCode = $("#verificationCode").val();
		if (verificationCode == "") {
			loginObj.showError($("#verificationCode"), "请填写验证码！");
			return;
		} else {
			loginObj.hideError($("#verificationCode"));
		}
		var data = {};
		data["loginId"] = mobile;
		data["password"] = pass;
		data["verificationCode"] = verificationCode;
		// 记住账号和密码
		if ($("#remebCount").is(':checked')) {
			$.cookie('loginId', mobile, {
				expires : 7
			});
			$.cookie('password', pass, {
				expires : 7
			});
		} else {
			$.cookie('loginId', null);
			$.cookie('password', null);
		}
		loginObj.toLogin(data);
	},
	toLogin : function(data) {
		$.ajax({
			type : "POST",
			url : authConfig.baseUrl + "/member/login.do",
			data : {
				"val" : JSON.stringify(data)
			}
		}).done(function(json) {
			if (json.head.resultCode == 200) {
				var url = loginObj.getUrlParam("url1");
				if (url != null) {
					if (url.indexOf("?") > 0)
						url += "?sid=" + json.body.sid;
					else
						url += "&sid=" + json.body.sid;
					location.href = url;
				}else
					location.href = "/index?sid="+json.body.sid
			} else {
				loginObj.showError($("#loginId"), json.head.message);
			}
		}).fail(function() {
			loginObj.showError($("#loginId"), json.head.message);
		});

	},
	showError : function(obj, str) {
		obj.parents("div").eq(0).addClass("has-error");
		str = '<i class="ep ep-warning m-r-10"></i>' + str;
		$(".error-msg>div>span").html(str);
		$(".error-msg").show();
	},
	hideError : function(obj) {
		$(".error-msg").hide();
		$(".error-msg>div>span").html("");
		obj.parents("div").eq(0).removeClass("has-error");
	},
	getUrlParam:function(name)
	{
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
		var r = window.location.search.substr(1).match(reg);  //匹配目标参数
		if (r!=null) return unescape(r[2]); return null; //返回参数值
	} 
};
$(function() {
	var mobile = sessionStorage.getItem("mobile");
	if (mobile != "") {
		$("#loginId").val(mobile);
	}
	loginObj.init();
	// 绑定验证码点击事件
	$("#refreshCode").bind(
			"click",
			function() {
				$(this).attr("src",
						"/member/putImage.do?data=" + new Date());
			});
});


