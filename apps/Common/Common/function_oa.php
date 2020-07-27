<?php
/********************************办公相关基础层方法 开始*************************************/

/**
* @name     getOaApprovalNodeInfo           
* @desc     获取指定模板指定字段的值，（支持单调数据多字段查询&&多条数据单字段查询&&多条数据多字段查询）
* @param    $node_no(支持多个)员工编号   
* @param    $field 字段名                   
* @return   指定节点指定字段的值,支持多个查询
* @author   靳邦龙
* @addtime  2017-11-02
* @version  V1.0.1
**/
function getOaApprovalNodeInfo($node_no, $field='node_name'){
    if(!empty($node_no)){
        $db_node=M('oa_approval_template_node');
        $no_arr = strToArr($node_no);
        $no_arr_length = sizeof($no_arr);//取编号数量
        if($field!='all'){
            $arr=strToArr($field);
            $arr_length=sizeof($arr);
            if($arr_length==1){//如果是一个字段，返回字符串
                $map['node_no']  = array('in',$no_arr);
                $node_value=$db_node->where($map)->getField($field,true);
                $node_value = arrToStr($node_value);
            }else if($arr_length>1){//如果是多字段查询，查询单条数据，多个字段
                if($no_arr_length>1){//多条数据select查询
                    $map['node_no']  = array('in',$no_arr);
                    $node_value=$db_node->where($map)->field($field)->select();
                }elseif($no_arr_length==1){//单条数据，find查询
                    $map['node_no']  = array('eq',$node_no);
                    $node_value=$db_node->where($map)->field($field)->find();
                }
            }
        }elseif($field=='all'){//查询完整记录
            if($no_arr_length>1){//多条数据select查询
                $map['node_no']  = array('in',$no_arr);
                $node_value=$db_node->where($map)->select();
            }elseif($no_arr_length==1){//单条数据，find查询
                $map['node_no']  = array('eq',$node_no);
                $node_value=$db_node->where($map)->find();
            }
        }
    }
    if($node_value){
        return $node_value;
    }else{
        return null;
    }
}
/**
 * @desc    获取模板节点列表
 * @name    getOaApprovalTplNodeList()
 * @param   $template_no 模板编号
 * @param   $mode   取值模式 all：取全部     up：取上级列表     down：下级列表，（当参数为prev或next时，需传$current_node_no） 
 * @param   $current_node_no   $mode参数用来对照的当前节点编号 
 * @return  array
 * @author  靳邦龙
 * @time    2017-11-02
 */
function getOaApprovalTplNodeList($template_no,$mode='all',$current_node_no=''){
    if($template_no){
        $db_node=M('oa_approval_template_node');
        $map['template_no']=$template_no;
        if($mode=='up'){
            
        }elseif($mode=='down'){
            
        }
        
    }
    if($status){
        $map['status']=$status;
    }
    $tpl_list=$db_tpl->where($map)->order("add_time desc")->select();
    return $tpl_list;
}
/**
 * @name    getOaApprovalTplSelect()
 * @desc    获取审批模板列表
 * @param   当前模板的编号   $selected_no（支持多个）
 * @return  带选中状态的模板下拉列表（HTML代码）
 * @author  靳邦龙
 * @version 版本 V1.0.1
 * @addtime 2016-04-28
 */
