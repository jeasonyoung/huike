<?php
/**
 * 班级有效/无效列表
 * @param $class $array 班级数组
 * @param $validity 有效/过期
 */
function classValidity($class,$validity=true){
    $result = array();
    foreach ($class as $key => $value){
        if($validity){
            if(date('Y-m-d',time()) <= $value['enddate']){
                array_push($result,$value);
            }
        }else{
            if(date('Y-m-d',time()) > $value['enddate']){
                array_push($result,$value);
            }  
        }
    }
    return $result;
}

/**
 * 播放时间转换
 * @param int $second 播放时间(秒)
 * @param int $unit 除数
 */
function playtime($second,$unit=60,$int=true){
    if($int){
        return floor($second/$unit); 
    }else{
        return str_replace('.',':',number_format($second/$unit,2));
    }
}

//学习进度比较
function learnRecord($seconds,$pos){
    if(!is_numeric($pos) || empty($pos)){return 'none';}
	$_time = abs($seconds - $pos);
	$_half = $seconds / 2;
	if($_time <= 10){
		return 'done';
	}else{
		return 'half';
	}
}


function unique($data = array()){
    $tmp = array();
    foreach($data as $key => $value){
        //把一维数组键值与键名组合
        foreach($value as $key1 => $value1){
            $value[$key1] = $key1 . '_|_' . $value1;//_|_分隔符复杂点以免冲突
        }
        $tmp[$key] = implode(',|,', $value);//,|,分隔符复杂点以免冲突
    }
    //对降维后的数组去重复处理
    $tmp = array_unique($tmp);
    //重组二维数组
    $newArr = array();
    foreach($tmp as $k => $tmp_v){
        $tmp_v2 = explode(',|,', $tmp_v);
        foreach($tmp_v2 as $k2 => $v2){
            $v2 = explode('_|_', $v2);
            $tmp_v3[$v2[0]] = $v2[1];
        }
        $newArr[$k] = $tmp_v3;
    }
    return $newArr;
}

/*播放器加密相关函数开始*/
function play_set($myurl){
    $encrypt=$myurl;
    $tb=mcrypt_module_open(MCRYPT_3DES,'','cbc',''); 
    $key=C('key');
    $iv=C('iv');
    mcrypt_generic_init($tb, $key, $iv);
    $encrypt=PaddingPKCS7($encrypt);
    $cipher=mcrypt_generic($tb,$encrypt); 
    $cipher=base64_encode($cipher);
    mcrypt_generic_deinit($tb); 
    mcrypt_module_close($tb); 
    return $cipher;
}

function PaddingPKCS7 ($data)
{
    $block_size = mcrypt_get_block_size(MCRYPT_3DES, 'cbc');
    $padding_char = $block_size - (strlen($data) % $block_size);  
    $data .= str_repeat(chr($padding_char), $padding_char); 
    return $data;
}
/*播放器加密相关函数结束*/

function tel_validate($str){
    $isMob="/^1[3-7,8]{1}[0-9]{9}$/";
    if(!preg_match($isMob,$str)){
        return false;
    }else{
        return true;
    }
}

/**
 * 加密。
 * @param  string $text 明文。
 * @return string       密文。
 */
function create_des_encrypt($text=''){
    if(APP_DEBUG) trace("加密...");
    if(empty($text)) return null;
    $_key = md5(C('md5_key'));
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                                        $_key, 
                                        $text, 
                                        MCRYPT_MODE_ECB, 
                                        mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 
                                                                            MCRYPT_MODE_ECB), 
                                                         MCRYPT_RAND)));
}

/**
 * 解密。
 * @param  string $base64 密文。
 * @return string         明文。
 */
