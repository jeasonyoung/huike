<?php
/**
 * 摘要认证数据模型
 */

namespace ApiService\Model;
use Think\Model;

class DigestAuthModel extends Model{
    //定义表名
    protected $tableName = 'jigou';

    /**
     * 根据简称获取机构接口密钥
     * @param  String $abbr 机构简称
     * @return String       接口密钥
     */
    public function loadAppKey($abbr=null){
        if(APP_DEBUG) trace('根据机构简称['.$abbr.']查询接口密钥..');
        if(is_null($abbr) || empty($abbr)){
            if(APP_DEBUG) trace('机构简称为空!');
            return null;
        }
        $_data = $this->field('appkey')->where("`abbr_en` = '%s'", array($abbr))->find();
        if(!$_data){
            if(APP_DEBUG) trace('机构简称['.$abbr.']不存在!');
            return null;
        }
        return $_data['appkey'];
    }
}