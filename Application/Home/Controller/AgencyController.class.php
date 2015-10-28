<?php
namespace Home\Controller;
use Think\Controller;

class AgencyController extends Controller{   
    private $province;
    //添加机构用户
    public function add_agency(){
        if(IS_POST){
            $model = D('Home/Agency');
            $rules = C('agencyRules');  //读取添加机构验证规则
            $model->setProperty('_validate', $rules);   //设置动态验证
            //使用create方法自动收集表单
            if(!$result=$model->create()){
                $this->error($model->getError());
            }else{
                //将考试类别转化为字符串保存
                if(is_array($result['AllExams'])){
                    $result['AllExams']= implode($result['AllExams'],',');
                }
                if($agencyId=$model->insert_agency($result)){
                    //添加机构成功,为机构添加默认管理员
                    $this->success('新增机构成功!',U('agency/add_user',array('aid' => $agencyId)));
                }else{
                    $this->error('新增机构失败');
                }
            }
        }else{
            $exam = A('ExamClass');
            $admin = A('Admin');
            $examlist = $exam->data_query();
            $adminlist = $admin->get_data();
            $this->assign('exams',$examlist);   //考试类别列表
            $this->assign('admin',$adminlist);
            $this->assign('province',$this->province);  //地区-省
            $this->display();
        }
    }
    
    /**
     * 修改合作机构
     * @param int $aid 机构ID
     */
    public function edit_agency($jgid){
        $model = D('Home/Agency');
        if(IS_POST){
            
        }else{
            $data = $model->get_agencyList('*','jgid='.$jgid,FALSE);
            /*地区相关信息*/
            $citys = A('Citys');
            $citylist = $citys->optSelect(2,array('cityid','shortname'),$data['province']);
            $countylist = $citys->optSelect(3,array('cityid','shortname'),$data['city']);
            $area = array(
                'province' => $this->province,
                'citys'    => $citylist,
                'county'   => $countylist
            );
            /*考试类别*/
            $exam = A('ExamClass');
            $examlist = $exam->data_query();

            /*管理员相关信息*/
            $admin = A('Admin');
            $adminlist = $admin->get_data();

            $this->assign('area',$area);        //地区
            $this->assign('exams',$examlist);   //考试类别列表
            $this->assign('admin',$adminlist);  //管理员选择列表
            $this->assign('data',$data);
            $this->display();
        }
    }
    
    /**
     * 删除合作机构
     */
    public function del_agency($jgid){
        $model = D('Home/Agency');
        if($model->delete_agency($jgid)){
            $this->success('成功删除一个合作机构',U('agency/list_agency'));
        }else{
            $this->error('删除合作机构失败');
        }
    }
    
    //添加机构用户
    public function add_user(){
        $model = D('Home/Agency');
        if(IS_POST){
            $JGID = I('JGID');
            $agencyInfo = $model->query_agency('md5key','jgid='.$JGID);
            $data = array();
            $data['UserName'] = I('UserName');
            $data['PassWords'] = md5($agencyInfo['md5key'].I('PassWords'));
            $data['JGID'] = $JGID;
            $data['Lock'] = I('Lock');
            $data['RegTime'] = date('Y-m-d H:i:s',time());
            $data['GroupID'] = I('GroupID');
            $data['LoginNum'] = 0;
            $data['RealName'] = I('RealName');
            if(empty($data['UserName']) || empty($data['PassWords']) || empty($data['RealName'])){
                $this->error('用户名，密码，真实姓名为必填项');
            }
            if(I('PassWords')!==I('RePassWords')){
                $this->error('两次输入密码不一样!');
            }
            if($model->query_user("username='".$data['UserName']."'",FALSE)){
                $this->error('用户名已存在');
            }
            if($uid=$model->insert_user($data)){
                //更新机构默认管理员字段
                $db = M('jigou');
                $db->where('jgid='.$JGID)->setField('JG_UID',$uid);
                $this->success('新增机构用户成功!',U('agency/list_agency'));
            }else{
                $this->error('新增机构用户失败');
            }
        }else{
            if(empty(I('get.aid'))){
                $agency = $model->get_agencyList(array('`company`','`jgid`'),'statetf=1');
                $this->assign('agency',$agency);
            }
            $this->display();
        }
    }
    
    //机构列表
    public function list_agency(){
        $model = D('Home/Agency');
        $field = array('`company`','`domain`','`contact`','`statetf`','`hztel`','`stutel`','`create_time`','`jgid`','`marketid`','`kefuid`','a.realname' => 'kfname','b.realname' => 'marketname');
        $info = $model->get_agencyList($field);
        $this->assign('info',$info);
        $this->display();
    }

    public function _initialize(){
        $opt = A('Citys');
        if(!F('province_temp')){
            $province = $opt->optSelect();
            F('province_temp',$province);
        }
        $this->province = F('province_temp');
    }
}