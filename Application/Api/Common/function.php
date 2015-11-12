<?php
/**
 * 模块公共函数库
 */


/**
 * 构建返回数据。
 * @param  boolean $success 是否成功。
 * @param  mixed   $data    返回数据。
 * @param  string  $msg     消息内容。
 * @return array            数组对象。
 */
function build_callback_data($success=false,$data=null,$msg=null){
    if(APP_DEBUG) trace('构建返回数据封装...');
    $_result = array();
    $_result['success'] = $success;

    if(isset($data)){
        $_result['data'] = $data;
    }

    if(isset($msg) && !empty($msg)){
        $_result['msg'] = $msg;
    }

    return $_result;
}

// /**
//  * 解密。
//  * @param  string $base64 密文。
//  * @return string         明文。
//  */
// function create_des_decrypt($base64=''){
//     if(APP_DEBUG) trace("解密...");
//     if(empty($base64)) return null;
    
//     if(empty(C('md5_key'))){
//         if(APP_DEBUG) trace('解密密钥[md5_key]不存在!');
//         return null;
//     }
//     $_key = md5(C('md5_key'));
//     return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 
//                           $_key,
//                           base64_decode($base64),
//                           MCRYPT_MODE_ECB,
//                           mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,
//                                                               MCRYPT_MODE_ECB), 
//                                            MCRYPT_RAND));
// }
// /**
//  * 加载json数据请求。
//  * @return array json对象
//  */
// function load_json_request(){
//     if(APP_DEBUG) trace('加载json数据请求...');
//     if(!IS_POST){
//         if(APP_DEBUG) trace('json数据请求应为POST提交!');
//         return null;
//     }
//     $_json = file_get_contents('php://input');
//     if(!isset($_json) || empty($_json)){
//         if(APP_DEBUG) trace('json数据请求为空!');
//         return null;
//     }
//     //返回Array
//     $_req = json_decode($_json,true);
//     if(!$_req){
//         if(APP_DEBUG) trace("请求数据不符合JSON格式!($_json)");
//         return null;
//     }
//     if(APP_DEBUG) trace("json:".serialize($_req));
//     return $_req;
// }