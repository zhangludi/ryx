<?phpnamespace Iclock\Controller;use Think\Controller;ob_end_clean();header('Content-type: text/plain');header("HTTP/1.1 200 OK");class CdataController extends Controller{    public function index()    {        $attlog = M("oa_att_log");        //$ceshi=M("ceshi");        $hr_att_machine = M("oa_att_machine");        $hr_communist_bio = M("ccp_communist_bio");        $hr_communist = M("ccp_communist");        $oa_meeting = M("oa_meeting");        $SN = I("get.SN");        if (!empty($SN)) {            //获取考勤机传值信息            if (!empty(I("get.table"))) {                $table = I("get.table");                if ($table == "ATTLOG") {                    $attdata = array();                    $data = array();                    $getpoststr = file_get_contents("php://input");                    $attarr = explode("\n", $getpoststr);                    $meeting_no = M('oa_meeting')->where('machine_no = $SN')->getField('meeting_no');                    foreach ($attarr as $attval) {                        $attd = explode("\t", $attval);                        $attdata['att_no'] = $attd[0];                        if (!empty($attdata['att_no'])) {                            $attdata['att_date'] = getFormatDate($attd[1], "Y-m-d");                            $attdata['att_time'] = getFormatDate($attd[1], "H:i:s");                            $attdata['check_time'] = getFormatDate($attd[1], "Y-m-d H:i:s");                            $checkflag = $attlog->where("att_no='" . $attdata['att_no'] . "' and att_date='" . $attdata['att_date'] . "' and att_time='" . $attdata['att_time'] . "'")->find();                            $count = sizeof($checkflag);                            if ($count < 1) {                                $attdata['status'] = $attd[2];                                $attdata['att_manner'] = $attd[3];                                $attdata['att_machine'] = $SN;                                $attdata['att_relation_no'] = $meeting_no;                                $attdata['update_time'] = date("Y-m-d H:i:s");                                $attdata['add_time'] = date("Y-m-d H:i:s");                                $attdata['memo'] = $getpoststr;                                $save = $attlog->add($attdata);                                $i = $i + 1;                            }                        }                    }                    if ($i) {                        echo "OK";                    } else {                        //考勤机上传指令开始执行后修改命令状态字段                        $res1["command_status"] = "0";                        $res1['status'] = "1";                        $res1['update_time'] = date("Y-m-d H:i:s");                        $res1['add_time'] = date("Y-m-d H:i:s");                        $res1['memo'] = $getpoststr;                        $command_save = $hr_att_machine->where("machine_no = '$SN'")->save($res1);                        echo "OK";                    }//                    $data["memo"]=$getpoststr;//                    $data["memo1"]="0";//                    $save=$ceshi->add($data);                    //echo "OK";                }                //考勤机指纹信息存库（数据库人员表里不存在的人上传的指纹信息不会存库）                if ($table == "OPERLOG") {                    $i = 0;                    $data1 = array();                    $getpoststr1 = file_get_contents("php://input");                    //$getpoststr1=$GLOBALS['HTTP_RAW_POST_DATA'];                    $filename = 'file.txt';                    $filename1 = 'file1.txt';                    //$word = "你好!\r\nwebkaka";  //双引号会换行 单引号不换行                    file_put_contents($filename, $getpoststr1);                    //$data1["memo1"]=$getpoststr1;                    $attart1 = explode("\n", $getpoststr1);                    $attd1 = array();                    foreach ($attart1 as $data1) {                        $attd1 = explode("\t", $data1);                        $str_total = explode(" ", $attd1[0]);                        if ($str_total[0] == "FP") {                            //人员编号                            $communists = explode("=", $str_total[1]);                            //指纹编号                            $fingers = explode("=", $attd1[4]);                            //判断用户是否存在                            $communist_arr = $hr_communist->getField("communist_no", true);                            if (in_array($communists[1], $communist_arr)) {                                $res["communist_no"] = $communists[1];                                $res["bio_no"] = $fingers[1];                                //头标注                                $res["bio_type"] = $str_total[0];                                //执行状态                                $res["is_do_status"] = "1";                                $checkbio = $hr_communist_bio->where("communist_no='" . $res["communist_no"] . "' and bio_no='" . $res["bio_no"] . "' and bio_type='" . $res["bio_type"] . "'")->find();                                $count = sizeof($checkbio);                                if ($count < 1) {//                                                       $res["memo"]=$communists[1];//                                                       $res["memo1"]=$fingers[1];//                                                       $res["memo2"]=$str_total[0];                                    $add_bio = $hr_communist_bio->add($res);                                    $i = $i + 1;                                }                            }                        }                        if ($str_total[0] == "USER") {                            //人员编号                            $communists = explode("=", $str_total[1]);                            $res["memo"] = $communists[1];                            //人员姓名                            //将中文的格式由GB2312转化成UTF-8                            $content = iconv("gb2312", "utf-8//IGNORE", $attd1[1]);                            $names = explode("=", $content);                            $res["memo1"] = $names[1];                            //头标注                            //$res["memo2"]=$str_total[0];                            //部门编号                            $partys = explode("=", $attd1[5]);                            $res["memo2"] = $partys[1];                            //$add=$ceshi->add($res);                            $i = $i + 1;                        }                        //$res["memo"]=$attd1[0];                        //file_put_contents($filename1, $data1);//                                                 $res["memo1"]=$data1;                    }                    echo "OK:" . $i;                }            }            //考勤机请求配置信息获取            elseif (!empty(I("get.options"))) {                echo "GET OPTION FROM: $SN \nErrorDelay=35 \nDelay=5 \nTransTimes=00:00;14:05 \nTransInterval=1 \nTransFlag=TransData AttLog\tOpLog\tAttPhoto\tEnrollUser\tChgUser\tEnrollFP\tChgFP\nRealtime=1\nTimeout=60\nSyncTime=0\nEncrypt=0\nServerVer=IIS5+\nATTLOGStamp=0\nOPERLOGStamp=0\nATTPHOTOStamp=0";            } else {                echo "OK";            }        } else {            echo "无法获取考勤机编号";        }    }}