<?php

namespace System\Controller;

use Database\Database;
use Think\Controller;
use Common\Controller\BaseController;
use Think\Verify;
use Think\Db;

class PublicController extends Controller // 继承Controller类
{
    /**
     * @name   register_save
     * @desc：  注册信息保存
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function register_save()
    {
        $username = I('post.username');
        $pwd = I('post.password');
        $npwd = I('post.rpassword');
        if (empty($username)) {
            // 用户名
            $this->error('用户名不能为空,请重新输入！！');
        }
        if (empty($pwd) || empty($npwd)) {
            // 密码
            $this->error('用户密码不能为空,请重新输入！！');
        }
        $pwd = md5($npwd);
        $data['user_name'] = $username;
        $data['user_pwd'] = $pwd;
        $sys_user = D("sys_user");
        $flag = $sys_user->add('$data');
        if ($sys_user) {
            // 存储session
            session('communist_no', $data['user_id']); // 当前用户id
            $this->success('注册成功！', U('Index/index'));
        } else {
            $this->error("注册失败");
        }
    }
    /**
     * @name    get_session_info
     * @desc：  判断session是否还存在
     * @author：ljj
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function get_session_info()
    {
        $staff_no = session('staff_no');
        $session_user_id = session('user_id');
        $party_no_auth = session('party_no_auth');
        if(empty($staff_no) || empty($session_user_id) || empty($party_no_auth)){
            $check_res = U(C('JUMP_MODULE').'/Public/'.LOGIN_ACTION);
        } else {
            $check_res = true;
        }
        ob_clean();$this->ajaxReturn($check_res);
    }
    /**
     * @name   login
     * @desc：  系统(登录)
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function login()
    {
        // checkLicense('sp_p5',"0987-1234-4567-9876");
		$logo_id = getConfig("logo_img");
		$logo_url = getUploadInfo($logo_id);
		$this->assign("logo_url",$logo_url);
		$this->display();
    }
    /**
     * @name   login_do
     * @desc：  登录验证
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function login_do()
    {
        
        $username = I('post.username');// 用户名
        $rememberme = I('post.rememberme');// 密码
        $pwd = I('post.password');
        $code = I('post.code');
        $res = $this->check_verify($code);
        if($res){
            $this->login_verify($username, $rememberme, $pwd, $code);
        }else{
            $this->error("验证码错误！");
        }
    }

   

    /**
     * @name   logout
     * @desc：  退出登录
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function logout()
    {
        session('[destroy]'); // 清除服务器的sesion文件
        //unset($_SESSION);
        $getcookiecode = getConfig('cust_code');
        //unset($_COOKIE);
        cookie($getcookiecode . '_username', null);
        cookie($getcookiecode . '_pwd', null);
        cookie($getcookiecode . '_communist_no', null);

        /* setcookie($getcookiecode.'_username',null);
        setcookie($getcookiecode.'_pwd',null);
        setcookie($getcookiecode.'_communist_no',null); */
        $this->success('已成功退出系统', U('Public/' . LOGIN_ACTION));
    }
    /**
     * @name   update_user_pwd
     * @desc：  修改密码
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function update_user_pwd()
    {
        $user_id = session('user_id'); // 获取session user_id
        $sys_user = M('sys_user');
        $user_map['user_id'] = $user_id;
        $user_info = $sys_user->where($user_map)->find();
        $this->assign("user_info",$user_info);
        $this->display();
    }
    /**
     * @name   user_pwd_setting
     * @desc：  修改密码
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function user_pwd_setting()
    {
        $oldpwd = I('post.user_oldpwd');
        $user_pwd = I('post.user_pwd');
        $user_npwd = I('post.user_npwd');
        $sys_user = M('sys_user');
        $user_id = session('user_id');

        if (!empty($user_pwd) && !empty($user_npwd) && !empty($oldpwd)) {
            $oldpwd = md5($oldpwd);
            $user_map['user_id'] = $user_id;
            $user_map['user_pwd'] = $oldpwd;
            $user_count = $sys_user->where($user_map)->count();

            if ($user_count < 1) {
                $this->error("原密码输入不正确!!!");
            }
            if (md5($user_pwd) == md5($user_npwd)) {
                $data['user_pwd'] = md5($user_npwd);
                $user_save_map['user_id'] = $user_id;
                $flag = $sys_user->where($user_save_map)->save($data);
                if ($flag) {
                    $this->success("操作成功!!!", U('Public/logout'));
                } else {
                    $this->error("操作失败！！！");
                }
            } else {
                $this->error("新密码和确认密码不一致");
            }
        } else {
            $this->error("密码不能为空！！！");
        }
    }
    /**
     * @name   login_verify
     * @desc：  验证登陆
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function login_verify($username, $rememberme, $pwd, $code)
    {
        $sys_user = M("sys_user");
        $getcookiecode = getConfig('cust_code');
        $getcookietime = C('COOKIE_EXPIRE');
        if (!empty($pwd)) {
            $pwd = md5($pwd);
        }
        /* 新增判断 针对万邦多入口 */
        switch (GROUP_CODE) {
            case 'communist':
                $user_relation_type = 2;
                break;
            case 'cust':
                $user_relation_type = 2;
                break;
            case 'agent':
                $user_relation_type = 3;
                break;
            case 'partner':
                $user_relation_type = 4;
                break;
            case 'company':
                $user_relation_type = 5;
                break;
            case 'member':
                $user_relation_type = 6;
                break;
        }
        // 通过用户名密码获取用户信息
        $data = $sys_user->where("(user_relation_type = '$user_relation_type') and user_name='%s'", $username)->find();
        // 判断用户ID是否存在
        if (!empty($data['user_id'])) {
            $period_validity = $data['period_validity'];
            if(empty($period_validity)) $period_validity = date('Y-m-d');
            $today = date('Y-m-d');
            if($period_validity < $today ){               
                 $this->error('用户已过有效期', U('Public/' . LOGIN_ACTION));
            }else{
                if ($data['user_pwd'] == $pwd) {
                    if ($data['status'] == 0) {
                        $this->error('当前账户已停用,请切换账户', U('Public/' . LOGIN_ACTION));
                    } else {
                        $data['last_login_time'] = date('Y-m-d H:i:s');
                        $save_res = $sys_user->where("user_id=" . $data['user_id'])->save($data);
                        if ($rememberme == 1) {
                            cookie($getcookiecode . '_username', md5($username), $getcookietime);
                            cookie($getcookiecode . '_pwd', base64_encode($pwd), $getcookietime);
                            cookie($getcookiecode . '_communist_no', $data['user_relation_no'], $getcookietime);
                            /*  setcookie($getcookiecode.'_username',md5($username),$getcookietime);
                            setcookie($getcookiecode.'_pwd',base64_encode($pwd),$getcookietime);
                            setcookie($getcookiecode.'_communist_no',$data['user_relation_no'],$getcookietime); */
                        }
                        // 获取权限下的党组织编号
                        $comm_map['staff_no'] = $data['user_relation_no'];
                        $staff_info = M('hr_staff')->where($comm_map)->field('staff_id,staff_dept_no')->find();
                        // 存储session
                        //session('communist_no', $data['user_relation_no']); // 当前用户communist_no
                        session('staff_no', $data['user_relation_no']); // 当前用户communist_no
                        session('user_id', $data['user_id']); // 当前用户id
                        session('staff_id',  $staff_info['staff_id']); // 当前用户id
                        cookie('user_id',$data['user_id']);
                        $people_id = peopleNo($data['user_relation_no'],2);
                        session('people_no',$people_id);
                        // 有权限查看全部党组织
                        $is_admin = checkDataAuth(session("staff_no"),"is_admin");
                        $party_map['status'] = 1;
                        if($is_admin){
                            $party_no_arr=M("ccp_party")->where($party_map)->getField("party_no",true);
                        } else {
                            if(!empty($data['charge_party'])){
                                $is_child = checkDataAuth(session("staff_no"),"is_child");
                                if($is_child){
                                    $party_no_arr=getPartyChildMulNos($data['charge_party'],'arr');
                                    sort($party_no_arr);
                                }else{
                                    $party_map['party_no'] = array('in',$data['charge_party']);
                                    $party_no_arr=M("ccp_party")->where($party_map)->getField("party_no",true);
                                }
                                
                            } else {
                                $party_no_arr=M("ccp_party")->where($party_map)->getField("party_no",true);
                            }
                        }
                        
                        $party_no_auth=arrToStr($party_no_arr);
                        session('party_no_auth',  $party_no_auth); // 当前用户可以查看的党组织

                        $dept_no_auth = getDeptChildNos($staff_info['staff_dept_no'], 'str','1','is_admin');
                        session('dept_no_auth',  $dept_no_auth); // 当前用户可以查看的党组织
                        saveLog(ACTION_NAME,4,'','操作员['.getStaffInfo(session('staff_no')).']于'.date("Y-m-d H:i:s").'登陆系统');
                        $this->success('登录成功！', U('Index/' . INDEX_ACTION));
                    }
                } else {
                    $this->error('用户密码错误', U('Public/' . LOGIN_ACTION));
                }
            }
        } else {
            $this->error('用户名错误', U('Public/' . LOGIN_ACTION));
        }
    }
    /**
     * @name   get_area_data
     * @desc：  获取地区信息数据
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function get_area_data()
    {
        if (!empty($_GET['pid'])) {
            $area_pno = $_GET['pid'];
            $select_id = $_GET['select_id'];
            $area_str = "<option value='0' selected>请选择</option>" . getAreaSelect($area_pno, $select_id);
        }
        echo $area_str;
    }
    /**
     * @name   getBackupDir
     * @desc：  获取备份目录，不存在则创建
     * @author：靳邦龙
     * @addtime:2017-11-18
     * @version：V1.0.0
     **/
    public function getBackupDir()
    {

        $path = "./data/";

        !is_dir($path) && mkdir($path, 0755, true);

        return $path;
    }
    /**
     * @name   dataBackup_index
     * @desc：  数据库备份
     * @author：靳邦龙
     * @addtime:2018-01-04
     * @version：V1.0.0
     **/
    public function dataBackup_index()
    {
        
		if(IS_AJAX){
			$table = M()->query('SHOW TABLE STATUS');
			foreach ($table as &$tab_val) {
				$tab_val['operate'] = "<a class='btn yellow-p5 btn-xs btn-outline'  href='" . U('optimize', array('table' => $tab_val['name'],'mark' => '1')) . "'><i class='fa fa-edit'></i>优化</a> <a class='btn green btn-xs btn-outline'  href='" . U('optimize', array('table' => $tab_val['name'],'mark' => '0')) . "'><i class='fa fa-edit'></i>修复</a>" ;
			}
			ob_clean();
			$table = array(
				'code'=>0,
				'msg'=>'',
				'count'=>$count,
				'data'=>$table
			);
			$this->ajaxReturn($table);
		}
		$this->display();
    }

    /**
     * 数据库备份操作
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function dataBackup()
    {
        $path = $this->getBackupDir();
        $config = [
            'path' => realpath($path) . '/',
            'part' => getConfig('data_backup_part_size'),
            'compress' => getConfig('data_backup_compress'),
            'level' => getConfig('data_backup_compress_level'),
        ];

        $lock = "{$config['path']}backup.lock";

        if (is_file($lock)) : showMsg('error', '检测到有一个备份任务正在执行，请稍后再试！'); endif;

        // 创建锁文件
        file_put_contents($lock, time());

        // 检查备份目录是否可写
        if (!is_writeable($config['path'])) : showMsg('error', '备份目录不存在或不可写，请检查后重试！'); endif;

        // 生成备份文件信息
        $file = ['name' => date('Ymd', time())];
        $Database = new Database($file, $config);
        if (false == $Database) : showMsg('error', '备份初始化失败！'); endif;

        $table_list = M()->query('SHOW TABLE STATUS');
        $table_list = array_extract($table_list, 'name');

        $error_table = '';

        foreach ($table_list as $v) {

            $start = $Database->backup($v, 0);

            if ($start === false) : $error_table = $v;
                break; endif;
        }

        unlink($lock);

        if (!empty($error_table)) : showMsg('error', '备份出错，表名：' . $error_table); endif;
        showMsg('error', '备份成功');
    }

    /**
     * 数据库修复与优化
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function optimize()
    {
        $mark = $_GET['mark'];
        $table = $_GET['table'];
        if(!empty($table)){
            $tables = $table;
        } else {
            $table_list_all = M()->query('SHOW TABLE STATUS');
            foreach ($table_list_all as $key => $value) {
                if(strpos($value['name'],'gm') == false){
                    $table_list[$key]['name'] = $value['name'];
                }
            }
            $table_list = array_extract($table_list, 'name');
            $tables = implode('`,`', $table_list);
        }
        $list = $mark == 1 ? M()->query("OPTIMIZE TABLE `{$tables}`") : M()->query("REPAIR TABLE `{$tables}`");
        $text = $mark == 1 ? '优化' : '修复';

        if (!$list) : showMsg('error', $text . '出错'); endif;

        showMsg('error', $text . '成功');
    }

    /**
     * 数据库还原
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function dataRestore_index()
    {
        $path = $this->getBackupDir();

        $path = realpath($path);

        $flag = \FilesystemIterator::KEY_AS_FILENAME;

        $glob = new \FilesystemIterator($path, $flag);

        $list = [];
        foreach ($glob as $name => $file) {

            if (!preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) : continue; endif;

            $name = sscanf($name, '%8s-%6s-%d');
            $part = $name[2];

            $info['part'] = $part;

            $info['size'] = $file->getSize();

            $info['name'] = $file->getFilename();

            $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));

            $info['compress'] = ($extension === 'SQL') ? '-' : $extension;

            $info['time'] = strtotime("{$name[0]} {$name[1]}");

            $list[] = $info;
        }
        $this->assign('data', $list);
        $this->display();
    }

    /**
     * 数据库还原操作
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function dataRestore()
    {
        $time = $_GET['time'];

        $name = date('Ymd-His', $time) . '-*.sql*';

        $path = $this->getBackupDir();

        $path = realpath($path) . DIRECTORY_SEPARATOR . $name;

        $files = glob($path);

        $list = [];

        foreach ($files as $name) {
            $basename = basename($name);
            $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
            $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
            $list[$match[6]] = array($match[6], $name, $gz);
        }

        ksort($list);

        // 检测文件正确性
        $last = end($list);

        if (!(count($list) === $last[0])) : showMsg('error', '备份文件可能已经损坏，请检查！'); endif;

        $path = $this->getBackupDir();

        $config = [
            'path' => realpath($path) . '/',
            'compress' => getConfig('data_backup_compress'),
        ];

        $error = '';

        foreach ($list as $file) {

            $database = new Database($file, $config);

            $start = $database->import(0);

            if (false === $start) : $error = '还原数据出错';
                break; endif;
        }

        if (!empty($error)) : showMsg('error', $error); endif;

        showMsg('success', '还原成功', U('dataRestore_index'));
    }

    /**
     * 删除数据库备份
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function backupDel()
    {
        $time = $_GET['time'];

        $name = date('Ymd-His', $time) . '-*.sql*';

        $path = $this->getBackupDir();

        $path = realpath($path) . DIRECTORY_SEPARATOR . $name;

        array_map("unlink", glob($path));

        if (count(glob($path))) : showMsg('error', '备份文件删除失败，请检查权限！'); endif;

        showMsg('success', '删除成功', U('dataRestore_index'));
    }

    /**
     * layui layedit 上传图片
     * @author 刘丙涛
     * @addtime 2018-01-04
     */
    public function layedit_upload()
    {
        $upload_res = upFiles($_FILES["file"], 'public');
        if($upload_res['status'] == 1){ // 上传成功
            $lay_data['code'] = 0;
            $lay_data['msg'] = '上传成功';
            $lay_data['data']['src'] = getConfig('web_url').'/'.$upload_res['upload_path'];
            $lay_data['data']['title'] = $upload_res['upload_source'];
        } else {
            $lay_data['code'] = 1;
            $lay_data['msg'] = '上传失败';
            $lay_data['src'] = '';
        }
        ob_clean();$this->ajaxReturn($lay_data);
    }


    /**
     *  verify
     * @desc 验证码
     * @user 王宗彬
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function verify(){
        // $config =    array(
        //     'fontSize'    =>    30,    // 验证码字体大小
        //     'length'      =>    4,     // 验证码位数
        //     'useNoise'    =>    true, // 关闭验证码杂点
        //     'useCurve'    =>    true, // 关闭验证码曲线
        // );
        // $Verify =  new \Think\Verify($config);
        // $Verify->imageW = 130; 
        // $Verify->imageH = 50;
        // // $Verify->fontSize = 15;
        // // $Verify->length   = 4;
        // // // 设置验证码字符为纯数字
        // //$Verify->codeSet = '0123456789abcdefghigkltnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWSYZ';
        // $Verify->codeSet = '0123456789';
        // $Verify->useNoise=falsed;

        ob_clean();
        $config = [
            'fontSize' => 19, // 验证码字体大小
            'length' => 4, // 验证码位数
            'imageH' => 34
        ];
        $Verify = new Verify($config);
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }
    /**
     *  check_verify
     * @desc 验证码验证
     * @user 王宗彬
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function check_verify($code){
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /**
     *  del_runtime
     * @desc 清理缓存
     * @user 王宗彬
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function del_runtime(){
        $clear_res = deldir();
        ob_clean();$this->ajaxReturn($clear_res);
    }

    /**
     * @name  select_staff_info_page()
     * @desc  人员选择页面弹窗
     * @param 
     * @return 
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @updatetime   
     * @addtime   2017-10-19
     */
    public function select_staff_info_page()
    {
        $name = I('get.field_name');
        //部门
        $staff_dept_no = getStaffInfo(session('staff_no'),'staff_dept_no');
        $dept_list = getDeptChildNos($staff_dept_no,'str',1,'is_admin');
        $dept_list = strToArr($dept_list, ',');
        foreach($dept_list as &$dept){
            $dept = getDeptInfo($dept ,all);
        }
        $this->assign('dept_list',$dept_list);
        //职务
        $party_list = M('ccp_party')->field('party_no,party_pno,party_name')->select();
        $this->assign('party_list',$party_list);
        //分组
        $group_list = M('ccp_group')->select();
        $this->assign('group_list',$group_list);
        $this->assign('name',$name);
        $type = I('get.type');  //有type 为第一书记
        $this->assign('type',$type);

        $group = I('get.group');  
        $this->assign('group',$group);

        $hide = I('get.hide');  //不显示人员分组
        $this->assign('hide',$hide);
        $this->display("select_staff_info_page");
    }

}

