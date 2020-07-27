<?php
/***************************** 转化类方法开始************************/
/**
 * @name    showMsg()
 * @desc    显示相应的提示内容,$this->success方法只能在控制器中使用
 * @param   $msgtype 提示类型（success，error)        	
 * @param   $msg 提示内容        	
 * @param   $url 跳转链接        	
 * @param   $jump_model 1是刷新父页面  2是父页面跳转  3是二级父页面跳转引用审批页面的跳转  默认本页面跳转       	
 * @return  需要显示的提示内容
 * @author  靳邦龙
 * @time    2016-04-19
 */
/**
 * [showMsg description]
 * @author liubingtao
 * @version 1.0.0
 * @addtime 2018-01-24T14:45:20+0800
 * @param   [type]
 * @param   [type]
 * @param   [type]
 * @param   [type]
 * @return  [type]
 */
function showMsg($msgtype, $msg, $url, $jump_model) {
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' /><script  charset='utf-8' charset='utf-8'>alert('$msg');</script>";
	if ($msgtype == 'error') {
		echo "<script>history.back();</script>";
		die ();
	} elseif ($msgtype == 'success') {
		
		switch ($jump_model) {
			case 1:
				echo "<script>parent.location.reload();</script>";
				die ();
			break;
			case 2:
		   		echo "<script>parent.location.href='$url';</script>";
		   		die ();
			break;
			case 3:
				echo "<script>parent.parent.location.href='$url';</script>";
				die ();
				break;
			default:
				echo "<script>window.location.href='$url';</script>";
				die ();
			break;
		}
	}
}

/**
 * @desc    全角字符转半角字符（中文转英文字符）
 * @name    signCn2En()
 * @param   string $str 要转换的字符串
 * @return  string $str 装换后的半角字符串
 * @date    2016-08-30
 * @author  王玮琪
 */
function signCn2En($str) {
	$arr = array (
			'０' => '0',
			'１' => '1',
			'２' => '2',
			'３' => '3',
			'４' => '4',
			'５' => '5',
			'６' => '6',
			'７' => '7',
			'８' => '8',
			'９' => '9',
			'Ａ' => 'A',
			'Ｂ' => 'B',
			'Ｃ' => 'C',
			'Ｄ' => 'D',
			'Ｅ' => 'E',
			'Ｆ' => 'F',
			'Ｇ' => 'G',
			'Ｈ' => 'H',
			'Ｉ' => 'I',
			'Ｊ' => 'J',
			'Ｋ' => 'K',
			'Ｌ' => 'L',
			'Ｍ' => 'M',
			'Ｎ' => 'N',
			'Ｏ' => 'O',
			'Ｐ' => 'P',
			'Ｑ' => 'Q',
			'Ｒ' => 'R',
			'Ｓ' => 'S',
			'Ｔ' => 'T',
			'Ｕ' => 'U',
			'Ｖ' => 'V',
			'Ｗ' => 'W',
			'Ｘ' => 'X',
			'Ｙ' => 'Y',
			'Ｚ' => 'Z',
			'ａ' => 'a',
			'ｂ' => 'b',
			'ｃ' => 'c',
			'ｄ' => 'd',
			'ｅ' => 'e',
			'ｆ' => 'f',
			'ｇ' => 'g',
			'ｈ' => 'h',
			'ｉ' => 'i',
			'ｊ' => 'j',
			'ｋ' => 'k',
			'ｌ' => 'l',
			'ｍ' => 'm',
			'ｎ' => 'n',
			'ｏ' => 'o',
			'ｐ' => 'p',
			'ｑ' => 'q',
			'ｒ' => 'r',
			'ｓ' => 's',
			'ｔ' => 't',
			'ｕ' => 'u',
			'ｖ' => 'v',
			'ｗ' => 'w',
			'ｘ' => 'x',
			'ｙ' => 'y',
			'ｚ' => 'z',
			'（' => '(',
			'）' => ')',
			'〔' => '[',
			'〕' => ']',
			'【' => '[',
			'】' => ']',
			'〖' => '[',
			'〗' => ']',
			'“' => '[',
			'”' => ']',
			'‘' => '[',
			'’' => ']',
			'｛' => '{',
			'｝' => '}',
			'《' => '<',
			'》' => '>',
			'％' => '%',
			'＋' => '+',
			'—' => '-',
			'－' => '-',
			'～' => '-',
			'：' => ':',
			'。' => '.',
			'、' => ',',
			'，' => '.',
			'、' => '.',
			'；' => ',',
			'？' => '?',
			'！' => '!',
			'…' => '-',
			'‖' => '|',
			'”' => '"',
			'’' => '`',
			'‘' => '`',
			'｜' => '|',
			'〃' => '"',
			'　' => ' ' 
	);
	
	return strtr ( $str, $arr );
}
/**汉字处理相关公共方法开始************************************************************/
/**
 * @name    getFirstCharMul
 * @desc    取多个汉字首字母
 * @param   $zh 汉字字符串
 * @param   $format='lower'  格式   lower小写，upper大写
 * @return  string   首字母
 * @author  靳邦龙
 * @time    2017-06-17
 */
function getFirstCharMul($zh,$format='lower'){
    $ret = "";
    $s1 = iconv("UTF-8","gb2312", $zh);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $zh){$zh = $s1;}
    for($i = 0; $i < strlen($zh); $i++){
        $s1 = substr($zh,$i,1);
        $p = ord($s1);
        if($p > 160){
            $s2 = substr($zh,$i++,2);
            $ret .= getFirstChar($s2);
        }else{
            $ret .= $s1;
        }
    }
    if($format=='lower'){
        return  strtolower($ret);
    }else{
        return  strtoupper($ret);
    }
}
/**
 * @name    getFirstChar
 * @desc    取单个汉字首字母
 * @param   $str 汉字字符串
 * @return  string   大写的首字母
 * @author  靳邦龙
 * @time    2017-06-17
 */
function getFirstChar($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}
/**汉字转拼音公共方法开始*******************************************/
/**
 * @name    getPinyin
 * @desc    汉字转拼音
 * @param   $_String 汉字字符串
 * @param   $_Code 编码格式    gb2312   或    UTF-8
 * @param   $format='lower'  格式   lower小写，upper大写
 * @return  string
 * @author  靳邦龙
 * @time    2017-06-17
 */
