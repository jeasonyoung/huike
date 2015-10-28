<?php
/**
 * 学习记录数据模型
 */
namespace ApiService\Model;
use Think\Model;

class LearnLogModel extends Model{
    //定义表名
    protected $tableName = 'learn_log';
    /**
     * 保存学习记录数据。
     * @param  array $data 学习记录数据。
     * @return array       反馈数据。
     */
    public function saveLearning($data){
        if(APP_DEBUG) trace('保存学习记录数据...');
        //处理数据
        $_records = array();
        $_records['UID']        = $data['studentId'];
        $_records['LessonID']   = $data['lessonId'];
        $_records['status']     = intval($data['status']);
        $_records['pos']        = intval($data['position']);
        
        //检查数据是否存在
        $_total = $this->where("`lessonid` = '%s' and `uid` = '%s'", array($data['lessonId'],$data['studentId']));
        $_total = $_total->count();
        if($_total && $_total > 0){
            $_records['last_time'] = date('Y-m-d H:i:s', time());
            if(APP_DEBUG) trace('学习记录存在，更新数据...'.serialize($_records));
            $_update = $this->where("`lessonid` = '%s' and `uid` = '%s'", array($data['lessonId'],$data['studentId']));
            $_update = $_update->save($_records);
            if(!$_update){
                if(APP_DEBUG) trace('更新数据失败!');
                return build_callback_data(false,null,'更新数据失败!');
            }
            return build_callback_data(true);
        }else{
            $_records['create_time'] = date('Y-m-d H:i:s', time());
            if(APP_DEBUG) trace('学习记录不存在，插入数据...'.serialize($_records));
            $_insert = $this->add($_records);
            if(!$_insert){
                if(APP_DEBUG) trace('插入数据失败!');
                return build_callback_data(false,null,'插入数据失败!');
            }
            return build_callback_data(true);
        }
    }

}