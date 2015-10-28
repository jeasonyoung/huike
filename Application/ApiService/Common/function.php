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
function build_callback_data($success=false,$data=null,$msg=''){
    if(APP_DEBUG) trace('构建返回数据封装...');
    return array(
            'success' => $success,
            'data'    => $data,
            'msg'     => $msg
        );
}

/**
 * 加载json数据请求。
 * @return array json对象
 */
function load_json_request(){
    if(APP_DEBUG) trace('加载json数据请求...');
    if(!IS_POST){
        if(APP_DEBUG) trace('json数据请求应为POST提交!');
        return null;
    }
    $_json = file_get_contents('php://input');
    if(!isset($_json) || empty($_json)){
        if(APP_DEBUG) trace('json数据请求为空!');
        return null;
    }
    //返回Array
    $_req = json_decode($_json,true);
    if(!$_req){
        if(APP_DEBUG) trace("请求数据不符合JSON格式!($_json)");
        return null;
    }
    if(APP_DEBUG) trace("json:".serialize($_req));
    return $_req;
}