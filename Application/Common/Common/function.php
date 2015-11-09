<?php
/*此文件为当前应用App_hk全局函数*/

/**
 * 调试函数
 * @param mix $msg 需要调试的变量
 */
function show_bug($msg){
  echo "<pre>";
  var_dump($msg);
  echo "</pre>";
}

function tel_validate($str){
    $isMob="/^1[3-5,8]{1}[0-9]{9}$/";
    $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
    if(!preg_match($isMob,$str) && !preg_match($isTel,$str)){
        return false;
    }else{
        return true;
    }
}

function chk_arrEmpty($array){
    return !empty($array);
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
