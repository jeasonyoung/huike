<?php
/**
 * 机构管理控制器。
 */
namespace Home\Controller;
use Home\Controller\BaseController;

class AgencyController extends BaseController{   
    private $province;
    /**
     * 添加机构用户。
     * @return void
     */
    public function add_agency(){
        if(APP_DEBUG) trace('调用add_agency...');
        $_model = D('Agency');
        if(IS_POST){
            //使用create方法自动收集表单
            if(!$_result=$_model->create()){
                $this->error($_model->getError());
            }else{
                //将考试类别转化为字符串保存
                // if(is_array($result['AllExams'])){
                //     $result['AllExams']= implode($result['AllExams'],',');
                // }
                $_result['create_time'] = date('Y-m-d', time);
                if($agencyId=$_model->insert_agency($result)){
                    //添加机构成功,为机构添加默认管理员
                    $this->success('新增机构成功!',U('Home/Agency/add_user',array('aid' => $agencyId)));
                }else{
                    $this->error('新增机构失败');
                }
            }
        }else{
            $this->assign('markets',$_model->loadAdminUsers(array(1,4)));//市场负责人
            $this->assign('services',$_model->loadAdminUsers(array(3,4)));//客服负责人
            $this->assign('province',$this->province);  //地区-省
            $this->display();
        }
    }

    /**
     * 机构列表。
     * @return void
     */
    public function list_agency(){
        if(APP_DEBUG) trace("调用list_agency...");
        $model = D('Agency');
        $field = array('`company`',
                       '`abbr_cn`',
                       '`domain`',
                       '`dicount`',
                       '`money`',
                       '`contact`',
                       '`statetf`',
                       '`hztel`',
                       '`stutel`',
                       '`create_time`',
                       '`jgid`',
                       '`marketid`',
                       '`kefuid`',
                       'a.realname' => 'kfname',
                       'b.realname' => 'marketname');
        $info = $model->get_agencyList($field);
        $this->assign('info',$info);
        $this->display();
    }
    
    /**
     * 修改合作机构
     * @return void
     */
    public function edit_agency(){
        if(APP_DEBUG) trace("调用edit_agency");
        $_model = D('Agency');
        if(IS_POST){
            if(!($_agency = $_model->loadAgency(I('JGID','')))){
                $this->error("机构已不存在!",U('Home/Agency/list_agency'));
            }
            if(floatval($_agency['money']) <= 0){
                $this->error("机构余额须大于0,否则不允许修改机构信息!",U('Home/Agency/list_agency'));
            }

            if(!$_result=$_model->create()){
                $this->error($_model->getError());
            }else{

                // if(is_array($result['AllExams'])){
                //     $result['AllExams']= implode($result['AllExams'],',');
                // }
                if($_model->update_agency($_result)){
                    $this->success('更新合作机构信息成功',U('Home/Agency/list_agency'));
                }else{
                    $this->error('更新合作机构信息失败或未更新!');
                }
            }
        }else{
            $_agencyId = I('JGID','');
            $_data = $_model->get_agencyList('*','jgid='.$_agencyId,FALSE);
            /*地区相关信息*/
            $_citys = A('Citys');
            $_citylist = $_citys->optSelect(2,array('cityid','shortname'),$_data['province']);
            $_countylist = $_citys->optSelect(3,array('cityid','shortname'),$_data['city']);
            $_area = array(
                'province' => $this->province,
                'citys'    => $_citylist,
                'county'   => $_countylist
            );
            $this->assign('area',$_area);//地区
            $this->assign('markets',$_model->loadAdminUsers(array(1,4)));//市场负责人
            $this->assign('services',$_model->loadAdminUsers(array(3,4)));//客服负责人
            $this->assign('data',$_data);
            $this->display();
        }
    }

