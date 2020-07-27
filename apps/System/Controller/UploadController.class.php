<?php
namespace System\Controller;
use Think\Controller;
use Common\Controller\BaseController;
class UploadController extends BaseController // 继承Controller类
{
    /**
     *
     * @name    upload_form
     * @desc    说明 文件上传的弹层页面(支持多图上传),需要文件上传时，使用layer弹出此页面,需要6个参数
     * @param   function（模块） 由父页面js方法 中的 地址栏直接传入(必需)
     * @param   type （类型） 由父页面js方法 中的 地址栏直接传入(必需)
     * @param   saveid_id string 父页面存放弹层返回的id字符串的input框的id值 (无值默认为0)
     * @param   savename_id string 父页面存放弹层返回的（原文件名）字符串的input框的id值 (无值默认为0)
     * @param   prev_id string 父页面(预览)弹层返回的文件的标记的id值 (无值默认为0)
     * @param   path_id string 父页面弹层返回的文件路径的标记的id值 (无值默认为0)
     * @param   rel int 是否刷新父页面 （1刷新，2不刷新）
     * @param   single int 单文件和多文件区分（0：单文件 1：多文件） 2016.4.27 新增
     * @param   upload_num=10 文件数量
     * @param   new_name   文件命名
     * @author  Jin BangLong
     * @time    2015.06.03
     */
    public function upload_form()
    {
        if (! isset($_GET["saveid_id"])) {
            $_GET["saveid_id"] = 0;
        }
        
        if (! isset($_GET["prev_id"])) {
            $_GET["prev_id"] = 0;
        }
        
        if (! isset($_GET["savename_id"])) {
            $_GET["savename_id"] = 0;
        }
        if (! isset($_GET["path_id"])) {
            $_GET["path_id"] = 0;
        }
		
		if (! isset($_GET["upload_num"])) {
            $num = 10;
        }else{
			$num = $_GET["upload_num"];
		}
        $this->assign("saveid_id", $_GET["saveid_id"]);
        $this->assign("savename_id", $_GET["savename_id"]);
        $this->assign("prev_id", $_GET["prev_id"]);
        $this->assign("path_id", $_GET["path_id"]);
        // 判断上传文件是单文件还是多文件
        if (I('get.single') == 0) {
            $single_img = 1;
        } else {
            $single_img = $num;
        }
        $this->assign("new_name", $_GET["new_name"]);
        $this->assign("single", $_GET["single"]);
        $this->assign("single_img", $single_img);
        $this->assign("rel", $_GET["rel"]);
        
        $this->assign("function",$_GET["function"]);//此处勿动
        //$this->assign("function", 'article');
        $this->assign("type", $_GET["type"]);
        $this->display();
    }

