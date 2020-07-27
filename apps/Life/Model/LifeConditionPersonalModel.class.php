<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeConditionPersonalModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
//        array('integral_relation_type','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT)
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array(add_staff, 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s')
    );
    /**
     * @name:getConditionLogList
     * @desc：获取我的任务列表
     * @param：$party_no 部门编号
     * @return：部门志愿者列表
     * @author:王桥元
     * @addtime:2017-05-23
     * @version：V1.0.0
     **/
    function getConditionPersonalList($where){
    	$condition_data = $this->where($where)->select();
        return $condition_data;
    }
}