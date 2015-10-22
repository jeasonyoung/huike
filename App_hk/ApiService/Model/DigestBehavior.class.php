<?php
/**
* Http摘要认证行为扩展
* 
*/
namespace ApiService\Model;

use Think\Behavior;

class DigestBehavior extends Behavior{

    //行为入口
    public function run(&$param){
        //获取realm
        $_realm = C('DIGEST_REALM');
        //设置验证方式
        $_qop   = 'auth';
        //判断是否为验证
        if(!isset($_SERVER['PHP_AUTH_DIGEST'])){
            if(APP_DEBUG) trace('发送401摘要信息...');
            $this->sendAuthenticate($_realm,$_qop);
        }
        if(APP_DEBUG) trace('解析请求参数...'.$_SERVER['PHP_AUTH_DIGEST']);
        //解析认证头消息
        $_digest = $this->parseAuthorization($_SERVER['PHP_AUTH_DIGEST']);
        //验证opaque
        $_opaque = md5($_realm.':'.$_qop.':'.$_digest['nonce']);
        if($_digest['opaque'] != $_opaque){
            if(APP_DEBUG) trace('验证opaque失败:['.$_opaque.']!=['.$_digest['opaque'].']');
            die('摘要认证参数验证错误!');
        }
        //判断用户名是否存在
        $_pwd = $this->loadAgencyAppKey($_digest['username']);
        if(!$_pwd){
            if(APP_DEBUG) trace('用户名不存在!'.$_SERVER['PHP_AUTH_DIGEST']);
            die('用户名不存在!');
        }
        //验证密码
        $_ha1       = md5($_digest['username'].':'.$_realm.':'.$_pwd);
        $_ha2       = md5(REQUEST_METHOD.':'.$_digest['uri']);
        $_response  = md5($_ha1.':'.$_digest['nonce'].':'.$_digest['nc'].':'.$_digest['cnonce'].':'.$_qop.':'.$_ha2);
        if($_digest['response'] != $_response){
            if(APP_DEBUG) trace('密码错误![ha1='.$_ha1.',ha2='.$_ha2.',response='.$_response.']=>'.$_digest['response']);
            die('密码错误!');
        }
    }

    //发送401摘要信息
    private function sendAuthenticate($realm,$qop){
        $_nonce = uniqid();
        //发送401
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate:DIGEST realm="'.$realm.'" qop="'.$qop.'" nonce="'.$_nonce.
            '" opaque="'.md5($realm.':'.$qop.':'.$_nonce).'"');
        die('您已取消身份认证');
    }

    //解析HTTP认证头消息
    private function parseAuthorization($authz = null){
        if(!isset($authz) || empty($authz)) return null;
        //正则表达式
        $_match_regex_0 = '/username="(?P<username>.*)",\s*realm="(?P<realm>.*)",\s*nonce="(?P<nonce>.*)",';
        $_match_regex_0 .= '\s*uri="(?P<uri>.*)",\s*response="(?P<response>.*)",\s*opaque="(?P<opaque>.*)",';
        //表达式1
        $_match_regex_1 = $_match_regex_0.'\s*qop=(?P<qop>.*),\s*nc=(?P<nc>.*),\s*cnonce="(?P<cnonce>.*)"/';
        //分析请求参数
        $_match = preg_match($_match_regex_1, $authz, $_digest);
        //正则表达式匹配是否成功
        if($_match == 0){
            if(APP_DEBUG) trace('解析失败(正则表达式匹配失败1)!'.$_match_regex_1);
            //表达式2
            $_match_regex_2 = $_match_regex_0.'\s*cnonce="(?P<cnonce>.*)",\s*nc=(?P<nc>.*),\s*qop="(?P<qop>.*)"/';
            $_match = preg_match($_match_regex_2, $authz, $_digest);
            if($_match == 0){
                if(APP_DEBUG) trace('解析失败(正则表达式匹配失败2)!'.$_match_regex_2);
                return null;
            }
        }
        return $_digest;
    }
    
    //根据机构英文简称加载接口验证密钥
    private function loadAgencyAppKey($abbr){
        if(APP_DEBUG) trace('准备查询数据库...'.$abbr);
        $_auth = D('DigestAuth');
        return $_auth->loadAppKey($abbr);
    }
}