/********************************时间倒计时********************************************/
var activeQuestion = 0; //当前操作的考题编号
var questioned = 0; //
var currentSlide = 0;
var checkQues = []; //已做答的题的集合
var itemList = ["A", "B", "C", "D", "E", "F"];
var questions_list = JSON.parse($("#strBtn").html()).questions_list;

/********************************展示考卷信息********************************************/
showQuestion(0);
function showQuestion(id) {
	//$(".questioned").text(id + 1);
	//var text = JSON.parse($("#strBtn").html()).questions_list;
	var text = questions_list;

	//questioned = (id + 1) / text.length;
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
	var type = question.type;
	var right_answer = question.questions_answer.split(",");
	var my_answer = question.my_questions_item.split(",");
	if(type = 2){ // 多选还是单选  多选用checkbox  单选用radio
		var checkbox_html = `<input type="checkbox" name=" item success${i}" id="success${itemList[i]}">`;
		var checkbox_true_html = `<input type="checkbox" checked="true" name=" item success${i}" id="success${itemList[i]}">`;

		for (var i = 0; i < items.length; i++) { // 循环问题选项
			if(isInArray(my_answer,itemList[i])){ // 选项在我的答案中
				if(isInArray(right_answer,itemList[i])){ // 我的选项在正确答案中    我的答案打对号
					item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ checkbox_true_html +`
						<label for="success${i}" style="position: relative;">
							<span class="xuanxiang${i} options"></span>
							<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`

				} else { // 我的选项不在正确答案中    我的答案打错
					item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ checkbox_html +`
						<label for="success${i}" style="position: relative;">
							<span class="xuanxiang${i} options check_false check"
							style="width:1.2rem;height:1.2rem;position:absolute;transform:translateY(-50%);top:37%;left:-6%">
							<img style="width: 22px;height: 22px;" src="../../../statics/apps/page_portal/portal/images/false.jpg" alt="答题卡">
							</span>
						<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`
				}
			} else {
				item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ checkbox_html +`
						<label for="success1" style="position: relative;">
							<span class="xuanxiang${i} options">${itemList[i]} . </span>
							<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`
			}
			$(".question").append(item);
		}
	} else {
		var radio_html = `<input type="radio"  name=" item success1" id="success${itemList[i]}">`;
		var radio_true_html = `<input type="radio" checked="true" name=" item success${i}" id="success${itemList[i]}">`;
		for (var i = 0; i < items.length; i++) {
			if(isInArray(my_answer,itemList[i])){
				if(isInArray(right_answer,itemList[i])){ // 我的选项在正确答案中    我的答案打对号
					item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ radio_true_html +`
						<label for="success${i}" style="position: relative;">
							<span class="xuanxiang${i} options"></span>
							<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`
				} else { // 我的选项不在正确答案中    我的答案打错
					item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ radio_html +`
						<label for="success${i}" style="position: relative;">
							<span class="xuanxiang${i} options check_false check"
							style="width:1rem;height:1rem;position:absolute;transform:translateY(-50%);top:50%;">
							<img src="__STATICS__/apps/page_portal/portal/images/false.jpg" alt="答题卡">
							</span>

							<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`
				}
			} else {
				item = `<li class="question_info content_classes_bots radio icheck-success"
									id= "item${i}">
						`+ radio_html +`
						<label for="success1" style="position: relative;">
							<span class="xuanxiang${i} options">${itemList[i]} . </span>
							<span class="pull-right" style="padding-left:1rem;display:block">${items[i]}</span>
						</label>
					</li>`
			}
			$(".question").append(item);
		}
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
function isInArray(arr,value){
    for(var i = 0; i < arr.length; i++){
        if(value === arr[i]){
            return true;
        }
    }
    return false;
}
/*答题卡*/
answerCard();
function answerCard() {
	//var text = JSON.parse($("#strBtn").html()).questions_list;
	var text = questions_list;
	
	$(".question_sum").text(text.length);
	for (var i = 0; i < text.length; i++) {
		var question = text[i];
		$(".questions_type").html(question.questions_type);
		if(question.my_questions_item==question.questions_answer){
			//正确
			var questionId =
			"<li id='ques" + i + "'onclick='saveQuestionState(" + i +
			")' class='questionId  pull-left question_true_id'>" +
			(i + 1) +
			"</li>";
		}else{
			var questionId =
			"<li id='ques" + i + "'onclick='saveQuestionState(" + i +
			")' class='questionId  pull-left question_false_id'>" +
			(i + 1) +
			"</li>";
		}
		
		
		
		$("#answerCard ul").append(questionId);
	}
}
/********************************选中考题********************************************/
var Question

function clickTrim(source) {
	var id = source.id

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
	// $('.answer_analysis').css('display', 'none')
	showQuestion(clickId)
	//var text = JSON.parse($("#strBtn").html()).questions_list
	var text =questions_list;
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
/********************************进入下一题********************************************/
$(function () {
	//进入下一题
	$("#nextQuestion").click(function () {
		//var text = JSON.parse($("#strBtn").html()).questions_list;
		var text =questions_list;
		// activeQuestion是当前操作的考题编号
		if ((activeQuestion + 1) != text.length) showQuestion(activeQuestion + 1);
		showQuestion(activeQuestion);
		// $('.answer_analysis').css('display', 'none')
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


//提交考试接口-----------------------------------------------------------------
function setEduExamQuestions() {
	
	$(".edu_score_mask").removeClass("di-n");
	
}








