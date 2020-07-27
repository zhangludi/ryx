<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeVolunteerModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
//        array('integral_relation_type','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT)
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s')
    );
    /**
	 * @name:getCommunistVolunteerList()
	 * @desc：获取志愿者列表
	 * @param：$party_no 部门编号
	 * @return：部门志愿者列表
	 * @author:王桥元
	 * @addtime:2017-05-23
	 * @version：V1.0.0
	 **/
	function getCommunistVolunteerList($party_no,$keyword){
		$db_volunteer=M('life_volunteer');
		
		$where .= "status <> 0";
		if(!empty($party_no)){
			$where .= " and party_no = '$party_no'";
		}
		$volunteer_data = $db_volunteer->where($where)->select();
		if($volunteer_data){
			return $volunteer_data;
		}else{
			return null;
		}
	}
	/**
	 * @name:getCommunistVolunteerInfo()
	 * @desc：获取志愿者详情
	 * @param：$volunteer_id 志愿者申请id
	 * @return：部门志愿者列表
	 * @author:王桥元
	 * @addtime:2017-05-23
	 * @version：V1.0.0
	 **/
	function getCommunistVolunteerInfo($volunteer_id){
		$db_volunteer=M('life_volunteer');
		$volunteer_data = $db_volunteer->where("volunteer_id = $volunteer_id")->find();
		if($volunteer_data){
			return $volunteer_data;
		}else{
			return null;
		}
	}
}