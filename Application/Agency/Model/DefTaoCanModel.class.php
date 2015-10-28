<?php
namespace Home\Model;
use Think\Model;

class DefTaoCanModel extends Model{
    /**
     * 向考试表插入考试数据
     * @param array $data 所添加的考试信息
     */
	protected $trueTableName = 'HK_Class_TaoCan';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询考试班级数据
     * @param int $subid 考试科目id
     */
    public function query_user($examid){
        if(isset($examid) && !empty($examid)){
			$TempStr = $this->join('LEFT JOIN HK_Class_TaoCanType as TCT ON HK_Class_TaoCan.TCTypeID = TCT.TCTypeID');
			$TempStr = $TempStr->field('tcid,defcnname,TCTypeName,total_price,sale_price,StateTF,validity,enddate,HK_Class_TaoCan.create_time,HK_Class_TaoCan.last_time,HK_Class_TaoCan.sortid');
			$TempStr = $TempStr->where('examid='.$examid)->select();
            return $TempStr;
        }
		else{
			echo '考试ID不能为空！';
			exit();
		}
    }
	//获取考试
    public function query_select_exam(){
        return $this->table('HK_ExamClass')->field('ExamID,CnName')->order('SortID')->select();
    }
	//获取套餐类型
	 public function query_taocantype(){
        return $this->table('HK_Class_TaoCanType')->order('SortID')->select();
    }
	//获取省份
	public function query_province(){
        return $this->table('HK_Citys')->field('CityID,CityName')->where('ParentID=0 and level=1')->order('SortID')->select();
    }
    /**
     * 修改考试科目信息
     * @param array $data 所修改的考试信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除考试科目信息
     * @param int $subid 要删除的考试科目id
     */
    public function delete_user($subid){
        return $this->where('subid='.$subid)->delete();
    }
}