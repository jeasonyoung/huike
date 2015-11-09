<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class SysClassController extends BaseController{
    /*添加考试科目*/
    public function add_user($examid,$subid){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        $model = D('Home/SysClass');
        if(IS_POST){
            $data = array();
            $data['ExamID'] = $examid;
            $data['SubID'] = $subid;
            $data['SCName'] = I('SCName');
            $data['CTID'] = I('CTID');
            $data['TeacherID'] = I('TeacherID');
			if(!empty(I('Province'))){
				$data['Province'] = I('Province');
			}            
			if (I('UseDate1')=='-1'){
				$data['validity'] = I('validity');
			}
			else{
				$data['validity'] = I('UseDate1');
			}
			if(I('StateTF')=='1'){
				$data['StateTF'] = 1;
			}
			else{
				$data['StateTF'] = 0;
			}
			$data['EndDate'] = I('EndDate');
			$data['PicPath'] = I('PicPath');
			$data['Price'] = I('Price');
			$data['ClassNum'] = I('ClassNum');
			if(!empty(I('Year'))){$data['Year'] = I('Year');}
            $data['create_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
            if(empty($data['SCName']) || empty($data['ExamID']) || empty($data['SubID']) || empty($data['CTID'])){
                $this->error('班级名称、所属考试、所属科目、班级类型必须填写!');
            }
			if(empty($data['TeacherID']) || empty($data['validity']) || empty($data['Price']) || empty($data['ClassNum'])){
                $this->error('主讲老师、有效期、招生截至时间、价格、课时数必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增班级成功!',U('SysClass/list_user',array('examid' => $examid,'subid' => $subid)));
            }else{
                $this->error('新增班级失败!',U('SysClass/add_user',array('examid' => $examid,'subid' => $subid)));
            }
        }else{
			$ExamName = array();
			$ExamName = $model->query_examsub($subid);
			$this->assign('ExamName',$ExamName);
			$ClassType = $model->query_classtype();
			$this->assign('ClassType',$ClassType);
			$Teacher = $model->query_teacher($examid);
			$this->assign('teachlist',$Teacher);
			$Province = $model->query_province();
			$this->assign('ProList',$Province);
            $this->display();
        }
    }
    
    /**
     * 删除考试班级
     * @param int $subid 考试科目ID
     * @param int $scid 考试班级ID
     * @param int $examid 考试ID
     * @return int 影响行数
     */
    public function del_user($scid,$subid,$examid){
        $model = D('Home/SysClass');
        if($model->delete_user($scid)){
            $this->success('删除考试班级成功',U('SysClass/list_user',array('examid' => $examid,'subid' => $subid)));
        }else{
            $this->error('删除考试班级失败,请联系技术人员');
        }
    }
    
    /**
     * 修改考试系统班级
     * @param int $subid 考试科目ID
     * @param int $scid 考试班级ID
     * @param int $examid 考试ID
     */
    public function edit_user($subid,$examid,$scid){
        $model = D('Home/SysClass');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['SCID'] = $scid;
            $data['ExamID'] = $examid;
            $data['SubID'] = $subid;
            $data['SCName'] = I('SCName');
            $data['CTID'] = I('CTID');
            $data['TeacherID'] = I('TeacherID');
			if(!empty(I('Province'))){
				$data['Province'] = I('Province');
			}            
			if (I('UseDate1')=='-1'){
				$data['validity'] = I('validity');
			}
			else{
				$data['validity'] = I('UseDate1');
			}
			if(I('StateTF')=='1'){
				$data['StateTF'] = 1;
			}
			else{
				$data['StateTF'] = 0;
			}
			$data['EndDate'] = I('EndDate');
			$data['PicPath'] = I('PicPath');
			$data['Price'] = I('Price');
			$data['ClassNum'] = I('ClassNum');
			if(!empty(I('Year'))){$data['Year'] = I('Year');}			
            $data['last_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
            if(empty($data['SCName']) || empty($data['ExamID']) || empty($data['SubID']) || empty($data['CTID'])){
                $this->error('班级名称、所属考试、所属科目、班级类型必须填写!');
            }
			if(empty($data['TeacherID']) || empty($data['validity']) || empty($data['Price']) || empty($data['ClassNum'])){
                $this->error('主讲老师、有效期、招生截至时间、价格、课时数必须填写!');
            }
            if($model->update_user($data)){
                $this->success('考试科目修改成功',U('SysClass/list_user',array('examid' => $examid,'subid' => $subid)));
            }else{
                $this->error('修改失败或未做修改',U('SysClass/edit_user',array('examid' => $examid,'subid' => $subid,'scid' => $scid)));
            }
        }else{
			$ExamName = array();
			$ExamName = $model->query_examsub($subid);
			$this->assign('ExamName',$ExamName);
			$ClassType = $model->query_classtype();
			$this->assign('ClassType',$ClassType);
			$Teacher = $model->query_teacher($examid);
			$this->assign('teachlist',$Teacher);
			$Province = $model->query_province();
			$this->assign('ProList',$Province);
            $data = $model->query_user($subid,$scid);
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*考试列表*/
    public function list_user($subid){
        $model = D('Home/SysClass');
        $data = $model->query_user($subid,'');
		$this->assign('sublist',$data);
        $this->display();
    }
}