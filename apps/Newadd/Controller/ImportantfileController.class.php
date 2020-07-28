<?php
/**************************三务公开**********************************************/
namespace Newadd\Controller;
use Common\Controller\BaseController;
class ImportantfileController extends BaseController{
	 /**
	 * @name  life_affairs_index()
	 * @desc  三务公开首页
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-24
	 */
	public function life_affairs_index(){
		checkAuth(ACTION_NAME);//是否有权限
		$communist_no=session('staff_no');
		$user_map['user_relation_no'] = $communist_no;
		$role=M("sys_user")->where($user_map)->getField('user_role');//获取角色
		if($role==10000){
			$this->assign("agent",1);
		}
		$cat_id = I('get.cat_id');
		if (!empty($cat_id)) {
			$cat_data = getArticleCatInfo($cat_id,'all');//获取文章类型名称
			$this->assign("cat_id", $cat_id);
			$this->assign("cat_data", $cat_data);
		}
		$is_page = I('get.is_page');
		if($is_page != 1){
			$is_page = 0;
		}
		$this->assign("is_page", $is_page);
		$staff_no = session('staff_no');
		$communist_data = getCommunistInfo($communist_no,'all');
		$party_str = getPartyChildNos($communist_data['party_no'],'str').$communist_data['party_no'];
		$staff_no = session('staff_no');
		$party_no=getCommunistInfo($communist_no,'party_no');
		$party_str = getPartyChildNos($party_no,'str').$party_no;//获取子级部门列表
		$cat_list = M("cms_affairs_category")->where("status='1'")->select();
		$this->assign('cat_list',$cat_list);
		$industry = getConfig('industry');
		$this->assign('industry',$industry);
		$type = '1';
		$this->assign('type',$type);
		$this->display("Importantfile/life_article_index");
	}
	/**
	 * @name  life_affairs_table()
	 * @desc  三务公开文章管理数据列表
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime    2017-10-25
	 */
	public function life_affairs_table(){
		$cat_id = I('get.cat_id');
		$this->assign('cat_id',$cat_id);
		$keyword = I('get.keyword');
		$this->assign('keyword',$keyword);
		$height=I('get.height');
		$this->assign('height',$height);
		$start_time = I('get.start_time');
		$this->assign('start_time',$start_time);
		$end_time = I('get.end_time');
		$this->assign('end_time',$end_time);
		$this->assign('type',1);
		$this->display("Importantfile/life_article_table");
	}
	
