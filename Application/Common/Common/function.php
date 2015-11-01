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
