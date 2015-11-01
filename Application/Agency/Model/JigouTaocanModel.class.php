<?php
/**
 * 机构套餐数据模型。
 */
namespace Agency\Model;
use Think\Model;

class JigouTaocanModel extends Model{
    //定义表名
    protected $tableName = 'JiGou_TaoCan_List';

    //验证
    protected $_validate = array(
        array('TaoCanName','require','套餐名称不能为空'),
        array('TaoCanName','','此套餐名称名称已存在',0,'unique',3),

        array('TCCID','require','套餐分类不能为空'),

        array('Sale_Price','require','销售价格不能为空'),
        array('Sale_Price','/^(\d*\.)?\d+$/','销售价格必须为数字',0,'regex'),

        array('Real_Price','require','优惠价格不能为空'),
        array('Real_Price','/^(\d*\.)?\d+$/','优惠价格必须为数字',0,'regex'),

        array('SortID','require','排序不能为空'),
        array('SortID','number','排序必须为数字'),
    );

    /**
     * 加载机构下的套餐数据集合。
     * @param  string $agencyId 机构ID。
     * @return mixed            套餐分类数据。
     */
    public function loadTaocans($agencyId){
        if(APP_DEBUG) trace("加载机构[$agencyId]下的套餐数据集合...");
        $_result = $this->table('hk_agency_group_view')
                        ->where("`agency_id` = '%s'", array($agencyId))
                        ->order('`order_no` desc')
                        ->select();
        return $_result;
    }

    /**
     * 加载系统套餐数据集合。
     * @param  string $agencyId 机构ID
     * @param  string $examId   考试ID
     * @return mixed            系统套餐数据
     */
    public function loadSysTaocans($agencyId,$examId){
        if(APP_DEBUG) trace("加载机构[$agencyId]系统套餐数据集合...");
        $_result = $this->table('hk_sys_group_view')
                        ->where("`agency_id` = '%s' and `exam_id` = '%s'", array($agencyId,$examId))
                        ->order('`order_no` desc')
                        ->select();
        return $_result;
    }

    /**
     * 加载套餐数据。
     * @param  string $id 套餐ID
     * @return mixed      加载数据结果
     */
    public function loadTaocan($id){
        if(APP_DEBUG) trace("加载套餐数据...$id");
        $_result = $this->where("`TaoCanID` = '%s'", array($id))
                        ->find();
        return $_result;
    }

    /**
     * 加载套餐分类数据。
     * @param  string $typeId 套餐分类ID
     * @return array          套餐分类数据
     */
    public function loadTaocanClass($typeId){
        if(APP_DEBUG) trace("加载套餐分类数据...$typeId");
        if(!isset($typeId) || empty($typeId)) return null;
        $_result = $this->field('id,name,exam_id,exam_name')
                        ->table('hk_agency_group_type_view')
                        ->where("`id` = '%s'", array($typeId))
                        ->find();
        return $_result;
    }

    /**
     * 加载考试下科目数据集合。
     * @param  string $examId 考试ID
     * @return array          科目数据集合
     */
    public function loadSubjects($examId){
        if(APP_DEBUG) trace("加载考试[$examId]下科目数据集合...");
        if(!isset($examId) || empty($examId)) return null;
        $_result = $this->field('`SubID` as id,`SubName` as name')
                        ->table('HK_Subjects')
                        ->where("`ExamID` = '%s'", array($examId))
                        ->order('`SortID` asc')
                        ->select();
        return $_result;
    }

    /**
     * 加载机构科目下的机构班级数据集合。
     * @param  string $agencyId  机构ID
     * @param  string $subjectId 科目ID
     * @return array            机构班级数据集合
     */
    public function loadAgencyClasses($agencyId,$subjectId=null,$groupId=null){
        if(APP_DEBUG) trace("加载机构[$agencyId]科目[$subjectId]下的机构班级数据集合...");
        
        $_where['status'] = 1;
        $_where['agency_id'] = $agencyId;

        if(isset($subjectId) && !empty($subjectId)){
            $_where['subject_id'] = $subjectId;
        }

        if(isset($groupId) && !empty($groupId)){
            $_where['_string'] = "`id` not in (select `JGCID` from HK_JiGou_TaoCan_Info where `TaoCanID` = '$groupId')";
        }

        $_result = $this->table('hk_agency_class_view')
                        ->where($_where)
                        ->order('order_no desc')
                        ->select();

        return $_result;
    }


    /**
     * 加载Session中机构班级数据集合。
     * @param  string $agencyId  机构ID
     * @param  array  $classIds  班级ID数组
     * @return mixed             班级数据集合
     */
    public function loadSessionAgencyClasses($agencyId,$classIds = null){
        if(APP_DEBUG) trace("加载Session中机构[$agencyId]班级数据集合...".implode(',', $classIds));
        if(!isset($classIds) || empty($classIds)) return null;

        $_result = $this->table('hk_agency_class_view')
                        ->where("`agency_id` = '%s' and (`id` in (%s))", array($agencyId, implode(',', $classIds)))
                        ->order('`subject_name` desc,`type_name` desc, `order_no` desc')
                        ->select();
        return $_result;
    }
    