function getPinyin($_String,$format='lower', $_Code='UTF-8'){

    $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".

        "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".

        "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".

        "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".

        "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".

        "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".

        "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".

        "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".

        "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".

        "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".

        "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".

        "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".

        "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".

        "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".

        "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".

        "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

    $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".

        "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".

        "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".

        "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".

        "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".

        "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".

        "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".

        "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".

        "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".

        "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".

        "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".

        "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".

        "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".

        "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".

        "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".

        "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".

        "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".

        "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".

        "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".

        "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".

        "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".

        "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".

        "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".

        "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".

        "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".

        "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".

        "|-10270|-10262|-10260|-10256|-10254";

    $_TDataKey   = explode('|', $_DataKey);

    $_TDataValue = explode('|', $_DataValue);

    $_Data = array_combine($_TDataKey,  $_TDataValue);

    arsort($_Data);

    reset($_Data);

    if($_Code!= 'gb2312') $_String = _U2_Utf8_Gb($_String);

    $_Res = '';

    for($i=0; $i<strlen($_String); $i++){

        $_P = ord(substr($_String, $i, 1));

        if($_P>160){

            $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536;

        }

        $_Res .= _Pinyin($_P, $_Data);

    }

    $ret = preg_replace("/[^a-z0-9]*/", '', $_Res); //???????
    if($format=='lower'){
        return  strtolower($ret);
    }else{
        return  strtoupper($ret);
    }
}
/**
 * @name    _Pinyin
 * @desc    仅供getPinyin方法调用
 * @author  靳邦龙
 * @time    2017-06-17
 */
function _Pinyin($_Num, $_Data){

    if($_Num>0 && $_Num<160 ){

        return c($_Num);

    }elseif($_Num<-20319 || $_Num>-10247){

        return '';

    }else{

        foreach($_Data as $k=>$v){ if($v<=$_Num) break; }

        return $k;

    }

}
/**
 * @name    _U2_Utf8_Gb
 * @desc    仅供getPinyin方法调用
 * @author  靳邦龙
 * @time    2017-06-17
 */
function _U2_Utf8_Gb($_C){

    $_String = '';

    if($_C < 0x80){

        $_String .= $_C;

    }elseif($_C < 0x800){

        $_String .= c(0xC0 | $_C>>6);

        $_String .= c(0x80 | $_C & 0x3F);

    }elseif($_C < 0x10000){

        $_String .= c(0xE0 | $_C>>12);

        $_String .= c(0x80 | $_C>>6 & 0x3F);

        $_String .= c(0x80 | $_C & 0x3F);

    }elseif($_C < 0x200000){

        $_String .= c(0xF0 | $_C>>18);

        $_String .= c(0x80 | $_C>>12 & 0x3F);

        $_String .= c(0x80 | $_C>>6 & 0x3F);

        $_String .= c(0x80 | $_C & 0x3F);

    }

    return iconv('UTF-8','gbk',$_String);

}
/**
 * @name    toCnNumSimplified
 * @desc    字符串转简体中文数字
 * @param   $num  阿拉伯数字
 * @return  简体中文数字
 * @author  张春山
 * @time    2017-05-17
 * @version V1.0.0
 **/
function toCnNumSimplified($num){
    $char = array("零","一","二","三","四","五","六","七","八","九");
    $dw = array("","十","百","千","万","亿","兆");
    $retval = "";
    $proZero = false;
    for($i = 0;$i < strlen($num);$i++){
        if($i > 0)    $temp = (int)(($num % pow (10,$i+1)) / pow (10,$i));
        else $temp = (int)($num % pow (10,1));
        if($proZero == true && $temp == 0) continue;
        if($temp == 0) $proZero = true;
        else $proZero = false;
        if($proZero){
            if($retval == "") continue;
            $retval = $char[$temp].$retval;
        }
        else $retval = $char[$temp].$dw[$i].$retval;
    }
    if($retval == "一十") $retval = "十";
    return $retval;
}
/**
 * @name    toCnNumTraditional
 * @desc    阿拉伯数字金额转汉字大写金额
 * @param   $money 阿拉伯数字金额
 * @return  汉字大写金额
 * @author  靳邦龙
 * @time    2017-05-17
 * @version V1.0.0
 **/
function toCnNumTraditional($money){
    $money = round($money,2);
    $cnynums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
    $cnyunits = array("圆","角","分");
    $cnygrees = array("拾","佰","仟","万","拾","佰","仟","亿");
    list($int,$dec) = explode(".",$money,2);
    $dec = array_filter(array($dec[1],$dec[0]));
    $ret = array_merge($dec,array(implode("",cnyMapUnit(str_split($int),$cnygrees)),""));
    $ret = implode("",array_reverse(cnyMapUnit($ret,$cnyunits)));
    return str_replace(array_keys($cnynums),$cnynums,$ret);
}
/**
 * @name    cnyMapUnit
 * @desc    仅供toCnNumTraditional方法调用
 * @author  靳邦龙
 * @time    2017-05-17
 * @version V1.0.0
 **/
function cnyMapUnit($list,$units) {
    $ul=count($units);
    $xs=array();
    foreach (array_reverse($list) as $x) {
        $l=count($xs);
        if ($x!="0" || !($l%4))
            $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            else $n=is_numeric($xs[0][0])?$x:'';
            array_unshift($xs,$n);
    }
    return $xs;
}
/**汉字转拼音公共方法结束*************************************************************************/
/**
 * @desc    空格转换英文逗号（全角或者半角都可以）
 * @name    empty2comma()
 * @param   string $str 要转换的字符串
 * @return  string $str 装换后的半角字符串
 * @date    2017-05-11
 * @author  王飞
 */
function empty2comma($str) {
	$str = str_replace ( '%20', ' ', $str ); // 替换全角空格为半角
	$str = str_replace ( '　', ' ', $str ); // 替换全角空格为半角
	$str = str_replace ( '  ', ' ', $str ); // 替换连续的空格为一个
	$noe = false; // 是否遇到不是空格的字符
	for($i = 0; $i < strlen ( $str ); $i ++) { // 遍历整个字符串
		if ($noe && $str [$i] == ' ')
			$str [$i] = ','; // 如果当前这个空格之前出现了不是空格的字符
		elseif ($str [$i] != ' ')
			$noe = true; // 当前这个字符不是空格，定义下 $noe 变量
	}
	return $str;
}
/**
 * @name    strToArr()
 * @desc    字符串转换为数组
 * @param   string $str 要分割的字符串
 * @param   string $glue 分割符
 * @return  array
 * @author  靳邦龙
 */
function strToArr($str, $glue = ',') {
    if(!empty($str)){
        $array = explode ( $glue, $str );
        return $array;
    } else {
        return $str;
    }
	
}

/**
 *
 * @name    arrToStr()
 * @desc    数组转换为字符串并去除重复数据，主要用于把分隔符调整到第二个参数
 * @param   array $arr 要连接的数组
 * @param   string $glue 分割符
 * @return  string
 * @author  靳邦龙
 */
function arrToStr($arr, $glue = ',') {
	$string = implode ( $glue, $arr );
	// if str_pos
	return $string;
}

/**
 * @desc    字符串去重复方法
 * @name    strUnique()
 * @param   string $str 	要连接的数组
 * @param   string $glue 分割符
 * @author  靳邦龙
 * @addtime 2016年12月23日
 * @version V1.0.0
 */
