<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/8/14
 * Time: 10:28
 */

namespace Common\Controller;

use Think\Controller;

class BaseController extends Controller
{
    protected $user_id = null;
    protected $staff_no; 
   

    public function _initialize()
    {
        $staff_no = session('staff_no');
        $cookie_user_id = cookie('user_id');
        $session_user_id = session('user_id');
        $party_no_auth = session('party_no_auth');
        if(empty($staff_no) || empty($session_user_id) || empty($party_no_auth) || empty($cookie_user_id)){
            $check_res = U(C('JUMP_MODULE').'/Public/'.LOGIN_ACTION);
            echo "<script>parent.location.href='".$check_res."';</script>";
        } else {
            $this->user_id = $session_user_id;
            $this->staff_no =$staff_no;
            if(empty($party_no_auth)){
                // 有权限查看全部党组织
                $party_no_arr=M("ccp_party")->where('status = 1')->field('party_no')->select();
                if(!empty($party_no_arr)){
                    foreach ($party_no_arr as $party_val) {
                        $party_nos_arr[] = $party_val['party_no'];
                    }
                    $party_nos_str=arrToStr($party_nos_arr);
                }
                session('party_no_auth',  $party_nos_str); // 当前用户可以查看的党组织
            }
        } 
        // 查找本站配置的地图AK
        $map_ak = getConfig('web_map_ak');
        $this->assign('web_map_ak',$map_ak);
    }
}