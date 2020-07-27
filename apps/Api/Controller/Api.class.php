<?php
namespace Api\Controller;

use Think\Controller;

class Api extends Controller {

    public function __construct()
    {
        self::UserType();
        // 其他终端调用API接口是首先验证token
        // 需要多穿的参数 // token 密钥  terminal_no  终端编号  
        // 1. 首先验证是否是调用api模块的接口  调用get_terminal_token（获取token值方法）除外
        // if (MODULE_NAME === 'Api' && ACTION_NAME !== 'get_terminal_token') {
        //     $token = isset($_POST['token'])?$_POST['token']:''; // 获取token值
        //     if(!empty($token)){ // 验证token值
        //         // 开始验证token
        //         // 获取token
        //         $terminal_no = $_POST['terminal_no']; // 获取终端编号（代码）
        //         $terminal_token = session($terminal_no.'_TOKEN'); // 获取session中对应的终端token值
        //         // 验证token
        //         if(md5($terminal_token) === md5($_POST['token'])){ // session的token值跟传过来的token值进行比对
        //             $data['code'] = 1;
        //             $data['tocke'] = $token;
        //             $data['terminal_no'] = $terminal_no;
        //             $data['check_date'] = date('Y-m-d');
        //             // self::send('验证通过',$data,1);
        //         } else { // 验证不通过
        //             $data['code'] = 0;
        //             $data['tocke'] = $token;
        //             $data['terminal_no'] = $terminal_no;
        //             $data['check_date'] = date('Y-m-d');
        //             self::send('验证未通过',$data,0);
        //         }
        //     } else { // token值未传
        //         $data['code'] = 0;
        //         $data['tocke'] = $token;
        //         $data['terminal_no'] = '';
        //         $data['check_date'] = date('Y-m-d');
        //         self::send('验证未通过,请输入终端token值',$data,0);
        //     }
        // }
    }

    /**
     * @desc 
     * @name send
     * @param string $msg  错误原因
     * @param array $data 返回数据
     * @author ljj
     * @addtime 2018-2-11
     * @updatetime  2018-2-11
     * @version  V1.0.0
     */
    public function send($msg = '暂无数据', $data = [], $status = 0,$field="",$res="")
    {
    	if(!empty($field)){
    		$result = [
    		'status' => $status,
    		'msg' => $msg,
    		$field =>$res,
    		];
    	}else {
    		$result = [
    		'status' => $status,
    		'msg' => $msg,
    		];
    	}
        if (!empty($data)) : $result['data'] = $data; endif;
        ob_clean();$this->ajaxReturn($result, 'json');
    }

    
    /**
     * @desc 魔术方法 有不存在的操作的时候执行
     * @name  __call
     * @param string $method 方法名
     * @param array $args 参数
     * @author ljj
     * @addtime 2018-2-11
     * @updatetime  2018-2-11
     * @version  V1.0.0
     */
    public function __call($method, $args)
    {
        self::send($method.'方法不存在,请联系开发人员');
    }

    /**
     * @desc  登陆人员类型
     * @name  UserType
     * @param string $method 方法名
     * @param array $args 参数
     * @author ljj
     * @addtime 2018-2-11
     * @updatetime  2018-2-11
     * @version  V1.0.0
     */
    private function UserType()
    {
        defined('COMMUNIST') or define('COMMUNIST', 1);//党员
        defined('MEMBER') or define('MEMBER', 2);//非党员
    }
}