function strUnique($str, $glue = ",") {
	$array = strToArr ( $str, $glue );
	$array = array_unique ( $array );
	$str = arrToStr ( $array, $glue );
	return $str;
}
/**
 * @desc    去除HTML标签
 * @name    removeHtml()
 * @param   $content
 * @return  str
 * @author  王彬
 * @version 版本 V1.0.0
 * @time 2016-11-24
 */
function removeHtml($content) {
	$content = preg_replace ( "/<a[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/a>/i", '', $content );
	$content = preg_replace ( "/<div[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/div>/i", '', $content );
	$content = preg_replace ( "/<font[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/font>/i", '', $content );
	$content = preg_replace ( "/<p[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/p>/i", '', $content );
	$content = preg_replace ( "/<span[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/span>/i", '', $content );
	$content = preg_replace ( "/<\?xml[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/\?xml>/i", '', $content );
	$content = preg_replace ( "/<o:p[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/o:p>/i", '', $content );
	$content = preg_replace ( "/<u[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/u>/i", '', $content );
	$content = preg_replace ( "/<b[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/b>/i", '', $content );
	$content = preg_replace ( "/<meta[^>]*>/i", '', $content );
	$content = preg_replace ( "/<\/meta>/i", '', $content );
	$content = preg_replace ( "/<!--[^>]*-->/i", '', $content ); // 注释内容
	$content = preg_replace ( "/<p[^>]*-->/i", '', $content ); // 注释内容
	$content = preg_replace ( "/style=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/class=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/id=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/lang=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/width=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/height=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/border=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/face=.+?['|\"]/i", '', $content ); // 去除样式
	$content = preg_replace ( "/face=.+?['|\"]/", '', $content );
	$content = preg_replace ( "/face=.+?['|\"]/", '', $content );
    $content = preg_replace ( "/<img[^>]*>/i", '', $content );
    $content = preg_replace ( "/<\/img>/i", '', $content );
    $content = preg_replace ( "/<table [^>]*>/i", '', $content );
    $content = preg_replace ( "/<\/table>/i", '', $content );
    $content = preg_replace ( "/<tbody[^>]*>/i", '', $content );
    $content = preg_replace ( "/<\/tbody>/i", '', $content );
    $content = preg_replace ( "/<tr [^>]*>/i", '', $content );
    $content = preg_replace ( "/<\/tr>/i", '', $content );
    $content = preg_replace ( "/<td [^>]*>/i", '', $content );
    $content = preg_replace ( "/<\/td>/i", '', $content );
    $content = preg_replace ( "/<tr>/i", '', $content );
	$content = str_replace ( "&nbsp;", "", $content );
	return $content;
}

/**
 * @desc    二维数组去重
 * @name    getAssocUnique()
 * @param   $arr二维数组 $key 去重的字段
 * @return  array
 * @author  yangluhai
 * @time    2016年9月2日上午10:47:03
 * @update  2016年9月2日上午10:47:03
 * @version 0.01
 */
function getAssocUnique($arr, $key) {
	$tmp_arr = array ();
	foreach ( $arr as $k => $v ) {
		if (in_array ( $v [$key], $tmp_arr )) {// 搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
			unset ( $arr [$k] );
		} else {
			$tmp_arr [] = $v [$key];
		}
	}
	sort ( $arr ); // sort函数对数组进行排序
	return $arr;
}

/**
 * @desc    获取指定时间的指定格式
 * @name    getFormatDate()
 * @param   指定的时间 $time        	
 * @param   格式 $format        	
 * @return  指定时间的指定格式
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getFormatDate($time, $format) {
	if (empty ( $time )) {
		$time = date ( "Y-m-d H:i:s" );
	}
	if (empty ( $format )) {
		$format = "Y-m-d";
	}
	$strtime = strtotime ( $time ); // 转时间戳
	$date = date ( $format, $strtime );
	return $date;
}
/**
 * @desc    生成从开始月份到结束月份的月份数组
 * @name    getMonthList
 * @param   $start_time 开始时间        	
 * @param   $end_time 结束时间        	
 * @return  array()
 * @author  靳邦龙
 * @time    2017-05-20
 */
function getMonthList($start_time, $end_time) {
	$start = getFormatDate ( $start_time, 'Y-m' );
	if (empty ( $end_time )) {
		$end_time = date ( "Y-m-d" );
	}
	$end = getFormatDate ( $end_time, 'Y-m' );
	// 转为时间戳
	$start = strtotime ( $start . '-01' );
	$end = strtotime ( $end . '-01' );
	$i = 0;
	$d = array ();
	while ( $start <= $end ) {
		// 这里累加每个月的的总秒数 计算公式：上一月1号的时间戳秒数减去当前月的时间戳秒数
		$d [$i] = trim ( date ( 'Y-m', $start ), ' ' );
		$start += strtotime ( '+1 month', $start ) - $start;
		$i ++;
	}
	return $d;
}
/**
 * @desc    获取指定时间的指定格式
 * @name    getTimePoint()
 * @param   计算方式：$calculate_way plus：加，minus：减
 * @param   计算的周期类型： $cycle_type 
 *              seconds：秒，minutes：分钟，hours：小时，days：天，week：周，months：月，years：年
 * @param   周期数量：$cycle_num（整数）
 * @param   返回格式：$format (默认:Y-m-d)
 * @param   开始时间：$start_time 计算的开始时间（默认当前时间：time()）
 * @return  指定时间的指定格式 默认返回从当后时间向前推算1天的时间
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getTimePoint($calculate_way = 'plus', $cycle_type = 'days', $cycle_num = '1', $format = 'Y-m-d', $start_time) {
	if ($calculate_way == 'plus') {
		$way = '+' . $cycle_num . $cycle_type; // eg:+5 day
	} elseif ($calculate_way == 'minus') {
		$way = '-' . $cycle_num . $cycle_type; // eg:-5 day
	}
	
	return date ( $format, strtotime ( "$start_time $way " ) );
	// echo "下个星期四:",date("Y-m-d",strtotime("next Thursday")), "<>";
	// echo "上个周一:".date("Y-m-d",strtotime("last Monday"))."<>";
	// echo "一个月前:".date("Y-m-d",strtotime("last month"))."<>";
	// echo "一个月后:".date("Y-m-d",strtotime("+1 month"))."<>";
	// echo "十年后:".date("Y-m-d",strtotime("+10 year"))."<>";
}
/**
 * @name  getMonthLastDay()
 * @desc  获取某个月的第一天和最后一天的日期
 * @param 某个时间点： $time
 * @return array('start_day'=>'第一天','last_day'=>'最后一天')
 * @author 靳邦龙
 * @time   2017-05-27
 */
function getMonthLastDay($time){
    $arr['first_day']=date('Y-m-01',strtotime($time));
    $arr['last_day']=date('Y-m-t',strtotime($time));
    return $arr;
}
/**
 * @desc    截取汉字字符串
 * @name    getStrCut()
 * @param   $str 需要截取的字符串
 * @param   $num 截取的字符串数量
 * @return  截取后的字符串
 * @author  靳邦龙
 * @time    2016-05-23
 */
