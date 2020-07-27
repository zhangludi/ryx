<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/10
 * Time: 14:32
 */

namespace Index\Controller;

use Think\Controller;

class MeetingController extends Controller
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
            if($key==4 ||$key==3 ){
                $list['class_login'] = 'see-details';
            }
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
     *  ipam_meeting_index
     * @desc
     * @user 刘长军
     * @date 2019/8/24
     * @version 1.0.0
     */
    public function ipam_meeting_index()
    {
        $type_list = M('bd_type')->where("type_group='meeting_type'")->field('type_no,type_name')->select();
        $this->assign('type_list', $type_list);
        // 轮播图
        $where['status'] = 23;
        $meeting_list = M('oa_meeting')->where($where)->field("meeting_no,meeting_name,meeting_real_start_time,meeting_addr,meeting_host,meetting_thumb")->order('add_time desc')->limit(2)->select();
        foreach ($meeting_list as &$list) {
            $list['meetting_thumb'] = getUploadInfo($list['meetting_thumb']);
            $list['meeting_people'] = M('oa_meeting_communist')->where("meeting_no={$list['meeting_no']}")->count();
            $list['meeting_host'] = getCommunistInfo($list['meeting_host']);
        }
        $this->assign('meeting_list',$meeting_list);
        $this->display();
    }
    /**
     *  ipam_meeting_index_count
     * @desc 会议个数ajax
     * @user 刘长军
     * @date 2019/8/24
     * @version 1.0.0
     */
    public function ipam_meeting_index_count(){
        if(I('post.type_no')){
            $meeting_map['meeting_type'] = I('post.type_no');
        }
        $meeting_map['status'] = 23;
        $count = M('oa_meeting')->where($meeting_map)->count('meeting_no');
        ob_clean();$this->ajaxReturn($count);
    }
    /**
     *  ipam_meeting_index_ajax
     * @desc 会议列表ajax
     * @user 刘长军
     * @date 2019/8/24
     * @version 1.0.0
     */
    public function ipam_meeting_index_ajax()
    {
        $type_no = I('post.type_no');
        $pagesize = I('post.pagesize');
        // $page = (I('post.page') - 1) * $pagesize;
        if($type_no){
            $meeting_map['meeting_type'] = $type_no;
        }
        $meeting_map['status'] = 23;
        $meeting_list = M('oa_meeting')->where($meeting_map)->field('meeting_no,meeting_camera,meeting_type,meeting_name,meeting_addr,meeting_start_time,memo,meetting_thumb')->limit($pagesize)->order('add_time desc')->select();
        $type_list = M('bd_type')->where("type_group='meeting_type'")->getField('type_no,type_name');
        $data = '';
        foreach ($meeting_list as $list) {
            $list['meeting_type'] = $type_list[$list['meeting_type']];
            $data .= "<li onclick='meeting_info(".$list['meeting_no'].")'>
                <div class='tit' style='overflow: hidden;text-overflow: ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:1;' title=".$list['meeting_name'].">".$list['meeting_name']."</div>
                <div class='clearfix mt-20'>
                    <div class='pull-left po-re'>
                        <img src='".getUploadInfo($list['meetting_thumb'])."' style='width:180px;height:102px'>
                        <div class='type'>".$list['meeting_type']."</div>
                    </div>
                    <div class='pull-right w-72'>
                        <div class='tit2' style='overflow: hidden;text-overflow: ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:3;height:4.7rem'>".$list['memo']."</div>
                        <div class='clearfix'>
                            <div class='pull-left address color-9e f-16em' style='overflow: hidden;text-overflow: ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:1;width:6rem;' title=".$list['meeting_addr'].">".$list['meeting_addr']."</div>
                            <div class='pull-right time color-9e f-16em'>时间：".$list['meeting_start_time']."</div>
                        </div>
                        
                    </div>
                </div>
            </li>";
        }
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }
    /**
     *  ipam_meeting_list
     * @desc
     * @user liubingtao
     * @date 2018/4/11
     * @version 1.0.0
     */
    public function ipam_meeting_list()
    {
        $type_no = I('get.type_no');
        $type_name = getBdTypeInfo($type_no, 'meeting_type');
        $meeting_map['meeting_type'] = $type_no;
        $meeting_map['status'] = 23;
        $count = M('oa_meeting')->where($meeting_map)->count();
        $this->assign('count', $count);
        $this->assign('type_name', $type_name);
        $this->assign('type_no', $type_no);
        $this->display();
    }

    /**
     *  getMeetingList
     * @desc 获取会议列表
     * @return string
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function getMeetingList()
    {
        $type_no = I('post.type_no');
        $pagesize = I('post.pagesize');
        $page = (I('post.page') - 1) * $pagesize;
        $meeting_map['meeting_type'] = $type_no;
        $meeting_map['status'] = 23;
        $meeting_list = M('oa_meeting')->where($meeting_map)->field('meeting_no,meeting_camera,meeting_name,meeting_start_time,memo,meetting_thumb')->order('add_time desc')->limit($page,$pagesize)->select();
        $data = '';
        foreach ($meeting_list as $list) {
            $data .= "<li class='public_word_lr_two mb-15 w-all'>
                        <a  class='see-details' href='".U('ipam_meeting_detail', array('meeting_no' => $list['meeting_no'], 'type_no' => $type_no))."'>
                            <div class='public_word_lr_two_img w-120 over-h'>
                                <img src='".getUploadInfo($list['meetting_thumb'])."' class='di-b w-all center-block'>
                            </div>
                            <div class='public_word_lr_two_content'>
                                <div class='public_word_lr_two_content_title fsize-16 fcolor-11'>
                                {$list['meeting_name']}
                                </div>
                                <div class='public_word_lr_two_content_date fsize-12 fcolor-b3b3b3'>".date('Y-m-d', strtotime($list['meeting_start_time']))."</div>
                                <p class='fsize-14 fcolor-808080'>{$list['memo']}</p>
                            </div>
                        </a>
                    </li>";
        }
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }

    /**
     *  ipam_meeting_detail
     * @desc 会议详情
     * @user liubingtao
     * @date 2018/4/12
     * @version 1.0.0
     */
    public function ipam_meeting_detail()
    {
        $type_no = I('get.type_no');
        $meeting_no = I('get.meeting_no');
        $meeting_map['meeting_no'] = $meeting_no;
        $meeting_info = M('oa_meeting')->where($meeting_map)->
                        field('meeting_name,meeting_camera,meeting_real_start_time,meeting_real_end_time,meeting_addr,meeting_host,memo,meetting_thumb,meeting_type')->find();
        $type_list = M('bd_type')->where("type_group='meeting_type'")->getField('type_no,type_name');
        $meeting_info['meeting_type'] = $type_list[$meeting_info['meeting_type']];
        $meeting_info['meetting_thumb'] = getUploadInfo($meeting_info['meetting_thumb']);
        $communist_list = M('oa_meeting_communist as m')->join('left join sp_ccp_communist as c on m.communist_no=c.communist_no')->where("m.meeting_no=$meeting_no and c.communist_no is not null")->field('c.communist_name,c.communist_avatar')->select();
        $communist_list_count = count($communist_list);
        $content = M('oa_meeting_minutes')->where("meeting_id=$meeting_no")->getField('meeting_minutes_content');
        $this->assign('communist_list_count', $communist_list_count);
        $this->assign('content', $content);
        $this->assign('meeting_info', $meeting_info);
        $this->assign('communist_list', $communist_list);
        $this->assign('type_no', $type_no);
        $this->display();
    }
}