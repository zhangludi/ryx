<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/4
 * Time: 11:06
 */
namespace Index\Controller;
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
        $where['status'] = '1';
        $nav_list = M('sys_nav')->where($where)->order('nav_order asc')->select();
        foreach ($nav_list as  $key=>&$list) {
            $list['nav_url'] = U($list['nav_url']);
        }
        $this->assign('nav_list',$nav_list);
        $where['status'] = '1';
        $blogroll_list = M('sys_blogroll')->where($where)->select();
        foreach ($blogroll_list as  &$list) {

            if($list['blogroll_type'] == '1'){
                $list['code'] = "<li><a href='".$list['blogroll_url']."'>".$list['blogroll_name']."</a> </li>";
            }else{
                $list['code'] =  "<li><a href='".U($list['blogroll_url'])."' target='_blank'>".$list['blogroll_name']."</a></li>";
            }
        }
        $this->assign('blogroll_list',$blogroll_list);
    }

    /**
     *  index
     * @desc 门户首页
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function index()
    {		
        $today_article = M('cms_article')->where("article_point = 1")->field("article_id,article_title")->limit(10)->order("update_time desc")->select();
        $this->assign('today_article', $today_article);
        $is_lose = I('get.is_lose');
        $door_communist_no = session('door_communist_no');
        if(!empty($is_lose) && empty($door_communist_no)){
            $this->assign('is_lose', $is_lose);
        }
        $this->display();
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
        $this->display();
    }
     /**
     *  register
     * @desc 注册页
     * @user 刘长军
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function register(){
        layout(false);
        $this->display();
    }
    /**
     *  register_save
     * @desc 注册页
     * @user 刘长军
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function register_save(){
        // dump($_POST);DIE;
        $where['communist_name'] = array('eq',I('post.communist_name'));
        $where['communist_idnumber'] = array('eq', I('post.communist_idnumber'));
        $username= I('post.username');
        $password= I('post.password');
        $phone = I('post.phone');
        $result = M('ccp_communist')->where($where)->getField('communist_no');
        if (empty($result)) {
            showMsg('error', '信息错误');
        }else{
            $no_map['communist_no'] = $result;
            $communist_id = M('ccp_communist')->where($no_map)->getField('communist_id');
            $data['communist_id'] = $communist_id;
            $data['username'] = $username;
            $data['communist_mobile'] = $phone;
            $data['password'] = md5($password);
            $index = M('ccp_communist')->save($data);
            if($index){
                showMsg('success', '注册成功', U('index'), 1);
            }else{
                showMsg('error', '注册失败');
            }
        }
    }
    /**
     *  register_verification
     * @desc ajax短信验证
     * @user 刘长军
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function register_verification(){
        $phone = I('post.phone');
        $code = mt_rand(999,9999);
        $data['success'] = aliyun_send($phone,'TemplateCode1',['code'=>$code]);
        if($data['success']){
            $data['success'] = 'ok';
            $data['code_no'] = $code;
        }
        $this->ajaxReturn($data);
    }
     /**
     *  register_phone
     * @desc ajax手机号验证重复
     * @user 刘长军
     * @date 2019/2/21
     * @version 1.0.0
     */
    public function register_phone(){
        $where['communist_name'] = I('post.communist_name');
        $where['communist_idnumber'] = I('post.communist_idnumber');
        $communist_mobile = M('ccp_communist')->where($where)->getField('communist_mobile');
        $this->ajaxReturn($communist_mobile);
    }
     /**
     *  register_phone
     * @desc ajax用户名验证重复
     * @user 刘长军
     * @date 2019/2/21
     * @version 1.0.0
     */
    public function register_username(){
        $where['username'] = I('post.username');
        $username = M('ccp_communist')->where($where)->find();
        $this->ajaxReturn($username);
    }
    // /**
    //  *  login_save
    //  * @desc 登录操作
    //  * @user liubingtao--
    //  * @date 2018/4/8
    //  * @version 1.0.0
    //  */
    // public function login_save()
    // {
    //     $where['party_no'] = array('eq', I('post.party_no'));
    //     $where['communist_name'] = array('eq', I('post.communist_name'));
    //     $where['communist_idnumber'] = array('eq', I('post.communist_idnumber'));
    //     $result = M('ccp_communist')->where($where)->getField('communist_no');
    //     if (empty($result)) {
    //         showMsg('error', '信息错误');
    //     } else {
    //         session('door_communist_no', $result);
    //         showMsg('success', '登录成功', U('index'), 1);
    //     }
    // }
    /**
     *  login_save
     * @desc 登录操作
     * @user 刘长军
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function login_save(){
        // $where['party_no'] = array('eq', I('post.party_no'));
        $where['username'] = array('eq', I('post.username'));
        $where['password'] = array('eq', md5(I('post.password')));
        $result = M('ccp_communist')->where($where)->getField('communist_no');
        $people = peopleNo($result);
        if (empty($result)) {
            showMsg('error', '账号密码或党组织错误');
        } else {
            session('people_no_door',$people);
            session('door_communist_no', $result);
			$this->redirect("index"); 
        }
    }
    public function logout(){
		unset($_SESSION['door_communist_no']);
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