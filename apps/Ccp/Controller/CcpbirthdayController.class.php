<?php
/******************   党费管理      *****************************************/
namespace Ccp\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class CcpbirthdayController extends BaseController{    
    /**
     * @name:ccp_birthday_index
     * @desc：党员生日首页
     * @author：ss_yk
     * @addtime:2018-08-14
     * @version：V1.0.0
     **/            
    public function ccp_birthday_index(){
        checkAuth(ACTION_NAME);
        $month=date("m");
        $this->assign('month',$month);
        $party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
        $this->assign('party_no',$party_list[0]['party_no']);
        $this->display("ccp_birthday_index");
    }
   /**
     * @name:ccp_dues_list_data
     * @desc：党员生日数据读取
     * @author：ss_yk
     * @addtime:20180814
     * @version：V1.0.0
     **/
    public function ccp_birthday_index_data(){
        $communist_no=I('get.communist_no');
        $communist_name=I('get.communist_name');
        $dues_month=I('get.dues_month');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        
        $party_no = I('get.party_no');
        $db_communist=M("ccp_communist");
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
            $duse_map['_string'] = "DATE_FORMAT(communist_ccp_date, '%m') = '$dues_month'";
        }
        if(!empty($communist_name)){
            $duse_map['communist_name']  = array('like', '%'.$communist_name.'%');
        }
        $duse_map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $dues_list=$db_communist->where($duse_map)->limit($page,$pagesize)->select();
        $count=$db_communist->where($duse_map)->count();
        $confirm = 'onclick="if(!confirm(' . "'确认发送祝福通知？'" . ')){return false;}"';
        if(!empty($dues_list)){
            foreach($dues_list as &$dues){
                $dues['communist_name'] = "<a href='".U('Ccpcommunist/ccp_communist_info',array('communist_no'=>$dues['communist_no']))."'  class=' fcolor-22 '>".$dues['communist_name']."</a>";
                $dues['party_name']=getPartyInfo($dues['party_no']);
                //$dues['operate']="<a class='btn btn-xs red btn-outline' href='" . U('ccp_birthday_biessing',array('communist_no' => $dues['communist_no'],'communist_ccp_date'=>$dues['communist_ccp_date'])) . "'$confirm><i class='fa fa-trash-o'></i>祝福 </a>";
            }
            $arr['code'] = 0;
            $arr['msg'] = '获取数据成功';
            $arr['data'] = $dues_list;
            $arr['count'] = $count;
            ob_clean();$this->ajaxReturn($arr);
        } else {
            $arr['code'] = 0;
            $arr['msg'] = '暂无相关数据';
            ob_clean();$this->ajaxReturn($arr);
        }
    }
    /**
     * @name:ccp_birthday_biessing
     * @desc：祝福通知
     * @author：ss_yk
     * @addtime:20171211
     * @version：V1.0.0
     **/
    public function ccp_birthday_biessing(){
        $db_dues=M("ccp_dues");
        $communist=I('get.communist_no');
        $communist_ccp_date=I('get.communist_ccp_date');
        $add_staff_name =getStaffInfo(session('staff_no'));
        $birthday_biessing = getConfig('birthday_biessing');
        $alert_title = "你有一条生日祝福";
        $alert_content = "$add_staff_name".'向您发来一条生日祝福！'.$birthday_biessing;
        $where['alert_type'] = 60;
        $where['alert_man'] = $communist;
        $where['_string'] = "DATE_FORMAT(add_time, '%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')";
        $res = M('bd_alertmsg')->where($where)->getField();
        if ($res){
            showMsg('error', '本月已经祝福，不可重复祝福');
        }else{
            $s = saveAlertMsg('60', $communist,'', $alert_title, $communist_ccp_date, '', '', session('staff_no'),0,$alert_content);
            if ($s){
                showMsg('success', '操作成功', U('ccp_birthday_index'));
            }else{
                showMsg('error', '数据有误，操作失败！');
            }
        }
    }
}