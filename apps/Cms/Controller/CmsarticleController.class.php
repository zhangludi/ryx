<?php
/***************************文章与文稿管理***********************************************/
namespace Cms\Controller;
use Common\Controller\BaseController;
use Think\Cache\Driver\Redis;
class CmsarticleController extends BaseController{
	/**
	 * @name  cms_article_index()
	 * @desc  文章管理首页
	 * @param
	 * @return
	 * @author 杨凯 ---王宗斌
	 * @version 版本 V1.0.0
	 * @updatetime 2017-11-8
	 * @addtime   2017.11.10 加session
	 */
	public function cms_article_index(){
		switch ($_GET['cat_id']) {
			case 10:$cat = "_3";break;
			case 11:$cat = "_2";break;
			case 13:$cat = "_4";break;
			case 14:$cat = "_7";break;
			case 17:$cat = "_5";break;
			case 18:$cat = "_6";break;
			case 15:$cat = "_15";break;
			case 19:$cat = "_19";break;
			default:
				$cat = "_1";break;
		}
		session('cat',$cat);
		checkAuth(ACTION_NAME.$cat);//判断越权
		$cat_id = $_GET['cat_id'];
		$party_no = I('get.party_no');
		$this->assign('party_no',$party_no);
		if(!empty($cat_id)){
			// 获取文章信息
			$cat_map['cat_id'] = $cat_id;
			$cat_info = M('cms_article_category')->where($cat_map)->field('cat_name,cat_type')->find();
			$cat_type = $cat_info['cat_type']; // 分类类型
			$cat_name = $cat_info['cat_name']; // 分类名称
		}
		if ($cat_type==3 ) {
			if ($cat_id!=14) {
				$agent = 1;
				$this->assign('agent',$agent);
			}
		}else {
			if ($cat_id!=14) {
				if($cat_id == 15){
					$cat_id = 15;
					$this->assign('agent',1);

				}else{
					$cat_id=0;
				}
				$cat_info['cat_type'] = 1;
				$cat_info['cat_name'] = '文章管理';
			}
		}
		
		if($cat_id == 14){
			$cat_list = getArticleCatChildNoselfNos($cat_id);
			$cat_num = count($cat_list);
			$cate_map['cat_id'] = $cat_id;
			$cat_info = M('cms_article_category')->where($cate_map)->field('cat_id,cat_name,cat_type,cat_pid')->find();
			$cat_list[$cat_num+1] = $cat_info;
		} else {
			$cat_list = M()->query("SELECT `cat_id`, `cat_name`, `cat_type`, `cat_pid` FROM `sp_cms_article_category` WHERE  cat_pid != '14' and cat_pid not in (SELECT cat_id FROM sp_cms_article_category where cat_pid = 14) and cat_type = '1' or cat_type = '4' and cat_id != '14' and cat_id != '12'");
		}
		$this->assign('cat_id',$cat_id);
		$this->assign('cat_info',$cat_info);
		$this->assign('cat_list',$cat_list);
		$this->display("Cmsarticle/cms_article_index");
	}
	/**
	 * @name  cms_article_cat_index()
	 * @desc  文章栏目首页
	 * @param
	 * @return
	 * @author 杨凯  
	 * @version 版本 V1.0.0
	 * @updatetime 
	 * @addtime   2017-10-11
	 */
	public function cms_article_cat_index()
	{
		switch ($_GET['cat_id']) {
			case 10:$cat = "_3";break;
			case 11:$cat = "_2";break;
			case 13:$cat = "_4";break;
			case 14:$cat = "_7";break;
			case 17:$cat = "_5";break;
			case 18:$cat = "_6";break;
			default:
				$cat = "_1";break;
		}
		
		session('cat',$cat);
		checkAuth(ACTION_NAME.$cat);
		$cat_id = $_GET['cat_id'];
		$category_list = $this->getArticleCatList($cat_id,-1);
		$this->assign('cat_id',$cat_id);
		$this->assign('category_list',$category_list);
		$this->display("Cmscat/cms_article_cat_index");
	}

