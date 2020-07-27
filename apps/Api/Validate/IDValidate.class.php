<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/2
 * Time: 上午10:19
 */

namespace Api\Validate;


class IDValidate extends Validate
{
    function __construct($model, $id, $msg = 'id错误')
    {
        if (!empty($model) && !empty($id)) {
            $this->rule = [
                [$id, 'isCheck', $msg, 'callback', $model]
            ];
        } else {
            $this->rule = [
                ['rule', 'require', 'id为空', 'regex']
            ];

        }
    }

    protected function isCheck($value, $msg, $param)
    {
        $db = M($param);

        $pk = $db->getPk();

        $result = $db->where("$pk=$value")->find();

        if ($result) : return true; endif;

        $this->error = $msg;

        return false;
    }
}