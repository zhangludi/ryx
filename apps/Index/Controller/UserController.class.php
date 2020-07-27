<?php
/**
 * Created by PhpStorm.
 * User: liubingtao
 * Date: 2018/4/8
 * Time: 14:54
 */

namespace Index\Controller;


use Ccp\Model\CcpCommunistModel;
use Ccp\Model\CcpIntegralLogModel;
use Think\Controller;

class UserController extends Controller
{
    private $communist_no = null;
    /**
     *  _initialize
     * @desc 构造函数
     * @user liubingtao
     * @date 2018/4/8
     * @version 1.0.0
     */
    public function _initialize()
    {
        $this->communist_no = session('door_communist_no');
        if (!empty($this->communist_no)) {
            $this->assign('is_login', 1);
        } else {
            $this->assign('is_login', 0);
        }
        
        $where['status'] = '1';
        $nav_list = M('sys_nav')->where($where)->order('nav_order asc')->select();
        foreach ($nav_list as  &$list) {
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
     *  ipam_user
     * @desc 我的中心首页
     * @user 王宗彬
     * @date 2019/8/23
     * @version 1.0.0
     */
    public function ipam_user_index()
    {
        $communist_no = $this->communist_no;
        $this->assign('communist_no',$communist_no);
        $communist = getCommunistInfo($communist_no,'all');
        $communist['party_no']=getPartyInfo($communist['party_no']);
        $communist['post_no']=getPartydutyInfo($communist['post_no']);
        $ranking = M()->query("select integral.rank from (select @rownum:=@rownum+1 rank,`sp_ccp_communist`.* from (select @rownum:=0) a, `sp_ccp_communist` where status=1 order by `communist_integral` desc,`add_time`) integral where integral.communist_no = '$communist_no'");
        $party_ranking = M()->query("select integral.rank from (select @rownum:=@rownum+1 rank,`sp_ccp_communist`.* from (select @rownum:=0) a, `sp_ccp_communist` where status=1 and party_no = '".getCommunistInfo($communist_no,'party_no')."' order by `communist_integral` desc,`add_time`) integral where integral.communist_no = '$communist_no'");
        $communist['ranking'] = $ranking['0']['rank'];
        $communist['party_ranking'] = $party_ranking['0']['rank'];
        $communist['communist_source'] = getBdCodeInfo($communist['communist_source'],'communist_source_code');
        $communist['communist_honor'] = getBdCodeInfo($communist['communist_honor'],'communist_honor_code');
        $communist['communist_label'] = getBdCodeInfo($communist['communist_label'],'communist_label_code');
        $communist_log_list = getCommunistLogList($communist_no,COMMUNIST_STATUS_COURSE);//党员历程
        $this->assign('communist_log_list',$communist_log_list);
        $this->assign('communist', $communist);

        $communist_no = session('door_communist_no');
        $customization = getEduCustomization($communist_no);
        $this->assign('customization',$customization);
        $this->display();
    }
    /**
     *  getUserlog
     * @desc 党内生活全纪实
     * @user 王宗彬
     * @date 2018/4/9
     * @version 1.0.0
     */
    public function getUserlog(){
        $post = I('post.');//communist_no,change_type
        $communist_log_list = getCommunistLogList($post['communist_no'],$post['change_type']);
        $statics = "/statics/apps/page_index/images/mine_time.png";
        foreach ($communist_log_list as &$log_list) {
            $data .= "<li class='clearfix'>
                    <div class='va-jz f-w'>
                        <img class='di-ib mr-10em' src='".$statics."'>{$log_list['add_time']}
                    </div>
                    <div class='line4'></div>
                    <div class='party_live_con_bg'>
                        <div class='tit'>{$log_list['log_content']}</div>
                    </div>
                </li>";
        }
        //var_dump($data);die;
        ob_clean();$this->ajaxReturn(['content' => $data]);
    }
    /**
     *  ipam_home_userinfo_info
     * @desc 个人资料
     * @user liubingtao
     * @date 2019/8/23
     * @version 1.0.0
     */
    public function ipam_user_info()
    {
        $communist_info = getCommunistInfo($this->communist_no, 'all');
        $this->assign('data', $communist_info);
        $this->display();
    }

    /**
     *  ipam_home_userinfo_info_save
     * @desc 资料修改
     * @user liubingtao--liuchangjun
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_info_save()
    {
        $data = I('post.');
        $communist = M('ccp_communist');
        $comm_map['communist_no'] = $data['communist_no'];
        $result = $communist->where($comm_map)->save($data);
        if ($result) {
            showMsg('success', '操作成功', U('ipam_user_info'));
        } else {
            showMsg('error', '操作失败');
        }
    }

    /**
     *  ipam_home_userinfo_contribute
     * @desc 我的投稿
     * @user liubingtao
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_contribute()
    {
        $db_article = M('cms_article');
        $article = $db_article->where("article_cat=15 and communist_no=$this->communist_no")->field('article_id,article_title,status')->select();
        foreach ($article as &$list) {
            $list['status'] = getStatusName('article_status', $list['status']);
        }
        $this->assign('article', $article);
        $this->display();
    }

    /**
     *  ipam_home_userinfo_exam
     * @desc 我的考试
     * @user liubingtao
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_exam()
    {
        $exam_list = M()->query("select l.exam_id,l.log_score,e.exam_title from sp_edu_exam_log as l left join sp_edu_exam as e on l.exam_id=e.exam_id where l.communist_no=$this->communist_no");
        $this->assign('exam_list', $exam_list);
        $this->display();
    }

    /**
     *  ipam_home_userinfo_survey
     * @desc 我的问卷
     * @user liubingtao
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_survey()
    {
        $survey_list = M('life_survey s')
            ->join('left join sp_life_survey_log as l on s.survey_id=l.survey_id')
            ->where("s.status=1 and l.communist_no=$this->communist_no")
            ->field('s.survey_id,s.survey_title,s.status')->select();
        foreach ($survey_list as &$list) {
            $list['status'] = '完成';
        }
        $this->assign('survey_list', $survey_list);
        $this->display();
    }

    /**
     *  ipam_home_userinfo_help
     * @desc 我的帮扶
     * @user liubingtao
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_help()
    {
        $help_map['communist_no'] = $this->communist_no;
        $help_list = M('ccp_communist_help')->where($help_map)->field('help_name,add_time')->select();
        foreach ($help_list as &$list) {
            $list['title'] = getCommunistInfo($this->communist_no).'帮扶了'.$list['help_name'];
            $list['add_time'] = date('Y-m-d', strtotime($list['add_time']));
        }
        $this->assign('help_list', $help_list);
        $this->display();
    }

    /**
     *  ipam_home_userinfo_integral
     * @desc 我的积分
     * @user liubingtao
     * @date 2018/4/10
     * @version 1.0.0
     */
    public function ipam_user_integral()
    {
        $db_integral = new CcpIntegralLogModel();
        $log_list = $db_integral->getIntegralLog('2', $this->communist_no, 1);
        $total = $db_integral->getIntegralInfo(2, $this->communist_no, 1);
        $this->assign('log_list', $log_list);
        $this->assign('total', $total);
        $this->display();
    }
}