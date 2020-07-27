/********************************时间倒计时********************************************/
var exam_time = $('#exam_time').val();
var maxtime=60*exam_time;
function CountDown() {
	$('.false').hide();
	if (maxtime >= 0) {
		minutes = Math.floor(maxtime / 60);
		minutes = minutes < 10 ? '0' + minutes : minutes;
		seconds = Math.floor(maxtime % 60);
		seconds = seconds < 10 ? '0' + seconds : seconds;
		msg = `<span>剩余时间 </span>
				<span class="time minute">${minutes}</span>&nbsp;:
				<span class="time second">${seconds}</span>`
		document.all["timer"].innerHTML = msg;
		if (maxtime == 5 * 60) $.message({
			message: '考试还剩5分钟',
			type: 'info'
		})
		--maxtime;
	} else {
		clearInterval(timer)
		alert('时间到，考试结束! 请停止答题');
		api.closeWin({})
	}
}

var timer = setInterval("CountDown()", 1000);





var activeQuestion = 0; //当前操作的考题编号
var questioned = 0; //
var currentSlide = 0;
var checkQues = []; //已做答的题的集合
var itemList = ["A", "B", "C", "D", "E", "F"];
var questions_list = JSON.parse($("#strBtn").html()).questions_list;
/********************************展示考卷信息********************************************/
var questions_list_length = questions_list.length;//当前考题的题数
$("#questions_list_length").val(questions_list_length);
showQuestion(0);
function showQuestion(id) {
	//$(".questioned").html(id + 1);
	//var text = JSON.parse($("#strBtn").html()).questions_list;
	var text = questions_list;
	var str_questions_id = '';
	$("#length").val(text.length);
	for (var i=0; i<text.length;i++) {
		str_questions_id += text[i].questions_id+',';
	}

	$('#str_questions_id').val(str_questions_id);
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
	var itemDiv = '';
	var trues = question.questions_type == '单选题' ? true : false

	// 显示选项列表
	for (var i = 0; i < items.length; i++) {
		itemDiv += '<li class="question_info content_classes_bots radio icheck-success" onclick="clickTrim(this,'+question.questions_id+')" id="item' + i + '">'
		if (trues) {
			itemDiv += ' <input type="radio" data-val="'+itemList[i]+'" name="item" id="success' + itemList[i] + '" value="' + itemList[i] + '">'
		} else {//多选题
			itemDiv += ' <input type="checkbox" data-val="'+itemList[i]+'" name="item'+i+'" id="success' + itemList[i] + '" value="' + itemList[i] + '">'
		}
		itemDiv += ' <label for="item'+i+'" style="position: relative;">'
		itemDiv += '   <span class="xuanxiang' + i + ' options" >' + itemList[i] + '.</span><span class="pull-right" style="padding-left:1rem;display:block">' + items[i] + '</span>'
		itemDiv += ' </label>'
		itemDiv += '</li>'

		$("#question_list").html(itemDiv);
		
	}

	$(".question").attr("id", "question" + id);
	$("#ques" + id).removeClass("active_question_id").addClass("question_id");
	for (var i = 0; i < checkQues.length; i++) {
		if (checkQues[i].id == id) {
			if(checkQues[i].type == 1){
				$("#" + checkQues[i].item).find("input").prop("checked", "checked");
				$("#" + checkQues[i].item).addClass("clickTrim");
				$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
			} else {
				if(checkQues[i].answer != undefined){
					$("#question_list").find("input[type='checkbox']:checked").each(function () {
						$(this).prop("checked", false);
					})
					var checkbox_answer_arr = checkQues[i].answer.split(',');
					for (var a = 0; a < checkbox_answer_arr.length; a++) {
						$("#question_list").find("input[data-val="+checkbox_answer_arr[a]+"]").prop("checked", "checked");
//						$("#" + checkQues[i].item).addClass("clickTrim");
						$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
					}
				}
			}
		}
	}
}

