<?php
/************************************第一书记、品牌党建***********************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
use Ccp;
class CcpsecretaryController extends BaseController{
 
	/*********************第一书记 ***************************/
	/**
	 * @name:ccp_secretary_index()
	 * @desc：第一书记/品牌党建 
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	public function ccp_secretary_index()
	{
		$secretary_type = I('get.secretary_type');
		if($secretary_type == 1){
			$suffix = '_2';
		}else {
			$suffix = '_1';
		}
		session('suffix',$suffix);
		checkAuth(ACTION_NAME.$suffix);
		$secretary_type = I('get.secretary_type');
		if($secretary_type == 2){
			$sys_config = M('sys_config');
			$config_map['config_code'] = 'secretary_intro';
			$config_value = $sys_config->where($config_map)->field('config_value')->find();
			$this->assign('config_value',$config_value);
		}
		$this->assign('secretary_type',$secretary_type);    //1: 品牌党建  2: 第一书记
		$this->display("Ccpsecretary/ccp_secretary_index");
	}
	/**
	 * @name:ccp_secretary_index_data()
	 * @desc：第一书记/品牌党建 数据
	 * @param：
	 * @return：
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	public function ccp_secretary_index_data(){
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
		$secretary_type = I('get.secretary_type');
		$secretary_list = getSecretaryList('', $secretary_type,$page,$pagesize);
		$num = 1;
		$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		$staff_name_arr = M("hr_staff")->getField('staff_no,staff_name');
		foreach ($secretary_list['data'] as &$list){
			$list['num'] = $num;
			$list['communist_name'] = getCommunistInfo($list['communist_no']);//$communist_name_arr[$list['communist_no']];
			$list['add_time'] = getFormatDate($list['add_time'],'Y-m-d');
			$list['party_name'] = $party_name_arr[$list['communist_party']];
			$list['add_staff'] = $staff_name_arr[$list['add_staff']];
			//$list['operate'] = "<a class='btn btn-xs red btn-outline' href='" . U('ccp_secretary_del', array('secretary_id' => $list['secretary_id'],'secretary_type'=>$secretary_type)) . "'$confirm><i class='fa fa-trash-o'></i> 删除</a>";
			$num++;
		}
		
		ob_clean();$this->ajaxReturn($secretary_list);
	}
	/**
	 * @name:ccp_secretary_edit
	 * @desc：组添加第一书记/品牌党建
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	public function ccp_secretary_edit(){
		checkAuth(ACTION_NAME.session('suffix'));
		$secretary_type = I('get.secretary_type');
		$type = I('get.type');
		if($secretary_type == '2' && $type == '2'){
			$sys_config = M('sys_config');
			$config_value = $sys_config->where("config_code = 'secretary_intro'")->field('config_value')->find();
			$this->assign('type',$type);
			$this->assign('config_value',$config_value);
		}else{
			$party_no = getPartyList('');
			$this->assign('party_no',$party_no);
		}
		$this->assign('secretary_type',$secretary_type);
		$this->display('Ccpsecretary/ccp_secretary_edit');
	}
	/**
	 * @name:ccp_secretary_do_save()
	 * @desc：执行添加
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	public function ccp_secretary_do_save(){
		
		$ccp_secretary = M('ccp_secretary');
		$post = I('post.');
		$secretary_type = $post['secretary_type'];
		$post['status'] = '1';
		$post['add_staff'] = $this->staff_no;
		$post['add_time'] = date('Y-m-d H:i:s');
		$post['update_time'] = date('Y-m-d H:i:s');
		
		$result = $ccp_secretary->add($post);
		if ($result){
			showMsg('success','操作成功',U('ccp_secretary_index',array('secretary_type'=>$secretary_type)),1);
		}else{
			showMsg('error','操作失败');
		}
	}
	/**
	 * @name:ccp_secretary_del
	 * @desc：删除第一书记/品牌党建
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	public function ccp_secretary_del(){
		checkAuth(ACTION_NAME);
		$ccp_secretary = M('ccp_secretary');
		$secretary_id = I('get.secretary_id');
		$secretary_type = I('get.secretary_type');
		$secretary_map['secretary_id'] = $secretary_id;
		$secretary_map['secretary_type'] = $secretary_type;
		$result = $ccp_secretary->where($secretary_map)->delete();
		if ($result){
			showMsg('success','操作成功',U('ccp_secretary_index',array('secretary_type'=>$secretary_type)));
		}else{
			showMsg('error','操作失败');
		}
	}
	/**
	 * @name:ccp_secretary_config_do_save
	 * @desc：第一书记简介
	 * @author：王宗彬
	 * @addtime:2017-10-30
	 * @version：V1.0.0
	 **/
	 public function ccp_secretary_config_do_save(){
	 	$sys_config = M('sys_config');
	 	$com_msg_type = M('com_msg_type');
	 	$data = I('post.');
	 	$data['update_time'] = date('Y-m-d H:i:s');
	 	$data['add_staff'] = session('staff_no');
	 	$_config_save = $sys_config->where("config_code = 'secretary_intro'")->save($data);
	 	$secretary_type = $data['secretary_type'];
	 	if($_config_save){
	 		showMsg('success','操作成功',U('ccp_secretary_index',array('secretary_type'=>$secretary_type)),1);
 		}else{
 			showMsg('error','操作失败');
 		}
	 }
	
}
