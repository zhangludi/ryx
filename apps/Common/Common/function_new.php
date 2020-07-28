<?php

use Api\Controller\PublicController;
use GuzzleHttp\RetryMiddleware;

//获取会议签到积分列表

function getMeetingSignList($communist_name,$meeting_type,$page,$pagesize){{
	
	$party_no = getPartyChildNos($party_no);
    $data['count'] = getCommunistCount($party_no,'',COMMUNIST_STATUS_OFFICIAL);
   
    	$num = $page+1;
		$where = "WHERE 1=1";
		if($communist_no){
			$where .= "and  meeting_sign_name like %$communist_no%";
		}
		
		if($meeting_type){
			$where .= "and  meeting_sign_type_id={meeting_sign_type_id}";

		}
		
		//获取签到会议的积分列表
		$communist_list = M()->query("
				SELECT m.*,t.meeting_sign_type
				FROM sp_meeting_sign m 
				LEFT JOIN sp_sys_user u ON u.user_id = m.add_staff 
				LEFT JOIN sp_meeting_sign_type t ON t.meeting_sign_type_id=m.meeting_sign_type_id 
				 {$where} ORDER BY m.create_time DESC limit $page,$pagesize");
    }
	
        foreach ($communist_list as &$list){
        	//操作
            $list['operate'] = "<a class='layui-btn layui-btn-primary layui-btn-xs' href='".U('ccp_communist_integral_info',array('is_year'=>$is_year,'communist_integral'=>$list['communist_integral'],'rank'=>$num,'communist_no' => $list['communist_no']))."'><i class='fa fa-info-circle'></i>查看</a>";
           
            $num++;
        }
        $data['data'] = $communist_list;
        return $data;
    
}






?>