function getOaApprovalTplSelect($selected_no){
    $db_tpl=M('oa_approval_template');
    $template_list=$db_tpl->where('status=1')->field('template_no,template_name')->order("add_time desc")->select();
    $template_options="";
    $select_arr = strToArr($selected_no);
    foreach($template_list as &$template){
        $selected="";
        foreach($select_arr as $arr){
            if($arr==$template['template_no']){
                $selected="selected=true";
            }
        }
        $template_options.="<option $selected value='".$template['template_no']."'>".$template['template_name']."</option>";
    }
    if(!empty($template_options)){
        return $template_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}
/**
 * @desc 获取待我审批列表
 * @name getOaApprovalList()
 * @param $communist_no 员工编号
 * @return array
 * @author 王彬
 * @time 2016-08-03
 */
function getOaApprovalList($communist_no, $status){
    if (empty($communist_no)) {
        $staff_no = session('staff_no');
    }
    if (empty($status)) {
        $sql = "select a.*,l.status as logstatus from sp_oa_approval a,sp_oa_approval_log l where a.node_log_id=l.log_id and   FIND_IN_SET('$communist_no',l.node_staff) ORDER BY a.approval_no DESC";
    } else {
        $sql = "select a.*,l.status as logstatus from sp_oa_approval a,sp_oa_approval_log l where a.node_log_id=l.log_id and FIND_IN_SET('$communist_no',l.node_staff) and l.status=$status ORDER BY a.approval_no DESC";
    }

    $approval_list = M()->query($sql);
    if ($approval_list) {
        return $approval_list;
    } else {
        return "无数据";
    }
}

/** @name    saveOaApproval()
 * @desc    生成审批单过程
 * @param   $data=array(
 *                      'approval_template'=>'',模板编号     必填
 *                      'approval_name'=>'',审批标题    必填
 *                      'approval_apply_man'=>'',发起人编号   必填
 *                      'approval_table_name'=>'',表名     必填
 *                      'approval_table_field'=>'',字段名   必填
 *                      'approval_rewrite_field'=>'' 回写字段  必填 
 *                      'approval_table_field_value'=>对应字段的值   必填
 *                      'approval_attach'=>$approval_attach,  附件  非必填
 *                      'approval_content'=>$approval_content,非必填
 *                      'party_no'=>$party_no,非必填 审核人科室
 *                      'approval_callfunction'=>"saveOaApproval('1','2');"执行方法后回调函数 格式为："方法名称（'参数'）;"注意要有分号 。 非必填字符串类型  
 *                      );
 * @author  靳邦龙--杨凯增加回调函数    王宗彬 增加回写字段
 * @addtime 2017年11月22日
 * @version V1.0.0
 */
function saveOaApproval($data){
    $db_approval=M("oa_approval");
    
    //第一步保存审批单信息
    $data['approval_no']=getFlowNo(date('Ym'), 'oa_approval', 'approval_no', 3);
    $data['approval_time']=date('Y-m-d H:i:s');
    $data['add_staff']=$data['approval_apply_man'];
    $data['add_time']=date('Y-m-d H:i:s');
    $data['update_time']=date('Y-m-d H:i:s');
    $data['status']=11;//待审核
    $add_id=$db_approval->add($data);
    if($add_id){
        //第二步移动节点
        $move_res=moveFields($data['approval_no'], $data['approval_template'],'');
        if($move_res){
            //第三部把approval_node表的第一条数据的员工和岗位改掉2017.11.04修改
            $approval_node=M("oa_approval_node");
            $hr['node_staff']=$data['approval_apply_man'];
            $hr['node_staff_real']=$data['approval_apply_man'];
            $hr['node_post']=getCommunistInfo($data['approval_apply_man'], 'post_no');//岗位字段注意名称可能不同
            $hr['status']=21;//发起人节点固定已审核状态
            $map['approval_no']=array('eq',$data['approval_no']);
            $min_id=$approval_node->where($map)->getField('MIN(node_id)');
            $node['node_id']=array('eq',$min_id);
            $approval_first_save=$approval_node->where($node)->save($hr);
        }else{
            $back['status']=0;
            $back['msg']='审批节点生成失败';
        }
        //第四步修改审批单当前节点
        $res=setOaApprovalCurrentNode($data['approval_no']);
        if(!$res){
            $back['status']=0;
            $back['msg']='审批当前节点保存失败';
        }
    }else{
        $back['status']=0;
        $back['msg']='审批单添加失败';
    }
    if($data['approval_table_name']&&$data['approval_table_field']&&$data['approval_table_field_value']){
        $res=OaApprovalWriteBack($data['approval_no']);//回写状态
    }
    if($res){
        $back['status']=1;
        $back['msg']='审批发起成功';
        $back['approval_no']=$data['approval_no'];
    }else{
        $back['status']=0;
        $back['msg']='参数回写失败';
    }
return $back;
}

/**
* @name     getOaApprovalTplInfo           
* @desc     获取指定模板指定字段的值，（支持单调数据多字段查询&&多条数据单字段查询&&多条数据多字段查询）
* @param    $template_no(支持多个)员工编号   
* @param    $field 字段名                   
* @return   指定模板指定字段的值,支持多个查询
* @author   靳邦龙
* @addtime  2017-11-02
* @version  V1.0.1
**/
function getOaApprovalTplInfo($template_no,$field='template_name'){
    if(!empty($template_no)){
        $db_tpl=M('oa_approval_template');
        $no_arr = strToArr($template_no);
        $no_arr_length = sizeof($no_arr);//取编号数量
        if($field!='all'){
            $arr=strToArr($field);
            $arr_length=sizeof($arr);
            if($arr_length==1){//如果是一个字段，返回字符串
                $map['template_no']  = array('in',$no_arr);
                $template_value=$db_tpl->where($map)->getField($field,true);
                $template_value = arrToStr($template_value);
            }else if($arr_length>1){//如果是多字段查询，查询单条数据，多个字段
                if($no_arr_length>1){//多条数据select查询
                    $map['template_no']  = array('in',$no_arr);
                    $template_value=$db_tpl->where($map)->field($field)->select();
                }elseif($no_arr_length==1){//单条数据，find查询
                    $map['template_no']  = array('eq',$template_no);
                    $template_value=$db_tpl->where($map)->field($field)->find();
                }
            }
        }elseif($field=='all'){//查询完整记录
            if($no_arr_length>1){//多条数据select查询
                $map['template_no']  = array('in',$no_arr);
                $template_value=$db_tpl->where($map)->select();
            }elseif($no_arr_length==1){//单条数据，find查询
                $map['template_no']  = array('eq',$template_no);
                $template_value=$db_tpl->where($map)->find();
            }
        }
    }
    if($template_value){
        return $template_value;
    }else{
        return null;
    }
}
/**
 * @desc    获取模板列表
 * @name    getOaApprovalTplList()
 * @param   $status
 * @return  array
 * @author  靳邦龙
 * @time    2017-11-02
 */
function getOaApprovalTplList($status=1){
    $db_tpl=M('oa_approval_template');
    if($status){
        $map['status']=$status;
    }else{
        $map="1=1";
    }
    $tpl_list=$db_tpl->where($map)->order("add_time desc")->select();
    return $tpl_list;
}

 /**
 * @name:getMeetingCommunistCount           
 * @desc：会议人数及签到人数统计
 * @param：$meeting_no(会议编号) 
 * @return：
 * @author：黄子正
 * @addtime::2017-05-09
 * @updatetime:2017-05-09
 * @version：V1.0.0
**/
function getMeetingCommunistCount($meeting_no){
    $oa_meeting_communist = M('oa_meeting_communist');
    $data=array();
    //参会人数统计
    $where['meeting_no'] = $meeting_no;
    $data["should"]=$oa_meeting_communist->where($where)->count();
    //签到人数统计
    $s=0;
    $communists=$oa_meeting_communist->where($where)->getField("communist_no",true);
    foreach($communists as $v){
        $is_check=getIsChecked($meeting_no,$v);
        if($is_check!="no"){
            $s=$s+1;
        }
    }
    $data["attended"]=$s;
    if($data){
        return $data;
    }else{
        return null;
    }
}

 /**
 * @name:getMeetingCommunistInfo               
 * @desc：获取考勤人员表某一条记录或某一条字段
 * @param：$communist_no(参会人员编号) $meeting_no(会议编号)  $field指定参数
 * @return：
 * @author：黄子正
 * @addtime:2017-05-09
 * @updatetime:2017-05-09
 * @version：V1.0.0
**/
function getMeetingCommunistInfo($communist_no,$meeting_no,$field='all'){
    $oa_meeting_communist = M('oa_meeting_communist');
    if(!empty($communist_no)&&(!empty($meeting_no))){
                $where["communist_no"]=$communist_no;
                $where["meeting_no"]=$meeting_no;
        if($field == "all"){                       
            $data = $oa_meeting_communist->where($where)->find();
            if($data){
                //添加部门名称
                $data['party_name']=getPartyInfo($data['party_no']);        
                 //添加参会人员名称
                $data['communist_name']=getCommunistInfo($data['communist_no'],"communist_name");
                //添加附件地址
                $data['meeting_upload_url']=getUploadInfo($data['meeting_upload_no']);
                //添加签到地点
                $data["checked_addr"]=coordinateToAddr($data["checked_addr"]);
                //添加签到状态判断
                $result1=getIsChecked($data["meeting_no"],$communist_no);
                if($result1!="no"){
                    $data["checked_name"]="已签到";
                }else{
                    $data["checked_name"]="未签到";
                }                          
                return $data;
            }else{
                return null;
            }
        }else{
            $data = $oa_meeting_communist->where($where)->field($field)->find();
            if($data){
                            if(!empty($data['party_no'])){
                                //添加部门名称
                                $data['party_name']=getPartyInfo($data['party_no']);     
                            }
                            if(!empty($data['communist_no'])){
                                 //添加参会人员名称
                                $data['communist_name']=getCommunistInfo($data['communist_no'],"communist_name"); 
                            }
                            if(!empty($data['meeting_upload_no'])){
                                 //添加附件地址
                                 $data['meeting_upload_url']=getUploadInfo($data['meeting_upload_no']);  
                            }
                            if(!empty($data['checked_addr'])){
                                //添加签到地点
                                $data["checked_addr"]=coordinateToAddr($data["checked_addr"]);
                            }
                            if(!empty($data["status"])){
                                //添加签到状态判断
                                $result1=getIsChecked($data["meeting_no"],$communist_no);
                                if($result1!="no"){
                                    $data["checked_name"]="已签到";
                                }else{
                                    $data["checked_name"]="未签到";
                                }              
                            }                        
                            return $data;
            }else{
                            return null;
            }
        }
    }else{
            $data = $oa_meeting_communist->select();
            
            $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
            foreach($data as &$v){
                //添加部门名称
                $v['party_name']=getPartyInfo($v['party_no']);        
                 //添加参会人员名称
                $v['communist_name']= $communist_name_arr[$v['communist_no']];
                //添加附件地址
                $v['meeting_upload_url']=getUploadInfo($v['meeting_upload_no']);
                //添加签到地点
                $v["checked_addr"]=coordinateToAddr($v["checked_addr"]);
                //添加签到状态判断
                $result1=getIsChecked($v["meeting_no"],$communist_no);
                if($result1!="no"){
                    $v["checked_name"]="已签到";
                }else{
                    $v["checked_name"]="未签到";
                }           
            }
            if($data){
                return $data;
            }else{
                return null;
            }
    }
}
/**
 * @name:getMeetingCommunistList               
 * @desc：获取考勤人员表列表
 * @param：$meeting_no(会议编号)
 * @return：
 * @author：黄子正
 * @addtime:2017-05-10
 * @updatetime:2017-05-10
 * @version：V1.0.0
**/
function getMeetingCommunistList($meeting_no){
    $oa_meeting_communist = M('oa_meeting_communist');
    if(!empty($meeting_no)){
        $where['meeting_no'] = $meeting_no;
        $data=$oa_meeting_communist->where($where)->select();
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        foreach($data as &$v){
            //添加部门名称
            $v['party_name']=$party_name_arr[$v['party_no']];        
             //添加参会人员名称
            $v['communist_name']=$communist_name_arr[$v['communist_no']];
            //添加附件地址
            $v['meeting_upload']=getUploadInfo($v['meeting_upload_no']);
            $att=explode(",",$v['meeting_upload']);
            foreach($att as $v1){
                $v['meeting_upload_url'][]=$v1;
            }
            //添加签到地点
//            $v["checked_addr"]=coordinateToAddr($v["checked_addr"]);
            //添加签到状态判断
            $result1=getIsChecked($meeting_no,$v["communist_no"]);
            if($result1!="no"){
                $v["checked_name"]="已签到";
            }else{
                $v["checked_name"]="未签到";
            }           
        }
    }
    if($data){
        return $data;
    }else{
        return null;
    }
}
/**
 * @name:getMeetingInfo             
 * @desc：获取会议表某一字段及相应的参会人员信息(会议详情)
 * @param  $meeting_no(编号)   $field指定参数
 * @param  $is_handle int 是否对获取的数据进行处理
 * @return  null/array
 * @author：黄子正 刘丙涛
 * @addtime:2017-04-11
 * @updatetime:2017-12-19 删除多余信息
 * @version：V1.0.0
**/
function getMeetingInfo($meeting_no,$field='all',$is_handle){
    $oa_meeting = M('oa_meeting');
    if(!empty($meeting_no)){
        $where['meeting_no'] = $meeting_no;
        if($field == "all"){
            $data =$oa_meeting->where($where)->find();
            if($data){
                //添加部门名称
                $data['party_name'] = getPartyInfo($data['party_no']);
                 //添加主持人名称
                $data['meeting_host_name'] = getCommunistInfo($data['meeting_host'],"communist_name");
                if(empty($data['meeting_host_name'])){
                     $data['meeting_host_name']="无";
                 }
                //添加会议类型名称
                $data['meeting_type_name'] = getBdTypeInfo($data['meeting_type'],"meeting_type");
                if($data["status"]=="21"){
                    $data["status_name"]="已召开";
                }else{
                    $data["status_name"]="未召开";
                }
                //添加应到，实到人数
                $result = getMeetingCommunistCount($meeting_no);
                $data["should"]=$result["should"];
                $data["attended"]=$result["attended"];
                $data['communist_num'] = $result["should"];;
                //添加摄像头非空判断
                if(empty($data['meeting_camera'])){
                    $data['meeting_camera']="no";
                }
                if (empty($is_handle)){
                    //添加考勤机名称
                    $data['machine_name'] = getMachineInfo($data['machine_no'],"machine_name");
                    //添加会议状态字段

                    //添加参会人员信息
                    $data['communist_info']=getMeetingCommunistList($meeting_no);
                    //非空判断
                    if(empty($data["meeting_end_time"])){
                        $data["meeting_end_time"]="0";
                    }
                    if(empty($data["meeting_face_start_time"])){
                        $data["meeting_face_start_time"]="0";
                    }
                    if(empty($data["meeting_face_end_time"])){
                        $data["meeting_face_end_time"]="0";
                    }
                    if(empty($data["add_staff"])){
                        $data["add_staff"]="0";
                    }
                    if(empty($data["memo"])){
                        $data["memo"]="无";
                    }
                    if(empty($data["communist_info"])){
                        $data["communist_info"]=array();
                    }
                }
                return $data;
            }else{
                return null;
            }
        }else{
            $data = $oa_meeting->where($where)->field($field)->find();
            return $data;
        }
    }else{
        return null;
    }
}

/**
 * @name:getMeetingInfo             
 * @desc：获取会议表某一字段及相应的参会人员信息(会议详情)
 * @param  $meeting_no(编号)   $field指定参数
 * @param  $is_handle int 是否对获取的数据进行处理
 * @return  null/array
 * @author：黄子正 刘丙涛
 * @addtime:2017-04-11
 * @updatetime:2017-12-19 删除多余信息
 * @version：V1.0.0
**/
function getMeetingInfos($meeting_no,$field='all',$is_handle){
    $oa_meeting = M('oa_meeting');
    if(!empty($meeting_no)){
        $where['meeting_no'] = $meeting_no;
        if($field == "all"){
            $data =$oa_meeting->where($where)->find();
            if($data){
                //添加部门名称
                $data['party_name'] = getPartyInfo($data['party_no']);
                 //添加主持人名称
                $data['meeting_host_name'] = getCommunistInfo($data['meeting_host'],"communist_name");
                if(empty($data['meeting_host_name'])){
                     $data['meeting_host_name']="无";
                 }
                //添加会议类型名称
                $data['meeting_type_name'] = getBdTypeInfo($data['meeting_type'],"meeting_type");
                if($data["status"]=="21"){
                    $data["status_name"]="已召开";
                }else{
                    $data["status_name"]="未召开";
                }
                $data['communist_name'] = $data['meeting_host_name'];
                return $data;
            }else{
                return null;
            }
        }else{
            $data = $oa_meeting->where($where)->field($field)->find();
            return $data;
        }
    }else{
        return null;
    }
}
/**
 * @name:getMeetingList
 * @desc：获取会议列表(包含部门书记和党员数量)
 * @param：$party_no(部门编号) $communist_no(参会人员编号)
 * @return：
 * @author：王宗彬
 * @addtime:2017-11-30
 * @version：V1.0.0
**/
function getMeetingList($party_no,$meeting_name,$meeting_start_time,$meeting_type,$page,$pagesize){
    //实例化会议表对象
    $oa_meeting=M("oa_meeting");
    if(!empty($party_no)){
        // $party_nos = getPartyChildNos($party_no,'str');
        $map['party_no'] = array('in',$party_no);
    }
    if(!empty($meeting_name)){
        $map['meeting_name'] = array('like',"%$meeting_name%");
    }
    if(!empty($meeting_start_time)){
        $map['meeting_start_time'] = array('eq',$meeting_start_time);
    }
    if(!empty($meeting_type)){
        $map['meeting_type'] = array('in',$meeting_type);
    }
    $att_meeting_index['data']=$oa_meeting->where($map)->limit($page,$pagesize)->order("add_time desc")->select();
    $att_meeting_index['count']=$oa_meeting->where($map)->count();
    $att_meeting_index['code']=0;
    $att_meeting_index['msg']=0;
    return $att_meeting_index;
}
/**
 * @name:getMeetingweixinInfo
 * @desc：获取会议详情(包含部门书记和党员数量)
 * @param $communist_no(人员编号)
 * @param $meeting_no(会议编号)
 * @return array
 * @author：刘丙涛
 * @addtime:2017-07-27
 * @version：V1.0.0
 **/
function getMeetingweixinInfo($meeting_no,$communist_no){
    $oa_meeting= M('oa_meeting');
    $oa_meeting_communist = M('oa_meeting_communist');
    $where['meeting_no'] = $meeting_no;
    $meeting_info = $oa_meeting->where($where)->find();
    $meeting_info['host_name'] = getCommunistInfo($meeting_info['meeting_host']);//主持人
    $meeting_info['party_name'] = getPartyInfo($meeting_info['party_no']);//组织部门
    $meeting_info['meeting_start_time'] = date('Y-m-d',strtotime($meeting_info['meeting_start_time']));//会议时间
    $map['meeting_no'] = $meeting_info['meeting_no'];
    $meeting_info['communist_num'] = $oa_meeting_communist->where($map)->count();
    $meeting_info['checked'] = getIsChecked($meeting_no,$communist_no);
    if($meeting_info){
        return $meeting_info;
    }else{
        return null;
    }
}
/**
 * @name:getCommunistMeetingList
 * @desc：获取党员参与的会议列表
 * @param $communist_no(人员编号)
 * @param $meeting_type(会议类型)
 * @param $status(会议状态)
 * @return array
 * @author：刘丙涛
 * @updatetime 2071219
 * @addtime:2017-07-27
 * @version：V1.0.0
 **/
function getCommunistMeetingList($communist_no,$meeting_type,$status,$page,$pagesize){
    $oa_meeting= M('oa_meeting');
    $oa_meeting_communist = M('oa_meeting_communist');
    $map['communist_no']=$communist_no;
    $meeting_nos = $oa_meeting_communist->where($map)->getField("meeting_no",true);
    if (!empty($meeting_nos)){
        $meeting_nos = implode(',',$meeting_nos);
        if(!empty($meeting_nos)){
            $where['meeting_no'] = array('in',$meeting_nos);
        }
        if(!empty($status)){
            $where['status'] = array('in',$status);
        }
        //$where['_string'] = "meeting_no in($meeting_nos) and status in($status)";
        if (!empty($meeting_type)){
            $where['meeting_type'] = $meeting_type;
        }
        if (empty($pagesize)){
            $meeting_list = $oa_meeting->where($where)->order('add_time desc')->select();
        }else{
            $meeting_list = $oa_meeting->where($where)->limit($page,$pagesize)->order('add_time desc')->select();
        }
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

        $party_name_arr = M('ccp_party')->getField('party_no,party_name');
        $communist_name_arr = M('ccp_communist')->getField('communist_no,communist_name');
        foreach ($meeting_list as &$list){
            $list['host_name'] = getCommunistInfo($list['meeting_host']);
            $list['party_name'] = $party_name_arr[$list['party_no']];//组织部门
            $list['meeting_start_time'] = date('Y-m-d',strtotime($list['meeting_start_time']));//会议时间
            $map['meeting_no'] = $list['meeting_no'];
            $list['communist_num'] = $oa_meeting_communist->where($map)->count();
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
            $list['status'] = getStatusInfo('meeting_status',$list['status']);
            $list['meeting_type_val'] = getBdTypeInfo($list['meeting_type'], 'meeting_type','type_name');
        }
        if($meeting_list){
            return $meeting_list;
        }else{
            return null;
        }
    }else {
        return null;
    }
}
/**
 * @name:getMeetingSelect               
 * @desc：获取会议下拉列表
 * @param：$meeting_no:要选中的会议编号-选填
 * @return：
 * @author：黄子正
 * @addtime:2017-04-11
 * @updatetime:2017-05-09
 * @version：V1.0.0
**/
function getMeetingSelect($meeting_no){
    $oa_meeting=M('oa_meeting');
    $party_no_auth = session('party_no_auth');//取本级及下级组织
    $meeting_map['party_no'] = array('in',$party_no_auth);
    $type_list=$oa_meeting->where($meeting_map)->field('meeting_no,meeting_name')->select();
    $type_options="";
    foreach($type_list as &$type){
        $selected="";
        if($meeting_no==$type['meeting_no']){
            $selected="selected=true";
        }
        $type_options.="<option $selected value='".$type['meeting_no']."'>".$type['meeting_name']."</option>";
    }
    if(!empty($type_options)){
        return $type_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}
/**
 * @name:getAttrCommunistMeetingList
 * @desc：获取党员参会记录
 * @param：$satff_no(党员编号)   $field指定参数
 * @return：
 * @author：王桥元
 * @addtime:2017-04-25
 * @updatetime:
 * @version：V1.0.0
 **/
function getAttrCommunistMeetingList($satff_no,$field='all'){
    $communist_seeting = M("oa_meeting_communist");
    $oa_meeting= M('oa_meeting');
    if(!empty($satff_no)){
        $where['communist_no'] =  $satff_no;
        $meeting_data = $communist_seeting->where($where)->select();
        $meeting_list = array();
        foreach ($meeting_data as &$meeting){
            if($field == "all"){
                $meeting_list['meeting_list'] = getMeetingList("",$meeting['communist_no']);
            }else{
                $meeting_list['meeting_list'] = getMeetingList("",$meeting['communist_no']);
            }
        }
        if(!empty($meeting_list)){
            return $meeting_list;
        }else{
            return null;
        }
    }else{
        return null;
    }
}

/**
 * @name:sendAttMeetingCommunist
 * @desc: 会议记录
 * @param：
 * @return：
 * @author：黄子正
 * @addtime:2017-06-01
 * @updatetime:2017-06-01
 * @version：V1.0.0
 **/
function getMeetingminutesList($party_no,$meeting_minutes_type = '1',$meeting_minutes_title,$add_staff,$page,$pagesize){
    $oa_meeting_minutes = M('oa_meeting_minutes');
    $map['meeting_minutes_type'] = array('eq',$meeting_minutes_type);
	if(!empty($party_no)){// 部门
		$party_no = getPartyChildNos($party_no);
		$map['party_no'] = array('in',"$party_no");
	}else{
		$map['party_no'] = array('in',session('party_no_auth'));
	}
    //$meeting_minutes_title = I('get.meeting_minutes_title');
    if(!empty($meeting_minutes_title)){
        $map['meeting_minutes_title'] = array('like',"%$meeting_minutes_title%");
    }
    if(!empty($add_staff)){
        $map['add_staff'] = array('like',"%".$add_staff."%");
    }
    $data['data'] = $oa_meeting_minutes->where($map)->limit($page,$pagesize)->order("add_time desc")->select();
    $data['count'] = $oa_meeting_minutes->where($map)->count();
    $data['code'] = 0;
    $data['msg'] = 0;
    return $data;

}

/**
 * @name:getCommunistMeetingInfo
 * @desc：获取党员参会记录
 * @param $satff_no(党员编号)   $field指定参数
 * @return array string
 * @author：刘丙涛
 * @addtime:2017-07-31
 * @updatetime:
 * @version：V1.0.0
 **/
function getCommunistMeetingInfo($meeting_no,$satff_no,$field='all'){
    $communist_seeting = M("oa_meeting_communist");
    if(!empty($satff_no)){
        $where['communist_no'] = $satff_no;
        $where['meeting_no'] = $meeting_no;
        $meeting_data = $communist_seeting->where($where)->find();
        if($field != "all"){
            $meeting_data = $meeting_data[$field];
        }
        if(!empty($meeting_data)){
            return $meeting_data;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
 
 
/**
 * 获取日志列表
 * 
 * @name getWorklogList()
 * @param $communist_no 员工编号            
 * @param $audit_man 审核人            
 * @param $worklog_type 总结类型(01:每日02:每周 03:每月 04:会议)
 * @param $start_date 开始日期            
 * @param $end_date 结束日期            
 * @param $keyword 标题及内容   关键字          
 * @param $worklog_status 状态            
 * @param $party_no 部门            
 * @param $is_child 是否显示子日志            
 * @param $is_page 是否启用分页0不启用 1启用 （默认0）
 * @param $page 页数 默认显示1
 * @param $count 条数 默认10
 * @return array
 * @author 杨陆海
 *         @time 2016-08-22 17:24
 */
function  getWorklogList($staff_no, $audit_man, $worklog_type, $start_date, $end_date, $keyword, $worklog_status, $dept_no, $is_child = '1', $is_page = '0')
{
    $db_worklog = M('oa_worklog');
    $worklog_list = array();
    $where = '1 = 1';
    // 总结类型(01:每日 02:每周 03:每月 04:会议)
    if (! empty($worklog_type)) {
        $where .= " and  worklog_type='$worklog_type'";
    }
    // 标题及内容
    if (! empty($keyword)) {
        $where .= " and (worklog_title like'%" . $keyword . "%' or worklog_summary like'%" . $keyword . "%' )";
    }
    // 状态
    if ($worklog_status != '' || $worklog_status == '0') {
        $where .= "  and `status`='$worklog_status'";
    }
    // 添加人
    if (! empty($staff_no)) {
        $where .= " and worklog_staff = '$staff_no'";
        // $where .= " and worklog='$staff_no' or worklog_audit_man='$audit_man'";
    }
    // 时间
    if (! empty($start_date) && !empty($end_date)) {
        $where .= " and worklog_date >='$start_date' and worklog_date <='$end_date'";
    }
    // 审核人
    if (! empty($audit_man)) {
        $where .= " and worklog_audit_man='$audit_man'";
    }
    // 部门编号
//    if (! empty($dept_no)) {
//        if ($is_child == '1') {
//            $party_list = getPartyChildNos($dept_no, 'str') . $dept_no;
//            $where .= " and party_no in($party_list)";
//        } else {
//            $where .= " and party_no='$dept_no'";
//        }
//    }
    // 是否分页
    if ($is_page == '0') {
        $sql = "SELECT * from(select w.*, s.staff_dept_no from sp_oa_worklog as w, sp_hr_staff as s
        where w.worklog_staff=s.staff_no )t where $where  ORDER BY add_time desc";
    } else {
        $sql = "SELECT * from(select w.*, s.staff_dept_no from sp_oa_worklog as w, sp_hr_staff as s
        where w.worklog_staff=s.staff_no ORDER BY add_time desc)t where $where ";
    }
    $Model = new \Think\Model();
    $worklog_list = $Model->query($sql);
    return $worklog_list;
}



/**
 * 获取日志信息
 * @name getWorklogInfo()
 * @param $staff_no 员工编号
 *        worklog_id#id
 *        worklog_type#类型
 *        worklog_date#时间
 *        status#状态
 * @return array
 * @author 杨陆海
 *         @time 2016-08-22 17:24
 */
function getWorklogInfo( $worklog_id,$worklog_type,$worklog_date,$status,$communist_no){
    $db_worklog = M('oa_worklog');
    $where = '1=1';
    if (! empty($worklog_id)) {
        $where['worklog_id'] = $worklog_id;
    }
    // 总结类型(01:每日 02:每周 03:每月 04:会议)
    if (! empty($worklog_type)) {
        $where['worklog_type'] = $worklog_type;
    }
    // 状态
    if ($status!= '') {
        $where['status'] = $status;
    }
    // 添加人
    if (! empty($communist_no)) {
        $where['add_staff'] = $communist_no;
    }
    try {
        $worklog_info= $db_worklog->where($where)->find();
    } catch (Exception $e) {
        $worklog_info = null;
    }
    return $worklog_info;
}

/**
 * *************************** 工作日志结束 ***********************
 */
/**
 * *************************** 工作计划开始 ***********************
 */
///**
// * 获取工作计划列表
// * @name getWorkplanList()
// * @param $executor_man 执行人
// * @param $arranger_man 安排人
// * @param $status 工作计划状态
// * @param $start_time 开始时间
// * @param $end_time 结束时间
// * @param $workplan_info 模糊查询（计划标题，内容）
// * @param $party_no 部门
// * @param $is_child 是否获取子集
// *            (默认 1)
// * @param $date_type 时间类型(1.开始时间，2结束时间)
// * @return array
// * @author 杨陆海
// *         @time 2016-07-30 10:30
// */
//function getWorkplanList($executor_man, $arranger_man, $status, $start_time, $end_time, $workplan_info, $party_no, $date_type = '0', $is_child = '1', $is_page = '0', $page = '1', $count = '10')
//{
//    $where = "1=1";
//    // 执行情况
//    if (! empty($status)) {
//        $plan_status = "  and status in($status) ";
//    }
//    if (! empty($start_time) && ! empty($end_time)) {
//        if ($start_time == $end_time) {
//            $end_time = $end_time . '23:59:59';
//        }
//        if ($date_type == '1') {
//            $date = " and (workplan_start_time BETWEEN '$start_time' AND '$end_time')";
//        }
//        if ($date_type == '2') {
//            // 结束时间
//            $date = " and (workplan_end_time BETWEEN '$start_time' AND '$end_time')";
//        }
//    }
//    if (! empty($workplan_info)) {
//        $where .= " and (workplan_title like '%" . $workplan_info . "%' or workplan_content like '%" . $workplan_info . "%' )";
//    }
//    // 部门编号
//    if (! empty($party_no)) {
//        if ($is_child == '1') {
//            $party_list = getPartyChildNos($party_no, 'str') . $party_no;
//            $party_list = implode(',', array_unique(explode(',', $party_list)));
//            $communist_list = getCommunistList($party_list);
//            $communist_list = implode(',', array_unique(explode(',', $communist_list)));
//            $communist_list = rtrim($communist_list, ",");
//        } else {
//            $communist_list = getCommunistList($party_no, 'str', 0);
//        }
//        // 查询我自己的
//        if (! empty($executor_man)) {
//            $where .= " and workplan_executor_man in($executor_man) ";
//        }
//        // 安排人
//        if (! empty($arranger_man)) {
//            $where .= " and  workplan_arranger_man in($arranger_man) and workplan_executor_man <> $arranger_man";
//        }
//        if (empty($executor_man) && empty($arranger_man)) {
//            $where .= " and workplan_executor_man in($communist_list) or workplan_arranger_man in($communist_list) ";
//        }
//    } else {
//        // 执行人
//        if (! empty($executor_man)) {
//            $where .= " and workplan_executor_man in ('$executor_man') ";
//        }
//        // 安排人
//        if (! empty($arranger_man)) {
////             $where .= " and  workplan_arranger_man in($arranger_man) and workplan_executor_man <> $arranger_man";
//            $where .= " and  workplan_arranger_man in($arranger_man)";//2017-10-11添加（工作计划安排人和执行人可以说同一个人）
//        }
//    }
//    if ($is_page == '1') {
//        $page = ($page - 1) * $count;
//        $sql = " select * from ( select * from sp_oa_workplan where $where  order by  status ASC ,workplan_expectstart_time asc)t where 1=1  $plan_status $date LIMIT $page,$count";
//    } else {
//        $sql = " select * from ( select * from sp_oa_workplan where $where  order by  status ASC,workplan_expectstart_time asc )t where 1=1  $plan_status $date  ";
//    }
//    try {
//        $Model = new \Think\Model();
//        $workplanlist = array();
//        $workplanlist = $Model->query($sql);
//        foreach ($workplanlist as & $work) {
//            $work['status_name'] = getStatusName('workplan_status', $work['status']);
//            $work['workplan_expectend_time']=getFormatDate($work['workplan_expectend_time'],'Y-m-d');
//            $work['workplan_expectstart_time']=getFormatDate($work['workplan_expectstart_time'],'Y-m-d');
//            $work['workplan_end_time']=getFormatDate($work['workplan_end_time'],'Y-m-d');
//        }
//    } catch (Exception $e) {
//        return $workplanlist;
//    }
//    return $workplanlist;
//}

/**
 * 获取工作计划列表
 *
 * @name getWorkplanList()
 * @param $executor_man 执行人
 * @param $arranger_man 安排人/审核人
 * @param $status 工作计划状态
 * @param $start_time 开始时间
 * @param $end_time 结束时间
 * @param $workplan_info 模糊查询（计划标题，内容）
 * @param $dept_no 部门
 * @param $is_child 是否获取子集
 *            (默认 1)
 * @param $date_type 时间类型(1.开始时间，2结束时间)
 * @return array
 * @author 杨陆海
 *         @time 2016-07-30 10:30
 */
function getWorkplanList($executor_man, $arranger_man, $status, $start_time, $end_time, $workplan_info, $dept_no, $date_type = '0', $is_child = '1', $is_page = '0', $page = '1', $count = '10')
{
    $where = "1=1";
    // 执行情况
    if (! empty($status)) {
        $where .= "  and status in($status) ";
    }
	if ($start_time != '' && $end_time != '') {
        $start_time = $start_time.' 00:00:00';
        $end_time = $end_time.' 23:59:59';
        if ($date_type == '1') {
            $where .= " and (workplan_start_time BETWEEN '$start_time' AND '$end_time')";
        }
        if ($date_type == '2') {
            // 结束时间
            $where .= " and (workplan_end_time BETWEEN '$start_time' AND '$end_time')";
        }
    }

    if (! empty($workplan_info)) {
        $where .= " and (workplan_title like '%" . $workplan_info . "%' or workplan_content like '%" . $workplan_info . "%' )";
    }

    // 部门编号
    if (! empty($dept_no)) {
        if ($is_child == '1') {
            $dept_list = getDeptList($dept_no, 'str') . $dept_no;
            $dept_list = implode(',', array_unique(explode(',', $dept_list)));
            $staff_list = getStaffList($dept_list, '');
            $staff_list = implode(',', array_unique(explode(',', $staff_list)));
            $staff_list = rtrim($staff_list, ",");
        } else {
            $staff_list = getStaffList($dept_no, 'str', 0);
        }
        if (empty($executor_man) && empty($arranger_man)) {
            $where .= " and workplan_executor_man in($staff_list) or workplan_arranger_man in($staff_list) ";
        }
    }
    // 查询我自己的
    if (! empty($executor_man)) {
        $where .= " and workplan_executor_man in($executor_man) ";
    }
    // 安排人
    if (! empty($arranger_man)) {
        $where .= " and  workplan_arranger_man in($arranger_man)";
    }
	if ($is_page == '1') {
        $sql = " select * from sp_oa_workplan where $where  order by  status ASC ,workplan_expectstart_time asc LIMIT $page,$count";
    } else {
        $sql = " select * from sp_oa_workplan where $where  order by  status ASC,workplan_expectstart_time asc";
    }
    try {
        $Model = new \Think\Model();
        $workplanlist = array();
        $workplanlist = $Model->query($sql);

        foreach ($workplanlist as & $work) {
			$work['where'] = $where;
            $work['status_name'] = getStatusName('workplan_status', $work['status']);
            $work['workplan_expectend_time']=getFormatDate($work['workplan_expectend_time'],'Y-m-d');
            $work['workplan_expectstart_time']=getFormatDate($work['workplan_expectstart_time'],'Y-m-d');
            $work['workplan_end_time']=getFormatDate($work['workplan_end_time'],'Y-m-d');
        }

        // $workplanlist = $oa_workplan->where($where)->order('status')->select();
    } catch (Exception $e) {
        return $workplanlist;
    }
    return $workplanlist;
}
/**
 * 获取工作计划
 * @name getWorkplanInfo()
 * @param $workplan_id id            
 * @param $field='workplan_title'                       
 * @return array
 * @author 杨陆海
 *         @time 2016-07-30 10:30
 */
function getWorkplanInfo($workplan_id, $field='workplan_title')
{
    $oa_workplan=M('oa_workplan');
    $where['workplan_id'] = $workplan_id;
    if($field=='all')
    {
        $workplan_info=$oa_workplan->where($where)->find();
        $workplan_info['status_name'] = getStatusName('workplan_status', $workplan_info['status']);
        $workplan_info['workplan_expectend_time']=getFormatDate($workplan_info['workplan_expectend_time'],'Y-m-d');
        $workplan_info['workplan_expectstart_time']=getFormatDate($workplan_info['workplan_expectstart_time'],'Y-m-d');
        $workplan_info['workplan_end_time']=getFormatDate($workplan_info['workplan_end_time'],'Y-m-d');
    }else{
        $workplan_info=$oa_workplan->where($where)->getField($field);
    }
    return $workplan_info;
}
/**
 * 保存工作计划信息
 * @name saveWorkplan()
 * @param $workplan_title 计划标题            
 * @param $workplan_content 计划内容            
 * @param $executor_man 执行人            
 * @param $arranger_man 安排人            
 * @param $expectstart_time 预计开始时间            
 * @param $expectend_time 预计结束时间            
 * @param $communist_no 员工编号            
 * @param $file_id 附件            
 * @return bool
 * @author 杨陆海
 *         @time 2016-07-30 10:30
 */
function saveWorkplan($workplan_title, $workplan_content, $executor_man, $arranger_man, $expectstart_time, $expectend_time, $communist_no, $file_id, $memo)
{
    $oa_workplan = M('oa_workplan');
    if (! empty($workplan_title) && ! empty($workplan_content) && ! empty($communist_no)) {
        $workplan['workplan_title'] = $workplan_title;
        $workplan['workplan_content'] = $workplan_content;
        $workplan['workplan_executor_man'] = $executor_man;
        $workplan['workplan_expectend_time'] = $expectend_time;
        $workplan['workplan_arranger_man'] = $arranger_man;
        $workplan['workplan_expectstart_time']=$expectstart_time;
        $workplan['add_staff']=$communist_no;
        $workplan['add_time']=date("Y-m-d H:i:s");
        $workplan['status']='11';
        $workplan['workplan_attach']=$file_id;
        $workplan['memo']=$memo;
        try {
            $flag=$oa_workplan->add($workplan);
        } catch (Exception $e) {
            return null;
        }
    }
    if($flag)
    {
        return $flag;
    }else {
        return null;
    }
}
/**
 * @name  saveWorkplanLog()
 * @desc 保存工作计划总结
 * @param   $workplan_id 计划ID
 * @param   $planlog_summary 计划内容
 * @param   $planlog_communist 计划总结人
 * @return  $planlog_id 计划总结ID
 * @author 杨陆海
 * @time   2016-07-30 10:30
 */
function saveWorkplanLog($workplan_id,$planlog_summary,$planlog_communist,$status,$memo)
{
  if(empty($workplan_id)|| empty($planlog_summary) || empty($planlog_communist))
  {
      return null; 
  }
  $workplan_log=M('oa_workplan_log'); 
  $data['workplan_id']=$workplan_id;
  $data['planlog_summary'] = $planlog_summary;
  $data['add_time'] = date("Y-m-d H:i:s");
  $data['update_time']= date("Y-m-d H:i:s");
  $data['add_staff'] = $planlog_communist;
  $data['planlog_communist'] = $planlog_communist;
  $data['status'] = $status;
  $data['memo']=$memo;
  try {
      $oper_res = $workplan_log->add($data);
  } catch (Exception $e) {
      return $e;
  }
  return $oper_res;
}

/**
 * @name  getWorkplanLogList()
 * @desc 获取工作计划总结
 * @param   $date 计划总结时间
 * @param   $staff_no 计划总结人
* @return 
 * @author 杨陆海
 * @time   2016-07-30 10:30
 */
function getWorkplanLogList($workplan_id,$status,$start_date,$end_date,$staff_no)
{
    $workplan_log=M('oa_workplan_log');
    if(!empty($workplan_id))
    {
        $where['workplan_id'] = $workplan_id;
    }
    if(!empty($start_date) && !empty($end_date))
    {
        if($start_date==$end_date)
        {
            $where[''] = array('like','%'.$start_date.'%');
        }else{
            $where['add_time'] = array(array('EGT',$start_date),array('ELT',$end_date)) ;
        }
    }
    if(!empty($status))
    {
         $where['status'] = array('in',$status);
    }
    if(!empty($staff_no))
    {
        $where['add_stafft'] = $staff_no;
    }
    try {
        $planlog_list=$workplan_log->where($where)->order('add_time desc')->select();
    }
    catch (Exception $e) {
        return $e;
    }
    return $planlog_list;
}

/***************************** 工作计划结束 ************************/


/***************************** 公告开始 ************************/

 /**
 * @name:getNoticeInfo               
 * @desc：获取公告名称
 * @param：公告id $notice_id
*  $field  字段名 传入此参数时、查询此字段的值
 * @return：公告名称/整条数据
 * @author：王世超
 * @addtime:2016-08-23
 * @version：V1.0.0
**/
function getNoticeInfo($notice_id,$field='all'){
    if(!empty($notice_id)){
        $db_notice=M('oa_notice');
        $where['notice_id'] = $notice_id;
        $notice_info=$db_notice->where($where)->find();
        if($field!='all')
        {
            $notice_info=$notice_info[$field];
        }
    }
    if($notice_info){
        return $notice_info;
    }else{
        return null;
    }
}
  /**
 * @name:getNoticeList               
 * @desc：获取公告列表
 * @param：$party_no 部门编号    $communist_no 添加人编号      $page 页数   $pagesize显示条数
 * @return：array
 * @author：王世超--李祥超--王桥元
 * @addtime:2016-08-24
 * @Updatatime：2016-09-26----20170613
 * @version：V1.0.0
**/
function getNoticeList($party_no,$communist_no="",$page="",$pagesize=""){
    $db_notice=M('oa_notice');
    if(empty($communist_no)){
        $communist_no = session('staff_no');
    }
    if(empty($page)){
        $page = '1';
    }
    if(empty($pagesize)){
        $pagesize = '10';
    }
    $start = ($page - 1) * $pagesize;
    $end = $page * $pagesize;
    
    if(empty($party_no)){
        $notice_list =$db_notice->where('status=1 ')->limit($start,$end)->order('add_time desc')->select();
    }else{
        $notice_list =$db_notice->query("select * from sp_oa_notice where status=1 and FIND_IN_SET('$party_no',party_no) or add_staff = $communist_no ORDER BY add_time desc limit $start,$end");
    }
    
    
    foreach($notice_list as &$list){
        $notice_info=getNoticeLogInfo($list['notice_id'],$communist_no);
        if($notice_info){
            $list['is_read'] = "1";
        }else{
            $list['is_read'] = "0";
        }
    }
    return $notice_list;
}
  /**
 * @name:getNoticeListSetList               
 * @desc：获取公告列表
 * @param： $communist_no 添加人编号      
            $notice_content 标题内容   
            $start，开始时间 $end 结束时间 
            $is_read  是否已读
 * @return：array
 * @author：王世超--李祥超--王桥元
 * @addtime:2016-08-24
 * @Updatatime：2016-09-26----20170613
 * @version：V1.0.0
**/
 function getNoticeListSetList($staff_no,$notice_content,$start,$end,$is_read)
{
        $db_notice=M('oa_notice');
        $oa_notice_viewrecord = M('oa_notice_log');
        //标题及内容
        if(!empty($notice_content))
        {
            $notice_map['notice_content'] = array('like','%'.$notice_content.'%');
            $notice_map['notice_title'] = array('like','%'.$notice_content.'%');
            $notice_map['_logic'] = 'or';
        } 
        if(!empty($notice_map)){
            $map['_complex'] = $notice_map;
        }
        $map['status'] = 1;
        if(!empty($staff_no))
        {
            $map['add_staff'] = $staff_no;
        }
        if(!empty($start) && !empty($end))
        {   
            $end=$end." 23:59:59";
            $map['update_time']  = array('between',array($start,$end));
        }
        $viewrecord_list = M('oa_notice')->where($map)->field('notice_id,notice_title,add_staff,update_time')->select();
      return $viewrecord_list;
} 

/**
 * @name getNoticeLogInfo
 * @desc：获取指定人员已读公告列表
 * @param：公告id $notice_id  人员编号 $communist_no
 * @return：公告名称/整条数据
 * @author：王世超
 * @addtime:2016-08-23
 * @version：V1.0.0
 **/
function getNoticeLogInfo($notice_id,$communist_no){
    $db_notice_log=M('oa_notice_log');
    $where = "1=1";
    if($notice_id){
        $where['notice_id'] = $notice_id;
    }
    if($communist_no){
        $where['communist_no'] = $communist_no;
    }
    $notice_info=$db_notice_log->where($where)->find();
    if($notice_info){
        return $notice_info;
    }else{
        return null;
    }
}

/***************************** 公告结束 ************************/
/***************************** 工作审批方法开始 ************************/

/**
 * @name    getOaApprovalTplSelect()
 * @desc    获取审批模板列表
 * @param   当前模板的编号   $selected_no（支持多个）
 * @return  带选中状态的模板下拉列表（HTML代码）
 * @author  靳邦龙
 * @version 版本 V1.0.1
 * @addtime 2016-04-28
 */
// function getOaApprovalTplSelect($selected_no){
//     $db_tpl=M('oa_approval_template');
//     $template_list=$db_template->field('template_no,template_name')->select();
//     $template_options="";
//  $select_arr = strToArr($selected_no);
//     foreach($template_list as &$template){
//      $selected="";
//      foreach($select_arr as $arr){
//          if($arr==$template['template_no']){
//              $selected="selected=true";
//          }
//      }
//      $template_options.="<option $selected value='".$template['template_no']."'>".$template['template_name']."</option>";
//     }
//     if(!empty($template_options)){
//         return $template_options;
//     }else{
//         return "<option value=''>无数据</option>";
//     }
// }
/********************************办公相关基础层方法 结束*************************************/


/***************************** 必做任务开始 ************************/
/**
 * @name:getWilldoInfo
 * @desc：获取必做任务名称
 * @param：$willdo_id 必做任务id
 * $field  字段名 传入此参数时、查询此字段的值
 * @return：必做任务名称/整条数据
 * @author：王世超
 * @addtime:2016-08-11
 * @version：V1.0.0
 **/
function getWilldoInfo($willdo_id,$field='willdo_title'){
    if(!empty($willdo_id)){
        $oa_willdo = M('oa_willdo');
        $where['willdo_id'] = $willdo_id;
        $willdo_fieldinfo=$oa_willdo->where($where)->find();
        if($field!='all')
        {
            $willdo_fieldinfo=$willdo_fieldinfo[$field];
        }else {
            $willdo_fieldinfo['willdo_time'] = getFormatDate($willdo_fieldinfo['willdo_start_time'],"H:i") . "--" . getFormatDate($willdo['willdo_end_time'],"H:i");

            //任务周期
            if($willdo_fieldinfo['willdo_cycle']==01)//每日
            {
                $willdo_fieldinfo['willdo_cyda'] = "每日";

            }
            if ($willdo_fieldinfo['willdo_cycle']==02)//每周
            {
                switch($willdo_fieldinfo['willdo_operdate'])
                {
                    case "1":
                        $willdo_fieldinfo['willdo_cyda'] = "每周(一)";
                        break;
                    case '2':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(二)";
                        break;
                    case '3':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(三)";
                        break;
                    case '4':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(四)";
                        break;
                    case '5':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(五)";
                        break;
                    case '6':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(六)";
                        break;
                    case '7':
                        $willdo_fieldinfo['willdo_cyda'] = "每周(日)";
                        break;
                    default:
                        $willdo_fieldinfo['willdo_cyda'] = "每周";
                }

            }
            if ($willdo_fieldinfo['willdo_cycle']==03)//每月
            {
                $willdo_fieldinfo['willdo_cyda'] = "每月(" . $willdo_fieldinfo['willdo_operdate'].")号";

            }
            if ($willdo_fieldinfo['willdo_cycle']==04)//每年
            {
                $willdo_operdate=$willdo_fieldinfo['willdo_operdate'];
                $month=(int)substr($willdo_operdate,0,2);
                $day=(int)substr($willdo_operdate,2);
                $willdo_fieldinfo['willdo_cyda'] = "每年(" . $month.")月(".$day.")号";

            }
            //时间
            if($willdo_fieldinfo['is_alert']=="1")
            {

                $alert_date=$willdo_fieldinfo['alert_date'];

                $arralert=explode(',',$alert_date);
                $alert_info=null;
                //提醒时间 1：半小时，2：2小时 ，3：24小时，4：36小时，5：48小时
                foreach ($arralert as $s){

                    switch ($s)
                    {
                        case '1':
                            $alert_info.="半小时/";
                            break;
                        case '2':
                            $alert_info.="2小时/";
                            break;
                        case '3':
                            $alert_info.="24小时/";
                            break;
                        case '4':
                            $alert_info.="36小时/";
                            break;
                        case '5':
                            $alert_info.="48小时/";
                            break;
                    }
                }
                if($alert_info!=null)
                {

                    $alert_info=substr($alert_info,0,strlen($alert_info)-1);

                }
                $willdo_fieldinfo['alert_info']=$alert_info;
            }
        }

    }
    if($willdo_fieldinfo){
        return $willdo_fieldinfo;
    }else{
        return null;
    }
}
/**
 * @name:getWilldoList
 * @desc：获取必做任务列表
 * @param：$communist_no 员工编号
 * $willdo_cycle 任务周期
 * $choosedate 选择日期
 * $willdo_content 搜索内容
 * $start_time 开始时间
 * $end_time 结束时间
 * $status 要查询状态值为多少的
 * @return：array
 * @author：杨陆海--王世超
 * @addtime:2016-07-30 10:30
 * @updatetime:2016-08-24
 * @version：V1.0.1
 **/

function getWilldoList($staff_no,$willdo_cycle,$choosedate,$willdo_content,$start_time,$end_time,$status,$page ='',$limit='')

{
    $db_willdo = M('oa_willdo');

    //任务周期：
    if(!empty($willdo_cycle))
    {
        $where['willdo_cycle'] = (int)$willdo_cycle;
    }
    //执行人：
    if(!empty($staff_no))
    {
        $where['add_staff'] = $staff_no;
    }
    //选择日期：
    $time=strtotime($choosedate);
    if(!empty($choosedate))
    {
        $week=date("N",$time);
        $year=getFormatDate($choosedate,"md");
        $month=getFormatDate($choosedate,"d");
        $where['_string'] ="  ( willdo_cycle=01 or willdo_operdate='$year' or willdo_operdate='$month' or willdo_operdate='$week')";
    }
    //标题及内容
    if(!empty($willdo_content))
    {
        $where['willdo_content'] = array(array('like','%'.$willdo_content.'%'),array('like','%'.$willdo_content.'%'),'or');
    }
    if(!empty($start_time) && !empty($end_time))
    {
        $start_time = $start_time.":00";
        $end_time = $end_time.":00";
        $where['_string'] = "willdo_start_time >= '$start_time' and willdo_end_time <='$end_time'";
    }

    if($status!="")
    {
        $where['status'] = array('in',$status);
    }
	if($page != '' && $limit != ''){
		$willdo_list = $db_willdo->where($where)->order("add_time desc")->limit(($page-1)*$limit,$limit)->select();
	}else{
		$willdo_list = $db_willdo->where($where)->order("add_time desc")->select();
	}
    foreach ($willdo_list as &$willdo)
    {
		$willdo['where'] = $where;
        $willdo['willdo_time'] = getFormatDate($willdo['willdo_start_time'],"H:i") . "--" . getFormatDate($willdo['willdo_end_time'],"H:i");
        $willdo['add_time'] = getFormatDate($willdo['add_time'],"Y-m-d H:i");
        //任务周期
        if($willdo['willdo_cycle']==01)//每日
        {
            $willdo['willdo_cyda'] = "每日";

        }
        if ($willdo['willdo_cycle']==02)//每周
        {
            switch($willdo['willdo_operdate'])
            {
                case "1":
                    $willdo['willdo_cyda'] = "每周(一)";
                    break;
                case '2':
                    $willdo['willdo_cyda'] = "每周(二)";
                    break;
                case '3':
                    $willdo['willdo_cyda'] = "每周(三)";
                    break;
                case '4':
                    $willdo['willdo_cyda'] = "每周(四)";
                    break;
                case '5':
                    $willdo['willdo_cyda'] = "每周(五)";
                    break;
                case '6':
                    $willdo['willdo_cyda'] = "每周(六)";
                    break;
                case '7':
                    $willdo['willdo_cyda'] = "每周(日)";
                    break;
                default:
                    $willdo['willdo_cyda'] = "每周";
            }

        }
        if ($willdo['willdo_cycle']==03)//每月
        {
            $willdo['willdo_cyda'] = "每月(" . $willdo['willdo_operdate'].")号";

        }
        if ($willdo['willdo_cycle']==04)//每年
        {
            $willdo_operdate=$willdo['willdo_operdate'];
            $month=(int)substr($willdo_operdate,0,2);
            $day=(int)substr($willdo_operdate,2);
            $willdo['willdo_cyda'] = "每年(" . $month.")月(".$day.")号";

        }
        //时间
        if($willdo['is_alert']=="1")
        {
            // $willdo['is_alert']='是';
            $alert_date=$willdo['alert_date'];

            $arralert=explode(',',$alert_date);
            $alert_info=null;
            //提醒时间 1：半小时，2：2小时 ，3：24小时，4：36小时，5：48小时
            foreach ($arralert as $s){
                switch ($s)
                {
                    case '1':
                        $alert_info.="半小时/";
                        break;
                    case '2':
                        $alert_info.="2小时/";
                        break;
                    case '3':
                        $alert_info.="24小时/";
                        break;
                    case '4':
                        $alert_info.="36小时/";
                        break;
                    case '5':
                        $alert_info.="48小时/";
                        break;
                }
            }
            if($alert_info!=null)
            {

                $alert_info=substr($alert_info,0,strlen($alert_info)-1);

            }
            $willdo['alert_info']=$alert_info;
        }
    }

    return $willdo_list;
}


/**
 * @name:getWilldoLogInfo
 * @desc：获取必做任务的日志状态
 * @param：$willdo_id 员工编号
 * $choosedate 选择日期
 * $communist_no 搜索内容
 * @return：array
 * @author：杨陆海--王世超
 * @addtime:2016-10-13 10:30
 * @version：V1.0.1
 **/
function getWilldoLogInfo($willdo_id,$choosedate,$communist_no,$status)
{

    $willdo_log = M('oa_willdo_log');
    $where['_string'] ="willdo_id='$willdo_id' and willdolog_operdate like'%".$choosedate."%'  and add_staff='$communist_no' and status='$status'";
    $willdo_log=$willdo_log->where($where)->find();

    return $willdo_log;
}

/**
 * @name:getWilldoLogList
 * @desc：获取必做任务的日志列表
 * @param：   $willdo_id 员工编号
 *          start_date#开始时间
 *          end_date#结束时间
 *          communist_no#工号
 *          status#状态
 * @return：array
 * @author：杨陆海--王世超
 * @addtime:2016-10-13 10:30
 * @version：V1.0.1
 **/
function getWilldoLogList($willdo_id,$start_date,$end_date,$communist_no,$status)
{
    $willdo_log = M('oa_willdo_log');

    if(!empty($willdo_id))
    {
        $where['willdo_id'] = $willdo_id;
    }
    if(!empty($start_date) && !empty($end_date))
    {
        if($start_date==$end_date)
        {
            $map['willdolog_operdate'] = array('like',"%$start_date%");

        }else{
            $where['_string'] = " and willdolog_operdate BETWEEN '".$start_date."' and  '".$end_date."'"  ;

        }

    }

    if(!empty($communist_no))
    {
        $where['add_staff'] = $communist_no;
    }
    if(!empty($status))
    {
        $where['status'] = array('in',$status);
    }
    $willdo_log_list=$willdo_log->where($where)->order(" willdolog_operdate desc ")->select();
    return $willdo_log_list;
}

/**
 * @name:saveWilldoLog
 * @desc：保存必做任务的日志
 * @param：$willdo_id id
 *         $willdolog_summary 总结
 *         $status 状态
 *         $staff_no 工号
 * @return：true/false
 * @author：杨陆海
 * @addtime:2016-10-13 10:30
 * @version：V1.0.1
 **/
function saveWilldoLog($willdo_id,$willdolog_summary,$status,$staff_no)
{
    $willdo_log=M('oa_willdo_log');
    $data['willdo_id']=$willdo_id;
    $data['willdolog_summary']=$willdolog_summary;
    $data['willdolog_operdate']=date("Y-m-d H:i:s");
    $data['staff_no'] = $staff_no;
    $data['add_staff'] = $staff_no;
    $data['status']=$status;
    $data['add_time'] = date("Y-m-d H:i:s");
    $data['update_time']= date("Y-m-d H:i:s");
    try {
        $willdo_flag=$willdo_log->add($data);

    } catch (Exception $e) {
        return null;
    }

    return $willdo_flag;
}
/***************************** 必做任务结束 ************************/

/**
 * @name:getOaNotesList
 * @desc：获取备忘录列表
 * @param：$staff_no 员工编号
 * $notes_classify 备忘录分类      （新增）
 * $notes_content 搜索内容
 * $start 开始时间
 * $end 结束时间
 * $is_timedesc 接口按时间分类
 * @return：array
 * @author：王世超-李祥超
 * @addtime:2016-08-23
 * @updatatime:2016-09-21
 * @version：V1.0.0
 **/
function getOaNotesList($staff_no,$notes_content,$start,$end,$is_timedesc,$notes_classify,$page='',$limit="")
{
    $db_notes=M('oa_notes');
    $where['add_staff'] = $staff_no;

    //分类 2016-09-21新增
    if(!empty($notes_classify)){
        $where['notes_type'] = $notes_classify;
    }
    //标题及内容
    if(!empty($notes_content))
    {
        $where['_string'] =" (notes_title like'%".$notes_content."%' or notes_content like'%".$notes_content."%' )";
    }
    if($is_timedesc==1)
    {
        $where['update_time'] =array('like',"%$start%");
    }else
    {
        if(!empty($start) && !empty($end))
        {
            if($start==$end)
            {
                $end=$end." 23:59:59";
            }
            $end=$end." 23:59:59";
            $where['_string']=" and update_time between '$start' and '$end' ";
        }
    }
	if($limit != '' && $page!=''){
		$notes_list =$db_notes->order('notes_id desc')->where($where)->limit(($page-1)*$limit,$limit)->select();
	}else{
		$notes_list =$db_notes->order('notes_id desc')->where($where)->select();
	}
    
    foreach($notes_list as &$list){
		$list['where'] = $where;
        $list['update_time'] = getFormatDate($list['update_time'],"Y-m-d H:i");
        $list['add_staff']= getStaffInfo($list['add_staff'],'staff_name');
        //$list['notes_type']=getTypeName( $list['notes_type'],"notes_type");

        if($list['is_alert']==1)
        {
            $list['is_alert']="是";
            $list['alert_time']=getFormatDate($list['alert_time'],"Y-m-d H:i");
        }else
        {
            $list['is_alert']="否";
            $list['alert_time']="";
        }
    }
    return $notes_list;
}
 /**
 * @name:getOaNotesInfo            
 * @desc：获取备忘录名称
 * @param：备忘录id $notes_id
 * $field  字段名 传入此参数时、查询此字段的值
 * @return：备忘录名称/整条数据
 * @author：王世超
 * @addtime:2016-08-11
 * @version：V1.0.0
**/
function getOaNotesInfo($notes_id,$field='notes_title'){
    if(!empty($notes_id)){
        $oa_notes = M('oa_notes');
        $where['notes_id'] = $notes_id;
        $notes_fieldinfo=$oa_notes->where($where)->find();
        if($field!='all')
        {
            $notes_fieldinfo=$notes_fieldinfo[$field];
        }
    }
    if($notes_fieldinfo){
        return $notes_fieldinfo;
    }else{
        return null;
    }
}

/********************************办公相关业务层方法 开始*************************************/

/********************************公文回掉函数******************************************/

/**
 * @desc    向数据表回写状态
 * @name    OaApprovalWriteBack
 * @param   $approval_no 员工编号
 * @return  true/false
 * @author  靳邦龙     王宗彬 增加要回写的字段
 * @time    2017-11-21
 */
function OaApprovalWriteBack($approval_no){
    $db_approval=M('oa_approval');
    if($approval_no){
        $map['approval_no']=$approval_no;
        $app_list=$db_approval->where($map)->find();
        $table_name=$app_list['approval_table_name'];
        $table_field=$app_list['approval_table_field'];
        $field_value=$app_list['approval_table_field_value'];
        $rewrite_field=$app_list['approval_rewrite_field'];  //回写字段  
        $approval_callfunction=$app_list['approval_callfunction'];
        if($table_name&&$table_field&&$field_value){
            $db_table=M($table_name);
            $table['update_time']=date("Y-m-d H:i:s");
            $table[$rewrite_field]=$app_list['status'];
            $table['approval_no']=$app_list['approval_no'];
            $where[$table_field]=$field_value;
            $res=$db_table->where($where)->save($table);
            if (!empty($approval_callfunction)){
                $status = $app_list['status']; //增加2017-12-26
                $node_staff = $app_list['node_staff']; 
                eval($approval_callfunction);
            }
        }
    }
    return $res;
}
/**
 * @name    callsaveOamissive
 * @desc    向数据表回写状态
 * @param   $missive_no 公文编号
 *          $missive_title 公文标题
 *          $recipients 收件人
 *          $communist_no 添加人
 *          $status 审核状态
 *          $node_staff 审核人
 * @return  
 * @author  王宗彬 
 * @time    2017-12-28
 */

function callsaveOamissive($missive_no,$missive_title,$recipients,$communist_no,$status,$node_staff){
    $missive_receiver = str_replace('_',',',$recipients);  //把_分割改成 ，分割
    $alert_url = "Oa/Oamissive/oa_missive_info/type/1/missive_no/".$missive_no;
    switch ($status) {
        case 11:
            $alert_title = "您有一条".$missive_title."的公文待审核";
            saveAlertMsg('43', $node_staff,$alert_url, $alert_title, '', '', '', $communist_no);
            ;break;
        case 12:
            $alert_title = "您有一条".$missive_title."的公文待审核";
            saveAlertMsg('43', $node_staff,$alert_url, $alert_title, '', '', '', $communist_no);
            ;break;
        case 21:
            
            $alert_title = $missive_title;
            saveAlertMsg('43',$missive_receiver,$alert_url, $alert_title, '', '', '', $communist_no);
            ;break;
        default:return false;;break;
    }   
}
/**
 * @name    moveFields()
 * @desc    保存后将模板节点转移到审批单节点中
 * @param   $approval_no审批编号
 * @param   $template_no模板编号
 * @return  带选中状态的模板下拉列表（HTML代码）
 * @author  靳邦龙
 * @version 版本 V1.0.1
 * @addtime 2017年11月1日
 */
function moveFields($approval_no,$template_no,$party_no=''){
    $db_approval=M('oa_approval');
    $db_approval_node=M('oa_approval_node');
    $db_template_node=M('oa_approval_template_node');
    //先删除原有的节点
    $where['approval_no'] = $approval_no;
    $db_approval_node->where($where)->delete();
    //重新添加符合条件的节点
    $res['template_no'] = $template_no;
    $node_list=$db_template_node->where($res)->order('node_order')->select();
    $bd_staff = M('hr_staff');
    foreach($node_list as &$node){
        $node['approval_no']=$approval_no;
        if (!empty($node['node_post'])) {
            $map="find_in_set(".$node['node_post'].",staff_post_no) and status = 1";//审核人所在岗位
        if (empty($party_no)) {
            $staff_nos=$bd_staff->where($map)->getField('staff_no',true);
        }else{
            $map['party_no'] = $party_no;//审核人所在支部
            $staff_nos=$bd_staff->where($map)->getField('staff_no',true);
            if (empty($staff_nos)) {
                $where="find_in_set(".$node['node_post'].",post_no)";//审核人所在岗位
                $staff_nos=$bd_staff->where($where)->getField('staff_no',true);
            }
        }
        $node['node_staff']=arrToStr($staff_nos);//所有具有审核权限的人员编号
        }
        $node['status']=11;
    }
    $node_add=$db_approval_node->addAll($node_list);
    if($node_add){
        return true;
    }else{
        return false;
    }
}
/********************************办公相关业务层方法 结束*************************************/




/** @name    setOaApprovalCurrentNode()
 * @desc    修改当前审批所处的节点 关联approval_node表（node_id），在移动节点、同意审批或退回时调用
 * @param   $approval_no模板编号
 * @param   $mode='edit'操作类型     edit编辑或添加       agree同意       return驳回
 * @author  靳邦龙
 * @addtime 2017年11月1日
 * @version V1.0.0
 */
function setOaApprovalCurrentNode($approval_no,$mode='edit'){
    $db_approval=M("oa_approval");
    $db_approval_node=M("oa_approval_node");
    //查询approval_node表待审核状态的第一条数据的node_id
    $where['approval_no'] = $approval_no;
    $where['status'] = '11';
    $min_id=$db_approval_node->where($where)->getField("MIN(node_id)");
    $map['node_id'] = $min_id;
    $node_staff=$db_approval_node->where($map)->getField('node_staff');
    $data['node_id']=$min_id;//节点ID
    $data['node_staff']=$node_staff;//节点审核人
    if(empty($min_id)){
        $data['node_id']='end';
        $data['status']='21';//审核完成
    }else{
        if($mode=='edit'){
            $data['status']='11';//待审核
        }elseif($mode=='agree'){
            $data['status']='12';//审核中
        }elseif($mode=='return'){
            $data['status']='31';//审核中
        }
    }
    $data['update_time']=date("Y-m-d H:i:s");
    $res['approval_no'] = $approval_no;
    $approval_save=$db_approval->where($res)->save($data);
    if($approval_save){
        return true;
    }else{
        return false;
    }
}



/**
* @name:sendAttMeetingCommunist         
* @desc:会议通知发送
* @param：
* @return：
* @author：黄子正
* @addtime:2017-06-01
* @updatetime:2017-06-01
* @version：V1.0.0
**/
 function sendAttMeetingCommunist($meeting_no){
    //实例化会议表对象
    $oa_meeting=M("oa_meeting");
    //实例化考勤机表对象
    $_att_machine=M("oa_att_machine");
    //实例化参会人员表对象
    $oa_meeting_communist=M("oa_meeting_communist");
    //        $meeting_no=I("get.meeting_no"); 
    $where['meeting_no'] = $meeting_no;
    //获取会议信息
    $meeting_info=$oa_meeting->where($where)->find();
    //获取参会人员编号
    $att_meeting_communist=$oa_meeting_communist->where($where)->getField("communist_no",true);        
    //开会信息
    $title="会议通知";
    $content="党务平台温馨提示，您有一个".$meeting_info['meeting_name']."会议将于".$meeting_info['meeting_start_time']."在".$meeting_info['meeting_addr']."召开，请您按时参加。";
    $time=date("Y-m-d H:i:s");
    foreach($att_meeting_communist as $v){
        //发送开会信息
    //            $meeting_send=sendPushMsg($title,$content,$v,$time);
        $meeting_send=sendXinGeMsg($title,$content,"0",$v,$time);
    //            
    }  
    //获取会议主持人编号
    $att_meeting_host=$oa_meeting->where($where)->getField("meeting_host");
    //主持信息
    $title1="会议主持通知";
    $content1="党务平台温馨提示，您有一个".$meeting_info['meeting_name']."会议需要主持，会议将于".$meeting_info['meeting_start_time']."在".$meeting_info['meeting_addr']."召开，请您提前半小时到场准备。";
    //发送开会信息
    //        $meeting_send1=sendPushMsg($title1,$content1,$att_meeting_host,$time);
      $meeting_send1=sendXinGeMsg($title1,$content1,"0",$att_meeting_host,$time);
    if($meeting_send&&$meeting_send1){
        return TRUE;
    }
    else{
        return false;
    }
}

/**
 * @name:getIsChecked           
 * @desc：签到判断
 * @param： $meeting_no(会议编号) $communist_no(参会人员编号)
 * @return：
 * @author：
 * @addtime::2017-05-09
 * @updatetime:2017-05-09
 * @version：V1.0.0
**/
function getIsChecked($meeting_no,$communist_no){
    $_att_log=M('oa_att_log');
    $oa_meeting = M('oa_meeting');
    $map['meeting_no'] = $meeting_no;
    $start=$oa_meeting->where($map)->getField("meeting_real_start_time");
    $end=$oa_meeting->where($map)->getField("meeting_real_end_time");
    $status=$oa_meeting->where($map)->getField("status");
    /***********会议实际开始时间为签到开始时间，会议实际结束时间为签到结束时间     结束   2017-10-21***********/
    //根据会议状态获取签到时间段
    if($status=='23'){
        //会议已召开，签到结束时间存在
        $where['check_time']=array(
            array("gt",$start),
            array("lt",$end)
        );        
    }elseif($status=='21'){
        //会议进行中，签到结束时间不存在
        $where['check_time']=array("gt",$start);
    } else{
        //会议未召开均为未签到状态
        return "no";
    } 
    $where['att_no']=$communist_no;    
    $meeting_precheck_time=$_att_log->where($where)->find();
    if(!empty($meeting_precheck_time)){
        return $meeting_precheck_time["att_id"];
    }else{
        return "no";
    }
}

/**
 *  getChecked
 * @desc 获取签到情况
 * @param $meeting_no
 * @param $communist_no
 * @return array
 * @user liubingtao
 * @date 2018/2/2
 * @version 1.0.0
 */
function getChecked($meeting_no,$communist_no)
{
    $_att_log = M('oa_att_log');
    $oa_meeting = M('oa_meeting');
    $where['meeting_no'] = $meeting_no;
    $time = $oa_meeting->where($where)->field('meeting_real_start_time,meeting_real_end_time,meeting_end_time')->find();
    if (empty($time['meeting_real_start_time'])) : return false; endif;

    $end_time = empty($time['meeting_real_end_time']) ? $time['meeting_end_time'] : $time['meeting_real_end_time'];

    $maps['att_no'] = ['in', $communist_no];
    $maps['check_time'] = ['between', "{$time['meeting_real_start_time']},$end_time"];

    $log_list = $_att_log->where($maps)->select();

    return $log_list;
}
