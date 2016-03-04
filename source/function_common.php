<?php

if(!defined('IN_SITE')) {
	exit('Access Denied');
}

//SQL ADDSLASHES
function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

//取消HTML代码
function shtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = shtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
			str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : 'biyadi');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

//清空cookie
function clearcookie() {
	global $_SGLOBAL;
	
	obclean();
	ssetcookie('user_auth', '', -86400 * 365);
	ssetcookie('loginuser', '', -86400 * 365);
	$_SGLOBAL['member'] = array();
	$_SGLOBAL['uid'] = 0;
	$_SGLOBAL['username'] = '';
	$_SGLOBAL['session'] = array();
	//user_session
	unset($_SESSION['uid']);
	unset($_SESSION['username']);
}


//cookie设置
function ssetcookie($var, $value, $life=0) {
	global $_SGLOBAL, $_SC, $_SERVER;
	setcookie($_SC['cookiepre'].$var, $value, $life?($_SGLOBAL['timestamp']+$life):0, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
}

//数据库连接
function dbconnect() {
	global $_SGLOBAL, $_SC;

	include_once(S_ROOT.'./source/class_mysql.php');

	if(empty($_SGLOBAL['db'])) {
		$_SGLOBAL['db'] = new dbstuff;
		$_SGLOBAL['db']->charset = $_SC['dbcharset'];
		$_SGLOBAL['db']->connect($_SC['dbhost'], $_SC['dbuser'], $_SC['dbpw'], $_SC['dbname'], $_SC['pconnect']);
	}
}

//获取在线IP
function getonlineip($format=0) {
	global $_SGLOBAL;

	if(empty($_SGLOBAL['onlineip'])) {
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		$_SGLOBAL['onlineip'] = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	}
	if($format) {
		$ips = explode('.', $_SGLOBAL['onlineip']);
		for($i=0;$i<3;$i++) {
			$ips[$i] = intval($ips[$i]);
		}
		return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
	} else {
		return $_SGLOBAL['onlineip'];
	}
}


//获取到表名
function tname($name) {
	global $_SC;
	return $_SC['tablepre'].$name;
}


//生成一个随机字符串
function randomStr($type,$num){
	$sword= '0123456789';
	$zword = 'ABCDEFGHIJKLMNOPQRSTUVWYXZ';
	$word=$type==1?$zword:$sword;
	$len = strlen($word);
	$len = $len-2;
	$str = '';
	for ($x=0;$x<$num;$x++){
		$i = rand(0,$len);
		$theword = substr($word,$i,1);
		$str .= $theword;
	}
	return $str;
}

//对话框
function showmsg($msgkey,$type=1,$url_forward,$second=1) {
	global $_SGLOBAL;
	
	obclean();
	//include_once(S_ROOT.'./data/msglang.php');
	//$msg = isset($msg_data[$msgkey])?$msg_data[$msgkey]:$msgkey;
	$msg = $msgkey;
	
	header('Content-Type: text/html; charset=utf-8');
	
	if($type == 1){ //页面提示信息
		!$url_forward && $url_forward='javascript:history.go(-1)';
		$errmsg ="<div style='font-size:12px;font-family:verdana;line-height:180%;color:#666;border:dashed 1px #ccc;padding:1px;margin:20px;'>";
		$errmsg.="<div style=\"background: #eeedea;padding-left:10px;font-weight:bold;height:25px;\">提示信息</div>";
		$errmsg.="<div style='padding:20px;font-size:14px;'><span>$msg</span></div>";
		$errmsg.="<div style=\"text-align:center;height:30px;\"><a href='".$url_forward."'>返回</a></div>";
		$errmsg.="</div>";
		die($errmsg);
	}elseif($type == 2){ //js 输出对话框
		echo "<script language='javascript'>\n";
		echo "alert('".$msg."');\n";
		if($url_forward){
			echo "top.location='$url_forward';\n";
		}
		echo "</script>";
		exit;
	}
}

//判断提交是否正确
function submitcheck($var) {
	if(!empty($_POST[$var]) && $_SERVER['REQUEST_METHOD'] == 'POST') {
		if((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['formhash'] == formhash()) {
			return true;
		} else {
			//showmessage('submit_invalid');
			exit('submit_invalid');
		}
	} else {
		return false;
	}
}

//产生form防伪码
function formhash() {
	global $_SGLOBAL, $_SCONFIG;

	if(empty($_SGLOBAL['formhash'])) {
		$hashadd = defined('IN_ADMINCP') ? 'Only For biyadi AdminCP' : '';
		$_SGLOBAL['formhash'] = substr(md5(substr($_SGLOBAL['timestamp'], 0, -7).'|'.$_SGLOBAL['supe_uid'].'|'.md5($_SCONFIG['sitekey']).'|'.$hashadd), 8, 8);
	}
	return $_SGLOBAL['formhash'];
}


//添加数据 silent=1 不报错
function inserttable($tablename, $insertsqlarr, $returnid=0, $replace = false, $silent=0) {
	global $_SGLOBAL;

	$insertkeysql = $insertvaluesql = $comma = '';
	foreach ($insertsqlarr as $insert_key => $insert_value) {
		$insertkeysql .= $comma.'`'.$insert_key.'`';
		$insertvaluesql .= $comma.'\''.$insert_value.'\'';
		$comma = ', ';
	}
	//replace=>主键冲突先删除旧记录再插入新纪录
	$method = $replace?'REPLACE':'INSERT';
	$query = $method.' INTO '.tname($tablename).' ('.$insertkeysql.') VALUES ('.$insertvaluesql.')';
	if(preg_match('/__now__/i',$query)){
		$query = str_replace('\'__now__\'','NOW()',$query);
	}
	$_SGLOBAL['db']->query($query,$silent?'SILENT':'');
	if($returnid && !$replace) {
		return $_SGLOBAL['db']->insert_id();
	}
}

//更新数据
function updatetable($tablename, $setsqlarr, $wheresqlarr, $silent=0) {
	global $_SGLOBAL;

	$setsql = $comma = '';
	foreach ($setsqlarr as $set_key => $set_value) {
		$setsql .= $comma.'`'.$set_key.'`'.'=\''.$set_value.'\'';
		$comma = ', ';
	}
	$where = $comma = '';
	if(empty($wheresqlarr)) {
		$where = '1';
	} elseif(is_array($wheresqlarr)) {
		foreach ($wheresqlarr as $key => $value) {
			$where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
		}
	} else {
		$where = $wheresqlarr;
	}
	$_SGLOBAL['db']->query('UPDATE '.tname($tablename).' SET '.$setsql.' WHERE '.$where, $silent?'SILENT':'');
}


//时间格式化
function sgmdate($dateformat, $timestamp='', $format=0) {
	global $_SCONFIG, $_SGLOBAL;
	if(empty($timestamp)) {
		$timestamp = $_SGLOBAL['timestamp'];
	}
	$result = '';
	if($format) {
		$time = $_SGLOBAL['timestamp'] - $timestamp;
		if($time > 24*3600) {
			$result = gmdate($dateformat, $timestamp + $_SCONFIG['timeoffset'] * 3600);
		} elseif ($time > 3600) {
			$result = intval($time/3600).lang('hour').lang('before');
		} elseif ($time > 60) {
			$result = intval($time/60).lang('minute').lang('before');
		} elseif ($time > 0) {
			$result = $time.lang('second').lang('before');
		} else {
			$result = lang('now');
		}
	} else {
		$result = gmdate($dateformat, $timestamp + $_SCONFIG['timeoffset'] * 3600);
	}
	return $result;
}

//字符串时间化
function sstrtotime($string) {
	global $_SGLOBAL, $_SCONFIG;
	$time = '';
	if($string) {
		$time = strtotime($string);
		if(sgmdate('H:i') != date('H:i')) {
			$time = $time - $_SCONFIG['timeoffset'] * 3600;
		}
	}
	return $time;
}

//分页
function multi($num, $perpage, $curpage, $mpurl) {
	global $_SCONFIG;
	$_SCONFIG['maxpage'] = 10000;
	$page = 5;
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $_SCONFIG['maxpage'] && $_SCONFIG['maxpage'] < $realpages ? $_SCONFIG['maxpage'] : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1" class="first">1 ...</a>' : '').
			($curpage > 1 ? '<a href="'.$mpurl.'page='.($curpage - 1).'" class="prev">&lsaquo;&lsaquo;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
				'<a href="'.$mpurl.'page='.$i.'">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.'page='.($curpage + 1).'" class="next">&rsaquo;&rsaquo;</a>' : '').
			($to < $pages ? '<a href="'.$mpurl.'page='.$pages.'" class="last">... '.$realpages.'</a>' : '');
		$multipage = $multipage ? ('<em>共&nbsp;'.$num.'&nbsp;条</em>'.$multipage):'';
	}
	$maxpage = $realpages;
	return $multipage;
}

//ajax_page
function amulti($num, $perpage, $curpage, $mpurl,$obj) {
	global $_SCONFIG;
	$_SCONFIG['maxpage'] = 10000;
	$page = 5;
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $_SCONFIG['maxpage'] && $_SCONFIG['maxpage'] < $realpages ? $_SCONFIG['maxpage'] : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="javascript:page(\''.$mpurl.'page=1\',\''.$obj.'\',\''.$obj.'_page\')" class="first">1 ...</a>' : '').
			($curpage > 1 ? '<a href="javascript:page(\''.$mpurl.'page='.($curpage - 1).'\',\''.$obj.'\',\''.$obj.'_page\')" class="prev">&lsaquo;&lsaquo;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
				'<a href="javascript:page(\''.$mpurl.'page='.$i.'\',\''.$obj.'\',\''.$obj.'_page\')">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="javascript:page(\''.$mpurl.'page='.($curpage + 1).'\',\''.$obj.'\',\''.$obj.'_page\')" class="next">&rsaquo;&rsaquo;</a>' : '').
			($to < $pages ? '<a href="javascript:page(\''.$mpurl.'page='.$pages.'\',\''.$obj.'\',\''.$obj.'_page\')" class="last">... '.$realpages.'</a>' : '');
		$multipage = $multipage ? ('<em>共&nbsp;'.$num.'&nbsp;条</em>'.$multipage):'';
	}
	$maxpage = $realpages;
	return $multipage;
}

//ob
function obclean() {
	global $_SC;

	ob_end_clean();
	if ($_SC['gzipcompress'] && function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}
}


//获取数目
function getcount($tablename, $wherearr, $get='COUNT(*)') {
	global $_SGLOBAL;
	if(empty($wherearr)) {
		$wheresql = '1';
	} else {
		$wheresql = $mod = '';
		foreach ($wherearr as $key => $value) {
			$wheresql .= $mod."`$key`='$value'";
			$mod = ' AND ';
		}
	}
	return $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT $get FROM ".tname($tablename)." WHERE $wheresql LIMIT 1"), 0);
}


//处理搜索关键字
function stripsearchkey($string) {
	$string = trim($string);
	$string = str_replace('*', '%', addcslashes($string, '%_'));
	$string = str_replace('_', '\_', $string);
	return $string;
}

//连接字符
function simplode($ids) {
	return "'".implode("','", $ids)."'";
}

//显示进程处理时间
function debuginfo() {
	global $_SGLOBAL, $_SC, $_SCONFIG;

	if(empty($_SCONFIG['debuginfo'])) {
		$info = '';
	} else {
		$mtime = explode(' ', microtime());
		$totaltime = number_format(($mtime[1] + $mtime[0] - $_SGLOBAL['supe_starttime']), 4);
		$info = 'Processed in '.$totaltime.' second(s), '.$_SGLOBAL['db']->querynum.' queries'.
				($_SC['gzipcompress'] ? ', Gzip enabled' : NULL);
	}

	return $info;
}

//获取文件内容
function sreadfile($filename) {
	$content = '';
	if(function_exists('file_get_contents')) {
		@$content = file_get_contents($filename);
	} else {
		if(@$fp = fopen($filename, 'r')) {
			@$content = fread($fp, filesize($filename));
			@fclose($fp);
		}
	}
	return $content;
}

//写入文件 openmod=>ab 追加
function swritefile($filename, $writetext, $openmod='w') {
	if(@$fp = fopen($filename, $openmod)) {
		flock($fp, 2);
		fwrite($fp, $writetext);
		fclose($fp);
		return true;
	} else {
		//runlog('error', "File: $filename write error.");
		return false;
	}
}
//写文件 phpwind
function writeover($filename,$data,$method="rb+",$iflock=1,$check=1,$chmod=1){
	$check && strpos($filename,'..')!==false && exit('Forbidden');
	touch($filename);
	$handle = fopen($filename,$method);
	$iflock && flock($handle,LOCK_EX);
	fwrite($handle,$data);
	$method=="rb+" && ftruncate($handle,strlen($data));
	fclose($handle);
	$chmod && @chmod($filename,0777);
}

//产生随机字符
function random($length, $numeric = 0) {
	PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
	$seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed[mt_rand(0, $max)];
	}
	return $hash;
}

