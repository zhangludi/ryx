<?php
/**
 * 公共基础
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:34
 */

namespace Api\Controller;

use Api\Validate\LoginValidate;
use Api\Validate\RequireValidate;

class PublicController extends Api
{
    /**
     * check_login
     * @desc 用户登录
     * @param int type 1党员2非党员
     * @param int party_no 支部编号
     * @param string communist_name 党员姓名
     * @param string communist_idnumber 身份证
     * @param int phone 手机号
     * @param int user_pwd 密码
     * @author liubingtao
     * @addtime:2018-01-27
     * @version：V1.0.0
     **/
    public function check_login()
    {
        $type = I('post.type');

        if (empty($type)) : $this->send('登陆类型为空'); endif;

        (new LoginValidate($type))->goCheck();

        switch ($type) {
            case COMMUNIST :
                $db_communist = M('ccp_communist');
                $party_no = I('post.party_no');
                $communist_name = I('post.communist_name');
                $communist_idnumber = I('post.communist_idnumber');
                $result = $db_communist->where("party_no='$party_no' and communist_name='$communist_name' 
                            and communist_idnumber='$communist_idnumber'")
                            ->field('communist_no,communist_name as user_name')->find();
                if ($result) {
                    $db_volunteer = M("life_volunteer");
                    $db_secretary = M('ccp_secretary');
                    $db_secretary_sign = M('ccp_secretary_sign');
                    $today = date('Y-m-d');
                    $is_volunteer = $db_volunteer->
                                    where("communist_no = {$result['communist_no']}")->getField('status');
                    $is_secretary = $db_secretary->
                                    where("communist_no = {$result['communist_no']}")->getField('status');
                    $is_secretary_sign = $db_secretary_sign->
                                    where("communist_no = {$result['communist_no']} and add_time > '$today'")->find();
                    $result['is_volunteer'] = empty($is_volunteer) ? 0 : 1;

                    $result['is_secretary'] = empty($is_secretary) ? 0 : 1;

                    $result['is_secretary_sign'] = !empty($is_secretary_sign) ? 1 : 0;
                }
                break;
            case MEMBER :
                $db_user = M('sys_user');
                $phone = I('post.phone');
                $user_pwd = I('post.user_pwd');
                $result = $db_user->where("user_name='$phone' and user_pwd='$user_pwd'")->
                            field('user_name,user_id')->find();
                break;
            default :
                $result = null;
        }
        if ($result) {
            $this->send('登陆成功', $result, 1);
        }else {
            $this->send('无此用户');
        }
    }
    /**
     * check_login_app
     * @desc 用户登录app
     * @param int type 1党员2非党员
     * @param int party_no 支部编号
     * @param string communist_name 党员姓名
     * @param string communist_idnumber 身份证
     * @param int phone 手机号
     * @param int user_pwd 密码
     * @author 王宗斌
     * @addtime:2018-01-27
     * @version：V1.0.0
     **/
    public function check_login_app()
    {
        $type = I('post.type');

        if (empty($type)) : $this->send('登陆类型为空'); endif;
        // (new LoginValidate($type))->goCheck();
        switch ($type) {
            case 1 :
                $db_communist = M('ccp_communist');
                $username = I('post.username');
                $password = md5(I('post.password'));
                $data_password = $db_communist->where("username='$username'")->getField('password');
                if(!empty($data_password)){
                    if($data_password == $password){
                        $result = $db_communist->where("username='$username'and password='$password'")->field('communist_no,communist_name as user_name,party_no')->find();
                        if ($result) {
                            $db_volunteer = M("life_volunteer");
                            $db_secretary = M('ccp_secretary');
                            $db_secretary_sign = M('ccp_secretary_sign');
                            $today = date('Y-m-d');
                            $where['communist_no'] = $result['communist_no'];
                            $is_volunteer = $db_volunteer->
                                            where($where)->getField('status');
                            $is_secretary = $db_secretary->
                                            where($where)->getField('status');
                                            $where['add_time'] =array('GT',$today);
                            $is_secretary_sign = $db_secretary_sign->
                                            where($where)->find();
                            if($is_volunteer == '2'){
                                $result['is_volunteer'] = '1';
                            }else{  
                                $result['is_volunteer'] = '0';
                            }
                                //$result['is_volunteer'] = empty($is_volunteer) ? 0 : 1;
                            $result['is_secretary'] = empty($is_secretary) ? 0 : 1;

                            $result['is_secretary_sign'] = !empty($is_secretary_sign) ? 1 : 0;
                        }
                    }else{
                         $this->send('密码错误');
                    }
                }else{
                    $this->send('用户名错误');
                }
                break;
            case 2 :
                $db_user = M('sys_user');
                $phone = I('post.phone');
                $user_pwd = I('post.user_pwd');
                $result = $db_user->where("user_name='$phone' and user_pwd='$user_pwd'")->
                            field('user_name,user_id')->find();
                break;
            default :
                $result = null;
        }
        if ($result) {
            $this->send('登陆成功', $result, 1);
        }else {
            $this->send('无此用户');
        }
    }
    /*register_save
    * @desc app注册
    * @user 刘长军
    * @date 2019/2/23
    * @version 1.0.0
    */
    public function register_save(){
        $where['communist_name'] = array('eq',I('post.communist_name'));
        $where['communist_idnumber'] = array('eq', I('post.communist_idnumber'));
        $result = M('ccp_communist')->where($where)->getField('communist_no');
        $communist_id = M('ccp_communist')->where($where)->getField('communist_id');
        $username= I('post.username');
        $password= I('post.password');
        $phone = I('post.phone');
        if(empty($result)){
             $this->send('信息错误','1', 1);
        }else{
            $map_name['username'] = $username;
            $username_no = M('ccp_communist')->where($map_name)->find();
            if($username_no != null){
                $this->send('用户名重复','2', 1);
            }else{
                $map_post['communist_no'] = $result;
                $map_post['username'] = $username;
                $map_post['communist_id'] = $communist_id;
                $map_post['password'] = md5($password);
                $post = M('ccp_communist')->save($map_post);
                $dd['communist_no'] = $result;
                $dd['type'] = '3';
                $this->send('注册成功',$dd, 1);
            }
        }
    }
    /*register_auth_code
    * @desc app验证码
    * @user 刘长军
    * @date 2019/2/23
    * @version 1.0.0
    */
    public function register_auth_code(){
        $phone = I('post.phone');
        $code = mt_rand(999,9999);
        $data['success'] = aliyun_send($phone,'TemplateCode1',['code'=>$code]);
        if($data['success']){
            $data['success'] = 'ok';
            $data['code_no'] = $code;
        }
        $this->send('发送成功',$data['code_no'], 1);
    }
    /**
     *  get_bd_code_list
     * @desc 获取code列表
     * @param string code_group
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_bd_code_list()
    {
        (new RequireValidate(['code_group']))->goCheck();

        $maps['code_group'] = ['eq', I("post.code_group")];
        $maps['status'] = ['eq', 1];
        $db_code = M('bd_code');

        $code_list = $db_code->where($maps)->field('code_no,code_name')->select();
        if ($code_list) {
           $this->send('获取成功', $code_list, 1);
        } else {
            $this->send();
        }
    }

	/**
     *  get_bd_type_list
     * @desc 获取type列表
     * @param string type_group
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_bd_type_list(){
        (new RequireValidate(['type_group']))->goCheck();
        $type_group = I('post.type_group');
        $type_no = I("post.type_no");
        $maps['type_group'] = ['eq', $type_group];
        if (!empty($type_no)) {
            $maps['type_no'] = array('in',$type_no);
        }
        $bd_type = M ( 'bd_type' );
        $type_list = $bd_type->where($maps)->field('type_no,type_name')->select();
        if($type_group == 'meeting_type'){
            $where['communist_no']=I('post.communist_no');
            $meeting_nos = M('oa_meeting_communist')->where($where)->getField("meeting_no",true);
            $meeting_nos = implode(',',$meeting_nos);
            $map['_string'] = "meeting_no in($meeting_nos)";
            $status = I("post.status");
            $status = $status == 1 ? '11,21' : '23';
            if (!empty($status)){
                $map['status'] = array('in',$status);
            }
            foreach ($type_list as &$list) {
                $map['meeting_type'] = $list['type_no'];
                $list['count'] = M('oa_meeting')->where($map)->count();
            }
        }
        if ($type_list) {
            $this->send('获取成功', $type_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_bd_status_list
     * @desc 获取基础状态
     * @param string status_group
     * @user liubingtao
     * @date 2018/1/29
     * @version 1.0.0
     */
    public function get_bd_status_list()
    {
        (new RequireValidate(['status_group']))->goCheck();

        $status_group = I("post.status_group");

        $status_list = getStatusList($status_group);
        if ($status_list) {
            $this->send('获取成功', $status_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_config
     * @desc 获取config配置
     * @param string config_code
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_config()
    {
        (new RequireValidate(['config_code']))->goCheck();

        $config_code = I("post.config_code");

        $sys_config = M('sys_config');

        $config_value = $sys_config->where("config_code = '$config_code'")->getField('config_value');

        if ($config_value) {
            $this->send('获取成功', $config_value, 1);
        } else {
            $this->send();
        }
    }
    
    /**
     * @name  getToken()
     * @desc  获取电视token
     * @param
     * 		@$user_tv_name(电视用户名)
     * 		@$user_tv_pwd (电视用户密码)
     * @return token
     * @author 王桥元
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2017-08-25
     */
    function getToken(){
    	$tv_video = "http://".$_SERVER['HTTP_HOST']."/uploads/video/";
    	//获取视频路径
    	$tvurl = $tv_video.getConfig("tvurl");
    	//获取appkey
    	$tv_appkey = getConfig("tv_appkey");
    	//获取token值
    	$tvsecret = getConfig("tvsecret");
    	if(!empty($tvsecret)){
    		$data['data']['secret'] = $tvsecret;
    		$data['data']['appkey'] = $tv_appkey;
    		$data['data']['tvurl'] = $tvurl;
    		$data['status'] = '1';
    		$data['msg'] = '登录成功';
    	}else{
    		$data['status'] = '0';
    		$data['msg'] = '本系统后台未设置token值，请至后台系统设置中添加';
    	}
    	ob_clean();$this->ajaxReturn($data);
    }

    /**
     *
     * @name    file_upload 说明 文件上传的服务器端方法
     * @param   function_code（模块） 直接从uploader页面（实例化时）server地址栏传入
     * @param   type （类型） 直接从uploader页面（实例化时）server地址栏传入
     * @return $json 返回到页面的 json格式数据 ,包括:
     *         status 状态
     *         msg 提示信息
     *         upload_path 文件保存路径
     * @author Jin BangLong
     *         @time 2016.05.11
     */
    public function file_upload()
    {
        $upload_status=upFiles($_FILES["file"], $_POST['function_code'], $_POST['type'], $_POST['communist_no']);
        
        ob_clean();$this->ajaxReturn($upload_status);
    }

    /**
     * 删除上传文件
     * 
     * @name del_upload_info()
     * @param   file_id=文件id#
     * @return true/false
     * @author 王彬
     *         @time 2016-07-07
     */
    public function del_upload_info(){
        $bd_upload = M('bd_upload');
        $dt['upload_id'] = I('post.upload_id');
        $upload_data = $bd_upload->where($dt)->delete();
        if($upload_data){
            $data['status'] = 1;
            $data['msg'] = '操作成功';
        }else{
            $data['status'] = 0;
            $data['msg'] = '操作失败';
        }
        ob_clean();$this->ajaxReturn($data);
    }
    /**
     *  get_versions
     * @desc 版本更新
     * @user 王宗彬
     * @date 2018/1/31
     * @version 1.0.0
     */
    public  function get_versions()
    {
    	$sys_config = M('sys_config');
    	$tv_code = $sys_config->where("config_code = 'tv_code'")->getField('config_value');
    	$tv_ver = $sys_config->where("config_code = 'tv_ver'")->getField('config_value');
    	$tv_downurl = $sys_config->where("config_code = 'tv_downurl'")->getField('config_value');
    	$tv_size = $sys_config->where("config_code = 'tv_size'")->getField('config_value');
    	$tv_verlog = $sys_config->where("config_code = 'tv_verlog'")->getField('config_value');
    	$tv_old_code = $sys_config->where("config_code = 'tv_old_code'")->getField('config_value');
    	$list['tv_old_code'] = $tv_old_code;
    	$list['tv_code'] = $tv_code;
    	$list['tv_ver'] = $tv_ver;
    	$list['tv_downurl'] = $tv_downurl;
    	$list['tv_size'] = $tv_size;
    	$list['tv_verlog'] = $tv_verlog;
    	
    	
    	$data['status'] = 1;
    	$data['msg'] = '操作成功';
    	$data['list'] = $list;
    	
    	ob_clean();$this->ajaxReturn($data);
    
    }

    /**
     * 通过传入账号密码交换token
     * @param  string $terminal_no    终端编号
     * @param  string $check_date     验证日期
     * @return string           token
     */
    public function get_terminal_token()
    {
        # 接收post数据
        $post = $_POST;
        # 判断
        if (!isset($post['terminal_no']) || !isset($post['check_date'])) {
            $this->send('未指定终端和日期','', 0);
        } else{
            // 获取传值编号
            $terminal_no = $post['terminal_no'];
            $check_date = $post['check_date'];
            // 返回值信息
            $data['terminal_no'] = $terminal_no;
            $data['check_date'] = $check_date;
            // 平台终端编号
            $terminal_array = array(APP_CODE, TV_CODE, WX_CODE, PORTAL_CODE,BIGDATA_CODE,ALLINONE_CODE);
            // 验证所传终端编号是否存在
            if(in_array($terminal_no,$terminal_array)){
                // 使用uuid生成唯一秘钥写入redis中，并设置30分钟后过期
                // $hash = password_hash($terminal_no.'_'.$check_date,PASSWORD_DEFAULT);
                $token = md5($terminal_no.'_'.$check_date);
                session($terminal_no.'_TOKEN',$token);
                if (!empty(session($terminal_no.'_TOKEN'))) {
                    $data['code'] = 1;
                    $data['tocke'] = $token;
                    $this->send('获取成功',$data, 1);
                }else{
                    $data['code'] = 0;
                    $data['tocke'] = '';
                    $data['check_date'] = $check_date;
                    $this->send('获取失败',$data, 0);
                }
            } else {
                $data['code'] = 0;
                $data['tocke'] = '';
                $this->send('终端编号错误',$data, 0);
            }
        }
    }

    /**
     * 生成唯一的uuid值
     * @param  integer $lenght 生成的uuid长度
     * @return
     */
    public function uniqidReal($lenght = 13)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
    /**
     * 后台工作人员列表
     * @param 
     * @return
     */
    public function staff_list(){
      $page = I('get.page');
      $pagesize = I('get.limit');
      $page = ($page-1)*$pagesize;
      $taff_list = M('hr_staff')->limit($page,$pagesize)->field('staff_no,staff_name,staff_avatar,staff_dept_no')->select();
      $dept = M('hr_dept')->getField('dept_no,dept_name');
      foreach($taff_list as &$list){
        $list['staff_avatar'] = getUploadInfo($list['staff_avatar']);
        $list['staff_dept_name'] = $dept[$list['staff_dept_no']];
      }
      $this->send('获取成功',$taff_list, 0);
    }

    /**
     * 后台工作人员列表
     * @param 
     * @return
     */
    public function set_XinGeMsg_info(){
      $title = I('get.title');
      $content = I('get.content');
      $environment = I('post.environment',0);
      $accounts = I('get.accounts',9999);
      $accounts=strToArr($accounts);
      $send_ret = sendXinGeMsg($title,$content,$environment,$accounts,$time);
      $this->send('获取成功',$send_ret, 0);
    }
    /**
     * 聊天
     * @param 
     * @return
     */
     public function public_chat(){
        $communist_no = I('post.communist_no');
        $chat_data = M('ccp_communist_chat')->select();
        foreach ($chat_data as &$list){
            if($list['communist_no']==$communist_no){
                $list['status'] = 1;
            }
            $list['communist_avatar'] = M('ccp_communist')->where("communist_no=$communist_no")->getField("communist_avatar");
        }
        $this->send('获取成功',$chat_data, 1);
     }
     /**
     * 聊天
     * @param 
     * @return
     */
     public function public_chat_new(){
        $communist_no = I('post.communist_no');
        $chat_id = I('post.chat_id');
        $where['chat_id'] = array('GT',$chat_id);
        $chat_data = M('ccp_communist_chat')->where($where)->limit(5)->select();
        foreach ($chat_data as &$list){
            if($list['communist_no']==$communist_no){
                $list['status'] = 1;
            }
            $list['communist_avatar'] = M('ccp_communist')->where("communist_no=$communist_no")->getField("communist_avatar");
            
        }
        $this->send('获取成功',$chat_data, 1);
     }
      /**
     * 聊天
     * @param 
     * @return
     */
     public function public_chat_add(){
        $post['communist_no'] = I('post.communist_no');
        $post['chat_content'] = I('post.chat_content');
        $post['add_time'] = date("Y-m-d H:i:s");
        $data = M('ccp_communist_chat')->add($post);
        $this->send('获取成功',$data, 1);
    }
    //最大页数
     public function public_chat_count(){
        $pagesize = I('post.pagesize'); //个数
        $chat_count = M('ccp_communist_chat')->count();
        $ceil = ceil($chat_count/$pagesize);
        $this->send('获取成功',$ceil, 1);
     }
}