function create_des_decrypt($base64=''){
    if(APP_DEBUG) trace("解密...");
    if(empty($base64)) return null;
    $_key = md5(C('md5_key'));
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 
                          $_key,
                          base64_decode($base64),
                          MCRYPT_MODE_ECB,
                          mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,
                                                              MCRYPT_MODE_ECB), 
                                           MCRYPT_RAND));
}
/**
* 检查是否是以手机浏览器进入(IN_MOBILE)
*/
 function isMobile() {
    $mobile = array();
    static $mobilebrowser_list ='Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
    //note 获取手机浏览器
    if(preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
        return 1;
    }else{
        if(preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return 0;
        }else{
            if($_GET['mobile'] === 'yes') {
                return 1;
            }else{
                return 0;
            }
        }
    }
 }
 
 /**
 * 创建订单号 
 * $fix 放重复唯一字符 最好用用户id
 */
function create_order($fix='',$preFix=''){
	$rand = rand(10,99);
	if(empty($preFix)){
		$preFix = C('ORDER_PREFIX');
	}
	return $preFix.date("YmdHis").substr($rand.$fix,-2);
}


/**
 * 获取客户端浏览器类型
 * @param  string $glue 浏览器类型和版本号之间的连接符
 * @return string|array 传递连接符则连接浏览器类型和版本号返回字符串否则直接返回数组 false为未知浏览器类型
 */
 function get_client_browser($glue = null) {
    $browser = array();
    $agent = $_SERVER['HTTP_USER_AGENT']; //获取客户端信息
    
    /* 定义浏览器特性正则表达式 */
    $regex = array(
        'ie'      => '/(MSIE) (\d+\.\d)/',
        'chrome'  => '/(Chrome)\/(\d+\.\d+)/',
        'firefox' => '/(Firefox)\/(\d+\.\d+)/',
        'uc' => '/(UCBrowser)\/(\d+\.\d+)/',
        'opera'   => '/(Opera)\/(\d+\.\d+)/',
        'safari'  => '/Version\/(\d+\.\d+\.\d) (Safari)/',
    );
    foreach($regex as $type => $reg) {
        preg_match($reg, $agent, $data);
        if(!empty($data) && is_array($data)){
            $browser = $type === 'safari' ? array($data[2], $data[1]) : array($data[1], $data[2]);
            break;
        }
    }
    $browser = empty($browser) ? false : (is_null($glue) ? $browser : implode($glue, $browser));
    return $browser[0].'-'.$browser[1];
 }
 
 //获取结构一个字段值
 function oneField($field,$agencyId){
	 $db = M('jigou');
	 $v =$db->where(array('JGID'=>$agencyId))->getField($field);
	 if($v){
		 if($field=='City'){
			 return M('citys')->where(array('CityID'=>$v))->getField('CityName');
		 }else{
			 return $v;
		 }
		 
	 }else{
		 return '';
	 }
 }
 
 function getClass($para='',$agencyId,$taocanid){
	 $strs = '<table border="1" class="xytab"><tbody><tr><td width="189"><div align="center"><strong>班次</strong></div></td><td width="175"><div align="center"><strong>学费</strong><strong></strong></div></td></tr><tr>';
	 $db = M('order_detail');
	 $data = $db->where(array('UserName'=>session('username'),'JGID'=>$agencyId,'JG_ProID'=>$taocanid))->find();
	 if($data){
		$_class = M('jigou_class')->field('CnName')->where(array('JGCID'=>array('In',$data['alljgcid'])))->select();
		foreach($_class as $val){
			$class_str.="<li>".$val['cnname']."</li>";
		}
		$price = M('orders')->where(array('OrderID'=>$data['orderid']))->getField('SalesPrice');
		$strs.="<td><ul class=\"cl\">$class_str</ul></td><td>".$price."元</td></tr></table>";
		return $strs;
	 }
	 return '';
 }
 
 function getMember($para='',$agencyId){
	 $memberName = M('user')->where(array('JGID'=>$agencyId,'UserName'=>session('username')))->getField('RealName');
	 if($memberName){
		 return $memberName;
	 }else{
		 return '';
	 }
 }