<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/4
 * Time: 11:06
 */

namespace Allinone\Controller;


use Think\Controller;

class IndexController extends Controller
{
    /**
     *  _initialize
     * @desc 构造函数
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function _initialize()
    {
        $door_communist_no = session('door_communist_no');
        if (!empty($door_communist_no)) {
            $this->assign('is_login', 1);
        } else {
            $this->assign('is_login', 0);
        }
    }

    /**
     *  index
     * @desc 首页
     * @user 王宗彬
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function index()
    {
        layout(false);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display('index');
    }
    /**
     * community_service
     * @desc 社区服务
     * @user 王宗彬
     * @date 2019/6/20
     * @version 1.0.0
     */
    public function community_service()
    {
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display('community_service');
    }
    /**
     * livelihood_service
     * @desc 民生服务
     * @user 王宗彬
     * @date 2019/6/20
     * @version 1.0.0
     */
    public function livelihood_service()
    {
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
         $cat_list = M('cms_article_category')->where("cat_pid=14 and status=1")->field('cat_id,cat_name')->select();
        $this->assign('cat_list', $cat_list);
        $this->display('livelihood_service');
    }

    /**
     *  login
     * @desc 登录页
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function login()
    {
        layout(false);
        $web_name = getConfig('web_name');
        $this->assign('web_name', $web_name);
        $this->display();
    }

    /**
     *  login_save
     * @desc 登录操作
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function login_save()
    {
        $where['party_no'] = array('eq', I('post.party_no'));
        $where['communist_name'] = array('eq', I('post.communist_name'));
        $where['communist_idnumber'] = array('eq', I('post.communist_idnumber'));
        $result = M('ccp_communist')->where($where)->getField('communist_no');
        if (empty($result)) {
            showMsg('error', '信息错误');
        } else {
            session('door_communist_no', $result);
            showMsg('success', '登录成功', U('index'), 1);
        }
    }

    public function logout()
    {
        session('[destroy]'); // 清除服务器的sesion文件
        showMsg('success', '已成功退出系统', U('index'));
    }
    /**
     * check_login
     * @desc 检查登陆
     * @user 王宗彬
     * @date 2018/4/21
     * @version 1.0.0
     */
    public function check_login(){
    	if(session('door_communist_no')){
    		ob_clean();$this->ajaxReturn(false);
    	}else{
    		ob_clean();$this->ajaxReturn(true);
    	}
    }
}