<?php
namespace Home\Controller;
use Think\Controller;

class DefTaoCanController extends Controller{
    /*添加默认套餐*/
    public function add_user($examid){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        $model = D('Home/DefTaoCan');
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
                $this->success('新增班级成功!',U('DefTaoCan/list_user').'&examid='.$examid.'&subid='.$subid);
            }else{
                $this->error('新增班级失败!',U('DefTaoCan/add_user').'&examid='.$examid.'&subid='.$subid);
            }
        }else{
			$ExamName = array();
			$TaoCanType = $model->query_taocantype();
			$this->assign('TaoCanType',$TaoCanType);
			$Province = $model->query_province();
			$this->assign('ProList',$Province);
            $this->display();
        }
    }
    
    /**
     * 删除考试
     * @param int $subid 考试科目ID
     * @return int 影响行数
     */
    public function del_user($subid){
        $model = D('Home/DefTaoCan');
        if($model->delete_user($subid)){
            $this->success('删除考试科目成功',U('DefTaoCan/list_user'));
        }else{
            $this->error('删除考试科目失败,请联系技术人员');
        }
    }
    
    /**
     * 修改考试
     * @param int $subid 考试科目ID
     */
    public function edit_user($subid,$examid,$scid){
        $model = D('Home/DefTaoCan');
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
                $this->success('考试科目修改成功',U('DefTaoCan/list_user',array('examid' => $examid,'subid' => $subid)));
            }else{
                $this->error('修改失败或未做修改',U('DefTaoCan/edit_user',array('examid' => $examid,'subid' => $subid,'scid' => $scid)));
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
			if(!strtotime($data['validity'])==1){
				$data['usedate'] = $data['validity'];
			}
			else{
				$data['usedate'] = '-1';
			}
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*考试列表*/
	 public function select_exam(){
        $model = D('Home/DefTaoCan');
        $data = $model->query_select_exam();
		$this->assign('exams',$data);
        $this->display('select_exam');
    }
	/*默认套餐列表*/
    public function list_user($examid){
        $model = D('Home/DefTaoCan');
        $data = $model->query_user($examid,'');
		$this->assign('taocanlist',$data);
        $this->display();
    }
}