    /**
     * 设置机构考试。
     * @return void
     */
    public function agency_exam(){
        if(APP_DEBUG) trace("调用agency_exam...");
        //获取机构ID
        $_agencyId = I('JGID','');
        //初始化数据模型
        $_model = D('Agency');
        if(IS_POST){//提交数据
            $_result['JGID'] = $_agencyId;
            $_examIds = I('examId','');
            if(isset($_examIds) && !empty($_examIds) && is_array($_examIds)){
                $_result['AllExams'] = implode($_examIds,',');
            }else{
                $_result['AllExams'] = '';
            }
            //更新数据
            if($_model->update_agency($_result)){
                $this->success('设置机构考试成功',U('Home/Agency/list_agency'));
            }else{
                $this->error('设置机构考试失败或未更新!',U('Home/Agency/list_agency'));
            }
        }else{//显示页面
            //加载机构数据
            $_data = $_model->loadAgency($_agencyId);
            if(!$_data) $this->error('机构已不存在!');
            //设置机构ID
            $this->assign('agency_id', $_agencyId);
            //设置机构名称
            $this->assign('agency_name', $_data['abbr_cn']);
            //设置考试数据
            $this->assign('all_exams', $_model->loadAllExams());
            //设置机构考试
            $this->assign('exam_ids', $_data['allexams']);
            //显示
            $this->display();
        }
    }
    
    /**
     * 删除合作机构
     */
    public function del_agency(){
        $model = D('Agency');
        if($model->delete_agency(I('JGID',''))){
            $this->success('成功删除一个合作机构',U('Home/Agency/list_agency'));
        }else{
            $this->error('删除合作机构失败');
        }
    }
    
    //添加机构用户
    public function add_user(){
        $model = D('Agency');
        if(IS_POST){
            $JGID = I('JGID');
            if(empty($JGID)){$this->error('请选择合作机构!');}
            $data = array();
            $data['UserName'] = I('UserName');
            $data['PassWords'] = md5(I('PassWords'));
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
                $this->success('新增机构用户成功!',U('Home/Agency/list_agency'));
            }else{
                $this->error('新增机构用户失败');
            }
        }else{
            if(empty(I('get.aid'))){
                $agency = $model->get_agencyList(array('`abbr_cn`','`jgid`'),'statetf=1');
                $this->assign('agency',$agency);
            }
            $db_group = M('jigou_group');
            $this->assign('group',$db_group->field('id,title')->select());
            $this->display();
        }
    }
    
    //修改机构管理员
    public function edit_user($UID){
        $model = D('Agency');
        if(IS_POST){
            $rules = array(
                array('UserName','require','用户名不能为空'),
                array('RePassWords','PassWords','确认密码不正确',2,'confirm'),
                array('JGID','require','请选择合作机构'),
                array('RealName','require','真实姓名不能为空')
            );
            $check = M('jigou_admin');
            $check->setProperty('_validate', $rules);
            if(!$result=$check->create()){
                $this->error($model->getError());
            }else{
                $update_info = array_filter($result);
                if(!empty($update_info['PassWords'])){
                    $update_info['PassWords'] = md5(I('PassWords'));
                }
                if($model->update_user($update_info)){
                    $this->success('更新机构管理员信息成功',U('Home/Agency/list_users'));
                }else{
                    $this->error('更新管理员信息失败');
                }
            }
        }else{
            $data = $model->query_user('uid='.$UID,FALSE);
            $agency = $model->get_agencyList(array('`abbr_cn`','`jgid`'),'statetf=1');
            $this->assign('data',$data);
            $this->assign('agency',$agency);
            $db_group = M('jigou_group');
            $this->assign('group',$db_group->field('id,title')->select());
            $this->display();
        }
    }
    
    /*机构用户列表*/
    public function list_users(){
        $model = D('Agency');
        $data = $model->query_user();
        $this->assign('users',$data);
        $this->display();
    }
    
    //删除结构管理员
    public function del_users($uid){
        $model = D('Agency');
        if($model->delete_user($uid)){
            $this->success('成功删除一个机构管理员');
        }
    }
    
    
    
    /**
     * 公用接口
     * 获得机构列表
     * @param string $condition 查询条件
     */
    public function getAgencyList($condition='statetf=1',$field=array('`abbr_cn`','`jgid`')){
        $model = D('Agency');
        return $model->get_agencyList($field,$condition);
    }

    public function _initialize(){
        parent::_initialize();
        $opt = A('Citys');
        if(!F('province_temp')){
            $province = $opt->optSelect();
            F('province_temp',$province);
        }
        $this->province = F('province_temp');
    }
}