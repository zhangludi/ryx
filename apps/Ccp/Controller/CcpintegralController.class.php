<?php

namespace Ccp\Controller;

use Ccp\Model\CcpIntegralLogModel;
use Common\Controller\BaseController;

class CcpintegralController extends BaseController // 继承Controller类
{
	 /**
	 * @name:ccp_communist_integral_index               
	 * @desc：党员积分列表
	 * @param：
	 * @author：刘丙涛
	 * @addtime:2017-10-17
	 * @version：V1.0.0
	**/
    public function ccp_communist_integral_index(){
        checkAuth(ACTION_NAME);
        $party_no_auth = session('party_no_auth');//取本级及下级组织
		$this->assign('party_no_auth',$party_no_auth);
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
        $this->assign('party_list',$party_list);
        $is_excellence = getConfig('is_excellence');
        $is_talk = getConfig('is_talk');
        $this->assign('is_talk',$is_talk);
        $this->assign('is_excellence',$is_excellence);
        $this->display("Ccpintegral/ccp_communist_integral_index");
    }
    /**
     * @name:ccp_communist_integral_index_data
     * @desc：党员积分列表数据
     * @param：
     * @author：刘丙涛
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_communist_integral_index_data(){
        $staff_no = session('staff_no');
        $is_year = I('get.is_year');
        $party_no = I('get.party_no'); 
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
        if(empty($party_no)){
            $party_no_auth = session('party_no_auth');//取本级及下级组织
            $party_map['status'] = 1;
            $party_map['party_no'] = array('in',$party_no_auth);
            $party_no = M('ccp_party')->where($party_map)->limit(1)->order('party_no asc')->getField("party_no");
        }
        $data = getCommunistIntegralList($communist_no,$party_no,$is_year,$page,$pagesize);
        $data['code'] = 0;
        $data['msg'] = 0;
        ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }
    /**
     * @name:ccp_communist_integral_info
     * @desc：党员积分详情
     * @param：
     * @author：刘丙涛
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_communist_integral_info(){
        checkAuth(ACTION_NAME);
        $db_log = new CcpIntegralLogModel();
        $communist_no = I('get.communist_no');
        $is_year = I('get.is_year');
        $communist_data = getCommunistRank($communist_no,$is_year);
		if($communist_data['integral'] < 0){
			$communist_data['integral'] = '0.00';
		}
		
        $log_list = $db_log->getIntegralLog('2',$communist_no,$is_year);

        $this->assign('log_list',$log_list);
		// dump($log_list);
		
        $this->assign('communist_no',$communist_no);
        $this->assign('communist_data',$communist_data);
        $this->assign('is_year',$is_year);
        $this->display("Ccpintegral/ccp_communist_integral_info");
    }
     /**
     * @name:ccp_communist_integral_info_data
     * @desc：党员积分详情
     * @param：
     * @author：刘丙涛
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_communist_integral_info_data(){
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $db_log = new CcpIntegralLogModel();
        $communist_no = I('get.communist_no');
        $year = I('get.is_year');
        $log_list = $db_log->getIntegralLog('2',$communist_no,$year,'',$page,$pagesize);
        ob_clean();$this->ajaxReturn($log_list);
    }
    
    /**
     * @name:ccp_communist_integral_index_excellent
     * @desc：优秀党员/纪检约谈
     * @param：
     * @author：王宗彬
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_communist_integral_index_excellent(){
    	$add_no = I('get.communist_no');
    	$type = I('get.type');
    	if($type == 2){
    		$add_no = I('get.party_no');
    	}
    	$res = setSuperviseOutstandingInfo($add_no,session('staff_no'),$type);
    	if($res){
    		showMsg('success',$res,U('ccp_communist_integral_index'));
    	}else{
    		showMsg('error',$res);
    	}
    }
    
    /**
     * @name:ccp_communist_integral_index_operation
     * @desc：纪检约谈
     * @param：
     * @author：王宗彬
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_communist_integral_index_operation(){
    	$add_no = I('get.communist_no');
    	$type = I('get.type'); // 1是党员  2党组织
    	$talk = I('get.talk'); //1纪检
    	if($type == 2){
    		$add_no = I('get.party_no');
    		$url = U('ccp_party_integral_index');
    	}else{
    		$url = U('ccp_communist_integral_index');
    	}
    	if(!empty($talk)){
    		$res = setSuperviseChatInfo($add_no,session('staff_no'),$type);
    	}else{
    		$res = setSuperviseOutstandingInfo($add_no,session('staff_no'),$type);
    	}
    	if($res){
    		showMsg('success',$res,$url);
    	}else{
    		showMsg('error',$res);
    	}
    }
    /**
     * @name  ccp_party_integral_index
     * @desc  党组织积分列表
     * @author 刘丙涛
     * @addtime:2017-10-18
     * @version：V1.0.0
     **/
    public function ccp_party_integral_index(){
        checkAuth(ACTION_NAME);
		
		$party_no_auth = session('party_no_auth');//取本级及下级组织
		$this->assign('party_no_auth',$party_no_auth);
		
        $is_excellence = getConfig('is_excellence');
        $is_talk = getConfig('is_talk');
        $this->assign('is_talk',$is_talk);
        $this->assign('is_excellence',$is_excellence);
    	$this->display("Ccpintegral/ccp_party_integral_index");
    }
    /**
     * @name ccp_party_integral_index_data
     * @desc 党组织积分列表数据
     * @author：刘丙涛  王宗彬
     * @addtime:2017-10-17
     * @version：V1.0.0
     **/
    public function ccp_party_integral_index_data(){
    	$ccp_party = M('ccp_party');
    	$db_integral = M('ccp_integral_log');
    	$db_communist = M('ccp_communist');
    	$staff_no = session('staff_no');
    	$is_year = I('get.is_year');
    	$pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
        $party_no = I('get.party_no');
        if(!empty($party_no)){
            $party_no_auth = getPartyChildMulNos($party_no,'str');
        }else{
            $party_no_auth = session('party_no_auth');//取本级及下级组织
        }
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        if(!empty($is_year)){
            $year = date("Y");
            $party_list = M()->query("SELECT p.party_no,p.party_pno,p.party_name,IFNULL(log.integral_total,0) as party_integral FROM sp_ccp_party p LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 2 AND YEAR = $year GROUP BY log_relation_no) log ON p.party_no = log.log_relation_no WHERE p.`status` = 1 and p.party_no IN ($party_no_auth) ORDER BY party_integral DESC limit $page,$pagesize");
        } else {
            $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,party_integral')->order('party_integral desc')->limit($page,$pagesize)->select();
        }
        $data['count'] = M('ccp_party')->where($party_map)->count();
        $party_name_arr = M("ccp_party")->getField("party_no,party_name");
        $num = $page+1;
        foreach ($party_list as &$list){
            if($list['party_pno'] == "0"){
                $list['party_pname'] = "无上级党组织";
            }else{
                $list['party_pname'] = $party_name_arr[$list['party_pno']];
            }
            if($list['party_integral'] < 0){
                $list['party_integral'] = 0;
            }
            $list['num'] = $num;
            //$list['operate'] =  "<a class=' layui-btn layui-btn-primary layui-btn-xs' href='" . U('ccp_party_integral_info', array('is_year'=>$is_year,'party_integral'=>$list['party_integral'],'rank'=>$num,'party_no' => $list['party_no'])) . "'><i class='fa fa-info-circle'></i>查看</a>  ";
            $list['excellent'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='".U('ccp_communist_integral_index_operation',array('type'=>'2','party_no'=>$list['party_no']))."'>优秀党组织</a>";
            $list['talk'] = "<a class='layui-btn layui-btn-del layui-btn-xs' href='".U('ccp_communist_integral_index_operation',array('talk'=>'1','type'=>'2','party_no'=>$list['party_no']))."'>纪检约谈</a>";
            $num++;
        }
        $data['data'] = $party_list;
        $data['code'] = 0;
        $data['msg'] = 0;
        ob_clean();$this->ajaxReturn($data); // 返回json格式数据
    }
    /**
     * @name:ccp_party_integral_info
     * @desc：党组织积分详情
     * @param：
     * @return：
     * @author：王桥元  王宗彬
     * @addtime:2017-05-04
     * @version：V1.0.0
     **/
    public function ccp_party_integral_info(){
    	checkAuth(ACTION_NAME);
    	$is_year = I('get.is_year');
        $db_log = new CcpIntegralLogModel();
        $db_Integral = new CcpIntegralLogModel();
        $party_no = I('get.party_no');
        $party_integral_now = I('get.party_integral');
        $log_list = $db_log->getIntegralLog('1',$party_no,$is_year);
        $party_integral = $db_Integral->getIntegralInfo('1',$party_no,$is_year);
        $party_nos = getPartyChildNos($party_no);
        $communist_list = getCommunistList($party_nos,'str','1');
        $data['party_integral'] = $party_integral['integral_total'];
        $data['integral_rank'] = I('get.rank');
        $this->assign('data',$data);
        $this->assign('party_no',$party_no);
        $this->assign('is_year',$is_year);
        $this->assign('log_list',$log_list);
        $this->display("Ccpintegral/ccp_party_integral_info");
    }
    /**
     * @name:ccp_party_integral_info_data
     * @desc：党组织积分详情ajax
     * @param：
     * @return：
     * @author： 王宗彬
     * @addtime:2017-05-04
     * @version：V1.0.0
     **/
    public function ccp_party_integral_info_data(){
        $is_year = I('get.is_year');
        $db_log = new CcpIntegralLogModel();
        $party_no = I('get.party_no');
        $page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
        $log_list = $db_log->getIntegralLog('1',$party_no,$is_year,'',$page,$pagesize);
        ob_clean();$this->ajaxReturn($log_list);
     
        
    }
    /**
     * @name:ccp_integral_add_score
     * @desc：手动增加或减少积分
     * @param：
     * @return：
     * @author：刘丙涛
     * @addtime:2017-05-16
     * @version：V1.0.0
     **/
    public function ccp_integral_add_score(){
        $log_relation_no = I('get.log_relation_no');
        $log_relation_type = I('get.log_relation_type');
        $year = I('get.year');
        $db_integral = new CcpIntegralLogModel();
        $list = $db_integral->getIntegralInfo($log_relation_type,$log_relation_no,$year);
        $this->assign('integral_relation_type',$log_relation_type);
        $this->assign('list',$list);
        $this->display('Ccpintegral/ccp_integral_add_score');
    }
    /**
     * @name:ccp_integral_add_score_do_save
     * @desc：手动增加或减少积分执行
     * @param：
     * @return：
     * @author：刘丙涛
     * @addtime:2017-05-16
     * @version：V1.0.0
     **/
    public function ccp_integral_add_score_do_save(){
        $relation_no = I('post.integral_relation_no');
        $relation_type = I('post.integral_relation_type');
        $log_type = I('post.integral_type');
        $change_integral = I('post.change_integral');
        $cause = I('post.cause');
        if($relation_type == 1){ // 党组织
            if($log_type == 'integral_add'){
                $party_integral = getPartyInfo($relation_no,'party_integral');
                updateIntegral(2,7,$relation_no,$party_integral,$change_integral,'手动调整积分',$cause); // 手动调整积分
            } else {
                $party_integral = getPartyInfo($relation_no,'party_integral');
                updateIntegral(2,8,$relation_no,$party_integral,$change_integral,'手动调整积分',$cause); // 手动调整积分
            }
        } else { // 党员
            if($log_type == 'integral_add'){
                $communist_integral = getCommunistInfo($relation_no,'communist_integral');
                updateIntegral(1,7,$relation_no,$communist_integral,$change_integral,'手动调整积分',$cause); // 手动调整积分
            } else {
                $communist_integral = getCommunistInfo($relation_no,'communist_integral');
                updateIntegral(1,8,$relation_no,$communist_integral,$change_integral,'手动调整积分',$cause); // 手动调整积分
            }
        }
        showMsg('success','操作成功','','1');
    }
}
