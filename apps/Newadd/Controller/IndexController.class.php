<?php
namespace System\Controller;//命名空间
use Think\Controller;
use Common\Controller\BaseController;

class IndexController extends BaseController//继承Controller类
{
	/**
	 * @name  index()
	 * @desc  首页
	 * @param 
	 * @return 
	 * @author 王彬
	 * @memo 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-16
	 */
	public function index()
	{
		checkLogin();
		$function_pid = I('get.function_pid',1);
		$this->assign("function_pid", $function_pid);
		$user_id = session('user_id'); // 获取登录的user_id
		$this->assign("user_id", $user_id);
		$user = M('sys_user');
		$user_map['user_id'] = $user_id;
		$user_info = $user->where($user_map)->field('user_name,user_pwd,user_role')->find();
		// 获取用户信息
		$this->assign('user_info', $user_info);
		$function_list = $this->getFunctionList($user_id); // 获取用户的菜单权限
		$this->assign('functionList', $function_list);
		$this->assign("main", getConfig('default_main'));
		$is_big_data = getConfig('is_big_data');
        $this->assign('is_big_data',$is_big_data);
		$this->display("index");
	}
	/**
	 * @name  sp_guide()
	 * @desc  引导页面
	 * @param 
	 * @return 
	 * @author 王彬
	 * @memo 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-16
	 */
	public function sp_guide()
	{
		checkLogin();
		
		$this->display("sp_guide");
	}
	/**
	 * @name  getFunctionList()
	 * @desc  获取用户权限相关菜单
	 * @param 
	 * @return 
	 * @author 王彬
	 * @memo 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-16
	 */
	protected function getFunctionList($user_id)
	{
		$user_map['user_id'] = $user_id;
		$user_role = M('sys_user')->where($user_map)->getField('user_role');
		/*根据session(user_id)查询菜单列表*/
		if (!empty($user_role)) {
			$function_list = M()->query("select * from sp_sys_function as f where f.function_id in ( select a.function_id from sp_sys_auth as a where a.role_id in ($user_role )) and f.function_type=0 and f.status=1 and f.function_pid = 0 and group_code = 'communist' order by f.function_order ");
		}
		//二级菜单
		foreach ($function_list as &$list) {
			$list['href'] = U($list['function_url']);
			$list['function_child'] = array();
			$list['is_child'] = 0;
			$list['function_pid'] = $list['function_pid'];
			$result = $this->GetFunctionChildList($list['function_id']);
			if ($result['status'] === 1) {
				$list['function_child'] = $result['function_list'];
				$list['is_child'] = 1;
				//三级菜单
				foreach ($list['function_child'] as &$list2) {
					$list2['href'] = U($list2['function_url']);
					$list2['function_child'] = array();
					$list2['is_child'] = 0;
					$result2 = $this->GetFunctionChildList($list2['function_id']);
					if ($result2['status'] === 1) {
						$list2['function_child'] = $result2['function_list'];
						$list2['is_child'] = 1;
						//四级菜单
						foreach ($list2['function_child'] as &$list3) {
							$list3['href'] = U($list3['function_url']);
							$list3['function_child'] = array();
							$list3['is_child'] = 0;
							$result3 = $this->GetFunctionChildList($list3['function_id']);
							if ($result3['status'] == 1) {
								$list3['function_child'] = $result3['function_list'];
								$list3['is_child'] = 1;
								//五级菜单
								foreach ($list3['function_child'] as &$list4) {
									$list4['href'] = U($list4['function_url']);
									$list4['function_child'] = array();
									$list4['is_child'] = 0;
									$result4 = $this->GetFunctionChildList($list4['function_id']);
									if ($result4['status'] === 1) {
										$list4['function_child'] = $result4['function_list'];
										$list4['is_child'] = 1;
									} else {
										$list4['is_child'] = 0;
									}
								}
							} else {
								$list3['is_child'] = 0;
							}
						}
					} else {
						$list2['is_child'] = 0;
					}
				}
			} else {
				$list['is_child'] = 0;
			}
		}
		return $function_list;
	}
	/**
	 * @name  GetFunctionChildList()
	 * @desc  获取菜单的下级权限菜单
	 * @param $function_id 父级菜单id
	 * @return 
	 * @author 王彬
	 * @memo 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-07-16
	 */
	protected function GetFunctionChildList($function_id)
	{
		$function_list = M('sys_function')->where("function_pid=$function_id and function_type=0 and status=1")->select();
		if ($function_list) {
			$result['status'] = 1;
			foreach ($function_list as &$list) {
				$list['href'] = U($list['function_url']);
			}
			$result['function_list'] = $function_list;
		} else {
			$result['status'] = 0;
		}
		return $result;
	}

