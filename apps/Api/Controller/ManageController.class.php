<?php
/**
 * 监督平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:45
 */

namespace Api\Controller;

use Api\Validate\CommunistNoValidate;
use Api\Validate\NumberValidate;
use Edu\Model\EduNotesModel;

class ManageController extends Api
{
	
	/**
	 *  get_oa_meeting_intro
	 * @desc TV获取
	 * @param int party_no 组织编号
	 * @user liubingtao
	 * @date 2018/2/1
	 * @version 1.0.0
	 */
	public function get_oa_meeting_intro()
	{
		(new NumberValidate(['party_no']))->goCheck();
		$party_no = I('post.party_no');
		$party_info = getPartyInfo($party_no,'all');
		if ($party_info) {
			$party_info['meeting_begin'] = M('oa_meeting')->where("party_no='$party_no' and  status in(11,21) ")->count();
			$party_info['meeting_finish'] = M('oa_meeting')->where("party_no='$party_no' and  status in(23) ")->count();
			$party_info['party_manager'] = getCommunistInfo($party_info['party_manager']);
			$party_info['communist_num'] = M('ccp_communist')->where("party_no = '$party_no'")->count();
			$party_info['qrcode_url'] = createQrcode('http://zhnjbd.sp11.cn/index.php/System/Gis/index.html','qrcode_sign_tv_img');
            \Think\Log::record('测试日志信息'.$party_info['qrcode_url']);
            $this->send('获取成功', $party_info, 1);
		} else {
			$this->send();
		}
	}
	
