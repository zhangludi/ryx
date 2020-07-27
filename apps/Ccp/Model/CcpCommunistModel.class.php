<?php

namespace Ccp\Model;

use Common\Model\PublicModel;
use  Composer\Autoload\includeFile;

class CcpCommunistModel extends PublicModel
{
    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array(add_staff, 'session', self::MODEL_INSERT, 'function', 'communist_no'),
        array('add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'),
        array('update_time', 'date', self::MODEL_UPDATE, 'function', 'Y-m-d H:i:s')
    );

    public function getCommunistInfo($communist_no)
    {
        if (empty($communist_no)) : return false; endif;
        $info = $this->where("communist_no=$communist_no")->find();
        if (empty($info)) : return false; endif;
        $info['communist_diploma'] = getBdCodeInfo($info['communist_diploma'], "diploma_level_code");
        $info['party_no'] = getPartyInfo($info['party_no']);
        $info['post_no'] = getPartydutyInfo($info['post_no']);
        $info['communist_birthday'] = getFormatDate($info['communist_birthday'], "Y年m月d日");
        $info['status'] = getStatusName('communist_status',$info['status']);//状态
        $info['communist_sex'] = intval($info['communist_sex']) === 1 ? '男' : '女';
        $info['communist_ccp_date'] = getFormatDate($info['communist_ccp_date'], 'Y年m月d日');
        $info['communist_nation'] = getTableInfo('bd_nation', 'nation_id', $info['communist_nation'], 'nation_name');

        $dev_log_list = getCommunistLogList($communist_no, '10');//党员发展历程
        $communist_log_list = getCommunistLogList($communist_no, COMMUNIST_STATUS_COURSE);//党员历程
        
        return ['info' => $info, 'dev_log' => $dev_log_list, 'communist_log' => $communist_log_list];
    }
}