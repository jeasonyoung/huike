<?php
/**
 * 学员建议数据模型
 */
namespace ApiService\Model;
use Think\Model;

class SuggestModel extends Model{
    //定义表名
    protected $tableName = 'learn_advice';

    /**
     * 保存学员建议。
     * @param  array $data 建议数组
     * @return array       反馈数据
     */
    public function saveSuggest($data){
        if(APP_DEBUG) trace('保存学员建议数据...');
        //处理数据
        $_records = array();
        $_records['UID']            = $data['studentId'];
        $_records['Content']        = $data['content'];
        $_records['create_time']    = date('Y-m-d H:i:s', time());
        //插入数据    
        $_insert = $this->add($_records);
        if(!$_insert){
            if(APP_DEBUG) trace('插入建议数据失败!');
            return build_callback_data(false,null,'保存建议数据失败!');
        }
        return build_callback_data(true);
    }
}
