<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/1/29
 * Time: 下午4:27
 */

namespace Api\Validate;


class NumberValidate extends Validate
{
    public function __construct($keys)
    {
        if (!empty($keys) && is_array($keys)) {
            $rule = [];
            foreach ($keys as $value) {
                $rule[] = [$value, 'isPositiveInteger', $value.'错误', 'callback'];
            }

            $this->rule = $rule;
        } else {
            $this->error = '参数错误';
        }

    }
}