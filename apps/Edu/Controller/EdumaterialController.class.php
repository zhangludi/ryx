<?php
/*******************************************学习资料管理***************************************************** */
namespace Edu\Controller;
use Common\Controller\BaseController;
class EdumaterialController extends BaseController{
	/**
	 * @name  edu_material_index()
	 * @desc  学习资料列表首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-11
	 */
	public function edu_material_index(){
		//echo getFunctionInfo(ACTION_NAME);
		checkAuth(ACTION_NAME);
		$communist_no=session('staff_no');
		$cat_id = I('get.material_cat_id');
		if(!empty($cat_id)){
			$this->assign('material_cat',$cat_id);
		}
		$is_page = I('get.is_page');
		if($is_page != 1){
			$is_page = 0;
		}
		$this->assign("is_page", $is_page);
	
		$is_hunt=I('get.is_hunt');
		if($is_hunt=='1')
		{
			$post=I('post.');
			$this->assign("post", $post);
		}
  		$cat_list = getMaterialCatList('',0,'',1);
		$this->assign('cat_list',$cat_list);
		$industry = getConfig('industry');
		$this->assign('industry',$industry);

		$this->display('Edumaterial/edu_material_index');
	}

	/**
	 * @name  edu_material_edit()
	 * @desc  资料添加/编辑表单页面
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-10-24
	 */
	public function edu_material_edit()
	{
		checkAuth(ACTION_NAME);
		//$post = I('post.');
		// 添加时获取学习类型的id
		$cat_id = I('get.cat_id');
		if (! empty($cat_id)) {
			$cat_map['cat_id'] = $cat_id;
			$cat_type = M('edu_material_category')->where($cat_map)->getField('cat_type');
			$this->assign("cat_type", $cat_type);
			$this->assign("cat_id", $cat_id);
		}
		$materialCat = $this->getMaterialCatList('0','-1','1');
		$this->assign("materialcat", $materialCat);
		
		// 编辑时获取学习id
		$article_id = I('get.material_id');
		if (! empty($article_id)) {// 编辑学习
			$edu_material = M('edu_material');
			$material_map['material_id'] = $article_id;
			$material_data = $edu_material->where($material_map)->find();
			$this->assign("material_data", $material_data);
			$material_topic = $material_data['material_topic'];
			$this->assign('material_topic',$material_topic);
		}
		$topic_data = getTopicList(1);
		$this->assign("topic_data", $topic_data);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display("Edumaterial/edu_material_edit");
	}

