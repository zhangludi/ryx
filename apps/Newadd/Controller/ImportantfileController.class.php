<?php
namespace System\Controller;//命名空间
use Think\Controller;
use Common\Controller\BaseController;

class ImportantfileController extends BaseController//继承Controller类
{
	//重要文件留存	
	public function important_file_index(){
		checkAuth(ACTION_NAME);//判断越权
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
		
		
	}
	
	
	 /**
     * @name:important_file_index
     * @desc：重要文件留存
     * @author：靳邦龙
     * @addtime:2017-10-10
     * @version：V1.0.0
     **/
    public function important_file_index(){
        checkAuth(ACTION_NAME);
     
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
        $party_no = I('get.party_no');
        $this->assign('party_no',$party_no); 
		
		

        $this->display("Importantfile/ccp_dues_index");
    }
   /**
     * @name:ccp_dues_list_data
     * @desc：党费列表数据读取
     * @author：靳邦龙
     * @addtime:20171010
     * @version：V1.0.0
     **/
    public function ccp_dues_list_data(){
        $communist_no=I('get.communist_no');
        $communist_name=I('get.communist_name');
        $dues_month=I('get.dues_month');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        
        $status=I('get.status');
        $party_no = I('get.party_no');
        $db_dues=M("ccp_dues");
        if(!empty($communist_no)){
            $duse_map['communist_no'] = $communist_no;
        }
        if(!empty($party_no)){
            $party_nos=getPartyChildNos($party_no);
        } else {
            $party_nos = session('party_no_auth');//取本级及下级组织
        }
        $duse_map['party_no'] = array('in',$party_nos);
        if(!empty($dues_month)){
            $duse_map['_string'] = "DATE_FORMAT(dues_time, '%Y-%m') = '$dues_month'";
        }
        if(!empty($status)){
            $duse_map['status'] = $status;
        }
        if(!empty($communist_name)){
            $duse_map['communist_name']  = array('like', '%'.$communist_name.'%');
        }
        $dues_list=$db_dues->where($duse_map)->limit($page,$pagesize)->order('add_time desc')->select();
        $count=$db_dues->where($duse_map)->count();
        $confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
        $status_map['status_group'] = 'dues_status';
        $status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
        $status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');
        if(!empty($dues_list)){
            foreach($dues_list as &$dues){
                $dues['status_name'] = "<font color='" . $status_color_arr[$dues['status']] . "'>" . $status_name_arr[$dues['status']] . "</font> ";//getStatusName('dues_status', );
               // $dues['operate']="<a class='btn btn-xs red btn-outline' href='" . U('ccp_dues_do_del',array('dues_id' => $dues['dues_id'])) . "'$confirm><i class='fa fa-trash-o'></i> 删除 </a>";
                $dues['update_time'] = getFormatDate($dues['update_time'],"Y-m-d");
            }
            $arr['data'] = $dues_list;
            $arr['count'] = $count;
            $arr['code'] = 0;
            $arr['msg'] = '获取数据成功';
            ob_clean();$this->ajaxReturn($arr);  
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($arr);  
        }
        
    }
	/**
	* @name:ccp_dues_edit
	* @desc：添加
	* @author：靳邦龙
	* @addtime:20171010
	* @version：V1.0.0
	**/
	public function ccp_dues_edit(){
		
        $party_no_auth = session('party_no_auth');//取本级及下级组织
		$this->assign('party_no_auth',$party_no_auth);
		
		
		$this->display("Ccpdues/ccp_dues_edit");
	}
	/**
	* @name:ccp_dues_status
	* @desc：添加
	* @author：靳邦龙
	* @addtime:20171010
	* @version：V1.0.0
	**/
	public function ccp_dues_status(){
		$dues_id=I('get.dues_id');
        $duse_map['dues_id'] = $dues_id;
		$data['status'] = 2;
		$dues_res = M('ccp_dues')->where($duse_map)->save($data);	
		if ($dues_res){
			showMsg('success', '缴纳成功', U('ccp_dues_index'));
		}else{
			showMsg('error', '缴纳失败！');
		}
	}
	
	
   /**
     * @name:ccp_dues_do_save
     * @desc：党费数据保存方法
     * @author：靳邦龙
     * @addtime:20171010
     * @version：V1.0.0
     **/
    public function ccp_dues_do_save(){
        $db_dues=M("ccp_dues");
        $data=I('post.');
	
        $map['dues_month'] = $data['dues_month'];
        $map['communist_no'] = $data['communist_no'];
        $res = $db_dues->where($map)->find();
        $title = getCommunistInfo($data['communist_no']).'本月缴费信息已存在，不可重复录入！';
        if(!empty($res)){
            showMsg('error',$title);
        }
        $data['add_staff']=session('staff_no');//添加人
        $data['add_staff_name']=M('hr_staff')->where("staff_no = ".$data['add_staff'])->getField('staff_name');//添加人姓名
        $data['party_no']=getCommunistInfo($data['communist_no'],'party_no');//缴费人部门
        $data['party_name']=getPartyInfo($data['party_no'],'party_name');//缴费人部门名称
        $data['communist_name']=getCommunistInfo($data['communist_no']);//缴费人姓名
        $data['add_time']=date("Y-m-d H:i:s");
        $data['update_time']=date("Y-m-d H:i:s");
       
        if(!trim($data['dues_time'])){
            $data['dues_time']=null;
        }
        $dues_res=$db_dues->add($data);
		if ($dues_res){
			showMsg('success', '操作成功', '', 1);
		}else{
			showMsg('error', '数据有误，操作失败！');
		}
    }
}

