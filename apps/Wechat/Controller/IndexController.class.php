<?php
namespace Wechat\Controller;
use Think\Controller;
use Wxjssdk\JSSDK;
class IndexController extends Controller
{
	/**
	* @name:index
	* @desc：
	* @author：王宗彬
	* @addtime:2018-04-25
	* @version：V1.0.0
	**/
	public function index()
	{
		$ad_list = getbannerList(1);
		$communist_no = session('wechat_communist');
		$artic_list = getArticleList('30','0','6');
		$array = array();
		foreach ($artic_list['data'] as $item){
			$list['article_id'] = $item['article_id'];
			$list['article_title'] = $item['article_title'];//mb_substr(strip_tags($item['article_title']),0,15,'utf-8');
			$list['article_thumb'] = getUploadInfo($item['article_thumb']);
			$list['add_time'] = date('Y-m-d',strtotime($item['add_time']));
			$list['add_staff'] = peopleNoName($item['add_staff']);
			$array[] = $list;
		}
		$this->assign('ad_list',$ad_list);
		if(!empty($communist_no)){
			$communist_name = getCommunistInfo($communist_no,'communist_name');
			$party_name = getPartyInfo(getCommunistInfo($communist_no,'party_no'),'party_name');
			$communist_avatar = getCommunistInfo($communist_no,'communist_avatar');
			if(empty($communist_avatar)){
				$communist_avatar = "/statics/public/images/default_photo.jpg";
			}
			$this->assign('communist_avatar',$communist_avatar);
			$communist_integral = round(getCommunistInfo($communist_no,'communist_integral'));
			$this->assign('communist_name',$communist_name);
			$this->assign('party_name',$party_name);
			$this->assign('communist_integral',$communist_integral);
		}
		
		$this->assign('communist_no',$communist_no);
		$this->assign('array',$array);

		$communist_no = session('wechat_communist');
        $map['_string'] = "FIND_IN_SET('$communist_no',alert_man)";
        $map['status'] = 0;
        $alert_count=M('bd_alertmsg')->where($map)->order('add_time DESC')->count('alert_id');
        $this->assign('alert_count',$alert_count);
		$this->display('Index/index');
	}
	/**
	 * @party_interaction
	 * @desc：
	 * @author：王宗彬
	 * @addtime:2018-04-28
	 * @version：V1.0.0
	 **/
	public function party_interaction()
	{
		$ad_list = getbannerList(5);
		$this->assign('ad_list',$ad_list);
		$this->display('Index/party_interaction');
	}	
	
	
	/**
	* @register
	* @desc：
	* @author：刘长军
	* @addtime:2019-02-22
	* @version：V1.0.0
	**/
	public function register()
	{
		$this->display('Index/register');
	}
	/**
	* @register
	* @desc：注册
	* @author：刘长军
	* @addtime:2019-02-22
	* @version：V1.0.0
	**/
	public function register_save(){
		// $post = $_POST;
		// dump($post);die;
		$where['communist_name'] = array('eq',I('post.communist_name'));
        $where['communist_idnumber'] = array('eq', I('post.communist_idnumber'));
        $username= I('post.username');
        $password= I('post.password');
        $phone = I('post.phone');
        $result = M('ccp_communist')->where($where)->getField('communist_no');
        
        if (empty($result)) {
            showMsg('error', '信息错误');
        }else{
        	$map_name['username'] = $username;
            $username_no = M('ccp_communist')->where($map_name)->find();
            if(!empty($username_no)){
            	showMsg('error', '用户名重复');
            }else{
            	$no_map['communist_no'] = $result;
	            $communist_id = M('ccp_communist')->where($no_map)->getField('communist_id');
	            $data['communist_id'] = $communist_id;
	            $data['username'] = $username;
	            $data['communist_mobile'] = $phone;
	            $data['password'] = md5($password);
	            $index = M('ccp_communist')->save($data);
	            if($index){
	                showMsg('success', '注册成功！',U('Index/login'));
	            }else{
	                showMsg('error', '注册失败');
	            }
            }
            
        }
	}

	/**
	* @register
	* @desc：手机号验证码
	* @author：刘长军
	* @addtime:2019-02-23
	* @version：V1.0.0
	**/
	public function ajax_phone(){
		$phone = I('post.phone');
		$code = mt_rand(999,9999);
        $data['success'] = aliyun_send($phone,'TemplateCode1',['code'=>$code]);
        if($data){
        	$data['success'] = 'ok';
        	$data['code_no'] = $code;
        }
        $this->ajaxReturn($data);
	}
	/**
	* @login
	* @desc：
	* @author：王宗彬
	* @addtime:2018-04-28
	* @version：V1.0.0
	**/
	public function login(){
		$party_list = M('ccp_party')->field('party_no,party_name')->select();
		$this->assign('party_list',$party_list);
		$this->display('Index/login');
	}
	/**
	* @check_login
	* @desc：
	* @author：王宗彬
	* @addtime:2018-04-28
	* @version：V1.0.0
	**/
	public function check_login()
	{
		$db_communist = M('ccp_communist');
        $username = I('post.username');
        $password = md5(I('post.password'));
        $communist_name = $db_communist->where("username='$username'")->getField('communist_name');
        if(!empty($communist_name)){
        	$result = $db_communist->where("username='$username' and password='$password'")->field('communist_no,communist_name as user_name')->find();
        	$people_no = peopleNo($result['communist_no'],1);
        	session('wechat_people',$people_no);
        	if ($result) {
				session('wechat_communist',$result['communist_no']);
				showMsg('success', '登录成功！','Index/index');
			}else{
				showMsg('error', '密码错误！');
			}
        }else{
        	 	showMsg('error', '账号错误！');
        }
		
	}
	/**
	* @name:store_index
	* @desc：超市
    * @author：王宗彬
	* @addtime:2019-06-05
	* @version：V1.0.0
	**/
	public function store_index()
	{
		$communist_no = session('wechat_communist');
		$this->assign('communist_no',$communist_no);
		$this->display('Index/store_index');
	}
		
	
}