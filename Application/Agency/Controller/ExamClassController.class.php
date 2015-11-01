<?php
namespace Agency\Controller;
use Agency\Controller\AdminController;

class ExamClassController extends AdminController{
    
    /**
     * 修改当前机构包含考试信息
     * @param int $jgid 用户ID
     */
    public function edit_jigou(){
        $model = D('Agency/Jigou');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交     
            $data = array();
            $data['JGID'] = I('JGID');
            $array = I('ExamID');
            $size = count($array);  
            for($i=0; $i< $size; $i++){
                if($i==0){
                    $AllExamID = $array[$i];
		}else{
                    $AllExamID = $AllExamID.','.$array[$i];
		}
            }
            $data['AllExams'] = $AllExamID;
            if($model->update_jigou($data)){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败或未做编辑');
           }
        }else{
            $examclass = D('Agency/Examclass');
            $exams = $examclass->query_exam_class();
            $this->assign('examlist',$exams);
            $jigoumodel = $model->query_jigou(session('JGID'));
            $this->assign('info',$jigoumodel);
            $this->display('list_exam_class');
        }
    }
    /*查询考试类目信息*/
    public function list_exam_class(){
        $model = D('Agency/Examclass');
        $data = $model->query_exam_class();
        $this->assign('examlist',$data);
        $jigou = D('Agency/Jigou');
        $jigoumodel = $jigou->query_jigou(session('JGID'));
        $this->assign('info',$jigoumodel);
        $this->display();
    }
}