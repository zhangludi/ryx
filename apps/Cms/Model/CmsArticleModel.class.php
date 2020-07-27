<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/1
 * Time: 下午6:05
 */

namespace Cms\Model;


use Common\Model\PublicModel;

class CmsArticleModel extends PublicModel
{
    protected $_auto = [
        ['add_time', 'date', self::MODEL_INSERT, 'function', 'Y-m-d H:i:s'],
        ['update_time', 'date', self::MODEL_BOTH, 'function', 'Y-m-d H:i:s']
    ];
}