<?php
/**
 * 机构数据模型
 */
namespace ApiService\Model;
use Think\Model;

class GroupModel extends Model{
    //定义视图名
    protected $tableName = 'app_groups_view';
    //定义字段映射
    protected $_map = array(
        'orderNo'   =>  'orderno',
    );

    /**
     * 加载机构考试下的套餐/班级集合
     * @param  string $agencyId 机构ID
     * @param  string $examId   考试ID
     * @return array            返回数据
     */
    public function loadGroupClasses($agencyId,$examId){
        if(APP_DEBUG) trace("加载机构[$agencyId]考试[$examId]下的套餐/班级集合...");
        $_result = $this->field('pid,id,name,type,orderNo');
        $_result = $_result->where("`agencyId` = '%s' and `examId` = '%s'", array($agencyId,$examId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`orderNo` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载班级下的试听课程资源集合失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
    }
}