/*答题卡*/
answerCard();
function answerCard() {
	//var text = JSON.parse($("#strBtn").html()).questions_list;
	var text =questions_list;
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
/********************************选中考题********************************************/
var Question;
function clickTrim(source,questions_id) {
	var id = source.id;
	var is_check = $(source).find('input:checkbox').prop('checked');
	var next_element = $(source).find('input:checkbox').length; // 根据checkbo判断是否多选
	if(next_element == 0){
		var type = 1; // 单选
		$("#" + id).find('.options').hide().parents('li').siblings().find(".options").show();
		$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
	} else {
		var type = 2; // 多选
		$("#" + id).find('.options').hide();
		$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
	}
	$("#" + id).find("input").prop("checked", "checked");
//	$("#" + id).find('.options').hide().parents('li').siblings().find(".options").show();
//	$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue");
	var ques = 0;
	for (var i = 0; i < checkQues.length; i++) {
		if (checkQues[i].id == activeQuestion && checkQues[i].item != id) {
			ques = checkQues[i].id;
			checkQues[i].item = id; //获取当前考题的选项ID
			checkQues[i].answer = $("#" + id).find("input[name=item]:checked").val(); //获取当前考题的选项值
		}
	}
	var checkbox_answer_str = '';
	$("#question_list").find("input[type='checkbox']:checked").each(function () {
		var answer_str = $(this).data('val');
		checkbox_answer_str += answer_str+',';
	})

	if(checkbox_answer_str != ''){
		checkbox_answer_str = checkbox_answer_str.substring(0,checkbox_answer_str.length-1);
	}
	if(type == 1){ // 单选
		//if (checkQues.length == 0 || Question != activeQuestion && activeQuestion != ques) {
			var check = {};
			check.id = activeQuestion; //获取当前考题的编号
			check.questions_id = questions_id;
			check.item = id; //获取当前考题的选项ID
			check.answer = $("#" + id).find("input[name=item]:checked").val(); //获取当前考题的选项值
			check.type = type; //获取当前考题的选项值
			checkQues.push(check);
		//}
	} else {
		var check = {};
		check.id = activeQuestion; //获取当前考题的编号
		check.questions_id = questions_id;
		check.item = id; //获取当前考题的选项ID
		check.answer = checkbox_answer_str;
		check.type = type; //获取当前考题的选项值
		checkQues.push(check);
	}

	var target = {};


	checkQues.forEach(item => {
		var source = JSON.parse(`{"${item.questions_id}":"${item.answer}"}`); //将数据转换为 JavaScript 对象
		Object.assign(target, source);//拷贝

	});
	//转换成数组
	target8 = target;
	var target_array = new Array();
	$.each(target8,function(k,v){
	    target_array.push(v);
	});
	$('#target_array').val(target_array.length);
	//转换成数组end
	var target1=JSON.stringify(target);//将对象转为字符串
	$('#questions_arr').val(target1);
	localStorage.setItem("target",target1);



	$(".question_info").each(function () {
		var otherId = $(this).attr("id");
		if (otherId != id) {
			if(next_element == 0){ //radio
				$("#" + otherId).find("input").prop("checked", false);
			} else {
				if(is_check == true){
					$(source).find("input:checkbox").prop("checked", false);
					$(source).find('.options').show()
					var check_length = $("#question_list").find("input[type='checkbox']:checked").length;
					if(check_length == 0){
						$("#ques" + activeQuestion).removeClass("question_id").removeClass("clickQue");

					}
					var checkbox_answer_str2 = '';
					$("#question_list").find("input[type='checkbox']:checked").each(function () {
						var answer_str = $(this).data('val');
						checkbox_answer_str2 += answer_str+',';
					})
					if(checkbox_answer_str2 != ''){
						checkbox_answer_str2 = checkbox_answer_str2.substring(0,checkbox_answer_str2.length-1);
					}
					var check = {};
					check.id = activeQuestion; //获取当前考题的编号
					check.item = id; //获取当前考题的选项ID
					check.answer = checkbox_answer_str2;
					check.type = type; //获取当前考题的选项值
					checkQues.push(check);
					var target = {};
					checkQues.forEach(item => {
						var source = JSON.parse(`{"${item.id + 1}":"${item.answer}"}`); //将数据转换为 JavaScript 对象
						Object.assign(target, source);//拷贝

					});
					  
					var target1=JSON.stringify(target);//将对象转为字符串
					$('#questions_arr').val(target1);
					localStorage.setItem("target",target1);
					// alert($('#questions_arr').val())
				}
			}
			$("#" + otherId).removeClass("clickTrim");
		}
	})
	Question = activeQuestion;
}
//提交考试接口-----------------------------------------------------------------

/*保存考题状态 已做答的状态*/
function saveQuestionState(clickId) {
	$('.z-index-mask_card').hide();
	$('.answer_analysis').css('display', 'none');
	showQuestion(clickId);
	//var text = JSON.parse($("#strBtn").html()).questions_list;
	var text=questions_list;
	// activeQuestion是当前操作的考题编号
	showQuestion(activeQuestion);	
	if (activeQuestion == text.length - 1) {
		$('#nextQuestion').hide();
		$('.submitQuestions').show();
	}
	if (activeQuestion !== text.length - 1) {
		$('.submitQuestions').hide();
		$('#nextQuestion').show();
	}
}
/********************************进入下一题********************************************/
$(function () {
	$("#nextQuestion").click(function () {
		
		//var text = JSON.parse($("#strBtn").html()).questions_list;
		var text=questions_list;
		// activeQuestion是当前操作的考题编号
		if ((activeQuestion + 1) != text.length) showQuestion(activeQuestion + 1);
		showQuestion(activeQuestion);
		$('.content_class_con').css('display', 'none');
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



// 答案解析显示隐藏
var n = 0;
$('.content_class_answer').click(function () {
	n++
	if (n % 2 !== 0) {
		$('.answer_analysis').show()
	} else {
		$('.answer_analysis').hide()
	}
})