function getStrCut($str, $num) {
	$cut_str = mb_substr ( $str, 0, $num, 'utf-8' );
	return $cut_str;
}
/**
 * @desc    获取excel表格中的时间（导入、导出专用）
 * @name    getExcelTime()
 * @param   $date
 * @author  靳邦龙
 * @time    2016-11-03
 */
function getExcelTime($date) {
	$date = date ( 'Y-m-d H:i:s', ($date - 25569) * 24 * 60 * 60 );
	$date = getTimePoint ( 'minus', 'hours', 8, 'Y-m-d H:i:s', $date );
	return $date;
}
/**
 * @desc    格式化商品价格
 * @name    getFormatPrice()
 * @param   float $price 商品价格
 *        	1保留不为 0 的尾数 ， 2不四舍五入，保留1位，3直接取整，4四舍五入，保留 1 位，5先四舍五入，不保留小数
 * @param   int $price_format 价格格式
 * @param   string $currency_format 返回参数类型 '%s'代表字符串，
 * @return  string 格式化的价格 默认返回 00.00格式，若带钱币符号，如'￥00.00'，需设置$currency_format='￥%s'
 * @author  靳邦龙
 * @time    2016-11-03
 */
function getFormatPrice($price = 0, $price_format = 0, $currency_format = '%s') {
	switch ($price_format) {
		case 0 :
			$price = number_format ( $price, 2, '.', '' );
			break;
		case 1 : // 保留不为 0 的尾数
			$price = preg_replace ( '/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format ( $price, 2, '.', '' ) );
			if (substr ( $price, - 1 ) == '.') {
				$price = substr ( $price, 0, - 1 );
			}
			break;
		case 2 : // 不四舍五入，保留1位
			$price = substr ( number_format ( $price, 2, '.', '' ), 0, - 1 );
			break;
		case 3 : // 直接取整
			$price = intval ( $price );
			break;
		case 4 : // 四舍五入，保留 1 位
			$price = number_format ( $price, 1, '.', '' );
			break;
		case 5 : // 先四舍五入，不保留小数
			$price = round ( $price );
			break;
	}
	return sprintf ( $currency_format, $price );
}

/**
 * @desc    导出数据为excel表格
 * @name    exportExcel($fileName,$headArr,$data)
 * @param   $fileName 下载的文件名
 *        	$headArr excel的第一行标题,一个数组,如果为空则没有标题
 *        	$data 一个二维数组,结构如同从数据库查出来的数组
 * @return
 * @author  王彬
 * @time    2016-06-21
 */
function exportExcel($fileName, $headArr, $data) {
	// 导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
	import ( "Org.Util.PHPExcel" );
	import ( "Org.Util.PHPExcel.Writer.Excel5" );
	import ( "Org.Util.PHPExcel.IOFactory.php" );
	
	$date = date ( "Y_m_d", time () );
	$fileName .= "_{$date}.xls";
	
	// 创建PHPExcel对象，注意，不能少了\
	$objPHPExcel = new \PHPExcel ();
	$objProps = $objPHPExcel->getProperties ();
	
	// 设置表头
	$key = ord ( "A" );
	// print_r($headArr);exit;
	foreach ( $headArr as $v ) {
		$colum = chr ( $key );
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $colum . '1', $v );
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( $colum . '1', $v );
		$key += 1;
	}
	
	$column = 2;
	$objActSheet = $objPHPExcel->getActiveSheet ();
	
	// print_r($data);exit;
	foreach ( $data as $key => $rows ) { // 行写入
		$span = ord ( "A" );
		foreach ( $rows as $keyName => $value ) { // 列写入
			$j = chr ( $span );
			$objActSheet->setCellValue ( $j . $column, $value );
            //$objActSheet->setCellValueExplicit($j.$column,$value,PHPExcel_Cell_DataType::TYPE_STRING);
            $objActSheet->setCellValueExplicit($j.$column,$value,PHPExcel_Cell_DataType::TYPE_STRING);
            //$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//文字居中
			$span ++;
		}
		$column ++;
	}
	
	$fileName = iconv ( "utf-8", "gb2312", $fileName );
	// 重命名表
	// $objPHPExcel->getActiveSheet()->setTitle('test');
	// 设置活动单指数到第一个表,所以Excel打开这是第一个表
	$objPHPExcel->setActiveSheetIndex ( 0 );
	ob_end_clean ();
	header ( 'Content-Type: application/vnd.ms-excel;charset=utf-8' );
	header ( "Content-Disposition: attachment;filename=\"$fileName\"" );
	header ( 'Cache-Control: max-age=0' );
	
	$objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
	$objWriter->save ( 'php://output' ); // 文件通过浏览器下载
	exit ();
}

/**
 * @name    exportWord($html)
 * @todo    页面生成word文档
 * @param   $html:页面内容 $name
 *        	默认取时间
 * @return  boolean
 */
function exportWord($html, $name) {
	if (empty ( $name )) {
		$name = time ();
	}
	ob_start ();
	echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">';
	echo $html;
	echo "<html>";
	
	$data = ob_get_contents ();
	$path = "status/word/" . $name . ".doc";
	file_put_contents ( "status/word/" . $name . ".doc", $data );
	ob_end_clean ();
	
	return $path;
}

/***************************** 转换类方法结束************************/

/***************************** 验证类方法开始************************/

/**
 * 方法名：checkLicense($pro_code,$sn)
 * 作用：    验证站点是否处于有效期
 * 参数：       $pro_code：站点编码
 *       $sn：站点16位序列号     格式：1234-4321-4567-8765
 * 作者：靳邦龙
 * 时间：20130325
 **/
//远程提交数据的方法

function checkLicense($pro_code,$sn){
    //发送接口
    $SendUrl="https://www.isimpro.com/index.php/Api/Liccheck/index.html?product=$pro_code&sn=$sn";
    $return_msg=getHttpPage('post',$SendUrl);
    $json_data =json_decode($return_msg,true);//将返回的json字符串转成数组。
    if($json_data['status']==1){//有信息
        if($json_data['is_close']==1){
            echo "<script>alert('本站已关闭！');</script>";
            echo "<script>history.back();</script>";die;
        }else{
            if($json_data['alertdays']<=0&&$json_data['days']>0){//提示即将到期
                echo "<script>alert('程序还有".$json_data['days']."天到期，请及时联系开发人员');</script>";
            }elseif($json_data['days']<=0){//已到期提示
                if($json_data['pro_status']==0){
                    echo "<script>alert('本站内测时间已到期,请及时提交客户进行公测!');</script>";
                    //echo "<script>parent.location.href='".U('Public/logout')."';</script>";die;
                }elseif ($json_data['pro_status']==1){
                    echo "<script>alert('本站公测时间已到期,敬请期待正式版本!');</script>";
                    //echo "<script>parent.location.href='".U('Public/logout')."';</script>";die;
                }elseif ($json_data['pro_status']==2){
                    echo "<script>alert('本站使用有效时间已到期,请及时联系开发人员!');</script>";
                    echo "<script>history.back();</script>";die;
                }
            }else{
                return true;
            }
        }
    }else{//没有查到
        echo "<script>alert('".$json_data['content']."');</script>";
        echo "<script>history.back();</script>";die; 
    }
}

