<?php

namespace Ccp\Model;

use Common\Model\PublicModel;

class CcpCommunistChangeModel extends PublicModel
{


    /* 自动完成规则 */
    protected $_auto = array(
        array(add_staff, 'session', self::MODEL_INSERT, 'function', 'communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'),
        array('start_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'),
        array('update_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'),
        array('status', 1, self::MODEL_INSERT),
        array('change_audit_status', 10, self::MODEL_INSERT),
    );

    /**
     * @name:get_communist_change_list
     * @desc：获取党员流动列表
     * @param：
     * @return：
     * @author：王桥元
     * @addtime:2017-10-20
     * @version：V1.0.0
     **/
    function get_communist_change_list($type, $where,$page,$pagesize)
    {
        if (empty($where)) {
            $where = "1=1";
        }
        $change_data['data'] = $this->where($where)->order('add_time desc')->limit($page,$pagesize)->select();
        $change_data['count'] = M('ccp_communist_change')->where($where)->count();
        $change_data['code'] = 0;
        $change_data['msg'] = 0;
        return $change_data;
    }

    /**
     * @name:get_communist_change_info
     * @desc：获取流动信息详情
     * @param：
     * @return：
     * @author：王桥元
     * @addtime:2017-10-21
     * @version：V1.0.0
     **/
    function get_communist_change_info($change_id)
    {
        $change_data = $this->where("change_id = $change_id")->find();
        return $change_data;
    }

    /**
     *  isChange
     * @desc 判断是否存在转移流程
     * @param $communist_no
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function isChange($communist_no)
    {
        $result = $this->where("communist_no='$communist_no' and change_audit_status in (10,20,30)")->find();

        return $result;
    }
}