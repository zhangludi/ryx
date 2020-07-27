<?php
/**
 * 登陆判断
 * User: liubingtao
 * Date: 2018/1/29
 * Time: 上午11:26
 */

namespace Api\Validate;


class LoginValidate extends Validate
{
    // 党员
    protected $communist = [
        ['party_no', 'require', '支部编号为空', 'regex'],
        ['communist_name', 'require', '党员姓名为空', 'regex'],
        ['communist_idnumber', 'require', '党员身份证为空', 'regex']
    ];

    //管理员
    protected $member = [
        ['party_no', 'require', '支部编号为空', 'regex'],
        ['communist_name', 'require', '党员姓名为空', 'regex']
    ];

    public function __construct($type)
    {
        switch ($type) {
            case COMMUNIST :
                $rule = $this->communist;
                break;
            case MEMBER :
                $rule = $this->member;
                break;
            default :
                $rule = [['rule', 'require', '暂无此登陆类型', 'regex']];
                break;
        }
        $this->rule = $rule;
    }
}