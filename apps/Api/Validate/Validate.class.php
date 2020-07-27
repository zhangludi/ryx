<?php
/**
 * 验证类
 * User: liubingtao
 * Date: 2018/1/29
 * Time: 上午11:19
 */

namespace Api\Validate;

class Validate
{

    protected $error;//错误信息

    protected $rule;//验证规则 [['验证字段', '验证规则', '提示信息', '规则类型']] regex function callback


    /**
     *  validate
     * @desc
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    final public function validate()
    {
        $rule = array_filter($this->rule);

        if (empty($rule) && !is_array($rule)) : $this->error = '规则为空或规则错误'; return false; endif;

        $data = I('request.');
        foreach ($rule as $value) {
            if (!empty($value[3])) {
                switch ($value[3]) {
                    case 'regex' :
                        $this->regex($data[$value[0]] ,$value[1], $value[2]);
                        break;
                    case 'function':
                        call_user_func_array($value[1], $data[$value[0]]);
                        break;
                    case 'callback':
                        call_user_func_array(array($this, $value[1]), ['value' => $data[$value[0]] , 'msg' => $value[2], 'param'=>$value[4]]);
                        break;
                    default:
                        $this->error = '规则类型错误';
                }
            }
        }

        if (isset($this->error)) :return false; endif;

        return true;

    }

    /**
     *  regex
     * @desc  使用正则验证数据
     * @param $value string 要验证的数据
     * @param $rule string 验证规则
     * @param $message string 提醒信息
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    final private function regex($value, $rule, $message) {
        if (isset($this->error)) : return; endif;
        $validate = array(
            'require'   =>  '/\S+/',
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency'  =>  '/^\d+(\.\d+)?$/',
            'number'    =>  '/^\d+$/',
            'zip'       =>  '/^\d{6}$/',
            'integer'   =>  '/^[-\+]?\d+$/',
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
            'english'   =>  '/^[A-Za-z]+$/',
        );
        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)])) :$rule = $validate[strtolower($rule)]; endif;
        if (preg_match($rule, $value) === 0) : $this->error = $message; endif;
        return;
    }

    /**
     *  getError
     * @desc 返回最后的错误信息
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *  isPositiveInteger
     * @desc 自定义正整数验证方法
     * @param $value
     * @param $msg
     * @return boolean
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    final protected function isPositiveInteger($value, $msg)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) >0) :return true ; endif;
        $this->error = $msg;
        return false;
    }


    /**
     *  goCheck
     * @desc 自动验证
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    final public function goCheck()
    {
        $result = $this->validate();

        if ($result) : return true; endif;

        header('Content-Type:application/json; charset=utf-8');

        exit(json_encode(['status' => '0' , 'msg' => $this->getError()]));
    }

}