<?php
/**
 * 考试数据模型
 */
namespace ApiService\Model;
use Think\Model;

class ExamModel extends Model{
    //定义视图名
    protected $tableName = 'ExamClass';
    //定义字段映射
    protected $_map = array(
        'id'        =>  'examid',
        'name'      =>  'cnname',
        'abbr'      =>  'enname',
        'orderNo'   =>  'sortid',
    );

    /**
     * 加载机构下的考试集合
     * @param  string $agencyId 机构ID
     * @return array            返回数据
     */
    public function loadExams($agencyId){
        if(APP_DEBUG) trace("加载机构[$agencyId]下的考试集合...");
        $_result = $this->field('examId,CnName,EnName,SortID');
        $_result = $_result->where("`examId` in (select `allExams` from HK_JiGou where `JGID` = '%s')", array($agencyId));
        $_result = $_result->limit(C('QUERY_LIMIT_TOP'))->order("`SortID` desc")->select();
        if(!$_result){
            if(APP_DEBUG) trace('加载班级下的试听课程资源集合失败!');
            return build_callback_data(false,null,'无数据!');
        }
        return build_callback_data(true,$_result);
    }

}