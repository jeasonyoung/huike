<?php
/**
 * 接口验证基类。
 */
namespace Api\Controller;
use Think\Controller\RestController;

class AuthController extends RestController{
    /**
     * 校验签名。
     * @param  array  $params 参数数组。
     * @return mixed          验证结果(验证成功返回所属机构ID，失败返回false)
     */
    protected function verificationSignature($params=array()){
        if(APP_DEBUG)trace('校验签名...'.serialize($params));
        if(empty($params)){//参数为空
            $this->send_callback_error(-101,'参数为空!');
            return false;
        }
        //过滤空值参数
        $params = array_filter($params);
        if(empty($params)){
            $this->send_callback_error(-102,'参数值为空!');
            return false;
        }
        //去重复参数
        $params = array_unique($params);
        //获取令牌
        if(!isset($params['token'])){
            $this->send_callback_error(-103,'未带令牌!');
            return false;
        }
        $_token = $params['token'];
        if(APP_DEBUG)trace("获取令牌:$_token");
        //获取签名
        if(!isset($params['sign'])){
            $this->send_callback_error(-104,'未带签名!');
            return false;
        }
        $_sign = $params['sign'];
        if(APP_DEBUG)trace("获取签名:$_sign");
        //加载密钥
        $_secretKey = $this->loadSecretKeyByToken($_token);
        if($_secretKey){
            if(APP_DEBUG) trace("加载密钥:$_secretKey");
            //从参数数组中移除签名
            unset($params['sign']);
            if(APP_DEBUG) trace('移除签名后的参数数组:'.serialize($params));

            //参数按键名排序
            ksort($params);
            if(APP_DEBUG) trace('按键名排序后的参数数组:'.serialize($params));

            //循环参数数组
            $_parameters = array();
            foreach ($params as $key => $value) {
                $_parameters[] = "$key=$value";
            }
            //叠加参数键值对
            $_sign_source_str = implode('&', $_parameters);
            if(APP_DEBUG) trace("拼接参数字符串:$_sign_source_str");
            $_sign_source_str .= $_secretKey;
            if(APP_DEBUG) trace("叠加密钥后的字符串:$_sign_source_str");
            //计算签名
            $_new_sign = md5($_sign_source_str);
            if(APP_DEBUG) trace("计算签名:$_new_sign");
            //对比签名
            if($_sign == $_new_sign){
                //签名验证成功,返回所属机构ID
                $_agencyId = $this->loadAgencyIdByToken($_token);
                if($_agencyId){
                    return $_agencyId;
                }
                //机构ID不存在
                $this->send_callback_error(-105,'获取所属机构ID失败!');
            }else{
                //签名验证失败
                $this->send_callback_error(-100,'签名验证失败!');
            }
        }
        return false;
    }

    /**
     * 根据令牌获取所属机构ID.
     * @param  string $token 令牌。
     * @return string        所属机构ID。
     */
    protected function loadAgencyIdByToken($token=''){
        if(APP_DEBUG) trace("根据令牌[$token]获取所属机构ID...");
        if(empty($token)) return null;
        //缓存
        static $_agencyIds = array();
        //从缓存中加载数据
        if(isset($_agencyIds[$token])){
            return $_agencyIds[$token];
        }
        //从表中查找数据
        $_model = M('Jigou')->field('jgid')
                            ->where("`abbr_en` = '%s'", array($token))
                            ->find();
        if($_model){
            $_agencyIds[$token] = $_model['jgid'];
            return $_agencyIds[$token];
        }
        return null;
    }

    /**
     * 根据令牌获取密钥。
     * @param  string $token 令牌
     * @return string        密钥
     */
    private function loadSecretKeyByToken($token=''){
        if(APP_DEBUG) trace("根据令牌[$token]获取密钥...");
        if(empty($token)){
            $this->send_callback_error(-200,'令牌为空!');
            return null;
        }
        //从表中查找数据
        $_model = M('Jigou')->field('appkey,statetf')
                            ->where("`abbr_en` = '%s'", array($token))
                            ->find();
        if(!$_model){
            $this->send_callback_error(-201,'令牌不存在!');
            return null;
        }
        if(empty($_model['statetf'])){
            $this->send_callback_error(-202,'令牌已被停用!');
            return null;
        }
        if(!isset($_model['appkey']) || empty($_model['appkey'])){
            $this->send_callback_error(-203,'机构未设置密钥!');
            return null;
        }
        return $_model['appkey'];
    }

    /**
     * 发送反馈成功消息。
     * @param  mixed $data 反馈数据。
     * @return void
     */
    protected function send_callback_success($data=null){
        $this->response(build_callback_success($data));
    }
    /**
     * 发送反馈失败消息。
     * @param  integer $code  错误代码。
     * @param  string  $msg   错误消息。
     * @return void
     */
    protected function send_callback_error($code=0,$msg=''){
        $this->response(build_callback_error($code,$msg));
    }

    /**
     * 重载数据反馈。
     * @param  mixed   $data  数据
     * @param  string  $type  数据格式
     * @param  integer $code  返回代码
     * @return void
     */
    protected function response($data,$type='json',$code=200){
        if(APP_DEBUG) trace('数据反馈response...');
        parent::response($data,$type,$code);
    }

    /**
     * 重载数据编码。
     * @param  mixed  $data  数据
     * @param  string $type  数据格式
     * @return string        格式化化后的字符串
     */
    protected function encodeData($data,$type='') {
        if(APP_DEBUG) trace('重载数据编码encodeData...');
        if(empty($data)) return '';
        if('json' == $type){
            //设置内容类型
            $this->setContentType($type);
            //设置数据
            return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        }else{
            return parent::encodeData($data,$type);
        }
    }

}