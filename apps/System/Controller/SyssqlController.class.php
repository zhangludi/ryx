<?php
namespace System\Controller;

// 命名空间
use Think\Controller;
use Common\Controller\BaseController;

class SyssqlController extends BaseController// 继承Controller类

{
    /***************************** 数据库管理开始 ************************/

    /**
     * @name  sys_sqlcheck_index()
     * @desc  数据库初始化首页
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_dbinit_index()
    {
        checkAuth(ACTION_NAME);
        $where['log_type'] = 6;
        $where['status'] = 1;
        if(IS_AJAX){
			$page = I('get.page');
			$limit = I('get.limit');
			$db_log = M('sys_log')->where($where)->field('log_id,log_newcontent,add_time,add_staff')->limit(($page-1)*$limit,$limit)->select();
			$count = M('sys_log')->where($where)->count();
			$staff_name_arr = M('hr_staff')->getField('staff_no,staff_name');
			foreach ($db_log as &$log) {
				$log['staff_name'] = $staff_name_arr[$log['add_staff']];
			}
			ob_clean();
			$db_log = array(
				'code'=>0,
				'msg'=>'',
				'count'=>$count,
				'data'=>$db_log
			);
			$this->ajaxReturn($db_log);
		}
		
        $this->display("Syssql/sys_dbinit_index");
    }

    /**
     * @name  sys_dbinit_do_save()
     * @desc  数据库初始执行
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_dbinit_do_save()
    {
        $col_sql_url = SITE_PATH.C('BASE_SQL_FILES_URL');//获取数据库更新sql
        $sql_field_content = file_get_contents($col_sql_url);//写自己的.sql文件
        $sql_arr = explode(';', $sql_field_content);
        $staff_no = session('staff_no');
        $staff_map['staff_no'] = $staff_no;
        $staff_name = M('hr_staff')->where($staff_map)->getField('staff_name');
        //执行sql语句
        M()->execute('set names utf8;'); //设置编码方式
        foreach ($sql_arr as $sql) {
            if(!empty($sql)){
                $result = M()->execute($sql.';');
            } else {
                $result = true;
            }
        }
        if ($result) {
            saveLog(ACTION_NAME,6,$staff_name."于2018-09-28 14:38:42对数据库进行了初始化操作",$staff_name."于2018-09-28 14:38:42对数据库进行了初始化操作");
            $data['code'] = 1;
            $data['msg'] = '初始化成功';
        } else {
            $data['code'] = 0;
            $data['msg'] = '初始化失败，请联系管理员';
        }
        $this->ajaxReturn($data);
    }

    /**
     * @name  sys_sqlcheck_index()
     * @desc  数据库校验首页
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_sqlcheck_index()
    {
        checkAuth(ACTION_NAME);
        $this->display("Syssql/sys_sqlcheck_index");
    }

    /**
     * @name  sys_sqlexport_json()
     * @desc  数据库校验首页
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_sqlexport_json()
    {
        $database = C('DB_NAME');//获取数据库名称、
        $prefix = C('DB_PREFIX');//获取表前缀
        $col_sql_url = SITE_PATH.C('COL_SQL_FILES_URL');//获取数据库更新sql
        $sql = 'show tables';
        $tables = M()->query($sql);
        $fileds = '';
        foreach($tables as $value){
            $filed = 'show full fields from '.$value['tables_in_'.$database];
            $table = M()->query($filed);
            foreach($table as $filed_info){
                $filed_info['table'] = $value['tables_in_'.$database];
                $fileds[] = $filed_info;
            }
        }
        $fileds = json_encode($fileds);
        $fileds = str_replace($prefix,"sp_",$fileds);
        $fp = fopen($col_sql_url, 'r+');
        fwrite($fp,'');     
        fwrite($fp, $fileds); 
        fclose($fp);
        showMsg('success', '更新成功', U('sys_sqlcheck_index'));
    }

    /**
     * @name  sys_sqlfield_update()
     * @desc  数据库校验更新
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_sqlfield_update()
    {
        $database = C('DB_NAME');//获取数据库名称、
        $prefix = C('DB_PREFIX');//获取表前缀
        $col_sql_url = SITE_PATH.C('COL_SQL_FILES_URL');//获取数据库更新sql
        $sql = 'show tables';
        $tables = M()->query($sql);
        $fileds = '';
        foreach($tables as $value){
            $filed = 'show full fields from '.$value['tables_in_'.$database];
            $table = M()->query($filed);
            foreach($table as $filed_info){
                $filed_info['table'] = $value['tables_in_'.$database];
                $fileds[] = $filed_info;
            }
        }
        $fileds = json_encode($fileds);
        $fileds = str_replace($prefix,"sp_",$fileds);
        $fp = fopen($col_sql_url, 'r+');
        fwrite($fp,'');     
        fwrite($fp, $fileds); 
        fclose($fp);
        showMsg('success', '更新成功', U('sys_sqlcheck_index'));
    }

    /**
     * @name  sys_sqlfield_check()
     * @desc  数据库校验
     * @param
     * @return
     * @author ljj
     * @addtime   2018-9-26 17:03:56
     * @updatetime  2018-9-26 17:04:03
     * @version v1.0.0
     */
    public function sys_sqlfield_check()
    {
        $database = C('DB_NAME');//获取数据库名称、
        $prefix = C('DB_PREFIX');//获取表前缀
        $col_sql_url = SITE_PATH.C('COL_SQL_FILES_URL');//获取数据库更新sql
        if(file_exists($col_sql_url)){
            $myfile = fopen($col_sql_url, "r");
            $fileds = fread($myfile,filesize($col_sql_url));
            fclose($myfile);
            $fileds = json_decode(str_replace("sp_",$prefix,$fileds),true);
            $field_sql = array();
            foreach($fileds as $key=>$value){
                if($value['table'] != $check_table){
                    $check_table_sql = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = "'.$database.'" AND table_name="'.$value['table'].'" limit 1';
                    $is_table = M()->query($check_table_sql);
                }
                $check_table = $value['table'];
                //查询字段长度，类型，是否存在e
                $column_sql = 'SELECT column_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = "'.$database.'" AND table_name="'.$value['table'].'" AND column_name="'.$value['field'].'"';
                $column_arr = M()->query($column_sql);

                if(empty($is_table)){ // 检查表是否存在
                    $value['code'] = 'table';
                    $value['msg'] = '缺少'.$value['table'].'数据表';
                    $field_sql[$value['table']] .= "`".$value['field']."` ".$value['type'];
                    if($value['null'] == 'NO'){
                        $field_sql[$value['table']] .= " NOT NULL ";
                    } else {
                        $field_sql[$value['table']] .= " DEFAULT NULL ";
                    }
                    if(!empty($value['comment'])){
                        $field_sql[$value['table']] .= "".$value['extra']." COMMENT '".$value['comment']."',";
                    } else {
                        $field_sql[$value['table']] .= "".$value['extra'].",";
                    }
                    if(!empty($value['key'])){
                        $pri_key = $value['field'];
                    }
                    $check_data[$value['table']] = $value;
                    $check_data[$value['table']]['sql'] .= $field_sql[$value['table']];
                    $check_data[$value['table']]['pri_key'] = $pri_key;
                } else if(empty($column_arr)){//是否存在字段
                    $value['code'] = 'empty_field';
                    $value['msg'] = '数据表'.$value['table'].'中'.$value['field'].'字段缺失';
                    $check_data[] = $value;
                }else{ // 字段数据类型是否匹配
                    $info = '';
                    foreach($column_arr as $column){
                        if($column['column_type'] != $value['type']){
                            $info = $value; 
                        }else{
                            $info = ''; 
                            break;
                        }
                    }
                    if(!empty($info)){
                        $info['code'] = 'field';
                        $info['msg'] = '数据表'.$value['field'].'字段数据类型不匹配';
                        $check_data[] = $info;
                    }
                } 
            }
        }
        // else{
        //     $check_data['code'] = 0;
        //     $check_data['msg'] = '校验标准文件缺失';
        // }
        if(!empty($check_data)){
            $num = 0;
            foreach ($check_data as $c_key => &$check) {
                $data[$num]['num'] = $num+1;
                $data[$num]['code'] = $check['code'];
                $data[$num]['field'] = $check['field'];
                $data[$num]['key'] = $check['pri_key'];
                if($check['code'] == 'table'){
                    $data[$num]['type_name'] = '缺少数据表';
                } else if($check['code'] == 'empty_field'){
                    $data[$num]['type_name'] = '缺少字段';
                } else {
                    $data[$num]['type_name'] = '数据类型不匹配';
                }
                $data[$num]['type'] = $check['type'];
                $data[$num]['sql'] = $check['sql'];
                $data[$num]['comment'] = $check['comment'];
                $data[$num]['table'] = $check['table'];
                $data[$num]['msg'] = $check['msg'];
                $data[$num]['operate'] = "<a class='btn yellow-p5 btn-xs btn-outline' onclick='one_repair(this)'><i class='fa fa-edit'></i>修复</a>" ;
                $num++;
            }
        } else {
            $data = [];
        }
        $count = count($data);
        ob_clean();
		$data = array(
			'code'=>0,
			'msg'=>'',
			'count'=>$count,
			'data'=>$data
		);
		$this->ajaxReturn($data);
    }

