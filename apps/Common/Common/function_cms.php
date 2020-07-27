<?php

/********************************文章相关基础层方法 开始*************************************/
/**
 * @name    getArticleInfo()
 * @desc    获取文章详情
 * @param   $article_id(文章ID)
 * @param   $field(all:获取所有字段值)
 * @return  文章详情
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-08
 */
function getArticleInfo($article_id,$field='article_title'){
	$cms_article = M('cms_article');
	$article_map['article_id'] = $article_id;
	if($field == 'all'){
		$article_data = $cms_article->where($article_map)->find();
		return $article_data;
	}else{
		$article_data = $cms_article->where($article_map)->field($field)->find();
		return $article_data[$field];
	}
}

/**
 * @name    getArticleList()
 * @desc    获取当前栏目及下级栏目所有文章列表
 * @param   $cat_id(栏目ID)
 * @param   $keyword(搜索关键词),
 * @param   $start_time(搜索开始时间),
 * @param   $end_time(搜索结束时间)
 * @param   
 * @return  文章列表
 * @author  杨凯
 * @version 版本 V1.1.0
 * @time    2016-07-15
 * @update  2017/11/14
 */
 function getArticleList($cat_id=0,$page,$count,$keyword = null,$start_time = null,$end_time = null,$type = null){
 	$cms_article = M('cms_article');
 	$cms_article_category = M('cms_article_category');
	if(!empty($keyword)){ // 文章关键字查询
		$keyword_map['article_title'] = array('like','%'.$keyword.'%');
		$keyword_map['article_keyword'] = array('like','%'.$keyword.'%');
		$keyword_map['article_description'] = array('like','%'.$keyword.'%');
		$keyword_map['article_content'] = array('like','%'.$keyword.'%');
		$keyword_map['_logic'] = 'or';
	}
	if(!empty($keyword_map)){ // 拼接where条件
		$article_map['_complex'] = $keyword_map;
	}
	if(!empty($start_time) && !empty($end_time)){ // 文章时间段查询
		$start_time = $start_time.' 00:00:00';
		$end_time = $end_time.' 23:59:59';
		$article_map['_string'] = "add_time >= '$start_time' and add_time <= '$end_time' ";
	}
	// 分类查询
	$ids = "";
	if($cat_id){ 
		$cat_map['cat_id'] = $cat_id;
		$cat_map['cat_pid'] = $cat_id;
		$cat_map['_logic'] = 'or';
		$category_list = $cms_article_category->where($cat_map)->order('add_time desc')->select();
	}else{
	    $category_list = $cms_article_category->where("status = '1' and (cat_type = 4 or cat_type = 1)")->order('add_time desc')->select();
	}
	// if(!empty($category_list)){
		// foreach($category_list as $list){
			// $ids .= $list['cat_id'].",";
		// }
		// $ids = rtrim($ids,','); // 去除id字符串中的最后一个逗号
		// $article_map['article_cat'] = array('in',$ids);
	// } else {
		// if (!empty($cat_id)) {
			// $article_map['article_cat'] = $cat_id;
		// }
	// }
	if (!empty($count)) { // 分页查询
		if($type == '1'){
			$cms_article_sonlist = $cms_article->where($article_map)->order("article_view desc")->limit($page,$count)->select();
		}else{
			$cms_article_sonlist = $cms_article->where($article_map)->order("add_time desc")->limit($page,$count)->select();
		}
		$count = $cms_article->where($article_map)->count();
		
		$arr['data'] = $cms_article_sonlist;
		$arr['count'] = $count;
		return $arr;
	}else { // 统计条数
		$cms_article_sonlist = $cms_article->where($article_map)->order("add_time desc")->count();
		return $cms_article_sonlist;
	}
}


/**
 * @name    delArticle()
 * @desc    删除文章
 * @param   $article_id(文章ID) 
 * @return  true/false
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-14
 */
function delArticle($article_id){
	$cms_article = M('cms_article');
	$article_map['article_id'] = $article_id;
	$article_data = $cms_article->where($article_map)->delete();
	if($article_data){
		return true;
	}else{
		return false;
	}
}

/**
 * @name    getArticleCatInfo()
 * @desc    获取文章类型名称
 * @param   类型id   $cat_id
 * @param   字段值   $field（all:取全部字段）
 * @return  文章字段值/info
 * @author  杨凯
 * @time    2016-04-27
 * @update  2017-11-08
 */
