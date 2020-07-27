apiready = function () {
	exitApp()
	getEduExamInfo()//调用（获取考试详情）接口
	//$('#questions_list').hide()
	//$('#submitExam').hide()
}

// 退出提醒
function exitApp() {
	api.addEventListener({
		name: 'keyback'
	}, function (ret, err) {
		//利用对话框返回的值 （true 或者 false）
		if (confirm("确定离开考试页面吗？")) {
			//点击了确定
			api.openWin({
				name: 'edu_exam',
				url: '../edu_exam.html',
			});
			setInterval(function () {
				api.closeWin();
			}, 200)
		}
		else {
			//点击了取消
		}
	})
}


//获取考试详情接口------------------------------------------------------------------------------------------------
function getEduExamInfo() {
	showProgress();//加载等待
	var url = localStorage.url + "/index.php/Api/Edu/get_edu_exam_info";
	api.ajax({
		url: url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: {
				"communist_no": localStorage.user_id,//登录人员编号
				"exam_id": api.pageParam.id,//试题编号
			}
		}
	}, function (ret, err) {
		if (ret) {
			if (ret.status == 1) {
				var data = ret.data
				if (data.questions_list && data.questions_list.length > 0) {
					$("#strBtn").html(JSON.stringify(data));
					// 页面初始化执行的方法必须在这个里面调用 不能在($())//这种形式
					showQuestion(0);
					answerCard();
				}
				api.hideProgress();//隐藏进度提示框
			} else {
				api.hideProgress();//隐藏进度提示框
				/**无网络提示 */
				api.toast({ msg: ret.msg, duration: 3000, location: 'top' });
			}
		} else {
			api.hideProgress();//隐藏进度提示框
			/**无网络提示 */
			api.toast({ msg: '网络链接失败...', duration: 3000, location: 'top' });
		}
	})
}




//提交考试接口------------------------------------------------------------------------------------------------
function setEduExamQuestions() {
	showProgress();//加载等待
	var questionsArr = '{';
	for (var i = 0; i < $('ul').length; i++) {
		questionsArr = questionsArr + '"' + $('ul').eq(i).find('.questionsId').attr('noo') + '"' + ':' + '"';
		for (var j = 0; j < $('ul').eq(i).find('li').length; j++) {
			if ($('ul').eq(i).find('li').eq(j).attr('select') == 'true') {
				questionsArr = questionsArr + $('ul').eq(i).find('li').eq(j).find('span').html().substring(0, 1) + ',';
			}
		}
		questionsArr = questionsArr.substring(0, questionsArr.length - 1) + '"' + ',';
	}
	questionsArr = questionsArr.substring(0, questionsArr.length - 1) + '}';
	var url = localStorage.url + "/index.php/Api/Edu/set_edu_exam_questions";
	//	alert(localStorage.user_id);
	//	alert(api.pageParam.type);
	//	alert(questionsArr);
	api.ajax({
		url: url,
		method: 'post',
		timeout: 100,
		data: {//传输参数
			values: {
				"communist_no": localStorage.user_id,
				"exam_id": api.pageParam.type,
				"questions_arr": questionsArr,
			}
		}
	}, function (ret, err) {
		if (ret) {
			if (ret.status == 1) {
				api.hideProgress();//隐藏进度提示框
				alert('提交成功');
				api.closeWin();
				if (api.pageParam.no == 1) {
					//打开页面加刷新
					api.execScript({
						name: 'edu_special_twostudies_header',
						frameName: 'edu_special_twostudies',
						script: 'exec();'
					});
				} else if (api.pageParam.no == 2) {
					api.execScript({
						name: 'edu_exam_header',
						frameName: 'edu_exam',
						script: 'exec();'
					});
				}

			} else {
				api.hideProgress();//隐藏进度提示框
				/**无网络提示 */
				api.toast({ msg: ret.msg, duration: 3000, location: 'top' });
			}
		} else {
			api.hideProgress();//隐藏进度提示框
			/**无网络提示 */
			api.toast({ msg: '网络链接失败...', duration: 3000, location: 'top' });
		}
	});
}



