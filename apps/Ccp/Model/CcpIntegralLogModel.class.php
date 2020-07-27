<?php

namespace Ccp\Model;

use Think\Model;
use Common\Model\PublicModel;

class CcpIntegralLogModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
//        array('integral_relation_type','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT)
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s')
    );

    /**
     * @name  getIntegralLog()
     * @desc  获取积分日志
     * @param $relation_type int 1:党组织2:党员
     * @param $communist_no string 党员编号
     * @param $year string 年份
     * @param $log_type string 年份
     * @return array
     * @author 刘丙涛
     * @addtime   2017-10-17
     * @version 1.0.0
     */
   public function getIntegralLog($relation_type,$relation_no,$year,$log_type = null,$page,$pagesize){
        if(!empty($year)){ $log_map['year'] = date("Y"); }
        if($log_type == "integral_volunteer_communist"){
            $log_map['memo'] = "参加志愿者活动";
        }
        if($relation_type == 1){
            $log_map['log_relation_type'] = 2;
        } else {
            $log_map['log_relation_type'] = 1;
        }
        $log_map['log_relation_no'] = $relation_no;
        $log_list['data'] = $this->where($log_map)->field('change_type,change_integral,year,add_time,memo,cause')->order('add_time desc')->limit($page,$pagesize)->select();
        $log_list['count'] = $this->where($log_map)->count();
        if (!empty($log_list)){
            foreach ($log_list['data'] as &$list){
                if($list['change_type'] == 7){
                   $list['change_integral'] = "+".(float)$list['change_integral'];
                } else {
                   $list['change_integral'] = "-".(float)$list['change_integral'];
                }
            }
        }
        $log_list['code'] = 0;
        $log_list['msg'] = 0;
        return $log_list;
    }
    /**
     * @name  getIntegralInfo()
     * @desc  获取积分某员工或组织积分总额
     * @param $relation_type int 1:党组织2:党员
     * @param $communist_no string 党员编号
     * @param $year string 年份
     * @return array
     * @author 刘丙涛
     * @addtime   2017-10-17
     * @version 1.0.0
     */
    public function getIntegralInfo($relation_type,$relation_no,$year){
        if ($relation_type ==1){
            if(!empty($year)) {
                $year = date("Y");
                $integral_info = M()->query("SELECT p.party_no,p.party_pno,p.party_name,IFNULL(log.integral_total,0) as integral_total FROM sp_ccp_party p LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 2 AND YEAR = $year GROUP BY log_relation_no) log ON p.party_no = log.log_relation_no WHERE p.`status` = 1 and p.party_no = $relation_no");
                $integral_data = $integral_info[0];
            } else {
                $party_map['party_no'] = $relation_no;
                $integral_data = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,party_integral as integral_total')->find();
            }
        }else{
            if(!empty($year)){
                $year = date("Y");
                $integral_info = M()->query("SELECT c.party_no,c.communist_no, c.communist_name,IFNULL(log.integral_total,0) as integral_total FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 AND YEAR = $year GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no where c.communist_no = $relation_no");
                $integral_data = $integral_info[0];
            } else {
                $comm_map['communist_no'] = $relation_no;
                $integral_data = M('ccp_communist')->where($comm_map)->field('communist_no,communist_name,party_no,communist_integral as integral_total')->find();
            }
        }
        $data['integral_total'] = $integral_data['integral_total'];
        $data['integral_relation_no'] = $relation_no;
        if ($relation_type == 2){
            $data['party_name'] = getPartyInfo(getCommunistInfo($relation_no,'party_no'));
            $data['communist_name'] = getCommunistInfo($relation_no);
        }else{
            $data['party_name'] = getPartyInfo($relation_no);
        }
        return $data;
    }
}