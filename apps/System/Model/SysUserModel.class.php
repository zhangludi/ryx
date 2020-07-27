<?php

namespace System\Model;

use Think\Model;
use Common\Model\PublicModel;

class SysUserModel extends PublicModel
{
    /* 自动验证规则 */
    protected $_validate = array (
        array('user_relation_no','','选择人员重复！',self::EXISTS_VALIDATE,'unique'),
        array("user_name", "", '帐号名称已经存在！',self::EXISTS_VALIDATE,'unique'),

    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('add_staff', 'session', self::MODEL_INSERT, 'function','communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function','Y-m-d H:i:s'),
        array('update_time', 'date', self::MODEL_UPDATE, 'function','Y-m-d H:i:s'),
    );

    /**
     * @name  getUserinfo()
     * @desc  获取用户信息
     * @param $user_id string 用户id
     * @return   array     用户信息
     * @author 刘丙涛
     * @addtime   2017-10-13
     * @version 1.0.0
     */
    public function getUserinfo($user_id){

        $user_data = $this->selectData('0',"user_id=$user_id");
        if ($user_data){
            $user_data['communist_name'] = getStaffInfo($user_data['user_relation_no']);
            return $user_data;
        }else{
            showMsg('error','获取失败');
        }
    }
    /**
     * @name  setUserstatus()
     * @desc  修改用户状态
     * @param
     * @return   true/false
     * @author 刘丙涛
     * @addtime   2017-10-13
     * @version 1.0.0
     */
    public function setUserstatus(){
        $data = I('get.');
        $result = $this->updateData($data,'user_id');
        if ($result){
            return true;
        }else{
            return false;
        }
    }
}