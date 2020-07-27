<?php
/**
 * 必填判断
 * User: liubingtao
 * Date: 2018/1/29
 * Time: 下午2:23
 */

namespace Api\Validate;


class RequireValidate extends Validate
{
    public function __construct($keys)
    {
        if (!empty($keys) && is_array($keys)) {
            $rule = [];
            foreach ($keys as $value) {
                $rule[] = [$value, 'require', $value.'不能为空', 'regex'];
            }

            $this->rule = $rule;
        } else {
            $this->error = '参数错误';
        }

    }
}