<?php
/**
 * 模块公共函数库
 */


/**
 * 构建返回数据。
 * @param  boolean $success 是否成功。
 * @param  mixed   $data    返回数据。
 * @param  integer $code    错误代码。
 * @param  string  $msg     错误消息。
 * @return array            返回对象。
 */
function build_callback_data($success=false,$data=null,$code=null,$msg=null){
    if(APP_DEBUG) trace('构建返回数据封装...');
    $_result = array();
    $_result['success'] = $success;

    if(isset($data)){
        $_result['data'] = $data;
    }

    if(isset($code)){
        $_result['code'] = $code;
    }

    if(isset($msg) && !empty($msg)){
        $_result['msg'] = $msg;
    }

    return $_result;
}
/**
 * 构建成功返回。
 * @param  mixed   $data    返回数据。
 * @return array            返回对象。
 */
function build_callback_success($data=null){
    return build_callback_data(true,$data);
}
/**
 * 构建失败返回。
 * @param  integer $code 错误代码。
 * @param  string  $msg    错误消息。
 * @return [type]        返回对象。
 */
function build_callback_error($code=0,$msg=''){
    return build_callback_data(false,null,$code,$msg);
}
