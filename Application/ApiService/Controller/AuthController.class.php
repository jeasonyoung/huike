<?php
/**
 * 接口验证基类。
 */
namespace ApiService\Controller;
use Think\Controller\RestController;

class AuthController extends RestController{

    /**
     * 根据令牌获取所属机构ID.
     * @param  string $token 令牌。
     * @return string        所属机构ID。
     */
    protected function loadAgencyIdByToken($token=''){
        if(APP_DEBUG) trace("根据令牌[$token]获取所属机构ID...");
        if(isset($token)) return null;
        //缓存
        static $_agencyIds = array();
        //从缓存中加载数据
        if(isset($_agencyIds[$token])){
            return $_agencyIds[$token];
        }
        //从表中查找数据
        $_model = M('Jigou')->field('JGID')
                            ->where("`abbr_en` = '%s'", array($token))
                            ->find();
        if($_model){
            $_agencyIds[$token] = $_model['jgid'];
            return $_agencyIds[$token];
        }
        return null;
    }

    


    /**
     * 发送反馈消息
     * @param  boolean $success 请求状态。
     * @param  mixed   $data    反馈数据。
     * @param  string  $msg     错误消息。
     * @return void
     */
    protected function send_callback($success=false,$data=null,$msg=''){
        $this->response(build_callback_data($success,$data,$msg));
    }    

    /**
     * 重载数据反馈。
     * @param  [type]  $data [description]
     * @param  string  $type [description]
     * @param  integer $code [description]
     * @return [type]        [description]
     */
    protected function response($data,$type='json',$code=200){
        if(APP_DEBUG) trace('数据反馈response...');
        parent::response($data,$type,$code);
    }

    /**
     * 重载数据编码。
     * @param  [type] $data [description]
     * @param  string $type [description]
     * @return [type]       [description]
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