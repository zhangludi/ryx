<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/2/5
 * Time: 15:08
 */

namespace Api\Validate;


class DateValidate extends Validate
{

    function __construct($time, $param, $msg = '时间格式错误错误')
    {
        if (!empty($param) && is_array($time) && !empty($time)) {
            $rule = [];
            foreach ($time as $value) {
                $rule[] = [$value, 'checkDateIsValid', $msg, 'callback', $param];
            }
            $this->rule = $rule;
        } else {
            $this->rule = [
                ['rule', 'require', '时间为空', 'regex']
            ];

        }
    }

    protected function checkDateIsValid($value, $msg, $param = 'Y-m-d') {
        $unixTime = strtotime($value);
        return var_dump($value);
        if (!$unixTime) {
            $this->error = $msg;
            return false;
        }
        if (date($param, $unixTime) == $value) {
            return true;
        }
        $this->error = $msg;
        return false;
    }
}