	/**
	 * @name  cms_article_cat_index()
	 * @desc  文章栏目首页
	 * @param
	 * @return
	 * @author 杨凯  
	 * @version 版本 V1.0.0
	 * @updatetime 
	 * @addtime   2017-10-11
	 */
	public function cms_article_cat_index_data()
	{
		$cat_name = I('get.cat_name');
		$cat_id = I('get.cat_id');
		$category_list = $this->getArticleCatList($cat_id,-1,$cat_name);
		$article_cat_data['data'] = $category_list;
		$article_cat_data['count'] = 0;
		$article_cat_data['code'] = 0;
		$article_cat_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($article_cat_data);
	}
	/**
	 * @name  cms_article_cat_edit()
	 * @desc  文章栏目添加add/编辑edit
	 * @param $cat_id(栏目ID)$cat_pid(栏目父级ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-08
	 */
	public function cms_article_cat_edit(){
		switch ($_GET['cat_id']) {
			case 10:$cat = "_3";break;
			case 11:$cat = "_2";break;
			case 13:$cat = "_4";break;
			case 14:$cat = "_7";break;
			case 17:$cat = "_5";break;
			case 18:$cat = "_6";break;
			default:
				$cat = "_1";break;
		}
		session('cat',$cat);
		checkAuth(ACTION_NAME.$cat);
		$cat_id = $_GET['cat_id'];
		$category_list = $this->getArticleCatList();
		$this->assign('category_list',$category_list);
		if (!empty($cat_id)) {
			$cat_data = getArticleCatInfo($cat_id,'all');
			$this->assign("cat_data", $cat_data);
		}
		$cat_pid = I("get.cat_pid");
		$this->assign('cat_pid',$cat_pid);
		$this->display("Cmscat/cms_article_cat_edit");
	}
	/**
	 * @name  cms_article_page_data()
	 * @desc  判断文章栏目是否为单页
	 * @param
	 * @return
	 * @author 王彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2016-08-13
	 */
	public function cms_article_page_data(){
		$cat_id = I('get.cat_id');
		$cat_data = getArticleCatInfo($cat_id,'all');
		ob_clean();$this->ajaxReturn($cat_data);
	
	}
	
