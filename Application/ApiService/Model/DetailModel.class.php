<?php
/**
 * 答疑明细数据模型
 */
namespace ApiService\Model;
use Think\Model;

class DetailModel extends Model{
    //定义表名
    protected $tableName = 'learn_answer';
    //定义字段映射
    protected $_map = array(
        'topicId'       =>  'topicid',
        'userId'        =>  'userid',
        'userName'      =>  'username',
        'createTime'    =>  'createtime',
    );
    /**
     * 新增答疑明细。
     * @param  array $data  答疑明细数据。
     * @return array
     */
    public function addDetail($data){
        if(APP_DEBUG) trace('新增答疑明细数据...');
        //处理数据
        $_records = array();
        $_records['AskID']          = $data['topicId'];
        $_records['UID']            = $data['userId'];
        $_records['Content']        = $data['content'];
        $_records['create_time']    = date('Y-m-d H:i:s', time());
        //插入数据
        $_insert = $this->add($_records);
        if(!$_insert){
            if(APP_DEBUG) trace('插入答疑明细数据失败!');
            return build_callback_data(false,null,'新增答疑明细失败!');
        }
        return build_callback_data(true);
    }

    /**
     * 加载答疑主题ID下的明细集合。
     * @param  string $topicId  答疑主题ID。
     * @return array            返回数据。
     */
    public function loadDetails($topicId){
        if(APP_DEBUG) trace('加载答疑明细数据...'.$topicId);
        //查询数据
        $_result = $this->table('hk_app_details_view');
        $_result = $_result->where("`topicId` = '%s'",array($topicId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`createTime` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载答疑明细失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
    }
}