<?php
/**
 * 我的课程数据模型
 */
namespace ApiService\Model;
use Think\Model;

class CourseModel extends Model{
    //定义视图名
    protected $tableName = 'app_courses_view';
    //定义字段映射
    protected $_map = array(
        'orderNo'       =>  'orderno',
    );
    /**
     * 加载用户下的套餐/班级集合.
     * @param  string $userId  学员用户ID
     * @return array           返回数据
     */
    public function loadCourses($userId){
        if(APP_DEBUG) trace("加载用户下的套餐/班级集合...$userId");
        $_result = $this->field('pid,id,name,type,orderNo');
        //->table('hk_app_courses_view');
        $_result = $_result->where("`userId` = '%s'",array($userId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`orderNo` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载用户下的套餐/班级集合失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
    }
}