	/**
	 * @name  life_affairs_table_data()
	 * @desc  获取三务公开文章数据
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_affairs_table_data()
	{
		$cat_id = I("get.cat_id");
		if(empty($cat_id)){
			$cat_id = session(cat_id);
			if(!empty($cat_id)){
				$cat_id = session(cat_id);
			}else{
				$cat_id = 0;
			}
			unset($_SESSION['cat_id']);
		}
		$keyword = I('get.keyword');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start_time = $strt[0];
		$end_time = $strt[1];
		$type = I('get.type');
		$where = "1=1";
		if(!empty($keyword)){
			$where .= " and (article_title like '%$keyword%' or article_keyword like '%$keyword%' or article_description like '%$keyword%' or article_content like '%$keyword%')";
		}
		if(!empty($start_time) && !empty($end_time)){
			$where .= " and add_time >= '$start_time' and add_time <= '$end_time' ";
		}

		$article_data['data'] = getAffairsList($cat_id,0,0,'',$keyword,$start_time,$end_time,'',2);
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($article_data['data'] as &$article) {
			$article['type'] = 0;
			$article['add_staff']=$staff_name_arr[$article['add_staff']];
			$article['add_time']=getFormatDate($article['add_time'], "Y-m-d");
			$logo = getUploadInfo($article['article_thumb']);
			if(empty($logo)){
				// 文章缩略图
				$article['article_thumb'] = "无";
			}else{
				// 文章缩略图
				$article['article_thumb'] = "<img src='$logo' width='50'>";
			}
			//$article['article_title'] = mb_substr($article['article_title'], 0, 10, 'utf-8');
			$article['article_describe'] = mb_substr($article['article_describe'], 0, 10, 'utf-8');
			$article['article_content'] = mb_substr(strip_tags($article['article_content']), 0, 10, 'utf-8');
			//$article['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('life_affairs_info', array('article_id' => $article['article_id'])) . "' target='_self'><i class='fa fa-info'></i> 详情</a> <a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('life_affairs_edit', array('article_id' => $article['article_id'],'cat_id' => $article['article_cat'])) . "' target='_self'><i class='fa fa-edit'></i>编辑</a> <a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('life_affairs_del', array('article_id' => $article['article_id'])) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>";
		}
		$article_data['count'] = 0;
		$article_data['code'] = 0;
		$article_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($article_data);
	}
	
	
	 /**
	 * @name  life_affairs_edit()
	 * @desc  三务公开编辑
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime    2017-10-25
	 */
	public function life_affairs_edit(){
		checkAuth(ACTION_NAME);
		$category_list = $this->getArticleCatList();
		//获取部门列表
		$db_party = M("ccp_party");
		$party_data = $db_party->where("status <> 0")->select();
		$this->assign("party_list",$party_data);
		$this->assign('category_list',$category_list); 
		// 添加时获取文章类型的id
		// 修改时获取文章id
		$article_id = I('get.article_id');
		if (! empty($article_id)) {// 修改
			$affairs_map['article_id'] = $article_id;
			$article_data = M('cms_affairs')->where($affairs_map)->find();
			$this->assign("article", $article_data);
		}
		$this->assign('type',1);
	    $this->display("Importantfile/life_article_edit");
	}
	 /**
	 * @name  life_affairs_info()
	 * @desc  三务公开详情
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-25
	 */
	public function life_affairs_info(){
	   checkAuth(ACTION_NAME);
	   $article_id = I("get.article_id");
	   $article_data = getAffairsInfo($article_id,'all');
	   //$article_data['add_staff'] = getCommunistInfo($article_data['add_staff'],'communist_name');
	   $where['staff_no'] = $article_data['add_staff'];
	   $article_data['add_staff']  = M('hr_staff')->where($where)->getField('staff_name');
	   
	   // 文章缩略图
	   $article_img = getUploadInfo($article_data['article_thumb']);
	  
	   $article_data['article_thumb'] = $article_img;//"<img src='$article_img' width='50px'>";
	   $this->assign("article_data", $article_data);
	   $this->display("Importantfile/life_article_info");
	}
	 /**
	 * @name  life_affairs_do_save()
	 * @desc  三务公开保存执行
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-25
	 */
	public function life_affairs_do_save(){
		$db_article = M("cms_affairs");
		$article_id = $_POST['article_id'];
		$post = $_POST;
		$post['update_time'] = date("Y-m-d H:i:s");
		if (! empty($post['article_imgs'])) {
			$post['article_thumb'] = $post['article_imgs'];
		}
		if (! empty($article_id)) {
			$article_map['article_id'] = $article_id;
			$article_data = $db_article->where($article_map)->save($post);
		} else {
			$post['add_staff'] = session('staff_no');
			$post['producer'] = session('staff_no');
			$post['add_time'] = date("Y-m-d H:i:s");
			
			$post['cms_affairs_type'] = 2;
			$article_data = $db_article->add($post);
		}
		//session('cat_id',$post['article_cat']);
		if ($article_data) {
			showMsg('success', '操作成功！！！', U('Lifeaffairs/life_affairs_index'),1);
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	 /**
	 * @name  life_affairs_del()
	 * @desc  三务公开执行删除
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-25
	 */
	public function life_affairs_del(){
		checkAuth(ACTION_NAME);
		$article_id = I("get.article_id");
		$db_article = M("cms_affairs");
		$article_map['article_id'] = $article_id;
		$res = $db_article->where($article_map)->delete();
		if($res){
			showMsg('success', '操作成功！！！', U('Lifeaffairs/life_affairs_index',array('type'=>1)));
		}else{
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  life_article_cat_index()
	 * @desc  三务公开栏目首页
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-24
	 */
	public function life_affairs_cat_index()
	{
		checkAuth(ACTION_NAME);
		$this->assign('type',1);
		$this->display("Lifecat/life_article_cat_index");
	}
	/**
	 * @name  life_affairs_cat_index_data()
	 * @desc  三务公开栏目首页数据
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-4-1
	 */
	public function life_affairs_cat_index_data()
	{
		$category_list['data'] = $this->getArticleCatList();
		$category_list['code'] = 0;
		$category_list['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($category_list);
	}
	/**
	 * @name  life_affairs_cat_edit()
	 * @desc  三务公开栏目首页栏目添加add/编辑edit
	 * @param $cat_id(栏目ID)$cat_pid(栏目父级ID)
	 * @return
	 * @author   王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime  2017-10-24
	 * @addtime   2016-07-15
	 */
	public function life_affairs_cat_edit()
	{
		checkAuth(ACTION_NAME);
		$type = I('get.type');
		$this->assign('type',$type);
		$category_list = $this->getArticleCatList();
		$this->assign('category_list',$category_list);
		$cat_id = I("get.cat_id");
		if (! empty($cat_id)) {
			$cat_map['cat_id'] = $cat_id;
			$cat_data = M('cms_affairs_category')->where($cat_map)->find();
			$this->assign("cat_data",$cat_data);
		}
		$cat_pid = I('get.cat_pid');
		$this->assign('cat_pid',$cat_pid);
		$this->assign('type',$type);
		$this->display("Lifecat/life_article_cat_edit");
	}
	/**
	 * @name  life_affairs_cat_do_save()
	 * @desc  三务公开栏目保存执行
	 * @param $_POST
	 * @return
	 * @author 杨凯  王宗彬
	 * @version 版本 V1.1.0
	 * @updatetime  2017-10-24
	 * @addtime   2017-10-12
	 */
	public function life_affairs_cat_do_save(){
		//checkLogin();
		$db_cat = M("cms_affairs_category");
		$post = $_POST;
		if(! empty($post['cat_id'])){
			$post['update_time'] = date("Y-m-d H:i:s");
			$cat_map['cat_id'] = $post['cat_id'];
			$cat_data = $db_cat->where($cat_map)->save($post);
			if ($cat_data) {
				showMsg('success', '操作成功！！！', U('Lifeaffairs/life_affairs_cat_index'));
			} else {
				showMsg('error', '操作失败！！！','');
			}
		}else{
			$cat_name = $post['cat_name'];
			$cat_name_map['cat_name'] = $cat_name;
			$cat_dt = $db_cat->where($cat_name_map)->find();
			if($cat_dt){
				showMsg('success', '类型名称重复！请重新输入。', U('Lifeaffairs/life_affairs_cat_edit',array('type'=>1)));
			}else{
				$post['add_time'] = date("Y-m-d H:i:s");
				$post['update_time'] = date("Y-m-d H:i:s");
				$post['add_staff'] = session('staff_no');
				$cat_data = $db_cat->add($post);
				if ($cat_data) {
					showMsg('success', '操作成功！！！', U('Lifeaffairs/life_affairs_cat_index',array('type'=>1)));
				} else {
					showMsg('error', '操作失败！！！','');
				}
			}
		}
	}
	/**
	 * @name  life_cat_do_save()
	 * @desc  三务公开栏目单页保存
	 * @param $_POST
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-24
	 */
	public function life_cat_do_save()
	{
		checkLogin();
		$db_cat = M("cms_affairs_category");
		$post = $_POST;
		if (! empty($post['cat_id'])) {
			$post['update_time'] = date("Y-m-d H:i:s");
			$cat_map['cat_id'] = $post['cat_id'];
			$cat_data = $db_cat->where($cat_map)->save($post);
		}
		if ($cat_data) {
			ob_clean();$this->ajaxReturn(1);
		} else {
			ob_clean();$this->ajaxReturn(0);
		}
	}
	/**
	 * @name  life_affairs_cat_do_del()
	 * @desc  删除当前栏目及下级栏目及所属栏目的所有文章及所有附件文件
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-24
	 */
	public function life_affairs_cat_do_del()
	{
		checkAuth(ACTION_NAME);
		$cat_id = $_GET['cat_id'];
		$type = I('get.type');
		if (!empty($cat_id)) {
			$cat_map['cat_id'] = $cat_id;
			$affairs_cat = M('cms_affairs_category')->where($cat_map)->delete();//删除当前栏目
			if($affairs_cat){
				$cat_list = $this->getArticleCatList($cat_id);//获取当前栏目的所属下级栏目（所有子级）
				foreach($cat_list as $list){//删除当前栏目的所属下级栏目（所有子级）
					$id = $list['cat_id'];
					$aff_cat_map['cat_id'] = $id;
					M('cms_affairs_category')->where($aff_cat_map)->delete();
				}
				$affairs_list = getArticleList($cat_id,'','','','','','','',$type);//获取当前栏目及下级栏目所有文章（所有子级）
				foreach($affairs_list as $list){//删除当前栏目及下级栏目所有文章
					$affairs_id = $list['affairs_id'];
					$affairs_map['article_id'] = $article_id;
					M('cms_affairs')->where($affairs_map)->delete();
					delFile($list['affairs_img']);
				}
			}
		}
		if ($affairs_cat) {
			showMsg('success', '操作成功！！！', U('Lifeaffairs/life_affairs_cat_index',array('type'=>'$type')));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  life_affairs_page_data()
	 * @desc  获取三务公开栏目单页数据(树状结构)
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-25
	 */
	public function life_affairs_page_data(){
		$cat_id = I('get.cat_id');
		$cat_data = getArticleCatInfo($cat_id,'all',1);
		ob_clean();$this->ajaxReturn($cat_data);
	
	}
	/**
	 * @name  getArticleCatList()
	 * @desc  获取文章所有栏目列表-类似树形结构
	 * @param $cat_pid(父级ID) $num(制表符数量)
	 * @return 文章栏目列表
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017-10-24
	 * @addtime   2016-07-15
	 */
	public function getArticleCatList($cat_pid = 0,$num = -1){
		$cms_affairs_category = M('cms_affairs_category');
		$ccp_communist = M('ccp_communist');
		$staff_no = session('staff_no');
		$comm_map['communist_no'] = $staff_no;
		$communist_data = $ccp_communist->where($comm_map)->find();
		$staff_dept_no=getStaffInfo($staff_no,'staff_dept_no');
		$party_str = getDeptChildNos($staff_dept_no,'str').$staff_dept_no;
		$category_list = array();
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		if(!empty($party_str)){
			$cat_map['cat_pid'] = $cat_pid;
			$cat_map['status'] = 1;
			$affairs_category_list = $cms_affairs_category->where($cat_map)->order('add_time desc')->select();
			$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
			foreach($affairs_category_list as &$list){
				$list['cat_name'] = $tabs.$list['cat_name'];
				switch($list['status']){
					case 0:$list['status'] = "停用";
					break;
					case 1:$list['status'] = "可用";
					break;
				}
				$list['add_staff'] = $staff_name_arr[$list['add_staff']];
				$list['update_time'] = getFormatDate($list['update_time'],'Y-m-d H:i');
				$list['operate'] = "<a href='".U('life_affairs_cat_edit',array('cat_pid'=>$list['cat_id'],'type'=>1))."'class='btn blue btn-xs btn-outline'>添加子栏目</a>&nbsp;&nbsp;<a href='".U('life_affairs_cat_edit',array('cat_id'=>$list['cat_id'],'type'=>1))."'class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>&nbsp;<a href='".U('life_affairs_cat_do_del',array('cat_id'=>$list['cat_id'],'type'=>1))."'class='layui-btn layui-btn-del layui-btn-xs' $confirm >删除</a>";
				$category_list[] = $list;
				$affairs_category_sonlist = $this->getArticleCatList($list['cat_id'],$num);
				foreach($affairs_category_sonlist as &$sonlist){
					$category_list[] = $sonlist;
				}
			}
		}else{
			$aff_cat_map['cat_pid'] = $cat_pid;
			$aff_cat_map['add_staff'] = $communist_no;
			$aff_cat_map['status'] = 1;
			$affairs_category_list = $cms_affairs_category->where($aff_cat_map)->order('add_time desc')->select();
			$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
			foreach($affairs_category_list as &$list){
				$list['cat_name'] = $tabs.$list['cat_name'];
				switch($list['status']){
					case 0:$list['status'] = "停用";
					break;
					case 1:$list['status'] = "可用";
					break;
				}
				$list['add_staff'] = $staff_name_arr[$list['add_staff']];
				$list['update_time'] = getFormatDate($list['update_time'],'Y-m-d H:i');
				$list['operate'] = "<a href='".U('life_affairs_cat_edit',array('cat_pid'=>$list['cat_id'],'type'=>1))."'class='btn blue btn-xs btn-outline'>添加子栏目</a>&nbsp;<a href='".U('life_affairs_cat_edit',array('cat_id'=>$list['cat_id'],'type'=>1))."'class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>&nbsp;<a href='".U('life_affairs_cat_do_del',array('cat_id'=>$list['cat_id'],'type'=>1))."'class='layui-btn layui-btn-del layui-btn-xs' $confirm >删除</a>";
				$category_list[] = $list;
				$affairs_category_sonlist = $this->getArticleCatList($list['cat_id'],$num);
				foreach($affairs_category_sonlist as &$sonlist){
					$category_list[] = $sonlist;
				}
			}
		}
		return $category_list;
	}
}
