<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeConditionModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
        array('condition_title', 'require', '标题为空', self::MODEL_INSERT),
        array('condition_content', 'require', '内容为空', self::MODEL_INSERT),
        array('condition_personnel', 'require', '姓名为空', self::MODEL_INSERT),
        array('condition_personnel_mobile', 'require', '手机号为空', self::MODEL_INSERT),
        array('condition_area', 'require', '地址为空', self::MODEL_INSERT),
        array('type_no', 'require', '写入失败', self::MODEL_INSERT),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 0, self::MODEL_INSERT, 'string'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
    	array('update_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s')
    );
    /**
	 * @name:getConditionList
	 * @desc：获取民情列表
	 * @param：
	 * @return：
	 * @author:王桥元
	 * @addtime:2017-05-23
	 * @version：V1.0.0
	 **/
	function getConditionList($where,$page,$pagesize){
		$condition_data['data'] = $this->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
		$condition_data['count'] = $this->where($where)->count();
        return $condition_data;
	}
	/**
	 * @name:getConditionInfo
	 * @desc：获取民情详情
	 * @param：
	 * @return：
	 * @author:王桥元
	 * @addtime:2017-05-23
	 * @version：V1.0.0
	 **/
	function getConditionInfo($condition_id){
		$condition_data = $this->where("condition_id = $condition_id")->find();
		if($condition_data){
			return $condition_data;
		}else{
			return null;
		}
	}
}