<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class ClassResourcesController extends BaseController{
    /*添加考试科目*/
    public function add_user($scid,$yearid){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        $model = D('Home/ClassResources');
        if(IS_POST){
            $data = array();
            $data['SCID'] = $scid;
            $data['CnName'] = I('CnName');
            $data['StartDate'] = I('StartDate');
            $data['MP3Url'] = I('MP3Url');
            $data['VideoUrl'] = I('VideoUrl');
            $data['HightUrl'] = I('HightUrl');
            $data['SuperUrl'] = I('SuperUrl');
            $data['PPTUrl'] = I('PPTUrl');
            $data['ExamID'] = I('examid');
            $data['SubID'] = I('subid');
            $data['Year'] = $yearid;
            $data['create_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
			$data['TimeLen'] = I('TimeLen');
            if(empty($data['CnName']) || empty($data['StartDate']) || empty($data['VideoUrl']) || empty($data['SortID']) || empty($data['Year'])){
                $this->error('课时名称、开放时间、标清视频、课时编号、所属年份必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增课时资源成功!',U('ClassResources/list_user').'&scid='.$scid.'&yearid='.$yearid);
            }else{
                $this->error('新增课时资源失败!',U('ClassResources/add_user').'&scid='.$scid.'&yearid='.$yearid);
            }
        }else{
			$ExamName = array();
			$ExamName = $model->get_examclassname($scid);
			$this->assign('ExamName',$ExamName);
			$DefClassNum = array();
			$DefClassNum = $model->get_defclassnum($scid,$yearid);
			if(count($DefClassNum)==0){
				$DefClassNum['sortid'] = 1;
			}
			$this->assign('DefClassNum',$DefClassNum);
            $this->display();
        }
    }
    
    /**
     * 删除考试
     * @param int $subid 考试科目ID
     * @return int 影响行数
     */
    public function del_user($subid){
        $model = D('Home/ClassResources');
        if($model->delete_user($subid)){
            $this->success('删除考试科目成功',U('ClassResources/list_user'));
        }else{
            $this->error('删除考试科目失败,请联系技术人员');
        }
    }
    
    /**
     * 修改考试
     * @param int $subid 考试科目ID
     */
    public function edit_user($scid,$lessonid){
        $model = D('Home/ClassResources');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $data['LessonID'] = $lessonid;
            $data['SCID'] = $scid;
            $data['CnName'] = I('CnName');
            $data['StartDate'] = I('StartDate');
            $data['MP3Url'] = I('MP3Url');
            $data['VideoUrl'] = I('VideoUrl');
            $data['HightUrl'] = I('HightUrl');
            $data['SuperUrl'] = I('SuperUrl');
            $data['PPTUrl'] = I('PPTUrl');
            $data['ExamID'] = I('examid');
            $data['SubID'] = I('subid');
            $data['Year'] = I('yearid');
            $data['last_time'] = date('Y-m-d H:i:s',time());
            $data['SortID'] = I('SortID');
			$data['TimeLen'] = I('TimeLen');
            if(empty($data['CnName']) || empty($data['StartDate']) || empty($data['VideoUrl']) || empty($data['SortID']) || empty($data['Year'])){
                $this->error('课时名称、开放时间、标清视频、课时编号、所属年份必须填写!');
            }
            if($model->update_user($data)){
                $this->success('课时资源修改成功',U('ClassResources/list_user',array('scid' => $scid,'yearid' => I('yearid'))));
            }else{
                $this->error('修改失败或未做修改',U('ClassResources/edit_user',array('subid' => $subid,'yearid' => I('yearid'))));
            }
        }else{
			$ExamName = array();
			$ExamName = $model->get_examclassname($scid);
			$this->assign('ExamName',$ExamName);
            $data = $model->query_lessoninfo($lessonid);
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*考试列表*/
    public function list_user($scid,$yearid){
        $model = D('Home/ClassResources');
        $data = $model->query_user($scid,$yearid);
		$this->assign('resources',$data);
        $data = $model->query_year($scid);
		if(count($data)==0){
			$data = array(array('year'=>date("Y")-1),array('year'=>date("Y")),array('year'=>date("Y")+1));
		}
		else{
			/*根据情况重新组合数组*/
			$MaxYear = 0;
			$MinYear = date("Y");
			foreach($data as $k=>$val){
				if($val['year']>$MaxYear){
					$MaxYear = $val['year'];
				}
				if($val['year']<$MinYear){
					$MinYear = $val['year'];
				}
			}
			$YearStr='';
			$jj = 0;
			for($ii=$MinYear;$ii<=$MaxYear;$ii++){
				$data[$jj] = array('year'=>$ii);
				$jj = $jj + 1;
			}
		}
		$this->assign('examyears',$data);
		$data = $model->query_scname($scid);
		$this->assign('scname',$data);
        $this->display();
    }
}