    /**
     *  get_oa_meeting_new_list
     * @desc 获取会议列表
     * @param int communist_no 党员编号
     * @param int meeting_type 会议类型
     * @param int status 会议状态 可参加已结束
     * @param int page
     * @param int pagesize
     * @user 刘长军
     * @date 2020/01/19
     * @version 1.0.0
     */
    public function get_oa_meeting_new_list(){
        $communist_no = I('post.communist_no');
        $oa_meeting= M('oa_meeting');
		$meeting_type = '2001,2002,2003,2004';
    	$meeting_list = getCommunistMeetingList($communist_no, $meeting_type, '11,21', 0, 3);
	    foreach ($meeting_list as &$value){
			$value['party_no'] = getPartyInfo($value['party_no']);
            $value['meeting_host'] = getCommunistInfo($value['meeting_host']);
            $value['add_staff'] = getStaffInfo($value['add_staff']);
            $value['meeting_start_time'] = date('Y-m-d',strtotime($value['meeting_start_time']));//会议时间

		}
        if ($meeting_list) {
            $this->send('获取成功', $meeting_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_oa_meeting_list
     * @desc 获取会议列表
     * @param int communist_no 党员编号
     * @param int meeting_type 会议类型
     * @param int status 会议状态 可参加已结束
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_meeting_list()
    {
        //(new CommunistNoValidate())->goCheck();
        (new NumberValidate(['page', 'status', 'pagesize']))->goCheck();

        $communist_no = I('post.communist_no');
        $party_no = I('post.party_no');
        $meeting_type = I('post.meeting_type');
        $status = I('post.status');
        $page = I('post.page');
        $pagesize = I('post.pagesize');
        $source = I('post.source');
		if($party_no){
			$communist_no = '';
		}else{
			$party_no = '';
		}
        $page = ($page - 1) * $pagesize;
        if($source == 1){
            $status = $status == 1 ? '11' : '23';
        } else {
            $status = $status == 1 ? '11,21' : '23';
        }
        \Think\Log::record('测试日志信息'.$party_no);
		if(!empty($party_no)){
            $where['party_no'] = $party_no;
			if (!empty($meeting_type)){
				$where['meeting_type'] = $meeting_type;
			}
			if (!empty($status)){
                $where['status'] = array('in',$status);
			}
			$oa_meeting= M('oa_meeting');
			$meeting_count = $oa_meeting->where("party_no='$party_no' and  status in($status) ")->count();
			$meeting_list = $oa_meeting->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
            $where['meeting_type']=2001;
            $meeting_type1 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no');
            $where['meeting_type']=2002;
            $meeting_type2 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no');
            $where['meeting_type']=2003;
            $meeting_type3 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no');
            $where['meeting_type']=2004;
            $meeting_type4 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no'); 
            $where['meeting_type']=2005;          
            $meeting_type5 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no');
            $where['meeting_type']=2006;
            $meeting_type6 = $oa_meeting->where($where)->order('add_time desc')->getField('meeting_no');
			foreach ($meeting_list as &$list){
                $list['host_name'] = getCommunistInfo($list['meeting_host']);
				$list['party_name'] = getPartyInfo($list['party_no']);//组织部门
				// $list['meeting_type'] = getBdTypeInfo($list['meeting_type'], 'meeting_type');
				$list['status'] = getStatusInfo('meeting_status',$list['status']);
				$list['meeting_start_time'] = date('Y-m-d',strtotime($list['meeting_start_time']));//会议时间
                $meeting_map['meeting_no'] = $list['meeting_no'];
                $list['communist_num'] = M('oa_meeting_communist')->where($meeting_map)->count();
                if(!empty($list['meetting_thumb'])){
                    $list['meetting_thumb'] = getUploadInfo($list['meetting_thumb']);
                }else{
                    $list['meetting_thumb'] = '';
                }
                if($meeting_type1 == $list['meeting_no']){
                    $list['is_one'] = '1';
                }elseif ($meeting_type2 == $list['meeting_no']) {
                    $list['is_one'] = '1';
                }elseif ($meeting_type3 == $list['meeting_no']) {
                    $list['is_one'] = '1';
                }elseif ($meeting_type4 == $list['meeting_no']) {
                    $list['is_one'] = '1';
                }elseif ($meeting_type5 == $list['meeting_no']) {
                    $list['is_one'] = '1';
                }elseif ($meeting_type6 == $list['meeting_no']) {
                    $list['is_one'] = '1';
                }
                $list['room_no'] = M('oa_meeting_room')->where($meeting_map)->getField("room_no");
			}
		}else{
			$meeting_list = getCommunistMeetingList($communist_no, $meeting_type, $status, $page, $pagesize);
		}
	    foreach ($meeting_list as &$meeting_l){
			if($list['meeting_camera'] == "" || $list['meeting_camera'] == "no"){
				$list['meeting_camera'] = '0';
			}
            $meeting_l['add_staff_name'] = getCommunistInfo($meeting_l['add_staff']);
		}
        if ($meeting_list) {
            $this->send('获取成功', $meeting_list, 1,'meeting_count',$meeting_count);
        } else {
            $this->send();
        }
    }

    /**
     *  get_oa_meeting_info
     * @desc 获取会议详情
     * @param int communist_no 党员编号
     * @param int meeting_no 会议编号
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_meeting_info()
    {
       //(new CommunistNoValidate())->goCheck();
       (new NumberValidate(['meeting_no']))->goCheck();

        $communist_no = I('post.communist_no');
        $meeting_no = I('post.meeting_no');

        $meeting_info = getMeetingInfo($meeting_no, 'all', '1');
        // dump($meeting_info);die;
        if ($meeting_info) {
        	if($meeting_info['meeting_camera'] == 'no' || empty($meeting_info['meeting_camera'])){
        		$meeting_info['meeting_camera'] = "0";
        	}
            $is_sign = getIsChecked($meeting_no,$communist_no);
            $meeting_info['is_sign'] = $is_sign=='no'?'0':'1';
        	$meeting_info['host_name'] = getCommunistInfo($meeting_info['meeting_host']);//主持人
        	$meeting_info['party_no'] = getPartyInfo($meeting_info['party_no']);//组织部门
        	$meeting_info['meeting_type'] = getBdTypeInfo($meeting_info['meeting_type'], 'meeting_type');
        	$meeting_info['meeting_start_time'] = date('Y-m-d',strtotime($meeting_info['meeting_start_time']));//会议时间
        	if(!empty($meeting_info['meeting_video'])){
                $meeting_video = explode(',', $meeting_info['meeting_video']);
                $meeting_info['meeting_video'] = "/uploads/video/".$meeting_video[0];
            }
        	if(!empty($meeting_info['meetting_thumb'])){
                $meeting_info['meetting_thumb'] = getUploadInfo($meeting_info['meetting_thumb']);
            }else{
                $meeting_info['meetting_thumb'] = '';
            }
            $meeting_info['qrcode_url'] = '/'.createQrcode('http://sdnjpt.sp11.cn/index.php/System/Gis/index.html','');
            \Think\Log::record('测试日志信息'.$meeting_info['qrcode_url']);
        	$this->send('获取成功', $meeting_info, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_oa_meeting_communist
     * @desc 获取人员
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function get_oa_meeting_communist()
    {
    	(new NumberValidate(['meeting_no']))->goCheck();
    	$meeting_no = I('post.meeting_no');
    	
    	$oa_meeting_communist=M("oa_meeting_communist");
    	$where['meeting_no']="$meeting_no";
    	$meeting=$oa_meeting_communist->where($where)->order('add_time desc')->select();
    	$i=0;
    	$k = 0;
    	foreach($meeting as &$v){
    		$k++;
    		$v['check_time'] = '0000-00-00 00:00:00';
    		$v['communist_avatar']="";
    		$v['communist_name'] = getCommunistInfo($v['communist_no'],"communist_name");
    		$v['communist_avatar'] = getCommunistInfo($v['communist_no'],"communist_avatar");
    		if(!empty($v['communist_avatar'])){
    			$v['communist_avatar'] = "/".$v['communist_avatar'];
    		}else{
                $v['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
    		$v['party_name'] =  getPartyInfo($v['party_no'],"party_name");
    		$where1=array(
    				"meeting_no"=>"$meeting_no",
    				"communist_no"=>$v['communist_no']
    		);
    		$data=array();
    		$data=$oa_meeting_communist->where($where1)->find();
    		$meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
    		//判断签到时间是否符合规定
    		$v["att_manner"]='';
    		if($meeting_precheck_time!="no"){
    			$v['status_name']="<font color=red>已签到</font>";
    			$att_arr=M("oa_att_log")->where("att_id='$meeting_precheck_time'")->find();
    			$v["att_manner"]=$att_arr["att_manner"];
    			$i++;
                $v["sign_type"]=$att_arr["att_manner"];
    			if($att_arr["att_manner"] == "12"){
    				$v["ckeck_type_name"]="手机签到";
    				$v['check_time']=$att_arr['check_time'];
    				$v["check_addr"]=coordinateToAddr($data["checked_addr"]);
    				$v["study_content"]=$data["study_content"];
    			}else{
    				$v["ckeck_type_name"]="考勤机签到";
    				$v['check_time']=$att_arr['check_time'];
    				$mach=getMachineInfo($row["machine_no"]);
    				$v["check_addr"]=$mach["machine_addr"];
    				$v["study_content"]=$data["study_content"];
    			}
                $v['is_check']=1;
    		}else{
                $v['is_check']=0;
    			$v['status_name']="未签到";
    		}
    	}
    	$meeting = $this->arraySequence($meeting,'check_time');
    	if ($meeting) {
    		$meetting_data['msg'] = '操作成功';
    		$meetting_data['status'] = 1;
    		$meetting_data['sign'] = $i;
    		$meetting_data['meeting_count'] = $k;
    		$meetting_data['data'] = $meeting;
    		
    		ob_clean();$this->ajaxReturn($meetting_data,'json');
    	} else {
    		$this->send('操作失败');
    	}
    	 
    }
    /**
     *  set_oa_meeting_status
     * @desc 修改会议状态
     * @param int status 状态
     * @param int meeting_no 会议编号
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function set_oa_meeting_status()
    {
    	(new NumberValidate(['meeting_no', 'status']))->goCheck();
    	 
    	$meeting_no = I('post.meeting_no');
    	$status = I('post.status');
    	if($status == 11){
    		$data['status'] = 21;
    		$data['meeting_real_start_time'] = date('Y-m-d H:i:s');
    		$res = "开始会议成功";
    	} elseif ($status == 21){
    		$data['status'] = 23;
    		$res = "您已结束会议";
    		$data['meeting_real_end_time'] =  date('Y-m-d H:i:s');
    	}
    	$meeting = M('oa_meeting')->where("meeting_no = '$meeting_no'")->save($data);
    	
    	if ($meeting) {
    		$this->send($res, $data, 1);
    	} else {
    		$this->send();
    	}
    }
    
    /**
     *  set_oa_meeting_sign
     * @desc 会议签到
     * @param int communist_no 党员编号
     * @param int meeting_no 会议编号
     * @param string att_address 地址
     * @user liubingtao
     * @date 2018/2/1
     * @version 1.0.0
     */
    public function set_oa_meeting_sign()
    {
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['meeting_no']))->goCheck();
		
        $communist_no = I('post.communist_no');
        $meeting_no = I('post.meeting_no');
        $att_address = I('post.att_address');
       
        $meeting_info = getMeetingInfo($meeting_no, 'all', '1');
        if (!$meeting_info) : $this->send('签到失败'); endif;
        if ($meeting_info['status'] != 21) : $this->send('会议未开始'); endif;
        $time = date("Y-m-d H:i:s");
        if ($time >= $meeting_info['meeting_real_start_time']) {
            $db_log = M('oa_att_log');
            $log_list['att_no'] = $communist_no;
            $log_list['att_address'] = $att_address;
            $log_list['att_date'] = date('Y-m-d');
            $log_list['att_time'] = date('H:i:s');
            $log_list['check_time'] = date('Y-m-d H:i:s');
            $log_list['att_manner'] = '12';
            $log_list['att_relation_no'] = $meeting_no;
            $log_list['add_staff'] = $communist_no;
            $log_list['status'] = '1';
            $log_list['add_time'] = date('Y-m-d H:i:s');
            $result = $db_log->add($log_list);
            if ($result) {
                $this->send('签到成功', null, 1);
            } else {
                $this->send('签到失败');
            }
        } else {
            $this->send('未在会议时间内签到');
        }
    }
    /**
     * arraySequence
     * @desc 数组排序 
     * @param  $array 数组
     * @param $field 排序的字段
     * @param $sort排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     * @user 王宗彬
     * @date 2018/3/29
     * @version 1.0.0
     */
    function arraySequence($array, $field, $sort = 'SORT_DESC')
    {
    	$arrSort = array();
    	foreach ($array as $uniqid => $row) {
    		foreach ($row as $key => $value) {
    			$arrSort[$key][$uniqid] = $value;
    		}
    	}
    	array_multisort($arrSort[$field], constant($sort), $array);
    	return $array;
    }

    /**
     * create_meeting_home
     * @desc 创建app应用
     * @param  null
     * @user ljj
     * @date 2018/12/24
     * @version 1.0.0
     */
    function create_meeting_home()
    {
        $home_name = I('post.home_name');
        $maxusernum = I('post.maxusernum') ? I('post.maxusernum') : 9; // 最大人数
        $kickuser = I('post.kickuser') ? I('post.kickuser') : false; // 是否禁止自动踢人
        // 引入appcliet包和鉴权包
        vendor("Qiniu.Rtc.AppClient");
        vendor("Qiniu.Auth");
        // accessKey 和 secretKey
        $ak = 'X9Rih9E0DPewKKVxk4UEAqOzyuaTS7kIVoGnzL0m';
        $sk = '64QBAoTfZGpN18zang5i-dWieftg5pQitB4eBlsr';
        // 针对accessKey 和 secretKey 进行auth鉴权
        $auth = new \Auth($ak,$sk);
        // 鉴权之后进行创建app
        $AppClient = new \AppClient($auth);
        $hub_name = I('post.hub_name') ? I('post.hub_name') : 'lfxlive'; // 直播空间名
        $app_title = I('post.app_title') ? I('post.app_title') : 'lfxlive'; // app的名称
        $home_info = $AppClient->createApp($hub_name,$app_title,$maxusernum,$kickuser); // 创建app
        if(!empty($home_info[0])){
            $data['status'] = 1;
            $data['msg'] = '创建app应用成功';
            // 创建房间成功之后存储到数据库
            // $home_info[0]['status'] = 1;
            // $home_info[0]['add_time'] = date('Y-m-d H:i:s');
            // $home_info[0]['update_time'] = date('Y-m-d H:i:s');
            // $result = M('oa_meeting_room')->add($home_info[0]);
        } else {
            $data['status'] = 0;
            $data['msg'] = '创建app应用失败，请重试！';
        }
        $this->send($data['msg'], $home_info[0], $data['status']);
    }

    /**
     * get_meeting_home_token
     * @desc 获取党建token
     * @param appId: app 的唯一标识，创建的时候由系统生成。
     * @param roomName: 房间名称，需满足规格 ^[a-zA-Z0-9_-]{3,64}$
     * @param userId: 请求加入房间的用户 ID，需满足规格 ^[a-zA-Z0-9_-]{3,50}$
     * @param expireAt: int64 类型，鉴权的有效时间，传入以秒为单位的64位Unix绝对时间，token 将在该时间后失效。
     * @param permission: 该用户的房间管理权限，"admin" 或 "user"，默认为 "user" 。当权限角色为 "admin" 时，拥有将其他用户移除出房间等特权.
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_home_token()
    {
        $appId = I('post.appId') ? I('post.appId') : 'dxxe5zq9g';
        $roomName = I('post.roomName') ? I('post.roomName') : 'lfxlive'; // 房间名称
        $userId = I('post.userId') ? I('post.userId') : '9999'; // 用户id
        $expireAt = I('post.expireAt') ? I('post.expireAt') : (time()+3600); // 鉴权的有效时间
        $permission = I('post.permission') ? I('post.permission') : 'user'; // 房间管理权限
        // 引入appcliet包和鉴权包
        vendor("Qiniu.Rtc.AppClient");
        vendor("Qiniu.Auth");
        // accessKey 和 secretKey
        $ak = 'X9Rih9E0DPewKKVxk4UEAqOzyuaTS7kIVoGnzL0m';
        $sk = '64QBAoTfZGpN18zang5i-dWieftg5pQitB4eBlsr';
        // 针对accessKey 和 secretKey 进行auth鉴权
        $auth = new \Auth($ak,$sk);
        // // 实例化appclient
        $AppClient = new \AppClient($auth);
        // 获取token
        $resp = $AppClient->appToken($appId, $roomName, $userId, (time()+3600), 'user');
        // 返回值
        $data['status'] = 1;
        $data['msg'] = '获取成功！！';
        $data['data']['token'] = $resp;
        $this->send($data['msg'], $data['data'], $data['status']);
    }

    /**
     * save_meeting_home_info
     * @desc 电视端创建会议房间之后保存到数据库
     * @param room_id: 房间编号。
     * @param appId: 应用id
     * @param room_name: 房间名称
     * @param type: 项目标示
     * @param communist_no: 创建人
     * @date 2018/12/24
     * @version 1.0.0
     */
    function save_meeting_home_info()
    {
        $oa_meeting_communist = M('oa_meeting_communist');
        $oa_meeting= M('oa_meeting');
        $streamKey = date('YmdHis');
        $post_info = I('post.'); // 电视端传入的信息
        // 创建房间成功之后存储到数据库
        if(!empty($post_info['meeting_no'])){
            $data['meeting_no'] = $post_info['meeting_no'];
        } else {
            $data['meeting_no'] =  getFlowNo(date("ym"), 'oa_meeting', 'meeting_no', '3');
        }
        // 查找是否存在和该会议相关的房间
        $meeting_room_map['meeting_no'] = $data['meeting_no'];
        $is_has = M('oa_meeting_room')->where($meeting_room_map)->find();
        if(!empty($is_has)){ // 含有 返回房间信息
            $room_info = $is_has;
            $data['status'] = 2;
            $data['msg'] = '该会议已经有房间，不需要创建房间';
        } else { // 未含有 1 创建房间
            // 会议房间信息
            $room_info['meeting_no'] = $data['meeting_no'];
            $room_info['room_no'] = $post_info['room_id'];
            $room_info['appId'] = $post_info['appId'];
            $room_info['room_name'] = $post_info['room_name'];
            $room_info['room_type'] = $post_info['type'];
            $room_info['room_creater'] = $post_info['communist_no'];
            $room_info['add_communist'] = $post_info['communist_no'];
            $room_info['status'] = 1;
            $room_info['add_time'] = date('Y-m-d H:i:s');
            $room_info['update_time'] = date('Y-m-d H:i:s');
            $room_info['streamTitle'] = $streamKey;
            $result = M('oa_meeting_room')->add($room_info);
            if($result){ // 创建房间成功之后 判断是否有相关会议（是否是临时会议） 临时会议需要保存会议信息 计划中的会议 不需要保存会议信息 直接返回房间信息
                if(empty($post_info['meeting_no'])){
                    $data['meeting_name'] = $post_info['room_name'];
                    $data['meeting_type'] = $post_info['meeting_type'];
                    $data['party_no'] = $post_info['party_no'];
                    $data['meeting_start_time'] = $post_info['meeting_start_time'];
                    $data['meeting_real_start_time'] = $post_info['meeting_start_time'];
                    $data['meeting_host'] = $post_info['communist_no'];
                    $data['add_time'] = date('Y-m-d H:i:s');
                    $data['update_time'] = date('Y-m-d H:i:s');
                    $data['status'] = '21';
                    $oa_meeting->add($data);
                    $comm_map['party_no'] = $post_info['party_no'];
                    $meeting_communist=M('ccp_communist')->where($comm_map)->getField('communist_no',true);
                    foreach($meeting_communist as &$communist){
                        //添加参会人员表特有字段
                        $meeting_comm_arr['communist_no']=$communist;
                        $meeting_comm_arr['party_no']=getCommunistInfo($communist,'party_no');
                        $meeting_comm_arr['meeting_no']=$data['meeting_no'];
                        //添加公共字段
                        $meeting_comm_arr['add_staff']=$post_info['communist_no'];
                        $meeting_comm_arr['status']=0;
                        $meeting_comm_arr['update_time']=date("Y-m-d H:i:s");
                        $meeting_comm_arr['add_time']=date("Y-m-d H:i:s");
                        $oa_meeting_communist->add($meeting_comm_arr);
                        /*if($communist_add){
                            $alert_title = "你有一条会议通知";
                            $alert_content = "你有一场".$_POST['meeting_name']."的会议预计在".$post_info['meeting_real_start_time']."开始,请准时参加！";
                            saveAlertMsg('14', $communist,$alert_url, $alert_title, $post_info['meeting_real_start_time'], '', '', $post_info['communist_no'],0,$alert_content);
                        }*/
                    }
                }
                $data['sql'] = $sql;
                $data['status'] = 1;
                $data['msg'] = '保存成功';
            } else {
                $data['status'] = 0;
                $data['msg'] = '保存失败，请重试！';
            }
        }
        $this->send($data['msg'], $room_info, $data['status']);
    }

    /**
     * get_meeting_home_list
     * @desc 电视端创建会议房间之后保存到数据库
     * @param type: 项目标示
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_home_list()
    {
        $oa_meeting_communist = M('oa_meeting_communist');
        $type = I('post.type'); // 项目标示
        $communist_no = I('post.communist_no'); // 项目标示
        $where['communist_no']=$communist_no;
        $meeting_nos = $oa_meeting_communist->where($where)->getField("meeting_no",true);
        $room_map['room_type'] = $type;
        $room_map['meeting_no'] = array('in',$meeting_nos);
        $room_list = M('oa_meeting_room')->where($room_map)->select();
        foreach ($room_list as &$room) {
            $room['communist_name'] = getCommunistInfo($room['room_creater']);
            $room['meeting_info'] = getMeetingInfos($room['meeting_no']);
        }
        if(!empty($room_list)){
            $data['status'] = 1;
            $data['msg'] = '获取成功';
        } else {
            $data['status'] = 0;
            $data['msg'] = '获取成功，房间列表为空';
        }
        $this->send($data['msg'], $room_list, $data['status']);
    }

    /**
     * get_meeting_home_active_user
     * @desc 电视端创建会议房间之后保存到数据库
     * @param type: 项目标示
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_home_active_user()
    {
        $appId = I('post.appId') ? I('post.appId') : 'dwzw7kjh9';
        $roomName = I('post.roomName') ? I('post.roomName') : '20181226'; // 房间名称
        // 引入appcliet包和鉴权包
        vendor("Qiniu.Rtc.AppClient");
        vendor("Qiniu.Auth");
        // accessKey 和 secretKey
        $ak = 'X9Rih9E0DPewKKVxk4UEAqOzyuaTS7kIVoGnzL0m';
        $sk = '64QBAoTfZGpN18zang5i-dWieftg5pQitB4eBlsr';
        // 针对accessKey 和 secretKey 进行auth鉴权
        $auth = new \Auth($ak,$sk);
        // // 实例化appclient
        $AppClient = new \AppClient($auth);
        // 获取房间人数
        $resp = $AppClient->listUser($appId, $roomName);
        $user_list = $resp[0]['users'];
        if(!empty($user_list)){
            $data['msg'] = '获取成功';
            $data['status'] = 1;
            foreach ($user_list as &$info) {
                $info['communist_name'] = getCommunistInfo($info['userId']);
            }
        } else {
            $data['msg'] = '获取失败，人数为空';
            $data['status'] = 0;
        }
        $this->send($data['msg'], $user_list, $data['status']);
    }

    /**
     * get_meeting_home_active_user
     * @desc 电视端创建会议房间之后保存到数据库
     * @param type: 项目标示
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_home_url()
    {
        $hubName = I('post.hub_name') ? I('post.hub_name') : 'sp-djdev';
        $host_url = I('post.host_url') ? I('post.host_url') : 'djdev.sp11.cn';
        $streamKey = I('post.stream_no') ? I('post.stream_no') :  date('YmdHis');
        // 引入appcliet包和鉴权包
        require join(array(INCLUDE_PATH, 'spcore/Library/Vendor/qnStream/', 'Pili_v2.php'));
        // accessKey 和 secretKey
        $ak = 'X9Rih9E0DPewKKVxk4UEAqOzyuaTS7kIVoGnzL0m';
        $sk = '64QBAoTfZGpN18zang5i-dWieftg5pQitB4eBlsr';
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        //创建hub
        $hub = $client->hub($hubName);
        // 创建streamKey
        // 创建stream
        //$resp = $hub->create($streamKey);
        // 获取直播状态
        $stream = $hub->stream($streamKey);
        //$listLive = $hub->listLiveStreams('', 5, "");
        // $status = $stream->liveStatus();
        // dump($status);
        //RTMP 直播放址
        $host_url = 'pili-live-rtmp.'.$host_url;
        $rtmp_url = \Qiniu\Pili\RTMPPlayURL($host_url, $hubName, $streamKey);
        if($rtmp_url){
            $data['msg'] = '获取成功';
            $data['status'] = 1;
            $data['url']['rtmp_url'] = $rtmp_url;
        } else {
            $data['msg'] = '获取失败';
            $data['status'] = 0;
        }
        $this->send($data['msg'], $data['url'], $data['status']);
    }

    /**
     * del_meeting_home
     * @desc 删除会议房间
     * @param communist_no: 删除人编号
     * @param room_no: 房间编号
     * @date 2018/12/24
     * @version 1.0.0
     */
    function del_meeting_home()
    {
        $communist_no = I('post.communist_no'); // 删除人编号
        $room_no = I('post.room_no'); // 房间编号
        $room_map['room_no'] = $room_no;
        $room_info = M('oa_meeting_room')->where($room_map)->find();
        if(!empty($room_info)){
            if($room_info['room_creater'] != $communist_no){
                $data['status'] = 0;
                $data['msg'] = '您不是会议房间的创建人不能删除该房间！';
            } else {
                $room_result = M('oa_meeting_room')->where($room_map)->delete();
                if($room_result){
                    $data['status'] = 1;
                    $data['msg'] = '删除房间成功！';
                } else {
                    $data['status'] = 0;
                    $data['msg'] = '删除房间失败！';
                }
            }
        }
        $this->send($data['msg'], $room_result, $data['status']);
    }

    /**
     * get_meeting_home_info
     * @desc 获取会议房间信息
     * @param communist_no: 删除人编号
     * @param room_no: 房间编号
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_home_info()
    {
        $room_no = I('post.room_no'); // 房间编号
        $room_map['room_no'] = $room_no;
        $room_creater = M('oa_meeting_room')->where($room_map)->getField('room_creater');
        $room_info['room_creater'] = $room_creater;
        if($room_creater){
            $data['status'] = 1;
            $data['msg'] = '获取成功！';
        } else {
            $data['status'] = 0;
            $data['msg'] = '获取失败！';
        }
        $this->send($data['msg'], $room_info, $data['status']);
    }

    /**
     * get_meeting_othertype_list
     * @desc 获取会议类型
     * @param communist_no: 删除人编号
     * @param room_no: 房间编号
     * @date 2018/12/24
     * @version 1.0.0
     */
    function get_meeting_othertype_list()
    {
        $type_map['type_group'] = 'meeting_type';
        $type_map['type_no'] = array('not in',array('2001','2002','2003','2004'));
        $type_list = M('bd_type')->where($type_map)->field('type_no,type_name')->select();
        if($type_list){
            $data['status'] = 1;
            $data['msg'] = '获取成功！';
        } else {
            $data['status'] = 0;
            $data['msg'] = '获取失败！';
        }
        $this->send($data['msg'], $type_list, $data['status']);
    }
}
