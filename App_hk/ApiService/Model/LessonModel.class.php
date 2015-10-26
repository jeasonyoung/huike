<?php
/**
 * 课程资源数据模型
 */
namespace ApiService\Model;
use Think\Model;

class LessonModel extends Model{
    //定义视图名
    protected $tableName = 'app_lessons_view';
    //定义字段映射
    protected $_map = array(
        'videoUrl'  =>  'videourl',
        'highVideoUrl'  =>  'highvideourl',
        'superVideoUrl' =>  'supervideourl',
        'orderNo'       =>  'orderno',
    );
    /**
     * 加载班级下的课程资源集合.
     * @param  string $classId  机构班级ID
     * @return array            返回数据
     */
    public function loadLessons($classId){
        if(APP_DEBUG) trace("加载班级[$classId]下的课程资源集合...");
        $_result = $this->field('pid','id','name','type','orderNo');
        $_result = $_result->where("`classId` = '%s'", array($classId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`orderNo` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载班级下的课程资源集合失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
    }
    /**
     * 加载班级下的免费试听课程资源集合
     * @param  string $classId  机构班级ID
     * @return array            返回数据
     */
    public function loadFreeLessons($classId){
        if(APP_DEBUG) trace("加载班级[$classId]下的试听课程资源集合...");
        $_result = $this->field('id,name,videoUrl,highVideoUrl,superVideoUrl,time,orderNo');
        $_result = $_result->table('hk_app_free_lessons_view');
        $_result = $_result->where("`classId` = '%s'", array($classId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`orderNo` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载班级下的试听课程资源集合失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);

    }
}