//考试未开始或者考试开始后推出----------------------------------------------------------------------------------
function goBlackExam() {
	if (theExamBegins == 'true') {
		$('#whetherToStart').removeClass('di-n');
		$('#blackTheExam').click(function () {
			api.closeWin();
		});
		$('#continueTheExam').click(function () {
			$('#whetherToStart').addClass('di-n');
		})
	} else {
		api.closeWin();
	}
}

//刷新页面-----------------------------------------------------------------------------------------------------
function exec() {
	location.reload();
}



//打开学习分析页面-------------------------------------------------------------------------------------------
function edu_exam_analysis(winName) {
	api.openWin({
		name: winName,
		url: 'header/' + winName + '.html',
		reload: true,
		pageParam: {

		}
	})
}

var activeQuestion = 0; //当前操作的考题编号
var questioned = 0; //
var currentSlide = 0;
var checkQues = []; //已做答的题的集合
var itemList = ["A", "B", "C", "D", "E", "F"];



//展示考卷信息
function showQuestion(id) {
	$(".questioned").text(id + 1);
	var text = JSON.parse($("#strBtn").html()).questions_list;
	questioned = (id + 1) / text.length;
	if (activeQuestion != undefined) {
		$("#ques" + activeQuestion).removeClass("question_id").addClass("active_question_id");
	}
	activeQuestion = id
	$(".question").find(".question_info").remove();
	var question = text[id];
	$(".question_title").html("<strong>" + (id + 1) + "  、</strong>" + question.questions_title);
	$(".questions_type").html(question.questions_type);
	$(".xuanxiang").html(question.questions_answer);
	$('.content_classes_msg .answer_memo').html(question.memo)
	var items = question.questions_item.split(",");
	var item = "";

	for (var i = 0; i < items.length; i++) {
		item = `<li class="question_info content_classes_bots radio icheck-success"
								onclick="clickTrim(this)" id= "item${i}">
								<input type="radio" name=" item success1" id="success${itemList[i]}">
								<label for="success1" style="position: relative;"><span class="xuanxiang${i} options">${itemList[i]} . </span><span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span></label>
								</li>`

		$(".question").append(item);
	}
	$(".question").attr("id", "question" + id);
	$("#ques" + id).removeClass("active_question_id").addClass("question_id");
	for (var i = 0; i < checkQues.length; i++) {
		if (checkQues[i].id == id) {
			$("#" + checkQues[i].item).find("input").prop("checked", "checked");
			$("#" + checkQues[i].item).addClass("clickTrim");
			$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
		}
	}
}

/*答题卡*/
function answerCard() {
	var text = JSON.parse($("#strBtn").html()).questions_list;
	$(".question_sum").text(text.length);
	for (var i = 0; i < text.length; i++) {
		var questionId =
			"<li id='ques" + i + "'onclick='saveQuestionState(" + i +
			")' class='questionId  pull-left'>" +
			(i + 1) +
			"</li>";
		$("#answerCard ul").append(questionId);
	}
}

/*选中考题*/
var Question;

function clickTrim(source) {
	var id = source.id;
	//let i = id.slice(-1);

	$("#" + id).find("input").prop("checked", "checked");
	$("#" + id).find('.options').hide().parents('li').siblings().find(".options").show();

	$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
	var ques = 0;
	for (var i = 0; i < checkQues.length; i++) {
		if (checkQues[i].id == activeQuestion && checkQues[i].item != id) {
			ques = checkQues[i].id;
			checkQues[i].item = id; //获取当前考题的选项ID
			checkQues[i].answer = $("#" + id).find("input[name=item]:checked").val(); //获取当前考题的选项值
		}
	}
	if (checkQues.length == 0 || Question != activeQuestion && activeQuestion != ques) {
		var check = {};
		check.id = activeQuestion; //获取当前考题的编号
		check.item = id; //获取当前考题的选项ID
		check.answer = $("#" + id).find("input[name=item]:checked").val(); //获取当前考题的选项值
		checkQues.push(check);
	}
	$(".question_info").each(function () {
		var otherId = $(this).attr("id");
		if (otherId != id) {
			$("#" + otherId).find("input").prop("checked", false);
			$("#" + otherId).removeClass("clickTrim");
		}
	})
	Question = activeQuestion;
}