//判断字符串是否存在 有-true 无false
function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}

//站点链接
function getsiteurl() {
	global $_SC;
	
	if(empty($_SC['siteurl'])) {
		$uri = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
		return 'http://'.$_SERVER['HTTP_HOST'].substr($uri, 0, strrpos($uri, '/')+1);
	} else {
		return $_SC['siteurl'];
	}
}

//获取文件名后缀
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}
//去掉slassh
function sstripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = sstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

//编码转换
function siconv($str, $out_charset, $in_charset='') {
	global $_SC;

	$in_charset = empty($in_charset)?strtoupper($_SC['charset']):strtoupper($in_charset);
	$out_charset = strtoupper($out_charset);
	if($in_charset != $out_charset) {
		if (function_exists('iconv') && (@$outstr = iconv("$in_charset//IGNORE", "$out_charset//IGNORE", $str))) {
			return $outstr;
		} elseif (function_exists('mb_convert_encoding') && (@$outstr = mb_convert_encoding($str, $out_charset, $in_charset))) {
			return $outstr;
		}
	}
	return $str;//转换失败
}

//ip访问允许
function ipaccess($ipaccess) {
	return empty($ipaccess)?true:preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($ipaccess, '/')).")/", getonlineip());
}

//ip访问禁止
function ipbanned($ipbanned) {
	return empty($ipbanned)?false:preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($ipbanned, '/')).")/", getonlineip());
}