function getArticleCatInfo($cat_id,$field='cat_name'){
	$cat_map['cat_id'] = $cat_id;
    if(!empty($cat_id )&&is_numeric($cat_id )){
    	$db_type=M('cms_article_category');
		if($field == 'all'){
			$cat_data=$db_type->where($cat_map)->find();
			if($cat_data){
				return $cat_data;
			}else{
				return "无此类型";
			}
		}else{
			$cat_name=$db_type->where($cat_map)->field($field)->find();
			if($cat_name){
				return $cat_name[$field];
			}else{
				return "无此类型";
			}
		}
    }else{
		return "无此类型";
	}
}

/**
 * @name    getArticleCatList()
 * @desc    获取文章标题
 * @param   $cat_ids  类型ids是多值
 * @param   $cat_pid文章父类id
 * @param   $status栏目状态
 * @return  获取文章类型列表json
 * @author  杨凯
 * @time    2017-11-08
 * @update 
 */
function getArticleCatList($cat_ids,$cat_pid=0,$status=1){
	$article_category = M("cms_article_category");
	if(!empty($cat_ids)){
		$cat_map['cat_id'] = array('in',$cat_ids);
		$cat_list = $article_category->where($cat_map)->order('add_time desc')->select();
		if($cat_list){
			return $cat_list;
		}else{
			return false;
		}
	} else if(!empty($cat_pid) || $cat_pid == 0){//获取文章所有栏目列表
		if($cat_pid != 0){//隐藏主题文章查询标题文章条件
			$cat_type = getTableInfo('cms_article_category', 'cat_id', $cat_pid , 'cat_type');
			$where['_string'] = "cat_pid=$cat_pid or cat_id=$cat_pid";
		}else {
			$where['cat_type'] = array(array('eq',1),array('eq',4),'or');
		}
		$where['status'] = array('eq',$status);
		$category_list =$article_category->where($where)->select();
		return $category_list;
	}
}

/**
* @name  getArticleCatChildNoselfNos()
* @desc  获取当前分类的所有下级文章分类（供getPartyChildNos调用）
* @param 当前分类id
* @return 所有下级分类的一维数组
* @author ljj
* @time   2017-10-16
*/
function getArticleCatChildNoselfNos($cat_pid){
	$article_category=M('cms_article_category');
	$cat_map['cat_pid'] = $cat_pid;
	$cat_list = $article_category->where($cat_map)->field('cat_id,cat_name,cat_type,cat_pid,status,add_staff,update_time')->select();
	if($cat_list){
		foreach($cat_list as $cat_val){
			$next_nos_arr=getArticleCatChildNoselfNos($cat_val['cat_id']);
			if($next_nos_arr){
				$cat_list=array_merge($cat_list,$next_nos_arr);
			}
		}
	}
	return $cat_list;
}

/**
 * @name    getArticleCatSelect()
 * @desc    获取文章类型下拉列表
 * @param   $cat_pid  类型父级id
 * @param   $self [是否包含本身0不包含，1包含]
 * @param   $selected_id 当前选中的类型id
 * @param   $cat_type 查询文章标题类型1为标题，2为单页栏目
 * @param   $status 查询文章类型0停用,1为启用
 * @return  带选中状态的文章类型下拉列表（HTML代码）
 * @author  杨凯
 * @time   2017-11-07
 */
function getArticleCatSelect($cat_pid=0,$self=1,$selected_id,$cat_type=1,$status=1){
    $db_type=M('cms_article_category');
    $cat_map['cat_type'] = $cat_type;
    $cat_map['status'] = $status;
    if(!empty($cat_pid)){
    	$category_map['cat_pid'] = $cat_pid;
    	$Lower_cat = checkCatChild($cat_pid);

    	if ($self==1 || !$Lower_cat || $cat_pid == 14) {
    		$category_map['cat_id'] = $cat_pid;
    		$category_map['_logic'] = 'or';
    	}
    }
    if(!empty($category_map)){
    	$cat_map['_complex'] = $category_map;
    }
    $cat_list=$db_type->where($cat_map)->field('cat_id,cat_name,cat_pid')->select();
    $cat_options="<option value='0'>--请选择--</option>";
    foreach($cat_list as &$type){
        $selected="";
        if($selected_id==$type['cat_id']){
            $selected="selected=true";
        }
        if ($type['cat_pid']=='0') {
        	$kg="├─";
        }else {
        	$kg="&nbsp;&nbsp;&nbsp;&nbsp;├─";
        }
        $cat_options.="<option $selected value='".$type['cat_id']."'>".$kg.$type['cat_name']."</option>";
    }
    if(!empty($cat_options)){
        return $cat_options;
    }else{
        return "<option value=''>无数据</option>";
    }
}

