<?php
/**
 * 学生答疑标题数据模型
 */
namespace ApiService\Model;
use Think\Model;

class TopicModel extends Model{
    //定义表名
    protected $tableName = 'learn_ask';
    //定义字段映射
    protected $_map = array(
        'lessonId'       =>  'lessonid',
        'lessonName'     =>  'lessonname',
        'lastTime'      =>  'lasttime',
    );
    /**
     * 新增答疑主题。
     * @param  array $data 保存数据。
     * @return array       返回数组。
     */
    public function addTopic($data){
        if(APP_DEBUG) trace('新增答疑主题数据...');
        //处理数据
        $_records = array();
        $_records['UID']            = $data['studentId'];
        $_records['LessonID']       = $data['lessonId'];
        $_records['Title']          = $data['title'];
        $_records['Content']        = $data['content'];
        $_records['create_time']    = $_records['last_time']   = date('Y-m-d H:i:s', time());
        //插入数据
        $_insert = $this->add($_records);
        if(!$_insert){
            if(APP_DEBUG) trace('插入答疑主题数据失败!');
            return build_callback_data(false,null,'新增答疑主题失败!');
        }
        return build_callback_data(true);
    }

    /**
     * 加载学员用户ID下答疑主题集合。
     * @param  string $userId 学员用户ID。
     * @return array          返回数据。
     */
    public function loadTopics($userId){
        if(APP_DEBUG) trace('加载答疑主题数据...'.$userId);
        //查询数据
        $_result = $this->field('id,title,content,lessonID,lessonName,lastTime');
        $_result = $_result->table('hk_app_topics_view');
        $_result = $_result->where("`userId` = '%s'",array($userId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`lastTime` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载答疑主题失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
        
    }
}