//取数组中的随机个
function sarray_rand($arr, $num) {
	$r_values = array();
	if($arr && count($arr) > $num) {
		if($num > 1) {
			$r_keys = array_rand($arr, $num);
			foreach ($r_keys as $key) {
				$r_values[$key] = $arr[$key];
			}
		} else {
			$r_key = array_rand($arr, 1);
			$r_values[$r_key] = $arr[$r_key];
		}
	} else {
		$r_values = $arr;
	}
	return $r_values;
}

//获取目录
function sreaddir($dir, $extarr=array()) {
	$dirs = array();
	if($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if(!empty($extarr) && is_array($extarr)) {
				if(in_array(strtolower(fileext($file)), $extarr)) {
					$dirs[] = $file;
				}
			} else if($file != '.' && $file != '..') {
				$dirs[] = $file;
			}
		}
		closedir($dh);
	}
	return $dirs;
}

if (!function_exists('json_encode')) {
     function format_json_value(&$value) 
    {
        if(is_int($value)) {
            $value = intval($value);
        } else if(is_float($value)) {
            $value = floatval($value);
        } else if(defined($value) && $value === null) {
            $value = strval(constant($value));
        } else if(is_string($value)) {
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }

    function json_encode($data) 
    {
        if(is_object($data)) {
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  false;
        }else {
            $assoc  =  true;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_null($val)) {
                if($assoc) {
                    $json .= "\"$key\":".json_encode($val).",";
                }else {
                    $json .= json_encode($val).",";
                }                
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}


function Char_cv($msg){
	$msg = str_replace('&','&amp;',$msg);
	$msg = str_replace('&nbsp;',' ',$msg);
	$msg = str_replace('"','&quot;',$msg);
	$msg = str_replace("'",'&#039;',$msg);
	$msg = str_replace("<","&lt;",$msg);
	$msg = str_replace(">","&gt;",$msg);
	$msg = str_replace("\t","   &nbsp;  &nbsp;",$msg);
	$msg = str_replace("\r","",$msg);
	$msg = str_replace("   "," &nbsp; ",$msg);
	return $msg;
}

/**
 * 批量初始化POST or GET变量,并将变量全局化
 *
 * @param Array $keys
 * @param string $method
 * @param var $htmcv
 */
function InitGP($keys,$method='GP',$htmcv=0){
	!is_array($keys) && $keys = array($keys);
	foreach($keys as $val){
		$GLOBALS[$val] = NULL;
		if($method!='P' && isset($_GET[$val])){
			$GLOBALS[$val] = $_GET[$val];
		} elseif($method!='G' && isset($_POST[$val])){
			$GLOBALS[$val] = $_POST[$val];
		}
		$htmcv && $GLOBALS[$val] = Char_cv($GLOBALS[$val]);
	}
}

/**
 * 初始化单一POST or GET 变量
 *
 * @param string $key
 * @param string $method
 * @return unknown
 */
function GetGP($key,$method='GP'){
	if($method=='G' || $method!='P' && isset($_GET[$key])){
		return $_GET[$key];
	}
	return $_POST[$key];
}

//输出json
function showmsg2($msg){
	$msgg = array('ok'=>0,'error'=>strtoutf8($msg));
	echo json_encode($msgg);
	exit;
}

//输出数据(动画,ajax)
function showdata($data_array){
	global $_SGLOBAL;
	include_once(S_ROOT.'./data/msglang.php');
	$key_msg = array('error');
	foreach($data_array as $key=>$value){
		if(in_array($key,$key_msg)){
			$value = isset($msg_data[$value])?$msg_data[$value]:$value;
		}
		$array[$key] = $value;
	}
	if($_SGLOBAL['inajax']){
		foreach($array as $key=>$value){
			if(in_array($key,$key_msg)){
				$array2[$key]=strtoutf8($value);
			}else{
				$array2[$key]=$value;
			}
		}
		echo json_encode($array2);
	}else{
		foreach($array as $key=>$value){
			$add = empty($echostr)?'':'&';
			$echostr .=$add.$key.'='.$value;
		}
		echo $echostr;
	}
	exit;
}

//跳转
function ObHeader($URL){
	global $_SC;
	if($_SC['gzipcompress']){
		header("Location: $URL");exit;
	}else{
		ob_start();
		echo "<script language='javascript'>\n";
		echo "window.location='$URL';\n";
		echo "</script>";
		exit;
	}
}

//截取字符串
function substrs($content,$length,$num=0,$add=1,$code='UTF-8'){
	$code = $code ? $code : strtoupper('gb2312');
	$content = strip_tags($content);
	//去掉空格
	$content = str_replace(array("&nbsp;"),array(''),$content);
	if($length && strlen($content)>$length){
		$retstr='';
		if($code == 'UTF-8'){		
			$wordscut = '';
			$n = 0;
			$tn = 0;
			$noc = 0;
			while ($n < strlen($content)) {
				$t = ord($content[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1;
					$n++;
					$noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2;
					$n += 2;
					$noc += 2;
				} elseif(224 <= $t && $t < 239) {
					$tn = 3;
					$n += 3;
					$noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4;
					$n += 4;
					$noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5;
					$n += 5;
					$noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6;
					$n += 6;
					$noc += 2;
				} else {
					$n++;
				}
				if ($noc >= $length) {
					break;
				}
			}
			if ($noc > $length) {
				$n -= $tn;
			}
			$wordscut = substr($content, 0, $n);			
			$retstr = $wordscut;
		}else{
			for($i = 0; $i < $length; $i++) {
				if(ord($content[$i]) > 127){
					if($num){
						$retstr .=$content[$i].$content[$i+1];
						$i++;
						$length++;
					}elseif(($i+1<$length)){
						$retstr .=$content[$i].$content[$i+1];
						$i++;
					}
				}else{
					$retstr .=$content[$i];
				}
			}
		}
		return $retstr.($add ? '...' : '');
	}
	return $content;
}


//检查邮箱是否有效
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

//检查是否移动设备访问
function ismobile(){
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
		return true;
	}else{
		return false;	
	}
}

//伪静态url设置
function rewriteurl($array){
	global $_SC;
	$url = '';
	if($_SC['isrewrite']){
		if(count($array) == 1){
			$url = $array[0].'.html';	
		}else{
			$url = implode('-',$array);	
			$url.='.html';
		}
	}else{
		if(count($array) == 1){
			$url = $array[0].'.php';	
		}else{
			if($array[0] == 'news' && count($array) == 3){
				$url = $array[0].'.php?'.$array[1].'='.$array[2];
			}elseif($array[0] == 'service' && count($array) == 3){
				$url = $array[0].'.php?'.$array[1].'='.$array[2];
			}elseif($array[0] == 'car'){
				if(count($array) == 2){
					$url = $array[0].'.php?name='.$array[1];
				}else{
					$url = $array[0].'.php?mod='.$array[1].'&name='.$array[2];
				}
			}else{
				$url = $array[0].'.php?mod='.$array[1];
			}
		}
	}
	return $url;
}

//输出json
function showjson($msg){
	echo json_encode($msg);
	exit;
}

//判断当前用户登录状态
function checkauth() {
	global $_SGLOBAL,$_SCOOKIE;
	
	if($_SCOOKIE['user_auth']) { //先判断cookie
		@list($password, $uid) = explode("\t", authcode($_SCOOKIE['user_auth'], 'DECODE'));
		$_SGLOBAL['uid'] = intval($uid);
	}elseif($_SESSION['uid']){
		$_SGLOBAL['uid'] = intval($_SESSION['uid']);	
	}
	
	//$_SGLOBAL['uid'] = intval($_SGLOBAL['uid']);
	if($_SGLOBAL['uid']) {
		$query = $_SGLOBAL['db']->query("SELECT id,name,password,email,realname,address,tele FROM ".tname('member')." WHERE id='$_SGLOBAL[uid]'");
		if($member = $_SGLOBAL['db']->fetch_array($query)) {
			if($member['password'] == $password) {
				$_SGLOBAL['username'] = addslashes($member['name']);
				$_SGLOBAL['session'] = $member;
				
			}else{
				$_SGLOBAL['uid'] = 0;	
			}
		} else {
			$_SGLOBAL['uid'] = 0;
		}
	}
		
	if(empty($_SGLOBAL['uid'])) {
		clearcookie();
	}
}


?>