<?php
namespace Life\Controller;
use Common\Controller\BaseController;
use Life\Model\LifeBbsPostModel;
class LifebbsController extends BaseController // 继承Controller类
{
	/**
	 * @name  life_bbs_post_index()
	 * @desc  论坛首页
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_index()
	{
		checkAuth(ACTION_NAME);
		$cat_list = M('life_bbs_post_category')->where('status=1')->select();
		$this->assign('cat_list',$cat_list);
		$this->display("Lifebbs/life_bbs_post_index");
	}
	/**
	 * @name  life_bbs_post_index_data()
	 * @desc  论坛首页数据
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_index_data()
	{
		$db_bbs = new LifeBbsPostModel();
		$cat_id = I("get.cat_id");
		if(!empty($cat_id)){
			$where['cat_id'] = $cat_id;
		}
		$pagesize = I('get.limit');
    	$page = (I('get.page')-1)*$pagesize;
		$bbs_data = $db_bbs->getbbsList($where,$page,$pagesize);
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除本次论坛？'" . ')){return false;}"';
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach ($bbs_data['data'] as &$bbs) {
			$bbs_id = $bbs['post_id'];
			
			$bbs['add_time'] = getFormatDate($bbs['add_time'], 'Y-m-d');
			
			$bbs['communist_name'] = $communist_name_arr[$bbs['communist_no']];
			switch ($bbs['status']) {
				case '0':
					$bbs['bbs_status'] = "<span class='fcolor-red'>待审核</span>";
					$status = "<a class='btn btn-xs green btn-outline' onclick='bbs_check($bbs_id)' ><i class='fa fa-edit'></i>审核</a>";
					break;
				case '1':
					$bbs['bbs_status'] = "<span class='fcolor-green'>已通过</span>";
					$status = "";
					break;
				case '2':
					$bbs['bbs_status'] = "<span class='fcolor-red'>已驳回</span>";
					$status = "";
					break;
			}
			$bbs['operate'] = $status."<a class='layui-btn layui-btn-primary layui-btn-xs' href='" . U('life_bbs_post_info', array('post_id' => $bbs['post_id'])). "'><i class='fa fa-info'></i>查看</a>";
		}
		$bbs_data['code'] = 0;
		$bbs_data['msg'] = "获取数据成功";
		ob_clean();$this->ajaxReturn($bbs_data);
	}
	/**
	 * @name  life_bbs_post_table()
	 * @desc  论坛单页
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_table()
	{
		$party_no = I('get.party_no');
		$this->assign("party_no",$party_no);
		$this->display("Lifebbs/life_bbs_post_table");
	}
	/**
	 * @name  life_bbs_post_info()
	 * @desc  论坛详情
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_info()
	{
		checkAuth(ACTION_NAME);
		$bbs_id = I('get.post_id');
		$life_bbs_post = M('life_bbs_post');
		if (! empty($bbs_id)) {
			$bbs_map['post_id'] = $bbs_id;
			$bbs_info = $life_bbs_post->where($bbs_map)->find();
			$data['post_theme'] = $bbs_info['post_theme'];
			$data['add_time'] = $bbs_info['add_time'];
			$data['communist_name'] = getCommunistInfo($bbs_info['communist_no'], 'communist_name');
			$data['post_content'] = $bbs_info['post_content'];
			
			$life_bbs_post_comment = M('life_bbs_post_comment');
			$confirm = 'onclick="if(!confirm(' . "'确认驳回？'" . ')){return false;}"';
			$bbs_comment = $life_bbs_post_comment->where($bbs_map)->select();
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			$communist_avatar_arr = M('ccp_communist')->getField('communist_no,communist_avatar');
			foreach ($bbs_comment as &$comment) {
			    $comment['communist_name'] = $communist_name_arr[$comment['communist_no']];
				$comment['communist_avatar'] = $communist_avatar_arr[$comment['communist_no']];
			    if(!$comment['communist_avatar']){
			    	$comment['communist_avatar'] = '/statics/public/images/default_photo.jpg';
			    }
				$comment_pid = $comment['comment_id'];
				switch ($comment['status']) {
					case '0':
						$comment['bbs_status'] = '待审核';
						$comment['operate'] = "<a class='btn btn-xs  blue btn-outline' href='" . U('life_bbs_post_comment_do_confirm', array(
							'comment_id' => $comment['comment_id']
						)) . "'><i class='fa fa-edit'></i>通过</a>" . "<a class='btn btn-xs btn-xs red btn-outline' href='" . U('life_bbs_post_comment_do_rejected', array(
							'comment_id' => $comment['comment_id']
						)) . "'$confirm><i class='fa fa-trash-o'></i>驳回</a>  ";
						break;
					case '1':
						$comment['bbs_status'] = '已通过';
						break;
					case '2':
						$comment['bbs_status'] = '已驳回';
						break;
				}
			}
			$this->assign("bbs", $data);
			$this->assign("comment", $bbs_comment);
		}
		$this->display("Lifebbs/life_bbs_post_info");
	}
	/**
	 * @name  life_bbs_post_check()
	 * @desc  论坛审核
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_check()
	{
		$db_bbs = new LifeBbsPostModel();
		$bbs_id = I('get.bbs_id');
		$bbs_data = $db_bbs->getbbsInfo($bbs_id);
		$bbs_data['communist_name'] = getCommunistInfo($bbs_data['communist_no'],"communist_name");
		$this->assign("bbs_data",$bbs_data);
		$this->display("Lifebbs/life_bbs_post_check");
	}
	/**
	 * @name  life_bbs_post_check_save()
	 * @desc  论坛审核
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_check_save()
	{
		$db_bbs = new LifeBbsPostModel();
		$post = I('post.');
		$post['post_auth_staff'] = session('staff_no');
		$bbs_data = $db_bbs->updateData($post,"post_id");
		if($bbs_data){
			showMsg('success', '操作成功！！！', U('life_bbs_post_index'),"1");
		}else{
			showMsg('error', '当前论坛审核失败！！！','');
		}
		
	}
	/**
	 * @name  life_bbs_post_do_confirm()
	 * @desc  论坛审核通过状态保存
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_do_confirm()
	{
		checkAuth(ACTION_NAME);
		$bbs_id = I('get.post_id');
		if (!empty($bbs_id)) {
			$life_bbs_post = M('life_bbs_post');
			$life_bbs_post->status = '1';
			$bbs_map['post_id'] = $bbs_id;
			$flag = $life_bbs_post->where($bbs_map)->save();
			if ($flag) {
				$this->success("审核成功！！", U('Cms/life_bbs_post_index'));
			} else {
				$this->success("审核失败！！");
			}
		} else {
			$this->error('论坛编号不能为空！！！');
		}
	}
	/**
	 * @name  life_bbs_post_do_rejected()
	 * @desc  论坛审核驳回
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_do_rejected()
	{
		checkAuth(ACTION_NAME);
		$bbs_id = I('get.post_id');
		if (! empty($bbs_id)) {
			$life_bbs_post = M('life_bbs_post');
			$life_bbs_post->status = '2';
			$bbs_map['post_id'] = $bbs_id;
			$flag = $life_bbs_post->where($bbs_map)->save();
			if ($flag) {
				$this->success("驳回成功！！", U('Cms/life_bbs_post_index'));
			} else {
				$this->success("驳回失败！！");
			}
		} else {
			$this->error('论坛编号不能为空！！！');
		}
	}
	/**
	 * @name  life_bbs_post_comment_do_confirm()
	 * @desc  论坛评论审核保存
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_comment_do_confirm()
	{
		checkAuth(ACTION_NAME);
		$comment_id = I('get.comment_id');
		if (! empty($comment_id)) {
			$life_bbs_post_comment = M('life_bbs_post_comment');
			$life_bbs_post_comment->status = '1';
			$comment_map['comment_id'] = $comment_id;
			$flag = $life_bbs_post_comment->where($comm_map)->save();
			if ($flag) {
				$this->success("审核成功！！");
			} else {
				$this->success("审核失败！！");
			}
		} else {
			$this->error('论坛编号不能为空！！！');
		}
	}
	/**
	 * @name  life_bbs_post_comment_do_rejected()
	 * @desc  论坛评论审核驳回
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_comment_do_rejected()
	{
		checkAuth(ACTION_NAME);
		$comment_id = I('get.comment_id');
		if (! empty($comment_id)) {
			$life_bbs_post_comment = M('life_bbs_post_comment');
			$life_bbs_post_comment->status = '2';
			$comment_map['comment_id'] = $comment_id;
			$flag = $life_bbs_post_comment->where($comment_map)->save();
			if ($flag) {
				$this->success("驳回成功！！");
			} else {
				$this->success("驳回失败！！");
			}
		} else {
			$this->error('论坛编号不能为空！！！');
		}
	}
	/**
	 * @name  life_bbs_post_comment_do_save()
	 * @desc  论坛评论恢复保存
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-27
	 */
	public function life_bbs_post_comment_do_save()
	{
		checkLogin();
		$life_bbs_post_comment = M('life_bbs_post_comment');
		$data['leave_id'] = I('post.leave_id');
		$data['comment_content'] = I('post.comment_content');
		$data['comment_time'] = date("Y-m-d H:i:s");
		$data['communist_no'] = session('staff_no');
		$data['add_staff'] = session('staff_no');
		$data['add_time'] = date("Y-m-d H:i:s");
		$flag = $life_bbs_post_comment->add($data);
		if ($flag) {
			$this->success('回复成功');
		} else {
			$this->success('回复失败');
		}
	}
	/**
	 * @name:life_bbs_post_cat_index
	 * @desc：获取论坛栏目
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	public function life_bbs_post_cat_index(){
		checkAuth(ACTION_NAME);
		$this->display("Lifebbs/life_bbs_post_cat_index");
	}
	/**
	 * @name:life_bbs_post_cat_index_data
	 * @desc：获取论坛栏目
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	public function life_bbs_post_cat_index_data(){
		$cat_list =$this->getLifecatLists(0,-1);
		foreach($cat_list as $list){
			$array[] = $list;
		}
		$array['data'] = $array;
		$array['count'] = 0;
		$array['code'] = 0;
		$array['mag'] = 0;
		ob_clean();$this->ajaxReturn($array);
	}
	/**
	 * @name:life_bbs_post_cat_edit
	 * @desc：论坛栏目添加/编辑
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_bbs_post_cat_edit(){
		checkAuth(ACTION_NAME);
		$life_bbs_cat = M('life_bbs_post_category');
		$cat_list =$this->getLifecatLists(0,-1);
		foreach($cat_list as $list){
			$array[] = $list;
		}
		$this->assign('cat_list',$array);

		if(!empty($_GET['cat_id'])){
			$cat_map['cat_id'] = $_GET['cat_id'];
			$cat_map['status'] = 1;
			$cat_data = $life_bbs_cat->where($cat_map)->find();
			$this->assign('cat_data',$cat_data);
		}
		$this->display("Lifebbs/life_bbs_post_cat_edit");
	}
	
	/**
	 * @name:life_bbs_post_cat_do_save
	 * @desc：论坛栏目保存
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_bbs_post_cat_do_save(){
		$life_bbs_cat = M('life_bbs_post_category');
		$post = $_POST;
		$post['add_staff'] = session('staff_no');
		$post['update_time'] = date('Y-m-d H:i:s');
		$cat_name = $post['cat_name'];
		$cat_name_map['cat_name'] = $cat_name;
		$res = $life_bbs_cat->where($cat_name_map)->select();
		if(empty($res)){
			if(!empty($post['cat_id'])){
				$cat_map['cat_id'] = $post['cat_id'];
				// $cat_map['cat_pid'] = '0';
				$cat_data = $life_bbs_cat->where($cat_map)->save($post);
			}else{
				$post['add_time'] = date('Y-m-d H:i:s');
				$cat_data = $life_bbs_cat->add($post);
			}
			if(!empty($cat_data)){
				showMsg('success', '操作成功！！！', U('life_bbs_post_cat_index'));
			}else{
				showMsg('error', '操作失败！！！','');
			}
		}else{
			showMsg('error', '类型名称已存在！！！','');
		}
	}
	/**
	 * @name:life_bbs_post_cat_do_del
	 * @desc：论坛栏目删除
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-15
	 * @version：V1.0.0
	 **/
	public function life_bbs_post_cat_do_del(){
		checkAuth(ACTION_NAME);
		$cat_id = $_GET['cat_id'];
		$cat_map['cat_id'] = $cat_id;
		$res = M('life_bbs_post_category')->where($cat_map)->delete();
		M('life_bbs_post')->where("cat_id = '$cat_id'")->delete();
		if($res){
			showMsg('success', '操作成功！！！', U('life_bbs_post_cat_index'));
		}else{
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name:getLifecatLists
	 * @desc：获取栏目列表数据
	 * @param：$cat_pid(父级no) $num(制表符数量)
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-12-14
	 * @version：V1.0.0
	 **/
	function getLifecatLists($cat_pid = 0,$num = -1){
		$life_bbs_cat = M('life_bbs_post_category');
		$cat_map['cat_pid'] = $cat_pid;
		$cat_list = $life_bbs_cat->where($cat_map)->select();
		$category_list = array();
		$symbol = "├─";
		$tabs = "";
		for($i = 0;$i <= $num; $i++){
			$tabs .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$tabs .= $symbol;
		$num++;
		
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($cat_list as &$cat) {
			$cat['partcate'] = $tabs.$cat['cat_name'];
			if (!empty($cat['add_staff'])){
			    $cat['add_staff'] = $staff_name_arr[$cat['add_staff']];
			}
			$cat['operate'] = "<a class='layui-btn layui-btn-xs layui-btn-f60' href='" . U('Lifebbs/life_bbs_post_cat_edit', array(
					'cat_id' => $cat['cat_id']
			)) . "'><i class='fa fa-edit'></i> 编辑</a>  " . "<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('Lifebbs/life_bbs_post_cat_do_del', array(
					'cat_id' => $cat['cat_id']
			)) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>  ";
			$category_list[] = $cat;
			$article_category_sonlist =$this-> getLifecatLists($cat['cat_id'],$num);
			foreach($article_category_sonlist as &$sonlist){
				$category_list[] = $sonlist;
			}
		}
		return $category_list;
	}
}