	/**
	 * @name  edu_material_do_save()
	 * @desc  学习资料保存
	 * @param $_POST
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-16
	 */
	public function edu_material_do_save()
	{
		checkLogin();
		$db_article = M("edu_material");
		$article_id = $_POST['material_id'];
		$post = $_POST;
		if($post['material_cat']!=2){
			$post['video_duration'] = '';
			$post['material_vedio'] = '';
		}
		$post['update_time'] = date("Y-m-d H:i:s");
		if (! empty($post['material_imgs'])) {
			$post['material_thumb'] = $post['material_imgs'];
		}
		if (! empty($article_id)) {
			$material_map['material_id'] = $article_id;
			$post['status'] = 1;
			$article_data = $db_article->where($material_map)->save($post);
		} else {
			$post['add_staff'] = session('staff_no');
			$post['producer'] = session('staff_no');
			$post['add_time'] = date("Y-m-d H:i:s");
			$post['status'] = 1;
			$article_data = $db_article->add($post);
		}
		session('material_cat_id',$post['material_cat']);
		if ($article_data) {
			showMsg('success', '操作成功！！！', U('Edumaterial/edu_material_index'),1);
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  edu_material_info()
	 * @desc  资料详情
	 * @param $article_id
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime 2019-03-05，编辑学习笔记列表
	 * @addtime    2017-10-16
	 */
	public function edu_material_info()
	{
		checkAuth(ACTION_NAME);
		$article_id = I("get.material_id");
		$article_data = getMaterialInfo($article_id,'all');
		//dump($article_data);die;
		// 学习缩略图
		$article_img = $article_data['material_thumb'];
		$string = C("TMPL_PARSE_STRING");
		$upload = $string['__UPLOAD__'] . "/";
		if(empty($article_img)){
			$article_data['material_thumb'] = "<img src='" . $upload . "/cms/photos.jpg' width='50px'>";
		}else{
			$article_data['material_thumb'] = "<img src='". $article_img . "' width='200px'>";
		}
		
		//开始计算积分时间
		$article_data['material_duration_integral_time'] = $article_data['material_duration_integral']*60000;
		$article_data['material_notes'] = getMaterialNotesList($article_id);
		$article_data['add_time'] = getFormatDate($article_data['add_time'], "Y-m-d");
		//获取学习人数
		$edu_notes = M('edu_notes');
		$notes_map['material_id'] = $article_id;
		$count =$edu_notes->where($notes_map)->count();
		$pagecount = 5;
		$page = new \Think\Page($count , $pagecount);
		//$page->parameter = $row; //此处的row是数组，为了传递查询条件
		$page->setConfig('first','首页');
		$page->setConfig('prev','上一页');
		$page->setConfig('next','下一页');
		$page->setConfig('last','尾页');
		$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 第1页/共 %TOTAL_PAGE% 页 ( '.$pagecount.' 条/页 共 %TOTAL_ROW% 条)');
		$show = $page->show();
		$notes_data = $edu_notes->where($notes_map)->limit($page->firstRow.','.$page->listRows)->select();
		$edu_material_communist = M('edu_material_communist');
		$material_map['material_id'] = $article_id;
		$material_num = $edu_material_communist->where($material_map)->group('communist_no')->count();
		$article_data['material_num'] = $material_num;

		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		
		foreach ($notes_data as &$notes){
		    $notes['material_notes_communist'] = $communist_name_arr[$notes['add_communist']];//getcommunistInfo(,"communist_name");
		}
		if(!empty($article_data['material_vedio'])){
			$article_data['material_vedio'] ="<video src='".__ROOT__.$article_data["material_vedio"]. "' width='552' height='300' controls autobuffer><video>";
	
		}else{
			$article_data['material_vedio'] = "";
		}

		//已学习党员的职务，姓名，以职务分组。每个党员后面显示笔记的数量和列表
		$sql_str="select m.communist_no,c.communist_name,c.communist_avatar,p.post_no,p.post_name,n.notes_id,m.material_no,n.notes_title,left(n.notes_content,20) as notes_content,n.add_time from sp_edu_material_log as m left join sp_ccp_communist as c on m.communist_no=c.communist_no left join sp_ccp_party_duty as p on p.post_no=c.post_no left join sp_edu_notes as n on m.material_no=n.material_id and m.communist_no=n.add_staff where m.material_no={$article_id} GROUP BY communist_no,notes_id ORDER BY notes_id desc";
		$result=M()->query($sql_str);
		$notes_list=[];
		$communist_count=0;//学习的总人数
		foreach($result as $key=>$value){
			$item=(string)$value["post_no"];
			$communist_no=(string)$value["communist_no"];
			//$notes_list[$key]["post_no"]=$value["post_no"];
			if(empty($value["communist_avatar"])){
				$value["communist_avatar"]="/statics/apps/page_layout/images/photo3.jpg";
			}
			if(!array_key_exists($communist_no,$notes_list[$item]["communist"])){
				$communist_count++;
			}
			if(array_key_exists($item,$notes_list)){
				//$notes_list[$item]["post_name"]=$value["post_name"];
				if(!array_key_exists($communist_no,$notes_list[$item]["communist"])){
					$notes_list[$item]["communist"][$communist_no]=[
						"name"=>$value["communist_name"],
						"avatar"=>$value["communist_avatar"]
					];
				}
			}else{
				$notes_list[$item]["post_name"]=$value["post_name"];
				$notes_list[$item]["communist"][$communist_no]=[
					"name"=>$value["communist_name"],
					"avatar"=>$value["communist_avatar"]
				];
			}
			if(!is_numeric($notes_list[$item]["communist"][$communist_no]["notes_count"])){
				if($value["notes_id"]){
					$notes_list[$item]["communist"][$communist_no]["notes_count"]=1;
				}else{
					$notes_list[$item]["communist"][$communist_no]["notes_count"]=0;
				}
			}else{
				$notes_list[$item]["communist"][$communist_no]["notes_count"]++;
			}
			$notes_list[$item]["communist"][$communist_no]["notes"][]=[
				"id"=>$value["notes_id"],
				"title"=>$value["notes_title"],
				"time"=>date("Y-m-d",strtotime($value["add_time"])),
				"content"=>$value["notes_content"]
			];
		}
		$this->assign('notes_list',$notes_list);
		$this->assign('communist_count',$communist_count);
		//$this->assign("material_count",count($temp_visit_list));
		$this->assign("count",$count);
		$this->assign('page',$show);
		$this->assign('notes_data',$notes_data);
		$this->assign("material_data", $article_data);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display("Edumaterial/edu_material_info");
	}

	/**
	 * @name  edu_material_do_del()
	 * @desc  学习删除（同时删除附件及附件文件）
	 * @param $article_id(学习ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-19
	 */
	public function edu_material_do_del()
	{
		checkAuth(ACTION_NAME);
		$db_material = M('edu_material');
		$material_id = I("get.material_id");
		if (! empty($material_id)) {
			$material_dt = getArticleInfo($material_id,'all');
			$material_map['material_id'] = $material_id;
			$material_data = $db_material->where($material_map)->delete();
		}
		if ($material_data) {
			delFile($material_dt['material_img']);
			showMsg('success', '操作成功！！！', U('edu_material_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}

	/**
	 * @name  edu_material_table()
	 * @desc  学习管理数据列表
	 * @param
	 * @return
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2016-08-13
	 */
	public function edu_material_table(){
		$cat_id = I('get.material_cat');
		$this->assign('cat_id',$cat_id);
		$keyword = I('get.keyword');
		$this->assign('keyword',$keyword);
		$height=I('get.height');
		$this->assign('height',$height);
		$start_time = I('get.start_time');
		$this->assign('start_time',$start_time);
		$end_time = I('get.end_time');
		$this->assign('end_time',$end_time);
		$this->display("Edumaterial/edu_material_table");
	}

	/**
	 * @name  edu_material_table_data()
	 * @desc  学习表数据页面
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-16
	 */
	public function edu_material_table_data()
	{
		$cat_id = I("get.cat_id");
		if(empty($cat_id)){
			$cat_id = 0;
		}
		$db_edutopic=M('edu_material');
		$page = I('get.page');
		$pagesize = I('get.limit');
		$page = ($page-1)*$pagesize;
		$keyword = I('get.keyword');
		
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
	 	if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $topic_map['add_time']  = array('between',array($start,$end));
        }
        if(!empty($keyword)){
        	$topic_map['material_title']  = array('like','%'.$keyword.'%');
        }
        if(!empty($cat_id)){
        	$topic_map['material_cat'] = $cat_id;
        }
		$topic_map['status'] = 1;
		$material_data['data'] = $db_edutopic->where($topic_map)->order('add_time desc')->limit($page,$pagesize)->select();
		$material_data['count'] = $db_edutopic->where($topic_map)->count();
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$firm = 'onclick="if(!confirm(' . "'确认进行此操作？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		if(!empty($material_data['data']) && $material_data['data'] != 'null'){
			foreach ($material_data['data'] as &$material) {
				$material['cat_name'] = getArticleCatInfo($material['material_cat']);
				$material['add_staff'] = $staff_name_arr[$material['add_staff']];
				$material['add_time']=getFormatDate($material['add_time'], "Y-m-d");
				$logo = getUploadInfo($material['material_thumb']);
				if ($material['material_cat']=='15') {
					switch ($material['status']){
						case 1:$material['operate'] = "<a class='btn blue btn-xs btn-outline' >审核通过</a>";
							break;
						case 21:$material['operate'] = "<a class='btn blue btn-xs btn-outline' href='".U('cms_article_status',array('$material_id' => $material['article_id'],'status'=>1))."' $firm>通过</a>
						<a class='btn red btn-xs btn-outline' href='".U('cms_article_status',array('$material_id' => $material['material_id'],'status'=>31))."' $firm>驳回</a>";
							break;
						case 31:$material['operate'] = "<a class='btn red btn-xs btn-outline'>已驳回</a>";
							break;
					}
				}
				if(empty($logo)){
					// 文章缩略图
					$material['material_thumb'] = '无';
				}else{
					// 文章缩略图
					$material['material_thumb'] = "<img src='$logo' width='50'>";
				}
				if(!empty($material['article_describe'])){
				 	$material['article_describe'] = mb_substr($material['article_describe'], 0, 10, 'utf-8');
				}
				if(!empty($material['material_content'])){
					$material['material_content'] = mb_substr(strip_tags($material['material_content']), 0, 10, 'utf-8');
				}
				// $material['operate'] = '';
				// $material['operate'] .= "<a class='btn yellow btn-xs btn-outline' href='" . U('edu_material_edit', array('material_id' => $material['material_id'],'cat_id' => $material['material_cat'])) . "' target='_self'><i class='fa fa-edit'></i>编辑</a> <a class='btn red btn-xs btn-outline' href='" . U('cms_material_do_del', array('material_id' => $material['material_id'],'cat_id' => $material['material_cat'])) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>";
			}
			$material_data['code'] = 0;
			$material_data['msg'] = '获取数据成功';
			ob_clean();$this->ajaxReturn($material_data);
		} else {
			$material_data['code'] = 1;
			$material_data['msg'] = '暂无相关数据';
			ob_clean();$this->ajaxReturn($material_data);
		}
	}

	/**
	 * @name  edu_material_info_export()
	 * @desc  参加学习人员列表导出
	 * @param $article_id
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2016-07-18
	 */
	public function edu_material_info_export()
	{
		$material_id = I("get.material_id");
		$data = array();
		$article_data['material_notes'] = getMaterialNotesList($material_id);
		$material_title = getMaterialInfo($material_id,"material_title");
		foreach ($article_data['material_notes'] as &$notes){
			$name_string = 'party_no,communist_name';
			$communist_name = getcommunistInfo($notes['add_communist'],$name_string);
			$party_name = getPartyInfo($communist_name['party_no']);
			//$notes['material_notes_communist'] = $communist_name['communist_name'];
			$item['material_title'] = $material_title;
			$item['material_communist'] = $communist_name['communist_name'];
			$item['material_party'] = $party_name;
			$item['material_time'] = $notes['add_time'];
			$data[] = $item;
		}
		$headArr = array('学习标题','学习人员','所属支部','学习时间');
		exportExcel($material_title,$headArr,$data);
	}

	/**
	 * @name  edu_material_duty_export()
	 * @desc  参加学习人员列表及其职务导出
	 * @param $article_id
	 * @return
	 * @author 曾宪坤--
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2016-07-18
	 */
	public function edu_material_duty_export(){
		$material_id=I("get.material_id");
		$sql_all="
			select a.material_title as title,c.communist_name as name,d.post_name,m.add_time as time
			from sp_edu_material_log as m left join sp_ccp_communist as c
			on m.communist_no=c.communist_no
			left join sp_ccp_party_duty as d 
			on c.post_no=d.post_no
			left join sp_edu_material as a 
			on m.material_no=a.material_id 
			where m.material_no={$material_id} 
			group by c.communist_no 
			order by d.post_no asc
		";
		$visit_list=M()->query($sql_all);

		$head['title'] = "标题";
		$head['name'] = "党员姓名";
		$head['post_name'] = "党员职务";
		$head['time'] = "学习时间";

		exportExcel("学习人员列表",$head,$visit_list);
	}
	
	/**
	 * @name  edu_material_cat_index()
	 * @desc  学习资料类型
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-11
	 */
	public function edu_material_cat_index()
	{
		checkAuth(ACTION_NAME);
		$this->display("Educat/edu_material_cat_index");
	}
	public function edu_material_cat_data()
	{
		$db_edutopic=M('edu_material_category');
		$keyword = I('get.keyword');
        $str = I('get.start');
        $strt = strToArr($str, ' - ');  //分割时间
        $start = $strt[0];
        $end = $strt[1];
        if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $status_map['add_time']  = array('between',array($start,$end));
        }
        if(!empty($keyword)){
            $status_map['cat_name']  = array('like','%'.$keyword.'%');
        }
        
		$material_cat_data['data'] = $db_edutopic->where($status_map)->order('add_time desc')->select();
		
		$communist_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		$material_cat_data['code'] = 0;
		$material_cat_data['msg'] = "获取数据成功";
		$confirma = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
		$confirmb = 'onclick="if(!confirm(' . "'确认启用？'" . ')){return false;}"';
		foreach($material_cat_data['data'] as &$list){
			if ($list['status']==1) {
				$list['status'] = '可用';
				$list['operate'] = "<a onclick='edit(".$list['cat_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60' ><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_material_cat_do_status', array('cat_id' => $list['cat_id'])) . "' $confirma class='btn btn-xs red btn-outline'><i class='fa fa-edit'></i> 停用</a>";
			}else {
				$list['status'] = '停用';
				$list['operate'] = "<a onclick='edit(".$list['cat_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60'><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_material_cat_do_status', array('cat_id' => $list['cat_id'],'status'=>1)) . "' $confirmb class='btn btn-xs blue btn-outline'><i class='fa fa-edit'></i> 启用</a>";
			}
			$list['add_staff'] = $communist_name_arr[$list['add_staff']];
			$list['add_time'] = date('Y-m-d',strtotime($list['add_time']));

		}
		ob_clean();$this->ajaxReturn($material_cat_data);
	}

	/**
	 * @name  edu_material_cat_edit()
	 * @desc  学习栏目添加add/编辑edit
	 * @param $cat_id(栏目ID)$cat_pid(栏目父级ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-16
	 */
	public function edu_material_cat_edit()
	{
		// $get = $_GET;
		checkAuth(ACTION_NAME);
		//查询资料pid
		$article_category = M("edu_material_category");
		$_communist = M('ccp_communist');
		$staff_no = session('staff_no');
		$category_list = $this->getMaterialCatList();
		$this->assign('category_list',$category_list);

		$cat_id = I("get.cat_id");
		if (! empty($cat_id)) {
			$cat_data = getMaterialCatInfo($cat_id,"all");
			$this->assign("cat_data", $cat_data);
		}
		$cat_pid = I('get.cat_pid');
		$this->assign('cat_pid',$cat_pid);
		$this->display("Educat/edu_material_cat_edit");
	}

	/**
	 * @name  edu_material_cat_do_save()
	 * @desc  学习栏目保存执行
	 * @param $_POST
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.1.0
	 * @updatetime  
	 * @addtime   2017-11-1
	 */
	public function edu_material_cat_do_save()
	{	
		checkLogin();
		$post = $_POST;
		$db_cat = M("edu_material_category");
		$party = "";
		if(! empty($post['cat_id'])){
			$post['update_time'] = date("Y-m-d H:i:s");
			$cat_map['cat_id'] = $post['cat_id'];
			$cat_data = $db_cat->where($cat_map)->save($post);
		}else{
			$cat_name = $post['cat_name'];
			$cat_name_map['cat_name'] = $cat_name;
			$cat_dt = $db_cat->where($cat_name_map)->find();
			if($cat_dt){
				showMsg('success', '类型名称重复！请重新输入。', U('edu_material_cat_edit'));
			}else{
				$post['add_time'] = date("Y-m-d H:i:s");
				$post['update_time'] = date("Y-m-d H:i:s");
				$post['add_staff'] = session('staff_no');
				$cat_data = $db_cat->add($post);
			}
		}
		if ($cat_data) {
			showMsg('success', '操作成功！！！', U('edu_material_cat_index'),1);
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	
	/**
	 * 更改前栏目及下级栏目及所属栏目
	 * @name edu_material_cat_do_status
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-11
	 */
	public function edu_material_cat_do_status(){
		checkAuth(ACTION_NAME);
		$staff_no = session('staff_no');
		$db_cat=M('edu_material_category');
		$edu_material = M('edu_material');
		$cat_id = I('get.cat_id');
		$status = I('get.status');
		if(!$status){
			$status = 0;
		}
		if (!empty($cat_id)) {
			$cat_map['cat_id'] = $cat_id;
			$cat_edit = $db_cat->where($cat_map)->find();
			$cat_data['cat_id'] = $cat_id;
			$cat_data['status'] = $status;
			$cat_data['update_time'] = date("Y-m-d H:i:s");
			$save_not = $db_cat->save($cat_data);
			$map['material_cat'] = $cat_id;
			$material['status'] = $status;
			$edu_material->where($map)->save($material);
			showMsg('success', '操作成功！！！', U('edu_material_cat_index'));
		}else {
			showMsg('error', '操作失败！！！', '');
		}
	}
	/**
	 * @name  edu_material_cat_do_del()
	 * @desc  删除当前栏目及下级栏目及所属栏目的所有学习及所有附件文件
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-16
	 */
	public function edu_material_cat_do_del(){
		$material_cat = M("edu_material_category");
		$cat_id = $_GET['cat_id'];
		if (! empty($cat_id)) {
			$cat_map['cat_id'] = $cat_id;
			$article_cat = $material_cat->where($cat_map)->delete();
		}
		if ($article_cat) {
			showMsg('success', '操作成功！！！', U('Edu/edu_material_cat_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  edu_material_notice_do_save()
	 * @desc  pc学习笔记保存
	 * @param $_POST
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-16
	 */
	public function edu_material_notice_do_save(){
		$notes['material_id'] = I("post.material_id");
		$notes['notes_content'] = I("post.material_notes_content");
		$notes['notes_title'] = I("post.material_name");
		$notes['notes_type'] = '1';
		//获取中文字符长度
		if(mb_strlen($notes['notes_content'],'utf-8') < 100){
			showMsg('error', '输入字数不少于100子','');
		}else{
			$notes_data = saveCommunistNotes($notes);
			if ($notes_data) {
				showMsg('success', '操作成功！！！', U('edu_material_index'));
			} else {
				showMsg('error', '操作失败！！！','');
			}
		}
	}
	
	/**
	 * @name  getArticleCatList()
	 * @desc  获取学习所有栏目列表-类似树形结构
	 * @param $cat_pid(父级ID) $num(制表符数量)
	 * @return 学习栏目列表
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime   2016-08-09
	 * @addtime   2016-07-15
	 */
	public function getMaterialCatList($cat_pid = 0,$num = -1,$type){
		$cms_article_category = M('edu_material_category');
		$ccp_communist = M('ccp_communist');
		$staff_no = session('staff_no');
		$comm_map['communist_no'] = $communist_no;
		$communist_data = $ccp_communist->where($comm_map)->find();
		$category_list = array();
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		$confirm = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
		$cat_map['cat_pid'] = $cat_pid;
		if(!empty($type)){
			$cat_map['status'] = 1;
		}
		$article_category_list = $cms_article_category->where($cat_map)->select();
		
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach($article_category_list as &$list){
			$list['cat_name'] = $tabs.$list['cat_name'];
			$list['add_staff'] = $staff_name_arr[$list['add_staff']];
			$list['update_time'] = getFormatDate($list['update_time'],'Y-m-d H:i');
			if($list['material_cat_pid'] == '0'){
				$operate = "<a href='".U('edu_material_cat_edit',array('cat_pid'=>$list['cat_id']))."'class='btn blue btn-xs btn-outline'>添加子栏目</a>&nbsp;";
			}else{
				$operate = "&nbsp;";
			}
			switch($list['status']){
				case 0:$list['status'] = "停用";
				$list['operate'] = $operate."<a href='".U('edu_material_cat_edit',array('cat_id'=>$list['cat_id']))."'class='btn blue btn-xs btn-outline'>编辑</a>&nbsp;
					<a href='".U('edu_material_cat_do_status',array('cat_id'=>$list['cat_id'],'status'=>1))."'class='btn blue btn-xs btn-outline' $confirm >启用</a>";
				break;
				case 1:$list['status'] = "可用";
				$list['operate'] = $operate."<a href='".U('edu_material_cat_edit',array('cat_id'=>$list['cat_id']))."'class='btn blue btn-xs btn-outline'>编辑</a>&nbsp;
					<a href='".U('edu_material_cat_do_status',array('cat_id'=>$list['cat_id'],'status'=>0))."'class='btn red btn-xs btn-outline' $confirm >停用</a>";
				break;
			}
			$category_list[] = $list;
			$article_category_sonlist = $this->getMaterialCatList($list['cat_id'],$num,$type);
			foreach($article_category_sonlist as &$sonlist){
				$category_list[] = $sonlist;
			}
		}
		return $category_list;
	}
	
}
