<?php
/**
 * 党员编号验证
 * User: liubingtao
 * Date: 2018/1/29
 * Time: 下午4:15
 */

namespace Api\Validate;


class CommunistNoValidate extends Validate
{
    protected $rule = [
        ['communist_no', 'isExist', '人员不存在', 'callback'],
        ['communist_no', 'isPositiveInteger', '编号错误', 'callback'],

    ];

    public function __construct($no = null)
    {
        if (!empty($no)){
            $this->rule = [
                [$no, 'isExist', '人员不存在', 'callback'],
                [$no, 'isPositiveInteger', '编号错误', 'callback'],
            ];
        }
    }



    protected function isExist($value, $msg)
    {
        $result = getCommunistInfo($value);

        if ($result) : return true; endif;

        $this->error = $msg;

        return false;
    }

}