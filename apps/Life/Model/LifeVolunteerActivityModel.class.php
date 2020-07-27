<?php

namespace Life\Model;

use Common\Model\PublicModel;

class LifeVolunteerActivityModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array(//        array('integral_relation_type','require','写入失败',self::EXISTS_VALIDATE,self::MODEL_INSERT)
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 11, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function', 'communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'),
        array('update_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s')
    );

    /**
     * @name:getCommunistVolunteerCatgroyList
     * @desc：获取志愿活动列表
     * @param： $party_no 部门编号
     * @return：部门志愿活动列表
     * @author:王桥元
     * @addtime:2017-05-23
     * @version：V1.0.0
     **/
    function getCommunistVolunteerActivityList($party_no,$activity_title,$page,$pagesize)
    {
        $where['status'] = array('neq',0);
        if (!empty($party_no)) {
            $where['party_no'] = array('find_in_set',$party_no);
        }
        if(!empty($activity_title)){
             $where['activity_title'] = array('like',"%$activity_title%");
        }
        if(!empty($pagesize)){
           $volunteer_data['data'] = $this->where($where)->Order("add_time desc")->limit($page,$pagesize)->select(); 
           $volunteer_data['count'] = $this->where($where)->count(); 

        }else{
            $volunteer_data = $this->where($where)->Order("add_time desc")->select(); 
        }
        return $volunteer_data;
    }

    /**
     * @name:getActivityinfo
     * @desc：获取志愿者活动详情
     * @param： $activity_id：活动id
     * @return：活动闲详情
     * @author:王桥元
     * @addtime:2017-05-23
     * @version：V1.0.0
     **/
    function getActivityinfo($activity_id, $field = "all")
    {
        if ($field == "all") {
            $volunteer_data = $this->where("activity_id = $activity_id")->find();
        } else {
            $volunteer_data = $this->where("activity_id = $activity_id")->field($field)->find();
        }
        return $volunteer_data;
    }

    /**
     * @name:getActivityAmount
     * @desc：获取志愿者活动人员
     * @param： $activity_id：活动id
     * @return：true/false
     * @author:王桥元
     * @addtime:2017-05-23
     * @version：V1.0.0
     **/
    function getActivitycommunistlist($activity_id)
    {
        $volunteer_data = $this->where("activity_id = $activity_id")->select();
        return $volunteer_data;
    }
}