	/**
	 * @name  get_function_json_list()
	 * @desc  获取当前菜单的子菜单json格式（点击菜单刷新方法使用）
	 * @param 
	 * @return 
	 * @author ljj
	 * @memo 
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2018-05-03
	 */
	public function get_function_json_list()
	{
		$function_pid = I('get.function_pid');
		$user_id = session('user_id');
		$user_map['user_id'] = $user_id;
		$user_role = M('sys_user')->where($user_map)->getField('user_role');
		/*根据session(user_id)查询菜单列表*/
		if (!empty($user_role)) {
			$function_map['function_id'] = $function_pid;
			$function_name = M('sys_function')->where($function_map)->getField('function_name');
			$function_list['code'] = 0;
			$function_list['title'] = $function_name;
			$function_list['msg'] = 0;
			$function_list['data'] = M()->query("select 
				f.function_id,f.function_name,f.function_url,f.function_pid,f.function_code,f.function_order,f.function_icon,f.status from sp_sys_function as f where f.function_id in ( select a.function_id from sp_sys_auth as a where a.role_id in ($user_role )) and f.function_type=0 and f.status=1 and f.function_pid = $function_pid order by f.function_order ");
		}
		if(!empty($function_list['data'])){
			//二级菜单
			foreach ($function_list['data'] as &$list) {
				$list['name'] = $list['function_code'];
				$list['title'] = $list['function_name'];
				$list['icon'] = $list['function_icon'];
				if(strpos($list['function_url'],'http') !==false){
					$list['jump'] = $list['function_url'];
					$list['code'] = 1;
				} else {
					$list['jump'] = U($list['function_url']);
					$list['code'] = 2;
				}
				$list['list'] = array();
				$list['is_child'] = 0;
				$result = $this->GetFunctionChildList($list['function_id']);
				if ($result['status'] === 1) {
					$list['list'] = $result['function_list'];
					$list['is_child'] = 1;
					//三级菜单
					foreach ($list['list'] as &$list2) {
						$list2['name'] = $list2['function_code'];
						$list2['title'] = $list2['function_name'];
						$list2['icon'] = $list['function_icon'];
						if(strpos($list2['function_url'],'http') !==false){
							$list2['jump'] = $list2['function_url'];
							$list2['code'] = 1;
						} else {
							$list2['jump'] = U($list2['function_url']);
							$list2['code'] = 2;
						}
						$list2['list'] = array();
						$list2['is_child'] = 0;
						$result2 = $this->GetFunctionChildList($list2['function_id']);
						if ($result2['status'] === 1) {
							$list2['list'] = $result2['function_list'];
							$list2['is_child'] = 1;
							//四级菜单
							foreach ($list2['list'] as &$list3) {
								$list3['name'] = $list3['function_code'];
								$list3['title'] = $list3['function_name'];
								$list3['icon'] = $list['function_icon'];
								if(strpos($list3['function_url'],'http') !==false){
									$list3['jump'] = $list3['function_url'];
									$list3['code'] = 1;
								} else {
									$list3['jump'] = U($list3['function_url']);
									$list3['code'] = 2;
								}
								$list3['list'] = array();
								$list3['is_child'] = 0;
								$result3 = $this->GetFunctionChildList($list3['function_id']);
								if ($result3['status'] == 1) {
									$list3['list'] = $result3['function_list'];
									$list3['is_child'] = 1;
									//五级菜单
									foreach ($list3['list'] as &$list4) {
										$list4['name'] = $list['function_code'];
										$list4['title'] = $list['function_name'];
										$list4['icon'] = $list['function_icon'];
										if(strpos($list4['function_url'],'http') !==false){
											$list4['jump'] = $list4['function_url'];
											$list4['code'] = 1;
										} else {
											$list4['jump'] = U($list4['function_url']);
											$list4['code'] = 2;
										}
										$list4['list'] = array();
										$list4['is_child'] = 0;
										$result4 = $this->GetFunctionChildList($list4['function_id']);
										if ($result4['status'] === 1) {
											$list4['list'] = $result4['function_list'];
											$list4['is_child'] = 1;
										} else {
											$list4['is_child'] = 0;
										}
									}
								} else {
									$list3['is_child'] = 0;
								}
							}
						} else {
							$list2['is_child'] = 0;
						}
					}
				} else {
					$list['is_child'] = 0;
				}
			}
		}
		ob_clean();$this->ajaxReturn($function_list);
	}

	/**
	  * @name  my_center()
	  * @desc  个人主页
	  * @param  
	  * @return  
	  * @author yangluhai
	  * @addtime   2016年9月2日下午4:44:53
	  * @updatetime  2016年9月2日下午4:44:53
	  * @version 0.01
	  */
	//个人主页
	public function my_center(){
		checkAuth(ACTION_NAME); 
		$industry=getConfig('industry');
		$this->assign('industry',$industry);
		$db_worklog=M('oa_worklog');
		$db_type=M('bd_type');
		$oa_willdo_log=M('oa_willdo_log');
		$staff_no=session('staff_no');
		$staff_name=getStaffInfo($staff_no,'staff_name');
		$this->assign('staff_name',$staff_name);
		$now_date=date('Y-m-d');
		//工作日志
		$worklog_info=$db_worklog->where("add_staff='$staff_no' and worklog_date='$now_date'")->find();
		$this->assign('worklog_info',$worklog_info);
		$worklog_list=getWorklogList($staff_no, '', '', '', '', '', '1', '','0','1','1','10');
		$this->assign('worklog_list',$worklog_list);
		// 获取每日必做任务
		$willdolist = getWilldoList($staff_no, "", $now_date, "", "", "", "0");
		$willdo_num=0;
		foreach ($willdolist as &$willdo) {
			$willdo['willdo_title']=mb_substr($willdo['willdo_title'],0,10,'utf-8');
			$willdo_status = getWilldoLogInfo($willdo['willdo_id'], $now_date, $staff_no,2);
			$willdo['log_status'] = $willdo_status['status'];
			if($willdo_status['status']==2){
				$willdo_num++;
			}
		}
		if (count($willdolist) > 0) {
			$percentage=round(($willdo_num/count($willdolist))*100).'%';
			//总数量
			$this->assign('willdo_count',count($willdolist));
		   //已完成的任务
			$this->assign('willdo_end',$willdo_num);
			//百分百
			$this->assign('percentage',$percentage);
			$this->assign("willdolist", $willdolist);
		}else{
			$this->assign('willdo_count',0);
			$this->assign('willdo_end',0);
			$this->assign('percentage','0%');
		}
		// 获取每日我执行的工作计划
		$workplanlist = getWorkplanList($staff_no, "", '11,21,31', "", "", "", "");
		$workplan_num=0;
		$plan_add=0;
		$plan_start=0;
		$plan_end=0;
		//当日需完成的工作
		$nowplan_list=array();
		foreach ($workplanlist as &$work) {
			$work['workplan_title']=mb_substr( $work['workplan_title'],0,10,'utf-8');
			if ($work['workplan_arranger_man'] != $staff_no) {
				$work['arranger_man'] = getStaffInfo($work['workplan_arranger_man']);
			}
			$work['executor_man'] = getStaffInfo($work['workplan_executor_man']);
			if($work['status']=='11'){
				$plan_add++;
				if($work['workplan_expectstart_time']<=$now_date){
					array_push($nowplan_list, $work);
				}
			}
			if($work['status']=='21'){
				 $plan_start++;
				 array_push($nowplan_list, $work);
			}
			if($work['status']=='31'){
				$plan_end++;
			}
		}
		foreach ($nowplan_list as &$nowplan){
			if (getWorkplanLogList($nowplan["workplan_id"], '21,22,23', $now_date)) {
				$nowplan["log_status"] = "1";
				$workplan_num++;
			}
		}
		if (count($workplanlist) > 0) {
			$percentage=round(($workplan_num/count($nowplan_list)*100)).'%';
			 $this->assign('planpercentage',$percentage);
			//我执行的所有工作计划
			$this->assign('workplan_sum',count($nowplan_list));
			$this->assign('workplan_end',$workplan_num);
			$this->assign('workplan_count',count($workplanlist));
			$this->assign("workplanlist", $workplanlist);
			$this->assign("nowplan_list",$nowplan_list);
		}else{
			$this->assign('planpercentage','0%');
			//我执行的所有工作计划
			$this->assign('workplan_sum','0');
			$this->assign('workplan_end','0');
			$this->assign('workplan_count','0');
		}
		$this->assign('plan_add',$plan_add);
		$this->assign('plan_start',$plan_start);
		$this->assign('plan_end',$plan_end);
		//我安排的工作计划
		$myplanlist=getWorkplanList("", $staff_no, "", "", "", "", "");
		$myplan_add=0;
		$myplan_start=0;
		$myplan_end=0;
		foreach ($myplanlist as &$myplan) {
			$myplan['workplan_title']=mb_substr($myplan['workplan_title'],0,10,'utf-8');
			$myplan['executor_man'] = getStaffInfo($myplan['workplan_executor_man']);
			if($myplan['status']=='11'){
				$myplan_add++;
			}
			if($myplan['status']=='21'){
				$myplan_start++;
			}
			if($myplan['status']=='31'){
				$myplan_end++;
			}
		}
		$this->assign('myplan_add',$myplan_add);
		$this->assign('myplan_start',$myplan_start);
		$this->assign('myplan_end',$myplan_end);
		$this->assign('myplanlist',$myplanlist);
		//我的提醒
		$staff_no11 = session('user_id');
		$alertmsg_list=getAlertMsgList($staff_no,0);
		$this->assign('alertmsg_list',$alertmsg_list);
		if(count($oaapproval_list)>0){
			$this->assign('oaapproval_sum',count($oaapproval_list));
			$this->assign('oaapproval_end',$num);
			$this->assign('oaapproval_list',$oaapproval_list);
		}else{
			$this->assign('oaapproval_sum',0);
			$this->assign('oaapproval_end',0);
			$this->assign('oaapppercentage','0%');
		}
		$this->display("my_center");
	}

	/**
	 * @name  baidu_map()
	 * @desc  百度地图
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-09-16
	 */
	public function baidu_map(){
		$party_no_auth = session('party_no_auth');//取下级组织
		$party_map['party_no'] = array('in',$party_no_auth);
		$party_list = M("ccp_party")->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->order('party_no asc')->select();
        $this->assign('party_list',$party_list);
        $web_address = getConfig('web_address');
        $this->assign('web_address',$web_address);
		$this->assign('party_list', $party_list);
		$this->assign('party_no', $party_list[0]['party_no']);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display('baidu_map');
	}
	/**
	 * @name  getPartyGC()
	 * @desc  用于党建地图返回党支部坐标
	 * @param party_no
	 * @return 有下级反下级坐标（json），无下级反自身坐标
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-09-16
	 */
	public function getPartyGC(){
		$party_no = I("get.party_no");
		$party_level = I("get.party_level");
		if(!empty($party_no)){
			$party_map['party_pno'] = $party_no;
			$party_map['party_no'] = $party_no;
			$party_map['_logic'] = 'or';
			$party_list = M("ccp_party")->where($party_map)->select();
		} else if(!empty($party_level)){
			$party_map['party_level_code'] = $party_level;
			$party_list = M("ccp_party")->where($party_map)->select();
		}
        $staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
        if(!empty($party_list)){
			foreach ($party_list as &$party) {
				if(!empty($party['memo'])){
					if(mb_strlen($party['memo'],'UTF8') >= 106){
						$party['memo'] = mb_substr($party['memo'], 0, 106, 'utf8')."..."; 
					}
				}
				if(!empty($party['party_manager'])){
					$manager_list = explode(",", $party['party_manager']); // 分割成数组

                	$manager_name = "";
					foreach ($manager_list as & $manager) {
	                    if (empty($manager_name)) {
	                        $manager_name = $staff_name_arr[$manager];
	                    } else {
	                        $manager_name .= ',' .  $staff_name_arr[$manager];
	                    }
	                }
                	$party['party_manager'] = $manager_name;
				}else{
					$party['party_manager'] = '暂无负责人';
				}
				// 获取党组织下级编号
				$party['party_nos'] = getPartyChildNos($party['party_no']);
				$party_nos = $party['party_nos'];
				// 获取党组织下的党员人数
				$comm_num_map['party_no']=array('in',$party_nos);
				$comm_num_map['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
				$party['communist_num'] = M("ccp_communist")->where($comm_num_map)->count();
				// 获取党组织的积分数
				$num_map['party_no']=array('in',$party_nos);
				$party['integral_num'] = M('ccp_party')->where($num_map)->getField('party_integral');
				// 获取党组织下的相关会议
				$party['meetting_num'] = M('oa_meeting')->where($num_map)->count();
				// 获取党组织下的党费缴纳情况
				$party['duse_num'] = M('ccp_dues')->where($num_map)->sum('dues_amount');
				// 判断积分数
				$party['integral_num'] = !empty($party['integral_num']) ? $party['integral_num'] : 0;
				// 判断会议数
				$party['meetting_num'] = !empty($party['meetting_num']) ? $party['meetting_num'] : 0;
				// 判断党费缴纳数
				$party['duse_num'] = !empty($party['duse_num']) ? $party['duse_num'] : 0;
				// 党支部风采图片
				$party['party_propagates'] = explode(',', getUploadInfo($party['party_propagate']));
				$party['propagate_html'] = '';
				if(!empty($party['party_propagate'])){
					foreach ($party['party_propagates'] as $img_val) {
						$party['propagate_html'] .= "<img class='mb-5 di-b' style='width: 100%; margin-left:5px;' src=".$img_val." alt=''>";
					}
				}
			}
		}
		if ($party_list) {
			ob_clean();$this->ajaxReturn($party_list);
		} else {
			ob_clean();$this->ajaxReturn(false);
		}
	}

	/**
	 * @name  baidu_map_grid()
	 * @desc  网格地图展示页面
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-10-13
	 */
	public function baidu_map_grid(){
		$party_no_auth = session('party_no_auth');//取下级组织
		$party_map['party_no'] = array('in',$party_no_auth);
		$party_list = M("ccp_party")->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->order('party_no asc')->select();
		$web_address = getConfig('web_address');
		// 获取党组织分级
		$code_map['code_group'] = 'party_level_code';
		$code_map['status'] = 1;
		$party_level_list = M('bd_code')->where($code_map)->field('code_id,code_no,code_name,code_group')->select();
		$this->assign('party_list', $party_list);
		$this->assign('party_level_list', $party_level_list);
		$this->assign('party_no', $party_list[0]['party_no']);
		$this->assign('web_address', $web_address);
		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display('baidu_map_grid');
	}
	/**
	 * @name  getPartyGCGrid()
	 * @desc  用于网格地图返回网格坐标数组
	 * @param party_no
	 * @author 靳邦龙
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-09-16
	 */
	public function getPartyGCGrid(){
		$party_no = I("get.party_no");
		$db_grid = M("ccp_party_grid");
		$party_map['party_pno'] = $party_no;
		$party_map['party_no'] = $party_no;
		$party_map['_logic'] = 'or';
		$party_list = M("ccp_party")->where($party_map)->field('party_no,party_name')->select();
		foreach ($party_list as &$party) {
			$party_no = $party['party_no'];
			$party['grid'] = $db_grid->where("party_no='$party_no'")->field('party_no,gc_lng,gc_lat')->select();
		}
		if ($party_list) {
			ob_clean();$this->ajaxReturn($party_list);
		} else {
			ob_clean();$this->ajaxReturn(false);
		}
	}
	/**
	 * @name  layer_chart()
	 * @desc  大数据右侧显示
	 * @author 刘丙涛
	 * @version 版本 V1.0.0
	 * @addtime   2017-10-20
	 */
	public function layer_chart(){
        //获取首页配置项
		$party_no = I('get.party_no');
		$_per_entering = M("perf_assess");
		$db_meeting = M('oa_meeting');
		$db_communist = M('ccp_communist');
        //绩效
        $perf_map['template_relation_type'] = 'party';
        $perf_map['_string'] = "FIND_IN_SET('$party_no',template_relation_no)";
		$template_data = M("perf_assess_template")->where($perf_map)->find();
		$item_map['item_group'] = 'party';
		$item_map['template_id'] = $template_data['template_id'];
		$assess_list = M("perf_assess_template_item")->where($item_map)->select();
		$date = getdatelist('', '12');
		$entering_scorea = "";
		foreach ($date as $datelist) {
			$month = $datelist['0'];
			$enteringa = 0;
			foreach ($assess_list as &$lists) {
				$entering = $_per_entering->where("assess_relation_type='party' and assess_cycle='$month' and item_id=" . $lists['item_id'] . " and assess_relation_no = '$party_no'")->getfield("assess_score") * ($lists['item_proportion'] / 100);
				$enteringa += $entering;
			}
			$entering_scorea .= empty($enteringa) ? "0," : $enteringa . ",";
		}
		$entering_scorea = substr($entering_scorea, 0, -1);
		$meeting_num = '';
		for ($i = 1; $i < 13; $i++) {
			$month = date('Y') . $i;
			$meeting_map['party_no'] = $party_no;
			$meeting_map['_string'] = "DATE_FORMAT(add_time,'%Y%c')='$month'";
			$meeting_num .= $db_meeting->where($meeting_map)->count() . ',';
		}
		$meeting_num = substr($meeting_num, 0, -1);
		$comm_map['communist_sex'] = 1;
		$comm_map['party_no'] = $party_no;
		$comm_map['status'] = 1;
		$male = $db_communist->where($comm_map)->count();
		$comm_map['communist_sex'] = 0;
		$female = $db_communist->where($comm_map)->count();
		$thirty = 0;
		$forty = 0;
		$fifty = 0;
        /*年龄*/
		$communist_data = M()->query("select  communist_sex,(year(now())-year(communist_birthday)-1) + ( DATE_FORMAT(communist_birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age,(year(now())-year(communist_ccp_date)-1) + ( DATE_FORMAT(communist_ccp_date, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as ccp_age from sp_ccp_communist where party_no=$party_no and status = 1");
        /*通过年龄统计比例*/
		foreach ($communist_data as $age) {
			if (abs($age['age']) < 30) {
				$thirty++;
			} elseif (abs($age['age']) < 50) {
				$forty++;
			} elseif (abs($age['age']) > 50) {
				$fifty++;
			}
		}
		$zero = 0;//0-10年
		$one = 0;//11-20年
		$two = 0;//0-10年
		$tee = 0;//11-20年
		$four = 0;//0-10年
		$five = 0;//11-20年
		foreach ($communist_data as $years) {
			if (abs($years['ccp_age']) <= 10) {
				$zero++;
			} elseif (abs($years['ccp_age']) <= 20) {
				$one++;
			} elseif (abs($years['ccp_age']) <= 30) {
				$two++;
			} elseif (abs($years['ccp_age']) <= 40) {
				$tee++;
			} elseif (abs($years['ccp_age']) <= 50) {
				$four++;
			} elseif (abs($years['ccp_age']) <= 60) {
				$five++;
			}
		}
		$diploma_list = M('bd_code')->where("code_group='diploma_level_code'")->select();
		foreach ($diploma_list as &$list) {
			$list['num'] = $db_communist->where("party_no='$party_no' and communist_diploma='" . $list['code_no'] . "' and status=1")->count();
		}
		$this->assign("diploma_list", $diploma_list);
		$this->assign("zero", $zero);
		$this->assign("one", $one);
		$this->assign("two", $two);
		$this->assign("tee", $tee);
		$this->assign("four", $four);
		$this->assign("five", $five);
		$this->assign("thirty", $thirty);
		$this->assign("forty", $forty);
		$this->assign("fifty", $fifty);
		$this->assign("male", $male);
		$this->assign("female", $female);
		$this->assign("entering_scorea", $entering_scorea);
		$this->assign("meeting_num", $meeting_num);
		$this->assign("party_name", getPartyInfo($party_no));
		$this->display('Index/layer_chart');
	}
	
	/**
	 * @name  main_dj()
	 * @desc  资料首页显示
	 * @author 刘丙涛
	 * @addtime   2017/10/20
	 * @version 1.0.0
	 */
	//用户资料首页显示
	public function main_dj(){
		$staff_no = session('staff_no');
        //消息提醒
		$rows = getAlertMsgList($staff_no, ALERTMSG_TIMELY,'','',0,0,5);//党员发展、会议、工作计划提醒提取一个月内数据
		$this->assign('alertmsg_count', $rows['count']);
		$this->assign('alertmsg_row', $rows['data']);
		// 查找党员表中的正式党员和预备党员数
		$party_name_arr = M('ccp_party')->where('status = 1')->getField('party_no,party_name_short');
		$communist_sql = "SELECT party1.party_no,count(comm1.communist_no) AS real_num,IFNULL(reday.ready_num,0) as ready_num,IFNULL(article_num.article_num,0) as article_num,party1.party_integral FROM sp_ccp_communist comm1 RIGHT JOIN sp_ccp_party party1 ON party1.party_no = comm1.party_no LEFT JOIN (SELECT party2.party_no,count(comm2.communist_no) AS ready_num FROM sp_ccp_communist comm2 RIGHT JOIN sp_ccp_party party2 ON comm2.party_no = party2.party_no WHERE comm2.`status` = 17 GROUP BY party2.party_no ) reday ON comm1.party_no = reday.party_no LEFT JOIN (SELECT party3.party_no, count(article.article_id) AS article_num FROM sp_ccp_party party3 RIGHT JOIN sp_cms_article article ON party3.party_no = article.party_no WHERE article.`status` = 1 GROUP BY party3.party_no) article_num ON comm1.party_no = article_num.party_no WHERE comm1.`status` = 21 GROUP BY party1.party_no ORDER BY party1.party_no asc limit 10";
		$communist_num_range = M()->query($communist_sql);
        $ready_num_str = '';
        $article_num_str = '';
        $integral_num_str = '';
        $real_num_str = '';
		if(!empty($communist_num_range)){
			foreach ($communist_num_range as $key => $range_value) {
				$communist_range[$range_value['party_no']]['real_num'] = $range_value['real_num']; // 正式党员
				$real_num_str.=$range_value['real_num'].',';
				$communist_range[$range_value['party_no']]['ready_num'] = $range_value['ready_num'];// 预备党员
				$ready_num_str.=$range_value['ready_num'].',';
				$communist_range[$range_value['party_no']]['party_name'] = $party_name_arr[$range_value['party_no']];
				$article_num_str.=$range_value['article_num'].','; // 文章上传量
				$integral_num_str.=$range_value['party_integral'].','; // 党组织积分数
			}
		}

		$this->assign('communist_range', $communist_range);
		$this->assign('real_num_str', substr($real_num_str, 0, -1));
		$this->assign('ready_num_str', substr($ready_num_str, 0, -1));
		$this->assign('article_num_str', substr($article_num_str, 0, -1));
		$this->assign('integral_num_str', substr($integral_num_str, 0, -1));


		$is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		$this->display('Index/main_dj');
	}
	/**
	 * @name		main_dj_data()
	 * @desc		首页/门户统计图数据
	 * @return 		
	 * @author		王玮琪
	 * @version		版本 V1.0.0
	 * @updatetime	2017-07-10
	 * @addtime		 
	 */
	public function main_dj_data(){
		$db_communist = M('ccp_communist');
		$db_party = M('ccp_party');
		$db_meeting = M('oa_meeting');

		$party_no_all = session('party_no_auth');//权限下的党组织编号
		$party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_all);
		$party_no_num = $db_party->where($party_map)->count();//党组织数量
		$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
		$comm_map['party_no'] = array('in',$party_no_all);
		$communist_num = $db_communist->where($comm_map)->count();//正式党员人数（流动党员）
		//$meeting_map['status'] = 23;
		$where_map['party_no'] = array('in',$party_no_all);
		//$where_map['_string'] = "DATE_FORMAT(add_time, '%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')";
		$meeting_num = $db_meeting->where($where_map)->count(); //活动数量
		// $article_count = M('cms_article')->where("article_cat = 10")->count();//品牌党建数量

		$minutes_count = M('ccp_communist_comment')->where($where_map)->count();//民主评议数量
		$volunteer_count = M('life_volunteer_activity')->count();//志愿者活动数量
		// $partyday_count = M('ccp_partyday_plan')->where($where_map)->count();//党日活动数量
		$activity_num = $meeting_num + $minutes_count + $volunteer_count;
        //党费统计
		$time = time();
		$month = date('Y-m', $time);
		$dues_map['dues_month'] = $month;
		$dues_map['status'] = 2;
		$dues_map['party_no'] = array('in',$party_no_all);
		$cost_sum = M("ccp_dues")->where($dues_map)->sum('dues_amount');
		if (empty($cost_sum)) {
			$cost_sum = 0;
		}
        /*年龄*/
		$communist_data = M()->query("select  communist_no,communist_sex,(year(now())-year(communist_birthday)-1) + ( DATE_FORMAT(communist_birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age,(year(now())-year(communist_ccp_date)-1) + ( DATE_FORMAT(communist_ccp_date, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as ccp_age from sp_ccp_communist where party_no in($party_no_all) and status in (" . COMMUNIST_STATUS_OFFICIAL . ")");
		foreach ($communist_data as $comm_val) {
			/*通过党龄统计比例*/
			if (abs($comm_val['ccp_age']) <= 10) {
				$zero++;
			} elseif (abs($comm_val['ccp_age']) <= 20) {
				$one++;
			} elseif (abs($comm_val['ccp_age']) <= 30) {
				$two++;
			} elseif (abs($comm_val['ccp_age']) <= 40) {
				$tee++;
			} elseif (abs($comm_val['ccp_age']) <= 50) {
				$four++;
			} elseif (abs($comm_val['ccp_age']) > 50) {
				$five++;
			}
			//  /*通过年龄统计比例*/
			// if (abs($comm_val['age']) <= 30) {
			// 	$thirty++;
			// } elseif (abs($comm_val['age']) <= 50) {
			// 	$forty++;
			// } elseif (abs($comm_val['age']) > 50) {
			// 	$fifty++;
			// }
			// /*通过性别统计比例*/
			// if (abs($comm_val['communist_sex']) == '1') {
			// 	$male++;
			// } else {
			// 	$female++;
			// }
		}
		$data['party_no_num'] = $party_no_num;//党组织数量
		$data['communist_num'] = $communist_num;//党员数量
		$data['activity_num'] = $activity_num;//活动数量
		$data['cost_sum'] = $cost_sum;//党费数量
        //年龄
		$data['thirty'] = $thirty;//三十以下
		$data['forty'] = $forty;//三十到五十
		$data['fifty'] = $fifty;//五十以上
        //性别
		$data['male'] = $male;//男
		$data['female'] = $female;//女
        //党龄
		$data['zero'] = $zero;//0-10
		$data['one'] = $one;//10-20
		$data['two'] = $two;//20-30
		$data['tee'] = $tee;//30-40
		$data['four'] = $four;//40-50
		$data['five'] = $five;//50以上

		//会议类型
		$oa_meeting=M("oa_meeting");
        $ccp_party = M('ccp_party');
        $map['type_group'] = 'meeting_type';
        $map['type_no'] = array('in','2001,2002,2003,2004');
        $type_list=M("bd_type")->where($map)->field("type_name,type_no")->select();

        foreach ($type_list as &$value11) {
            $value11['num'] = $oa_meeting->where("meeting_type=".$value11['type_no']."")->count();
        }
        $data['type_list'] = $type_list;

        //三会一课情况
        $party_num = $ccp_party->limit(7)->field("party_no,party_name,party_name_short")->select();
		$party_me_num = [];
		$party_name_num = [];
		foreach ($party_num as $value) {
			$party_me_num[] = $oa_meeting->where("party_no=".$value['party_no']."")->count();
			$party_name_num[] = $value['party_name_short'];

			
		}
		$data['party_me_num'] = $party_me_num;
		$data['party_name_num'] = $party_name_num;
		//随手拍
		$cat_name_list = [];
		$cat_name_num = [];
		$life_bbs_cat_list = M('life_condition_category')->field("cat_id,cat_name")->select();
		foreach ($life_bbs_cat_list as $mmp) {
			$cat_name_list[] = $mmp['cat_name'];
			$cat_name_num[] = M('life_condition')->where("type_no=".$mmp['cat_id']."")->count("condition_id");
		}
		$data['cat_name_num'] = $cat_name_num;
		$data['cat_name_list'] = $cat_name_list;
		ob_clean();$this->ajaxReturn($data);
	}
	/***********************考勤机管理模块开始   2017-10-20添加******************************/
	/**
	 * @name:att_machine_index
	 * @desc：考勤设置页面
	 * @param：
	 * @return：
	 * @author：王彬
	 * @addtime:2016-12-03
	 * @updatetime:2016-12-06
	 * @version：V1.0.0
	 **/
	public function att_machine_index(){
		checkAuth(ACTION_NAME);
		$setting = getTimeSetting();//获取时间设置
		$this->assign("setting", $setting);
	    //页签默认选中开始
		$active = I('get.active');
		if (empty($active)) {
			$active = 6;
		}
		$this->assign("active", $active);
		$this->display("Machine/att_machine_index");
	}
	/**
	 * @name:att_machine_list_data
	 * @desc：考勤机返回列表json数据
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_list_data(){
		$_att_machine = M("oa_att_machine");
		$machine_name = I('get.machine_name');
		$machine_map['status'] = 1;
		if(!empty($machine_name)){
			$machine_map['machine_name'] = array('like','%'.$machine_name.'%');
		}
		$att_machine_list_data = $_att_machine->where($machine_map)->select();
		foreach ($att_machine_list_data as &$v) {
			$v['party_name'] = getPartyInfo($v['party_no']);
			if ($v['machine_type'] == '1') {
				$v['machine_type_name'] = '会议签到';
			} else {
				$v['machine_type_name'] = '考勤签到';
			}
		}
		if ($att_machine_list_data) {
			$machine_data['code'] = 0;
			$machine_data['msg'] = '获取数据成功';
			$machine_data['count'] = 0;
			$machine_data['data'] = $att_machine_list_data;
			ob_clean();$this->ajaxReturn($machine_data);
		} else {
			$machine_data['code'] = 1;
			$machine_data['msg'] = '暂无相关数据';
			$machine_data['count'] = 0;
			$machine_data['data'] = '';
			ob_clean();$this->ajaxReturn($machine_data);
		}

	}
	/**
	 * @name:att_machine_edit
	 * @desc：考勤机添加修改方法
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_edit(){
		if (!empty(I('get.machine_no'))) {
			$machine_no = I('get.machine_no');
			$row = getMachineInfo($machine_no);
			if ($row) {
				$this->assign('row', $row);
			}
		}
		$this->display("Machine/att_machine_edit");
	}
	/**
	 * @name:att_machine_do_save
	 * @desc：考勤机保存方法
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_do_save(){
		$att_machine = M("oa_att_machine");
		if (!empty($_POST)) {
	        //添加考勤表特有字段
			$data["machine_no"] = $_POST['machine_no'];
			$data["machine_name"] = $_POST['machine_name'];
			$data["party_no"] = $_POST['party_no'];
			$data["machine_addr"] = $_POST['machine_addr'];
			$data["machine_type"] = $_POST['machine_type'];
	        //添加公共字段
			$data['add_staff'] = session('staff_no');
			$data['status'] = "1";
			$data['update_time'] = date("Y-m-d H:i:s");
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['command_status'] = "0";

			$where["machine_no"] = $data["machine_no"];
			$finds = $att_machine->where($where)->getField("machine_no");
			if (!empty($finds)) {
				$att_machine_do_save = $att_machine->where($where)->save($data);
			} else {
				$att_machine_do_save = $att_machine->add($data);
			}
			if ($att_machine_do_save) {
				showMsg("success", "操作成功！", U('att_machine_index', array("active" => '6')));
			} else {
				showMsg("success", "执行失败，请联系管理员。", U('att_machine_index', array("active" => '6')));
			}
		}
	}

	/**
	 * @name:att_machine_download
	 * @desc：下载党员信息到考勤机
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_download(){
		$_att_machine = M("oa_att_machine");
		$machine_no = I("get.machine_no");
		$res1["command_status"] = "2";
		$res1['update_time'] = date("Y-m-d H:i:s");
		$res1['add_time'] = date("Y-m-d H:i:s");
		$machine_map['machine_no'] = $machine_no;
		$command_save = $_att_machine->where($machine_map)->save($res1);
		if ($command_save) {
			showMsg("success", "人员信息下载到考勤机开始执行！", U('att_machine_index', array("active" => '6')));
		} else {
			showMsg("success", "操作失败，请联系程序员！", U('att_machine_index', array("active" => '6')));
		}
	}
	/**
	 * @name:att_machine_upload
	 * @desc：考勤机上传党员信息
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_upload(){
		$_att_machine = M("oa_att_machine");
		$machine_no = I("get.machine_no");
		$res1["command_status"] = "1";
		$res1['update_time'] = date("Y-m-d H:i:s");
		$res1['add_time'] = date("Y-m-d H:i:s");
		$machine_map['machine_no'] = $machine_no;
		$command_save = $_att_machine->where($machine_map)->save($res1);
		if ($command_save) {
			showMsg("success", "考勤机人员信息上传开始执行！", U('att_machine_index', array("active" => '6')));
		} else {
			showMsg("success", "操作失败，请联系程序员！", U('att_machine_index', array("active" => '6')));
		}
	}
	/**
	 * @att_machine_do_del
	 * @desc：考勤机删除
	 * @param：
	 * @return：
	 * @author：黄子正
	 * @addtime:2017-04-11
	 * @updatetime:2017-04-11
	 * @version：V1.0.0
	 **/
	public function att_machine_do_del(){
		$_att_machine = M("oa_att_machine");
		$oa_meeting = M("oa_meeting");
		$machine_no = I("get.machine_no");
		$machine_map['machine_no'] = $machine_no;
		if (!empty($machine_no)) {
	        //考勤机被使用时，不能删除
			$meeting_count = $oa_meeting->where($machine_map)->count();
			if ($meeting_count != 0) {
	            //考勤机使用中，执行失败
				showMsg("success", "考勤机使用中，无法删除！", U('att_machine_index', array("active" => '6')));
			} else {
				$del = $_att_machine->where($machine_map)->delete();
				if ($del) {
	                //执行成功
					showMsg("success", "操作成功！", U('att_machine_index', array("active" => '6')));
				} else {
	                //执行失败
					showMsg("success", "操作失败", U('att_machine_index', array("active" => '6')));
				}
			}
		} else {
	        //执行失败
			showMsg("success", "操作失败", U('att_machine_index', array("active" => '6')));
		}
	}

	/**
	 * @name   alertmsg_index
	 * @desc：  消息提醒首页
	 * @author：ljj
	 * @addtime:2018-04-03
	 * @version：V1.0.0
	 **/
	function alertmsg_index(){
		$this->display();
	}
	/**
	 * @name   alertmsg_jump
	 * @desc：  消息提醒的跳转
	 * @author：靳邦龙
	 * @addtime:2017-11-18
	 * @version：V1.0.0
	 **/
	function alertmsg_jump(){
		$alert_id = I('get.alert_id', '');
		$db_alert = M("bd_alertmsg");
		if ($alert_id) {
			$data['alert_id'] = $alert_id;
			$data['status'] = 1;
			$data['update_time'] = date("Y-m-d H:i:s");
			$res = $db_alert->where($where)->save($data);
		}
		if ($res) {
			$alert_map['alert_id'] = $alert_id;
			$url = $db_alert->where($alert_map)->getField('alert_url');
			$url = U($url);
			echo "<script>location.href='$url';</script>";
		} else {
			showMsg('error', '跳转错误');
		}
	}

	/**
	 * @name   alertmsg_data
	 * @desc：  消息提醒的刷新
	 * @author：靳邦龙
	 * @addtime:2017-11-18
	 * @version：V1.0.0
	 **/
	function alertmsg_data(){
		//消息提醒
		$staff_no = session('staff_no');
		$rows = getAlertMsgList($staff_no, ALERTMSG_INSTANT,'','',0,0,5);//即时提醒
		$alertmsg_count = count($rows);
		if (empty($alertmsg_count)) {
			$alertmsg_count = "";
		}
		if (empty($rows)) {
			$rows = "";
		}
		$this->assign('alertmag_list', $rows);
	}
	/***********************考勤机管理模块结束   2017-10-20添加******************************/
	/**
	 * @name   alertmsg_data
	 * @desc：  消息提醒的刷新
	 * @author：靳邦龙
	 * @addtime:2017-11-18
	 * @version：V1.0.0
	 **/
	function print_table(){
		$word = new \COM(".application",null,CP_UTF8) or die("Can't start Word!"); 
		$word->Documents->OPen("http://dangjian.sp11.cn/uploads/tpl_xls/print.docx"); 
		$test= $word->ActiveDocument->content->Text; 
		echo $test; 
		echo "<br>"; 
		//打开文件,（只读模式+二进制模式） 
	  // 	@$fp=fopen("http://dangjian.sp11.cn/uploads/tpl_xls/print.docx",'r'); 
	  // 	flock($fp,LOCK_SH); 
	  // 	if(!$fp){ 
	  //   	echo "<p><strong>订单没有加载，请再试一次</strong></p>"; 
	  //   	exit; 
	 	// } 
	  // 	while(!feof($fp)){ 
	  //  		$order=fgets($fp,999); 
	  //   	echo $order."<br/>"; 
	  // 	} 
	  // 	//释放已有的锁定 
	  // 	flock($fp,LOCK_UN); 
	  // 	//关闭文件流 
	  // 	fclose($fp); 
	}

}

