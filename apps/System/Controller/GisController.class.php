<?php
namespace System\Controller;
 // 命名空间
use Think\Controller;
use Common\Controller\BaseController;
class GisController extends BaseController // 继承Controller类
{
    /**
     *  index
     * @desc 地图
     * @user 
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function index()
    {
        $party_no_auth = session('party_no_auth');//取下级组织party_no_all
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M("ccp_party")->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->order('party_no asc')->select();
        $this->assign('party_list',$party_list);
        $web_address = getConfig('web_address');
        $this->assign('web_address',$web_address);
        $this->assign('party_list', $party_list);
        $this->assign('party_no', $party_list[0]['party_no']);
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
        $where_c_num['party_no'] = array('in',$party_no_auth);
        $where_c_num['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
        $communist_num = M('ccp_communist')->where($where_c_num)->count(); // 党员数
        $party_num = M('ccp_party')->count(); // 党组织数
        //$meeting_num = M('oa_meeting')->count(); // 组织生活会议
        $where_m_num['party_no'] = array('in',$party_no_auth);
        $where_m_num['status'] = array('eq',23);
        $where_m_num['_string'] = "DATE_FORMAT(meeting_real_end_time, '%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')";
        $meeting_num = M('oa_meeting')->where($where_m_num)->count();
        $time = time();
        $month = date('Y-m');
        $where_cost_num['log_relation_no'] = array('in',$party_no_auth);
        $where_cost_num['log_relation_type'] = array('eq',2);
        $where_cost_num['add_time'] = array('like',"$month%");
        $dues_month_num=M("ccp_integral_log")->where($where_cost_num)->sum('change_integral');//获得积分
        $code_list = getBdCodeList('party_level_code');
            $this->assign('code_list',$code_list);
        $this->assign('party_list',$party_list);
        $this->assign("communist_num",$communist_num);
        $this->assign("party_num",$party_num);
        $this->assign("meeting_num",$meeting_num);
        $this->assign("dues_month_num",$dues_month_num);
        $this->display('gis');
    }
}
