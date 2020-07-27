<?php
namespace Perf\Controller;
use Common\Controller\BaseController;
use Zend\Http\Header\From;
use Perf;
class PerfController extends BaseController{
    /**
     * @name  perf_entering()
     * @desc  绩效录入页面
     * @param
     * @return
     * @author 袁文豪
     * @version 版本 V1.0.0
     * @updatetime
     * @addtime   2016-09-22
     */
    public function perf_entering(){
    	$communist_no = I('get.communist_no');
        $arr = array();
        $template_id=I("get.template_id");
        $template_type=I("get.template_type");
        $nos_arr=getAssessTplInfo($template_id,'template_relation_no');
        $communist_nos=$nos_arr['template_relation_no'];
        //获取员工列表
        if(!empty($communist_nos)){
        	$comm_map['communist_no'] = array('in',$communist_nos);
            $communist=M("ccp_communist")->where($comm_map)->field("communist_no,communist_name,party_no")->select();
            foreach($communist as &$communist_list){
                $communist_list['party_pno']=$category_info['party_no'];
                $communist_list['party_no']=$communist_list['communist_no'];
                $communist_list['party_type']="communist";
                $communist_list['party_name']=$communist_list['communist_name'];
                $communist_list['is_communistmember']="1";
            }
        }

        $this->assign("month",date("m"));
        $this->assign('communist_no',$communist_no);
        $this->assign("communist_list",$communist);
        $this->assign("template_id",$template_id);
        $this->assign("template_type",$template_type);
        $this->display("Perfmanage/perf_entering");
    }
    
    
	/** 
	 * @name  pref_assess_tpl_item_do_save()
	 * @desc  考核项添加/修改保存操作
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017-12-05
	 */
    public function pref_assess_tpl_item_do_save(){
		$post=I("post.");
		if($post["item_pid"]==0){
			$post["item_proportion"]="";
		}
		$post['add_time']=date("Y-m-d H:i:s");
		if(!empty($post['item_id'])){
			$item_map['item_id'] = $post['item_id'];
			$post['update_time']=date("Y-m-d H:i:s");
			$query=M("perf_assess_template_item")->where($item_map)->save($post);
			$query = $post['item_id'];
		}else{
		    $post['add_staff']=session('staff_no');
		    $post['status']='1';
		    $post['item_date'] = date('Y-m');
		    $post['update_time']=date("Y-m-d H:i:s");
			$query=M("perf_assess_template_item")->add($post);
			if(!empty($post['item_pid']) && $post['item_type'] == '2'){
				$template_party = M('perf_assess_template')->where("template_id = $post[template_id]")->getField('template_relation_no');
				$template_party_arr = explode(',',$template_party);
				switch ($post['item_manual_type']) {
					case '1':
						$year = date('Y');
						$month = date('m');
						if($post['item_group'] == 'party'){
							$type = '2';
						}else{
							$type = '1';
						}
						$sum['item_id'] = $query;
						$sum['assess_relation_type'] = $post['item_group'];
						$sum['assess_date'] = date('Y-m-d');
						$sum['assess_year'] = date('Y');
						$sum['assess_cycle'] = date('m');
						$sum['assess_cycle_type'] = $post['item_cycle_type'];
						$sum['add_time']=date("Y-m-d H:i:s");
						$sum['status']=1;
						$sum['add_staff']=session('staff_no');
						$sum['update_time']=date("Y-m-d H:i:s");
						foreach ($template_party_arr as $party) {
							$sum['assess_relation_no'] = $party;
							$integral_total = M()->query(" SELECT log_relation_no,SUM(CASE WHEN change_type = '7' THEN change_integral ELSE -change_integral END) AS integral_total FROM sp_ccp_integral_log WHERE log_relation_type = $type AND YEAR = $year AND month = $month AND log_relation_no IN ($party)");
							foreach ($integral_total as $kye=>$integral) {
								$sum['assess_score'] = floor($integral['integral_total']);
							}
							M('perf_assess')->add($sum);
						}
						break;
					default:
						break;
				}
			}
		}
    	if($query){
    			echo "<script>alert('保存成功')</script>";
				echo "<script> window.parent.location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);</script>";
    	}else{
			showMsg("error",'操作失败');
    	}
    }
	/**
	 * @name  perf_examine()
	 * @desc  绩效查看
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_examine(){
		// 现在日期数据
		$Yer=date("Y");
		$date=date("m");
		$time=I("post.time");
		$time = !empty($time) ? $time : $date;
		$this->assign("time",$time);
		$this->assign("Yer",$Yer);
		// 登录人信息数据获取
		$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
		$communist_first = M("ccp_communist")->where($comm_map)->field('communist_name,communist_no')->find();
		$communist_no = '9999';
		if(!empty($communist_first)){
			$communist_name = $communist_first['communist_name'];
			$communist_no = $communist_first['communist_no'];
		}
		$this->assign("communist_no",$communist_no);
		// 登录人绩效
		$assess_map['assess_relation_no'] = $communist_no;
		$assess_map['assess_cycle'] = $time;
		$assess_map['assess_year'] = $Yer;
		$entering=M("perf_assess")->where($assess_map)->select();
		// 基础数据查询
		$type_name_arr = M('bd_type')->where("type_group = 'template_type'")->getField('type_no,type_name');
		$type_names_arr = M('bd_type')->where("type_group = 'cycle_type'")->getField('type_no,type_name');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		// 获取绩效分
		foreach($entering as &$row){
			if($row['assess_no']!=""){
			    $where1['item_id']=$row['item_id'];
			    $items=M('perf_assess_template_item')->where($where1)->find();
			    $row['item_name'] = $items['item_name'];
			    $row['item_proportion'] = $items['item_proportion'];
			    $row['type_name'] = $type_name_arr[$items['item_type']];
			    $row['cycle_name'] = $type_names_arr[$items['item_cycle_type']];
			    $row['communist_name'] = $communist_name_arr[$items['item_manager']];
			    $row['assess_score'] = $row['assess_score']*($row['item_proportion']/100);
			    $per_sum+=$row['assess_score'];
			}
		}
		$luna=getdatelist(date("m"),"all");
		$this->assign("date",$date);
		$this->assign("luna",$luna);
		$this->assign("per_sum",$per_sum);
		$this->assign("communist_info",$entering);	
		// 获取党组织人员数据	
		$party_no_auth = session('party_no_auth');//取本级及下级组织
		$party_map['status'] = 1;
		$party_map['party_no'] = array('in',$party_no_auth);
		$party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
		$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
		$comm_map['party_no'] = array('in',$party_no_auth);
		$communist=M("ccp_communist")->where($comm_map)->field("communist_no,communist_name,party_no")->select();
		foreach($communist as &$communist_list){
			$communist_list['party_pno']=$communist_list['party_no'];
			$communist_list['party_no']=$communist_list['communist_no'];
			$communist_list['party_name']=$communist_list['communist_name'];
			$communist_list['is_communistmember']=1;
		}
		// 组合人员和党组织数据
		$category_list=array_merge($party_list,$communist);
		$this->assign("month",$date);
    	$this->assign("category_list",$category_list);
    	$this->assign("communist_name",$communist_name);
		$this->display("Perfmanage/perf_examine");
	}
	/**
	 * @name  perf_examine_table()
	 * @desc  绩效局刷
	 * @param 
	 * @return 
	 * @author 黄子正   王宗彬(考核方式 ，考核时间 从type表获取)
	 * @version 版本 V1.0.0
	 * @updatetime   2017-12-05 
	 * @addtime   2016-09-22
	 */
	public function perf_examine_table(){
		$time = I("get.time");
		$communist_no = I("get.communist_no");
		if(empty($communist_no)){
			$staff_no = session('staff_no');
		}
		$Yer=date("Y");
		$date=date("m");
		if(!empty($_POST['time'])){
			$time=I("get.time");
			$this->assign("time",$time);
			$this->assign("Yer",$Yer);
		}else{
			$this->assign("Yer",$Yer);
			$this->assign("time",$date);
		}
		$per_sum=0;
		$assess_map['assess_relation_no'] = $communist_no;
		$assess_map['assess_cycle'] = $time;
		$assess_map['assess_year'] = $Yer;
		$entering=M("perf_assess")->where($assess_map)->select();
		// $entering['per_sum']=0;
		$type_name_arr = M('bd_type')->where("type_group = 'template_type'")->getField('type_no,type_name');
		$type_names_arr = M('bd_type')->where("type_group = 'cycle_type'")->getField('type_no,type_name');
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$i=0;
		foreach($entering as &$row){
		    $i++;
			if($row['assess_no']!=""){
			    $where1['item_id']=$row['item_id'];
			    $items=M('perf_assess_template_item')->where($where1)->find();
			    $row['item_name'] = $items['item_name'];
			    $row['item_proportion'] = $items['item_proportion'];
			    $row['type_name'] = $type_name_arr[$items['item_type']];
			    $row['cycle_name'] = $type_names_arr[$items['item_cycle_type']];
			    $row['communist_name'] = $communist_name_arr[$items['item_manager']];
			    $row['assess_score'] = $row['assess_score']*($row['item_proportion']/100);
			    //$entering['per_sum']+=$row['assess_score'];
			}
		}
		$communist_name = getCommunistInfo($communist_no);
		$luna=getdatelist(date("m"),"all");
		$this->assign("date",$date);
		$this->assign("luna",$luna);
		$this->assign("communist_name",$communist_name);
		$this->assign("communist_info",$entering);
		$adax_arr['count'] = $i;
		$adax_arr['data'] =$entering;
		$adax_arr['code'] =0;
		$adax_arr['msg'] =0;
		ob_clean();$this->ajaxReturn($adax_arr);
		//$this->display("Perfmanage/perf_examine_table");
	}
	/**
	 * @name  perf_mine()
	 * @desc  我的绩效
	 * @param 
	 * @return 
	 * @author 黄子正   王宗彬(考核方式 ，考核时间 从type表获取)
	 * @version 版本 V1.0.0
	 * @updatetime   2017-12-05
	 * @addtime   2016-09-22
	 */
	public function perf_mine(){
		$communist_no=$_SESSION['communist_no'];
		$comm_map['communist_no'] = $communist_no;
		$communist_list=M("ccp_communist")->where($comm_map)->find();
		$Yer=date("Y");
		$date=date("m");
		$party_info=getpartycommunistList();
		$time=I("post.time");
		$time = !empty($time) ? $time : $date;
		$this->assign("time",$time);
		$this->assign("Yer",$Yer);
		$assess_map['assess_relation_no'] = $communist_no;
		$assess_map['assess_cycle'] = $time;
		$assess_map['assess_year'] = $Yer;
		$entering=M("perf_assess")->where($assess_map)->select();
	    $entering['per_sum']=0;
	    $type_name_arr = M('bd_type')->where("type_group = 'template_type'")->getField('type_no,type_name');
	    $type_names_arr = M('bd_type')->where("type_group = 'cycle_type'")->getField('type_no,type_name');
	    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		foreach($entering as &$row){
		    if($row['assess_no']!=""){
		        $where1['item_id']=$row['item_id'];
		        $items=M('perf_assess_template_item')->where($where1)->find();
		        $row['item_name']=$items['item_name'];
		        $row['item_proportion']=$items['item_proportion'];
		        $row['type_name'] = $type_name_arr[$items['item_type']];
		        $row['cycle_name'] = $type_names_arr[$items['item_cycle_type']];
		        $row['communist_name']=$communist_name_arr[$items['item_manager']];
		        $row['assess_score']=$row['assess_score']*($row['item_proportion']/100);
		        $entering['per_sum']+=$row['assess_score'];
		    }
		}
		$luna=getdatelist(date("m"),"all");
		$this->assign("date",$date);
		$this->assign("luna",$luna);
		$this->assign("communist_list",$communist_list);
		$this->assign("communist_info",$entering);
		$this->display("Perfmanage/perf_mine");
	}
	/**
	 * @name  perf_hr_comparison()
	 * @desc  人员绩效对比
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_hr_comparison(){
		$Yer=date("Y");
		$category= getPartyList('');
		$date=getdatelist('','12');
		$arr = array();
		foreach($category as &$category_info){
			$party_no = $category_info['party_no'];
			$category_info['is_communistmember']="2";
			$category_info['party_type']="party";
		}
		$category_list=array_merge($category,$arr);
    	$this->assign("category_list",$category_list);
		$this->display("Perfcomparison/perf_hr_comparison");
	}
	
	/**
	 * @name  perf_hr_comparison_date()
	 * @desc  人员绩效对比ajax返回数据
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_hr_comparison_date(){
		$communist_id=I("get.communist_id");
		$list=getcommunistperfc($communist_id);
		ob_clean();$this->ajaxReturn($list);
	}
	/**
	 * @name  perf_hr_comparison_table()
	 * @desc  人员绩效对比数据获取
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_hr_comparison_table(){
		$party_type=I("post.type");
		$yer=date("Y");
		$month=date("m");
		$party_no = I("post.party_no",1);
		$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
		$comm_map['party_no'] = $party_no;
		$communist=M("ccp_communist")->where($comm_map)->field("communist_name,communist_no")->select();
		$date=getdatelist('','12');
		$assess_item_arr = M('perf_assess_template_item')->getField('item_id,item_proportion');
		foreach($communist as &$list){
			$per="";
			unset($assess_map['assess_cycle']);
			$assess_map['assess_relation_type'] = 'communist';
			$assess_map['assess_relation_no'] = $list['communist_no'];
			$assess_map['assess_year'] = $yer;
			$assess_count = M('perf_assess')->where($assess_map)->count();
			if($assess_count > 0){
				foreach($date as $date_val){
					$assess_map['assess_cycle'] = $date_val[0];
					$entering=M("perf_assess")->where($assess_map)->field('assess_score,item_id')->select();
					$pers=0;
					foreach($entering as &$entering_val){
						$pers+= $entering_val['assess_score']*($assess_item_arr[$entering_val['item_id']]/100);
					}
					$per.=empty($pers)?"0,":$pers.",";
				}
			}
			$entering_score = rtrim($per, ",");
			if(!empty($entering_score)){
				$list['type']=1;
			}
			$list['entering_score']="[$entering_score]";//$entering_score;
        }
        $communist_list = [];
        foreach($communist as &$list){
        	if($list['type'] == '1'){
        		$communist_list[] = $list;
        	}

        }
		$this->assign("communist",$communist);
		$this->display("Perfcomparison/perf_hr_comparison_table");
	}
	/**
	 * @name  perf_party_comparison()
	 * @desc  部门绩效对比
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_party_comparison(){
		$luna=getdatelist();
		$rs=M("ccp_party")->field("party_name,party_no,party_pno")->where("party_pno=1")->select();
		foreach($rs as &$row){
			$party_map['party_pno'] = $row['party_no'];
			$list[]=M("ccp_party")->field("party_name,party_no,party_pno")->where($party_map)->select();
		};
		foreach($list as $v){
			if(!empty($v)){
				foreach($v as $communist){
					$communist_per[]=$communist;
				}
			}
		}
		foreach($communist_per as &$communist_per_party){
			$per_map['party_no'] = $communist_per_party['party_no'];
			$communist_per_party['communist']=M("ccp_communist")->where($per_map)->select();
		}
		$date=date("m");
		if(I("post.time")==""){
				$time=date("Y")."-".date("m");
				$party_per=getpartyperlist($communist_per,$time);
				$this->assign("time",$date);
		}else{
			$time=date("Y")."-".I("post.time");
			$party_per=getpartyperlist($communist_per,$time);
			$this->assign("time",I("post.time"));
		}
		$this->assign("party_per",$party_per);
		$this->assign("date",$date);
		$this->assign("luna",$luna);
		$this->display("Perfcomparison/perf_party_comparison");
	}
	/**
	 * @name  perf_party_comparison()
	 * @desc  年度绩效对比
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-22
	 */
	public function perf_year_comparison(){
		if(I("post.time")!=""){
			$this->assign("time",I("post.time"));
		}else{
			$this->assign("time",date("Y"));
		}
		$party_no_auth = session('party_no_auth');//取本级及下级组织
		$party_map['status'] = 1;
		$party_map['party_no'] = array('in',$party_no_auth);
		$category = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno,gc_lng,gc_lat')->select();
		$this->assign("month",date("m"));
    	$this->assign("category",$category);
		$this->display("Perfcomparison/perf_year_comparison");
	}
	/**
	 * @name  perf_year_comparison_data()
	 * @desc  党员年绩效数据
	 * @param 
	 * @return 
	 * @author 黄子正  杨凯 2018.1.11添加积分和党员推荐
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017 05 09
	 */
	public function perf_year_comparison_data(){
		$yer=I("get.time");
		$party_no=I("get.party_no",1);
    	$where .= " and party_no='$party_no'";
    	$comm_map['party_no'] = $party_no;
    	$comm_map['status'] = array('in',COMMUNIST_STATUS_OFFICIAL);
    	$category_list =M("ccp_communist")->where($comm_map)->field("communist_name,communist_no")->select();
    	$adax_arr['count'] = M("ccp_communist")->where($comm_map)->count();
    	$date=getdatelist('','12');
    	foreach($category_list as &$row){
    		$num_score = 0;
    		foreach($date as &$rr){
    			$entering = M("perf_assess")->where("assess_relation_type = 'communist' and assess_relation_no='".$row['communist_no']."' and assess_cycle ='".$rr[0]."' and assess_year='".$yer."'")->select();
    			$per=0;
    			foreach($entering as &$entering_list){
    				if(!empty($entering_list['item_id'])){
    					$where = "item_id = $entering_list[item_id]" ;
    				}
    				$assess_info=M("perf_assess_template_item")->where($where)->find();
    				if(count(explode(",",$entering_list['assess_score']))==4){
    					$rd=explode(",",$entering_list['assess_score']);
    					$entering_list['assess_score']=($rd['0']+$rd['1']+$rd['2']+$rd['3'])/4*($assess_info['item_proportion']/100);
    				}else{
    					$entering_list['assess_score']=$entering_list['assess_score']*($assess_info['item_proportion']/100);
    				}
    				$per+=$entering_list['assess_score'];
    			}
    			$row[$rr[0]]=$per;
    			$num_score = $num_score+$per;
    		}
    		$integral_map['party_no'] = $row['party_no'];
    		$integral_data = M('ccp_party')->where($integral_map)->getField('party_integral');
    		$row['score'] = $integral_data;
    		$row['num_score'] = $num_score+$row['score'];
    		$row['operate'] = "<a class='btn btn-xs blue btn-outline' href='".U('Perf/operate_integral',array('group'=>1,'type'=>'1','add_no'=>$row['communist_no']))."'>优秀党员</a>
							<a class='btn btn-xs red btn-outline' href='".U('Perf/operate_integral',array('group'=>1,'type'=>'2','add_no'=>$row['communist_no']))."'>纪检约谈</a>";
    		
    	}
    	$adax_arr['data'] =$category_list;
    	$adax_arr['code'] =0;
    	ob_clean();$this->ajaxReturn($adax_arr);
	}
	/********************************党支部绩效**************************************/
	/**
	 * @name  communist_assess_tpl_index()
	 * @desc  党支部绩效模板管理
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017 07 27
	 */
	public function communist_assess_tpl_index(){
		$template_type = I("get.template_type");
		if($template_type == 'communist'){
			$cat = '_1';
		}
		$this->assign("cat",$cat);
		$this->assign("template_type",$template_type);
		$search = I("post.");
		if(!empty($search)){
			$this->assign("search",$search);
		}
		$this->display("Communist/communist_assess_tpl_index");
	}
	/**
	 * @name  communist_assess_tpl_index_data()
	 * @desc  党支部绩效模板管理数据方法
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_index_data(){
	    
		$assess_keyword = I("get.assess_keyword");
		$template_type = I("get.template_type");
		if(!empty($template_type)){
			$tpl_map['template_relation_type'] = $template_type;
		}
		if(!empty($assess_keyword)){
			$tpl_map['template_name'] = array('like','%'.$assess_keyword.'%');
		}
		$assess_data = getAssessTplInfo("","",$tpl_map);
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除此绩效模板？'" . ')){return false;}"';
		$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
		$party_name_arr = M('ccp_party')->getField('party_no,party_name');
		foreach ($assess_data['data'] as &$assess){
			$assess['number_no'] = explode(',',$assess['template_relation_no']);
			$assess_name = "";
			foreach ($assess['number_no'] as &$party){
				if($assess['template_relation_type'] == "communist"){
				    $party_data = $communist_name_arr[$party];
					$assess_name .= $party_data." ";
				}else{
				    $party_data = $party_name_arr[$party];
					$assess_name .= $party_data." ";
				}
			}
			$assess['template_relation_no'] = $assess_name;
			$assess['number_no'] = $assess['number_no'][0];
			// 格式化时间
			$assess['add_time'] = getFormatDate($assess['add_time'],"Y-m-d");
			$assess['template_item'] = "<a class='btn blue btn-xs btn-outline' href='" . U('communist_assess_tpl_item',array('template_id'=>$assess['template_id'],'template_type'=>$template_type)) . "' target='_self'><i class='fa fa-info'></i> 设置考核项 </a>";
			$template_id = $assess['template_id'];

			if($template_type == "communist"){
				//$assess['template_id'] = "<a class='fcolor-22' href='" . U('perf_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'>".$assess['template_id']."</a>";
				//$assess['template_name'] = "<a class='fcolor-22' href='" . U('perf_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'>".$assess['template_name']."</a>";
				$assess['operate'] = "<a class='btn green btn-xs btn-outline' href='" . U('perf_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'><i class='fa fa-edit'></i> 录入 </a>";
			}else{


				//$assess['template_id'] = "<a class='fcolor-22' href='" . U('communist_assess_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'>".$assess['template_id']."</a>";
				//$assess['template_name'] = "<a class='fcolor-22' href='" . U('communist_assess_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'>".$assess['template_name']."</a>";
				$assess['operate'] = "<a class='btn green btn-xs btn-outline' href='" . U('communist_assess_entering',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'><i class='fa fa-edit'></i> 录入 </a>";
			}	
			$assess['operate'] .= $ef."<a class='layui-btn  layui-btn-xs layui-btn-f60' href='" . U('communist_assess_tpl_edit',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self'><i class='fa fa-edit'></i> 编辑</a>  <a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('communist_party_tpl_delete',array('template_id'=>$template_id,'template_type'=>$template_type)) . "' target='_self' $confirm_del><i class='fa fa-trash-o'></i>删除</a>" ;	
		}
		$assess_arr['data'] = $assess_data['data'];
		$assess_arr['count'] = $assess_data['count'];
		$assess_arr['code'] = 0;
		$assess_arr['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($assess_arr);
		//ob_clean();$this->ajaxReturn($assess_data);
	}
	/**
	 * @name  communist_assess_tpl_edit()
	 * @desc  新增/修改党支部绩效模板
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_edit(){

		$template_id = I("get.template_id");

		//获取模板类型
		$template_type = I("get.template_type");
		$this->assign("template_type",$template_type);
		
		if(!empty($template_id)){
			$assess_data = getAssessTplInfo($template_id);
			$party_no = strToArr($assess_data['template_relation_no']);
			$no = "";
			$name = "";
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			$party_name_arr = M('ccp_party')->getField('party_no,party_name');
			foreach ($party_no as &$party){
				if($assess_data['template_relation_type'] == "communist"){
				    $partyname = $communist_name_arr[$party];
				}elseif($assess_data['template_relation_type'] == "party"){
				    $partyname = $party_name_arr[$party];
				}
				if($no == ""){
					$no = $party;
				}else{
					$no .= ",".$party;
				}
				if($name == ""){
					$name = $partyname;
				}else{
					$name .= ",".$partyname;
				}
			}
			$assess_data['no'] = $no;
			$assess_data['name'] = $name;
			$this->assign("template_data",$assess_data);
			$this->assign("template_id",$template_id);
		}
		$assess_type_list=M("perf_assess_template")->select();	//考核方式
		$item_map['item_cycle_type'] = $template_type;
		$assess_cycle_list=M("perf_assess_template_item")->where($item_map)->select();//考核周期
// 		$assess_communist_list=getCommunistList("","arr","1");//责任人选择
// 		$this->assign("assess_type_list",$assess_type_list);
		$this->assign("assess_cycle_list",$assess_cycle_list);
		$this->assign("assess_communist_list",$assess_communist_list);
		$this->display("Communist/communist_assess_tpl_edit");
	}
	
	/**
	 * @name  communist_assess_tpl_do_save()
	 * @desc  保存党支部绩效模板
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_do_save(){
	    $post = I("post.");
		if(!empty($post)){
			//将部门编号以逗号分割
			
		    if ($post['template_type'] == 'communist'){
		        $post['template_relation_no'] = $post['template_communist'];
		    }else {
		        $post['template_relation_no'] = $post['template_party'];
		    }
		   
			$post['template_relation_type'] = $post['template_type'];
		}
		$assess_data = saveAssessTpl($post);
		if ($assess_data) {
			showMsg('success', '操作成功！！！', U('communist_assess_tpl_index',array('template_type'=>$post['template_type'])));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  communist_party_tpl_delete()
	 * @desc  删除党支部绩效模板
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_party_tpl_delete(){
		$template_type = I("get.template_type");
		$template_id = I("get.template_id");
		$db_assess_template = M("perf_assess_template");
		$template_map['template_id'] = $template_id;
		$assess_data = $db_assess_template->where($template_map)->delete();
		if ($assess_data) {
			showMsg('success', '操作成功！！！', U('communist_assess_tpl_index',array('template_type'=>$template_type)));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  get_assess_partyselect_ajax()
	 * @desc  查询部门/人员编号是否已在模板中存在的ajax方法
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function get_assess_partyselect_ajax(){
		$select_no = I("get.select_no");
		$type = I("get.type");
		$where['template_relation_type'] = $type;
		$where['_string'] = "FIND_IN_SET('$select_no',template_relation_no)";

		$template_data = getAssessTplInfo("","",$where);
		if(!empty($template_data)){
			ob_clean();$this->ajaxReturn("error");
		}else{
			ob_clean();$this->ajaxReturn("success");
		}
	}
	/**
	 * @name  communist_assess_tpl_item()
	 * @desc  绩效模板考核项列表
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_item(){
		$template_id = I("get.template_id");
		$template_type = I("get.template_type");
		$this->assign("template_type",$template_type);
		$item_data = getBdTypeList('template_type', $template_id);
		if(!empty($template_id)){
			$this->assign("template_id",$template_id);
		}
		$this->display("Communist/communist_assess_tpl_item");
	}
	/**
	 * @name  communist_assess_tpl_item_data()
	 * @desc  绩效模板考核项数据获取
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_item_data(){
		$template_id = I("get.template_id");
		$group = I("get.group");
		$confirm_del = 'onclick="if(!confirm(' . "'确认删除此绩效模板？'" . ')){return false;}"';
		$where['item_group'] = $group; //党组织绩效的组
		if(!empty($template_id)){
			$item_data['data'] = getAssessTplItemList($template_id,"",$where);
			$communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
			foreach ($item_data['data'] as &$item){
			    $item['communist_name']=$communist_name_arr[$item['item_manager']];
			    if(!empty($item['item_pid'])){
			    	$item['item_pid_name'] = M('perf_assess_template_item')->where("item_id = $item[item_pid]")->getField('item_name');
			    }else{
			    	$item['item_pid_name'] = "无";
			    }
				$item['operate'] = "<a class='layui-btn  layui-btn-xs layui-btn-f60' href='javascript:add(".$item['item_id'].")' target='_self'><i class='fa fa-edit'></i> 编辑</a>  
				    <a class='layui-btn layui-btn-del layui-btn-xs' href='" . U('communist_assess_tpl_item_delete',array('item_id'=>$item['item_id'],'template_id'=>$template_id,'$group'=>$group)) . "' target='_self' $confirm_del><i class='fa fa-trash-o'></i>删除</a>";
			}
		}
		$item_data['code'] = 0;
        $item_data['msg'] = '获取数据成功';
		ob_clean();$this->ajaxReturn($item_data);
	}
	/**
	 * @name  communist_assess_tpl_item_edit()
	 * @desc  绩效模板考核项编辑
	 * @param
	 * @return
	 * @author 王桥元  王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime 2017-12-04
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_item_edit(){
		$template_id = I("get.template_id");
		$item_id = I("get.item_id");
		$assess_group= I("get.group");
		$where['item_pid'] = '0';
		$where['template_id'] = $template_id;
		$item_data = M('perf_assess_template_item')->where($where)->select();
		$this->assign('item_data',$item_data);
		if($assess_group == party){
			$choice = '3,4,5';
		}else{
			$choice = '1,2';
		}
		$this->assign("choice",$choice);
		if(!empty($template_id)){
			//计算已有的权重  #zxk
			$exist_pro=0;	//已有的权重，默认为0
			$exist_map["template_id"]=["eq",$template_id];
			$exist_map["item_pid"]=["neq",0];
			$exist_pro_list=M('perf_assess_template_item')->where($exist_map)->field("item_proportion as pro")->select();
			foreach($exist_pro_list as $key=>$value){
				if(empty($value["pro"])){
					$exist_pro += 0;
				}else{
					$exist_pro += $value["pro"];
				}
			}
			//剩余的权重
			$this->assign("remain_pro" , 100 - $exist_pro);

			//$item_id不为空，是编辑；为空，则是添加
			if(!empty($item_id)){
			    $map['item_id'] = $item_id;
				$item_data = getAssessTplItemInfo($template_id,'',$map);
				$this->assign("assess_list",$item_data);  //考核项的信息
			}
			$this->assign("template_id",$template_id);
		}
		$this->assign("assess_group",$assess_group);
		$this->display("Communist/communist_assess_tpl_item_edit");
	}
	/**
	 * @name  communist_assess_tpl_item_delete()
	 * @desc  删除绩效模板考核项
	 * @param
	 * @return
	 * @author 王桥元
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2017-07-27
	 */
	public function communist_assess_tpl_item_delete(){
		$item_id = I("get.item_id");
		$template_id = I("get.template_id");
		$group = I('get.group');
		$db_assess = M("perf_assess_template_item");
		$assess_map['item_id'] = $item_id;
		$template_item = $db_assess->where($assess_map)->delete();
		if ($template_item) {
			showMsg('success', '操作成功！！！', U('communist_assess_tpl_item',array('template_id'=>$template_id,'template_type'=>$group)));
		} else {
			showMsg('error', '操作失败！！！','');
		}
	}
	/**
	 * @name  communist_assess_list()
	 * @desc  党支部考核项列表
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017 05 09
	 */
	public function communist_assess_list(){
	    $category_list =getPartyList('');
        foreach($category_list as $list){
            $array[] = $list;
        }
	    $this->assign("category_list",$array);
		$this->display("Communist/communist_assess_list");
	}
	/**
	 * @name  communist_target_set()
	 * @desc  党支部目标设定
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2017 05 11
	 */
	public function communist_target_set(){
		$category_list =getPartyList('');
        foreach($category_list as $list){
            $array[] = $list;
        }
	    $this->assign("category_list",$array);
		$this->display("Communist/communist_target_set");
	}
	/**
	 * @name  communist_target_set_data()
	 * @desc  目标设定
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-20
	 */
    public function communist_target_set_data(){
		$party_no=I("get.party_no");
		$group=I("get.group");
		$list=getAssessList('',$party_no,$group);
		foreach($list as &$row){
      		$row['operate'] = "<input type='hidden' value='".$row['assess_id']."' class='form-control center' />
			 <input type='number' value='".$row['setting']."' class='form-control center' />";
      	}
		ob_clean();$this->ajaxReturn($list);
    }
	/**
	 * @name  communist_assess_entering()
	 * @desc 绩效录入
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016 05 11
	 */
	public function communist_assess_entering(){
		$template_type = I("get.template_type");
		$template_id = I("get.template_id");
		$party_no = I('get.party_no');
		$template_data = getAssessTplInfo($template_id,'template_relation_no');
		$party_map['party_no'] = array('in',$template_data['template_relation_no']);
		$party_list = M('ccp_party')->where($party_map)->field('party_no,party_name,party_pno')->select();
		$this->assign("party_no",$party_no);
		$this->assign("month",date("m"));
		$this->assign("year",date("Y"));
	    $this->assign("category_list",$party_list);
	    $this->assign("template_id",$template_id);
	    $this->assign("template_type",$template_type);
		$this->display("Communist/communist_assess_entering");
	}
	/**
	 * @name  communist_assess_entering_data()
	 * @desc  绩效录入数据
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-20
	 */
    public function communist_assess_entering_data(){
    	$communist = I("get.communist_no");
    	$party_no = I("get.party_no");
    	$template_id =  I("get.template_id");
    	$group =  I("get.group");
    	$month = I("get.month");
    	$year = I("get.year");
    	if($group == 'communist'){
    		if(!empty($communist)){
	    		$assess_map['assess_relation_no'] = $communist;
	    	} else {
	    		$map['template_id'] = $template_id;
	    		$template_relation_no = M('perf_assess_template')->where($map)->getField('template_relation_no');
	    		$comm_map['communist_no'] =array('in',$template_relation_no);
	    		$communist_list = M('ccp_communist')->where($comm_map)->field('communist_no')->select();
	    		$communist = $communist_list[0]['communist_no'];
	    		$communist = !empty($communist) ? $communist : 9999;
	    		$assess_map['assess_relation_no'] = $communist;
	    	}
    	} else {
    		if(!empty($party_no)){
	    		$assess_map['assess_relation_no'] = $party_no;
	    	} else {
	    		$map['template_id'] = $template_id;
	    		$template_relation_no = M('perf_assess_template')->where($map)->getField('template_relation_no');
	    		$party_map['party_no'] =array('in',$template_relation_no);
	    		$party_list = M('ccp_party')->where($comm_map)->field('party_no')->select();
	    		$party_no = $party_list[0]['party_no'];
	    		$party_no = !empty($party_no) ? $party_no : 1;
	    		$assess_map['assess_relation_no'] = $party_no;
	    	}
    	}
       	
    	$month = empty($month) ? date('m') : $month;
    	$year = empty($year) ? date('Y') : $year;
     	if(!empty($group)){
     		$tpl_map['item_group'] = $group;
     		$assess_map['assess_relation_type'] = $group;
     	}
     	$assess_map['assess_year'] = $year;
     	$assess_map['assess_cycle'] = $month;
     	$tpl_map['item_pid'] = array('neq','0');
     	$template_item_data['data'] = getAssessTplItemList($template_id,"",$tpl_map);
    	foreach($template_item_data['data'] as &$row){
    		$assess_map['item_id'] = $row['item_id'];
    	    $entering = M("perf_assess")->where($assess_map)->getfield("assess_score");
    		if(!empty($entering)  || $entering == '0'){
    			$readonly="readonly";
    		}else{
    			$readonly="";
    		}
    		if($group == 'communist'){
    			$row['operate'] = "<input type='hidden' value='".$row['item_id']."' class='form-control center' />
    			<input type='number' onblur='query(this)' $readonly value='$entering' class='form-control center' />
    			<input type='hidden' value='".$communist."' class='form-control center' />";
    		} else {
    			$row['operate'] = "<input type='hidden' value='".$row['item_id']."' class='form-control center' />
    			<input type='number' onblur='query(this)' $readonly value='$entering' class='form-control center' />
    			<input type='hidden' value='".$party_no."' class='form-control center' />";
    		}
    	}
    	$template_item_data['code'] = 0;
        $template_item_data['msg'] = '获取数据成功';
        ob_clean();$this->ajaxReturn($template_item_data);
    }
    /**
	 * @name  assess_item_list()
	 * @desc  绩效评分
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2019-04-23
	 */
	public function assess_item_list(){
		$party_no = I('get.party_no');
		$communist_no = I('get.communist_no');
		$item_type = I('get.item_type');
		$year = I('get.year');
		$month = I('get.month');
		$this->assign('year',$year);
		$this->assign('month',$month);
		$this->assign('party_no',$party_no);
		$this->assign('communist_no',$communist_no);
		$this->assign('item_type',$item_type);//1党组织2党员
		ob_clean();$this->display('Communist/assess_item_list');
	}
	/**
	 * @name  assess_item_list_data()
	 * @desc  绩效评分ajax
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2019-04-23
	 */
	public function assess_item_list_data(){
		$party_no = I('get.party_no');
		$communist_no = I('get.communist_no');
		$item_type = I('get.item_type');
		$year = I('get.year');
		$month = I('get.month');
		$page = I('get.page');
        $pagesize = I('get.limit');
        $page = ($page-1)*$pagesize;
		if(!empty($item_type)){
			$map['item_type'] = $item_type;
			if($item_type == '1'){
				if(!empty($party_no)){
					$map['relation_no'] = $party_no;
				}
			}else{
				if(!empty($communist_no)){
					$map['relation_no'] = $communist_no;
				}
			}
		}
		if(!empty($year)){
			$map['year'] = $year;
		}else{
			$map['year'] = date('Y');
		}
		if(!empty($month)){
			$map['month'] = $month;
		}else{
			$map['month'] = date('m');
		}
		$data_list['data'] = M('perf_assess_score')->where($map)->limit($page,$pagesize)->order('add_time desc')->select();
		$data_list['count'] = M('perf_assess_score')->where($map)->count();
		foreach ($data_list['data'] as &$list) {
			$list['name'] = M('perf_assess_item')->where("item_id=".$list['item_id'])->getField('item_name');
			$list['score'] = M('perf_assess_item')->where("item_id=".$list['item_id'])->getField('item_score');
			$list['add_staff'] = getStaffInfo($list['add_staff']);
		}
		$data_list['code'] = 0;
		ob_clean();$this->ajaxReturn($data_list);
	}
	/**
	 * @name  assess_item_list_edit()
	 * @desc  绩效评分
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2019-04-23
	 */
	public function assess_item_list_edit(){
		$party_no = I('get.party_no');
		$communist_no = I('get.communist_no');
		$item_type = I('get.item_type');
		$year = I('get.year');
		$month = I('get.month');
		$this->assign('year',$year);
		$this->assign('month',$month);
		$this->assign('party_no',$party_no);
		$this->assign('communist_no',$communist_no);
		$this->assign('item_type',$item_type);//1党组织2党员
		ob_clean();$this->display('Communist/assess_item_list_edit');
	}
	/**
	 * @name  assess_item_list_do_save()
	 * @desc  绩效评分
	 * @param 
	 * @return 
	 * @author 王宗彬
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2019-04-23
	 */
	public function assess_item_list_do_save(){
		$post = I('post.');
		if($post['item_type'] == '1'){
			$data['relation_no'] = $post['party_no'];
		}else{
			$data['relation_no'] = $post['communist_no'];
		}
		$data['item_type'] = $post['item_type'];
		$data['item_id'] = $post['item_id'];
		$data['year'] = $post['year'];
		$data['month'] = $post['month'];
		$data['year'] = empty($data['year']) ? date('Y') : $data['year']; 
		$data['month'] = empty($data['month']) ? date('m') : $data['month'];
		$data['assess_date'] = date('Y-m-d');
		$data['add_staff'] = session('staff_no');
		$data['add_time'] = date('Y-m-d H:i:s');
		$data['update_time'] = date('Y-m-d H:i:s');
		$res = M('perf_assess_score')->add($data);
		if(!empty($res)){
        	showMsg('success', '操作成功',  U('Perf/assess_item_list',array('item_type'=>$post['item_type'],'party_no'=>$post['party_no'],'communist_no'=>$post['communist_no'],'year'=>$post['year'],'month'=>$post['month'])));
        }else{
        	showMsg('success', '操作失败');
        }

	}
	/**
	 * @name  communist_assess_entering_add()
	 * @desc  绩效录入数据操作
	 * @param 
	 * @return 
	 * @author 黄子正-黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   2017-10-17
	 * @addtime   2016-05-12
	 */
    public function communist_assess_entering_add(){
		$post=I("post.");
        $where['item_id']=$post['item_id'];
		$post['assess_cycle_type']=M('perf_assess_template_item')->where($where)->getField('item_cycle_type');
		if(empty($post['assess_year'])){
			$post['assess_year'] = date('Y');
		}
		$post['assess_date']=date('Y-m-d');
		$post['assess_year']=date('Y');
		$post['add_time']=date("Y-m-d H:i:s");
		$post['add_staff']=session('staff_no');
		$post['status']='1';
		$post['update_time']=date("Y-m-d H:i:s");
		$entering_add=M("perf_assess")->add($post);
		if($entering_add){
		    ob_clean();$this->ajaxReturn('success');//成功
		}else{
			ob_clean();$this->ajaxReturn("error");//成功
		}
    }
	/**
	 * @name  communist_comparison()
	 * @desc  绩效录入数据操作
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-05-12
	 */
    public function communist_comparison(){
		$ccp_party = M('ccp_party');
		$category_list = $ccp_party->where("status = '1'")->select();
		$this->assign("category_lista",$category_list);
		$this->display("Communist/communist_comparison");
    }
		/**
	 * @name  communist_comparison_table()
	 * @desc  
	 * @param 
	 * @return 
	 * @author 黄子正  王桥元
	 * @version 版本 V1.0.0
	 * @updatetime   2017-08-17
	 * @addtime   2016-05-12 
	 */
    public function communist_comparison_table(){
    	$ccp_party = M('ccp_party');
    	$per_entering = M("perf_assess");
    	$pno=I("get.pno");
    	$party_map['party_pno'] = $pno;
    	$party_no = $ccp_party->where($party_map)->getField('party_no',true);
    	if(empty($party_no)){
    		$party_no = $ccp_party->where($party_map)->getField('party_no',true);
    	}
    	if(!empty($party_no)){
    		$party_no=arrToStr($party_no);
    		$sql = "select * from sp_ccp_party where party_no in($party_no)";
    		$category_list = $ccp_party->query($sql);
    	}
    	foreach($category_list as $list){
    		$party_no = $list['party_no'];
    		$template_data =M("perf_assess_template")->where("template_relation_type='party' and FIND_IN_SET('$party_no',template_relation_no)")->order('add_time desc')->find();
    		$assess_list=M("perf_assess_template_item")->where("item_group='party' and template_id ='".$template_data['template_id']."'")->select();
    		$date=getdatelist('','12');
    		$entering_scorea="";
    		foreach($date as $datelist){
    			$yer=date("Y");
    			$month=$datelist['0'];
    			$enteringa=0;
    			foreach($assess_list  as &$lists){
    				$entering=$per_entering->where("assess_relation_type='party' and assess_cycle='$month' and assess_year='$yer' and item_id=".$lists['item_id']." and assess_relation_no = '$party_no'")->getfield("assess_score")*($lists['item_proportion']/100);
    				$enteringa+=$entering;

    			}
    			$entering_scorea.=empty($enteringa)?"0,":$enteringa.",";
    		}
    		$entering_scorea=substr($entering_scorea,0,-1);
    		$list['entering_score']="[$entering_scorea]";//$entering_scorea;
    		$array[] = $list;
    	}
    	$this->assign("category_list",$array);
		$this->display("Communist/communist_comparison_table");
    }
	/**
	 * @name  communist_comparison_details()
	 * @desc  人员绩效详情
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-05-12
	 */
	public function communist_comparison_details(){
		$charge_party = strToarr(session('party_no_auth'));
        $party_no = $charge_party[0];

	    $assess=getAssessInfo($party_no,date("Y-m"),'party',1);
		$this->assign("assess",$assess);
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $category_list = M('ccp_party')->where($party_map)->select();
		$this->assign("month",date("m"));
	    $this->assign("category_lista",$category_list);
		$this->display("Communist/communist_comparison_details");
	}
	/**
	 * @name  communist_comparison_details_data()
	 * @desc  绩效录入数据
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-09-20
	 */
    public function communist_comparison_details_data(){
		$party_no=I("get.party_no");
		$month=I("get.month");
		$group=I("get.group");
		$db_template = M("perf_assess_template");
		$template_map['template_relation_type'] = $group;
		$template_map['_string'] = "FIND_IN_SET('$party_no',template_relation_no)";
		$template_data = $db_template->where($template_map)->select();
		$item_map['item_group'] = $group;
		$item_map['item_pid'] = array('eq','0');
	    $list = getAssessTplItemList($template_data[0]['template_id'],"",$item_map);
	    $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
	    $i=0;
        foreach($list as &$row){
            $i++;
            $row['communist_name'] = $communist_name_arr[$row['item_manager']];
            $arr['assess_relation_no'] = $party_no;
            $arr['assess_relation_type'] = $group;
            $arr['assess_cycle'] = $month;
            $item_arra = M('perf_assess_template_item')->where("item_pid = $row[item_id]")->field('item_id,item_proportion')->select();
            $perf_item_num = 0;// 绩效分
            $perf_item_pro = 0;
            foreach($item_arra as &$arra){
            	$arr['item_id'] = $arra['item_id'];
            	$entering=M("perf_assess")->where($arr)->getfield("assess_score");
            	$entering_sum=$entering*($arra['item_proportion']/100);
            	$perf_item_num += $entering_sum; 
            	$perf_item_pro += $arra['item_proportion'];
            }
            $row['operate'] = empty($perf_item_num)?0:$perf_item_num; //"$entering";
            $row['item_proportion'] = empty($perf_item_pro)?0:$perf_item_pro; //"$entering";
        }
        $adax_arr['data'] = $list;
        $adax_arr['code'] = 0;
        $adax_arr['count'] = $i;
        ob_clean();$this->ajaxReturn($adax_arr);
    }
	/**
	 * @name  communist_comparison_details_table()
	 * @desc  绩效详情
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-05-12
	 */
	public function communist_comparison_details_table(){
		$db_template = M("perf_assess_template");
		$party_no = I("post.party_no");
		$group = I("post.group");
		$month=I("post.month");
		$assess_data = getAssessInfo($party_no,$month,$group,1);
		$this->assign("assess",$assess_data);
		$this->display("Communist/communist_comparison_details_table");
	}
	/**
	 * @name  communist_year_comparison()
	 * @desc  年度绩效
	 * @param 
	 * @return 
	 * @author 黄子正
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-05-12
	 */
	public function communist_year_comparison(){
		$yer=date("Y");
		$this->assign("time",$yer);
		$party_no_auth = session('party_no_auth');//取本级及下级组织
        $party_map['status'] = 1;
        $party_map['party_no'] = array('in',$party_no_auth);
        $category_list = M('ccp_party')->where($party_map)->select();
		$this->assign("category_list",$category_list);
		$this->display("Communist/communist_comparison_year");
	}
	/**
	 * @name  communist_year_comparison_data()
	 * @desc  人员年度绩效对比数据获取
	 * @param 
	 * @return 
	 * @author 黄子正   杨凯 2018.1.11添加积分和党员推荐
	 * @version 版本 V1.0.0
	 * @updatetime   
	 * @addtime   2016-05-12
	 */
	public function communist_year_comparison_data(){
		$yer=I("get.time");
		$party_no=I("get.party_no");
		if(empty($party_no)){
			$charge_party = strToarr(session('party_no_auth'));
        	$party_no = $charge_party[0];
		}
		if(!empty($party_no)){
			$party_map['party_no'] = $party_no;
		}
	    $party_map['status'] = 1;
		$category_list = M("ccp_party")->where($party_map)->field("party_name,party_no")->select();
		$adax_arr['count'] = M("ccp_party")->where($party_map)->count;
		$date=getdatelist('','12');
		foreach($category_list as &$row){
			$num_score = 0;
			foreach($date as &$rr){
				$assess_map['assess_relation_type'] = 'party';
				$assess_map['assess_relation_no'] = $row['party_no'];
				$assess_map['assess_cycle'] = $rr[0];
				$assess_map['assess_year'] = $yer;
				$entering = M("perf_assess")->where($assess_map)->select();
				$per=0;
				foreach($entering as &$entering_list){
					if(!empty($entering_list['item_id'])){
						$where = "item_id = $entering_list[item_id]" ;
					}
					$assess_info=M("perf_assess_template_item")->where($where)->find();
					if(count(explode(",",$entering_list['assess_score']))==4){
						$rd=explode(",",$entering_list['assess_score']);
						$entering_list['assess_score']=($rd['0']+$rd['1']+$rd['2']+$rd['3'])/4*($assess_info['item_proportion']/100);
					}else{
						$entering_list['assess_score']=$entering_list['assess_score']*($assess_info['item_proportion']/100);
					}
					$per+=$entering_list['assess_score'];
				}
				$row[$rr[0]]=$per;
				$num_score = $num_score+$per;
			}
			$integral_map['party_no'] = $row['party_no'];
    		$integral_data = M('ccp_party')->where($integral_map)->getField('party_integral');
			$row['score'] = $integral_data;
			$row['num_score'] = $num_score+$row['score'];
			$row['operate'] = "<a class='btn btn-xs blue btn-outline' href='".U('Perf/operate_integral',array('group'=>2,'type'=>'1','add_no'=>$row['party_no']))."'>优秀党员</a>
							<a class='btn btn-xs red btn-outline' href='".U('Perf/operate_integral',array('group'=>2,'type'=>'2','add_no'=>$row['party_no']))."'>纪检约谈</a>";
		}
		$adax_arr['data'] = $category_list;
		$adax_arr['code'] = 0;
		
		ob_clean();$this->ajaxReturn($adax_arr);
	}
	/**
	 * @name  operate_integral()
	 * @desc  推荐/约谈。操作
	 * @param
	 * @return
	 * @author 杨凯
	 * @version 版本 V1.0.0
	 * @updatetime
	 * @addtime   2018-01-11
	 */
	public function operate_integral(){
		$group=I("get.group");//1为党员2为党组织
		$type=I("get.type");//1优秀党员2纪检约谈
		$add_no=I("get.add_no");//添加党员、党组织编号
		if($type==1){
			$res = setSuperviseOutstandingInfo($add_no,session('communist_no'),$group);
		}else {
			$res = setSuperviseChatInfo($add_no,session('communist_no'),$group);
		}
		if($group==1){
			$url = U('Perf/perf_year_comparison');
		}else {
			$url = U('Perf/communist_year_comparison');
		}
		if($res){
			showMsg('success',$res,$url);
		}else{
			showMsg('error',$res);
		}
	}
	/*********************************党支部绩效结束***********************************************/
}
?>
