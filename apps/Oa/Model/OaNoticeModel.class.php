<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/1
 * Time: 下午4:34
 */

namespace Oa\Model;


use Common\Model\PublicModel;

class OaNoticeModel extends PublicModel
{
    protected $_auto = [
        ['status', 1, self::MODEL_INSERT, 'int'],
        ['add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'],
        ['update_time', 'date', self::MODEL_BOTH, 'function', 'Y-m-d H:i:s']
    ];
}