/**
 * @desc    判断某字段值是否重复
 * @name    checkRepeat()
 * @param   $table_name 表名        	
 * @param   $field 字段英文名称        	
 * @param   $value 需要匹配的值        	
 * @param   $fields 字段英文名称        	
 * @param   $val 需要匹配的值        	
 * @return  true/false
 * @author  靳邦龙
 * @time    2016-05-10
 */
function checkRepeat($table_name, $field, $value, $fields, $val) {
	$db_table = M ( $table_name );
	if (! empty ( $fields ) && ! empty ( $val )) {
		$table_list = $db_table->where ( "$field='$value' and $fields='$val'" )->find ();
	} else {
		$table_list = $db_table->where ( "$field='$value'" )->find ();
	}
	$count = sizeof ( $table_list );
	if ($count > 0) {
		return true;
	} else {
		return false;
	}
}

/****************************** 验证类方法结束************************/

/***************************** 取值类方法开始 ************************/

/**
 * @desc    远程获取数据，POST模式
 * @method  注意：
 *          1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 *          2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param   $method 提交方式        	
 * @param   $url 指定URL完整路径地址        	
 * @param   $para 请求的数据        	
 * @param   $cacert_url 指定当前工作目录绝对路径        	
 * @param   $input_charset 编码格式。默认值：空值
 * @return  远程输出的数据
 */
function getHttpPage($method = 'post', $url, $para = '', $cacert_url = '', $input_charset = '', $headers = 0) {
	if (trim ( $input_charset ) != '') {
		$url = $url . "_input_charset=" . $input_charset;
	}
	if (empty ( $headers )) {
		$headers = 0;
	}
	$curl = curl_init ( $url );
	curl_setopt ( $curl, CURLOPT_HEADER, $headers ); // 过滤HTTP头
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 显示输出结果
	curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, true ); // SSL证书认证
	curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // 严格认证
	curl_setopt ( $curl, CURLOPT_CAINFO, $cacert_url ); // 证书地址
	if ($method == 'post') {
		curl_setopt ( $curl, CURLOPT_POST, true ); // post传输数据
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, $para ); // post传输数据
	}
	$responseText = curl_exec ( $curl );
	if (curl_errno ( $curl )) {
		$responseText = 'Errno' . curl_error ( $curl );
	}
	
	curl_close ( $curl );
	return $responseText;
}
/**
 * @desc    获取验证码
 * @name    getVcode()
 * @param   $type 验证码类型（1：数字，2：图形）
 * @param 	$action 用途【1.登录 2.注册 3.短信】
 * @return  验证码
 * @author  靳邦龙
 * @time    2016-04-28
 */
function getVcode($type, $action) {
	if ($type == 2) { // 图片验证码暂不可用
		$Verify = new \Think\Verify ();
		$Verify->fontSize = 30;
		$Verify->length = 4;
		$Verify->useNoise = false;
		$Verify->useZh = false; // 中文
		$Verify->entry ();
	} else if ($type == 1) { // 4位数字验证码
		$vcode = '';
		for($i = 0; $i < 4; $i ++) {
			$vcode .= rand ( 0, 9 ) . "";
		}
		return $vcode;
	}
}

/**
 * @desc    生成流水号
 * @name    getFlowNo()
 * @param   $prefix 前缀
 * @param   $table 表名（注意表名不带前缀）
 * @param   $field 字段名
 * @param   $no_length 流水号序号长度(不包括前缀)
 * @return  流水号
 * @author  靳邦龙
 * @time    2016-06-13
 */
function getFlowNo($prefix, $table, $field, $no_length) {
	$prefix_length = strlen ( $prefix ); // 前缀的长度
	$length = $prefix_length + $no_length; // 编号总长度
	try {
		$max_no = M ()->query ( "select MAX($field) max_no from sp_$table where LENGTH($field)=$length and LEFT($field,$prefix_length)='$prefix'" );
		if (empty ( $max_no [0] [max_no] )) {
			for($i = 1; $i < $no_length; $i ++) {
				$no .= "0";
			}
			$new_no = $prefix . $no . '1';
		} else {
			$new_no = $max_no [0] [max_no];
			$r_no = substr ( $new_no, $prefix_length );
			$r_no = $r_no + 1;
			for($i = 0; $i < $no_length - strlen ( $r_no ); $i ++) {
				$no .= "0";
			}
			
			$new_no = $prefix . $no . $r_no;
		}
	} catch ( Exception $e ) {
		return $e; // "参数有误";
	}
	
	return $new_no;
}


/***************************** 取值类方法结束 ************************/

/**
 * @desc    拷贝文件
 * @name    xCopy
 * @param   $source 源文件目录	
 * @param   $destination 新文件目录        	
 * @param   $child  是否包括子目录      1包含     0不包含   	
 * @return
 * @author  王世超
 * @time    2016-09-20
 */
function xCopy($source, $destination, $child = '1') {
	if (! is_dir ( $source )) {
		echo ("Error:the $source is not a direction!");
		return 0;
	}
	if (! is_dir ( $destination )) {
		mkdir ( $destination, 0777 );
	}
	$handle = dir ( $source );
	
	while ( $entry = $handle->read () ) {
		if (($entry != ".") && ($entry != "..")) {
			if (is_dir ( $source . "/" . $entry )) {
				if ($child)
					xCopy ( $source . "/" . $entry, $destination . "/" . $entry, $child );
			} else {
				copy ( $source . "/" . $entry, $destination . "/" . $entry );
			}
		}
	}
	return 1;
}

/**
 * @desc    二维数组根据某一字段排序
 * @name    arraySort
 * @param   array $array 数组数据
 * @param   $keys  排序字段
 * @param   $mode  排序方式   SORT_DESC 降序；SORT_ASC 升序 
 * @return  array
 * @author  刘丙涛
 * @addtime 2017-05-17
 * @update  2017-11-07
 * @version V1.0.0
 */
function arraySort($array,$keys='add_time', $mode='SORT_DESC') {
    $arrSort = array ();
    foreach ( $array as $uniqid => $row ) {
        foreach ( $row as $key => $value ) {
            $arrSort [$key] [$uniqid] = $value;
        }
    }
    if ($mode) {
        array_multisort ( $arrSort [$keys], constant ($mode), $array );
    }
    return $array;
}

/**
 * @name        coordinateToAddr
 * @desc        经纬度转化成具体物理地址
 * @param       $coor（经纬坐标[此坐标纬度在前，经度在后]）
 * @return      返回具体地理地址名称
 * @author      黄子正
 * @time        2017-05-17
 * @version     V1.0.0
 */
