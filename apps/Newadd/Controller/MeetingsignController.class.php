<?php
namespace Newadd\Controller;//命名空间
use Think\Controller;
use Common\Controller\BaseController;

class MeetingsignController extends BaseController//继承Controller类
{
	//党费缴纳
	public function meeting_sign_index(){
		checkAuth(ACTION_NAME);//判断越权
		 $party_no_auth = session('party_no_auth');//取本级及下级组织
		$this->assign('party_no_auth',$party_no_auth);
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
		$party_options = $this->getMeetingType();
		$this->assign('party_options',$party_options);

		$this->display("Meetingsign/meeting_sign_index");

	}
	public function meeting_sign_index_data(){
		
        $staff_no = session('staff_nmeeting_typeo');
        $communist_name = I('get.communist_name');
        $meeting_type = I('get.meeting_type'); 
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
     
        $data = getMeetingSignList($communist_name,$meeting_type,$page,$pagesize);
		
        $data['code'] = 0;
        $data['msg'] = 0;
        ob_clean();$this->ajaxReturn($data); // 返回json格式数据
   
	}
	
		//添加或者减少积分
		public function meeting_sign_edit(){
		
       	
		//获取党组织列表
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
       // print_r($party_list);
		$this->assign('party_list',$party_list);
		
		//获取相关的数据
		$party_options = $this->getMeetingType();
		$this->assign('party_options',$party_options);
		
		
		$this->display("Meetingsign/meeting_sign_edit");
       
   
		}
		
		//保存积分
		public function meeting_sign_save(){
		
			$db_dues=M("meeting_sign");
			$data=I('post.');
			//print_r($data);
			//exit;
			$data['add_staff']=session('staff_no');//添加人
			$data['add_staff_name']=M('hr_staff')->where("staff_no = ".$data['add_staff'])->getField('staff_name');//添加人姓名
		   // $data['party_no']=getCommunistInfo($data['communist_no'],'party_no');//缴费人部门
			$data['party_name']=getPartyInfo($data['party_no'],'party_name');//缴费人部门名称
			$data['create_time']=date("Y-m-d H:i:s");
			$data['update_time']=date("Y-m-d H:i:s");
		   
		   //根据人员id,获取人员的相关数据
		   $communist = getCommunistInfo($data['meeting_sign_name_id'],'communist_name,communist_integral');//获取人员的相关数据
			$data['meeting_sign_name']=$communist['communist_name'];
			
			
			
			$dues_res=$db_dues->add($data);
			
			//更新积分
			$score = $data['meeting_sign_score']+$communist['communist_integral'];
			$update_score['communist_integral'] =  $score;
			
			$res = M("ccp_communist")->where("communist_no = {$data['meeting_sign_name_id']}")->save($update_score);
			
			//添加日志
			saveIntegralLog(2,$data['meeting_sign_name_id'],$score,7,$data['meeting_sign_bakup'],$cause);
			if ($dues_res){
				showMsg('success', '操作成功', '', 1);
			}else{
				showMsg('error', '数据有误，操作失败！');
			}
		   
   
		}
		
		//删除积分
		public function meeting_sign_del(){
		
       
       
   
		}
	
	
	//根据ajax 获取人员列表的数据
	public function communist_no_list_ajax(){
		$db_dues=M("ccp_dues");
        $data=I('post.');
		//print_r($data);
		if($data['party_no'] == ''){
			return false;
		}
		$getCommunistSelect = getCommunistSelect('',$data['party_no']);
		//print_r($getCommunistSelect);
		ob_clean();$this->ajaxReturn($getCommunistSelect); 
		
	}
	
	//获取分类
	public function getMeetingType(){
		//获取分类
		$type_arr = M("meeting_sign_type")->where("is_deleted=1")->select();
		//print_r($type_arr);
		$party_options = '';
		foreach($type_arr as $party){
			$party_options.="<option $selected data-type='".$party['meeting_sign_type_id']."' value='".$party['meeting_sign_type_id']."'>".$party['meeting_sign_type']."</option>";
		}
		
		return $party_options;
	}
	
}