/**
 * @name    delArticleCat()
 * @desc    删除当前栏目
 * @param   $cat_id(栏目ID) 
 * @return  true/false
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-18
 */
function delArticleCat($cat_id){
	$cms_article_category = M('cms_article_category');
	$cat_map['cat_id'] = $cat_id;
	$article_category_data = $cms_article_category->where($cat_map)->delete();
	if($article_category_data){
		return true;
	}else{
		return false;
	}
}

/**
 * @name    getAffairsInfo()
 * @desc    获取三务公开文章详情
 * @param   $article_id(文章ID)
 * @param   $field(all:获取所有字段值)
 * @return  文章详情
 * @author  王彬
 * @version 版本 V1.0.0
 * @time    2016-08-09
 */
function getAffairsInfo($article_id,$field='article_title'){
	$cms_article = M('cms_affairs');
	$article_map['article_id'] = $article_id;
	if($field == 'all'){
		$article_data = $cms_article->where($article_map)->find();
		//$article_data['article_cat'] = getArticleCatInfo($article_data['article_cat'],'',1);
		return $article_data;
	}else{
		$article_data = $cms_article->where($article_map)->field($field)->find();
		return $article_data[$field];
	}
}

/**
 * @name    getAffairsList()
 * @desc    获取当前栏目及下级栏目所有文章列表----三务公开
 * @param   $cat_id(栏目ID)
 * @param   $keyword(搜索关键词),
 * @param   $start_time(搜索开始时间),
 * @param   $end_time(搜索结束时间)
 * @return  文章列表
 * @author  王彬
 * @version 版本 V1.1.0
 * @time    2016-07-15
 * @update  2016/08/13
 */
function getAffairsList($cat_id,$is_child,$page=0,$count=10,$keyword,$start_time,$end_time,$communist_no){
	$cms_article = M('cms_affairs');
	$cms_article_category = M('cms_affairs_category');
	$ccp_communist = M('ccp_communist');
	$where = " 1 = 1 ";
	if(!empty($keyword)){
		$where .= " and (article_title like '%$keyword%' or article_keyword like '%$keyword%' or article_description like '%$keyword%' or article_content like '%$keyword%')";
	}
	if(!empty($start_time) && !empty($end_time)){
		$where .= " and add_time >= '$start_time 00:00:00'  and add_time <= '$end_time 23:59:59' ";
	}
	if(empty($communist_no)){
		$staff_no = session('staff_no');
	}
	$article_list = array();
	$party = getCommunistInfo($communist_no,'party_no');
	$category_list = $cms_article_category->select();
	
	$category_list = getArticleCatList('',$cat_id,'','','',$type);
	$cms_article_sonlist = $cms_article->where("$where")->order("add_time desc")->select();
	foreach($cms_article_sonlist as &$sonlist){
		$article_list[] = $sonlist;
	}
	if($page > 0){
		$page_list = array();
		$i = 0;
		$start = ($page-1)*$count;
		$end = $page*$count-1;
		foreach($article_list as $list){
			if($start <= $i && $i <= $end){
				$page_list[] = $list;
			}
			$i++;
		}
		$article_list = $page_list;
	}
	//对数据结果做排序，根据修改时间倒叙
	foreach($article_list as $val){
		$key_arrays[]=$val['add_time'];
	}
	array_multisort($key_arrays,SORT_DESC,$article_list);
	return $article_list;
}

/**
 * @name    getArticleTopCatNos()
 * @desc   查询栏目的最上级栏目id
 * @param   $cat_id(栏目ID)
 * @return  最上级栏目id
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-18
 */
function getArticleTopCatNos($cat_id){
	$cms_article_category = M('cms_article_category');
	$cat_map['cat_id'] = $cat_id;
	$cat_pid = $cms_article_category->where($cat_map)->getField('cat_pid');
	if($cat_pid!='0'){
		$cat_id=getArticleTopCatNos($cat_pid);
	}
	return $cat_id;
}
/**
 * @name    checkCatChild()
 * @desc   查询栏目是否有下级
 * @param   $cat_id(栏目ID)
 * @return  返回true/false
 * @author  杨凯
 * @version 版本 V1.0.0
 * @time    2017-11-18
 */
function checkCatChild($cat_id){
	$cms_article_category = M('cms_article_category');
	$cat_map['cat_pid'] = $cat_id;
	$lower_count = $cms_article_category->where($cat_map)->count();
	if($lower_count>0){
		return true;
	}else {
		return false;
	}
	
}

/********************************文章相关基础层方法 结束*************************************/
?>