<?php
namespace Iclock\Controller;
use Think\Controller;
ob_end_clean(); 
header("HTTP/1.1 200 OK");
header('Content-type: text/plain');
header("Connection: close");
header("Cache-Control: no-store");
header("Pragma: no-cache");
class DevicecmdController extends Controller {
    public function index(){
	//$ceshi=M("ceshi");
        $hr_att_machine=M("oa_att_machine");
        $hr_communist_bio=M("ccp_communist_bio");
        $hr_communist=M("ccp_communist");
        //是否接收到考勤机编号
        $SN=I("request.SN");
        if(!empty($SN)){
            $getpoststr=file_get_contents("php://input");
            $str=$GLOBALS['HTTP_RAW_POST_DATA']; 		 
            $data["memo"]=$getpoststr."\n";
            //$add=$ceshi->add($data);
            //考勤机上传指令开始执行后修改命令状态字段
            $res1["command_status"]="0";
            $res1['status']="1";
            $res1['update_time']=date("Y-m-d H:i:s");
            $res1['add_time']=date("Y-m-d H:i:s");
            $res1['memo']=$getpoststr;
            $command_save=$hr_att_machine->where("machine_no = '$SN'")->save($res1);
            //echo "OK:".strlen($getpoststr);
            $toulen=strlen($getpoststr);
            header("Date: ".gmdate("l jS \of F Y h:i:s A","GMT"));
            header("Content-Length:2");
            $communist_num=$hr_communist->count();
            $bio_num=$hr_communist_bio->count();
            $nums=$communist_num+$bio_num;
            echo "OK";
            //echo "OK:".strlen($getpoststr)."\n";
            //echo "OK:".$nums."\n";
        }
//        header("Connection:keep-alive");
//        header("Content-Length: strlen($getpoststr)");
//        header("User-Agent: libghttp/1.0");
//        echo "OK\n";
    }    
}