	/**
	 * @name  cms_article_edit()
	 * @desc  文章添加/编辑表单页面
	 * @param
	 * @return
	 * @author 杨凯 ---王宗斌
	 * @version 版本 V1.0.0
	 * @updatetime 2017.11.10 加session
	 * @addtime   2017-11-4
	 */
	public function cms_article_edit()
	{
		checkAuth(ACTION_NAME.session('cat'));
		//获取部门列表
		$db_party = M("ccp_party");
		$party_data = $db_party->where("status = 0")->select();
		$this->assign("party_list",$party_data);
		// 添加时获取文章类型的id
		$cat_id = I('get.cat_id');
		if (!empty($cat_id)) {
			$this->assign("cat_id", $cat_id);
		}
		$cat_map['cat_id'] = $cat_id;
		$cat_type = M('cms_article_category')->where($cat_map)->getField('cat_type');
		if(!empty($cat_type)){
			if ($cat_type != 1) {
				if($cat_type == 4 ){
					$cat_pid=M('cms_article_category')->where($cat_map)->getField('cat_pid');
				} else {
					$cat_pid=getArticleTopCatNos($cat_id);
				}
				$category_list = getArticleCatSelect($cat_pid,0,$cat_id,$cat_type);
			}else {
				$category_list = $this->getArticleCatList();
			}
		} else {
			$category_list = $this->getArticleCatList();
		}
		$this->assign('cat_type',$cat_type);
		$this->assign('category_list',$category_list);
		// 修改时获取文章id
		$article_id = I('get.article_id');
		if (! empty($article_id)) {// 修改文章
			$article_data = getArticleInfo($article_id,'all');
			$this->assign("article", $article_data);
		}
		$this->display("Cmsarticle/cms_article_edit");
	}
	/**
	 * @name  cms_article_info()
	 * @desc  文章详情
	 * @param $article_id
	 * @return
	 * @author 杨凯 ---王宗斌
	 * @version 版本 V1.0.0
	 * @updatetime 2017.11.10 加session
	 * @addtime   2016-07-18
	 */
	public function cms_article_info()
	{
		checkAuth(ACTION_NAME.session('cat')); 
		$article_id = I("get.article_id");
		$article_data = getArticleInfo($article_id,'all');
		//$article_data['add_staff'] = getStaffInfo($article_data['add_staff']);
		$article_data['add_staff'] = peopleNoName($article_data['add_staff']);
		// 文章缩略图
		$article_img = getUploadInfo($article_data['article_thumb']);
		$article_data['article_thumb'] =$article_img;
		$this->assign("article_data", $article_data);
		$map['cat_id'] = $article_data['article_cat'];
		$cat_pid = M("cms_article_category")->where($map)->getField("cat_pid");
		if(($article_data['article_cat']==14) || ($cat_pid==14)){
			$type = 14;
			$this->assign("cat_type_a", $type);
		}
		M('cms_article')->where("article_id='$article_id'")->setInc('article_view');
		$this->display("Cmsarticle/cms_article_info");
	}
	/**
	 * @name  cms_article_do_save()
	 * @desc  文章保存
	 * @param $_POST
	 * @return
	 * @author 杨凯   王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   2017-11-22 添加调转 
	 * @addtime   2017-11-08
	 */
	public function cms_article_do_save()
	{
		checkLogin();
		$db_article = M("cms_article");
		$post = $_POST;
		$article_id = $post['article_id'];
		if(!empty($post['article_cat'])){
			$cat_pid = getArticleTopCatNos($post['article_cat']);
			$post['update_time'] = date("Y-m-d H:i:s");
			if (! empty($post['article_imgs'])) {
				$post['article_thumb'] = $post['article_imgs'];
			}
			if (! empty($article_id)) {
				$article_map['article_id'] = $article_id;
				$article_data = $db_article->where($article_map)->save($post);
			} else {

				$post['add_staff'] = peopleNo(session('staff_no'),2);
				$post['producer'] = $this->staff_no;
				$post['add_time'] = date("Y-m-d H:i:s");
				$article_data = $db_article->add($post);
			}
			if($cat_pid == '14'){
				$article_cat = $cat_pid;
				session('cat_id',$article_cat);
			}else{
				session('cat_id',$post['article_cat']);
				$article_cat = $post['article_cat'];
			}
			if ($article_data) {
				if($article_cat){
					showMsg('success', '操作成功！！！',U('Cmsarticle/cms_article_index',array('cat_id'=>$article_cat)),1);
				}
			} else {
				showMsg('error', '操作失败！！！','');
			}
		} else {
			showMsg('error', '请选择文章类型！！！','');
		}
		
	}
	/**
	 * @name  cms_article_del()
	 * @desc  文章删除（同时删除附件及附件文件）
	 * @param $article_id(文章ID)
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2016-07-15
	 */
	public function cms_article_do_del()
	{
		checkAuth(ACTION_NAME.session('cat'));
		$db_article = M('cms_article');
		$article_id = I("get.article_id");
		session('cat_id',I("get.cat_id"));
		$cat_id = I("get.cat_id");
		$cat_map['cat_id'] = $cat_id;
		$cat_pid = M('cms_article_category')->where($cat_map)->getField('cat_pid');
		if (!empty($article_id)) {
			$article_dt = getArticleInfo($article_id,'all');
			$article_map['article_id'] = $article_id;
			$article_data = $db_article->where($article_map)->delete();
		}
		if($cat_pid == 14){
			$cat_id = 14;
		}
		if ($article_data) {
			delFile($article_dt['article_img']);
			showMsg('success', '操作成功！！！',U('Cmsarticle/cms_article_index',array('cat_id'=>$cat_id)));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  cms_article_cat_do_save()
	 * @desc  文章栏目保存执行
	 * @param $_POST
	 * @return
	 * @author 杨凯   
	 * @version 版本 V1.1.0
	 * @updatetime 2017-11-08
	 * @addtime   2017-10-27
	 */
	public function cms_article_cat_do_save(){
		checkLogin();
		$db_cat = M("cms_article_category");
		$post = $_POST;
		if(!empty($post['cat_id'])){
			$post['update_time'] = date("Y-m-d H:i:s");
			$cat_map['cat_id'] = $post['cat_id'];
			$cat_data = $db_cat->where($cat_map)->save($post);
		}else{
			$cat_name = $post['cat_name'];
			$cat_map['cat_name'] = $cat_name;
			$cat_dt = $db_cat->where($cat_map)->find();
			if($cat_dt){
				showMsg('success', '类型名称重复！请重新输入。', '');
			}else{
				$post['add_time'] = date("Y-m-d H:i:s");
				$post['update_time'] = date("Y-m-d H:i:s");
				$post['add_staff'] = $this->staff_no;
				$cat_data = $db_cat->add($post);
			}
		}
		if ($cat_data) {
			if ($post['cat_id']=='1403') {//便民服务大厅jq返回数据查询
				exit('保存完成');
			}else {
				showMsg('success', '操作成功！！！', U('cms_article_cat_index'),1);
			}
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  cms_article_cat_do_del()
	 * @desc  删除当前栏目及下级栏目及所属栏目的所有文章及所有附件文件
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime  2017-10-27
	 */
	public function cms_article_cat_do_del()
	{
		checkAuth(ACTION_NAME.session('cat'));
		$cat_id = $_GET['cat_id'];
		if (! empty($cat_id)) {
			$article_cat = delArticleCat($cat_id);//删除当前栏目
			if($article_cat){
				$cat_list = getArticleCatList('',$cat_id);
				foreach($cat_list as $list){//删除当前栏目的所属下级栏目（所有子级）
					delArticleCat($list['cat_id']);
				}
				$article_list = getArticleList($cat_id);//获取当前栏目及下级栏目所有文章（所有子级）
				foreach($article_list as $list){//删除当前栏目及下级栏目所有文章
					delArticle($list['article_id']);
					delFile($list['article_img']);
				}
			}	
		}
		if ($article_cat) {
			showMsg('success', '操作成功！！！', U('Cmsarticle/cms_article_cat_index'));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  cms_article_table_data()
	 * @desc  文章表数据页面
	 * @param $cat_id(栏目ID)
	 * @return
	 * @author 杨凯   
	 * @version 版本 V1.0.0
	 * @updatetime   2017-11-8
	 * @addtime   2017-10-11
	 */
	public function cms_article_table_data(){
		
		$cat_id = I("get.cat_id");
		if(empty($cat_id)){
			$cat_id = 0;
		}
		$keyword = I('get.keyword');
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start_time = $strt[0];
		$end_time = $strt[1];
		$page = I('get.page');
		$pagesize = I('get.limit');
		$page = ($page-1)*$pagesize;
		$party_no = I('get.party_no');
		if($party_no){
			$article_cat_arr =  M("cms_article_category")->where("cat_type=1")->getField("cat_id",true);
			$article_cat_str = arrToStr($article_cat_arr);
			$ppp['article_cat'] = array('in',$article_cat_str);
			$ppp['party_no'] = array('in',getPartyChildNos($party_no));
			$article_data['data'] = M("cms_article")->where($ppp)->limit($page,$pagesize)->select();
			$article_data['count'] = M("cms_article")->where($ppp)->count();
		}else{
			$article_data = getArticleList($cat_id,$page,$pagesize,$keyword,$start_time,$end_time);
		}
		


		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$firm = 'onclick="if(!confirm(' . "'确认进行此操作？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		if(!empty($article_data['data'] && $article_data['data'] != 'null')){
			foreach ($article_data['data'] as &$article) {
				$article['cat_name'] = getArticleCatInfo($article['article_cat']);
				// if(!empty($staff_name_arr[$article['add_staff']])){
				// 		$article['add_staff'] = $staff_name_arr[$article['add_staff']];
				// } else {
				// 		$article['add_staff'] = $communist_name_arr[$article['add_staff']];
				// }
            	$article['add_staff'] = peopleNoName($article['add_staff']);

				$article['add_time']=getFormatDate($article['add_time'], "Y-m-d");
				$logo = getUploadInfo($article['article_thumb']);
				$article['operate'] = '';
				if ($article['article_cat']=='15') {
					switch ($article['status']){
						case 1:$article['operate'] = "<a class='btn blue btn-xs btn-outline' >审核通过</a>";
							break;
						case 21:$article['operate'] = "<a class='btn blue btn-xs btn-outline' href='".U('cms_article_status',array('$article_id' => $article['article_id'],'status'=>1))."' $firm>通过</a>
						<a class='btn red btn-xs btn-outline' href='".U('cms_article_status',array('$article_id' => $article['article_id'],'status'=>31))."' $firm>驳回</a>";
							break;
						case 31:$article['operate'] = "<a class='btn red btn-xs btn-outline'>已驳回</a>";
							break;
					}
				}
				if(empty($logo)){
					// 文章缩略图
					$article['article_thumb'] = '无';
				}else{
					// 文章缩略图
					$article['article_thumb'] = "<img src='$logo' width='50'>";
				}
				$article['article_ids'] = $article['article_id'];
				$article['article_title'] = $article['article_title'];
				if(!empty($article['article_describe'])){
					$article['article_describe'] = mb_substr($article['article_describe'], 0, 10, 'utf-8');
				}
				if(!empty($article['article_content'])){
					$article['article_content'] = mb_substr(strip_tags($article['article_content']), 0, 10, 'utf-8');
				}
				
				$article['operate'] .= "<a class='btn yellow btn-xs btn-outline' href='" . U('cms_article_edit', array('article_id' => $article['article_id'],'cat_id' => $article['article_cat'])) . "' target='_self'><i class='fa fa-edit'></i>编辑</a> 
						<a class='btn red btn-xs btn-outline' href='" . U('cms_article_do_del', array('article_id' => $article['article_id'],'cat_id' => $article['article_cat'])) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>";
			}
			$article_data['code'] = 0;
			$article_data['msg'] = '获取数据成功';
			ob_clean();$this->ajaxReturn($article_data);
		} else {
			$article_data['code'] = 0;
			$article_data['msg'] = '暂无相关数据';
			ob_clean();$this->ajaxReturn($article_data);
		}

	}
	/**
	 * @name  getArticleCatList()
	 * @desc  获取文章所有栏目列表-类似树形结构
	 * @param $cat_pid(父级ID) $num(制表符数量)
	 * @return 文章栏目列表
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-11-9
	 */
	public function getArticleCatList($cat_pid = 0,$num = -1,$cat_name){
		$cms_article_category = M('cms_article_category');
		$category_list = array();
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		if(empty($cat_pid)){
			$cat_pid = 0;
		}
		if(!empty($cat_name)){
			$cat_map['cat_name'] = array('like','%'.$cat_name.'%');
		}
		if($cat_pid == 14 && $num == 0){
			if(!empty($cat_name)){
				$cat_map['cat_name'] = array('like','%'.$cat_name.'%');
				$cat_map['status'] = 1;
			} else {
				$cat_map['cat_id'] = $cat_pid;
				$cat_map['status'] = 1;
			}
			$article_category_list = $cms_article_category->where($cat_map)->select();
		} else {
			if($cat_pid != 14){
				$cat_map['cat_pid'] = $cat_pid;
				$cat_map['status'] = 1;
				$cat_map['cat_id'] = array('neq','14');
				$cat_map['_string'] = "( cat_type = '1' OR cat_type = '4')";
				$article_category_list = $cms_article_category->where($cat_map)->select();
			} else {
				$cat_map['cat_pid'] = $cat_pid;
				$cat_map['status'] = 1;
				$cat_map['cat_id'] = array('neq','14');
				$cat_map['_string'] = "( cat_type != '1' OR cat_type != '4')";
				$article_category_list = $cms_article_category->where($cat_map)->select();
			}
		}
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach($article_category_list as &$list){
			$list['cat_name'] = $tabs.$list['cat_name'];
			switch($list['status']){
				case 0:$list['status'] = "停用";
				break;
				case 1:$list['status'] = "可用";
				break;
			}
			$list['add_staff'] = $staff_name_arr[$list['add_staff']];
			$list['add_time'] = getFormatDate($list['add_time'],'Y-m-d');
			if ($list['cat_type']==3 || $list['cat_type']==4) {
				$list['operate'] = "<a>内置栏目</a>";
			} else if($list['cat_type']==2 ){
				//$list['operate'] = "<a href='".U('cms_article_cat_edit',array('cat_id'=>$list['cat_id']))."' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>";
				$list['operate'] = "<a onclick='edit_id(&#34;$list[cat_id]&#34;)' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>";
			} else if($list['cat_type']==5){
				$list['operate'] = "<a onclick='edit_pid(&#34;$list[cat_id]&#34;)' class='layui-btn  layui-btn-xs layui-btn-f60'>添加子栏目</a>&nbsp;
					<a onclick='edit_id(&#34;$list[cat_id]&#34;)' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>&nbsp;<a href='".U('cms_article_cat_do_del',array('cat_id'=>$list['cat_id']))."' class='layui-btn layui-btn-del layui-btn-xs' $confirm >删除</a>";

					//<a href='".U('cms_article_cat_edit',array('cat_pid'=>$list['cat_id']))."' class='layui-btn  layui-btn-xs layui-btn-f60'>添加子栏目</a><a href='".U('cms_article_cat_edit',array('cat_id'=>$list['cat_id']))."' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>
			}else {
				$list['operate'] = "<a onclick='edit_pid(&#34;$list[cat_id]&#34;)' class='layui-btn  layui-btn-xs layui-btn-f60'>添加子栏目</a>&nbsp;
					<a onclick='edit_id(&#34;$list[cat_id]&#34;)' class='layui-btn  layui-btn-xs layui-btn-f60'>编辑</a>&nbsp;
					<a href='".U('cms_article_cat_do_del',array('cat_id'=>$list['cat_id']))."' class='layui-btn layui-btn-del layui-btn-xs' $confirm >删除</a>";
			}
			$category_list[] = $list;
			$article_category_sonlist = $this->getArticleCatList($list['cat_id'],$num);
			foreach($article_category_sonlist as &$sonlist){
				$category_list[] = $sonlist;
			}
		}
		return $category_list;
	}
	/**
	 * @name  cms_article_status()
	 * @desc  文稿更改状态
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-11-17
	 */
	public function cms_article_status(){
		checkAuth(ACTION_NAME);
		$article_add = I("post.");
		$db_article = M("cms_article");
		$article_data = $db_article->save($article_add);
		if ($article_data) {
			showMsg("success", "操作成功！！",U('cms_article_index',array('cat_id'=>15)));
		} else {
			showMsg("error", "操作失败！！");
		}
	}	
}
