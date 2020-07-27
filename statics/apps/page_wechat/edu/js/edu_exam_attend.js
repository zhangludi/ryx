//var time = 0
//var theExamBegins = 'false'
apiready = function () {
	answerCard();
	showQuestion(0);
	//getEduExamInfo()//调用（获取考试详情）接口
	//$('#questions_list').hide()
	//$('#submitExam').hide()
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
				"exam_id": api.pageParam.type,//试题编号
			}
		}
	}, function (ret, err) {
		if (ret) {
			if (ret.status == 1) {
				$('#exam_id').html(ret.data.exam.exam_title);//考试名称
				$('#exam_name').html(localStorage.user_name);//考试人员
				$('#add_time').html(ret.data.exam.add_time.substring(0, 10));//考试时间
				$('#exam_time').html(ret.data.exam.exam_time);//考试时长
				$('#exam_point').html(ret.data.exam.exam_point);//总分
				if (ret.data.questions_list && ret.data.questions_list.length > 0) {
					for (var i = 0; i < ret.data.questions_list.length; i++) {
						$('#questions_list').append(
							'<div class="bor-ra-7 box-sha-5-ed over-h bg-color-whiter mb-12em">' +
							'<ul id="questions_item' + i + '" class="pl-12em pr-20em pt-12em">' +
							'<p class="f-14em lh-20em color-33 pb-10em">' +
							'<span class="questionsId" noo="' + ret.data.questions_list[i].questions_id + '">' + (i - (-1)) + '.</span>' +
							'<span class="color-ff3032 titleType">【' + ret.data.questions_list[i].questions_type + '题】</span>' +
							ret.data.questions_list[i].questions_title +
							'</p>' +
							'</ul>' +
							'</div>'
						)
						var questionsItem = ret.data.questions_list[i].questions_item.split(',');
						if (questionsItem.length > 0) {
							for (var j = 0; j < questionsItem.length; j++) {
								$('#questions_item' + i + '').append(
									'<li onclick="opt(this)" select="false" class="f-12em color-33 pb-15em lh-15em">' +
									'<img class="w-12em h-12em pull-left mr-12em exam_no" src="../../../statics/images/images_edu/edu_exam_no.png" alt="" />' +
									'<img class="w-12em h-12em pull-left mr-12em exam_yes di-n" src="../../../statics/images/images_edu/edu_exam_yes.png" alt="" />' +
									'<span>' + questionsItem[j] + '</span>' +
									'</li>'
								)
							}
						}
					}
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
	});
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

function open11(winName) {
	//alert("执行");
	api.openWin({
		name: winName,
		url: '../edu_special/header/' + winName + '.html',
		reload: true,
		pageParam: {
			//	type:type,

		}
	})
}

//题目选择答案-------------------------------------------------------------------------------------
function opt(This) {
	if ($(This).parent().find('.titleType').html() == "【单选题】" || $(This).parent().find('.titleType').html() == "【判断题】") {
		$(This).siblings('li').removeClass('opt');
		$(This).siblings('li').attr('select', 'false');
		$(This).siblings('li').find('.exam_yes').addClass('di-n');
		$(This).siblings('li').find('.exam_no').removeClass('di-n');
		$(This).addClass('opt');
		$(This).attr('select', 'true');
		$(This).find('.exam_yes').removeClass('di-n');
		$(This).find('.exam_no').addClass('di-n');
	} else if ($(This).parent().find('.titleType').html() == "【多选题】") {
		$(This).attr('select', ($(This).attr('select') == 'true' ? 'false' : 'true'));
		if ($(This).attr('select') == 'true') {
			$(This).addClass('opt');
			$(This).find('.exam_yes').removeClass('di-n');
			$(This).find('.exam_no').addClass('di-n');
		} else {
			$(This).removeClass('opt');
			$(This).find('.exam_yes').addClass('di-n');
			$(This).find('.exam_no').removeClass('di-n');
		}
	}
}

//开始考试-------------------------------------------------------------------------------------
$('#startExam').click(function () {
	theExamBegins = 'true';
	$(this).hide();
	$('#questions_list').show();
	$('#submitExam').show();
	var timeNum = $('#exam_time').html();
	var examTimer = setInterval(function () {
		if (timeNum <= 5) {
			if (timeNum <= 0) {
				setEduExamQuestions()//提交考试
			} else if (timeNum == 5) {
				api.toast({ msg: '5分钟后将自动提交考试，请抓紧时间答题', duration: 3000, location: 'middle' });
			}
		};
		timeNum--;

	}, 60000)
})

//考试未开始或者考试开始后推出------------------------------------------------------------------------
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
//刷新页面---------------------------------------------------------------------------------
function exec() {
	location.reload();
}

//打开个性化定制进度条
function openExc_edu() {
	api.execScript({
		name: 'bd_index',
		frameName: 'partyorg',
		script: 'mask_back();'
	});
	api.closeWin();

}


$("#list1 li").click(function () {
	$(this).addClass("active");
	$("#list1 li").not($(this)).removeClass("active");

});
$("#list2 li").click(function () {
	$(this).addClass("active");
	$("#list2 li").not($(this)).removeClass("active");

});
$("#list3 li").click(function () {
	if ($(this).hasClass("active")) {
		$(this).removeClass("active");
	} else {
		$(this).addClass("active");
	}
})
//关闭得分遮罩层
$(".edu_analysis_mask").click(function () {
	$(this).fadeOut();
});

//打开学习分析页面
function edu_exam_analysis(winName) {
	api.openWin({
		name: winName,
		url: 'header/' + winName + '.html',
		reload: true,
		pageParam: {

		}
	})
}
/********************************************************************/

var QuestionJosn = [{
	"questionId": "1",
	"questionTitle": "坚持反腐败无禁区、全覆盖、零容忍，坚定不移“打虎”、“拍蝇”、“猎狐”，（ ）的目标初步实现，（ ）的笼子越扎越牢，（ ）的堤坝正在构筑，反腐败斗争压倒性态势已经形成并巩固发展。 ",
	"questionItems": "神经纤维;神经纤维和门脉系统;垂体门脉系统;垂体束;轴浆运输",
	"questionAnswer": "c"
}, {
	"questionId": "2",
	"questionTitle": "下列激素中，哪一种不是腺垂体分泌的？ ",
	"questionItems": "生长素;催产素;黄体生成素;促卵泡激素;催乳素",
	"questionAnswer": "b"
}, {
	"questionId": "3",
	"questionTitle": "关于内分泌系统最佳的描述是 ",
	"questionItems": "区别于外分泌腺的系统;无导管，分泌物直接进入血液的腺体;分泌物通过体液传递信息的系统;包括内分泌腺和散在的内分泌细胞组成的大系统;全身的内分泌细胞群的总称",
	"questionAnswer": "d"
}, {
	"questionId": "4",
	"questionTitle": "呆小症的发生是由于幼年时 ",
	"questionItems": "生长素不足;催产素不足;维生素D3不足;甲状腺激素不足;先天性大脑发育不全",
	"questionAnswer": "d"
}, {
	"questionId": "5",
	"questionTitle": "影响降钙素分泌的主要因素是 ",
	"questionItems": "血镁浓度;血钙浓度;血磷浓度;血钠浓度;血钾浓度",
	"questionAnswer": "b"
}, {
	"questionId": "6",
	"questionTitle": "关于降钙素的叙述，错误的是 ",
	"questionItems": "由甲状腺C细胞分泌;属于肽类激素;可降低血钙，升高血磷;主要靶器官是骨;其分泌主要受血钙浓度调节",
	"questionAnswer": "c"
}, {
	"questionId": "7",
	"questionTitle": "激素作用的特异性，下述错误的是 ",
	"questionItems": "可作用于全身所有组织细胞;有的作用于靶腺;有选择性地作用于某些器官、腺体细胞;有的广泛影响细胞代谢;有的只与胞膜或胞浆受体结合发挥作用",
	"questionAnswer": "a"
}, {
	"questionId": "8",
	"questionTitle": "使皮质醇浓度增加的麻醉药是 ",
	"questionItems": "吗啡，巴比妥类;芬太尼;乙醚;恩氟烷",
	"questionAnswer": "d"
}, {
	"questionId": "9",
	"questionTitle": "使皮质醇浓度降低的因素是",
	"questionItems": "手术创伤;低血压;术中缺氧;二氧化碳蓄积;低温",
	"questionAnswer": "e"
}, {
	"questionId": "10",
	"questionTitle": "使甲状腺分泌功能降低的因素是 ",
	"questionItems": "乙醚;以硫喷妥钠为主的全麻;降温初期;手术;椎管内麻醉",
	"questionAnswer": "b"
}, {
	"questionId": "11",
	"questionTitle": "下丘脑“促垂体区”神经元的功能特点 ",
	"questionItems": "分泌类固醇激素;不受大脑的控制;将神经信息转变成激素信息;不具典型神经元的作用;无肽能神经元的作用",
	"questionAnswer": "c"
}, {
	"questionId": "12",
	"questionTitle": "下列不属于下丘脑调节肽的是 ",
	"questionItems": "TRH;GnRH;GHRH;ACTH;PRF",
	"questionAnswer": "d"
}, {
	"questionId": "13",
	"questionTitle": "下列哪一种激素为腺垂体所分泌? ",
	"questionItems": "促甲状腺激素;抗利尿激素;肾上腺皮质激素;生长素释放激素;催乳素释放抑制因子",
	"questionAnswer": "a"
}, {
	"questionId": "14",
	"questionTitle": "下列哪种激素不是由垂体前叶分泌的? ",
	"questionItems": "抗利尿激素;生长素;卵泡刺激素;催乳素;促甲状腺激素",
	"questionAnswer": "a"
}, {
	"questionId": "15",
	"questionTitle": "促进机体产热增加的主要激素是 ",
	"questionItems": "胰岛素;生长素;甲状腺激素;肾上腺素; 糖皮质激素",
	"questionAnswer": "c"
}, {
	"questionId": "16",
	"questionTitle": "在人类和哺乳动物，对骨和脑的发育尤为重要的激素是 ",
	"questionItems": "生长素;糖皮质激素;盐皮质激素;甲状腺激素;甲状旁腺激素",
	"questionAnswer": "d"
}, {
	"questionId": "17",
	"questionTitle": "地方性甲状腺肿的主要发病原因是 ",
	"questionItems": "由于促甲状腺素分泌过少;甲状腺合成的甲状腺激素过多;食物中缺少钙和蛋白质;食物中缺少酪氨酸;食物中缺少碘",
	"questionAnswer": "e"
}, {
	"questionId": "18",
	"questionTitle": "关于甲状腺激素正确的论述是 ",
	"questionItems": "属于类固醇激素;合成后贮存于细胞内;T4的活性比T3大;可促进生长和发育过程;幼年缺乏时可导致侏儒症",
	"questionAnswer": "d"
}, {
	"questionId": "19",
	"questionTitle": "人体内储存量最多的激素是 ",
	"questionItems": "生长素;胰岛素;甲状腺素;促肾上腺皮质激素;肾上腺素和去甲肾上腺素",
	"questionAnswer": "c"
}, {
	"questionId": "20",
	"questionTitle": "对胰岛素的叙述错误的是 ",
	"questionItems": "由胰岛B细胞分泌;可使血糖浓度下降;迷走神经兴奋可使其分泌减少;血糖浓度升高可促进其分泌;胃肠激素可促进其分泌",
	"questionAnswer": "c"
}];
var luyouqi = [{
	"questionId": "1",
	"questionTitle": "下丘脑与腺垂体之间主要通过下列哪条途径联系？ ",
	"questionItems": "神经纤维;神经纤维和门脉系统;垂体门脉系统;垂体束;轴浆运输",
	"questionAnswer": "c"
}, {
	"questionId": "2",
	"questionTitle": "下列激素中，哪一种不是腺垂体分泌的？ ",
	"questionItems": "生长素;催产素;黄体生成素;促卵泡激素;催乳素",
	"questionAnswer": "b"
}, {
	"questionId": "3",
	"questionTitle": "关于内分泌系统最佳的描述是 ",
	"questionItems": "区别于外分泌腺的系统;无导管，分泌物直接进入血液的腺体;分泌物通过体液传递信息的系统;包括内分泌腺和散在的内分泌细胞组成的大系统;全身的内分泌细胞群的总称",
	"questionAnswer": "d"
}, {
	"questionId": "4",
	"questionTitle": "呆小症的发生是由于幼年时 ",
	"questionItems": "生长素不足;催产素不足;维生素D3不足;甲状腺激素不足;先天性大脑发育不全",
	"questionAnswer": "d"
}, {
	"questionId": "5",
	"questionTitle": "影响降钙素分泌的主要因素是 ",
	"questionItems": "血镁浓度;血钙浓度;血磷浓度;血钠浓度;血钾浓度",
	"questionAnswer": "b"
}, {
	"questionId": "6",
	"questionTitle": "关于降钙素的叙述，错误的是 ",
	"questionItems": "由甲状腺C细胞分泌;属于肽类激素;可降低血钙，升高血磷;主要靶器官是骨;其分泌主要受血钙浓度调节",
	"questionAnswer": "c"
}, {
	"questionId": "7",
	"questionTitle": "激素作用的特异性，下述错误的是 ",
	"questionItems": "可作用于全身所有组织细胞;有的作用于靶腺;有选择性地作用于某些器官、腺体细胞;有的广泛影响细胞代谢;有的只与胞膜或胞浆受体结合发挥作用",
	"questionAnswer": "a"
}, {
	"questionId": "8",
	"questionTitle": "使皮质醇浓度增加的麻醉药是 ",
	"questionItems": "吗啡，巴比妥类;芬太尼;乙醚;恩氟烷",
	"questionAnswer": "d"
}, {
	"questionId": "9",
	"questionTitle": "使皮质醇浓度降低的因素是",
	"questionItems": "手术创伤;低血压;术中缺氧;二氧化碳蓄积;低温",
	"questionAnswer": "e"
}, {
	"questionId": "10",
	"questionTitle": "使甲状腺分泌功能降低的因素是 ",
	"questionItems": "乙醚;以硫喷妥钠为主的全麻;降温初期;手术;椎管内麻醉",
	"questionAnswer": "b"
}, {
	"questionId": "11",
	"questionTitle": "下丘脑“促垂体区”神经元的功能特点 ",
	"questionItems": "分泌类固醇激素;不受大脑的控制;将神经信息转变成激素信息;不具典型神经元的作用;无肽能神经元的作用",
	"questionAnswer": "c"
}, {
	"questionId": "12",
	"questionTitle": "下列不属于下丘脑调节肽的是 ",
	"questionItems": "TRH;GnRH;GHRH;ACTH;PRF",
	"questionAnswer": "d"
}, {
	"questionId": "13",
	"questionTitle": "下列哪一种激素为腺垂体所分泌? ",
	"questionItems": "促甲状腺激素;抗利尿激素;肾上腺皮质激素;生长素释放激素;催乳素释放抑制因子",
	"questionAnswer": "a"
}, {
	"questionId": "14",
	"questionTitle": "下列哪种激素不是由垂体前叶分泌的? ",
	"questionItems": "抗利尿激素;生长素;卵泡刺激素;催乳素;促甲状腺激素",
	"questionAnswer": "a"
}, {
	"questionId": "15",
	"questionTitle": "促进机体产热增加的主要激素是 ",
	"questionItems": "胰岛素;生长素;甲状腺激素;肾上腺素; 糖皮质激素",
	"questionAnswer": "c"
}, {
	"questionId": "16",
	"questionTitle": "在人类和哺乳动物，对骨和脑的发育尤为重要的激素是 ",
	"questionItems": "生长素;糖皮质激素;盐皮质激素;甲状腺激素;甲状旁腺激素",
	"questionAnswer": "d"
}, {
	"questionId": "17",
	"questionTitle": "地方性甲状腺肿的主要发病原因是 ",
	"questionItems": "由于促甲状腺素分泌过少;甲状腺合成的甲状腺激素过多;食物中缺少钙和蛋白质;食物中缺少酪氨酸;食物中缺少碘",
	"questionAnswer": "e"
}, {
	"questionId": "18",
	"questionTitle": "关于甲状腺激素正确的论述是 ",
	"questionItems": "属于类固醇激素;合成后贮存于细胞内;T4的活性比T3大;可促进生长和发育过程;幼年缺乏时可导致侏儒症",
	"questionAnswer": "d"
}, {
	"questionId": "19",
	"questionTitle": "人体内储存量最多的激素是 ",
	"questionItems": "生长素;胰岛素;甲状腺素;促肾上腺皮质激素;肾上腺素和去甲肾上腺素",
	"questionAnswer": "c"
}, {
	"questionId": "20",
	"questionTitle": "对胰岛素的叙述错误的是 ",
	"questionItems": "由胰岛B细胞分泌;可使血糖浓度下降;迷走神经兴奋可使其分泌减少;血糖浓度升高可促进其分泌;胃肠激素可促进其分泌",
	"questionAnswer": "c"
}];
var questions = QuestionJosn
var activeQuestion = 0 //当前操作的考题编号
var questioned = 0 //
var currentSlide = 0
var checkQues = [] //已做答的题的集合
var itemList = ["A", "B", "C", "D", "E", "F"]



//展示考卷信息
function showQuestion(id) {
	$(".questioned").text(id + 1)

	questioned = (id + 1) / questions.length
	if (activeQuestion != undefined) {
		$("#ques" + activeQuestion).removeClass("question_id").addClass("active_question_id");
	}
	activeQuestion = id;
	$(".question").find(".question_info").remove()
	let question = questions[id]
	$(".question_title").html("<strong>" + (id + 1) + "  、</strong>" + question.questionTitle)
	let items = question.questionItems.split(";")
	let item = ""
	for (let i = 0; i < items.length; i++) {
		item = `<li class="question_info content_classes_bots radio icheck-success"
						id= "item${i}">
						<input type="radio" name=" item success1" id="success${itemList[i]}" disabled="disabled">
						<span for="success1" style="position: relative;"><span class="xuanxiang${i} options">${itemList[i]}.   </span>
						<span class="pull-right check_false check" style="display:none;"><img src="../../../statics/images/images_edu/false.jpg" alt="答题卡"></span>
						<span class="pull-right check_true check"><img src="../../../statics/images/images_edu/true.jpg" alt="答题卡"></span>
						<span class="pull-right" style="padding-left:1.3rem;display:block">  ${items[i]}</span></span>
						</li>`
		$(".question").append(item)
	}
	$(".question").attr("id", "question" + id)
	$("#ques" + id).removeClass("active_question_id").addClass("question_id")
	for (let i = 0; i < checkQues.length; i++) {
		if (checkQues[i].id == id) {
			$("#" + checkQues[i].item).find("input").prop("checked", "checked")
			$("#" + checkQues[i].item).addClass("clickTrim")
			$("#ques" + activeQuestion).removeClass("question_id").addClass("clickQue")
		}
	}
}


/*答题卡*/
function answerCard() {
	$(".question_sum").text(questions.length)
	for (let i = 0; i < questions.length; i++) {
		let questionId =
			"<li id='ques" + i + "'onclick='saveQuestionState(" + i +
			")' class='questionId  pull-left question_true_id'>" +
			(i + 1) +
			"</li>";
		$("#answerCard ul").append(questionId);
	}
}

/*选中考题*/
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



/*点击答题卡 出现相应考题*/
function saveQuestionState(clickId) {
	$('.z-index-mask').hide()
}

$(function () {
	$(".middle-top-left").width($(".middle-top").width() - $(".middle-top-right").width())
	$(".progress-left").width($(".middle-top-left").width() - 200);
	var str = ''

	//进入下一题
	$("#nextQuestion").click(function () {
		if ((activeQuestion + 1) != questions.length) showQuestion(activeQuestion + 1);
		showQuestion(activeQuestion);
		if (activeQuestion == 19) {
			$('#nextQuestion').hide()
		}
	})
})

// 打开学习卡
$('#opencard').click(function () {
	$('.z-index-mask').show()
})
// 关闭学习卡
$('.mask_close').click(function () {
	$('.z-index-mask').hide()
})


// 时间倒计时
// let maxtime = 60 * 20 //一个小时，按秒计算，自己调整!
// function CountDown() {
// 	if (maxtime >= 0) {
// 		minutes = Math.floor(maxtime / 60)
// 		minutes = minutes < 10 ? '0' + minutes : minutes
// 		seconds = Math.floor(maxtime % 60)
// 		seconds = seconds < 10 ? '0' + seconds : seconds
// 		msg = `<span>所用时间 </span>
// 		<span class="time minute">${minutes}</span>:
// 		<span class="time second">${seconds}</span>`
// 		document.all["timer"].innerHTML = msg
// 	} else {
// 		clearInterval(timer)
// 	}
// }
// timer = setInterval("CountDown()", 1000)
