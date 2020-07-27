<?php

/********************************门户相关基础层方法 开始*************************************/

/**
 * @name    getNavPosition()
 * @desc    获取上级菜单面包屑
 * @param   $nav_code  模块编码
 * @return  上级菜单（HTML代码）
 * @author  靳邦龙
 * @time    2017-10-07
 */
function getNavPosition($nav_id){
    /* 先判断有无上级菜单 */
    $db_nav=M('sys_nav');
    $nav_map['nav_id'] = $nav_id;
    $this_nav=$db_nav->where($nav_map)->find();
    if(!empty($this_nav['nav_url'])){
        if($pid!=0){
            $pid=$this_nav['nav_pid'];
            $id=$this_nav['nav_id'];
            $url="href=".U($this_nav['nav_url'])."?nav_pid=$pid&nav_id=$id";
        }else{
            $url="href=".U($this_nav['nav_url'])."?nav_pid=$id&nav_id=$id";
        }
    }else{
        $url="href=''";
    }
    $this_position="<a $url>$this_nav[nav_name]</a>";
    $pid=$this_nav['nav_pid'];
    if($pid!=0){
        $this_position=getNavPosition($pid).'&gt;'.$this_position;
    }
    return $this_position;
}
/**
 * @desc    获取导航
 * @name    getBdNavInfo()
 * @param   $nav_id id 
 * @param   $field 字段名
 * @return  返回对应的中文名称
 * @author  靳邦龙
 * @time    2017-10-04
 */
function getBdNavInfo($nav_id,$field = "nav_name") {
    $db_nav = M ( 'sys_nav' );
    $nav_map['nav_id'] = $nav_id;
    if ($field == "all") {
        $nav_info = $db_nav->where ($nav_map)->find ();
    } else {
        $nav_info = $db_nav->where ($nav_map)->getField ( $field );
    }
    return $nav_info;
}

/**
 * @desc    获取导航下拉列表
 * @name    getBdNavSelect()
 * @param   导航编码 $nav_pid
 * @param   要选中的项目的编号 $selected_no
 * @return  导航列表(html代码，附带选中状态)
 * @author  靳邦龙
 * @time    2017-10-04
 */
function getBdNavSelect($nav_pid='0', $selected_id) {

    $db_nav = M ( 'sys_nav' );
    $nav_map['nav_pid'] = $nav_pid;
    $nav_list = $db_nav->where ($nav_map)->field ( 'nav_name,nav_id' )->select ();
    $nav_options = "";
    foreach ( $nav_list as &$nav ) {
        $selected = "";
        if ($selected_no == $nav ['nav_id']) {
            $selected = "selected=true";
        }
        $nav_options .= "<option $selected value='" . $nav ['nav_id'] . "'>" . $nav ['nav_name'] . "</option>";
    }
    if (! empty ( $nav_options )) {
        return $nav_options;
    } else {
        return "<option value=''>无数据</option>";
    }
}
/**
 * @desc    获取广告位详情
 * @name    getLocationInfo()
 * @param   $location_id(广告位ID)
 * @param   $field(all:获取所有字段值)
 * @return  广告位详情
 * @author  王彬
 * @version 版本 V1.0.0
 * @time 2016-08-27
 */
function getLocationInfo($location_id, $field = 'all') {

	$db_location = M ( 'sys_ad_location' );
	$location_map['location_id'] = $location_id;
	if (! empty ( $location_id )) {
		if ($field == "all") {
			$location_data = $db_location->where ($location_map)->find ();
		} else {
			$location_data = $db_location->where ($location_map)->getField($field);
		}
	} 
	return $location_data;
}
/**
 * @desc    获取广告位列表
 * @name    getLocationList()
 * @return  广告位列表
 * @author  杨凯
 * @version 版本 V1.0.1
 * @time    2017-11-13
 * @update  
 */
function getLocationList() {

	$db_location = M("sys_ad_location");

	$confirm = 'onclick="if(!confirm(' . "'确认删除？当前广告位下的所有广告也将被删除！'" . ')){return false;}"';
	$location_data = $db_location->select();
	$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
	foreach ( $location_data as &$location ) {
	    $location ['add_staff'] = $staff_name_arr[$location ['add_staff']];
		$location ['ad_list'] = "<a href='" . U ( 'bd_ad_list', array (
				'location_id' => $location ['location_id'] 
		) ) . "'class='btn blue btn-xs btn-outline'>广告列表</a>";
		$location ['operate'] = "<a href='javascript:void(0)' class='btn blue btn-xs btn-outline' target='_self' onclick='select_receive_user(" . $location ['location_id'] . ")'><i class='fa fa-edit'></i>编辑</a>";
	}
	return $location_data;
}
/**
 * @desc    获取广告详情
 * @name    getAdInfo()
 * @param   $ad_id(广告ID)
 * @param   $field(all:获取所有字段值)
 * @return  广告详情
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-13
 */
function getAdInfo($ad_id, $field = "all") {

	$db_ad = M ( "sys_ad" );
	$ad_map['ad_id'] = $ad_id;
	if (! empty ( $ad_id )) {
		if ($field == "all") {
			$ad_data = $db_ad->where ($ad_map)->find ();
		} else {
			$ad_data = $db_ad->where ($ad_map)->getField($field);
		}
	} 
	return $ad_data;
}
/**
 * @desc    获取广告列表
 * @name    getAdList()
 * @param   $location_id(广告位ID)
 * @return  广告列表
 * @author  杨凯
 * @version 版本 V1.0.1
 * @time    2017-11-13
 * @update  
 */
function getAdList($location_id) {

	$db_ad = M ( "sys_ad" );

	$confirm = 'onclick="if(!confirm(' . "'确认删除？'" . ')){return false;}"';
	if (! empty ( $location_id )) {
		$ad_map['ad_location'] = $location_id;
		$ad_data = $db_ad->where ($ad_map)->select();
		foreach ( $ad_data as &$ad ) {
			$ad ['ad_img'] = getUploadInfo ( $ad ['ad_img'] );
			$ad ['add_staff'] = getStaffInfo ($ad ['add_staff']);
			$ad ['add_time'] =  getFormatDate($ad ['add_time'], 'Y-m-d');
			if (empty ( $ad ['ad_img'] )) {
				$ad ['ad_img'] = "---";
			} else {
				$ad ['ad_img'] = "<img src='" . $ad ['ad_img'] . "' width='100'>";
			}
			$ad ['operate'] = "<a onclick='add_ad(" . $ad ['ad_id'] . "," . $location_id . ")' style='text-decoration: none;' href='javascript:void(0);'class='layui-btn layui-btn-xs layui-btn-f60'><i class='fa fa-edit'></i> 编辑</a>";
		}
	}
	return $ad_data;
}

/**
 * @desc    获取banner列表
 * @name    getBannerList()
 * @param   $location_id(广告位ID)
 * @return  广告列表
 * @author
 * @version 版本 V1.0.1
 * @time    2016-08-27
 * @update  2016-09-23
 *         
 */
function getBannerList($location_id) {

	$db_ad = M ( "sys_ad" );
	$ad_map['ad_location'] = $location_id;
	if (! empty ( $location_id )) {
		$ad_data = $db_ad->where ($ad_map)->select ();
		foreach ( $ad_data as &$ad ) {
			$ad ['ad_img'] = getUploadInfo( $ad ['ad_img'] );
		}
	}
	return $ad_data;
}
/********************************门户相关业务层方法 结束*************************************/

?>