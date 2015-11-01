<?php
namespace Agency\Controller;
use Think\Controller;

class JigouClassController extends Controller{
   /*添加机构班级*/
    public function add_class($examid){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Agency/JigouClass');
            $data = $model->create();
            $data['JGID'] = session('JGID');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if(($data['SCID'])==='false'){
                 $this->error('系统班级未选择！');
            }
            if(empty($data['CnName'])||empty($data['sale_price'])||empty($data['price'])){
                $this->error('班级名称、销售价格、优惠价格必须填写!');
            }
            if($model->insert_class($data)){
                $this->success('新增机构班级成功!',U('JigouClass/list_class',array('examid' => $examid)));
            }else{
                $this->error('新增机构班级失败!');
            }
        }else{  
            $model = D('Agency/ClassSys');
            $data = $model->query_exam_sysclass($examid);            
            $this->assign('classlist',$data);
            $model1 = D('Agency/Examclass');
            $data1 = $model1->query_select_exam($examid);
            $this->assign('exams',$data1);
            $this->display();
        }
    }
    /**
     * 添加考试下所有班级班级
     * @param int $examid 考试id
     */
    public function add_allclass($examid){
            $model = D('Agency/JigouClass');
            $num = $model->execute("insert into HK_JIGou_Class(SCID,ExamID,SubID,CnName,PicPath,sale_price,price,StateTF,ClassNum,create_time,JGID)"
            . "select SCID,ExamID,SubID,SCName,PicPath,sale_price,Price,StateTF,ClassNum,now(),(select JGID from hk_JiGou where JGID=".session("JGID").") "
                    . "as JGID from HK_Class_Sys where examid=".$examid);
            if($num){
                $this->success('新增机构班级成功!',U('JigouClass/list_class',array('examid' => $examid)));
            }else{
                 $this->error('新增机构班级失败!',U('JigouClass/add_class',array('examid' => $examid)));
            }
    }
    /**
     * 删除机构班级
     * @param int $jgcid 机构班级ID
     * @return int 影响行数
     */
    public function del_class($jgcid,$examid){
        $model = D('Agency/JigouClass');
        if($model->delete_class($jgcid)){
            $this->success('删除成功',U('JigouClass/list_class',array('examid' => $examid)));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改机构班级
     * @param int $jgcid 机构班级ID
     */
    public function edit_class($jgcid,$examid){
        $model = D('Agency/JigouClass');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
           $data = $model->create();
            if(empty($data['CnName'])||empty($data['sale_price'])||empty($data['price'])){
                $this->error('班级名称、销售价格、优惠价格必须填写!');
            }
            $data['JGCID']=$jgcid;
            if($model->update_class($data)){
                $this->success('修改成功',U('JigouClass/list_class',array('examid' => $examid)));
            }else{
                $this->error('修改失败或未做修改',U('JigouClass/edit_class',array('jgcid' => $jgcid)));
            }
        }else{
            $data = $model->query_class($jgcid,$examid);
            $this->assign('info',$data);
            $this->display('edit_class');
        }
    }
        /*考试列表*/
    public function select_exam($num){
        $model = D('Agency/Jigou');
        $jigou = $model->query_jigou(session('JGID'));
        $allexams = $jigou['allexams'];
        $model1 = D('Agency/Examclass');
        $data = $model1->query_exam_class($allexams);
	$this->assign('exams',$data);
        $this->display();
    }
    /*机构班级列表*/
    public function list_class($examid){
        $model = D('Agency/JigouClass');
        $data = $model->query_class(null,$examid);
        $this->assign('classlist',$data);
        $model1 = D('Agency/Examclass');
        $exam = $model1->query_select_exam($examid);
        $this->assign('info',$exam);
        $this->display();
    }
     public function details_class($jgcid){
        $model = D('Agency/JigouClass');
        $data = $model->query_details_class($jgcid);
        $this->assign('classinfo',$data);
        $this->display();
    }
    /**
     * 系统班级表关联数据
     * @param int $scid 系统班级ID
     */
    public function query_sys_class($scid){
        $model = D('Agency/ClassSys');
        $data = $model->query_sysclass($scid);
        $this->ajaxReturn($data,'JSON');
    }
}