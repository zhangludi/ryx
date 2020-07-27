<?php
/***************************大数据***********************************************/
namespace Bigdata\Controller;
use Think\Controller;
use Common\Controller\BaseController;
use Think\Cache\Driver\Redis;
class IndexController extends BaseController
{

    /**
     * @name  index()
     * @desc  党建大数据首页
     * @param
     * @return
     * @author 王宗彬
     * @version 版本 V1.0.0
     * @updatetime 2018-6-26
     * @addtime   2018-6-26
     */
    public function index(){
		$bd_communist = M('ccp_communist');
		$bd_party = M('ccp_party');
		$bd_meeting = M('oa_meeting');
		$party_no_all = session('party_no_auth');//权限下的党组织编号
		//党组织(个)
		$where_party_num['party_no'] = array('in',$party_no_all);
		$party_num = $bd_party->where($where_party_num)->count();
		//党员(个)
        $where_communist_num['party_no']=array('in',$party_no_all);
        $where_communist_num['status']=array('in',COMMUNIST_STATUS_OFFICIAL);
        $communist_num = $bd_communist->where($where_communist_num)->count();
		//活动(次)
        $where_meeting_num['party_no']=array('in',$party_no_all);
        //$where_meeting_num['status'] = 23;
		$meeting_count = $bd_meeting->where($where_meeting_num)->count();
		$minutes_count = M('ccp_communist_comment')->where($where_map)->count();//民主评议数量
		$volunteer_count = M('life_volunteer_activity')->count();//志愿者活动数量
		$activity_num = $meeting_count + $minutes_count + $volunteer_count;
		//党费(元)
		$dues_map['dues_month'] = date('Y-m');
		$dues_map['status'] = 2;
		$dues_map['party_no'] = array('in',$party_no_all);
		$dues_sum = M("ccp_dues")->where($dues_map)->sum('dues_amount');
		if(!$dues_sum){
			$dues_sum = 0;
		}
		$this->assign('party_num',$party_num);//党组织(个)
		$this->assign('communist_num',$communist_num);//党员(个)
		$this->assign('activity_num',$activity_num);//活动(次)
		$this->assign('dues_sum',$dues_sum);//党费(元)
		/********************************党员数据情况**********************/
		//书记总数
		$where_secretary_num['_string'] = "FIND_IN_SET('201',post_no)";//支部书记
		$secretary_num = $bd_communist->where($where_secretary_num)->count();
		//性别
		$where_communist_sex['party_no']=array('in',$party_no_all);
		$communist_data = M()->query("select  communist_no,communist_sex,age_distribute,(year(now())-year(communist_birthday)-1) + ( DATE_FORMAT(communist_birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as age,(year(now())-year(communist_ccp_date)-1) + ( DATE_FORMAT(communist_ccp_date, '%m%d') <= DATE_FORMAT(NOW(), '%m%d') ) as ccp_age from sp_ccp_communist where party_no in($party_no_all) and status in (" . COMMUNIST_STATUS_OFFICIAL . ")");
		$zero=0;
		$one=0;
		$two=0;
		$tee=0;
		$four=0;
		$five=0;
		$thirty=0;
		$forty=0;
		$fifty=0;
		$greater_sixty=0;
		$man_num=0;
		$woman_num=0;
		$agedness_num=0;
		foreach ($communist_data as $comm_val) {
			/*通过党龄统计比例*/
			if (abs($comm_val['ccp_age']) <= 5) {
				$zero++;
			} elseif (abs($comm_val['ccp_age']) <= 10) {
				$one++;
			} elseif (abs($comm_val['ccp_age']) <= 20) {
				$two++;
			} elseif (abs($comm_val['ccp_age']) <= 30) {
				$tee++;
			} elseif (abs($comm_val['ccp_age']) <= 40) {
				$four++;
			} elseif (abs($comm_val['ccp_age']) > 40) {
				$five++;
			}
			 /*通过年龄统计比例*/
			if (abs($comm_val['age']) <= 30) {
				$thirty++;
			} elseif (abs($comm_val['age']) <= 40) {
				$forty++;
			} elseif (abs($comm_val['age']) <= 50) {
				$fifty++;
			} elseif (abs($comm_val['age']) <= 60) {
				$sixty++;
			} elseif (abs($comm_val['age']) > 60) {
				$greater_sixty++;
			}
			/*通过性别统计比例*/
			if (abs($comm_val['communist_sex']) == '1') {
				$man_num++;
			} else {
				$woman_num++;
			}
			/*老年党员*/
			if (abs($comm_val['age_distribute']) == '3') {
				$agedness_num++;
			} 
		}
		if(!$agedness_num){
			$agedness_num = '0.001';
		}
		$man_num = ($man_num/$communist_num)*100;
		$woman_num = ($woman_num/$communist_num)*100;
				
		$this->assign('secretary_num',$secretary_num);//书记总数
		$this->assign('man_num',$man_num);//男
		$this->assign('woman_num',$woman_num);//女
		$this->assign('agedness_num',$agedness_num);//老年党员
		//党龄统计比例
		$this->assign('zero',$zero);
		$this->assign('one',$one);
		$this->assign('two',$two);
		$this->assign('tee',$tee);
		$this->assign('four',$four);
		$this->assign('five',$five);
		//年龄统计比例
		$this->assign('thirty',$thirty);
		$this->assign('forty',$forty);
		$this->assign('fifty',$fifty);
		$this->assign('sixty',$sixty);
		$this->assign('greater_sixty',$greater_sixty);
		/*********************组织类型情况*************************/
		$where_party_committee['party_no'] = array('in',$party_no_all);
		$where_party_committee['party_level_code'] = 1;
		$party_level1 = $bd_party->where($where_party_committee)->count();
		$where_party_committee['party_level_code'] = 7;
		$party_level2 = $bd_party->where($where_party_committee)->count();
		$where_party_committee['party_level_code'] = 8;
		$party_level3 = $bd_party->where($where_party_committee)->count();
		
		$this->assign('party_level1',$party_level1);//省委
		$this->assign('party_level2',$party_level2);//企业党委
		$this->assign('party_level3',$party_level3);//党支部
		/***************************三会一课情况***************************/
		//本年
		$where_session['party_no']=array('in',$party_no_all);
		$where_session['meeting_type']=array('in','2001,2002,2003,2004');
		$meeting_num = $bd_meeting->where($where_session)->count();
		$where_session['meeting_type']=2001;
		$where_session['_string'] = "DATE_FORMAT(meeting_start_time, '%Y') = DATE_FORMAT(CURDATE(),'%Y')";
		$year_party_session = $bd_meeting->where($where_session)->count();//支部大会本年
		$where_session['meeting_type']=2002;
		$year_cpc_session = $bd_meeting->where($where_session)->count();//支委会议本年
		$where_session['meeting_type']=2003;
		$year_group_session = $bd_meeting->where($where_session)->count();//党小组会本年
		$where_session['meeting_type']=2004;
		$year_lesson_session = $bd_meeting->where($where_session)->count();//党课本年
		//本月
		$where_session['_string'] = "DATE_FORMAT(meeting_start_time, '%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')";
		$where_session['meeting_type']=2001;
		$month_party_session = $bd_meeting->where($where_session)->count();//支部大会本月
		$where_session['meeting_type']=2002;
		$month_cpc_session = $bd_meeting->where($where_session)->count();//支委会议本月
		$where_session['meeting_type']=2003;
		$month_group_session = $bd_meeting->where($where_session)->count();//党小组会本月
		$where_session['meeting_type']=2004;
		$month_lesson_session = $bd_meeting->where($where_session)->count();//党课本月
		
		$this->assign('meeting_num',$meeting_num);//三会一课总数
		$this->assign('year_party_session',$year_party_session);//支部大会本年
		$this->assign('month_party_session',$month_party_session);//支部大会本月
		$this->assign('year_cpc_session',$year_cpc_session);//支委会议本年
		$this->assign('month_cpc_session',$month_cpc_session);//支委会议本月
		$this->assign('year_group_session',$year_group_session);//党小组会本年
		$this->assign('month_group_session',$month_group_session);//党小组会本月
		$this->assign('year_lesson_session',$year_lesson_session);//党课本年
		$this->assign('month_lesson_session',$month_lesson_session);//党课本月
		
		//随手拍分布情况
		//$condition_list = M('life_condition')->field("type_no,ISNULL(COUNT(*),0) as condition_count")->where("type_no in('1','10','11','13','14')")->group('type_no')->select();
		//dump($condition_list);die;
		$condition_count = M('life_condition')->count();
		$where_condition['type_no'] = array('in','1');
		$condition1 = M('life_condition')->where($where_condition)->count();
		$where_condition['type_no'] = array('in','10');
		$condition2 = M('life_condition')->where($where_condition)->count();
		$where_condition['type_no'] = array('in','11');
		$condition3 = M('life_condition')->where($where_condition)->count();
		$where_condition['type_no'] = array('in','13');
		$condition4 = M('life_condition')->where($where_condition)->count();
		$where_condition['type_no'] = array('in','14');
		$condition5 = M('life_condition')->where($where_condition)->count();
		$where_condition['type_no'] = array('in','15,16,17,18');
		$condition6 = M('life_condition')->where($where_condition)->count();
				
		$this->assign('condition1',$condition1);
		$this->assign('condition2',$condition2);
		$this->assign('condition3',$condition3);
		$this->assign('condition4',$condition4);
		$this->assign('condition5',$condition5);
		$this->assign('condition6',$condition6);
		
		$condition1_percentage = round(($condition1/$condition_count)*100,0)/100;
		$condition2_percentage = round(($condition2/$condition_count)*100,0)/100;
		$condition3_percentage = round(($condition3/$condition_count)*100,0)/100;
		$condition4_percentage = round(($condition4/$condition_count)*100,0)/100;
		$condition5_percentage = round(($condition5/$condition_count)*100,0)/100;
		$condition6_percentage = round(($condition6/$condition_count)*100,0)/100;
		
		$this->assign('condition1_percentage',$condition1_percentage);
		$this->assign('condition2_percentage',$condition2_percentage);
		$this->assign('condition3_percentage',$condition3_percentage);
		$this->assign('condition4_percentage',$condition4_percentage);
		$this->assign('condition5_percentage',$condition5_percentage);
		$this->assign('condition6_percentage',$condition6_percentage);
		
		//学习资料
		$material_count = M('edu_material')->count();
		$this->assign('material_count',$material_count);		
		$video_cat_list = M('edu_material_category')->where("status=1 and cat_type=21")->getField('cat_id', true);
		$article_cat_list = M('edu_material_category')->where("status=1 and cat_type=11")->getField('cat_id', true);
		$where_article['material_cat'] = array('in',$article_cat_list);
		$article_count = M('edu_material')->where($where_article)->count('material_id');//文章课件
		$where_video['material_cat'] = array('in',$video_cat_list);
		$video_count = M('edu_material')->where($where_video)->count('material_id');//视频数量
		$this->assign('article_count',$article_count);
		$this->assign('video_count',$video_count);
		//宣传资料
		$where_article_count['party_no']=array('in',$party_no_all);
		$article_count = M('cms_article')->where($where_article_count)->count();
		$this->assign('article_count',$article_count);
		/********************************宣传资料情况********************************/
		$article_list = M('cms_article')->where($where_article_count)->field("article_cat,COUNT(*) as article_count")->where("article_cat in('36','37','38','38','46')")->group('article_cat')->select();
		foreach($article_list as &$list){
			$list['article_cat'] = getArticleCatInfo($list['article_cat']);
			$list['percentage'] = round(($list['article_count']/$article_count)*100,2);
		}
		$this->assign('article_list',$article_list);
		
		
		
		
        $this->display("Index/index");
    }

   
}