    /**
     *
     * @name    fileupload
     * @desc     说明 文件上传的服务器端方法
     * @param   function（模块） 直接从uploader页面（实例化时）server地址栏传入
     * @param   type （类型） 直接从uploader页面（实例化时）server地址栏传入
     * @return  $json 返回到页面的 json格式数据 ,包括:
     *          id 文件上传后存入数据库的ID值
     *          path 文件的保存路径
     *          filename 源文件名
     * @author  Jin BangLong
     * @time    2015.06.03
     */
    public function fileupload()
    {
        date_default_timezone_set("PRC"); // 设置时区
        $id = ""; // 需要返回的参数id，全局变量
        $path = ""; // 需要返回的参数path （路径），全局变量
        $filename = ""; // 需要返回的参数path （源文件名），全局变量
        $staff_no=session('staff_no');
        $upload_status=upFiles($_FILES["file"], $_GET['function'], $_GET['type'], $staff_no,$_GET['new_name'], $is_thumb);
        if($upload_status['status'] == 0){
            $status=$upload_status['status'];
            $msg=$upload_status['msg'];
        }else{
            $id=$upload_status['upload_id'];
            $filename=$upload_status['upload_source'];
            $path=$upload_status['upload_path'];
            $status=$upload_status['status'];
            $msg=$upload_status['msg'];
        }
        // 这句是重点，它告诉接收数据的对象此页面输出的是json数据；
        header('Content-type:text/json');
        // 虽然这行数据形式上是json格式，如果没有上面那句的话，它是不会被当做json格式的数据被处理的；
        $json = '{"id":"' . $id . '","path":"' . $path . '","filename":"' . $filename . '","status":"' . $status . '","msg":"' . $msg . '"}';
        echo $json;
        // $this->display();
    }
    /**
     *
     * @name    file_preview
     * @desc    上传文件预览
     * @param   idstr   id字符串
     * @author  Jin BangLong
     * @time    2015.06.03
     */
    public function file_preview()
    {
        if (isset($_GET["idstr"])) {
            $str = $_GET["idstr"];
            // strlen($str)-1
            // $str =substr($str,0,-1);//0,起始位置 -1，结束位置
            $_SESSION["str"] = $str;
            $idarr = explode("`", $str);
            $_SESSION['idarr'] = $idarr;
        } else {
            $idarr = $_SESSION['idarr'];
            $str = implode('`', $idarr);
        }
        $this->assign('idstr', $str);
        // 获取公文数据
        $db = M('bd_upload');
        
        foreach ($idarr as &$id) {
            $upload_map['upload_id'] = $id;
            $list = $db->where($upload_map)->find();
            $list['upload_path']=strtolower($list['upload_path']);//转小写20160709修改
            if ($list != null) {
                $file_list[] = $list;
            }
        }
        $tt_table = $file_list;
        $this->assign('tt_table', $tt_table);
        $this->assign('table_name',$_GET["table_name"]);
        $this->assign('table_field',$_GET["table_field"]);
        $this->assign('field_id',$_GET["id"]);
        $this->display();
    }

    /**
     * @name    upload_file_del
     * @desc    文件删除
     * @param   id    文件ID
     * @author  Jin BangLong
     * @time    2015.06.03
     */
    public function upload_file_del()
    {
        $bd_upload = M('bd_upload');
        $upload_map['upload_id'] = $_GET['upload_id'];
        $file_url = $bd_upload->where($upload_map)->getField('upload_path');
        $file_del = $bd_upload->where($upload_map)->delete();
        if ($file_del) {
            unlink('uploads/'.$file_url);
            ob_clean();$this->ajaxReturn(1);
        } else {
            ob_clean();$this->ajaxReturn(0);
        }
    }
    /**
     * @name    download_file
     * @desc    文件下载
     * @param   id    文件ID
     * @author  Jin BangLong
     * @time    2015.06.03
     */
   public function download_file(){
       $id=$_GET['id'];
        $file=getUploadInfo($id);
        if(is_file($file)){
            $length = filesize($file);
            $type = mime_content_type($file);
            $showname =  ltrim(strrc($file,'/'),'/');
            header("Content-Description: File Transfer");
            header('Content-type: ' . $type);
            header('Content-Length:' . $length);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $showname . '"');
            }
            ob_clean();
            readfile($file);
            exit;
        } else {
            exit('文件已被删除！');
        }
    }
    /**
     * @name    get_upload_html
     * @desc    获取下载文件显示
     * @param   id    文件ID
     * @author  Jin BangLong
     * @time    2015.06.03
     */
   public function get_upload_html(){
       $upload_ids=$_GET['ids'];
       $has_chakan=I('get.has_chakan',2);
       if ($upload_ids) {
           $ids = str_replace ( "`", ",", $upload_ids );
           $upload_map['upload_id'] = array('in',$ids);
           $count = M("bd_upload")->where ($upload_map)->count();
       }
       if($count){
           $html=getUploadHtml($upload_ids, $height = "100",$width=100,$has_chakan);
           $data['html']=$html;
           $data['num']=ceil($count/5)*100+30;
       }
       ob_clean();$this->ajaxReturn($data);
   }
}