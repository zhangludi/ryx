<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/8
 * Time: 15:10
 */

namespace Index\Widget;


use Think\Controller;

class LeftmenuWidget extends Controller
{

    public function personal($active = 1)
    {
        layout(false);
        $menu[0]['name'] = '个人首页';
        $menu[0]['url'] = U('Index/User/ipam_user_index');
        $menu[0]['css'] = 'userinfo_home';
        $menu[0]['is_active'] = 0;
        $menu[1]['name'] = '个人资料';
        $menu[1]['url'] = U('Index/User/ipam_user_info');
        $menu[1]['css'] = 'userinfo_info';
        $menu[1]['is_active'] = 0;
        $menu[2]['name'] = '我的投稿';
        $menu[2]['url'] = U('Index/User/ipam_user_contribute');
        $menu[2]['css'] = 'userinfo_contribute';
        $menu[2]['is_active'] = 0;
        $menu[3]['name'] = '我的考试';
        $menu[3]['url'] = U('Index/User/ipam_user_exam');
        $menu[3]['css'] = 'userinfo_exam';
        $menu[3]['is_active'] = 0;
        $menu[4]['name'] = '我的问卷';
        $menu[4]['url'] = U('Index/User/ipam_user_survey');
        $menu[4]['css'] = 'userinfo_ques';
        $menu[4]['is_active'] = 0;
        $menu[5]['name'] = '我的帮扶';
        $menu[5]['url'] = U('Index/User/ipam_user_help');
        $menu[5]['css'] = 'userinfo_help';
        $menu[5]['is_active'] = 0;
        $menu[6]['name'] = '我的积分';
        $menu[6]['url'] = U('Index/User/ipam_user_integral');
        $menu[6]['css'] = 'userinfo_integral';
        $menu[6]['is_active'] = 0;
        $menu[7]['name'] = '退出登录';
        $menu[7]['url'] = U('Index/Index/logout');
        $menu[7]['is_active'] = 0;

        $menu[$active - 1]['is_active'] = 1;
        $this->assign('menu', $menu);
        $this->display('parts:ipam_user_index');
    }

    public function communist_avatar()
    {
        layout(false);
        $communist_no = session('door_communist_no');
        $avatar = getCommunistInfo($communist_no, 'communist_avatar');
        $this->assign('avatar', $avatar);
        $this->assign('communist_no', $communist_no);
        $this->display('parts:ipam_communist_avatar');
    }

    public function ipam_home_slide($type, $type_no)
    {   
        layout(false);
       
        switch ($type) {
            case 0 :
            	$menu['name'] = '会议';
            	$menu['image'] = 'apps/page_index/images/meeting_icon_nav.png';
                $type_list = M('bd_type')->where("type_group='meeting_type'")->field('type_no,type_name')->select();
                foreach ($type_list as &$list) {
                    $list['checked'] = $type_no === $list['type_no'] ? 1 : 0;
                    $list['url'] = U('Index/Meeting/ipam_meeting_list', array('type_no' => $list['type_no']));
                }
                break;
             case 1 :
             		$menu['name'] = '学习';
             		$menu['image'] = 'apps/page_index/images/meeting_icon_nav.png';
                	$type_list = M('bd_type')->where("type_group='index_edu_type'")->field('type_no,type_name,memo')->select();
                	
                	foreach ($type_list as &$list) {
                		$list['checked'] = $type_no === $list['type_no'] ? 1 : 0;
                		$list['url'] = U($list['memo'], array('type_no' => $list['type_no']));
                	}
                	break;
             case 2 :
                	$menu['name'] = '民生';
                	$menu['image'] = 'apps/page_index/images/meeting_icon_nav.png';
                	$type_list = M('bd_type')->where("type_group='index_life_type'")->field('type_no,type_name,memo')->select();
                	foreach ($type_list as &$list) {
                		$list['checked'] = $type_no === $list['type_no'] ? 1 : 0;
                		$list['url'] = U($list['memo'], array('type_no' => $list['type_no']));
//                 		$list['url'] = U('Index/Meeting/ipam_meeting_list', array('type_no' => $list['type_no']));
                	}
                	break;
             case 3 :
             	$menu['name'] = '专题';
             	$menu['image'] = 'apps/page_index/images/special_title.png';
             	$type_list = M('edu_topic')->where("topic_id in(1,2)")->field('topic_id,topic_title as type_name')->select();
             	foreach ($type_list as &$list) {
                	$list['checked'] = $type_no === $list['topic_id'] ? 1 : 0;
                	$list['url'] = U('Index/Special/ipam_special_study', array('topic_id' => $list['topic_id']));
                }
            	break;
            	case 4 :
                	$menu['name'] = '新闻';
                	$menu['image'] = 'apps/page_index/images/news_icon_nav.png';
                	$type_list = M('cms_article_category')->where("cat_type in (1,4) and status = 1")->field('cat_id,cat_name as type_name')->select();
                	$list = array(array('cat_id' => '1',type_name=>"通知公告"));
                	$li = array(array('cat_id' => '2',type_name=>"今日首推"));
                	$type_list[] = current($li);
                	$type_list[] = current($list);
                	foreach ($type_list as &$list) {
                		$list['checked'] = $type_no === $list['cat_id'] ? 1 : 0;
                		$list['url'] = U('Index/News/ipam_news_list', array('id' => $list['cat_id']));
                	}
                break;
        }

        $this->assign('menu', $menu);
        $this->assign('type_list', $type_list);
        $this->display('parts:ipam_home_slide');
    }
}