    /**
     * 加载套餐中的班级数据集合。
     * @param  string $groupId  套餐ID
     * @return mixed            套餐班级数据集合
     */
    public function loadTaocanAgencyClasses($groupId){
        if(APP_DEBUG) trace("加载套餐[$groupId]中的班级数据集合...");
        if(!isset($groupId) || empty($groupId)) return null;

        $_result = $this->table('hk_agency_class_view')
                        ->where("`id` in (select `JGCID` from hk_jigou_taocan_info where `TaoCanID` = '%s')", array($groupId))
                        ->order('`subject_name` desc,`type_name` desc, `order_no` desc')
                        ->select();
        return $_result;
    }

    /**
     * 删除套餐中的班级数据。
     * @param  string $groupId 套餐ID
     * @param  string $classId 班级ID
     * @return mixed           删除数据
     */
    public function delTaocanAgencyClass($groupId,$classId){
        if(APP_DEBUG) trace("删除套餐[$groupId]中的班级[$classId]数据...");
        if(!isset($groupId) || empty($groupId)) return null;
        //初始化套餐班级
        $_model = M('JigouTaocanInfo');
        $_model->where("`TaoCanID` = '%s'", array($groupId))
               ->where("`JGCID` = '%s'", array($classId))
               ->delete();
    }

    /**
     * 添加套餐班级数据。
     * @param string $groupId  套餐ID
     * @param array $classIds  班级IDs数组
     */
    public function addTaocanAgencyClass($groupId,$classIds = null){
        if(APP_DEBUG) trace("添加套餐[$groupId]班级数据...".serialize($classIds));
        if(isset($groupId) && !empty($groupId) && isset($classIds) && !empty($classIds)){
            //初始化套餐班级
            $_model = M('JigouTaocanInfo');
            //循环
            foreach ($classIds as $value) {
                //
                if(!empty($value)){
                    $data['TaoCanID'] = $groupId;
                    $data['JGCID'] = $value;
                    $_model->add($data);
                }
            }
        }
    }

    /**
     * 添加机构套餐数据。
     * @param array $data 添加数据
     * @param array $classIds 班级IDs
     * @param mixed 添加结果
     */
    public function addTaocan($data, $classIds = null){
        if(APP_DEBUG) trace('添加套餐...'.serialize($data));
        $_key = $this->add($data);
        if($_key){
            //初始化套餐班级
            $_model = M('JigouTaocanInfo');
            //删除套餐班级
            $_model->where("`TaoCanID` = '%s' ", array($_key))->delete();
            //添加班级
            if(isset($classIds) && !empty($classIds) && is_array($classIds)){
                //循环添加
                foreach ($classIds as $value) {
                    $data['TaoCanID'] = $_key;
                    $data['JGCID'] = $value;
                    $_model->add($data);
                }
            }
        }
        return $_key;
    }

    /**
     * 更新机构套餐数据。
     * @param   array $data 更新数据
     * @return  mixed       更新结果
     */
    public function updateTaocan($data){
        if(APP_DEBUG) trace('更新机构套餐...'.serialize($data));
        return $this->save($data);
    }

    /**
     * 删除机构套餐数据。
     * @param  mixed $ids  删除主键
     * @return mixed       删除结果
     */
    public function deleteTaocan($ids){
        if(APP_DEBUG) trace('删除机构套餐...'.serialize($ids));
        //判断主键集合是否存在
        if(isset($ids) && !empty($ids)){
            //初始化套餐班级
            $_model = M('JigouTaocanInfo');
            //
            if(is_array($ids)){
                //删除套餐班级
                $_model->where("`TaoCanID` in (%s)", implode(',',$_key));
            }else{
                //删除套餐班级
                $_model->where("`TaoCanID`= '%s'", array($ids));
            }
            //删除套餐班级
            $_model->delete();
            //删除机构套餐
            return $this->delete($ids);
        }
        return false;
    }

    /**
     * 加载机构下最大排序号。
     * @param  string $agencyId 机构ID
     * @return int              最大排序号
     */
    public function loadMaxSort($agencyId){
        if(APP_DEBUG) trace("加载机构[$agencyId]下最大排序号...");
        $_maxOrder = $this->where("`JGID` = '%s'", array($agencyId))->max('`SortID`');
        if($_maxOrder){
            return $_maxOrder + 1;
        }
        return 1;
    }

    /**
     * 加载机构下套餐分类数据集合。
     * @param  string $agencyId 机构ID
     * @return mixed            套餐分类数据集合
     */
    public function loadTaocanClasses($agencyId,$examId=null){
        if(APP_DEBUG) trace("加载机构[$agencyId]下套餐分类数据集合...");
        //
        $_where['agency_id'] = $agencyId;
        if(isset($examId) &&!empty($examId)){
            $_where['exam_id'] = $examId;
        }
        $_result = $this->field('id,name,discount,exam_id,exam_name,use_year')
                        ->table('hk_agency_group_type_view')
                        ->where($_where)
                        ->order('`order_no` desc')
                        ->select();
        return $_result;
    }

    /**
     * 加载机构下考试集合。
     * @param  string $agencyId 机构ID
     * @return mixed            机构考试集合
     */
    public function loadExams($agencyId){
        if(APP_DEBUG) trace("加载机构[$agencyId]下考试集合...");
        //获取机构下考试
        $_result = $this->field('allexams')->table('HK_JiGou')
                        ->where("`JGID` = '%s'", array($agencyId))
                        ->find();
        if($_result){
            //查询考试
            $_result = $this->field('`ExamID` as id,`CnName` as name')
                        ->table('HK_ExamClass')
                        ->where("`ExamID` in (%s)", array($_result['allexams']))
                        ->order('`SortID` asc')->select();
            return $_result;
        }
        return false;
    }
}