<?php
/*******************************************学习专题管理***************************************************** */
namespace Edu\Controller;
use Common\Controller\BaseController;
class EdutopicController extends BaseController{
	/**
	 * @name  edu_topic_index()
	 * @desc  学习专题首页
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime  
	 * @addtime   2017-10-11
	 */
	public function edu_topic_index()
	{
		checkAuth(ACTION_NAME);
		$this->display('Edutopic/edu_topic_index');
	}
    
	/**
	 * @name  edu_topic_index_data()
	 * @desc  专题首页数据加载
	 * @param
	 * @return
	 * @author 杨凯  王宗彬   编辑  分割是时间
	 * @version 版本 V1.0.0
	 * @updatetime  
	 * @addtime   2017-10-11
	 */
	public function edu_topic_index_data(){
		$db_edutopic=M('edu_topic');
		$page = I('get.page');
		$pagesize = I('get.limit');
		$page = ($page-1)*$pagesize;
		$keyword = I('get.keyword');
		//$is_hunt = I('get.time');
		
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		//$topic_content = I('get.topic_content');//关键字
	 	if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $topic_map['update_time']  = array('between',array($start,$end));
        }
        if(!empty($keyword)){
        	$topic_map['topic_title']  = array('like','%'.$keyword.'%');
        }
        // dump($topic_map);die;
		$communist_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		$edutopic_list['data'] = $db_edutopic->where($topic_map)->order('add_time desc')->limit($page,$pagesize)->select();
		// dump(M()->getLastSql());die;
		$edutopic_list['count'] = $db_edutopic->where($topic_map)->count();
		$confirma = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
		$confirmb = 'onclick="if(!confirm(' . "'确认启用？'" . ')){return false;}"';
		//$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		if(!empty($edutopic_list['data'] && $edutopic_list['data'] != 'null')){
			foreach ($edutopic_list['data'] as &$list) {
				$topic_id=$list['topic_id'];
			    $list['add_staff_name'] = $staff_name_arr[$list['add_staff']];
			  	$list['add_staff'] = $communist_name_arr[$list['add_staff']];
				$list['update_time'] = getFormatDate($list['update_time'], 'Y-m-d');
				if ($list['status']==1) {
					$list['status'] = '可用';
					$list['operate'] = "<a onclick='edit(".$list['topic_id'].")' class='layui-btn layui-btn-xs layui-btn-f60' ><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_topic_status', array('topic_id' => $list['topic_id'])) . "' $confirma class='btn btn-xs red btn-outline'><i class='fa fa-edit'></i> 停用</a>";
				}else {
					$list['status'] = '停用';
					$list['operate'] = "<a onclick='edit(".$list['topic_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60'><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_topic_status', array('topic_id' => $list['topic_id'])) . "' $confirmb class='btn btn-xs green btn-outline'><i class='fa fa-edit'></i> 启用</a>";
				}
			}
			$edutopic_list['code'] = 0;
			$edutopic_list['msg'] = '获取数据成功';
			ob_clean();$this->ajaxReturn($edutopic_list);
		}else{
			$edutopic_list['code'] = 0;
			$edutopic_list['msg'] = '获取数据失败';
			ob_clean();$this->ajaxReturn($edutopic_list);
		}
	}

	/**
	 * 获取专题首页数据添加/编辑
	 * @name edu_topic_edit
 	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime  
	 * @addtime   2017-10-11
	 */
	public function edu_topic_edit(){
 		checkAuth(ACTION_NAME);
 		$db_edutopic=M('edu_topic');
 		$topic_id = I('get.topic_id');
 		if (!empty($topic_id)) {
 			$topic_map['topic_id'] = $topic_id;
 			$topic_edit = $db_edutopic->where($topic_map)->find();
 		}
		$this->assign('topic_edit',$topic_edit);

 		$this->display("Edutopic/edu_topic_edit");
	}
	/**
	 * 专题数据添加更改
	 * @name edu_topic_save
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-11
	 */
	public function edu_topic_save(){
		$post = I('post.');
		checkLogin();
		$staff_no = session('staff_no');
		$db_topic = M('edu_topic');
		$topic_id = I('post.topic_id');
		$topic_data['update_time'] = date("Y-m-d H:i:s");
		if (! empty($topic_id)) // 编辑
		{
			$topic_data['topic_id'] = $topic_id;
			$topic_data['topic_title'] = $_POST['topic_title'];
// 			$topic_data['status'] = $_POST['topic_status'];
			$topic_data['memo'] = I('post.memo');
			$topic_data['topic_img'] = I('post.topic_img');
			$topic_not = $db_topic->save($topic_data);
		} else // 添加
		{
// 			$topic_data['status'] = $_POST['topic_status'];
			$topic_data['topic_title'] = $_POST['topic_title'];
			$topic_data['add_staff'] = $staff_no;
			$topic_data['add_time'] = date("Y-m-d H:i:s");
			$topic_data['memo'] = I('post.memo');
			$topic_data['topic_img'] = I('post.topic_img');
			$topic_not = $db_topic->add($topic_data);
		}
		if ($topic_not) {
			showMsg('success', '操作成功！！！', U('edu_topic_index'),1);
		}else{
			showMsg('error', '操作失败！！！', '');
		}
	}
	/**
	 * 专题数据状态更改
	 * @name edu_topic_status
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-11
	 */
	public function edu_topic_status(){
		checkAuth(ACTION_NAME);
		$staff_no = session('staff_no');
		$db_topic=M('edu_topic');
		$topic_id = I('get.topic_id');
		if (!empty($topic_id)) {
			$topic_map['topic_id'] = $topic_id;
			$topic_edit = $db_topic->where($topic_map)->find();
			if ($topic_edit['status']==1) {
				$topic_data['status'] = 0;
			}else {
				$topic_data['status'] = 1;
			}
			$topic_data['topic_id'] = $topic_id;
			$topic_data['update_time'] = date("Y-m-d H:i:s");
			$save_not = $db_topic->save($topic_data);
			showMsg('success', '操作成功！！！', U('edu_topic_index'));
		}else {
			showMsg('error', '操作失败！！！', '');
		}
	}
	/*******************************************对应群体管理***************************************************** */
	/**
	 * 对应群体首页列表
	 * @name edu_group_index
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_index(){
		$group_type = I('get.group_type');
		$this->assign('group_type',$group_type);
		switch ($group_type) {
			case 1:
				$group_type = '_1';
				break;
			case 2:
				$group_type = '_2';
				break;
		}
		session('group_type',$group_type);
		checkAuth(ACTION_NAME.$group_type);
 		$this->display("Edutopic/edu_group_index");
	}
	/**
	 * 对应群体数据
	 * @name edu_group_index_data
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_index_data(){
		$db_groupdata=M('edu_groupdata');
		$where['group_type'] = I('get.group_type');
		$page = I('get.page');
		$pagesize = I('get.limit');
		$page = ($page-1)*$pagesize;
		$keyword = I('get.keyword');
		//$is_hunt = I('get.time');		
		$str = I('get.start');
		$strt = strToArr($str, ' - ');  //分割时间
		$start = $strt[0];
		$end = $strt[1];
		//$topic_content = I('get.topic_content');//关键字
	 	if(!empty($start) && !empty($end)){
            $start=$start." 00:00:00";
            $end=$end." 23:59:59";
            $where['update_time']  = array('between',array($start,$end));
        }
        if(!empty($keyword)){
        	$where['group_title']  = array('like','%'.$keyword.'%');
        }
		$groupdata_list['data'] = $db_groupdata->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
		$groupdata_list['count'] = $db_groupdata->where($where)->count();
		$confirmdel = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$confirma = 'onclick="if(!confirm(' . "'确认停用？'" . ')){return false;}"';
		$confirmb = 'onclick="if(!confirm(' . "'确认启用？'" . ')){return false;}"';
		// $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		if(!empty($groupdata_list['data'] && $groupdata_list['data'] != 'null')){
			foreach ($groupdata_list['data'] as &$list) {
				$group_id=$list['group_id'];
			  	$list['add_staff'] = getStaffInfo($list['add_staff']);
				$list['update_time'] = getFormatDate($list['update_time'], 'Y-m-d');
				if ($list['status']==1) {
					$list['status'] = '可用';
					$list['operate'] = "<a onclick='edit(".$list['group_id'].")' class='layui-btn layui-btn-xs layui-btn-f60' ><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_group_status', array('group_id' => $list['group_id'],'group_type' => $where['group_type'])) . "' $confirma class='btn btn-xs red btn-outline'><i class='fa fa-edit'></i> 停用</a>&nbsp;&nbsp;<a href='" . U('edu_group_del', array('group_id' => $list['group_id'],'group_type' => $where['group_type'])) . "' $confirmdel class='layui-btn layui-btn-del layui-btn-xs'><i class='fa fa-edit'></i> 删除</a>";
				}else {
					$list['status'] = '停用';
					$list['operate'] = "<a onclick='edit(".$list['group_id'].")' class='layui-btn  layui-btn-xs layui-btn-f60'  ><i class='fa fa-edit'></i>编辑</a>&nbsp;&nbsp;<a href='" . U('edu_group_status', array('group_id' => $list['group_id'],'group_type' => $where['group_type'])) . "' $confirmb class='btn btn-xs green btn-outline'><i class='fa fa-edit'></i> 启用</a>&nbsp;&nbsp;<a href='" . U('edu_group_del', array('group_id' => $list['group_id'],'group_type' => $where['group_type'])) . "' $confirmdel class='layui-btn layui-btn-del layui-btn-xs'><i class='fa fa-edit'></i> 删除</a>";
				}
			}
			$groupdata_list['code'] = 0;
			$groupdata_list['msg'] = '获取数据成功';
			ob_clean();$this->ajaxReturn($groupdata_list);
		}else{
			$groupdata_list['code'] = 0;
			$groupdata_list['msg'] = '获取数据失败';
			ob_clean();$this->ajaxReturn($groupdata_list);
		}
	}
	/**
	 * 对应群体添加编辑
	 * @name edu_group_edit
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_edit(){
		checkAuth(ACTION_NAME.session('group_type'));
		$group_type = I('get.group_type');
		$this->assign('group_type',$group_type);
		$group_id = I('get.group_id');
		$db_groupdata=M('edu_groupdata');
		if($group_id){
			$where['group_id'] = $group_id;
			$groupdata = $db_groupdata->where($where)->find();
			$this->assign('groupdata',$groupdata);
		}
 		$this->display("Edutopic/edu_group_edit");
	}
	/**
	 * 对应群体添加编辑
	 * @name edu_group_save
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_save(){
		$post = I('post.');
		$db_groupdata=M('edu_groupdata');
		if($post['group_id']){
			$post['update_time'] = date('Y-m-d H:i:s');
			$groupdata = $db_groupdata->save($post);
		}else{
			$post['update_time'] = date('Y-m-d H:i:s');
			$post['add_time'] = date('Y-m-d H:i:s');
			$post['add_staff'] = session('staff_no');
			$groupdata = $db_groupdata->add($post);
		}
		if($groupdata) {
			showMsg('success', '操作成功！！！', U('edu_group_index',array('group_type'=>$post['group_type'])),1);
		}else {
			showMsg('error', '操作失败！！！', '');
		}
	}
	/**
	 * 对应群体关闭开启
	 * @name edu_group_status
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_status(){
		checkAuth(ACTION_NAME.session('group_type'));
		$group_type = I('get.group_type');
		$group_id = I('get.group_id');
		$db_groupdata=M('edu_groupdata');
		$where['group_id'] = $group_id;
		$groupdata = $db_groupdata->where($where)->find();
		$get['group_id'] = $group_id;
		if($groupdata['status']==1){
			$get['status'] = 0;
		}else{
			$get['status'] = 1;
		}
		$data = $db_groupdata->save($get);
		if($data) {
			showMsg('success', '操作成功！！！', U('edu_group_index',array("group_type"=>$group_type)));
		}else {
			showMsg('error', '操作失败！！！', '');
		}
	}
	/**
	 * 对应群体关闭开启
	 * @name edu_group_del
	 * @param
	 * @return
	 * @author 刘长军
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2019-03-28
	 */
	public function edu_group_del(){
		checkAuth(ACTION_NAME.session('group_type'));
		$group_type = I('get.group_type');
		$where['group_id'] = I('get.group_id');
		$db_groupdata=M('edu_groupdata');
		$groupdata = $db_groupdata->where($where)->delete();
		if($groupdata) {
			showMsg('success', '操作成功！！！', U('edu_group_index',array("group_type"=>$group_type)));
		}else {
			showMsg('error', '操作失败！！！', '');
		}

	}
}