    /**
    * @name         sys_mysql_field_repair()
    * @desc         数据表修复
    * @param        
    * @return       
    * @author       ljj
    * @addtime      2018-9-27 15:20:44
    * @updatetime   2018-9-27 15:20:48
    * @version      V1.0.0
    **/
    public function sys_mysql_field_repair(){
        $param = $_POST;
        if(!empty($param)){
            $database = C('DB_NAME');//获取数据库名称、
            $prefix = C('DB_PREFIX');//获取表前缀
            $col_sql_url = SITE_PATH.C('COL_SQL_FILES_URL');//获取数据库更新sql
            foreach($param['data'] as $fileds){

                if($fileds['code'] == 'table'){ // 没有数据表 创建数据表
                    $sql = 'CREATE TABLE `'.$fileds['table'].'` ( '.$fileds['sql'];
                    if($fileds['key']){
                        $sql .= ' PRIMARY KEY (`'.$fileds['key'].'`) USING BTREE';
                    } 
                    $sql .= ') ENGINE=INNODB DEFAULT CHARSET=utf8;';
                    $result = M()->execute($sql);
                }
                if($fileds['code'] == 'empty_field'){ // 没有数据表子字段 创建数据表字段
                    $sql = 'alter table '.$fileds['table'].' add '.$fileds['field'].' '.$fileds['type'] .' comment "'.$fileds['comment'].'"';
                    $result = M()->execute($sql);
                }
                if($fileds['code'] == 'field'){ // 字段数据类型不匹配 修改字段的数据类型
                    $field_sql = 'alter table '.$fileds['table'].' modify column '.$fileds['field'].' '.$fileds['type'] .';';
                    $result = M()->execute($field_sql);
                }
            }
            if($result || $result == 0){
                $data['code'] = 1;
                $data['msg'] = '更新成功';
            } else {
                $data['code'] = 0;
                $data['msg'] = '更新失败，请重试';
            }
        }else{
            $data['code'] = 0;
            $data['msg'] = '暂无需要修复的内容';
        }
        $this->ajaxReturn($data);
    }
}