function coordinateToAddr($coor) {
	$arr = array ();
	$arr = explode ( ",", $coor );
	$s = $arr [0];
	$arr [0] = $arr [1];
	$arr [1] = $s;
	$root1 = implode ( ",", $arr );
	$url2 = "http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location=$root1&output=json&pois=1&ak=C69cc900131b79148f5547f668e05285";
	$return_msg = getHttpPage ( 'get', $url2 );
	// $str1=str_replace("renderReverse&&renderReverse","",$return_msg);
	$str2 = strstr ( $return_msg, '","business"', true );
	$str3 = strstr ( $str2, '"formatted_address":"' );
	$str4 = str_replace ( '"formatted_address":"', '', $str3 );
	if ($str4) {
		return $str4;
	} else {
		return null;
	}
}

/**
 * @desc    随机数
 * @name    buildOrderNo
 * @return  int
 * @author  刘丙涛
 * @time    2017-6-17
 * @version V1.0.0
 *         
 */
function buildOrderNo() {
	/* return date('Ymd').rand(10,99).substr(implode(NULL,array_map('ord',str_split(substr(uniqid(),7,13),1))),0,8); */
	return rand ( 10, 99 ) . substr ( implode ( NULL, array_map ( 'ord', str_split ( substr ( uniqid (), 7, 13 ), 1 ) ) ), 0, 8 );
}

/**
 * @desc    计算当前时间与发布时间的间隔
 * @name    timeTran
 * @param   string $the_time 发布时间
 * @return  array
 * @author  刘丙涛
 * @time    2017-05-17
 * @version V1.0.0
 */
function timeTran($the_time) {
	$now_time = date ( "Y-m-d H:i:s", time () );
	// echo $now_time;
	$now_time = strtotime ( $now_time );
	$show_time = strtotime ( $the_time );
	$dur = $now_time - $show_time;
	if ($dur < 0) {
		return $the_time;
	} else {
		if ($dur < 60) {
			return $dur . '秒前';
		} else {
			if ($dur < 3600) {
				return floor ( $dur / 60 ) . '分钟前';
			} else {
				if ($dur < 86400) {
					return floor ( $dur / 3600 ) . '小时前';
				} else {
					if ($dur < 259200) { // 3天内
						return floor ( $dur / 86400 ) . '天前';
					} else {
						return $the_time;
					}
				}
			}
		}
	}
}
/**
 * @name    getCommunistAge()
 * @desc    根据生日计算年龄
 * @param   $birthday 生日   		
 * @return  年龄
 * @author  王桥元
 * @time    2017-08-04
 */
function getCommunistAge($birthday) {
	list($year,$month,$day) = explode("-",$birthday);
	$year_diff = date("Y") - $year;
	$month_diff = date("m") - $month;
	$day_diff  = date("d") - $day;
	if ($day_diff < 0 || $month_diff < 0)
		$year_diff--;
	return $year_diff;
}
/**
 * @name    excelTime()
 * @desc    转换时间格式
 * @param   时间戳
 * @return 
 * @author  王桥元
 * @time    2017-08-08
 */
function excelTime($date, $time = false) {
	if (function_exists('GregorianToJD')) {
		if (is_numeric($date)) {
			$jd = GregorianToJD(1, 1, 1970);
			$gregorian = JDToGregorian($jd + intval($date) - 25569);
			$date = explode('/', $gregorian);
			$date_str = str_pad($date[2], 4, '0', STR_PAD_LEFT) . "-" . str_pad($date[0], 2, '0', STR_PAD_LEFT) . "-" . str_pad($date[1], 2, '0', STR_PAD_LEFT) . ($time ? " 00:00:00" : '');
			return $date_str;
		}
	} else {
		$date = $date > 25568 ? $date + 1 : 25569; /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
		$ofs = (70 * 365 + 17 + 2) * 86400;
		$date = date("Y-m-d", ($date * 86400) - $ofs) . ($time ? " 00:00:00" : '');
	}
	return $date;
}

/**
 * @name    getTableInfo
 * @desc    获取指定表指定字段的值，支持单调数据多字段查询&&多条数据单字段查询
 * @param   $table_name 表名
 * @param   $key_field  主键字段
 * @param   $key_field_no  (支持多个)主键字段
 * @param   $field  (支持多个) 需要查询的字段名
 * @return  指定表指定字段的值,支持多个查询
 * @author  靳邦龙
 * @addtime 2017-11-02
 * @version V1.0.1
 **/
function getTableInfo($table_name,$key_field,$key_field_no,$field='name'){
    if(!empty($key_field_no)){
        if($field=='name'){
            $name_arr=strToArr($table_name, '_');
            $prex=$name_arr[sizeof($name_arr)-1];
            $field=$prex.'_'.$field;
        }
        $db_table=M($table_name);
        $no_arr = strToArr($key_field_no);
        $no_arr_length = sizeof($no_arr);//取编号数量
        if($field!='all'){
            $arr=strToArr($field);
            $arr_length=sizeof($arr);
            if($arr_length==1){//如果是一个字段，返回字符串
                $map[$key_field]  = array('in',$no_arr);
                $table_value=$db_table->where($map)->getField($field,true);
                $table_value = arrToStr($table_value);
            }else if($arr_length>1){//如果是多字段查询，查询单条数据，多个字段
                if($no_arr_length>1){//多条数据select查询
                    $map[$key_field]  = array('in',$no_arr);
                    $table_value=$db_table->where($map)->field($field)->select();
                }elseif($no_arr_length==1){//单条数据，find查询
                    $map[$key_field]  = array('eq',$key_field_no);
                    $table_value=$db_table->where($map)->field($field)->find();
                }
            }
        }elseif($field=='all'){//查询完整记录
            if($no_arr_length>1){//多条数据select查询
                $map[$key_field]  = array('in',$no_arr);
                $table_value=$db_table->where($map)->select();
            }elseif($no_arr_length==1){//单条数据，find查询
                $map[$key_field]  = array('eq',$key_field_no);
                $table_value=$db_table->where($map)->find();
            }
        }
    }
    if($table_value){
        return $table_value;
    }else{
        return null;
    }
}
/**
 * @name  createQrcode()
 * @desc  根据URL地址生成二维码
 * @param $url 链接地址或者内容
 * @param $img_name  将要生成不带扩展名的图片名称
 * @param $folder  图片存放地址
 *
 * @return   URL字符串    图片存储路径
 * @author 靳邦龙
 * @add_time   2017-08-8
 */
