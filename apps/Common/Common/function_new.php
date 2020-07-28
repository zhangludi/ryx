<?php

use Api\Controller\PublicController;
use GuzzleHttp\RetryMiddleware;

//获取会议签到积分列表

function getMeetingSignList($communist_name,$meeting_type,$party_no,$page,$pagesize){
	

    $data['count'] = M("meeting_sign")->where("is_deleted=1")->count();
   
    	$num = $page+1;
		$where = "WHERE 1=1";
		if($communist_name){
			$where .= " and  m.meeting_sign_name like '%$communist_name%'";
		}
		
		if($meeting_type){
			$where .= " and  m.meeting_sign_type_id={$meeting_type}";

		}

        if($party_no){
            $where .= " and  m.party_no={$party_no}";

        }

		//获取签到会议的积分列表
		$communist_list = M()->query("
				SELECT m.*,t.meeting_sign_type
				FROM sp_meeting_sign m 
				LEFT JOIN sp_sys_user u ON u.user_id = m.add_staff 
				LEFT JOIN sp_meeting_sign_type t ON t.meeting_sign_type_id=m.meeting_sign_type_id 
				LEFT JOIN sp_ccp_communist c ON c.communist_no=m.meeting_sign_type_id 
				 {$where} ORDER BY m.create_time DESC limit $page,$pagesize");

	
        foreach ($communist_list as &$list){
        	//操作
            $list['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='".U('ccp_communist_integral_info',array('is_year'=>$is_year,'communist_integral'=>$list['communist_integral'],'rank'=>$num,'communist_no' => $list['communist_no']))."'><i class='fa fa-info-circle'></i>查看</a>";
           
            $num++;
        }
        $data['data'] = $communist_list;
        return $data;
    
}






?>