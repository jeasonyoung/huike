<?php
/** 
  * Generates an UUID 
  * @author     Anis uddin Ahmad  
  * @param      string  an optional prefix 
  * @return     string  the formatted uuid 
  */  
  function uuid($prefix = '')  
  {  
    $chars = md5(uniqid(mt_rand(), true));  
    $uuid  = substr($chars,0,8) . '-';  
    $uuid .= substr($chars,8,4) . '-';  
    $uuid .= substr($chars,12,4) . '-';  
    $uuid .= substr($chars,16,4) . '-';  
    $uuid .= substr($chars,20,12);  
    return $prefix . $uuid;  
  }
  
  function fdate($ptrn='Y-m-d',$strTime){
      return date($ptrn,strtotime($strTime));
  }
  
  /**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共 <b>%TOTAL_ROW%</b> 条记录&nbsp;第 <b>%NOW_PAGE%</b> 页/共 <b>%TOTAL_PAGE%</b> 页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}

/**
 * 播放时间转换
 * @param int $second 播放时间(秒)
 * @param int $unit 除数
 */
function playtime($second,$unit=60){
    return $second/$unit;
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
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
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


/**
 * http认证接口验证
 * @param string $digest 接口发送的头信息
 * @param string $url 接口url
 * @param string $user 接口认证用户名
 * @param string $pwd 接口认证密码
 * @param int $index 防死循环变量
 * @return string 接口返回json信息
 */
function DigestClient($digest,$url,$user,$pwd,$index){
    $ch = curl_init();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, false );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_NOBODY, true );
    curl_setopt ( $ch, CURLOPT_POST, true );
    if(!empty($digest)){
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, array("Authorization:$digest") );
    }
    $result=curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($http==401){
        $heads=  get_headers($url);
        $realm = GetField($heads[5],'realm');
        $nonce = GetField($heads[5], 'nonce');
        $qop = GetField($heads[5], 'qop');
        $opaque = GetField($heads[5], 'opaque');
        //最多3次循环 防止死循环
        if($index < 4){
            $nonceCount = substr("00000000".strval($index + 1), -8);
            $clientNonce = substr("000000" .strval(mt_rand(10000000,999999999) * 10000),0, 10);
            $ha1 = md5(strval($user).":".strval($realm[0]).":".strval($pwd));
            $ha2 = md5("POST:".strval($url));
            $resp = md5(strval($ha1).":".strval($nonce[0]).":".strval($nonceCount).":".strval($clientNonce).":".strval($qop[0]).":".strval($ha2));
            $post_head='Digest username="'.$user.'",realm="'.$realm[0].'",nonce="'.$nonce[0].'",uri="'.$url.'",qop="'.$qop[0].'",nc="'.$nonceCount.'",cnonce="'.$clientNonce.'",response="'.$resp.'",opaque="'.$opaque[0].'"';
            return DigestClient($post_head, $url, $user, $pwd, $index+1);
        }
    }else{
        return $result;
    }
}

function GetField($str,$field){
    $match=array();
    preg_match_all("/$field=\"([\s\S]+?)\"/",$str,$match);
    return $match[1];
}
