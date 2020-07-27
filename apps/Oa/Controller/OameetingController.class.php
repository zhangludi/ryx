<?php
/*******************************会议管理（三会一课）************************************/
namespace Oa\Controller;
use Common\Controller\BaseController;
class OameetingController extends BaseController{
    /**
     * @name:oa_meeting_index
     * @desc：会议开展主页
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_index(){
		$type = I('get.type');
		switch($type){
			case 2005:
				$cat = "_2";
			break;
			case 2006:
				$cat = '_3';
			break;
			case 2007:
				$cat = '_4';
			break;
			default:
				$cat = "_1";break;
		}
    	session('cat',$cat);
		checkAuth(ACTION_NAME.$cat);//判断越权
        $party_no = I('get.party_no');
		
		$this->assign('party_no',$party_no);
		if(!empty($party_no)){
			$party_nos = getPartyChildMulNos($party_no,'arr');
		}else{
			$party_nos = session('party_no_auth');//取本级及下级组织
		}
		if(!empty($type)){
			$meeting_where['party_no'] = array('in',$party_nos);
			$meeting_where['meeting_type'] = array('in',$type);
			$count = M('oa_meeting')->where($meeting_where)->count();
			$this->assign('count',$count);
		}else{
			$type = '2001,2002,2003,2004';
			$type_where['type_group'] = 'meeting_type';
			$type_where['type_no'] = array('in','2001,2002,2003,2004');
			$type_list = M('bd_type')->where($type_where)->field('type_no,type_name')->select();
			foreach($type_list as &$list){
				$where['party_no'] = array('in',$party_nos);
				$where['meeting_type'] = $list['type_no'];
				$list['meeting_count'] = M('oa_meeting')->where($where)->count();
				$map['party_no'] = array('in',$party_nos);
				$map['meeting_type'] = $list['type_no'];
				$map['status'] = 23;
				$list['end_meeting_count'] = M('oa_meeting')->where($map)->count();
			}
			$this->assign('type_list',$type_list);
			$meeting_where['party_no'] = array('in',$party_nos);
			$meeting_where['meeting_type'] = array('in','2001,2002,2003,2004');
			$count = M('oa_meeting')->where($meeting_where)->count();
			$this->assign('count',$count);
		}        
        $this->assign('type',$type);
        $this->display("Oameeting/oa_meeting_index");
    }
    /**
     * @name:oa_meeting_index_data
     * @desc：获取会议数据
     * @param：
     * @return：
     * @author：王宗彬  王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-11-29
     * @version：V1.0.0
     **/
    public function oa_meeting_index_data(){
    	//实例化会议表对象
    	$oa_meeting=M("oa_meeting");
    	// 搜索条件
    	$meeting_name = I('post.meeting_name');
    	$meeting_type = I('post.meeting_type');
    	$party_no = I('post.party_no');
    	$status = I('post.status');
        $pagesize = I('post.pagesize');
    	$page = (I('post.page') - 1) * $pagesize;
    	if(!empty($party_no)){
    		$party_nos = getPartyChildNos($party_no);
    	}else{
    		$party_nos = session('party_no_auth');//取本级及下级组织
    	}
		$where['party_no'] = array('in',$party_nos);
    	if(!empty($meeting_type)){
			$where['meeting_type'] = array('in',$meeting_type);
    	}
		if(!empty($meeting_name)){
			$where['meeting_name'] = array('like','%'.$meeting_name.'%');
		}
		if(!empty($status)){
			$where['status'] = array('in',$status);
		}
		$meeting_list = M('oa_meeting')->where($where)->limit($page,$pagesize)->order("add_time desc")->select();
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $status_map['status_group'] = 'meeting_status';
    	$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
    	$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');	
		$meeting_content = '';
		foreach($meeting_list as $list){
			$result2 = getMeetingCommunistCount($list['meeting_no']);
			$list['meeting_host'] = getCommunistInfo($list['meeting_host']);
            $list['communist_num']=$result2['attended']."/".$result2['should'];
            $list['percentage']=($result2['attended']/$result2['should'])*100;	
			$list['meeting_start_time'] = getFormatDate($list['meeting_start_time'], 'Y-m-d') ;
			
			switch($list['meeting_type']){
				case '2001':
					$list['meeting_type_name'] = '支部大会';
					break;
				case '2002':
					$list['meeting_type_name'] = '支委会议';
					break;
				case '2003':
					$list['meeting_type_name'] = '党小组会';
					break;
				case '2004':
					$list['meeting_type_name'] = '党课';
					break;
				case '2005':
					$list['meeting_type_name'] = '组织生活';
					break;
				case '2006':
					$list['meeting_type_name'] = '民主生活';
					break;
				case '2007':
					$list['meeting_type_name'] = '主题党日';
					break;
			}
			$list['status_name'] = "<font color='" . $status_color_arr[$list['status']] . "'>" . $status_name_arr[$list['status']] . "</font> ";//getStatusName('meeting_status',$data['status']);
			$start='';
			$end='';
			if(getPartyData('is_admin',session('staff_no') || $list['add_staff'] == session('staff_no'))){  // 是否为 添加人 和超级管理员
				$start = "<div class='status_btn' onclick='status_start(&#34;$list[meeting_no]&#34;,&#34;$list[meeting_type]&#34;,&#34;$party_no&#34;)' >开始</div>";
				$end = "<div class='status_btn' onclick='status_end(&#34;$list[meeting_no]&#34;,&#34;$list[meeting_type]&#34;,&#34;$party_no&#34;)' >结束</div>";
				// $start = "<a class='layui-btn yellow btn-outline layui-btn-xs' href='" . U('oa_meeting_do_start', array('meeting_no' => $data[meeting_no],'type'=>$meeting_type,'party_no'=>$data['party_no'])) . "'> 会议开始 </a>  ";
				// $end = "<a class='layui-btn red btn-outline layui-btn-xs' href='" . U('oa_meeting_do_end', array('meeting_no' => $data[meeting_no],'type'=>$meeting_type,'party_no'=>$data['party_no'])) . "'> 结束</a>  ";
			}
            // if($list['status']=="23"){
				 // $operate="<div class='info_btn' onclick='info($list[meeting_no])' >详情</div>&nbsp;<div class='edit_btn' onclick='edit($list[meeting_no],$list[meeting_type])' >编辑</div>";
                $operate="<a class=' layui-btn layui-btn-primary layui-btn-xs' onclick='att_add(&#34;$data[meeting_no]&#34;)' >查看</a>"."<a class=' layui-btn  layui-btn-xs layui-btn-f60'  href='" . U('Oameeting/oa_meeting_edit',array('meeting_no'=>$data['meeting_no'])) . "' >编辑</a>";
            // }elseif($list['status']=="21"){
				$operate="<a class=' layui-btn layui-btn-primary layui-btn-xs' onclick='att_add(&#34;$data[meeting_no]&#34;)'>查看</a>".$end;
				 // $operate="<div class='info_btn' onclick='info($list[meeting_no])' >详情</div>&nbsp;".$end;
			// }else{
				$operate="<a class=' layui-btn layui-btn-primary layui-btn-xs' onclick='att_add(&#34;$data[meeting_no]&#34;)'>查看</a>"."<a class=' layui-btn  layui-btn-xs layui-btn-f60' href='" . U('Oameeting/oa_meeting_edit',array('meeting_no'=>$data['meeting_no'])) . "'  >编辑</a>"."<a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('oa_meeting_do_del', array('meeting_no' => $data[meeting_no],'party_no'=>$data['party_no'],'type'=>$meeting_type)) . "'$confirm >删除</a>".$start;
				 // $operate="<div class='info_btn' onclick='info($list[meeting_no])' >详情</div>&nbsp;<div class='edit_btn' onclick='edit($list[meeting_no],$list[meeting_type])' >编辑</div>&nbsp;".$start;
			// }
			switch($list['status']){
				case '11':
					$operate="<div class='info_btn' onclick='del(&#34;$list[meeting_no]&#34;,&#34;$meeting_type&#34;,&#34;$party_no&#34;)' >删除</div>&nbsp;<div class='edit_btn' onclick='edit($list[meeting_no],$list[meeting_type])' >编辑</div>&nbsp;".$start;
												
					$meeting_content .= "<div class='layui-row list_content'>
						<div class='layui-col-lg5 layui-col-md4'>
							<div class='content_left' onclick='info($list[meeting_no])'  style='cursor: pointer;'>
								<div class='content_left_title' >{$list['meeting_type_name']}</div>
								<div class='content_left_content'>
									<p class='content_left_content-1 wryh_blod article_over_one'  title='{$list['meeting_name']}' >{$list['meeting_name']}</p>
									<p class='content_left_content-2 article_over_one' title='{$party_name_arr[$list['party_no']]}' >{$party_name_arr[$list['party_no']]}</p>
								</div>
							</div>
						</div>
						<div class='layui-col-lg7 layui-col-md8'>
							<div class='layui-row'>
								<div class='layui-col-lg1 layui-col-md1' style='width:11.33333333%'>
									<p class='content-right-title'>状态</p>
									<p class='color-handle fsize-12em ' >{$list['status_name']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2m'>
									<p class='content-right-title'>开始时间</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_start_time']}' >{$list['meeting_start_time']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2em'>
									<p class='content-right-title'>会议地点</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_addr']}' >{$list['meeting_addr']}</p>
								</div>
								<div class='layui-col-lg1 layui-col-md1'>
									<p class='content-right-title'>主持人</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_host']}' >{$list['meeting_host']}</p>
								</div>
								<div class='layui-col-lg3 layui-col-md3 pt-2em' style='width:22%;'>
									<div class='layui-progress dis-block' lay-showPercent='true' >
										<div class='layui-progress-bar layui-bg-red' style='width: {$list['percentage']}%';></div>
									</div>
									<span class='color-ec322a f-18em-cur'>{$list['communist_num']}</span> 
								</div>								
								<div class='layui-col-lg3 layui-col-md3'>
									<div class='pull-right mt-5'>{$operate}										
									</div>
								</div>
							</div>
						</div>
					</div>";
					break;
				case '21':				
					$operate=$end;
					$meeting_content .= "<div class='layui-row list_content'>
						<div class='layui-col-lg5 layui-col-md4'>
							<div class='content_left' onclick='info($list[meeting_no])'  style='cursor: pointer;'>
								<div class='content_left_title'>{$list['meeting_type_name']}</div>
								<div class='content_left_content'>
									<p class='content_left_content-1 wryh_blod article_over_one'  title='{$list['meeting_name']}' >{$list['meeting_name']}</p>
									<p class='content_left_content-2 article_over_one' title='{$party_name_arr[$list['party_no']]}' >{$party_name_arr[$list['party_no']]}</p>
								</div>
							</div>
						</div>
						<div class='layui-col-lg7 layui-col-md8'>
							<div class='layui-row'>
								<div class='layui-col-lg1 layui-col-md1' style='width:11.33333333%'>
									<p class='content-right-title'>状态</p>
									<p class='color-handle fsize-12em ' >{$list['status_name']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2m'>
									<p class='content-right-title'>开始时间</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_start_time']}' >{$list['meeting_start_time']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2em'>
									<p class='content-right-title'>会议地点</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_addr']}' >{$list['meeting_addr']}</p>
								</div>
								<div class='layui-col-lg1 layui-col-md1'>
									<p class='content-right-title'>主持人</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_host']}' >{$list['meeting_host']}</p>
								</div>
								<div class='layui-col-lg3 layui-col-md3 pt-2em' style='width:22%;'>
									<div class='layui-progress dis-block' lay-showPercent='true' >
										<div class='layui-progress-bar layui-bg-red' style='width: {$list['percentage']}%';></div>
									</div>
									<span class='color-ec322a f-18em-cur'>{$list['communist_num']}</span> 
								</div>								
								<div class='layui-col-lg3 layui-col-md3' style='text-align: center;'>
									<div class=' mt-5'>{$operate}										
									</div>
								</div>
							</div>
						</div>
					</div>";
					break;
				case '23':
					$operate="<div class='info_btn' onclick='del(&#34;$list[meeting_no]&#34;,&#34;$meeting_type&#34;,&#34;$party_no&#34;)' >删除</div>&nbsp;<div class='record_btn' onclick='record($list[meeting_no])' >会议总结</div>";
					$meeting_content .= "<div class='layui-row list_content'>
						<div class='layui-col-lg5 layui-col-md4'>
							<div class='content_left' onclick='info($list[meeting_no])' style='cursor: pointer;'>
								<div class='content_left_title'>{$list['meeting_type_name']}</div>
								<div class='content_left_content'>
									<p class='content_left_content-1 wryh_blod article_over_one'  title='{$list['meeting_name']}' >{$list['meeting_name']}</p>
									<p class='content_left_content-2 article_over_one' title='{$party_name_arr[$list['party_no']]}' >{$party_name_arr[$list['party_no']]}</p>
								</div>
							</div>
						</div>
						<div class='layui-col-lg7 layui-col-md8'>
							<div class='layui-row'>
								<div class='layui-col-lg1 layui-col-md1'  style='width:11.33333333%'>
									<p class='content-right-title'>状态</p>
									<p class='color-handle fsize-12em ' >{$list['status_name']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2m'>
									<p class='content-right-title'>开始时间</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_start_time']}' >{$list['meeting_start_time']}</p>
								</div>
								<div class='layui-col-lg2 layui-col-md2 ml-2em'>
									<p class='content-right-title'>会议地点</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_addr']}' >{$list['meeting_addr']}</p>
								</div>
								<div class='layui-col-lg1 layui-col-md1'>
									<p class='content-right-title'>主持人</p>
									<p class='content-right-text article_over_one' title='{$list['meeting_host']}' >{$list['meeting_host']}</p>
								</div>
								<div class='layui-col-lg3 layui-col-md3 pt-2em' style='width:22%;'>
									<div class='layui-progress dis-block' lay-showPercent='true' >
										<div class='layui-progress-bar layui-bg-red' style='width: {$list['percentage']}%';></div>
									</div>
									<span class='color-ec322a f-18em-cur'>{$list['communist_num']}</span> 
								</div>								
								<div class='layui-col-lg3 layui-col-md3'  style='text-align: center;'>
									<div class=' mt-5'>{$operate}										
									</div>
								</div>
							</div>
						</div>
					</div>";
					break;
			}
		}
		$data['meeting_content'] = $meeting_content;
        ob_clean();$this->ajaxReturn($data);
    }
	/**
     * @name:oa_meeting_count_ajax
     * @desc：获取会议数据
     * @param：
     * @return：
     * @author：wangzongbin
     * @addtime:2019-12-12
     * @updatetime:2019-12-12
     * @version：V1.0.0
     **/
    public function oa_meeting_count_ajax(){
		$oa_meeting=M("oa_meeting");
		// 搜索条件
    	$meeting_name = I('post.meeting_name');
    	$meeting_type = I('post.meeting_type');
    	$status = I('post.status');
    	$party_no = I('post.party_no');
    	if(!empty($party_no)){
    		$party_nos = getPartyChildNos($party_no);
    	}else{
    		$party_nos = session('party_no_auth');//取本级及下级组织
    	}
		$where['party_no'] = array('in',$party_nos);
    	if(!empty($meeting_type)){
			$where['meeting_type'] = array('in',$meeting_type);
    	}
		if(!empty($meeting_name)){
			$where['meeting_name'] = array('like','%'.$meeting_name.'%');
		}
		if(!empty($status)){
			$where['status'] = array('in',$status);
		}
		$data['count'] = M('oa_meeting')->where($where)->count();
		ob_clean();$this->ajaxReturn($data);
	}
    /**
     * @name:oa_meeting_edit
     * @desc：会议增加修改方法
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_edit(){
		$type = I('get.type');
		switch($type){
			case 2005:
				$cat = "_2";
				$meeting_type = 2005;
			break;
			case 2006:
				$cat = '_3';
				$meeting_type = 2006;
			break;
			case 2007:
				$cat = '_4';
				$meeting_type = 2007;
			break;
			default:
				$cat = "_1";
				$meeting_type = '2001,2002,2003,2004,2005';
				break;
		}
		$this->assign('meeting_type',$meeting_type);
        session('cat',$cat);
    	checkAuth(ACTION_NAME.$cat);
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表对象
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表对象
        $oa_meeting_communist=M("oa_meeting_communist");
        if(!empty(I("get.meeting_no"))){
            $meeting_no=I("get.meeting_no");
            $row=array();
            $where['meeting_no']="$meeting_no";
            //获取会议表数据
            $row=$oa_meeting->where($where)->find();
            //获取参会人员编号
            $communist_names_no=$oa_meeting_communist->where($where)->getField("communist_no",true);
            $row['meeting_host_name'] = getCommunistInfo($row['meeting_host']);
            //获取参会人员姓名
            $name_arr=array();
            
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            foreach($communist_names_no as $k=>$v){
                $name_arr[$k] = $communist_name_arr[$v];
            }
            $str=implode(",",$communist_names_no).",";
            $str1 = rtrim($str,',');
            $row['meeting_communist_no']=$str1;
            $row['meeting_communist_name']=implode(",",$name_arr);
            $action_name="会议修改";
        }
        if(empty($action_name)){
        	$party_no = I('get.party_no');
        	$row['party_no'] = $party_no;
            $action_name="会议添加";
            $type = I('get.type');

            if(!empty($type)){
            	$row['meeting_type'] = $type;
            }
        }
        $this->assign("row",$row);
        $this->assign("action_name",$action_name);
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
        $this->display("Oameeting/oa_meeting_edit");
    }
    /**
     * @name:oa_meeting_do_save
     * @desc：会议保存方法
     * @param：
     * @return：
     * @author：王宗彬  王宗彬(修改添加人员)
     * @addtime:2017-04-11
     * @updatetime:2017-11-29
     * @version：V1.0.0
     **/
    public function oa_meeting_do_save(){
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表对象
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表对象
        $oa_meeting_communist=M("oa_meeting_communist");
        //添加会议表特有字段
        if(!empty(I("post.meeting_no"))){
            $data['meeting_no']=I("post.meeting_no");
            $do_type="update";
        }
        else{
            $data['meeting_no']=  getFlowNo(date("ym"), 'oa_meeting', 'meeting_no', '3');
            $do_type="add";
        }		
        $data['meeting_name']=$_POST['meeting_name']; // 会议名称
        $data['meeting_addr']=$_POST['meeting_addr']; // 会议地址
        $data['meeting_start_time']=$_POST['meeting_start_time']; // 开始时间
        $data['meeting_end_time']=$_POST['meeting_end_time']; // 结束时间
        $data['party_no']=$_POST['party_no']; // 党组织编号
        $data['meeting_host']=$_POST['meeting_host_no']; // 主持人
        $data['machine_no']=$_POST['machine_no']; // 考勤机编号
        $data['meeting_type']=$_POST['meeting_type']; // 会议类型
        $data['meeting_integral']=$_POST['meeting_integral']; // 会议积分
        $data['meeting_camera']=$_POST['meeting_camera']; // 摄像头
        $data['meeting_video']=$_POST['meeting_video']; // 会议视频
        $data['meetting_thumb']=$_POST['meetting_thumb']; // 图片
        $data['meetting_attach']=$_POST['meetting_attach']; // wenjian
        $data['meetting_mien']=$_POST['meetting_mien']; // fengcai
        $data['memo']=$_POST['memo']; // 备注
        if(!empty($_POST['status'])){
            $data['status']=$_POST['status'];
        }else{
            $data['status']="11";
        }
        //添加公共字段
        $data['add_staff']=session('staff_no');
        $data['update_time']=date("Y-m-d H:i:s");
        $data['add_time']=date("Y-m-d H:i:s");
        $type = $data['meeting_type'];
        if($do_type=="update"){
            $where["meeting_no"]=$data['meeting_no'];
            $meeting_add=$oa_meeting->where($where)->save($data);
            $status = $oa_meeting->where($where)->getField('status');
            $alert_url = "Oa/Oameeting/oa_meeting_info/meeting_no/".$data['meeting_no'];
        }else{
            $meeting_add=$oa_meeting->add($data);
            $alert_url = "Oa/Oameeting/oa_meeting_info/meeting_no/".$meeting_add['meeting_no'];
        }
        if($meeting_add){
            // 参会人员
        	$meeting_communist=strtoArr($_POST['meeting_communist_no']);
            // 主持人
            $meeting_host_no=strtoArr($_POST['meeting_host_no']);
        	$meeting_no=$data['meeting_no'];
            $meeting_map['meeting_no'] = $meeting_no;
            //判断所修改会议否有参会人员
            $communist_is_del=$oa_meeting_communist->where($meeting_map)->getField("communist_no",true);
            if($communist_is_del){
                $communist_del=$oa_meeting_communist->where($meeting_map)->delete();
            }
            if(!empty($meeting_communist)){
                // 添加参会人员数据和提醒信息
                foreach($meeting_communist as &$communist){
                    //添加参会人员表特有字段
                    $result['communist_no']=$communist;
                    $result['party_no']=getCommunistInfo($communist,'party_no');
                    $result['meeting_no']=$data['meeting_no'];
                    //添加公共字段
                    $result['add_staff']=session('staff_no');
                    $result['status']=0;
                    $result['update_time']=date("Y-m-d H:i:s");
                    $result['add_time']=date("Y-m-d H:i:s");
                    $communist_add=$oa_meeting_communist->add($result);
                    if($communist_add){
                        $alert_title = "你有一条会议通知";
                        $alert_content = "你有一场".$_POST['meeting_name']."的会议预计在".$_POST['meeting_start_time']."在".$_POST['meeting_addr']."开始,请准时参加！";
                        if($status != '23'){
                            saveAlertMsg('14', $communist,$alert_url, $alert_title, $_POST['meeting_start_time'], '', '', session('communist_no'),0,$alert_content);
                        }
                    }
                }
                if($communist_add){
                    if($type == '2005' || $type == '2006' || $type == '2007'){
                        showMsg("success","操作成功！",U('oa_meeting_index',array('type'=>$type)),1);
                    }else{
                        showMsg("success","操作成功！",U('oa_meeting_index'));
                    }
                }
            } else {
                // 添加主持人提醒信息
                if(!empty($meeting_host_no)){
                    foreach($meeting_host_no as &$host){
                        $alert_title = "你有一条会议主持通知";
                        $alert_content = "你有一场".$_POST['meeting_name']."的会议预计在".$_POST['meeting_start_time']."在".$_POST['meeting_addr']."需要你主持,请提前到场！";
                        saveAlertMsg('14', $host,$alert_url, $alert_title, $_POST['meeting_start_time'], '', '', session('communist_no'),0,$alert_content);
                    }
                }
                if($type == '2005' || $type == '2006' || $type == '2007'){
                    showMsg("success","操作成功！",U('oa_meeting_index',array('type'=>$type)),1);
                }else{
                    showMsg("success","操作成功！",U('oa_meeting_index'),1);
                }
            }
        } else {
            showMsg("error","操作失败，请重试。",U('oa_meeting_index'),1);
        }
    }
    /**
     * @name:oa_meeting_list
     * @desc：会议列表主页
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-11-28
     * @version：V1.0.0
     **/
    public function oa_meeting_list(){
        $party_no_auth = session('party_no_auth');//取本级及下级组织

        $child_nos_list = explode(',', $party_no_auth);
        $party_no = I('get.party_no',$child_nos_list[0]);
        $this->assign('party_no',$party_no);
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
       

        $oa_meeting=M("oa_meeting");
        $ccp_party = M('ccp_party');
        $map['type_group'] = 'meeting_type';
        $map['type_no'] = array('in','2001,2002,2003,2004');
        $type_list=M("bd_type")->where($map)->field("type_name,type_no")->select();

        foreach ($type_list as &$value11) {
            $value11['num'] = $oa_meeting->where("meeting_type=".$value11['type_no']."")->count();
            
        }
        $this->assign('type_list',$type_list);
		$party_num = $ccp_party->limit(4)->field("party_no,party_name,party_name_short")->select();
		$party_me_num = [];
		$party_name_num = [];
		foreach ($party_num as $value) {
			$party_me_num[] = $oa_meeting->where("party_no=".$value['party_no']."")->count();
			$party_name_num[] = $value['party_name_short'];

			$party_meeting_num[2001][] = $oa_meeting->where("party_no=".$value['party_no']." and meeting_type=2001")->count();
			$party_meeting_num[2002][] = $oa_meeting->where("party_no=".$value['party_no']." and meeting_type=2002")->count();
			$party_meeting_num[2003][] = $oa_meeting->where("party_no=".$value['party_no']." and meeting_type=2003")->count();
			$party_meeting_num[2004][] = $oa_meeting->where("party_no=".$value['party_no']." and meeting_type=2004")->count();
		}
		$this->assign("party_meeting_num",$party_meeting_num);
		$this->assign('party_me_num',$party_me_num);
		$this->assign('party_name_num',$party_name_num);
		

        $this->assign('party_list',$party_list);
        $this->display("Oameeting/oa_meeting_list");
    }
    /**
     * @name:oa_meeting_list_data
     * @desc：获取会议列表数据
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-05-03
     * @updatetime:2017-05-03
     * @version：V1.0.0
     **/
    public function oa_meeting_list_data(){
        //实例化会议表
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表
        $oa_meeting_communist=M("oa_meeting_communist");
        //实例化部门表
        $ccp_party = M('ccp_party');
        //搜索条件

        $child_nos_news = session('party_no_auth');//取本级及下级组织
        $child_nos_list = explode(',', $child_nos_news);
        $party_no = I('get.party_no',$child_nos_list[0]);
        

        $party_nos = getPartyChildNos($party_no);

        $ccp_party_data=$ccp_party->where("party_no=$party_no")->find();
        if(!$ccp_party_data['party_avatar'] || !file_exists(SITE_PATH.$ccp_party_data['party_avatar'])){
            $ccp_party_data['party_avatar'] = "".__ROOT__."/statics/apps/page_index/img/pd1_tu1.png";
        }else{
            $ccp_party_data['party_avatar'] = getUploadInfo($ccp_party_data['party_avatar']);
        }

        $where_new['party_no'] = array('in',$party_nos);
        $where_new['meeting_type'] = array('in',"2001,2002,2003,2004");
        $where_new['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y' ) = DATE_FORMAT( CURDATE() , '%Y' )";
        $ccp_party_data['new_shoulded']=$oa_meeting->where($where_new)->count();//应该开的会议
        $where_new['status'] = 23;
        $ccp_party_data['new_attended']=$oa_meeting->where($where_new)->count();//实际开的会议


        $where['party_no'] = array('in',$party_nos);
        $where['meeting_type'] = array('in',"2001,2002,2003,2004");
        $where['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $ccp_party_data['shoulded']=$oa_meeting->where($where)->count();//应该开的会议
        $where['status'] = 23;
        $ccp_party_data['attended']=$oa_meeting->where($where)->count();//实际开的会议
        

        //2001支部大会 
        $zbdh_where['meeting_type'] = 2001;
        $zbdh_where['party_no'] = array('in',$party_nos);
        $zbdh_where['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $ccp_party_data['shoulded_2001']=$oa_meeting->where($zbdh_where)->count();//应该开的会议
        $zbdh_where['status'] = 23;
        $ccp_party_data['attended_2001']=$oa_meeting->where($zbdh_where)->count();//实际开的会议

        //2002支委会议 
        $zwhy_where['meeting_type'] = 2002;
        $zwhy_where['party_no'] = array('in',$party_nos);
        $zwhy_where['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $ccp_party_data['shoulded_2002']=$oa_meeting->where($zwhy_where)->count();//应该开的会议
        $zwhy_where['status'] = 23;
        $ccp_party_data['attended_2002']=$oa_meeting->where($zwhy_where)->count();//实际开的会议
        //2003党小组会
        $dxzh_where['meeting_type'] = 2003;
        $dxzh_where['party_no'] = array('in',$party_nos);
        $dxzh_where['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $ccp_party_data['shoulded_2003']=$oa_meeting->where($dxzh_where)->count();//应该开的会议
        $dxzh_where['status'] = 23;
        $ccp_party_data['attended_2003']=$oa_meeting->where($dxzh_where)->count();//实际开的会议
        //2004党课
        $dk_where['meeting_type'] = 2004;
        $dk_where['party_no'] = array('in',$party_nos);
        $dk_where['_string'] = "DATE_FORMAT(meeting_real_start_time, '%Y%m' ) = DATE_FORMAT( CURDATE() , '%Y%m' )";
        $ccp_party_data['shoulded_2004']=$oa_meeting->where($dk_where)->count();//应该开的会议
        $dk_where['status'] = 23;
        $ccp_party_data['attended_2004']=$oa_meeting->where($dk_where)->count();//实际开的会议
            
       

        $ccp_party_list['code'] = 0;
        $ccp_party_list['msg'] = "获取数据成功";
        ob_clean();$this->ajaxReturn($ccp_party_data);
    }
    /**
     * @name:oa_meeting_list_edit
     * @desc：会议增加修改方法
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_list_edit(){
        $this->display("Oameeting/oa_meeting_list_edit");
    }
	/**
     * @name:oa_meeting_info
     * @desc：会议详情
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_info(){
    	checkAuth(ACTION_NAME.session('cat'));
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表对象
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表对象
        $oa_meeting_communist=M("oa_meeting_communist");
        //实例化会议总结表
        $meeting_minutes = M('oa_meeting_minutes');
        $_att_log=M("oa_att_log");
        $meeting_no=I("get.meeting_no");
        if($meeting_no){
            $row=getMeetingInfo($meeting_no);
			$status_map['status_group'] = 'meeting_status';
			$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
			$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');	
			$row['status_name'] = "<font color='" . $status_color_arr[$row['status']] . "'>" . $status_name_arr[$row['status']] . "</font> ";//getStatusName('meeting_status',$data['status']);
            $row['meeting_start_time'] = date("Y-m-d H:i",strtotime($row['meeting_start_time']));
            $meetting_attach_list = explode('`', $row['meetting_attach']);
            $map_up['upload_id'] = array('in',$meetting_attach_list);
            $upload = M('bd_upload')->where($map_up)->select();
            foreach ($upload as &$up) {
                $up['up_lo'] = getUploadInfo($up['upload_id']);
            }
            $this->assign("upload",$upload);

            $meetting_mien_list =getUploadInfo($row['meetting_mien']);	
			if(!empty($meetting_mien_list)){
				$meetting_mien_list = explode(',',$meetting_mien_list);
			}else{
				$meetting_mien_list = '';
			}
            $this->assign("meetting_mien_list",$meetting_mien_list);		
            $meeting_map['meeting_id'] = $meeting_no;
            $meeting_map['meeting_minutes_type'] = 1;
            $data = $meeting_minutes->field('meeting_minutes_content')->where($meeting_map)->find();
            $row['meeting_minutes_content'] = $data['meeting_minutes_content'];
            //判断该会场是否有摄像头
            if($row['meeting_camera']=="no"){
                $row['meeting_camera']="该会场暂无摄像头";                
            }else{
                $is_camera='1';
            }
            $row["shoulded_info"]=$row["attended"]."/".$row["should"];
            $this->assign("is_camera",$is_camera);
            $this->assign("row",$row);
            $att_meeting_communist=array();
            $where['meeting_no']="$meeting_no";
            $meeting=$oa_meeting_communist->where($where)->select();
            $i=0;
            $party_name_arr = M('ccp_party')->getField('party_no,party_name');
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            
            foreach($meeting as &$v){
                $v['communist_name'] = $communist_name_arr[$v['communist_no']];
                $v['party_name'] =  $party_name_arr[$v['party_no']];
                $i=$i+1;
                $v["meeting_ids"]=$i;
                $meeting_comm_map['meeting_no'] = $meeting_no;
                $meeting_comm_map['communist_no'] = $v['communist_no'];
                $meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
                //判断签到时间是否符合规定
                if($meeting_precheck_time!="no"){
                    $v['status_name']="<font color=red>已签到</font>";
                    $att_arr=$_att_log->where("att_id='$meeting_precheck_time'")->find();
                    if($att_arr["att_manner"]=="12"){
                        $att_address = M('oa_att_log')->where('att_id = '.$meeting_precheck_time.'')->getField('att_address');
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($att_address);
                    } if($att_arr["att_manner"]=="13"){
                        $att_address = M('oa_att_log')->where('att_id = '.$meeting_precheck_time.'')->getField('att_address');
                        $v["ckeck_type_name"]="手机补签";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($att_address);
                    } else{
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $mach=getMachineInfo($row["machine_no"]);
                        $v["check_addr"]=$mach["machine_addr"];
                    }
                }else{
                    $v['status_name']="未签到";
                }
            }
            $this->assign("meeting_result",$meeting);
			
			
			
			if(getPartyData('is_admin',session('staff_no') || $row['add_staff'] == session('staff_no'))){  // 是否为 添加人 和超级管理员
				$start = "<div class='status_btn layui-btn layui-btn-primary' style='line-height: 38px;' onclick='status_start(&#34;$meeting_no&#34;,&#34;$row[meeting_type]&#34;,&#34;$row[party_no]&#34)' >开始</div>";
				$end = "<div class='status_btn layui-btn layui-btn-primary' style='line-height: 38px;' onclick='status_end(&#34;$meeting_no&#34;,&#34;$row[meeting_type]&#34;,&#34;$row[party_no]&#34)' >结束</div>";
			}
			switch($row['status']){
				case '11':
					$operate="<div class='edit_btn layui-btn layui-btn-primary' style='line-height: 38px;' onclick='edit(".$row['meeting_no'].",".$row['meeting_type'].")' >编辑</div>&nbsp;".$start."&nbsp;";
					break;
				case '21':				
					$operate=$end."&nbsp;";
					break;
				case '23':
					$operate="<div class='record_btn layui-btn layui-btn-primary' style='line-height:38px;' onclick='record(".$row['meeting_no'].")' >会议总结</div>&nbsp;";
					break;
			}
			$this->assign('operate',$operate);
        }else{
            showMsg("success","执行失败，请联系管理员。",U('oa_meeting_index'));
        }
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		
		
		$record = M('oa_meeting_minutes')->where("meeting_minutes_type = 1 and meeting_id = $meeting_no")->find();
		
		
		$meeting_minutes_thumb_list = explode('`', $record['meeting_minutes_thumb']);
		$map_record['upload_id'] = array('in',$meeting_minutes_thumb_list);
		
		$upload_record = M('bd_upload')->where($map_record)->select();
		foreach ($upload_record as &$upl) {
			$upl['up_lo'] = getUploadInfo($upl['upload_id']);
		}
		$this->assign("upload_record",$upload_record);
		
		if(!empty($record)){
			$record['meetting_mien'] = $row['meetting_mien'];
			$record['meetting_attach'] = $row['meetting_attach'];			
			$record['meeting_video'] = $row['meeting_video'];
		}else{
			$record['meetting_mien'] = '';
			$record['meetting_attach'] = '';
			$record['meeting_video'] = '';
			$record['type'] = $type;
			$record['party_no'] = $party_no;
			$record['meeting_id'] = $meeting_no;
		}
		$this->assign('record',$record);

        $this->display("oa_meeting_info");
    }
    function oa_meeting_url_new(){
    	$meeting_type = I('get.meeting_type');
    	if($meeting_type){
			showMsg("success","操作成功",U('oa_meeting_index',array('meeting_type'=>$meeting_type)),1);
    	}
    }
	/**
     * @name:oa_meeting_info_communist
     * @desc：会议详情人员
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_info_communist(){
		$meeting_no = I('get.meeting_no');	
		$communist_no = I('get.communist_no');
		$data['att_no'] = $communist_no;
		$data['att_date'] = date('Y-m-d');
		$data['att_time'] = date('H:i:s');
		$data['check_time'] = date('Y-m-d H:i:s');
		$data['att_manner'] = 13;
		$data['add_staff'] = session('staff_no');
		$data['status'] = 1;
		$data['update_time'] = date('Y-m-d H:i:s');
		$data['add_time'] = date('Y-m-d H:i:s');		
		M('oa_att_log')->add($data);
		$where['meeting_no'] = $meeting_no;
		$meeting=M("oa_meeting_communist")->where($where)->select();
		
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');	
		$content = "";
		foreach($meeting as &$v){
			$v['communist_name'] = $communist_name_arr[$v['communist_no']];
			$meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
			//判断签到时间是否符合规定
			if($meeting_precheck_time == "no" ){
				$content .= "<li class='tip-right' communist_no=".$v['communist_no']." style='margin-right: 0.4rem;height: 0.4rem;line-height: 0.4rem;'>
					<img style='width: 0.19rem;height: 0.19rem;' class='mr-17em-cur ' src='/statics/apps/page_index/img/xq_false.png' alt=''>
					{$v['communist_name']}
				</li>";								
			}else{
				$content .= "<li style='margin-right: 0.4rem;height: 0.4rem;line-height: 0.4rem;'>
					<img style='width: 0.19rem;height: 0.19rem;' class='mr-17em-cur ' src='/statics/apps/page_index/img/xq_true.png' alt=''>
					{$v['communist_name']}
				</li>";
			}
		}
		$data['content'] = $content;
		$row=getMeetingInfo($meeting_no);
		$data['shoulded_info'] = $row["attended"]."/".$row["should"];
        ob_clean();$this->ajaxReturn($data);
	}
	/**
     * @name:oa_meeting_info_record
     * @desc：会议详情
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_info_record(){
        $oa_meeting_communist=M("oa_meeting_communist");
        //实例化会议总结表
        $meeting_minutes = M('oa_meeting_minutes');
        $_att_log=M("oa_att_log");
        $meeting_no=I("get.meeting_no");
        $type=I("get.type");
        $party_no=I("get.party_no");		
        if($meeting_no){
            $row=getMeetingInfo($meeting_no);
            
			$status_map['status_group'] = 'meeting_status';
			$status_name_arr = M('bd_status')->where($status_map)->getField('status_no,status_name');
			$status_color_arr = M('bd_status')->where($status_map)->getField('status_no,status_color');	
			$row['status_name'] = "<font color='" . $status_color_arr[$row['status']] . "'>" . $status_name_arr[$row['status']] . "</font> ";//getStatusName('meeting_status',$data['status']);
			
			
			$row['meeting_start_time'] = date("Y-m-d H:i",strtotime($row['meeting_start_time']));
            $meetting_attach_list = explode('`', $row['meetting_attach']);
            $map_up['upload_id'] = array('in',$meetting_attach_list);

            $upload = M('bd_upload')->where($map_up)->select();
            foreach ($upload as &$up) {
                $up['up_lo'] = getUploadInfo($up['upload_id']);
            }
            $this->assign("upload",$upload);

            $meetting_mien_list =getUploadInfo($row['meetting_mien']);
            $meetting_mien_list = explode(',',$meetting_mien_list);
            $this->assign("meetting_mien_list",$meetting_mien_list);

            $meeting_map['meeting_id'] = $meeting_no;
            $meeting_map['meeting_minutes_type'] = 1;
            $data = $meeting_minutes->field('meeting_minutes_content')->where($meeting_map)->find();
            $row['meeting_minutes_content'] = $data['meeting_minutes_content'];
            
            //判断该会场是否有摄像头
            if($row['meeting_camera']=="no"){
                $row['meeting_camera']="该会场暂无摄像头";                
            }else{
                $is_camera='1';
            }
            $row["shoulded_info"]=$row["attended"]."/".$row["should"];
            $this->assign("is_camera",$is_camera);
            $this->assign("row",$row);
            $att_meeting_communist=array();
            $where['meeting_no']="$meeting_no";
            $meeting=$oa_meeting_communist->where($where)->select();
            $i=0;
            $party_name_arr = M('ccp_party')->getField('party_no,party_name');
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            
            foreach($meeting as &$v){
                $v['communist_name'] = $communist_name_arr[$v['communist_no']];
                $v['party_name'] =  $party_name_arr[$v['party_no']];
                $i=$i+1;
                $v["meeting_ids"]=$i;
                $meeting_comm_map['meeting_no'] = $meeting_no;
                $meeting_comm_map['communist_no'] = $v['communist_no'];
                $meeting_precheck_time=getIsChecked($meeting_no,$v['communist_no']);
                //判断签到时间是否符合规定
                if($meeting_precheck_time!="no"){
                    $v['status_name']="<font color=red>已签到</font>";
                    $att_arr=$_att_log->where("att_id='$meeting_precheck_time'")->find();
                    if($att_arr["att_manner"]=="12"){
                        $att_address = M('oa_att_log')->where('att_id = '.$meeting_precheck_time.'')->getField('att_address');
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($att_address);
                    } if($att_arr["att_manner"]=="13"){
                        $att_address = M('oa_att_log')->where('att_id = '.$meeting_precheck_time.'')->getField('att_address');
                        $v["ckeck_type_name"]="手机补签";
                        $v['check_time']=$att_arr['check_time'];
                        $v["check_addr"]=coordinateToAddr($att_address);
                    } else{
                        $v["ckeck_type_name"]="手机签到";
                        $v['check_time']=$att_arr['check_time'];
                        $mach=getMachineInfo($row["machine_no"]);
                        $v["check_addr"]=$mach["machine_addr"];
                    }
                }else{
                    $v['status_name']="未签到";
                }
            }
            $this->assign("meeting_result",$meeting);
        }else{
            showMsg("success","执行失败，请联系管理员。",U('oa_meeting_index'));
        }
        $is_integral = getConfig('is_integral');
        $this->assign('is_integral',$is_integral);
		
		$record = M('oa_meeting_minutes')->where("meeting_minutes_type = 1 and meeting_id = $meeting_no")->find();
		if(!empty($record)){
			$record['meetting_mien'] = $row['meetting_mien'];
			$record['meetting_attach'] = $row['meetting_attach'];			
			$record['meeting_video'] = $row['meeting_video'];
		}else{
			$record['meetting_mien'] = '';
			$record['meetting_attach'] = '';
			$record['meeting_video'] = '';
			$record['type'] = $type;
			$record['party_no'] = $party_no;
			$record['meeting_id'] = $meeting_no;
		}
		$this->assign('record',$record);		
        $this->display("oa_meeting_info_record");
    }
	/**
	 * @name  oa_meeting_record_do_save()
	 * @desc  会议总结保存
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_record_do_save(){
		$post = $_POST;		
		$minutes['update_time'] = date('Y-m-d H:i:s');
		if(!empty($post['meeting_minutes_id'])){
			$minutes['meeting_id'] = $post['meeting_no'];
            $meeting_map['meeting_no'] = $post['meeting_no'];			
			$minutes['meeting_id'] = $post['meeting_no'];
            $meeting_map['meeting_no'] = $post['meeting_no'];			
			$minutes['meeting_minutes_content'] = $post['meeting_minutes_content'];
			$minutes['meeting_minutes_thumb'] = $post['meeting_minutes_thumb'];
			$post['meeting_minutes_title'] = M('oa_meeting')->where($meeting_map)->getField('meeting_name');
            $minutes_map['meeting_minutes_id'] = $post['meeting_minutes_id'];
			$meeting_minutes = M('oa_meeting_minutes')->where($minutes_map)->save($minutes);
		}else{
			$minutes['add_staff'] = session('staff_no');
			$minutes['add_time'] = date('Y-m-d H:i:s');
			$minutes['meeting_minutes_type'] = '1';
			$minutes['meeting_id'] = $post['meeting_no'];			
			$minutes['meeting_minutes_content'] = $post['meeting_minutes_content'];
			$minutes['meeting_minutes_thumb'] = $post['meeting_minutes_thumb'];			
            $meeting_map['meeting_no'] = $post['meeting_no'];
			$minutes['meeting_minutes_title'] = M('oa_meeting')->where($meeting_map)->getField('meeting_name');
			$meeting_minutes = M('oa_meeting_minutes')->add($minutes);			
		}
		if(!empty($meeting_minutes)){
			$meeting['meeting_video'] = $post['meeting_video'];
			$meeting['meetting_attach'] = $post['meetting_attach'];
			$meeting['meetting_mien'] = $post['meetting_mien'];
			$meeting_where['meeting_no'] = $post['meeting_no'];
			M('oa_meeting')->where($meeting_where)->save($meeting);
			showMsg("success","操作成功",U('oa_meeting_index',array('type'=>$post['type'],$party_no=>$post['party_no'])),1);
		}else{
			showMsg("success","执行失败，请联系管理员。");
		}
	}
    /**
     * @name:oa_meeting_do_del
     * @desc：会议删除方法
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-11
     * @updatetime:2017-04-11
     * @version：V1.0.0
     **/
    public function oa_meeting_do_del(){
        checkAuth(ACTION_NAME.session('cat'));
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        //实例化考勤机表对象
        $_att_machine=M("oa_att_machine");
        //实例化参会人员表对象
        $oa_meeting_communist=M("oa_meeting_communist");
        $meeting_no=I("get.meeting_no");
        $party_no = $_GET['party_no'];
        $type = $_GET['type'];
        if($meeting_no){
            $meeting_map['meeting_no'] = $meeting_no;
            $meeting_del=$oa_meeting->where($meeting_map)->delete();
            $meeting_communist_del=$oa_meeting_communist->where($meeting_map)->delete();
            M('oa_meeting_minutes')->where("meeting_minutes_type = 1 and meeting_id = $meeting_no")->delete();
        }
        if($meeting_del){
            showMsg("success","操作成功！",U('Oameeting/oa_meeting_index',array('party_no'=>$party_no,'type'=>$type)));
        }
        else{
            showMsg("success","执行失败，请联系管理员。");
        }
    
    }
    /**
     * @name:oa_meeting_do_start
     * @desc:会议开始
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-04-22
     * @updatetime:2017-04-22
     * @version：V1.0.0
     **/
    public function oa_meeting_do_start(){
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        $meeting_no=I("get.meeting_no");
        $data=array(
            "status"=>"21",
            "meeting_real_start_time"=>date("Y-m-d H:i:s"),
            "update_time"=>date("Y-m-d H:i:s")
        );
        $party_no = $_GET['party_no'];
        $type = $_GET['type'];
        $info_type = $_GET['info_type'];
        $where=array("meeting_no"=>$meeting_no);
        $meeting_update=$oa_meeting->where($where)->save($data);
        if($info_type==1){
            showMsg("success","操作成功",U('oa_meeting_info',array('meeting_no'=>$meeting_no,'party_no'=>$party_no,'type'=>$type)));

        }else{
        	if($meeting_update){
	            showMsg("success","操作成功",U('oa_meeting_index',array('party_no'=>$party_no,'type'=>$type)));
	        }
	        else{
	            showMsg("success","执行失败，请联系管理员。");
	        }
        }
        
    }
    /**
     * @name:oa_meeting_do_end
     * @desc:会议结束
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-10-21
     * @updatetime:2017-10-21
     * @version：V1.0.0
     **/
    public function oa_meeting_do_end(){
        //实例化会议表对象
        $oa_meeting=M("oa_meeting");
        $oa_meeting_communist=M("oa_meeting_communist");
        $meeting_no=I("get.meeting_no");
        $data=array(
            "status"=>"23",
            "meeting_real_end_time"=>date("Y-m-d H:i:s"),
            "update_time"=>date("Y-m-d H:i:s")
        );
        $party_no = $_GET['party_no'];
        $type = $_GET['type'];
        $info_type = $_GET['info_type'];
        $where=array("meeting_no"=>$meeting_no);
        $meeting_update=$oa_meeting->where($where)->save($data);
        $meeting_info = $oa_meeting->where($where)->field('meeting_integral,party_no')->find();
        $party_map['party_no'] = $meeting_info['party_no'];
        $party_integral = M('ccp_party')->where($party_map)->getField('party_integral');
        $meeting_comm_integral = getConfig('integral_meeting_communist');
        $party_integral = M('ccp_party')->where($party_map)->getField('party_integral');
        if(empty($meeting_info['meeting_integral'])){
            $meeting_info['meeting_integral'] = '0';
        }
        if($info_type==1){
            showMsg("success","操作成功",U('oa_meeting_info',array('meeting_no'=>$meeting_no,'type'=>$type)));
        }else{
        	if($meeting_update){
	            updateIntegral(2,7,$meeting_info['party_no'],$party_integral,$meeting_info['meeting_integral'],'组织会议'); // 给党组织加积分
	            $meeting_comm_list=$oa_meeting_communist->where($where)->field('communist_no')->select();
	            $communist_integral_arr = M('ccp_communist')->getField('communist_no,communist_integral');
	            foreach($meeting_comm_list as $val){
	                $meeting_comm_map['meeting_no'] = $meeting_no;
	                $meeting_comm_map['communist_no'] = $val['communist_no'];
	                $is_check=getIsChecked($meeting_no,$val['communist_no']);
	                if($is_check != 'no'){
	                    updateIntegral(1,7,$val['communist_no'],$communist_integral_arr[$val['communist_no']],$meeting_comm_integral,'参加会议'); // 给签到党员加积分
	                }
	            }
	            showMsg("success","操作成功",U('oa_meeting_index',array('party_no'=>$party_no,'type'=>$type)));
	        }
	        else{
	            showMsg("success","执行失败，请联系管理员。");
	        }
        }
        
    }
   /**
     * @name:oa_check_info_show
     * @desc:会议签到内容展示
     * @param：
     * @return：
     * @author：王宗彬
     * @addtime:2017-05-18
     * @updatetime:2017-04-23
     * @version：V1.0.0
     **/
    public function oa_check_info_show(){
        $communist_no=I("get.communist_no");
        $meeting_no=I("get.meeting_no");
        $communist_list=getMeetingCommunistInfo($communist_no,$meeting_no);
        if(empty($communist_list["study_content"])){
            $communist_list["study_content"]="暂无笔记";
        }
        if(empty($communist_list["meeting_upload_no"])){
            $communist_list["upload_img"]="暂无附件";
        }else{
            $communist_list["meeting_upload"]=getUploadInfo($communist_list["meeting_upload_no"]);
            $arr=explode(",", $communist_list["meeting_upload"]);
            foreach($arr as $k=>$v){
                if($k=="0"){
                    $communist_list["upload_img"]="";
                }
                $communist_list["upload_img"].="<img src=".$v." width='400' /><br/>";
            }
        }
        $this->assign("communist_list",$communist_list);
        $this->display("oa_check_info_show");
    }
    
    /***************************会议记录首页********************************/
	/**
	 * @name  oa_meeting_minutes_index()
	 * @desc  会议记录首页
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_minutes_index(){
		
		checkAuth(ACTION_NAME);//判断越权
    	$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
    	$this->assign('party_list',$party_list);
    	$party_no = I('get.party_no');
        if(empty($party_no) && !empty($party_list)){
            $party_no = $party_list[0]['party_no'];
        }
    	$this->assign('party_no',$party_no);
		$this->display("Oameeting/oa_meeting_minutes_index");
	}
	/**
	 * @name  oa_meeting_minutes_index_data()
	 * @desc  会议记录首页数据
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_minutes_index_data(){
        $pagesize = I('get.limit');
        $page = (I('get.page')-1)*$pagesize;
		$party_no = I('get.party_no');
		$meeting_minutes_title = I('get.meeting_minutes_title');
		$add_staff = I('get.add_staff');
		$data = getMeetingminutesList($party_no,'1', $meeting_minutes_title, $add_staff,$page,$pagesize);
		
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		//$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
		foreach ($data['data'] as &$list){
			$meeting_minutes_id = $list['meeting_minutes_id'];
			//$list['meeting_minutes_id'] = "<a class='fcolor-22' href='" . U('oa_meeting_minutes_info', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'>".$meeting_minutes_id."</a>";
			//$list['meeting_minutes_title'] = "<a class='fcolor-22' href='" . U('oa_meeting_minutes_info', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'>".$list['meeting_minutes_title']."</a>";
			$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
			$list['party_no'] = $party_name_arr[$list['party_no']];
			$list['add_staff'] = $staff_name_arr[$list['add_staff']];
            $list['add_time'] = getFormatDate($list['add_time'],'Y-m-d');
			//$list['operate'] .= "<a class='btn blue btn-xs btn-outline' href='" . U('oa_meeting_minutes_edit', array('meeting_minutes_id' => $meeting_minutes_id)) . "' target='_self'><i class='fa fa-edit'></i>编辑</a><a class='btn red btn-xs btn-outline' href='" . U('oa_meeting_minutes_do_del', array('meeting_minutes_id' => $meeting_minutes_id)) . "' $confirm><i class='fa fa-trash-o'></i>删除</a>";
		}
		ob_clean();$this->ajaxReturn($data);
	}
	/**
	 * @name  oa_meeting_minutes_info()
	 * @desc  会议记录详情
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_minutes_info(){
		checkAuth(ACTION_NAME);//判断越权
		if($_GET['meeting_minutes_id']){
			$meeting_minutes_id = $_GET['meeting_minutes_id'];
            $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
			$data = M('oa_meeting_minutes')->where($minutes_map)->find();
			$data['meeting_minutes_content'] = removeHtml($data['meeting_minutes_content']);
			$data['party_no'] = getPartyInfo($data['party_no']);
            $data['add_staff'] = getStaffInfo($data['add_staff']);
		}
		$this->assign('data',$data);
		$this->display("Oameeting/oa_meeting_minutes_info");
	}
	/**
	 * @name  oa_meeting_minutes_edit()
	 * @desc  会议记录添加/编辑
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_minutes_edit(){
		checkAuth(ACTION_NAME);//判断越权
		$party_no = I('get.party_no');
        $oa_meeting=M('oa_meeting');
		if($_GET['meeting_minutes_id']){ 
            $minutes_map['meeting_minutes_id'] = $_GET['meeting_minutes_id'];
			$data = M('oa_meeting_minutes')->where($minutes_map)->find();
            $this->assign('meeting_no',$data['meeting_id']);
			$this->assign('data',$data);
		}else{
            $data['staff_no'] =  session('staff_no');
            $this->assign('data',$data);
        }
		if(empty($party_no)){
			$party_no = $data['party_no'];
		}
        if(!empty($party_no)){
            $meeting_map['party_no'] = array('in',$party_no);
        }else{
           $party_no_auth = session('party_no_auth');
            $meeting_map['party_no'] = array('in',$party_no_auth); 
        }
        $meeting_map['status'] = 23;
        $meeting_list=$oa_meeting->where($meeting_map)->field('meeting_no,meeting_name')->order('add_time desc')->select();
        $this->assign('meeting_list',$meeting_list);
		$this->assign('party_no',$party_no);
		$this->display("Oameeting/oa_meeting_minutes_edit");
	}
	/**
	 * @name  oa_meeting_minutes_do_save()
	 * @desc  会议记录保存
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.11.30
	 */
	public function oa_meeting_minutes_do_save(){
		$post = $_POST;
		$oa_meeting_minutes = M('oa_meeting_minutes');
        if(empty($post['meeting_no'])){
            showMsg("error","请选择会议");
        } else if(empty($post['meeting_minutes_content'])){
            showMsg("error","请填写会议总结");
        }
		if(!empty($post['meeting_minutes_id'])){
			$meeting_minutes_id = $post['meeting_minutes_id'];
			$post['update_time'] = date('Y-m-d H:i:s');
			$post['meeting_id'] = $post['meeting_no'];
            $meeting_map['meeting_no'] = $post['meeting_no'];
			$post['meeting_minutes_title'] = M('oa_meeting')->where($meeting_map)->getField('meeting_name');
            $minutes_map['meeting_minutes_id'] = $meeting_minutes_id;
			$meeting_minutes = $oa_meeting_minutes->where($minutes_map)->save($post);
		}else{
			$post['party_no'] = $_POST['party_no'];
			$post['add_staff'] = session('staff_no');
			$post['add_time'] = date('Y-m-d H:i:s');
			$post['update_time'] = date('Y-m-d H:i:s');
			$post['meeting_minutes_type'] = '1';
			$post['meeting_id'] = $post['meeting_no'];
            $meeting_map['meeting_no'] = $post['meeting_no'];
			$post['meeting_minutes_title'] = M('oa_meeting')->where($meeting_map)->getField('meeting_name');
			$meeting_minutes = $oa_meeting_minutes->add($post);
		}
		if(!empty($meeting_minutes)){
			showMsg("success","操作成功",U('oa_meeting_minutes_index'),1);
		}else{
			showMsg("success","执行失败，请联系管理员。",U('oa_meeting_minutes_index'),1);
		}
	}
	/**
	 * @name  oa_meeting_minutes_do_del()
	 * @desc  会议记录删除
	 * @param
	 * @return
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @addtime   2017.12.1
	 */
	public function oa_meeting_minutes_do_del(){
		checkAuth(ACTION_NAME);
        $minutes_map['meeting_minutes_id'] = $_GET['meeting_minutes_id'];
		$res = M('oa_meeting_minutes')->where($minutes_map)->delete();
		if($res){
			showMsg("success","操作成功",U('oa_meeting_minutes_index',array('party_no'=>$post['party_nos'])));
		}else{
			showMsg("success","执行失败");
		}
		
	}
}
