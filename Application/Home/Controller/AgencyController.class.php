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
                $_result['create_time'] = date('Y-m-d', time);
                if($agencyId=$_model->insert_agency($result)){
                    //添加机构成功,为机构添加默认管理员
                    $this->success('新增机构成功!',U('Home/Agency/add_user',array('agencyId' => $agencyId)));
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
    
    /**
     * 添加机构用户。
     * @return void
     */
    public function add_user(){
        if(APP_DEBUG) trace('调用add_user...');
        //初始化机构管理数据模型
        $_model = D('AgencyAdmin');
        //提交数据处理
        if(IS_POST){
            if(!($_result = $_model->create())){
                $this->error($_model->getError());
            }else{
                $_data = array(
                    'UserName'  => $_result['UserName'],
                    'PassWords' => md5($_result['PassWords']),
                    'JGID'      => $_result['JGID'],
                    'Lock'      => $_result['Lock'],
                    'RegTime'   => date('Y-m-d H:i:s',time()),
                    'RealName'  => $_result['RealName'],
                    'GroupID'   => $_result['GroupID'],
                    'LoginNum'  => 0,
                );
                if($_uid = $_model->add($_data)){
                    if($_data['GroupID'] = 1){//更新机构默认管理员字段
                        M('Jigou')->save(array(
                            'JGID'   => $_result['JGID'],
                            'JG_UID' => $_uid,
                        ));
                    }
                    $this->success('新增机构管理员成功!',U('Home/Agency/list_users'));
                }else{
                    $this->error('新增机构管理员失败');
                }
            }
        }else{
            //加载页面
            $_agencyId = I('agencyId', null);
            //设置机构
            $this->assign('agencies', $_model->loadAllAgencies($_agencyId));
            //设置管理组
            $this->assign('groups', $_model->loadAgencyGroups(1));
            //显示页面
            $this->display();
        }
    }

    /**
     * 机构用户列表。
     * @return void
     */
    public function list_users(){
        $_model = D('Agency');
        if(IS_POST){//查询数据
            $_agencyId = I('agencyId',null);
            if(isset($_agencyId) && !empty($_agencyId)){
                $_where = "hk_jigou_admin.JGID='".$_agencyId."'";
                $this->assign('users',$_model->query_user($_where));
                $this->assign('agency_id', $_agencyId);
            }else{
                $this->assign('users',$_model->query_user());
            }
        }else{//加载数据
            $this->assign('users',$_model->query_user());
        }
        //设置机构
        $this->assign('agencies', D('AgencyAdmin')->loadAllAgencies());
        //显示页面
        $this->display();
    }
    
    /**
     * 修改机构管理员。
     * @return void。
     */
    public function edit_user(){
        if(APP_DEBUG) trace("调用edit_user...");
        //初始化机构管理数据模型
        $_model = D('AgencyAdmin');
        //更新数据
        if(IS_POST){
            $_rules = array(
                array('UserName','require','用户名不能为空'),
                array('RePassWords','PassWords','确认密码不正确',2,'confirm'),
                array('JGID','require','请选择所属机构'),
                array('RealName','require','真实姓名不能为空'),
                array('GroupID','require','管理组不能为空'),
            );
            //$_model->setProperty('_validate', $_rules);
            if(!$_result=$_model->validate($_rules)->create()){
                $this->error($_model->getError());
            }else{
                $_result = array_filter($_result);
                $_result['Lock'] = $_result['Lock'];
                if(!empty($_result['PassWords'])){
                    $_result['PassWords'] = md5(I('PassWords'));
                }
                if($_model->save($_result)){
                    $this->success('更新机构管理员信息成功',U('Home/Agency/list_users'));
                }else{
                    $this->error('更新机构管理员信息失败或未更新',U('Home/Agency/list_users'));
                }
            }
        }else{//加载数据
            $_data = $_model->loadAgencyAdmin(I('UID',null));
            //设置数据
            $this->assign('data', $_data);
            //设置机构
            $this->assign('agencies', $_model->loadAllAgencies($_data ? $_data['jgid'] : null));
            //设置管理组
            $this->assign('groups', $_model->loadAgencyGroups(1));
            //显示页面
            $this->display();

        }
    }

    /**
     * 删除结构管理员
     * @return void
     */
    public function del_users(){
        if(APP_DEBUG) trace('调用del_users...');
        $_uid = I('UID','');
        if(isset($_uid) && !empty($_uid)){
            $_model = D('AgencyAdmin');
            if(is_array($_uid)){
                $_model = $_model->where(array('UID' => array('in', $_uid)));
            }else{
                $_model = $_model->where("`UID` = '%s'", array($_uid));
            }
            if($_model->delete()){
                $this->success('成功删除一个机构管理员');
            }else{
                $this->error('删除失败或未删除');
            }
        }else{
            $this->error('未获取用户ID删除失败');
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