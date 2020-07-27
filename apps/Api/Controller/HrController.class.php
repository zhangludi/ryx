<?php
/**
 * 党务平台
 * User: liubingtao
 * Date: 2018/1/27
 * Time: 下午2:30
 */

namespace Api\Controller;


use Api\Validate\CommunistNoValidate;
use Api\Validate\NumberValidate;
use Api\Validate\RequireValidate;
use Ccp\Model\CcpCommunistChangeModel;
use Ccp\Model\CcpIntegralLogModel;

class HrController extends Api
{
    /**
     *  get_hr_party_list
     * @desc 获取关键字部门列表
     * @param string party_name 支部名称
     * @param int party_no 上级编号
     * @param int type 支部类型
     * @user liubingtao
     * @date 2018/1/30
     * @version 1.0.0
     */
    public function get_hr_party_list()
    {
        $party_name = I("post.party_name");
        $party_no = I("post.party_no");
        $party_pno = I("post.party_pno");
        $is_all = I("post.is_all");
        $type = I("post.type");
        $db_party = M('ccp_party');
        $where = 'status=1';
        if (!empty($party_name)) {
            $where .= " and party_name like '%$party_name%'";
        }
        if (!empty($party_no)) {
            $party_no_list = getPartyChildNos($party_no);
            $where .= " and find_in_set('party_no','$party_no_list')";
        }
        if($is_all == 1){
            if (!empty($party_pno) || $party_pno == '0') {
                $where .= " and party_pno = '$party_pno'";
            }
        }
        if (!empty($type)) {
            $where .= " and party_level_code='$type'";
        }
        $party_data = $db_party->where($where)->field('party_name,party_no,party_pno,gc_lng,gc_lat,party_avatar')->select();
        foreach ($party_data as &$data) {
        	$data['party_avatar'] = getUploadInfo($data['party_avatar']);
        }
        if ($party_data) {
            $this->send('获取支部列表成功', $party_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_party_info
     * @desc 支部详情
     * @param int party_no 支部编号
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_party_info()
    {
        (new NumberValidate(['party_no']))->goCheck();
        $party_no = I("post.party_no");
        $db_integral = new CcpIntegralLogModel();
        $party_info = M('ccp_party')->where("party_no=$party_no")->field('party_no,party_name,memo,party_propagate')->find();
        if ($party_info) {
            $child_nos = getPartyChildNos($party_no, 'arr',2);//取下级组织
            $party_info['party_num'] = count($child_nos);//党组织数量
            
            if(!empty($party_info['party_propagate'])){
                $upload_id = str_replace ( "`", ",", $party_info['party_propagate'] );
                $no_arr = strToArr($upload_id);
                $i = 0;
                foreach ($no_arr as &$arr) {
                    $party_info['thumb'][$i++] = getUploadInfo($arr);
                }
            } else {
                $party_info['thumb'] = null;
            }
            $party_info['communist_num'] = getCommunistCount($child_nos, null, COMMUNIST_STATUS_OFFICIAL);
            $integral = $db_integral->getIntegralInfo('1', $party_no);
            $party_info['integral_total'] = $integral['integral_total'];
            $where['party_no'] = array('in', $child_nos);
            $where['status']=2;
            $party_info['dues_amount'] = M("ccp_dues")->where($where)->sum('dues_amount');//党费
            $db_meeting = M('oa_meeting');
            $meeting_map['status'] = 23;
            $meeting_map['party_no'] = $party_no;
            $meeting_num = $db_meeting->where($meeting_map)->count();//会议数量
            if(empty($party_info['communist_num'])){
                $party_info['communist_num'] = 0;
            }
            if(empty($party_info['integral_total'])){
                $party_info['integral_total'] = 0;
            }
            if(empty($party_info['dues_amount'])){
                $party_info['dues_amount'] = 0;
            }
            if(empty($meeting_num)){
                $meeting_num = 0;
            }
            $party_info['meeting_num'] = $meeting_num;
            $meeting_list = getMeetingList($party_no);
            $party_info['meeting_list'] = $meeting_list;
            $this->send('获取成功', $party_info, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_hr_party_dynamic
     * @desc 支部动态
     * @param int party_no 支部编号
     * @user liubingtao
     * @date 2018/1/31 11，13
     * @version 1.0.0
     */
    public function get_hr_party_dynamic()
    {
        (new NumberValidate(['party_no']))->goCheck();
        $party_no = I("post.party_no");
       	$article_cat = "11,13";
        $where['article_cat'] = array('in',$article_cat);
       	$where['party_no'] = array("eq",$party_no);
        $article_list = M('cms_article')->where($where)->order('add_time desc')->select();
        $arr_list = array();
        foreach ($article_list as $k=>&$list){
        	$arr_list[$k]['article_thumb'] = getUploadInfo($list['article_thumb']);
        	$arr_list[$k]['add_time'] = getFormatDate($list['add_time'], 'Y-m-d');
        	$arr_list[$k]['article_id'] = $list['article_id'];
        	$arr_list[$k]['article_content'] = $list['article_content'];
        	$arr_list[$k]['article_description'] = $list['article_description'];
        	$arr_list[$k]['article_cat'] = $list['article_cat'];
        	$arr_list[$k]['article_title'] = "【".getArticleCatInfo($list['article_cat'])."】".$list['article_title'];
        }
        if ($arr_list) {
        	$this->send('获取成功', $arr_list, 1);
        } else {
            $this->send();
        }
    }
    /**
     *  get_communist_log_type
     * @desc 获取生活类型
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_communist_log_type()
    {
     	(new CommunistNoValidate())->goCheck();
     	$communist_log = M("ccp_communist_log");
    	$communist_no = I("post.communist_no");
    	$type_list = getBdTypeList('communist_log_type', '10,11,12');
    	$where['_string'] = "FIND_IN_SET('$communist_no',communist_no)";
    	foreach ($type_list as &$list){
    		if($list['type_no']){
    			$where['log_type'] = array('eq',$list['type_no']);
    		}else{
    			$where['log_type'] = array('in','10,11,12');
    		}
    		$list['count'] = $communist_log->where($where)->count();
    	}
    	
    	if(!empty($type_list)){
    		$this->send('获取成功', $type_list, 1);
    	} else {
    		$this->send();
    	}
    }
    
    
    
    /**
     *  get_hr_communist_info
     * @desc 获取党员信息
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_info()    {
        (new CommunistNoValidate())->goCheck();

        $ccp_communist_bio = M("ccp_communist_bio");
        $log_type = 	I('post.log_type');
        $communist_no = I("post.communist_no");

        $communist_info = getCommunistInfo($communist_no,
            'communist_no,communist_name,party_no,post_no,communist_avatar,communist_sex,
                            communist_birthday,communist_school,communist_idnumber,communist_email,communist_ccp_date,communist_paddress,communist_address,communist_specialty,communist_mobile,communist_tel,status');
        if ($communist_info) {
            if(empty($communist_info['communist_avatar']) || !file_exists(SITE_PATH.$communist_info['communist_avatar'])){
                $communist_info['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
            $communist_info['party_name'] = getPartyInfo($communist_info['party_no']);
            $communist_info['post_name'] = getPartydutyInfo($communist_info['post_no']);
            //积分
            $integral = getCommunistRank($communist_no);
            $communist_info['communist_sex'] = $communist_info['communist_sex'] == '1' ? '男' : '女';
            if (!empty($communist_info['communist_nation'])) {
                $communist_info['communist_nation'] = M('bd_nation')->where("nation_id=" . $communist_info['communist_nation'])->getField('nation_name');
            }
            $communist_info['bio_num'] = $ccp_communist_bio->where("communist_no = '$communist_no'")->count();//状态
            $communist_info['integral'] = $integral['integral'];
            $communist_info['ranking'] = $integral['ranking'];
            $communist_info['party_ranking'] = $integral['party_ranking'];
            $dev_log_list = getCommunistLogList($communist_no, '10');//党员发展历程
            foreach ($dev_log_list as &$dev_log) {
                $dev_log['add_time'] = getFormatDate($dev_log['add_time'],'Y-m-d');
                $t1 = mb_strpos($dev_log['log_content'],'[')+1;
                $t2 = mb_strpos($dev_log['log_content'],']');
                $dev_log['log_title'] = mb_substr($dev_log['log_content'],$t1,$t2-$t1);
            }

            if(!empty($log_type)){
            	$communist_log_list = getCommunistLogList($communist_no,$log_type);//党员历程
            }else{
            	$log_type = COMMUNIST_STATUS_COURSE;
            	$communist_log_list = getCommunistLogList($communist_no,$log_type);//党员历程
            }
            
            $communist_info['dev_log_list'] = $dev_log_list;
            $communist_info['log_list'] = $communist_log_list;
            //2018-02-09 增加
            $communist_info['status'] = getStatusInfo('communist_status',$communist_info['status']);
            $communist_info['time'] = $dev_log_list['0']['add_time'];
            $this->send('获取成功', $communist_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_list
     * @desc 获取党员列表
     * @param int party_no 支部编号
     * @param int status 状态
     * @param int post_no 岗位编号
     * @param string communist_name 党员姓名
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_list(){
        (new NumberValidate(['party_no', 'page', 'pagesize', 'status']))->goCheck();

        $party_no = I("post.party_no");
        $post_no = I("post.post_no");
        $communist_type = I("post.communist_type");
        $communist_name = I("post.communist_name");
        $pagesize = I("post.pagesize");
        $page = (I("post.page") - 1) * $pagesize;
		$status = I('post.status');
        switch ($status) {
            case '1':
                $status = COMMUNIST_STATUS_OFFICIAL;
                $source = '';
                break;
            case '2':
                $status = COMMUNIST_STATUS_DEVELOP;
                $source = '3';
                break;
        }
        switch($communist_type){
            case '1':
                $uu['_string'] = "`username` != ''";
                if(!empty($status)){
                    $status_arr=strToArr($status);
                    $uu['status']=array('in',$status_arr);
                }
                $communist_list['data'] = M("ccp_communist")->where($uu)->select();
                break;
            default:
                 $communist_list = getCommunistList($party_no, 'arr', '1', $post_no, $status, $communist_name,'','','','','','','', $source, $page, $pagesize);
                break;
        }
        if ($communist_list['data']) {
            $arr = array();
            foreach ($communist_list['data'] as $list) {
                $item['communist_no'] = $list['communist_no'];
                $item['communist_name'] = $list['communist_name'];
                $item['communist_sex'] = $list['communist_sex'];
                $item['party_no'] = $list['party_no'];
                $item['post_no'] = $list['post_no'];
                $item['communist_avatar'] = $list['communist_avatar'];
                if(empty($item['communist_avatar']) || !file_exists(SITE_PATH.$item['communist_avatar'])){
                    $item['communist_avatar'] = "/statics/public/images/default_photo.jpg";
                }
                $item['communist_mobile'] = $list['communist_mobile'];
                $item['communist_diploma'] = $list['communist_diploma'];
                $item['communist_qq'] = $list['communist_qq'];
                $item['communist_applytime'] = $list['communist_applytime'];
                $item['communist_birthday'] = getCommunistAge($list['communist_birthday']);
                //2018-02-09 增加
                $status = getStatusInfo('communist_status',$list['status'],'status_value,status_color');
                $status_name = array_keys($status);
                $status_name = $status_name['0'];
                $item['status_name'] = $status_name;
                $item['status_color'] = $status[$status_name];

                $arr[] = $item;
            }
            $this->send('获取成功', $arr, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_flow_list
     * @desc 获取流动党员列表
     * @param int party_no 支部编号
     * @param int page
     * @param int pagesize
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_flow_list()    {

        (new NumberValidate(['party_no', 'page', 'pagesize']))->goCheck();

        $party_no = I("post.party_no");
        $pagesize = I("post.pagesize");
        $page = (I("post.page") - 1) * $pagesize;
        $db_change = M('ccp_communist_change');
        $party_pno = getPartyChildNos($party_no);
        $change_list = $db_change->where("change_type in(3,4) and find_in_set(old_party,'$party_pno')")->field('communist_no')->limit($page, $pagesize)->group('communist_no')->order('add_time desc')->select();
		if ($change_list) {
            foreach ($change_list as &$list) {
				$list['add_time'] = $db_change->where("communist_no = $list[communist_no]")->order('add_time desc')->getField('add_time');
                $list['communist_name'] = getCommunistInfo($list['communist_no']);
                $list['communist_avatar'] = getCommunistInfo($list['communist_no'],'communist_avatar');
                if(empty($list['communist_avatar']) || !file_exists(SITE_PATH.$list['communist_avatar'])){
                    $list['communist_avatar'] = "/statics/public/images/default_photo.jpg";
                }
				$list['add_time'] = getFormatDate($list['add_time'],'Y年m月d日');
                $list['new_party'] = getPartyInfo(getCommunistInfo($list['communist_no'],'party_no'),'party_name');
            }
            $this->send('获取成功', $change_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_flow_info
     * @desc 获取流动党员详情
     * @param int change_id变动ID
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_flow_info(){	
		(new CommunistNoValidate())->goCheck();
        $db_change = M('ccp_communist_change');
        $communist_no = I("post.communist_no");
        $change_info = $db_change->where("communist_no=$communist_no")->field('communist_no,old_party,new_party,change_audit_status,add_time')->order('add_time desc')->find();
        if ($change_info) {
            $change_info['communist_name'] = getCommunistInfo($communist_no);
            $change_info['communist_avatar'] = getCommunistInfo($communist_no,'communist_avatar');
            if(empty($change_info['communist_avatar']) || !file_exists(SITE_PATH.$change_info['communist_avatar'])){
                $change_info['communist_avatar'] = "/statics/public/images/default_photo.jpg";
            }
            $change_info['old_party'] = getPartyInfo($change_info['old_party']);
			$change_info['status'] = getStatusInfo('change_audit_status',$change_info['change_audit_status']);
            $change_info['new_party'] = getPartyInfo($change_info['new_party']);
			$change_info['add_time'] = getFormatDate($change_info['add_time'],'Y年m月d日');
			// $start_time = empty($change_info['start_time']) ? $change_info['add_time'] : $change_info['start_time'];
            // $end_time = empty($change_info['end_time']) ? date('Y-m-d H:i:s') : $change_info['end_time'];
            $log_list = $db_change->where("communist_no=$communist_no")->order('add_time desc')->field('old_party,new_party,change_audit_status,add_time,memo')->select();
			foreach($log_list as &$list){
				$list['old_party'] = getPartyInfo($list['old_party']);
				$list['status'] = getStatusInfo('change_audit_status',$list['change_audit_status']);
				$list['new_party'] = getPartyInfo($list['new_party']);				
				$list['years_month'] = getFormatDate($list['add_time'],'Y.m');
				$list['day'] = getFormatDate($list['add_time'],'d');
			}
			$change_info['log_list'] = $log_list;
            $this->send('获取成功', $change_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_integral_info
     * @desc 获取积分详情
     * @param int communist_no 党员编号
     * @param int type 类型 本支部本系统
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_integral_info(){
        (new CommunistNoValidate())->goCheck();

        $db_integral = new CcpIntegralLogModel();

        $communist_no = I('post.communist_no');
        $type = I('post.type');
        $integral_info = getCommunistRank($communist_no,'',$type);
        if ($integral_info) {
            $integral_list = $db_integral->getIntegralLog('2', $communist_no, '', $type);
            $integral_info['log_list'] = $integral_list;
            $this->send('获取成功', $integral_info, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_integral_list
     * @desc 获取积分列表
     * @param int communist_no 党员编号
     * @param int type 类型 本支部本系统
     * @param int pagesize 条数
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_integral_list(){
        (new CommunistNoValidate())->goCheck();
        (new NumberValidate(['type', 'pagesize']))->goCheck();
        $communist_no = I('post.communist_no');
        $type = I('post.type');
        $pagesize = I('post.pagesize');
        $page = (I("post.page") - 1) * $pagesize;
        if ($type == '1') {
            $party_no = getCommunistInfo($communist_no, 'party_no');
            $communist_list = getCommunistList($party_no, 'str', '0');
        }
        $where = '1=1';
        if (!empty($communist_list)) {
            $where .= " and c.communist_no in($communist_list)";
        }
        //$integral_list = M()->query("SELECT c.post_no,c.communist_tel,c.communist_no,c.communist_name,c.party_no,a.change_integral as integral_total FROM sp_ccp_communist as c LEFT JOIN (SELECT log_relation_no,change_integral,year,change_type from sp_ccp_integral_log where year=" . date('Y') . " )  as a on a.log_relation_no = c.communist_no WHERE ( c.status in(" . COMMUNIST_STATUS_OFFICIAL . ") and $where )GROUP BY communist_no ORDER BY integral_total desc limit $pagesize");
         //integral_list = M()->query("SELECT log_relation_no, SUM(change_integral) as integral_total FROM sp_ccp_integral_log WHERE $where GROUP BY log_relation_no ORDER BY integral_total DESC LIMIT $pagesize");
        $integral_list = M()->query("SELECT c.party_no,c.communist_no, c.communist_name,IFNULL(log.integral_total,0) as integral_total FROM sp_ccp_communist c LEFT JOIN ( SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = 1 GROUP BY log_relation_no) log ON c.communist_no = log.log_relation_no WHERE  $where ORDER BY integral_total DESC,c.communist_no asc limit 0,$pagesize");
        if (!empty($integral_list)) {
            $i=1;
            foreach ($integral_list as &$list) {
                $list['rank'] = $i++;
                $list['party_no'] = getCommunistInfo($list['communist_no'],'party_no');
                $list['party_name'] = getPartyInfo($list['party_no']);
                if (empty($list['integral_total'])) : $list['integral_total'] = 0; endif;
            }
            $this->send('获取成功', $integral_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_communist_integral_log_list
     * @desc 获取积分日志列表
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_integral_log_list(){
        (new CommunistNoValidate())->goCheck();
        $db_integral = new CcpIntegralLogModel();
        $communist_no = I('post.communist_no');
        $integral_list = $db_integral->getIntegralLog('2', $communist_no, '1');
        if (!empty($communist_no)) {
            $this->send('获取成功', $integral_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  get_hr_post_list
     * @desc 获取职位列表
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_post_list(){
        $post_list = getPartydutyList();
        if (!empty($post_list)) {
            $this->send('获取成功', $post_list, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_hr_communist_change
     * @desc 写入转出申请
     * @param int communist_no 党员编号
     * @param int change_type 变动类型
     * @param string new_party 新支部
     * @param string memo 备注
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function set_hr_communist_change(){
        // (new CommunistNoValidate())->goCheck();

        // (new NumberValidate(['change_type']))->goCheck();

        // (new RequireValidate(['new_party']))->goCheck();

        $db_change = new CcpCommunistChangeModel();

        $data = I("post.");

        $flag = $db_change->isChange($data['communist_no']);

        if (!empty($flag)) : $this->send('请勿重复申请'); endif;

        $data['old_party'] = getCommunistInfo($data['communist_no'], 'party_no');

        $result = $db_change->Post($data);

        if ($result) {
            $this->send('申请成功，请等待审核。', null, 1);
        } else {
            $this->send('申请失败');
        }
    }

    /**
     *  get_hr_communist_change_list
     * @desc 获取转出列表
     * @param int communist_no 党员编号
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_change_list(){
        (new CommunistNoValidate())->goCheck();
        $db_change = M('ccp_communist_change');
        $communist_no = I("post.communist_no");
        $change_type = I("post.change_type");
		if(!empty($communist_no)){
			$where['communist_no'] = $communist_no;
		}
        if(!empty($change_type)){
			$where['change_type'] = $change_type;
		}else{
			$where['change_type'] = array('in',[1,2]);
		}
        $change_data = $db_change->field('change_id,communist_no,change_type,old_party,new_party,change_audit_status,add_time')->where($where)->order('add_time desc')->select();
		$communist_avatar = getCommunistInfo($communist_no,'communist_avatar');
		if(empty($communist_avatar) || !file_exists(SITE_PATH.$communist_avatar)){
			$communist_avatar = "/statics/public/images/default_photo.jpg";
		}
		$party_name = getPartyInfo(getCommunistInfo($communist_no,'party_no'),'party_name');
		if ($change_data) {
            foreach ($change_data as &$list) {
				$list['communist_name'] = getCommunistInfo($list['communist_no']);	
                $list['old_party'] = getPartyInfo($list['old_party'], "party_name");
                $list['new_party'] = getPartyInfo($list['new_party'], "party_name");
                $list['change_type'] = getBdTypeInfo($list['change_type'], "change_type");
                $list['status'] = getStatusInfo('change_audit_status',$list['change_audit_status']);
				$list['add_time'] = getFormatDate($list['add_time'],'Y年m月d日');
            }
            $status = 1;
        } else {
            $status = 0;
        }
        $result = [
            'status' => $status,
            'msg' => '获取成功',
            'communist_name' => getCommunistInfo($communist_no),
            'communist_avatar' => $communist_avatar,
            'party_name' => $party_name,
            'data' => $change_data
        ];
        ob_clean();$this->ajaxReturn($result, 'json');
    }
// {
        // (new CommunistNoValidate())->goCheck();
        // $communist_no = I("post.communist_no");
        // $db_change = M('ccp_communist_change');
        // $db_change = new CcpCommunistChangeModel();
        // $flag = $db_change->isChange($communist_no);
        // if(!empty($flag)){
            // $is_apply = 1;
        // } else {
            // $is_apply = 0;
        // }
        // $change_data = $db_change->where("communist_no=$communist_no and change_type in(1,2)")->order('add_time desc')->field('change_id,change_type,old_party,new_party,change_audit_status,memo,start_time,end_time')->select();
        // if ($change_data) {

            // foreach ($change_data as &$list) {
                // $list['old_party'] = getPartyInfo($list['old_party'], "party_name");
                // $list['new_party'] = getPartyInfo($list['new_party'], "party_name");
                // $list['type_name'] = getBdTypeInfo($list['change_type'], "change_type");
                // $list['status'] = getStatusInfo('change_audit_status',$list['change_audit_status']);
                // $change_id = $list['change_id'];
                // $res = M('ccp_communist_change_log')->where("change_id = '$change_id'")->select();
                // foreach ($res as &$item) {
                    // $item['check_staff'] = getCommunistInfo($item['check_staff']);
                // }
                // $list['log_list'] = $res;
                // $list['is_apply'] = $is_apply;
            // }
            // $this->send('获取成功', $change_data, 1);
        // } else {
            // $this->send();
        // }
    // }
    /**
     *  get_hr_communist_change_info
     * @desc 获取转出详情
     * @param int change_id 变动ID
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_hr_communist_change_info(){
        (new NumberValidate(['change_id']))->goCheck();
        $change_id = I("post.change_id");
        $db_change = M('ccp_communist_change');
		$where['change_id'] = $change_id;
        $change_data = $db_change->where($where)->field('change_id,communist_no,change_type,old_party,new_party,change_audit_status,add_time,memo')->find();
        if ($change_data) {
			$change_data['communist_name'] = getCommunistInfo($change_data['communist_no']);	
            $change_data['old_party'] = getPartyInfo($change_data['old_party'], "party_name");
			$change_data['new_party'] = getPartyInfo($change_data['new_party'], "party_name");
			$change_data['change_type'] = getBdTypeInfo($change_data['change_type'], "change_type");
			$change_data['status'] = getStatusInfo('change_audit_status',$change_data['change_audit_status']);
			$change_data['add_time'] = getFormatDate($change_data['add_time'],'Y年m月d日');
            $this->send('获取成功', $change_data, 1);
        } else {
            $this->send();
        }
    }

    /**
     *  set_hr_communist
     * @desc 修改个人资料
     * @param int communist_no 党员编号
     * @param string communist_mobile 手机号
     * @param string communist_address 地址
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function set_hr_communist(){
        (new CommunistNoValidate())->goCheck();

        $communist_no = I("post.communist_no");
        $data = I('post.');
        $data['update_time'] = date('Y-m-d H:i:s');
        $db_communist = M('ccp_communist');
        $result = $db_communist->where("communist_no=$communist_no")->save($data);
        if ($result) {
            $this->send('操作成功', null, 1);
        } else {
            $this->send('操作失败');
        }
    }
    /**
     *  get_ccp_secretary_list
     * @desc 第一书记列表
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_ccp_secretary_list(){
    	(new NumberValidate(['secretary_type']))->goCheck();  //区分2第一书记/1双联双创
    	$secretary_type = I("post.secretary_type");
    	$secretary_list = getSecretaryList('', $secretary_type);
    	$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
    	foreach ($secretary_list as &$list){
    		$list['communist_name'] = getCommunistInfo($list['communist_no']);
    		$list['party_name'] = getPartyInfo($list['communist_party'],'party_name');
    	}
    	if (!empty($secretary_list)) {
    		$this->send('获取成功', $secretary_list, 1);
    	} else {
    		$this->send('操纵失败');
    	}
    }
    /**
     *  get_secretary_sign_list
     * @desc 第一书记签到列表
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function get_secretary_sign_list(){
    	$db_sign = M("ccp_secretary_sign");
    	$result = $db_sign->order('add_time desc')->select();
    	foreach ($result as &$list){
    		$list['sign_time'] = getFormatDate($list['sign_time'], 'Y-m-d');
    		$list['party_name'] = getPartyInfo($list['communist_party']);
    	}
    	if (!empty($result)) {
    		$this->send('获取成功', $result, 1);
    	} else {
    		$this->send('操纵失败');
    	}
    }
    /**
     *  set_ccp_secretary_sign
     * @desc 第一书记签到
     * @user liubingtao
     * @date 2018/1/31
     * @version 1.0.0
     */
    public function set_ccp_secretary_sign(){
    	$db_sign = M("ccp_secretary_sign");
    	(new CommunistNoValidate())->goCheck();
    	$post['communist_no'] = I('post.communist_no');
    	$post['sign_communist'] = I('post.communist_no');
    	$post['add_staff'] = I('post.communist_no');
    	$post['communist_party'] = getCommunistInfo($post['communist_no'],'party_no');
    	$post['sign_name'] = getCommunistInfo($post['communist_no']);
    	$post['sign_type'] = "2";//第一书记签到类型（区分双联双创）
    	$post['status'] = "1";
    	$post['memo'] = I('post.memo');
    	$post['sign_position'] = I('post.sign_position');//地址
    	$post['sign_gc'] = I('post.sign_gc');//坐标
    	if(!empty($_POST['sign_time'])){
    		$post['sign_time'] = I('post.sign_time');//签到时间
    	}else{
    		$post['sign_time'] = date("Y-m-d H:i:s");
    	}
    	$post['sign_img'] = I('post.sign_img');//签到图片
    	$post['add_time'] = date("Y-m-d H:i:s");
    	$post['update_time'] = date("Y-m-d H:i:s");
    	$result = $db_sign->add($post);
    	if (!empty($result)) {
            $communist_no = I('post.communist_no');
            $integral_firstsecretary = getConfig('integral_firstsecretary');
            $communist_integral = getCommunistInfo($communist_no,'communist_integral');
            updateIntegral(1,7,$communist_no,$communist_integral,$integral_firstsecretary,'第一书记签到'); // 给签到党员加积分
    		$this->send('获取成功',null, 1);
    	} else {
    		$this->send('操纵失败');
    	}
    }
    /**
     * @name:get_communist_feepay
     * @desc：党费列表
     * @author：刘丙涛
     * @addtime:2017-12-29
     * @version：V1.0.0
     **/
    public function get_communist_feepay(){
        $communist_no = I("post.communist_no");
        $month = I("post.month");
        $dues_info =  M('ccp_dues')->where("communist_no='$communist_no' and dues_month='$month'")->find();
        if ($dues_info) {
            $data['status'] = 1;
            $data['msg'] = '成功';
            $dues_info['status_name'] = getStatusName('dues_status', $dues_info['status'],'text');
            $dues_info['order_no'] = $communist_no.$dues_info['dues_id'];//编号重复将无法重复缴费
            $data['dues_info'] = $dues_info;
        } else {
            $data['status'] = 0;
            $data['msg'] = "暂无缴费信息";
        }
        ob_clean();$this->ajaxReturn($data);
    }
    /**
     * @name  set_communist_fee()
     * @desc  保存某人某月党费缴纳信息
     * @param $dues_id  党费记录的id
     * @author 靳邦龙
     * @time   2017-12-04
     */
    public function set_communist_fee(){
        $dues_id=I("post.dues_id");
        if($dues_id){
            $date['dues_time']=date("Y-m-d H:i:s");
            $date['update_time']=date("Y-m-d H:i:s");
            $date['status']=2;
            $dues_res=M('ccp_dues')->where("dues_id='$dues_id'")->save($date);
        }
        if ($dues_res) {
            $res['status'] = 1;
            $res['msg'] = "成功";
        } else {
            $res['status'] = 0;
            $res['msg'] = "暂无缴费信息";
        }
        ob_clean();$this->ajaxReturn($res);
    }
}
