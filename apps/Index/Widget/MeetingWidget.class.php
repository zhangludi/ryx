<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/10
 * Time: 15:01
 */

namespace Index\Widget;


use Think\Controller;

class MeetingWidget extends Controller
{
    public function meeting_list($meeting_type, $type_name, $status = 23, $page = 1, $pagesize = 10, $type = 'default')
    {
        layout(false);
        $page = ($page - 1) * $pagesize;
        $where['status'] = ['eq', $status];
        if (!empty($meeting_type)) : $where['meeting_type'] = ['eq', $meeting_type]; endif;
        $meeting_list = M('oa_meeting')->
                        where($where)->
                        order('add_time desc')->limit($page, $pagesize)->select();
        $num = 1;
        foreach ($meeting_list as &$list) {
            $list['num'] = $num;
            $list['meetting_thumb'] = empty($list['meetting_thumb']) ? null : getUploadInfo($list['meetting_thumb']);
            $list['meeting_people'] = M('oa_meeting_communist')->where("meeting_no={$list['meeting_no']}")->count();
            $num++;
        }
        $this->assign('meeting_list', $meeting_list);
        $this->assign('type_name', $type_name);
        $this->assign('meeting_type', $meeting_type);
        switch ($type) {
            case 'ranking':
                $this->display('parts:ipam_meeting_ranking');
                break;
            case 'no_meeting':
                $this->display('parts:ipam_no_meeting');
                break;
            case 'carousel':
                $this->display('parts:ipam_meeting_carousel');
                break;
            default:
                $this->display('parts:ipam_meeting_list');
        }
    }
    /**
        日期查询会议
    */
    public function meeting_list_date(){
        layout(false);
        $date_num = [date('y年').(date('m')-2).'月',date('y年').(date('m')-1).'月',date('y年').(date('m')-0).'月'];
        $this->assign('date_num',$date_num);
        $map['_string'] = "DATE_FORMAT(meeting_real_end_time, '%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')";
        $map['status'] = 23;
        $meeting_name_arr1 = M('oa_meeting')->where($map)->field("meeting_no,meeting_name")->order('add_time desc')->limit(4)->select();
        foreach($meeting_name_arr1 as &$list){
            $list['meeting_name'] = mb_substr($list['meeting_name'],0,16);
        }
        $this->assign('meeting_name_arr1',$meeting_name_arr1);
        $date_last = date('Y-').'0'.(date('m')-1).'-01';
        $map['_string'] = "DATE_FORMAT(meeting_real_end_time, '%Y%m') = DATE_FORMAT('".$date_last."','%Y%m')";
        $meeting_name_arr2 = M('oa_meeting')->where($map)->field("meeting_no,meeting_name")->order('add_time desc')->limit(4)->select();
        foreach($meeting_name_arr2 as &$list){
            $list['meeting_name'] = mb_substr($list['meeting_name'],0,16);
        }
        $this->assign('meeting_name_arr2',$meeting_name_arr2);
        $date_last = date('Y-').'0'.(date('m')-2).'-01';
        $map['_string'] = "DATE_FORMAT(meeting_real_end_time, '%Y%m') = DATE_FORMAT('".$date_last."','%Y%m')";
        $meeting_name_arr3 = M('oa_meeting')->where($map)->field("meeting_no,meeting_name")->order('add_time desc')->limit(4)->select();
        foreach($meeting_name_arr3 as &$list){
            $list['meeting_name'] = mb_substr($list['meeting_name'],0,16);
        }
        $this->assign('meeting_name_arr3',$meeting_name_arr3);
        $this->display('parts:ipam_meeting_list_date');
    } 
    /**
        热门会议
    */
    public function meeting_list_re(){
        layout(false);
        // $communist = M('oa_meeting as m')->where("status=23")->select();
        $meeting_count = M('oa_meeting_communist as m')->join('left join sp_oa_meeting as o on m.meeting_no=o.meeting_no')->field('count(m.meeting_no) as meeting_count,m.meeting_no,o.status,o.meeting_addr,o.meeting_name,o.meetting_thumb')->group('m.meeting_no')->where('o.status=23')->order('meeting_count desc')->find();
        // $meeting_no = $meeting_count['meeting_no'];
        $meeting_count['meetting_thumb'] = getUploadInfo($meeting_count['meetting_thumb']);
        $this->assign('meeting_count',$meeting_count);
        $meeting_no_list = M('oa_meeting_communist')->where('meeting_no="'.$meeting_count['meeting_no'].'"')->getField("communist_no",true);
        $map['communist_no'] = array('in',$meeting_no_list);
        $communist_list = M('ccp_communist')->where($map)->field('communist_name,communist_avatar')->select();
        $this->assign('communist_list',$communist_list);
        $this->display('parts:ipam_meeting_list_re');
    }
}