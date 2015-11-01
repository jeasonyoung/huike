<?php
/**
 * 机构套餐分类数据模型操作
 */
namespace Agency\Model;
use Think\Model;

class JigouTaocanClassModel extends Model{
    //定义表名
    protected $tableName = 'JiGou_TaoCan_Class';

    //验证
    protected $_validate = array(
        array('TCCName','require','分类名称不能为空'),
        array('TCCName','','此分类名称已存在',0,'unique',3),

        array('TCTypeID','require','套餐类型不能为空'),

        array('UseYear','require','使用年份不能为空'),
        array('UseYear','/^\d{4}$/','使用年份必须为四位数字',0,'regex'),

        array('sale_price','require','销售价格不能为空'),
        array('sale_price','/^(\d*\.)?\d+$/','销售价格必须为数字',0,'regex'),

        array('price','require','优惠价格不能为空'),
        array('price','/^(\d*\.)?\d+$/','优惠价格必须为数字',0,'regex'),

        array('SortID','require','排序不能为空'),
        array('SortID','number','排序必须为数字'),
    );

    /**
     * 加载机构下的套餐分类数据集合。
     * @param  string $agencyId 机构ID。
     * @return mixed            套餐分类数据。
     */
    public function loadTaocanClasses($agencyId){
        if(APP_DEBUG) trace("加载机构[$agencyId]下的套餐分类数据集合...");
        $_result = $this->table('hk_agency_group_type_view')
                        ->where("`agency_id` = '%s'", array($agencyId))
                        ->order('`order_no` desc')
                        ->select();
        return $_result;
    }

    /**
     * 加载套餐分类数据。
     * @param  string $id 套餐分类ID
     * @return mixed      加载数据结果
     */
    public function loadTaocanClass($id){
        if(APP_DEBUG) trace("加载套餐分类数据...$id");
        $_result = $this->where("`TCCID` = '%s'", array($id))
                        ->find();
        return $_result;
    }

    /**
     * 添加机构套餐分类数据。
     * @param array $data 添加数据
     * @param mixed 添加结果
     */
    public function addTaocanClass($data){
        if(APP_DEBUG) trace('添加套餐分类...'.serialize($data));
        return $this->add($data);
    }

    /**
     * 更新机构套餐分类数据。
     * @param   array $data 更新数据
     * @return  mixed       更新结果
     */
    public function updateTaocanClass($data){
        if(APP_DEBUG) trace('更新套餐分类...'.serialize($data));
        return $this->save($data);
    }

    /**
     * 删除机构套餐分类数据。
     * @param  mixed $ids  删除主键。
     * @return mixed       删除结果
     */
    public function deleteTaocanClass($ids){
        if(APP_DEBUG) trace('删除套餐分类...'.serialize($ids));
        return $this->delete($ids);
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
     * 加载系统套餐类型集合。
     * @return mixed 系统套餐类型集合。
     */
    public function loadSysTaocanClasses(){
        if(APP_DEBUG) trace('加载系统套餐类型集合...');
        $_result = $this->field('`TCTypeID` as id,`TCTypeName` as name,discount')->table('HK_Class_TaoCanType')
                        ->order('`SortID` asc')->select();
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