/*保存考题状态 已做答的状态*/
function saveQuestionState(clickId) {
	$('.z-index-mask').hide()
	$('.answer_analysis').css('display', 'none')
	showQuestion(clickId)
	var text = JSON.parse($("#strBtn").html()).questions_list
		// activeQuestion是当前操作的考题编号
		showQuestion(activeQuestion);
		if (activeQuestion == text.length-1) {
			$('#nextQuestion').hide();
			$('.submitQuestions').show();
		}
		if (activeQuestion !== text.length-1) {
			$('.submitQuestions').hide();
			$('#nextQuestion').show();
		}
}

$(function () {
	$(".middle-top-left").width($(".middle-top").width() - $(".middle-top-right").width())
	$(".progress-left").width($(".middle-top-left").width() - 200)


	//提交试卷
	$(".submitQuestions").click(function () {
		var text = JSON.parse($("#strBtn").html()).questions_list
		console.log($(".clickQue").length)
		if ($(".clickQue").length === text.length) {
			//全部做答   弹出得分遮罩层
			$(".edu_analysis_mask").show();
		} else {
			$.message({
				message: (text.length - ($(".clickQue").length)) + "道题未做哦",
				type: 'info'
			})
		}

	})
	//进入下一题
	$("#nextQuestion").click(function () {
		var text = JSON.parse($("#strBtn").html()).questions_list
		// activeQuestion是当前操作的考题编号
		if ((activeQuestion + 1) != text.length) showQuestion(activeQuestion + 1);
		showQuestion(activeQuestion);
		$('.answer_analysis').css('display', 'none')
		if (activeQuestion == text.length - 1) {
			$('#nextQuestion').hide();
			$('.submitQuestions').show();
		}
		if (activeQuestion !== text.length - 1) {
			$('.submitQuestions').hide();
			$('#nextQuestion').show();
		}
	})
})

// 打开学习卡
$('#opencard').click(function () {
	$('.z-index-mask').show();
})
// 关闭学习卡
$('.mask_close').click(function () {
	$('.z-index-mask').hide();
})


// 时间倒计时
var maxtime = 60 * 20;//一个小时，按秒计算，自己调整!
function CountDown() {
	$('.false').hide();
	if (maxtime >= 0) {
		minutes = Math.floor(maxtime / 60);
		minutes = minutes < 10 ? '0' + minutes : minutes;
		seconds = Math.floor(maxtime % 60);
		seconds = seconds < 10 ? '0' + seconds : seconds;
		msg = `<span>剩余时间 </span>
				<span class="time minute">${minutes}</span>:
				<span class="time second">${seconds}</span>`
		document.all["timer"].innerHTML = msg;
		if (maxtime == 5 * 60) $.message({
			message: '考试还剩5分钟',
			type: 'info'
		})
		--maxtime;
	} else {
		clearInterval(timer)
		api.confirm({
			title: '时间到，考试结束! 请停止答题',
			msg: telNum,
			buttons: ['确定']
		}, function (ret, err) {
			var index = ret.buttonIndex;
			if (index == 1) {
				api.openWin({
					name: 'analysis',
					url: './edu_exam_analysis.js',
					pageParam: {
						name: 'test'
					}
				});
			}
		})
	}
}
timer = setInterval("CountDown()", 1000)

// 答案解析显示隐藏
var n = 0
$('.content_class_answer').click(function () {
	n++
	if (n % 2 !== 0) {
		$('.answer_analysis').show()
	} else {
		$('.answer_analysis').hide()
	}
})