function createQrcode($url='',$img_name='qrcode_app',$folder='statics/apps/page_layout/images'){
    if(empty($url)){
       $url="http://".$_SERVER['HTTP_HOST']."/app.html";
    }
    if(empty($img_name)){
        $img_name=time().'.png';
    }else{
        $img_name=$img_name.'.png';
    }

    Vendor('phpqrcode.phpqrcode');
    ob_clean();
    //生成二维码图片
    $object = new \QRcode();
    $level=3;
    $size=4;
    $errorCorrectionLevel =intval($level) ;//容错级别
    $matrixPointSize = intval($size);//生成图片大小
    $new_name=$folder.'/'.$img_name;
    $object->png($url, $new_name, $errorCorrectionLevel, $matrixPointSize, 2);
    if($object){
        return $new_name;
    }else{
        return false;
    }
}

 /**
 * @name:getTimeUnits
 * @desc：计算时间差
 * @param：  $start_time:签到时间 $end_time:签退时间 $type (d:天,h:小时,m:分钟,s:秒)
 * @return：
 * @author：yangluahi
 * @addtime:2016-12-03
 * @version：V1.0.0
**/
function getTimeUnits($start_time,$end_time,$type='h'){
	switch ($type)
	{
		case 'd':
			$unit=round((strtotime($end_time)-strtotime($start_time))/86400,1);
			break;
		case 'h':
			$unit=round((strtotime($end_time)-strtotime($start_time))%86400/3600,1);
			break;
		case 'm':
			$unit=round((strtotime($end_time)-strtotime($start_time))%86400/60,1);
			break;
		case 's':
			$unit=round((strtotime($end_time)-strtotime($start_time))%86400%60,1);
			break;
		default :
			$unit=round((strtotime($end_time)-strtotime($start_time))%86400/3600,1);

	}
	return $unit;
}

/**
 * @name:getDateIsWeekend
 * @desc：获取指定时间是否是周末
 * @param：$Date:指定时间 ;$type:1:单休，2：双休
 * @return：
 * @author：yangluhai
 * @addtime:2016-12-03
 * @version：V1.0.0
 **/
function getDateIsWeekend($date,$type){
	$flag=false;
	date_default_timezone_set('PRC');   //设置时区是 北京时间
	//w  星期中的第几天，数字表示 0（星期天）到 6（星期六）
	if(!empty($date))
	{
		$date=getFormatDate($date,'Y-m-d');
		$w=date('w', strtotime($date));
		if($type=='1')
		{
			if($w=='0') { $flag=true;}
		} else{
			if($w=='6') { $flag=true;}
			if($w=='0') { $flag=true;}
		}

	}
	return $flag;
}

/**
 * @name:getSearchTime
 * @desc：获取搜索时间
 * @param：$time_type(时间类型)
 * @param：$start_time(开始时间类型)
 * @param：$end_time(结束时间类型)
 * @return：array('start_time'=>'2012','end_time'=>'2016')
 * @author：靳邦龙
 * @addtime:2016-12-05
 * @version：V1.0.0
 **/
function getSearchTime($time_type,$start_time,$end_time){
    if(!empty($start_time)||!empty($end_time)){
        $time_type=6;
    }
    if(!empty($time_type)){
        switch ($time_type) {
            case 1: //今天
                $start_time=date('Y-m-d');
                $end_time=getTimePoint();break;
            case 2: //一周
                $start_time=getTimePoint('minus','week',1);
                $end_time=date('Y-m-d H:i:s');break;
            case 3://一月
                $start_time=getTimePoint('minus','months',1);
                $end_time=date('Y-m-d H:i:s');break;
            case 4://本年
                $start_time=date('Y');
                $end_time=date('Y')+1;break;
            case 5://去年
                $start_time=date('Y')-1;
                $end_time=date('Y');break;
            case 6://自定义
                break;
        }
        return array('start_time'=>$start_time,'end_time'=>$end_time);
    }
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 刘丙涛
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 6; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 将二维数组数组按某个键提取出来组成新的索引数组
 * @param  array $array      数组
 * @param  string $key 键
 * @return array
 * @author 刘丙涛
 */
function array_extract($array = [], $key = 'id')
{

    $count = count($array);

    $new_arr = [];

    for($i = 0; $i < $count; $i++) {

        if (!empty($array) && !empty($array[$i][$key])) {

            $new_arr[] = $array[$i][$key];
        }
    }

    return $new_arr;
}

/**
 * @name  getdatelist()
 * @desc  日期月份
 * @param
 * @return 日期月份
 * @author 袁文豪
 * @version 版本 V1.1.0
 * @updatetime   2016/09/28
 * @addtime   2016-09-21
 */

function getdatelist($selectct,$type){
    $this_month=date("m");
    $January['0']="01";
    $January['1']="1月份";
    $February['0']="02";
    $February['1']="2月份";
    $March['0']="03";
    $March['1']="3月份";
    $April['0']="04";
    $April['1']="4月份";
    $May['0']="05";
    $May['1']="5月份";
    $June['0']="06";
    $June['1']="6月份";
    $July['0']='07';
    $July['1']="7月份";
    $August ['0']='08';
    $August ['1']="8月份";
    $month['0']='09';
    $month['1']="9月份";
    $October['0']='10';
    $October['1']="10月份";
    $November['0']='11';
    $November['1']="11月份";
    $December['0']="12";
    $December['1']="12月份";
    $luna=array("0"=>$January,"1"=>$February
            ,"2"=>$March,"3"=>$April
            ,"4"=>$May,"5"=>$June
            ,"6"=>$July,"7"=>$August
            ,"8"=>$month,"9"=>$October
            ,"10"=>$November,"11"=>$December);
    if(empty($type)){
        $html="";
        foreach($luna as $list){
            if($list['0']==$selectct){
                $select="selected";
            }else{
                $select="";
            }
            if($this_month<$list['0']){
                $disabled="disabled";
            }else{
                $disabled="";
            }
            $html.="<option $select value='".$list['0']."' $disabled>".$list['1']."</option>";
        }
    }else{
        $html=$luna;
    }

    return $html;
}
/**  
 * 简单对称加密算法之加密  
 * @param String $string 需要加密的字串  
 * @param String $skey 加密EKY  
 * @author Anyon Zou <zoujingli@qq.com>  
 * @date 2013-08-13 19:30  
 * @update 2014-10-10 10:10  
 * @return String  
 */  
function encode($string = '', $skey = 'yourkey') {  
    $strArr = str_split(base64_encode($string));  
    $strCount = count($strArr);  
    foreach (str_split($skey) as $key => $value)  
        $key < $strCount && $strArr[$key].=$value;  
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));  
}  
/**  
 * 简单对称加密算法之解密  
 * @param String $string 需要解密的字串  
 * @param String $skey 解密KEY  
 * @author Anyon Zou <zoujingli@qq.com>  
 * @date 2013-08-13 19:30  
 * @update 2014-10-10 10:10  
 * @return String  
 */  
function decode($string = '', $skey = 'yourkey') {  
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);  
    $strCount = count($strArr);  
    foreach (str_split($skey) as $key => $value)  
        $key <= $strCount && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];  
    return base64_decode(join('', $strArr));  
}  
/**  
 * 提取字符串中的数字  
 * @param String $str 需要解密的字串  
 * @author ljj 
 * @date 2018-05-07
 * @update
 * @return String  
 */  
