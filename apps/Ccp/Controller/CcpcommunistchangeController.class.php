<?php
/*******************************党员关系转移、流动党员************************************/
namespace Ccp\Controller;
use Common\Controller\BaseController;
use Ccp\Model\CcpCommunistChangeModel;
class CcpcommunistchangeController extends BaseController{
    /**
     * @name:ccp_communist_change_index
     * @desc：党员关系转移
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_index()
    {
        $type = I("get.type",'change_out');
        switch ($type) {
            case 'change_out': $suffix='_1';break;
            case 'change_join': $suffix='_2';break;
            default: $suffix='_1';break;
        }
        session('suffix',$suffix);
        $this->assign('suffix',$suffix);
        checkAuth(ACTION_NAME.$suffix);
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	$this->assign('party_list',$party_list);
    	$this->assign('type',$type);
    	$this->display("Ccpcommunistchange/ccp_communist_change_index");
    }
    /**
     * @name:ccp_communist_change_index_data
     * @desc：党员关系转移数据
     * @param：
     * @return：
     * @author： 王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_index_data()
    {
        $type = I("get.type");
        $where = "1=1";
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
        if(empty($_GET['party_no'])){
            $party_nos = session('party_no_auth');
        }else{
            $party_no = I('get.party_no');
            $party_nos = getPartyChildNos($party_no,'str');
        }
        if($type == "change_out"){
            $where .= " and old_party in ($party_nos) and (change_type = 1 or change_type = 2)" ;  //系统内系统外
        }elseif($type == "change_join"){
            $where .= " and new_party in ($party_nos) and change_audit_status > 10 and change_type = 1" ;  //系统内,
        }
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $communist_change = new CcpCommunistChangeModel();
        $communist_change_data = $communist_change->get_communist_change_list($type,$where,$page,$pagesize);
        $communist_mobile_arr = M('ccp_communist')->getField('communist_no,communist_mobile');
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $status_map['status_group'] = 'change_audit_status';
        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        $type_map['type_group'] = 'change_type';
        $type_name_arr = M('bd_type')->where($type_map)->getField('type_no,type_name');
        foreach ($communist_change_data['data'] as &$communist){
            $change_id = $communist['change_id'];
            $communist['change_id'] = "<a class='fcolor-22' href='".U('Ccpcommunistchange/ccp_communist_change_info',array('change_id'=>$change_id,'type'=>$type))."' >".$change_id."</a>";
            $communist['communist_name'] = "<a class='fcolor-22' href='".U('Ccpcommunistchange/ccp_communist_change_info',array('change_id'=>$change_id,'type'=>$type))."' >".getCommunistInfo($communist['communist_no'],"communist_name")."</a>";
            $communist['operate'] = "<a class=' layui-btn layui-btn-primary layui-btn-xs' href='".U('Ccpcommunistchange/ccp_communist_change_info',array('change_id'=>$change_id,'type'=>$type))."' >查看</a>"."<a class='layui-btn layui-btn-del layui-btn-xs' href='".U('Ccpcommunistchange/ccp_communist_change_del',array('change_id'=>$change_id,'type'=>$type))."' $confirm>删除 </a>";
            $communist['change_audit_status'] = "<font color='" . $status_color_arr [$communist['change_audit_status']] . "'>" . $status_name_arr [$communist['change_audit_status']] . "</font> ";
            $communist['communist_mobile'] =  $communist_mobile_arr[$communist['communist_no']];
            $communist['old_party'] = $party_name_arr[$communist['old_party']];
            $communist['update_time'] = getFormatDate($communist['update_time'],"Y-m-d");
            if($party_name_arr[$communist['new_party']]){
                $communist['new_party'] = $party_name_arr[$communist['new_party']];
            }
            $communist['change_type'] = $type_name_arr[$communist['change_type']];
        }
        
        ob_clean();$this->ajaxReturn($communist_change_data);
    }
    /**
     * @name:ccp_communist_change_edit
     * @desc：新增党员关系转出
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_edit()
    {
    	$status = I('get.status');
    	$party_no = I('get.party_no');
    	$this->assign('status',$status);
    	$communist_no = I('get.communist_no');
    	$type = I('get.type');
    	if(!empty($communist_no)){
    		$communist_info = getcommunistInfo($communist_no,"all");
            $communist_info['communist_party_name']=getPartyInfo($communist_info['party_no']);//部门
            $communist_info['communist_post_name']=getPartydutyInfo($communist_info['communist_post_no']);//岗位
            $this->assign("communist_info",$communist_info);
    	}
    	$industry = trim(getConfig('industry'));
    	$this->assign('industry', $industry);
   		$communist_list=getCommunistList($party_no,'arr','1','','21','',$communist_no);
   		$this->assign("communist_list",$communist_list);
    	$this->assign("type",$type);
    	
    	$this->display("Ccpcommunistchange/ccp_communist_change_edit");
    }
    /**
     * @name:ccp_communist_change_save
     * @desc：党员关系转移数据保存方法
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_save(){
    	$post = I("post.");
    	$type = $post['type'];
    	$post['change_audit_status'] = '10';
    	$post['status'] = 1;
    	$post['add_time'] = date('Y-m-d H:i:s');
    	$post['update_time'] = date('Y-m-d H:i:s');
    	$post['start_time'] = date('Y-m-d H:i:s');
    	$db_change = M("ccp_communist_change");
    	//$alert_man=getAuthCommunistNos('1003012','is_check');
    	if(empty($post['change_id'])){
            $post['add_staff'] = $this->staff_no;
    	    $change_res = $db_change->add($post);
    	    $alert_man=getAuthCommunistNos($post['old_party'],'is_check');
    	    $alert_url='Ccp/Ccpcommunistchange/ccp_communist_change_info/change_id/'.$change_res.'/type/'.$type;
    	    $alert_title=getCommunistInfo($post['communist_no']).'党员的转移信息待您审核！';
    	    saveAlertMsg(12, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
    	}else{
            $post['update_time'] = date('Y-m-d H:i:s');
    	    $change_res = $db_change->save($post);
    	}
    	if ($change_res) {
    	    //20171202  提醒部门负责人与有审核权限的人
    		showMsg('success', '操作成功！！！',  U('Ccpcommunistchange/ccp_communist_change_index',array('type'=>$type)),1);
    	} else {
    		showMsg('error', '操作失败！！！','');
    	}
    }
    /**
     * @name:ccp_communist_change_check
     * @desc：党员关系转移审核
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_check()
    {
    	$post = I("post.");		
    	$type = $post['type'];
    	$change_model =  D('CcpCommunistChange');	//调用人员转移model层
    	$res['change_id'] = $post['change_id'];  //ID
    	$res['check_staff'] = session('staff_no');   //审核人
    	$res['add_time'] = date('Y-m-d H:i:s');  //审核时间
    	$res['check_content'] = $post['change_audit_content'];  //审核内容
   		$res['log_audit_man'] = $post['log_audit_man'];   //经办人
   		$res['log_contact_tel'] = $post['log_contact_tel']; //联系方式
   		$res['log_attach'] = $post['log_attach'];  //附件
   		/**每次变更状态提醒要审核的人**/
   		$alert_title=getCommunistInfo($post['communist_no']).'党员的流动信息待您审核！';
   		/****/
   		if(empty($post['change_audit_content'])){  //没有内容填写同意
    		$res['check_content'] = "同意~";
   		}
   		$post['change_audit_status'] = $post['change_audit_status']+10;//  改变状态
   		if(!empty($post['reject'])){  //驳回
   			$post['change_audit_status'] = $post['reject'];
    		if(empty($post['change_audit_content'])){ //没有内容填写驳回
    			$res['check_content'] = "拒绝~";
    		}
   		}else{
    		if($post['change_audit_status'] == 20 && $post['change'] == 2){  //2 系统外  20待接受     没有待接受把待接受状态改为待复核
   				$post['change_audit_status'] = $post['change_audit_status']+10;
   				$alert_url='Ccp/Ccpcommunistchange/ccp_communist_change_info/change_id/'.$res['change_id'].'/type/change_out';
   				$alert_man=getAuthCommunistNos($post['old_party'],'is_check');
   				saveAlertMsg(12, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
    		}
   		}
   		if($post['change'] == 1 && $post['change_audit_status'] == 20 ){  //添加提醒
   			$alert_url='Ccp/Ccpcommunistchange/ccp_communist_change_info/change_id/'.$res['change_id'].'/type/change_join';
   			$alert_man=getAuthCommunistNos($post['new_party'],'is_check');  
   			saveAlertMsg(12, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
   			
   		}else if($post['change'] == 1 && $post['change_audit_status'] == 30 ){  //待复核
   			$alert_url='Ccp/Ccpcommunistchange/ccp_communist_change_info/change_id/'.$res['change_id'].'/type/change_out';
   			$alert_man=getAuthCommunistNos($post['old_party'],'is_check');
   			saveAlertMsg(12, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('communist_no'));
   		}
   		
    	if($post['change'] == 1 && $post['change_audit_status'] == 40 ){  // 1 系统内 40 完成  转移成功修改党员信息
    		$data['party_no'] = $post['party_no'];
    		$communist_no = $post['communist_no'];
            $comm_map['communist_no'] = $communist_no;
    		M('ccp_communist')->where($comm_map)->save($data);
    	}else if($post['change'] == 2 && $post['change_audit_status'] == 40 ){ //2 系统外 40 完成  转移成功修改党员信息
    		$data['status'] = 31;    //系统外转出
    		$communist_no = $post['communist_no'];
            $comm_map['communist_no'] = $communist_no;
    		M('ccp_communist')->where($comm_map)->save($data);
    	}
    	$communist_change = $change_model->updateData($post,change_id);
    	$communist_change_log = M('ccp_communist_change_log')->add($res);
    	if(($post['change'] == 1 || $post['change'] == 2) && $post['change_audit_status'] == 40){//转移成功
    		if((getPartyInfo($post['new_party'],"party_name"))){
    			$post['new'] = getPartyInfo($post['new_party'],"party_name");
    		}else{
				$post['new'] = $post['new_party'];
			}
    		saveCommunistLog($post['communist_no'],11,'',session('communist_no'),getPartyInfo($post['old_party']),$post['new']);
    	}
    	if ($communist_change && $communist_change_log) {
    		showMsg('success', '操作成功！！！',  U('Ccpcommunistchange/ccp_communist_change_info',array('change_id'=>$post['change_id'],'type'=>$type)));
    	}
    }
    
    /**
     * @name:ccp_communist_change_info
     * @desc：党员关系转移信息详情
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_change_info()
    {
        if(!session('suffix')){
            $type = I("get.type",'change_out');
            switch ($type) {
                case 'change_out': $suffix='_1';break;
                case 'change_join': $suffix='_2';break;
                default: $suffix='_1';break;
            }
            session('suffix',$suffix);
        }
        checkAuth(ACTION_NAME.session('suffix'));
        $change_id = I("get.change_id");
        $type = I("get.type");
        $this->assign("type",$type);
        $change_model = D('CcpCommunistChange');	//调用人员转移model层
        $communist_change = $change_model->get_communist_change_info($change_id);
        $communist_change['old'] = $communist_change['old_party'];
        $communist_change['new'] = $communist_change['new_party'];
        $communist_change['old_party'] = getPartyInfo($communist_change['old_party'],"party_name");
        $communist_change['party_no'] = $communist_change['new_party'];
        $communist_change['new_party'] = getPartyInfo($communist_change['new_party'],"party_name");
        $communist_change['change'] = $communist_change['change_type'];
        $communist_change['join'] = $communist_change['change'];
        $communist_change['change_type'] = getBdTypeInfo($communist_change['change'],"change_type");
        $this->assign("communist_change",$communist_change);
        //查询转移的党员信息
        $communist_data = getCommunistInfo($communist_change['communist_no'],'all');
        $staff_no = session('staff_no');
        $old_party = $communist_change['old'];
        $new_party = $communist_change['new'];
        $old_party_map['party_no'] = $old_party;
        $old_party_map['_string'] = "(FIND_IN_SET('$staff_no',party_manager))";
        $old = M('ccp_party')->where($old_party_map)->find();
        $new_party_map['party_no'] = $new_party;
        $new_party_map['_string'] = "(FIND_IN_SET('$staff_no',party_manager))";
        $new = M('ccp_party')->where($new_party_map)->find();
        $check = checkDataAuth($staff_no,'is_check');
        $this->assign("old",$old);
        $this->assign("new",$new);
        $this->assign("check",$check);
        if(($communist_change['change_audit_status'] == 30 && $communist_change['join'] == 1 ) || ($communist_change['change_audit_status'] == 20 && $communist_change['join'] == 2)){//待复核 为党委办公室审核
        	$reveal = checkDataAuth($staff_no, 'is_one');  //是否有党委办公室审核权限
        	$reveal = $reveal?'1':'';
        }else{
        	if(($old ||$check) && ($communist_change['change_audit_status'] == 10 || $communist_change['change_audit_status'] == 30) && $type == change_out){
        		$reveal = 1;
        	}
        	if(($new || $check) && $communist_change['change_audit_status'] == 20 && $type == change_join){
        		$reveal = 1;
        	}
        }       
        $this->assign("reveal",$reveal);
        $this->assign("communist_data",$communist_data);
        $change_id = $communist_change['change_id'];
        $change_map['change_id'] = $change_id;
        $res = M('ccp_communist_change_log')->where($change_map)->select();
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        foreach ($res as &$list){
            $list['check_staff'] = $staff_name_arr[$list['check_staff']];
        }
        $this->assign('res',$res);
        $this->display("Ccpcommunistchange/ccp_communist_change_info");
    }
   
    /**
     * @name:ccp_communist_change_del
     * @desc： 党员转入/转出(流入/流出)删除
     * @param：
     * @return：
     * @author：王宗彬  
     * @addtime:2017-11-08
     * @version：V1.0.0
     **/
    public function ccp_communist_change_del()
    {
        checkAuth(ACTION_NAME);
    	$change_id = I('get.change_id');
    	$type = I('get.type');
        $change_map['change_id'] = $change_id;
    	$data = M('ccp_communist_change')->where($change_map)->delete();
    	if($data){
            $change_log_map['change_id'] = $change_id;
    		M('ccp_communist_change_log')->where($change_log_map)->delete();
    		showMsg('success', '操作成功！！！',  U('Ccpcommunistchange/ccp_communist_change_index',array('type'=>$type)));
    	} else {
    		showMsg('error', '操作失败！！！','');
    	}
    }
    /**
     * @name: ccp_communist_transfer_leave_info
     * @desc：回执单
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-07
     * @version：V1.0.0
     **/
    public function ccp_communist_transfer_leave_info()
    {
    	$communist_no = $_GET['communist_no'];
    	$change_id = $_GET['change_id'];  
    	$change_map['change_id'] = $change_id;
    	$data = M('ccp_communist_change')->join("sp_ccp_communist on sp_ccp_communist_change.communist_no = sp_ccp_communist.communist_no")->where($change_map)->find();
    	$num = getConfig('ccp_change_receipt'); //编号
    	if($num){
    		$data['num'] = date('Y').''.$num;   //编号
    		$num++;
    		$recruit_list['config_value'] = str_pad($num,4,"0",STR_PAD_LEFT);
            $config_map['config_code'] = 'ccp_change_receipt';
    		$res = M('sys_config')->where($config_map)->save($recruit_list);//获取回执联序列号
    	}
    	$data['communist_nation'] = getBdCodeInfo( $data['communist_nation'],'communist_nation_code');
    	$data['communist_sex'] = $data['communist_sex']?'男':'女';
    	$data['old_party'] = getPartyInfo($data['old_party']);
    	$new_party = getPartyInfo($data['new_party']);
    	if($new_party){
    		$data['new_party'] = $new_party;
    	}
    	$data['year'] = date('Y');  //年
    	$data['month'] = date('m'); //月
    	$data['day'] = date('d');   //日
    	$this->assign('data',$data);
    	$this->display("Ccpcommunistchange/ccp_communist_transfer_leave_info");
    }
    /**
     * @name: ccp_communist_history_index
     * @desc：历史党员
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-14
     * @version：V1.0.0
     **/
    public function ccp_communist_history_index(){
    	checkAuth(ACTION_NAME);
    	$status_list=getStatusList('communist_status',COMMUNIST_STATUS_HISTORY);
    	$this->assign('status_list', $status_list);
    	$this->display("Ccpcommunistchange/ccp_communist_history_index");
    }
    
    /**
     * @name: ccp_communist_history_index_data
     * @desc：历史党员数据
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-14
     * @version：V1.0.0
     **/
    public function ccp_communist_history_index_data(){
    	$status = I('get.status');
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
        $status = !empty($status)? $status : COMMUNIST_STATUS_HISTORY;
    	$communist_name = I('get.communist_name','');
    	$party_no = I('get.party_no','');
        if(!empty($party_no)){
            $party_nos_arr=getPartyChildMulNos($party_no,'arr');
            $map['party_no']=array('in',$party_nos_arr);
        }else{
			$map['party_no']=array('in',session('party_no_auth'));
		}
        if(!empty($status)){
            $status_arr=strToArr($status);
            $map['status']=array('in',$status_arr);
        }
        if(!empty($communist_name)){
            $map['communist_name'] = array('like',"%$communist_name%");
        }
        $communist_list['data'] = M('ccp_communist')->where($map)->limit($page,$pagesize)->order('update_time desc')->select();
        $communist_list['count'] = M('ccp_communist')->where($map)->count();
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $code_map['code_group'] = 'diploma_level_code';
        $code_name_arr = M('bd_code')->where($code_map)->getField('code_no,code_name');
        $status_map['status_group'] = 'communist_status';
        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
    	foreach ($communist_list['data'] as &$communist) {
            $communist['party_no'] = $party_name_arr[$communist['party_no']];
            $communist['post_no'] = getPartydutyInfo($communist['post_no']);
            $communist['communist_age']=getCommunistAge($communist['communist_birthday']);
            if (!empty($communist['communist_avatar'])){
                $communist['communist_avatar'] = __ROOT__.'/'.$communist['communist_avatar'];
            }
            $communist['communist_diploma'] = $code_name_arr[$communist['communist_diploma']];
            $communist['communiststaus'] = "<font color='" . $status_color_arr[$communist['status']] . "'>" . $status_name_arr[$communist['status']] . "</font> ";
            $communist['communist_sex'] = $communist['communist_sex'] == 1 ? '男' : '女';
        	if(!empty($communist['communist_ccp_date']))
        	{
        		$communist['communist_ccp_date']=getFormatDate($communist['communist_ccp_date'],"Y-m-d");
        	}
        	$communist['update_time'] = getFormatDate($communist['update_time'],"Y-m-d");
        	$communist['operate'] = $button;
        }
        $communist_list['code'] = 0;
        $communist_list['msg'] = 0;
    	ob_clean();$this->ajaxReturn($communist_list); // 返回json格式数据
    }
    /**
     * @name:ccp_communist_flow_index
     * @desc：党员关系流动
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_flow_index(){
    	
    	$type = I("get.type",'mobile_out');
    	switch ($type) {
    		case 'mobile_out': $cat='_1';break;
    		case 'mobile_join': $cat='_2';break;
    		default: $cat='_1';break;
    	}
    	session('cat',$cat);
    	$this->assign('cat',$cat);
    	checkAuth(ACTION_NAME.$cat);
    	$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	$this->assign('party_list',$party_list);
    	$this->assign('type',$type);
    	$this->display("Ccpcommunistchange/ccp_communist_flow_index");
    }
    /**
     * @name:ccp_communist_party_ajax
     * @desc：返回部门数据
     * @param：
     * @return：
     * @author：刘丙涛
     * @addtime:2017-5-9
     * @version：V1.0.0
     **/
    public function ccp_communist_party_ajax(){
    	$communist_no = I('post.communist_no');
    	$party_no = getCommunistInfo($communist_no,'party_no');
    	if (!empty($party_no)){
    		$party_name = getPartyInfo($party_no,'party_name');
    		$data['party_no'] = $party_no;
    		$data['party_name'] = $party_name;
    	}
    	ob_clean();$this->ajaxReturn($data);
    }
    /**
     * @name:ccp_communist_flow_index_data
     * @desc：党员流动数据
     * @param：
     * @return：
     * @author： 王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_flow_index_data()
    {
    	$get = I("get.");
    	$type = I("get.type");
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
    	if(empty($get['party_no'])){
    		//获取当前人员所属部门
    		// $party_no = getCommunistInfo(session("communist_no"),"party_no");
    		//获取当前及下属部门编号
    		$party_nos = session('party_no_auth');
    	}else{
    		$party_no = I('get.party_no');
    		$party_nos = getPartyChildNos($party_no,'str');
    	}
    	if($type == "mobile_out"){
            $change_map['old_party'] = array('in',$party_nos);
            $change_map['_string'] = "change_type = 3 or change_type = 4";
    	}elseif($type == "mobile_join"){
            $change_map['new_party'] = array('in',$party_nos);
            $change_map['_string'] = "change_audit_status > 10 and change_type = 3";
    		//流动
    	}
    	$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
    	$communist_change = new CcpCommunistChangeModel();
    	$communist_change_data = $communist_change->get_communist_change_list($type,$change_map,$page,$pagesize);


    	$communist_mobile_arr = M('ccp_communist')->getField('communist_no,communist_mobile');
    	$party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $status_map['status_group'] = 'change_audit_status';
    	$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
    	$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        $type_map['type_group'] = 'mobile_type';
    	$type_name_arr = M('bd_type')->where($type_map)->getField('type_no,type_name');
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
    	foreach ($communist_change_data['data'] as &$communist){
    		$change_id = $communist['change_id'];
    		$communist['change_id'] = "<a class='fcolor-22' href='".U('Ccpcommunistchange/ccp_communist_flow_info',array('change_id'=>$change_id,'type'=>$type))."' >".$change_id."</a>";
    		$communist['communist_name'] = "<a class='fcolor-22' href='".U('Ccpcommunistchange/ccp_communist_flow_info',array('change_id'=>$change_id,'type'=>$type))."' >".$communist_name_arr[$communist['communist_no']] ."</a>";
    		if(($communist['change_type'] == '3' || $communist['change_type'] == '4') && $communist['change_audit_status'] == 40 && $type == 'mobile_out'){
    			$communist['operate'] = "<a class='btn btn-xs blue btn-outline' href='".U('Ccpcommunistchange/ccp_communist_flow_info',array('change_id'=>$change_id,'type'=>$type))."' ><i class='fa fa-trash-o'></i> 查看
    		    	</a>" . "&nbsp; <a class='btn btn-xs red btn-outline' href='".U('Ccpcommunistchange/ccp_communist_flow_check',array('change_id'=>$change_id,'change_audit_status'=>'60','type'=>$type,'communist_no'=>$communist['communist_no']))."' > 结束流动 </a>";
    		}else{
    			$communist['operate'] = "<a class=' layui-btn layui-btn-primary layui-btn-xs' href='".U('Ccpcommunistchange/ccp_communist_flow_info',array('change_id'=>$change_id,'type'=>$type))."' >查看</a>";
    		}
    		$communist['change_audit_status'] = "<font color='" . $status_color_arr [$communist['change_audit_status']] . "'>" . $status_name_arr [$communist['change_audit_status']] . "</font> ";
    		$communist['communist_mobile'] = $communist_mobile_arr[$communist['communist_no']];
    		$communist['old_party'] = $party_name_arr[$communist['old_party']];
    		if($party_name_arr[$communist['new_party']]){
    		    $communist['new_party'] = $party_name_arr[$communist['new_party']];
    		}
    		$communist['update_time'] = getFormatDate($communist['update_time'],"Y-m-d");
    		$communist['change_type'] = $type_name_arr[$communist['change_type']];
    	}
    	
    	ob_clean();$this->ajaxReturn($communist_change_data);
    }
    /**
     * @name:ccp_communist_flow_check
     * @desc：党员关系转移审核
     * @param：
     * @return：
     * @author：  王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_flow_check(){
    	$post = I("post.");
    	$type = $_REQUEST['type'];
    	$change_model =  D('CcpCommunistChange');	//调用人员转移model层
    	$res['change_id'] = $post['change_id'];  //审核ID
    	$res['check_staff'] = session('staff_no');   //审核人
    	$res['add_time'] = date('Y-m-d H:i:s');  //审核时间
    	$res['check_content'] = $post['change_audit_content'];  //审核内容
   		$res['log_audit_man'] = $post['log_audit_man'];   //经办人
   		$res['log_contact_tel'] = $post['log_contact_tel']; //联系方式
   		$res['log_attach'] = $post['log_attach'];  //附件
   		/**每次变更状态提醒要审核的人**/
   		$alert_title=getCommunistInfo($post['communist_no']).'党员的流动信息待您审核！';
   		/****/
   		if(empty($post['change_audit_content'])){
    		$res['check_content'] = "同意~";
   		}
   		$post['change_audit_status'] = $post['change_audit_status']+10; //改变状态
   		if($post['change_audit_status'] == 20){  //添加提醒
   			$alert_man=getAuthCommunistNos($post['old_party'],'is_check');
   			$alert_url='Ccp/Ccpcommunistchange/ccp_communist_change_info/change_id/'.$res['change_id'].'/type/mobile_join';
   			saveAlertMsg(12, $alert_man, $alert_url, $alert_title, $alert_time, $alert_nexttime, $alert_cycle, session('staff_no'));
   		}
   		if($post['change_audit_status'] == 20 && $post['change'] == 4){
   			$post['change_audit_status'] = $post['change_audit_status']+10; //改变状态  流出没有待接受
   		}
   		
   		if(!empty($post['reject'])){   //驳回
   			$post['change_audit_status'] = $post['reject'];
    		if(empty($post['change_audit_content'])){
    			if($post['change_audit_status'] == '70'){
    				$res['check_content'] = "终止流动~";
    			}else{
    				$res['check_content'] = "拒绝~";
    			}
    		}
   		}else{ 
    		if( $post['change_audit_status'] == 30 ){  //没有待复核状态 让状态变成完成
    			$post['change_audit_status'] = $post['change_audit_status']+10;
   			}
   		}
   		if($_GET['change_audit_status'] == 60){  //流动结束  修改党员状态 
   			$data['status'] = 21;
   			$communist_no = $_GET['communist_no'];
            $comm_map['communist_no'] = $communist_no;
   			M('ccp_communist')->where($comm_map)->save($data);
   			$_GET['end_time'] = date('Y-m-d H:i:s');
   		}
    	if($post['change_audit_status'] == 40 ){ //改为流动党员  ,添加党员历程 修改党员状态
    		$data['status'] = 22;
    		$communist_no = $post['communist_no'];
            $comm_map['communist_no'] = $communist_no;
    		M('ccp_communist')->where($comm_map)->save($data);
    		if((getPartyInfo($post['new_party'],"party_name"))){
    			$post['new'] = getPartyInfo($post['new_party'],"party_name");
    		}else{
				$post['new'] = $post['new_party'];
			}
    		saveCommunistLog($post['communist_no'],12,'',session('staff_no'),getPartyInfo($post['old_party']),$post['new']); //流动历程
    	}
    	if (!empty($_GET['change_id'])) {
    		$communist_change = $change_model->updateData($_GET,change_id);
    		showMsg('success', '操作成功！！！',  U('Ccpcommunistchange/ccp_communist_flow_index',array('type'=>$type)));
    	}else{
    		$communist_change = $change_model->updateData($post,change_id);
    		$communist_change_log = M('ccp_communist_change_log')->add($res);
    		showMsg('success', '操作成功！！！',  U('Ccpcommunistchange/ccp_communist_flow_info',array('change_id'=>$post['change_id'],'type'=>$type)));
    	}
    }
    /**
     * @name:ccp_communist_flow_edit
     * @desc：新增党员关系转出
     * @param：
     * @return：
     * @author：王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_flow_edit(){
    	checkAuth(ACTION_NAME);
    	$status = I('get.status');
    	$party_no = I('get.party_no');
    	$this->assign('status',$status);
    	$communist_no = I('get.communist_no');
    	$type = I('get.type');
    	if(!empty($communist_no)){
    		$communist_info = getcommunistInfo($communist_no,"all");
            $communist_info['communist_party_name']=getPartyInfo($communist_info['party_no']);//部门
            $communist_info['communist_post_name']=getPartydutyInfo($communist_info['communist_post_no']);//岗位
            $this->assign("communist_info",$communist_info);
    	}
    	$industry = trim(getConfig('industry'));
    	$this->assign('industry', $industry);
    	$communist_list=getCommunistList($party_no,'arr','1','','','',$communist_no);
    	$this->assign("communist_list",$communist_list);
    	$this->assign("type",$type);
    	$this->display("Ccpcommunistchange/ccp_communist_flow_edit");
    }
    /**
     * @name:ccp_communist_flow_info
     * @desc：流动党员数据详情
     * @param：
     * @return：
     * @author：   王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function ccp_communist_flow_info(){
        if(!session('cat')){
            $type = I("get.type",'mobile_out');
            switch ($type) {
        		case 'mobile_out': $cat='_1';break;
        		case 'mobile_join': $cat='_2';break;
        		default: $cat='_1';break;
        	}
    	   session('cat',$cat);
        }
    	checkAuth(ACTION_NAME.session('cat'));
    	$change_id = I("get.change_id");
    	$type = I("get.type");
    	$this->assign("type",$type);
    	$change_model = D('CcpCommunistChange');	//调用人员转移model层
    	$communist_change = $change_model->get_communist_change_info($change_id);
    	$communist_change['old'] = $communist_change['old_party'];
    	$communist_change['new'] = $communist_change['new_party'];
    	$communist_change['old_party'] = getPartyInfo($communist_change['old_party'],"party_name");
    	$communist_change['party_no'] = $communist_change['new_party'];
    	$communist_change['new_party'] = getPartyInfo($communist_change['new_party'],"party_name");
    	$communist_change['change'] = $communist_change['change_type'];
    	$communist_change['change_type'] = getBdTypeInfo($communist_change['change'],"mobile_type");
    	$this->assign("communist_change",$communist_change);
    	//查询转移的党员信息
    	$communist_data = getCommunistInfo($communist_change['communist_no'],'all');
    	$staff_no = session('staff_no');
    	$old_party = $communist_change['old'];
    	$new_party = $communist_change['new'];
        $old_party_map['party_no'] = $old_party;
        $old_party_map['_string'] = "FIND_IN_SET('$staff_no',party_manager)";
    	$old = M('ccp_party')->where($old_party_map)->find();//部门负责人
        $new_party_map['party_no'] = $new_party;
        $new_party_map['_string'] = "FIND_IN_SET('$staff_no',party_manager)";
    	$new = M('ccp_party')->where($new_party_map)->find();//部门负责人
    	$check = checkDataAuth($staff_no,'is_check');
    	$this->assign("old",$old);
    	$this->assign("new",$new);
    	if(($check || $old) && $communist_change['change_audit_status'] == 10 && $type == mobile_out){//判断有没有审核权限
    		$reveal = 1;
    	}
    	if(($check || $new) && $communist_change['change_audit_status'] == 20 && $type == mobile_join){//判断有没有审核权限
    		$reveal = 1;
    	}
    	$this->assign("reveal",$reveal);
    	$this->assign("communist_data",$communist_data);
    	$change_id = $communist_change['change_id'];
        $change_map['change_id'] = $change_id;
    	$res = M('ccp_communist_change_log')->where($change_map)->select();
    	$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
    	foreach ($res as &$list){
    	    $list['check_staff'] = $staff_name_arr[$list['check_staff']];
    	}
    	$this->assign('res',$res);
    	$this->display("Ccpcommunistchange/ccp_communist_flow_info");
    }


    /**
     * @name:check_communist
     * @desc：详情
     * @param：
     * @return：
     * @author：   王宗彬
     * @update_time:2017-11-14
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    public function check_communist()
    {
        $communist_no = I('post.communist_no');
        $type = I('post.type');
        $map['communist_no'] = $communist_no;
        
        if($type == '1'){
            $map['change_audit_status'] = array('not in','40,50');
            $map['change_type'] = array('in','1,2');
        }else{
            $map['change_audit_status'] = array('neq','60');
            $map['change_type'] = array('in','3,4');
        }
        $change_data = M('ccp_communist_change')->where($map)->getField('change_id');

        $this->ajaxReturn($change_data);
    }
}
