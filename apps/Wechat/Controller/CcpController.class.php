<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
use Ccp;
use Symfony\Component\Yaml\Dumper;
class CcpController extends Controller 
{
	/**
	 * @ccp_communist_info
	 * @desc：个人中心
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function ccp_communist_info()
	{
		checkLoginWeixin();
		$communist_no = session('wechat_communist');
		$ccp_communist_bio = M("ccp_communist_bio");
		$log_type = 	I('get.log_type');
		$communist_info = getCommunistInfo($communist_no,
				'communist_no,communist_name,party_no,post_no,communist_avatar,communist_sex,communist_tel,
                            communist_birthday,communist_school,communist_idnumber,communist_email,communist_ccp_date,communist_paddress,communist_address,communist_specialty,communist_mobile');
		if ($communist_info) {
			if(!empty($communist_info['communist_avatar'])){
				$communist_info['communist_avatar'] =  $communist_info['communist_avatar'];
			}else{
				$communist_info['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
			}
			$communist_info['party_name'] = getPartyInfo($communist_info['party_no']);
			$communist_info['post_name'] = getPartydutyInfo($communist_info['post_no']);
			//积分
			$integral = getCommunistRank($communist_no);
			$communist_info['communist_sex'] = $communist_info['communist_sex'] == '1' ? '男' : '女';
			if (!empty($communist_info['communist_nation'])) {
				$communist_info['communist_nation'] = M('bd_nation')->where("nation_id=" . $communist_info['communist_nation'])->getField('nation_name');
			}
			$communist_info['bio_num'] = $ccp_communist_bio->where("communist_no = '$communist_no'")->count();//状态
			$communist_info['integral'] = round($integral['integral'],0);
			$communist_info['ranking'] = $integral['ranking'];
			$communist_info['party_ranking'] = $integral['party_ranking'];
			$dev_log_list = getCommunistLogList($communist_no, '10');//党员发展历程
			foreach ($dev_log_list as &$dev_log) {
                $dev_log['add_time'] = getFormatDate($dev_log['add_time'],'Y-m-d');
                $t1 = mb_strpos($dev_log['log_content'],'[')+1;
                $t2 = mb_strpos($dev_log['log_content'],']');
                $dev_log['log_title'] = mb_substr($dev_log['log_content'],$t1,$t2-$t1);
            }
			if(!empty($log_type)){
				$communist_log_list = getCommunistLogList($communist_no,$log_type);//党员历程
			}else{
				$log_type = COMMUNIST_STATUS_COURSE;
				$communist_log_list = getCommunistLogList($communist_no,$log_type);//党员历程
			}
			$communist_info['dev_log_list'] = $dev_log_list;
			$communist_info['log_list'] = $communist_log_list;
			//2018-02-09 增加
			$communist_info['status'] = getStatusInfo('communist_status',$communist_info['status']);
			$communist_info['time'] = $dev_log_list['0']['add_time'];
		}
		$this->assign('communist_info',$communist_info);
		$this->display('Ccp/ccp_communist_info');
	}
	/**
	 * @ccp_communist_integral
	 * @desc：个人积分
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function ccp_communist_integral()
	{
	    checkLoginWeixin();
		$communist_no = I('get.communist_no');
		if(!empty($communist_no)){
			$communist_info = getCommunistInfo($communist_no,'communist_no,communist_name,party_no,communist_avatar,communist_birthday');
			$communist_info['party_no'] = getPartyInfo($communist_info['party_no']);
			if(empty($communist_info['communist_avatar'])){
        		$communist_info['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
    		}
			$this->assign('communist_info',$communist_info);
			
			//积分
			$integral = getCommunistRank($communist_no);
			$this->assign('integral',$integral);
			
			$db_integral = new \Ccp\Model\CcpIntegralLogModel();
			$integral_map['log_relation_no'] = $communist_no;
			$integral_info['change_integral'] = M('ccp_integral_log')->where($integral_map)->sum('change_integral');			
			$integral_info['change_integral'] = round($integral_info['change_integral'],0);
			if ($integral_info) {
				$integral_list = $db_integral->getIntegralLog('2', $communist_no, '1', $type);

				$integral_info['log_list'] = $integral_list['data'];
			}
			if (empty($integral_info['change_integral'])) {
				$integral_info['change_integral'] = '0';
			}
			
			$this->assign('integral_info',$integral_info);
		}
		$this->display('Ccp/ccp_communist_integral');
		
	}
	/**
	 * @ccp_communist_choice
	 * @desc：选择人员
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function ccp_communist_choice()
	{
		$party_no= I('post.party_no');
		$party_no = implode(',',$party_no);
		$party_list = getPartyList();
		foreach ($party_list as &$list){
			$list['lists'] = getPartyList($list['party_no']);
		}
		$status = COMMUNIST_STATUS_OFFICIAL;
		$communist_list = getCommunistList($party_no,'arr','1',$post_no, $status);
		$this->assign('party_list',$party_list);
		$this->assign('communist_list',$communist_list);
		$this->assign('communist_name',$communist_name);
		$this->display('Ccp/ccp_communist_choice');
	}
	
	/**
	 * @ccp_party_index
	 * @desc：党组织列表
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function ccp_party_index()
	{
		checkLoginWeixin();
		$party_code = I('get.party_code',1);
		$party_code_name = getBdCodeInfo($party_code,'party_level_code');
		$this->assign('party_code_name',$party_code_name);
		$this->assign('party_code',$party_code);
		$code_list = getBdCodeList('party_level_code');
		$this->assign('code_list',$code_list);
		//getPartyList($party_name)
		$party_list = M('ccp_party')->where("party_level_code = '$party_code' and status = 1")->select();
		$this->assign('party_list',$party_list);
		$this->display('Ccp/ccp_party_index');
	}
	
	/**
	 * @ccp_party_info
	 * @desc：党组织详情
	 * @author：王宗彬
	 * @addtime:2018-04-25
	 * @version：V1.0.0
	 **/
	public function ccp_party_info()
	{
		$party_no = I('get.party_no');
		$db_integral = new \Ccp\Model\CcpIntegralLogModel();
		$party_info = M('ccp_party')->where("party_no=$party_no")->field('party_no,party_name,memo,party_level_code')->find();
		$pary_code = M('bd_code')->where("code_group = 'party_level_code'")->getField("code_no,code_name");
		if ($party_info) {
			$child_nos = getPartyChildNos($party_no, 'arr');//取下级组织
			$party_info['party_num'] = count($child_nos);//党组织数量
			$party_info['party_level_code'] = $pary_code[$party_info['party_level_code']];
			$party_info['communist_num'] = getCommunistCount($child_nos, null, COMMUNIST_STATUS_OFFICIAL);
			$integral = $db_integral->getIntegralInfo('1', $party_no);
			$party_info['integral_total'] = $integral['integral_total'];
			$where['party_no'] = array('in', $child_nos);
			$party_info['dues_amount'] = M("ccp_dues")->where($where)->sum('dues_amount');//党费
			if(empty($party_info['communist_num'])){
				$party_info['communist_num'] = 0;
			}
			if(empty($party_info['integral_total'])){
				$party_info['integral_total'] = 0;
			}
			if(empty($party_info['dues_amount'])){
				$party_info['dues_amount'] = 0;
			}
			$status = COMMUNIST_STATUS_OFFICIAL;
			$communist_list = getCommunistList($party_no,'arr', '1','',$status,'','','','','','','','','',0,8);
			foreach ($communist_list['data'] as &$list) {
				if(!$list['communist_avatar'] || !file_exists(SITE_PATH.$list['communist_avatar'])){
            		$list['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
        		}
			}
			$party_info['communist_list'] = $communist_list['data'];
			
			$party_nos = getPartyChildNos($party_no,'str');
			$map['party_no'] = array('in',$party_nos);
			$meeting_list = M("oa_meeting")->where($map)->order("add_time desc")->page("0,5")->select();
			foreach ($meeting_list as &$meeting) {
				$meeting['meeting_name'] = mb_substr(strip_tags($meeting['meeting_name']),0,10,'utf-8');
			}
			$party_info['meeting_list'] = $meeting_list;
			$this->assign('party_info',$party_info);
		}
		$this->display('Ccp/ccp_party_info');
	
	}
	/**
	 * @ccp_communist_list
	 * @desc：党员列表
	 * @author：王宗彬
	 * @addtime:2018-05-10
	 * @version：V1.0.0
	 **/
	public function ccp_communist_list()
	{
		$party_no = I('get.party_no');
		if(!$party_no){
			$party_no = getCommunistInfo(session('wechat_communist'));
		}
		$status = COMMUNIST_STATUS_OFFICIAL;
		$communist_list = getCommunistList($party_no,'arr', '1','',$status);
		foreach ($communist_list as &$list) {
			if(empty($list['communist_avatar'])){
            	$list['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
        	}
		}
		$this->assign('communist_list',$communist_list);
		
		$this->display('Ccp/ccp_communist_list');
	}
	/**
	 * @ccp_party_communist_info
	 * @desc：党员详情
	 * @author：王宗彬
	 * @addtime:2018-05-10
	 * @version：V1.0.0
	 **/
	public function ccp_party_communist_info()
	{
		$communist_no = I('get.communist_no');
		$communist_info = getCommunistInfo($communist_no,'communist_no,communist_name,communist_mobile,communist_address,party_no,post_no,communist_avatar,communist_birthday,communist_sex');
		$communist_info['party_no'] = getPartyInfo($communist_info['party_no']);
		$communist_info['post_no'] = getPartydutyInfo($communist_info['post_no']);
		if(empty($communist_info['communist_avatar'])){
            $communist_info['communist_avatar'] =  "/statics/public/images/default_photo.jpg";
        }
		if($communist_info['communist_sex'] == 1){
			$communist_info['communist_sex'] = '男';
		}else{
			$communist_info['communist_sex']  = '女';
		}
		$this->assign('communist_info',$communist_info);
		$this->display('Ccp/ccp_party_communist_info');
	}
	/**
	 * @out_communist
	 * @desc：退出
	 * @author：王宗彬
	 * @addtime:2018-05-10
	 * @version：V1.0.0
	 **/
	public function out_communist()
	{
	    session('[destroy]');
	    showMsg('success', '操作成功！', U('Index/index'));
	}
	/**
	 * @ccp_dues_index
	 * @desc：党费缴纳
	 * @author：王宗彬
	 * @addtime:2018-05-10
	 * @version：V1.0.0
	 **/
	public function ccp_dues_index()
	{
	    checkLoginWeixin();
	    $communit_no = session('wechat_communist');
	    $time = date('Y-m');
	    $dues_list = M('ccp_dues')->where("dues_month = '$time' and communist_no ='$communit_no'")->getField('dues_amount');
	    $status = M('ccp_dues')->where("dues_month = '$time' and communist_no ='$communit_no'")->getField('status');

	    $this->assign('dues_list',$dues_list);
	    $this->assign('status',$status);
	    $this->display('Ccp/ccp_dues_index');
	}
}