function findNum($str = '')
{
    $str = trim($str);
    if (empty($str)) {return '';}
    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
        if (is_numeric($str[$i])) {
            $result .= $str[$i];
        }
    }
    return $result;
}

/**
 * @name  object_array()
 * @desc  PHP stdClass Object转array 
 * @param
 * @return 数组
 * @author ljj
 * @version 版本 V1.0.0
 * @updatetime   2018-7-16 21:52:16
 * @addtime   2018-7-16 21:52:22
 */ 
function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
    } if(is_array($array)) {  
        foreach($array as $key=>$value) {  
            $array[$key] = object_array($value);  
        }  
    }  
    return $array;  
}

/**
 * 对提供的数据进行urlsafe的base64编码。
 *
 * @param string $data 待编码的数据，一般为字符串
 *
 * @return string 编码后的字符串
 * @link http://developer.qiniu.com/docs/v6/api/overview/appendix.html#urlsafe-base64
 */
function base64_urlSafeEncode($data)
{
    $find = array('+', '/');
    $replace = array('-', '_');
    return str_replace($find, $replace, base64_encode($data));
}

/**
 * Wrapper for JSON decode that implements error detection with helpful
 * error messages.
 *
 * @param string $json JSON data to parse
 * @param bool $assoc When true, returned objects will be converted
 *                        into associative arrays.
 * @param int $depth User specified recursion depth.
 *
 * @return mixed
 * @throws \InvalidArgumentException if the JSON cannot be parsed.
 * @link http://www.php.net/manual/en/function.json-decode.php
 */
function qiniu_json_decode($json, $assoc = false, $depth = 512)
{
    static $jsonErrors = array(
        JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
        JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
        JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
        JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
    );

    if (empty($json)) {
        return null;
    }
    $data = \json_decode($json, $assoc, $depth);

    if (JSON_ERROR_NONE !== json_last_error()) {
        $last = json_last_error();
        throw new \InvalidArgumentException(
            'Unable to parse JSON data: '
            . (isset($jsonErrors[$last])
                ? $jsonErrors[$last]
                : 'Unknown error')
        );
    }

    return $data;
}

/**
 * @name  xmltoarr()
 * @desc  PHP xml String转array 
 * @param
 * @return 数组
 * @author ljj
 * @version 版本 V1.0.0
 * @updatetime   2018-7-16 21:52:16
 * @addtime   2018-7-16 21:52:22
 */ 
function xmltoarr($xmlfile){//xml字符串转数组
    $ob= simplexml_load_string($xmlfile,'SimpleXMLElement', LIBXML_NOCDATA);//将字符串转化为变量
    $json = json_encode($ob);//将对象转化为JSON格式的字符串
    $configData = json_decode($json, true);//将JSON格式的字符串转化为数组
    return $configData;
}
/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @author 王宗彬
 * @return number
 * @version 版本 V1.0.0
 * @updatetime   2019-06-20
 * @addtime   2019-06-20
 */
function BetweenTwoDays ($day1, $day2){
    
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);
    return ($second1 - $second2) / 86400;
}

/**
 * @name  deldir()
 * @desc  清空文件夹函数和清空文件夹后删除空文件夹函数的处理
 * @param path 需要清空的文件夹路径
 * @return 数组
 * @author ljj
 * @version 版本 V1.0.0
 * @updatetime   2018-7-16 21:52:16
 * @addtime   2018-7-16 21:52:22
 */ 
function deldir($path)
{
    //设置需要删除的文件夹
    if(empty($path)){
        $path = "./Runtime/";
    }
    //如果是目录则继续
    if (is_dir($path)) {
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $p = scandir($path);
        foreach ($p as $val) {
            //排除目录中的.和..
            if ($val != "." && $val != "..") {
                //如果是目录则递归子目录，继续操作
                if (is_dir($path . $val)) {
                    //子目录中操作删除文件夹和文件
                    deldir($path . $val . '/');
                    //目录清空后删除空文件夹
                    @rmdir($path . $val . '/');
                } else {
                    //如果是文件直接删除
                    unlink($path . $val);
                }
            } 
        }
    }
    return true;
}

function decimal2ABC($num){
    $ABCstr = "";
    $ten = $num;
    if($ten==0) return "A";
    while($ten!=0){
        $x = $ten%26;
        $ABCstr .= chr(65+$x);
        $ten = intval($ten/26);
    }
    return strrev($ABCstr);
}
//字母（26）进制转10进制
function ABC2decimal($abc){
    $ten = 0;
    $len = strlen($abc);
    for($i=1;$i<=$len;$i++){
		$char = substr($abc,0-$i,1);//反向获取单个字符
        $int = ord($char);
        $ten += ($int-65)*pow(26,$i-1);
    }
    return $ten;
}
/*
 *  生成随机字符串
 *
 *   $length    字符串长度
 */
function random_str($length) {
	// 密码字符集，可任意添加你需要的字符
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = '';
	for($i = 0; $i < $length; $i++)
	{
		// 这里提供两种字符获取方式
		// 第一种是使用 substr 截取$chars中的任意一位字符；
		// 第二种是取字符数组 $chars 的任意元素
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		// $str .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	return $str;
}
/**
 * 生产密钥
 * @param string $key  密钥
 * @author 王宗彬
 */
function getSafeLock($time_str){
	//$time_str = '2020-03-18';
	$time_arr = explode('-',$time_str);
	$y1 = substr($time_arr[0],0,1);		
	$y1 = decimal2ABC($y1);	
	$yy1 = substr($time_arr[0],0,2);
	$yy1 = base_convert($yy1,10,16);
	$y2 = substr($time_arr[0],1,1);	
	$y3 = substr($time_arr[0],2,1);
	$y13 = decimal2ABC($y3);			
	$y4 = substr($time_arr[0],3,1);		
	$m1 = base_convert($time_arr[1],10,13);
	$d1 = base_convert($time_arr[2],10,32);
	$num = ($time_arr[1]*$time_arr[2])+C('dj_key');
	$random = random_str(1).$y1.$yy1.'-'.$y2.$y13.$y4.random_str(1).'-'.$y3.$m1.$d1.$y4.'-'.random_str(1).str_pad($num,3,random_str(1));
	return $random;
}

/**  
将以上两个函数放在Common下的function.php公共函数中。  
用法：常用语get传参  
    前端：<a href="<{:U('Index/view',array('id'=>encode($data['id']),'name'=>encode($data['title'])))}>"><{$data.title}></a>  
    后台：view方法中：$id = decode(trim(I("get.id")));即可还原  
    view模板中：<font color="red"><{$Think.get.name|decode}></font>  
**/  
  
/*建议将key自行修改，尽量不要太长，不然url很长，适当即可，加密性能很好，亲测*/
