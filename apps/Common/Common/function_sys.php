<?php
/********************************系统相关基础层方法 开始*************************************/



/********************************系统相关基础层方法 结束*************************************/

/********************************系统相关业务层方法 开始*************************************/



/********************************系统相关业务层方法 结束*************************************/


/**
 * @desc    用户权限判断函数
 * @todo    该函数主要用于验证用户是否有该权限，并能查看该权限页面
 * @name    checkFunctionAuth
 * @param   session('user_id') 登录用户id
 * @param   $function_id 需判断的function_id        	
 * @return  返回值：true-真 false-假
 * @author  靳邦龙
 * @time    2016-05-10
 */
function checkFunctionAuth($user_id, $function_code) {
	if ($function_code == "main") {
		return true;
	}
	$user = M ( "sys_user" );
	// 获取用户角色用户的角色
	$where['user_id'] = $user_id;
	$user_role = $user->where ( $where)->find ();
	$role = $user_role ['user_role'];
	if (! empty ( $role )) {
		// 获取用户权限
		$auth = M ();
		$function_code = $auth->query ( "select fun.function_code
            from
            sp_sys_function  fun , sp_sys_auth auth , sp_sys_role role , sp_sys_user u
            where auth.role_id = role.role_id
            AND auth.function_id = fun.function_id
            AND LENGTH(REPLACE(u.user_role,role.role_id,''))<LENGTH(u.user_role)
            AND auth.role_id in ($role)
            AND u.user_id = $user_id
            AND fun.function_code='$function_code'" );
	}
	if (! empty ( $function_code [0] ['function_code'] )) {
		return true;
	} else {
		return false;
	}
}

/**
 * @desc    判断当前是否登录/验证登录信息
 * @name    checkLogin
 * @param   $is_login 是否登录
 * @return  true/false，非登录状态直接跳转到登录页面
 * @author  靳邦龙 王世超
 * @time    2016-04-28
 * @update  2016-08-27
 * @version V1.0.1
 *         
 */
function checkLogin($is_login = null) {
//	$communist_no = session ( 'communist_no' );
//	if (empty ( $communist_no )) // session如果为空，则判断cookie
//{
//		$getcookiecode = getConfig ( 'cust_code' );
//		$username = cookie ( $getcookiecode . '_username' );
//		$pwd = cookie ( $getcookiecode . '_pwd' );
//		$communist_no = $getcookiecode . '_communist_no';
//
//		if (! empty ( $username ) && ! empty ( $pwd )) {
//			$communist_no = cookie ( $communist_no );
//			$db_user = M ( 'sys_user' );
//			$user_info = $db_user->where ( "user_relation_no=$communist_no" )->find ();
//			// 判断用户ID是否存在
//			if (! empty ( $user_info )) {
//				if (base64_encode ( $user_info ['user_pwd'] ) == $pwd) {
//					if ($user_info ['status'] == 0) {
//						session ( '[destroy]' ); // 清除服务器的sesion文件
//						/*
//						 * setcookie($getcookiecode.'_username',null);
//						 * setcookie($getcookiecode.'_pwd',null);
//						 * setcookie($getcookiecode.'_communist_no',null);
//						 */
//						cookie ( $getcookiecode . '_username', null );
//						cookie ( $getcookiecode . '_pwd', null );
//						cookie ( $getcookiecode . '_communist_no', null );
//						echo "<script>alert('账号停用')</script>";
//						return false;
//					} else {
//						$user_info ['last_login_time'] = date ( 'Y-m-d H:i:s' );
//						$save_res = $db_user->where ( "user_id=" . $user_info ['user_id'] )->save ( $user_info );
//						$communist_id = getCommunistinfo ( $user_info ['user_relation_no'], 'communist_id' );
//						// 存储session
//						session ( 'communist_no', $user_info ['user_relation_no'] ); // 当前用户communist_no
//						session ( 'user_id', $user_info ['user_id'] ); // 当前用户id
//						session ( 'communist_id', $communist_id ); // 当前用户id
//						return true;
//					}
//				} else {
//					session ( '[destroy]' ); // 清除服务器的sesion文件
//					/*
//					 * setcookie($getcookiecode.'_username',null);
//					 * setcookie($getcookiecode.'_pwd',null);
//					 * setcookie($getcookiecode.'_communist_no',null);
//					 */
//					cookie ( $getcookiecode . '_username', null );
//					cookie ( $getcookiecode . '_pwd', null );
//					cookie ( $getcookiecode . '_communist_no', null );
//					echo "<script>alert('密码错误')</script>";
//					$jump_url = U ( C ( 'JUMP_MODULE' ) . '/Public/' . LOGIN_ACTION );
//					echo "<script>location.href='" . U ( $jump_url ) . "'</script>";
//					return false;
//				}
//			} else {
//				session ( '[destroy]' ); // 清除服务器的sesion文件
//				cookie ( $getcookiecode . '_username', null );
//				cookie ( $getcookiecode . '_pwd', null );
//				cookie ( $getcookiecode . '_communist_no', null );
//				/*
//				 * setcookie($getcookiecode.'_username',null);
//				 * setcookie($getcookiecode.'_pwd',null);
//				 * setcookie($getcookiecode.'_communist_no',null);
//				 */
//				echo "<script>alert('用户名错误')</script>";
//				return false;
//			}
//		} else {
//			if ($is_login == 1) {
//				return false;
//			} else {
//				$jump_url = U ( C ( 'JUMP_MODULE' ) . '/Public/' . LOGIN_ACTION );
//				echo "<script>parent.location.href='" . $jump_url . "';</script>";
//				die ();
//			}
//		}
//	} else {
//		switch (GROUP_CODE) {
//			case 'communist' :
//				$user_relation_type = 1;
//				break;
//			case 'cust' :
//				$user_relation_type = 2;
//				break;
//			case 'agent' :
//				$user_relation_type = 3;
//				break;
//			case 'partner' :
//				$user_relation_type = 4;
//				break;
//			case 'company' :
//				$user_relation_type = 5;
//				break;
//			case 'member' :
//				$user_relation_type = 6;
//				break;
//		}
//		$db_user = M ( 'sys_user' );
//		$user_info = $db_user->where ( "user_relation_no='$communist_no'" )->getField ( 'user_relation_type' );
//		if ($user_info != $user_relation_type) {
//			session ( '[destroy]' ); // 清除服务器的sesion文件
//			                      // unset($_SESSION);
//			$getcookiecode = getConfig ( 'cust_code' );
//			// unset($_COOKIE);
//			cookie ( $getcookiecode . '_username', null );
//			cookie ( $getcookiecode . '_pwd', null );
//			cookie ( $getcookiecode . '_communist_no', null );
//			$jump_url = U ( C ( 'JUMP_MODULE' ) . '/Public/' . LOGIN_ACTION );
//			echo "<script>parent.location.href='" . $jump_url . "';</script>";
//			die ();
//		} else {
//			return true;
//		}
//	}
}

/**
 * @desc    判断当前是否登录/验证登录信息
 * @name    checkLoginCust
 * @return  true/false，非登录状态直接跳转到登录页面
 * @author  王彬
 * @time    2016-11-14
 * @version V1.0.0
 */
function checkLoginCust() {
	$cust_no = session ( 'cust_no' );
	if (empty ( $cust_no )) {
		$url = U ( 'Pms/Cust/login' );
		echo "<script>parent.location.href='" . $url . "';</script>"; die ();
	} else {
		return true;
	}
}

/**
 * @desc    判断前台当前是否登录
 * @name    checkIndexLogin
 * @return  true/false，非登录状态直接跳转到登录页面
 * @author  杨凯
 * @addtime 2017-09-14
 * @version V1.0.1
 *
 */
function checkIndexLogin(){
	$communist_no = session('index_communist_no');
		if (empty ( $communist_no )){
// 			$jump_url = U( 'Index/Index/' . LOGIN_ACTION );
// 			echo "<script>location.href='" . $jump_url . "'</script>";
			showMsg('success', '请登陆后进行查看此模块！', U('Index/Index/login'));
			return false;
		} else {
			return true;
		}
}
/**
 * @desc    判断越权
 * @name    checkAuth()
 * @param   $function_code 模块编码        	
 * @return  true/false
 * @author  靳邦龙
 * @time    2016-04-28
 */
function checkAuth($function_code) {
	// 没有登录直接进入到相关界面
	//if (checkLogin ()) {
		// 调用权限验证函数
		$rs_login = checkFunctionAuth ( session ( 'user_id' ), $function_code );
		// 对应模块的权限判断
		if ($rs_login == false) {
			echo "<script>alert('亲，您没有该权限!')</script>";
			echo "<script>history.back()</script>";
			die ();
		}else{
			return true;
		}
		
	//}
}

/**
 * @name:getCommunistAuth
 * @desc：获取员工数据权限
 * @param：$communist_no 工号
 *        $type_no 权限编号
 * @return：
 * @author：王彬
 * @addtime: 2016-11-22
 * @updatetime: 2016-11-22
 * @version：V1.0.0
 **/
function getCommunistAuth($communist_no,$type_no){
	$sys_user_auth = M('sys_user_auth');
	$where['communist_no'] = $communist_no;
	if(empty($type_no)){
	    $auth_list = $sys_user_auth->where($where)->order("add_time desc")->select();
	}else{
		$where['type_no'] = $type_no;
	    $auth_list = $sys_user_auth->where($where)->find();
	}
	
	if($auth_list){
		return $auth_list;
	}else{
		return null;
	}
}
/**
* @name:getAuthCommunistNos               
* @desc：获取有某个数据权限的所有人
* @param：$party_no  限定部门
* @param：$type_code  数据权限编码
* @param：$nomanager='1'  包含部门负责人
* @param：$format 返回数据格式  arr数组     str字符串             
* @return：$format=str以逗号隔 开的当前员工管理的所有部门的员工communist_no字符串；arr：一维数组
* @author：靳邦龙
* @addtime:2017-12-02
* @version：V1.0.1
**/
function getAuthCommunistNos($party_no,$type_code='is_admin',$partymanager='1',$format='str'){

	
    $sql="select  DISTINCT s.communist_no from sp_ccp_communist s,sp_sys_user_auth a where s.communist_no=a.communist_no and type_no='$type_code' and s.party_no='$party_no'";	
    //$sql="select  DISTINCT s.communist_no from sp_ccp_communist s,sp_sys_user_auth a where s.communist_no=a.communist_no and type_no='$type_code'";
    $communist_list=M()->query($sql);
    $communist_no=array();
    if($communist_list){
        foreach ($communist_list as $no){
            $communist_no[]=$no['communist_no'];
        }
    }
    $nos_str=arrToStr($communist_no);
    
    if($partymanager==1){
    	if(!empty($nos_str)){
    		$nos_str .= ",";
    	}
        $nos_str .= getPartyInfo($party_no,'party_manager');
    }
    $nos_arr=array_unique(strToArr($nos_str));
    if($format=='str'){
        $nos_arr=arrToStr($nos_arr,',');
    }
    return $nos_arr;
}
/**
* @name  checkUserAuth($type_no)
* @desc  判断登录用户是否有某项权限
* @param $type_no(数据权限编号)
* @return 有权限（true）  无权限（false）
* @author 黄子正
* @time   2017-09-20
*/
function checkUserAuth($type_no){
    $sys_user_auth=M('sys_user_auth');
    $communist_no=session('staff_no');
    $where['communist_no']=$communist_no;
    $where['type_no']=$type_no;
    $checked=$sys_user_auth->where($where)->getField('auth_id');
    if (!empty($checked)){
        return true;
    }else{
        return false;
    }
}
/**
 *@desc     获取配置结果
 * @name    getConfig()
 * @param   $code,配置的英文编码
 * @return  本条配置项的值
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getConfig($code) {
	$db_config = M ( 'sys_config' );
	$where['config_code'] = $code;
	$config = $db_config->where ($where)->field ( 'config_value' )->find ();
	if ($config) {
		return $config ['config_value'];
	} else {
		return "配置信息异常";
	}
}
/**
 *@desc     
 * @name    saveConfig()
 * @param   $code,配置的英文编码
 * 			$field 字段
 * 			$content 内容
 * @return  本条配置项的值
 * @author  靳邦龙
 * @time    2016-04-19
 */
function saveConfig($code,$field='config_value',$content='') {
	$db_config = M ( 'sys_config' );
	$data[$field] = $content;
	$where['config_code'] = $code;
	$config = $db_config->where ($where)->save($data);

	if ($config) {
		return true;
	}else{
		return false;
	}
}

/**
 * @desc    获取状态名
 * @name    getStatusName()
 * @param   string $status_group
 * @param   状态编号 $status_no
 * @param   风格 $style   text:返回纯文本；tags：返回带颜色的状态标签。
 * @return  状态名称    text/html
 * @author  靳邦龙
 * @time    2016-04-19
 * @update  2017-10-05
 */
function getStatusName($status_group, $status_no,$style='tags') {
	$db_status = M ( 'bd_status' );
	$where['status_group'] = $status_group;
	$where['status_no'] = $status_no;
	$status = $db_status->where ($where)->field ( "status_name,status_color" )->find ();
	if ($status) {
	    if($style=='tags'){
	        return "<font color='" . $status [status_color] . "'>$status[status_name]</font> ";
	    }else{
	        return $status ['status_name'];
	    }
	} else {
		return "无此状态";
	}
}
/**
 * @desc    获取状态值
 * @name    getStatusInfo()
 * @param   模块编码 $status_group
 * @param   状态编号 $status_no
 * @return  状态值
 * @author  靳邦龙
 * @time    2016-05-22
 */
function getStatusInfo($status_group, $status_no,$field='status_value') {
	$db_status = M ( 'bd_status' );
	$where['status_group'] = $status_group;
	$where['status_no'] = $status_no;
	$status = $db_status->where ($where)->getField($field);

	return $status;
}

/**
 * @desc    获取状态下拉列表
 * @name    getStatusSelect()
 * @param   类型编码 $status_group
 * @param   要选中的项目的编号 $selected_no
 * @param   $status_nos 多个编号逗号拼接
 * @return  对应模块的状态列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getStatusSelect($status_group, $selected_no,$status_nos) {
	$db_status = M ( 'bd_status' );
	$map['status']=1;
	if($status_group){
	    $map['status_group']=$status_group;
	}
	if($status_nos){
	    $map['status_no']=array('in',strToArr($status_nos));
	}
	$status_list = $db_status->where ($map)->field ( 'status_name,status_no' )->order ( 'status_order' )->select ();
	$status_options = "";
	foreach ( $status_list as &$status ) {
		$selected = "";
		if ($selected_no == $status ['status_no']) {
			$selected = "selected=true";
		}
		$status_options .= "<option $selected value='" . $status ['status_no'] . "'>" . $status ['status_name'] . "</option>";
	}
	if (! empty ( $status_options )) {
		return $status_options;
	} else {
		return "<option value=''>无数据</option>";
	}
}

/**
 * @desc    获取状态列表
 * @name    getStatusList()
 * @param   类型编码 $status_group
 * @param   要选中的项目的编号 $selected_no
 * @return  对应模块的状态列表
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getStatusList($status_group,$status_nos = '') {
	$db_status = M ( 'bd_status' );
	if($status_group){
		$map['status_group']=$status_group;
	}
	if($status_nos){
		$map['status_no']=array('in',strToArr($status_nos));
	}
	$map['status']=1;
	$status_list = $db_status->where($map )->field ( 'status_name,status_no' )->order('status_order')->select ();
	return $status_list;
}

/**
 * @desc    获取类型名称
 * @name    getBdTypeInfo()
 * @param   模块编码 $type_code //2016-06-15将编号改为编码和编号        	
 * @param   分组名称 $type_group        	
 * @return  类型名称
 * @author  靳邦龙-王彬
 * @time    2016-04-19
 * @update  2016-09-02
 */
function getBdTypeInfo($type_code, $group_code, $field = "type_name") {
	$db_type = M ( 'bd_type' );
	$map['_string']="(type_code='$type_code' or type_no='$type_code') and type_group='$group_code'";
	if ($field == "all") {
		$type_data = $db_type->where ($map)->find ();
	} else {
		$type_data = $db_type->where ($map)->getField($field);
	}
	return $type_data;
}
/**
 *@desc     获取类型下拉列表
 * @name    getBdTypeSelect()
 * @param   分组编码 $type_group
 * @param   类型父级id $type_pno 选填
 * @param   要选中的项目的编号 $selected_no 选填
 * @return  对应模块的类型列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getBdTypeSelect($type_group, $type_pno, $selected_no,$type_nos) {
	$db_type = M ( 'bd_type' );
	if (! empty ( $type_pno ) && is_numeric ( $type_pno )) {
		$where['type_pno'] = array('eq',$type_pno);
	}
	if(!empty($type_group)){
		$where['type_group']=array('eq',$type_group);
	}
	if(!empty($type_nos)){
		$where['type_no']=array('in',strToArr($type_nos));
	}
	$where['status']=array('eq',1);
	$type_list = $db_type->where ($where )->field ( 'type_name,type_no' )->select();
	$type_options = "";
	foreach ( $type_list as &$type ) {
		$selected = "";
		if ($selected_no == $type ['type_no']) {
			$selected = "selected";
		}
		$type_options .= "<option $selected value='" . $type ['type_no'] . "'>" . $type ['type_name'] . "</option>";
	}
	if (! empty ( $type_options )) {
		return $type_options;
	} else {
		return "<option value=''>无数据</option>";
	}
}
/**
 * 获取类型列表
 * @name   getBdTypeList()
 * @param   $type_group(类型分组)
 * @return  获取类型列表
 * @author  王彬  王宗彬   
 * @time    2016-09-01
 * @updatetime:2017-11-30 (添加 $type_nos 要选中的编号)
 */
function getBdTypeList($type_group,$type_nos) {
	$bd_type = M ( 'bd_type' );
	if($type_group){
		$map['type_group'] = $type_group;
	}
	if($type_nos){
		$map['type_no']=array('in',strToArr($type_nos));
	}
	$type_list = $bd_type->where($map)->select();
	if ($type_list) {
		return $type_list;
	} else {
		return "无相关数据！";
	}
}

/**
 * @name  getTypeSelect()
 * @desc  获取类型列表
 * @param 分组编码   $type_group
 *        类型父级id   $type_pno 选填
 *        要选中的项目的编号   $selected_no 选填
 * @return 对应模块的类型列表(html代码，附带选中状态)
 * @author 靳邦龙
 * @time   2016-04-28
 */
function getTypeSelect($type_group,$type_pno,$selected_no,$type_nos){
    $db_type=M('bd_type');
    if(!empty($type_pno)&&is_numeric($type_pno)){
        $where['type_pno']=$type_pno;
    }
	if(!empty($type_nos)){
        $where['type_no'] = array('in',$type_nos);
    }
    $where['type_group']=$type_group;
    $where['status']=1;
    $type_list=$db_type->where($where)->field('type_name,type_no')->select();
    $type_options="";
    foreach($type_list as &$type){
        $selected="";
        if($selected_no==$type['type_no']){
            $selected="selected=true";
        }
        $type_options.="<option $selected value='".$type['type_no']."'>".$type['type_name']."</option>";
    }
    if(!empty($type_options)){
        return $type_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}

/**
 * @name  getTypeChildNos()
 * @desc  获取当前类型的下级部门列表
 * @param 当前类型no
 * @param noself 是否包含自己
 * @param format 返回格式str或array
 * @return 以逗号隔开的当前类型及所有下级类型的type_no字符串或数组
 * @author 靳邦龙
 * @time   2017-11-07
 */
function getTypeChildNos($type_group,$type_pno='0',$format='str',$noself='1'){
    $type_nos_arr=getTypeChildNoselfNos($type_group,$type_pno);
    if($noself==1){
        $type_nos_arr[]=$type_pno;
    }
    if($format=='str'){
        $type_nos_arr=arrToStr($type_nos_arr);
    }
    return $type_nos_arr;
}

/**
 * @name  getTypeChildNoselfNos()
 * @desc  获取当前类型的所有下级类型列表（供getTypeChild调用）
 * @param 当前部门no
 * @return 所有下级类型数组
 * @author 靳邦龙
 * @time   2017-11-07
 */
function getTypeChildNoselfNos($type_group,$type_pno){
    $db_type=M('db_type');
    $map['type_group']=$type_group;
    $map['type_pno']=$type_pno;
    $type_list = $db_type->where($map)->getField('type_no',true);
    if($type_list){
        foreach($type_list as $type_no){
            $next_nos_arr=getTypeChildNoselfNos($type_group,$type_no);
            if($next_nos_arr){
                $type_list=array_merge($type_list,$next_nos_arr);
            }
        }
    }
    return $type_list;
}
/**
 * @desc    获取分组
 * @name    getBdGroupInfo()
 * @param   $group_code(分组编码)
 * @return  指定字段的值
 * @author  王彬
 * @version 版本 V1.0.0
 * @time    2016-10-13
 */
function getBdGroupInfo($group_code, $field = "group_name") {
	$bd_group = M ( 'bd_group' );
	if ($group_code != "") {
		$where['group_code'] = $group_code;
		if ($field == "all") {
			$group_data = $bd_group->where ($where)->find ();
		} else {
			$group_data = $bd_group->where ($where)->getField($field);
		}
	}
	if ($group_data) {
	    return $group_data;
	} else {
	    return null;
	}
}
/**
 * @desc    获取基础资料名称
 * @name    getBdCodeInfo()
 * @param   $code_no 资料编号
 * @param   $code_group 资料类型编码
 * @return  返回对应的中文名称
 * @author  靳邦龙
 * @time    2016-04-28
 * @time    2017-10-05
 */
function getBdCodeInfo($code_no, $code_group, $field = "code_name") {
	$db_code = M ( 'bd_code' );
	if(!empty($code_no)){
		if ($field == "all") {
			$map['code_group'] = array('eq',$code_group);
			$map['code_no'] = array('eq',$code_no);
			$code_info = $db_code->where ($map)->find ();
		} else {
			$no_arr = strToArr($code_no);
			$map['code_no']  = array('in',$no_arr);
			$map['code_group'] = array('eq',$code_group);
			$code_info = $db_code->where ($map)->getField($field,true);
		}
		$code_info = arrToStr($code_info);
		return $code_info;
	} else {
		return $code_no;
	}
	
}

/**
 * @desc    获取基础资料下拉列表
 * @name    getBdCodeSelect()
 * @param   基础资料类型编码 $code_group
 * @param   要选中的项目的编号 $selected_no
 * @return  对应模块、类型的基础资料列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getBdCodeSelect($code_group, $selected_no) {
	$db_code = M ( 'bd_code' );
	$map['code_group'] = $code_group;
	$map['status'] = 1;
	$code_list = $db_code->where ($map )->field ( 'code_name,code_no' )->select ();
	$selected_no_arr = strToArr($selected_no, ',');
	$code_options = "";
	foreach ( $code_list as &$code ) {
		$selected = "";
 		foreach ($selected_no_arr as &$number){
			
			if ($number == $code ['code_no']) {
				$selected = "selected=true";
			}
		}
		$code_options .= "<option $selected value='" . $code ['code_no'] . "'>" . $code ['code_name'] . "</option>";
	}
	if (! empty ( $code_options )) {
		return $code_options;
	} else {
		return "<option value=''>无数据</option>";
	}
}
/**
 * @desc    获取基础资料列表
 * @name    getBdCodeList()
 * @param   $code_group(基础信息类型)
 * @return  获取基础资料列表
 * @author  王彬
 * @version 版本 V1.0.0
 * @time    2016-09-01
 */
function getBdCodeList($code_group, $code_no) {
	$bd_code = M ( 'bd_code' );
	$where['status'] = "1";
	if (! empty ( $code_group )) {
		$where['code_group']= $code_group;
	}
	if (! empty ( $code_no )) {
		$where['code_no']= $code_no;
	}
	$code_list = $bd_code->where ( $where )->select ();
	if ($code_list) {
		return $code_list;
	} else {
		return null;
	}
}

/**
 * @desc    获取区域列表
 * @name    getAreaList()
 * @param   区域pid $type[1全部、2城市、3按pid取值]        	
 * @return  type=1:全部，type=2：返回所有城市，type=3：返回相应的子集列表
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getAreaList($area_pno) {
	$db_area = M ( 'bd_area' );
	if ($area_pno != '') {
		$where['area_pno']= $area_pno;
	}
	try {
		$area_list = $db_area->where ($where)->select ();
	} catch ( Exception $e ) {
		return null;
	}
	return $area_list;
}
/**
 * @desc    获取区域(省市县)名称
 * @name    getAreaName()
 * @param   区域id $area_no        	
 * @return  区域名称
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getAreaName($area_no) {
	$area_name = '';
	if (! empty ( $area_no ) && is_numeric ( $area_no )) {
		$db_area = M ( 'bd_area' );
		$where['area_no']= $area_no;
		$area_info = $db_area->where ($where)->field ( 'area_pno,area_name' )->find ();
		
		$area_name = $area_info ['area_name'];
		if (! empty ( $area_info ['area_pno'] )) {
			$area_no = $area_info ['area_pno'];
			$area_name = getAreaName ( $area_no ) . '_' . $area_name;
		}
	}
	if ($area_info) {
		return $area_name;
	} else {
		return null;
	}
}
/**
 * @desc    获取区域列表
 * @name    getAreaSelect()
 * @param   区域代码 $area_pno
 * @param   要选中的项目的编号 $selected_no 选填
 * @return  对应模块的类型列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getAreaSelect($area_pno, $selected_no) {
	$bd_area = M ( 'bd_area' );
	$where['area_pno']= $area_pno;
	$area_list = $bd_area->where ( $where )->field ( 'area_no,area_pno,area_name' )->select ();
	$area_options = "";
	foreach ( $area_list as &$area ) {
		$selected = "";
		if ($selected_no == $area ['area_no']) {
			$selected = "selected=true";
		}
		$area_options .= "<option $selected value='" . $area ['area_no'] . "'>" . $area ['area_name'] . "</option>";
	}
	
	if (! empty ( $area_options )) {
		return $area_options;
	} else {
		return "<option value=''>无数据</option>";
	}
}
/**
 * @desc    获取上级区域代码
 * @name    getAreaParentSelect()
 * @param   分组编码 $type_group
 * @param   要选中的项目的编号 $selected_no 选填
 * @return  对应模块的类型列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getAreaParentSelect($area_no, $selected_no) {
	$bd_area = M ( 'bd_area' );
	$where['area_pno']= $area_pno;
	$area_list = $bd_area->where ( $where )->field ( 'area_no,area_pno,area_name' )->select ();
	$area_options = "";
	foreach ( $area_list as &$area ) {
		$selected = "";
		if ($selected_no == $area ['area_no']) {
			$selected = "selected=true";
		}
		$area_options .= "<option $selected value='" . $area ['area_no'] . "'>" . $area ['area_name'] . "</option>";
	}
	if (! empty ( $area_options )) {
		return $area_options;
	} else {
		return "<option value=''>无数据</option>";
	}
}
/**
 * @name    upFiles(文件流,模块编码，上传类型，添加人编号，文件名，是否生成缩略图)
 * @todo    上传方法
 * @param   $file，文件流 $function_code
 * @param   模块编码
 * @param   $type='1' 上传类型
 * @param   $communist_no 添加人
 * @param   $is_thumb 是否生成缩略图（暂时不支持）1是、0否
 * @param   $new_name 新文件的名称 （暂时不支持）
 * @return boolean
 */
function upFiles($file, $function_code, $type, $communist_no, $new_name,$is_thumb) {

	date_default_timezone_set ( "PRC" ); // 设置时
	$upload = new \Think\Upload ();
	$upload->exts = array('jpg','gif','png','jpeg','xls','xlsx','doc','docx','pdf','mp4');// 设置附件上传类型 // 不限制类型
	$upload->maxSize = 0; // 不限制附件上传大小 */
	$upload->rootPath = C ( 'TMPL_PARSE_STRING' )['__UPLOAD_PATH__']; // 上传文件所在文件夹
	$upload->savePath = ''; // 设置附件上传目录
	$upload->replace = true; // 同名会覆盖
	$upload->autoSub = true; // 开启子目录保存
	if (empty ( $function_code )) {
		$function_code = "public";
	}
	$upload->subName = $function_code; // 子目录文件夹
	if($new_name){
	    $upload->saveName = $new_name; // 设置附件名称，同名会覆盖
	}
	$info = $upload->upload ();
	$path = $info ['file'] ['savepath'] . $info ['file'] ['savename'];
	if ($info) {
		$data ["upload_path"] = $path;
		// 上传成功后将数据存入数据库
		$db = M ( "bd_upload" );
		if (empty ( $type )) {
			$type = '1';
		}
		$filename = $data ["upload_source"] = $file ["name"];
		$data ['add_staff'] = $communist_no;
		$data ["add_time"] = date ( 'y-m-d H:i:s' );
		$data ["update_time"] = date ( 'y-m-d H:i:s' );
		$data ["function_code"] = $function_code;
		$data ["upload_type"] = $type;
		$data ["upload_size"] = $file ['size']; // 文件大小20160711新增
		$id = $db->add ( $data );
	} else {
		$data ["upload_path"] = "";
	}
	if ($id) {
		$up ['status'] = '1';
		$up ['msg'] = '上传成功！';
		$up ['upload_path'] = C ( 'TMPL_PARSE_STRING' )['__UPLOAD_PATH__'] . $path;
		$up ['upload_id'] = $id;
		$up ["upload_source"] = $filename;
		$up ["upload_size"] = $data ["upload_size"];
	} else {
		$up ['status'] = '0';
		$up ['msg'] = $upload->getError();
	}
	return $up;
}

/**
 * @DESC    删除附件（数据/文件）
 * @name    delFile()
 * @param   $upload_id(附件ID)
 * @return  true/false
 * @author  王彬
 * @version 版本 V1.0.0
 * @time    2016-07-15
 */
function delFile($upload_id) {
	$bd_upload = M ( 'bd_upload' );
	$where['upload_id']= $upload_id;
	$upload_dt = $bd_upload->where ($where)->find ();
	$upload = C ( "TMPL_PARSE_STRING" )['__UPLOAD_PATH__'];
	$upload .= $upload_dt ['upload_path'];
	if (unlink ( $upload )) {
		$upload_data = $bd_upload->where ($where )->delete ();
	} 
	if ($upload_data) {
	    return true;
	} else {
	    return false;
	}
}
/**
 * @DESC    获取文件信息    支持多文件多字段查询
 * @name    getUploadInfo()
 * @param   上传文件的id
 * @return  文件路径
 * @author  靳邦龙
 * @time    2017-11-10
 */
function getUploadInfo($upload_id, $field = "upload_path") {
    if(!empty($upload_id)){
        $upload_id = str_replace ( "`", ",", $upload_id );
        $url=C ( 'TMPL_PARSE_STRING' )['__UPLOAD__'].'/';
        $db_upload=M('bd_upload');
        $no_arr = strToArr($upload_id);
        $no_arr_length = sizeof($no_arr);//取编号数量
        
        if($field!='all'){
            $arr=strToArr($field);
            $arr_length=sizeof($arr);
            if($arr_length==1){//如果是一个字段，返回字符串
                $map['upload_id']  = array('in',$no_arr);
                if($field == "upload_path"){
                	$upload_value=$db_upload->where($map)->getField($field,true);
                    foreach($upload_value as &$path){$path= $url.$path;}
                }
                if($field=='upload_source'){//加链接
                	$upload_list=$db_upload->where($map)->field('upload_source,upload_path')->select();
                	$upload_value=array();
                	foreach($upload_list as &$up){
                		$upload_value[]=" <a target='_blank' href='".$url.$up['upload_path']."'>".$up['upload_source']."</a>";
                	}
                }
                $upload_value = arrToStr($upload_value,',');
            }else if($arr_length>1){//如果是多字段查询，查询单条数据，多个字段
                if($no_arr_length>1){//多条数据select查询
                    $map['upload_id']  = array('in',$no_arr);
                    $upload_value=$db_upload->where($map)->field($field)->select();
                    if(in_array('upload_path', $arr)){
                        foreach($upload_value as &$up){$up['upload_path']= $url.$up['upload_path'];}
                    }
                }elseif($no_arr_length==1){//单条数据，find查询
                    $map['upload_id']  = array('eq',$upload_id);
                    $upload_value=$db_upload->where($map)->field($field)->find();
                    if(in_array('upload_path', $arr)){
                        $upload_value['upload_path']= $url.$upload_value['upload_path'];
                    }
                }
            }
        }elseif($field=='all'){//查询完整记录
            if($no_arr_length>1){//多条数据select查询
                $map['upload_id']  = array('in',$no_arr);
                $upload_value=$db_upload->where($map)->select();
                foreach($upload_value as &$up){$up['upload_path']= $url.$up['upload_path'];}
            }elseif($no_arr_length==1){//单条数据，find查询
                $map['upload_id']  = array('eq',$upload_id);
                $upload_value=$db_upload->where($map)->find();
                $upload_value['upload_path']= $url.$upload_value['upload_path'];
            }
        }
    }
    if($upload_value){
        return $upload_value;
    }else{
        return null;
    }
}
/**
 * @desc    获取文件html代码
 * @name    getUploadHtml()
 * @param   上传文件的id，可以多个逗号隔开 upload_ids
 * @param   $height  高度
 * @param   $width   宽度
 * @param   $has_chakan=1   是否显示查看按钮，  1是，其他：否
 * @param   $is_info=1  1显示删除按钮 
 * @return  标签
 * @author  靳邦龙
 * @time    2017-11-08
 */
function getUploadHtml($upload_ids, $height ,$width=100,$has_chakan=1,$is_info = 1) {
	$db_upload = M ( 'bd_upload' );
    if($upload_ids){
        $upload_ids = str_replace ( "`", ",", $upload_ids );
        if ($upload_ids) {
        	$upload_map['upload_id'] = array('in' , $upload_ids);
            $path_list = $db_upload->where ($upload_map)->field('upload_path,upload_id')->select();
        }
        if($path_list){
            if($height){
                $height="height='$height'";
            }
            if($width){
                $width="width='$width'";
            }
            //$html = "<div id='js-grid-juicy-projects' class='cbp' STYLE='height:100"."px'>";
            $html = "";
            $item="<div class='cbp-item'>
                    <div class='cbp-caption'>
                        <div class='cbp-caption-defaultWrap'>";
            $html_s="</div>
                    <div class='cbp-caption-activeWrap'>
                        <div class='cbp-l-caption-alignCenter'>
                            <div class='cbp-l-caption-body'>";
            $foot="     </div> 
                      </div> 
                   </div> 
                </div> 
              </div> ";
            $f='';
            //         $f='</div>';
            foreach ($path_list as &$up) {
                $path=$up['upload_path'];
                $attach='';
                $path=C ( 'TMPL_PARSE_STRING' )['__UPLOAD__'].'/'.$path;//完整路径
                $extend = strtolower(end(explode('.',$path)));//截取文件扩展名
                $css='cbp-lightbox';//图片查看，文件下载
                if($extend=="jpg"||$extend=="jpeg"||$extend=="png"||$extend=="gif"||$extend=="bmp"||$extend=="webp"){
                    $img =" <img data-original='$path' $height $width src='$path'>";
                }else if($extend=="3gp"||$extend=="mp4"||$extend=="rmvb"||$extend=="avi"||$extend=="wmv"||$extend=="mkv"||$extend=="mp3"){
                    $img =" <embed src='$path' $height $width  autostart=false></embed>";
                }else{
                    $statics=C ( 'TMPL_PARSE_STRING' )['__STATICS__'];//完整路径
                    $img =" <img data-original='$path' $height $width src='".$statics."/public/images/file.png' >";
                    $css='';
                }
                $del=$up['upload_id'];
                $a='';
                if($is_info == 1){
                	$a.="<a href='javascript:;' onclick='uploader_del(this,$del,)' class='cbp-l-caption-buttonLeft btn red uppercase btn red uppercase' rel='nofollow'>删除</a>";
                }
                if($has_chakan==1){
                    $a.="<a href='$path' class='$css cbp-l-caption-buttonRight btn red uppercase btn red uppercase' data-title=''>查看</a>";
                }
                if($is_info == 0 && $has_chakan==0){
                	$html_s='';
                	$foot=" 
		                   </div> 
		                </div> 
		              </div> ";;
                }
                $attach=$item.$img.$html_s.$a.$foot;
                $html.=$attach;
            }
        }
    }
    return $html;
}

/**
 * @name		renameFiles()
 * @desc		附件重新命名
 * @param		$function_code 模块编码-存储的文件名称
 * @param		$key_id  表主键名称
 * @param		$upload_id  upload表的id
 * @param		$mode_name  模块名
 * @return		true/false
 * @author		ljj
 * @version		版本 V1.0.0
 * @updatetime	2018-03-01
 * @addtime		2018-03-01
 */
function renameFiles($function_code,$mode_name,$key_id,$upload_id){
	$bd_upload = M('bd_upload');
	$upload_map['upload_id'] = array('in',$upload_id);
	$upload_path_arr = $bd_upload->where($upload_map)->field('upload_id,upload_path')->select();
	$path = "uploads/".$function_code.'/';//文件绝对路径-前缀路径
	M()->startTrans(); // 开启表事物
	if(!empty($upload_path_arr)){
		foreach ($upload_path_arr as $key => $upload_path) {
			$savename_old = "uploads/".$upload_path['upload_path'];//原文件路径
			$fileext = end(explode('.', $upload_path['upload_path'])); // 文件后缀名
			//主附件上传-无后缀流水号
			$savename_new = $function_code.'/'.$mode_name.'_'.$key_id.'_'.($key+1).'.'.$fileext;//附件命名-新文件路径.
			$rename_res = rename($savename_old,"uploads/".$savename_new);//文件重新定义名称
			$upload_id = $upload_path['upload_id'];
			$data['upload_path'] = $savename_new;
			if($rename_res){
				$up_map['upload_id'] = $upload_id;
				$update_res = $bd_upload->where($up_map)->setField($data);
			}
		}
		if($update_res){
			M()->commit(); // 事物提交
			return true;
		}else{
			M()->rollback(); // 事物回滚
			return false;
		}
	}else{
		//单文件-多字段储存-暂不支持
		return false;
	}
}
/**
 * @desc    微信图片下载保存
 * @name    saveFile
 * @param   string $filename    图片名字
 * @param   string $filecontent 通过微信接口获取到的文件流
 * @param   string $function_code 模块
 * @return  array
 * @author  刘丙涛
 * @time    2017-05-17
 * @update  2017-05-17
 * @version V1.0.0
 */
function saveFile($filename, $filecontent, $function_code) {
	$savepath = C ( 'TMPL_PARSE_STRING' )['__UPLOAD_PATH__'] . '/' . $function_code . '/' . $filename;
	if (file_put_contents ( $savepath, $filecontent )) { // 写入图片流生成图片
		$db = M ( "bd_upload" );
		$data ["upload_path"] = $function_code . '/' . $filename;
		$data ["upload_source"] = $filename;
		$data ["add_time"] = date ( 'y-m-d H:i:s' );
		$data ["update_time"] = date ( 'y-m-d H:i:s' );
		$data ["function_code"] = 'WeCountry';
		$data ["upload_type"] = 1;
		$id = $db->add ( $data );
		if ($id) {
			return $id;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
/**
 * @desc   将本地文件的名称、路径、大小、更新时间写入数据库
 * @name    uploadLocalFile
 * @param   $path  文件夹路径 格式：H:/Work/images/app
 * @param   $file_level   存入库的文件目录路径  格式‘admin/approval’
 * @param   $function_code    模块编码
 * @return  true / false
 * @author  靳邦龙
 * @add_time    2017-11-09
 * @version V1.0.0
 */
function uploadLocalFile($path,$file_level,$function_code){
//     $hostdir=dirname('H:/Work/images/app/59c4c49872604.doc');
    $hostdir=dirname($path.'/59c4c49872604.doc');
    $filesnames = scandir($hostdir);
    $db_up=M("bd_upload");
    $arr=array();
    foreach ($filesnames as $name) {
//         $s['upload_path']='admin/approval/'.$name;
        $up['upload_path']=$file_level.'/'.$name;
        $up['upload_size']=filesize($path.'/'.$name);
        $up['function_code']=$function_code;
        $up['upload_type']=1;
        $up['upload_source']=$name;
        $up['update_time']=date("Y-m-d H:i:s",filemtime($path.'/'.$name));
        $up['add_time']=date("Y-m-d H:i:s",filemtime($path.'/'.$name));
        $arr[]=$up;
    }
    $res=$db_up->addAll($arr);
    if($res){
        return true;
    }else{
        return false;
    }
}

/**
 * getImageNum
 * 获取图片数量
 * @param  string $upload_id    图片id
 * @return string
 * @author 刘丙涛
 */
function getImageNum($upload_id, $num = 'all')
{
    if (empty($upload_id)) :return; endif;
    $where['upload_id']= array('in',$upload_id);
    $img_url = M('bd_upload')->where($where)->select();

    if (!is_array($img_url)) : return; endif;

    if (is_int($num) && ($num +0) == 1)
    {
        if (!isset($img_url[0]['upload_path'])) : return; endif;

        return C ( 'TMPL_PARSE_STRING' )['__UPLOAD__'].'/'.$img_url[0]['upload_path'];
    }
    else if ($num == 'all')
    {
        if (count($img_url) < $num || $num == 'all') : $num = count($img_url); endif;

        $arr = [];

        for ($i = 0; $i < $num; $i++)
        {
            $arr[$i]['img_url'] = C ( 'TMPL_PARSE_STRING' )['__UPLOAD__'].'/'.$img_url[$i]['upload_path'];
        }
        return $arr;
    }

    return;
}


/**
 * @desc    判断用户是否拥有某个权限，
 * @name    checkDataAuth
 * @param   $communist_no string 人员编号
 * @param   $auth_type string 权限编号
 * @param   $is_get string 是否获取对应数据
 * @param   $setting array/string 对应方法的参数
 * @return  有返回数据，部门数组/部门字符串；    没有返回false
 * @author  刘丙涛
 * @addtime 2017-10-13
 * @version V1.0.0
 *
 */
function checkDataAuth($communist_no,$auth_type='is_admin',$is_get='0',$setting){
    $db_auth = M('sys_user_auth');
    $where['communist_no']= array('eq',$communist_no);
    $where['type_no']= array('eq',$auth_type);
    $result = $db_auth->where($where)->find();
    if ($is_get){
        switch ($auth_type){
            case 'is_admin':
                $result = getPartyData($result,$communist_no,$setting);
                break;
            default:
                $result = getCommunistPartyList($communist_no);break;
        }
    }else{
        $result = empty($result)?false:true;
    }
    return $result;

}

/***************************** 取值类方法开始 ************************/
/**
 * @name    getUserInfo()
 * @desc    获取指定用户指定字段的值
 * @param   用户id   $user_id
 * @param   字段名          $field
 * @return  指定用户指定字段的值
 * @author  靳邦龙
 * @time    2016-04-20
 */
function getUserInfo($user_id,$field){
    if(!empty($user_id)&&is_numeric($user_id)){
        $db_user=M('sys_user');
        $where['user_id']= $user_id;
        $user_value=$db_user->where($where)->field($field)->find();
    }
    if($user_value){
        return $user_value[$field];
    }else{
        return null;
    }
}
/**
 * @name    getUserSelect()
 * @desc    获取用户列表
 * @return  返回全部列表。
 * @author  靳邦龙
 * @time    2016-04-20
 */
function getUserSelect(){
    $db_user=M('sys_user');
    $user_list=$db_user->select();
    if(empty($user_list))
    {
        return $user_list;
    }else {
        return null;
    }
   
}
/**
 * @name    getRoleInfo()
 * @desc    获取角色名称
 * @param   角色id     $role_id
 * @return  返回全部列表。
 * @author  靳邦龙
 * @time    2016-04-20
 */
function getRoleInfo($role_id,$field='role_name'){
    if(!empty($role_id)&&is_numeric($role_id)){
        $db_role=M('sys_role');
         $where['role_id']= $role_id;
        $role_name=$db_role->where($where)->getField($field);
    }
    if($role_name){
        return $role_name;
    }else{
        return '无此角色';
    }
}
/**
 * @name    getRoleList()
 * @desc    获取角色列表
 * @param   角色id     $role_id
 * @param   获取参数字段名    $field（all为获取全部字段）
 * @return  返回数组。
 * @author  黄子正
 * @time    2017-10-18
 */
function getRoleList($role_id,$field='all'){
	$db_role=M('sys_role');
	if(!empty($role_id)&&is_numeric($role_id)){	
		$where['role_id']= $role_id;	
		if($field=='all'){
			$role_name=$db_role->where($where)->select();
		}else{
			$role_name=$db_role->where($where)->field($field)->select();
		}		
	}else{
		if($field=='all'){
			$role_name=$db_role->select();
		}else{
			$role_name=$db_role->field($field)->select();
		}
	}
	if($role_name){
		return $role_name;
	}else{
		return '无此角色';
	}
}
/**
 * @name    getRoleSelect()
 * @desc    获取角色列表
 * @param   $selected_id 要选中的角色的id,支持多选
 * @return  对应角色列表(html代码)
 * @author  靳邦龙
 * @time    2016-04-19
 */
function getRoleSelect($selected_id){
    $db_role=M('sys_role');
    $role_list=$db_role->where('status=1 and role_type = 1')->field('role_name,role_id')->select();
    $selected_id_arr=explode(",",$selected_id);//分割成数组
    $role_options="";
    foreach($role_list as &$role){
        $selected="";
        if(in_array($role['role_id'], $selected_id_arr)){//判断角色id是否存在于数组中
            $selected="selected=true";
        }
        $role_options.="<option $selected value='".$role['role_id']."'>".$role['role_name']."</option>";
    }
    if(!empty($role_options)){
        return $role_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}
/**
 * @name    getPosition()
 * @desc    获取路径导航
 * @param   $function_code  模块编码
 * @return  路径导航（HTML代码）
 * @author  靳邦龙-王彬
 * @version
 * @update  2016-08-29
 * @time    2016-04-28
 */
function getPosition($function_code){
    $db_function=M('sys_function');
    $where['function_code']= $function_code;
    $where['group_code']= GROUP_CODE;
    $this_function=$db_function->where($where)->find();
    if(!empty($this_function['function_url'])){
        $url="href=".U($this_function['function_url']);
    }else{
        $url="href=''";
    }
    $this_position="<li class='active'> <a $url>$this_function[function_name]</a></li>";
    $pid=$this_function['function_pid'];
    $this_position=getParentFunction($pid).$this_position;
    $this_position="<li><em class='iconfont font-blue'>&#xe67e;</em>".$this_position."</li>";
    return $this_position;
}
/**
 * @name    getParentFunction()
 * @desc    获取上级菜单
 * @param   $function_code  模块编码
 * @return  上级菜单（HTML代码）
 * @author  靳邦龙-王彬
 * @time    2016-05-10
 * @update  2016-08-29
 */
function getParentFunction($function_id){
    /* 先判断有无上级菜单 */
    $db_function=M('sys_function');
    $where['function_id']= $function_id;
    $this_function=$db_function->where($where)->find();
    if(!empty($this_function['function_url'])){
        $url="href=".U($this_function['function_url']);
    }else{
        $url="href=''";
    }
    $this_position="<a $url>$this_function[function_name]</a> <i class='iconfont'>&#xe659;</i> ";
    $pid=$this_function['function_pid'];
    if($pid!=0){
        $this_position=getParentFunction($pid).$this_position;
    }
    return $this_position;
}
/**
 * @name    getFunctionInfo()
 * @desc    获取功能名称
 * @param   $function_code  模块编码
 * @param   $field  字段名称  
 * @return  功能名称
 * @author  靳邦龙
 * @time    2016-05-10
 * @update  2017-10-05
 */
function getFunctionInfo($function_code,$field='function_name'){
    $db_function=M('sys_function');
    if(!empty($function_code)){
    	$where['function_code']= $function_code;
   		$where['group_code']= GROUP_CODE;
        $function=$db_function->where($where)->getField($field);
    }
    if(!empty($function)){
        return $function;
    }else{
        return '无此功能';
    }
}
/**
 * @name    getFunctionNav()
 * @desc    获取子集导航菜单用于页面右上侧显示
 * @param   $function_code   父级id  
 * @return  HTML导航代码
 * @author  靳邦龙
 * @time    2016-05-10
 */
function getFunctionNav($function_code){
    $nav_str="";
    if(ENTER_ACTION=='index'){
        $nav_str="";
    }else {
        $db_function=M('sys_function');
        if(!empty($function_code)){
        	$where['function_code']= $function_code;
   			$where['group_code']= GROUP_CODE;
            $pid=$db_function->where($where)->getField('function_pid');
        }
        $map['function_pid']= $pid;
   		$map['group_code']= GROUP_CODE;
    	$nav_list=$db_function->where($map)->field("function_code,function_name,function_url")->select();
    	$nav_str="";
    	foreach($nav_list as &$nav){
    		$url="href=".U($nav['function_url']);
    		if($nav['function_code']==$function_code){
    		    $nav_str.="<a class='btn btn-xs blue' $url>$nav[function_name]</a> ";
    		}else{
    		    $nav_str.="<a class='btn btn-xs blue btn-outline' $url>$nav[function_name]</a>";
    		}
    	}
    }
    return $nav_str;
}

/**
 * @name    saveLog()
 * @desc    系统日志保存方法
 * @param   $function_code  模块编码
 * @param   $log_type 日志类型(1新增2修改3删除4登陆)
 * @param   $log_oldcontent  原内容
 * @param   $log_newcontent  新内容
 * @return  true/false
 * @author  靳邦龙
 * @time    2016-04-28
 */
function saveLog($function_code,$log_type,$log_oldcontent,$log_newcontent){
    $db_syslog=M('sys_log');
    $data['function_code']=$function_code;
    $data['log_type']=$log_type;
    $data['log_oldcontent']=$log_oldcontent;
    $data['log_newcontent']=$log_newcontent;
    $data['add_time']=date("Y-m-d H:i:s");
    $data['update_time']=date("Y-m-d H:i:s");
    $data['add_staff']=session('staff_no');
    $log_id=$db_syslog->add($data);
    if($log_id){
        return true;
    }else{
        return false;
    }
   
}

/**
 * @name  checkAuthweixin()
 * @desc  判断越权
 * @param $function_code 模块编码
 * @return true/false
 * @author 靳邦龙
 * @time   2016-04-28
 */
function checkAuthweixin($function_code){
    //没有登录直接进入到相关界面
    $communist_no = session('wechat_communist');
    if($communist_no){
        $db_user = M('sys_user');
        $where['user_relation_no']= $communist_no;	
        $role = $db_user->where($where)->field('user_id,user_role')->find();
        //调用权限验证函数
        $rs_login=checkFunctionAuthweixin($role['user_id'],'',$function_code);
        //对应模块的权限判断
        if($rs_login == false){
            echo "<script>alert('亲，您没有该权限!')</script>";
            echo "<script>window.location.href='".U('Index/index')."';</script>";die;
        }
        return true;
    }else{
        $rs_login = checkFunctionAuthweixin('','99999',$function_code);
        //对应模块的权限判断
        if($rs_login == false){
            echo "<script>alert('亲，您没有该权限!')</script>";
            echo "<script>window.location.href='".U('Index/index')."';</script>";die;
        }
        return true;
    }
}

/**
 * @name  checkFunctionAuthweixin
 * @desc  用户权限判断函数
 *        该函数主要用于验证用户是否有该权限，并能查看该权限页面
 * @param session('user_id') 登录用户id
 * @param $function_id 需判断的function_id
 * @return 返回值：true-真 false-假
 * @author 靳邦龙
 * @time   2016-05-10
 */
function checkFunctionAuthweixin($user_id,$role,$function_code){
    if($function_code=="index"){
        return true;
    }
    if (empty($role)){
        $user = M("sys_user");
        //获取用户角色用户的角色
        $where['user_id']= $user_id;	
        $user_role = $user->where($where)->find();
        $role=$user_role['user_role'];
        if(!empty($role)){
            //获取用户权限
            $auth =M();
            $function_code=$auth->query("select fun.function_code
                from
                sp_sys_function  fun , sp_sys_auth auth , sp_sys_role role , sp_sys_user u
                where auth.role_id = role.role_id
                AND auth.function_id = fun.function_id
                AND LENGTH(REPLACE(u.user_role,role.role_id,''))<LENGTH(u.user_role)
                AND auth.role_id in ($role)
                AND u.user_id = $user_id
                AND fun.function_code='$function_code'");
        }
    }else{
        $auth =M();
        $function_code=$auth->query("select fun.function_code
                from
                sp_sys_function  fun , sp_sys_auth auth , sp_sys_role role
                where auth.role_id = role.role_id
                AND auth.function_id = fun.function_id
                AND auth.role_id='$role' 
                AND fun.function_code='$function_code'");
    }

    if(!empty($function_code[0]['function_code'])){
        return true;
    }else{
        return false;
    }

}
/**
 * @name  checkLoginWeixin
 * @desc  微信判断是否认证
 * @return 返回值：true-真 false-假
 * @author 王宗彬
 * @time   2016-07-03
 */
function checkLoginWeixin(){
    $communist_no = session('wechat_communist');
    if (empty($communist_no)){
        echo "<script>alert('您没有登陆')</script>";
        echo "<script>location.href='".U('Index/login')."'</script>";
    }
}
/***************************** 取值类方法结束 ************************/
?>