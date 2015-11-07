<?php
/**
 * 机构学员控制器。
 */
namespace Home\Controller;
use Home\Controller\BaseController;

class MemberController extends BaseController{
    // private static $model;
    // private $agencyList;

    /**
     * 选择机构。
     * @return void
     */
    public function select_agency(){
        if(APP_DEBUG) trace("调用select_agency...");
        //初始化数据模型
        $_model = D('Member');
        //设置机构数据
        $this->assign('agencies',$_model->loadAllAgencies());
        //显示
        $this->display('select_agency');
    }

    /**
     * 添加学员。
     * @return void
     */
    public function add_member(){
        if(APP_DEBUG) trace('add_member...');
        if(IS_POST){//提交数据
            //数据验证规则
            $_rules = array(
                    array('UserName','require','用户名不能为空'),
                    array('PassWords','require','密码不能为空'),
                    array('RePassWords','PassWords','确认密码不正确',0,'confirm'),
                    array('JGID','require','请选择机构'),
                    array('Email','email','邮箱格式不正确',2),
                );
            //初始化数据模型
            $_model = D('Member');
            //动态验证
            if(!$_result=$_model->validate($_rules)->create()){
                $this->error($_model->getError());
            }else{
                //验证机构用户名的唯一性
                if($_model->uniqueMemberUser($_result['JGID'],$_result['UserName'])){
                    $this->error("用户名已存在");
                }else{
                    $_result['Psw'] = $result['PassWords'];
                    $_result['PassWords'] = md5($result['PassWords']);
                    $_result['RegTime'] = date('Y-m-d H:i:s',time());
                    //
                    if($_model->add($_result)){
                        $this->success('学员添加成功',U('Home/Member/member_list'));
                    }else{
                        $this->error('学员添加失败');
                    }
                }
            }
        }else{//显示
            $_agencyId = I('agencyId');
            if(!isset($_agencyId) || empty($_agencyId)){
                //选择机构
                $this->select_agency();
            }else{
                //初始化数据模型
                $_model = D('Member');
                //设置机构集合
                $this->assign('agencies',$_model->loadAllAgencies());
                //设置当前机构ID
                $this->assign('agency_id', $_agencyId);
                //显示
                $this->display();
            }
        }
    }

    /**
     * 学员列表。
     * @return void
     */
    public function member_list(){
        if(APP_DEBUG) trace('调用member_list...');
        //初始化数据模型
        $_model = D('Member');
        //查询数据
        if(IS_POST){
            $_where = "";
            $_agencyId = I('agencyId','');
            if(isset($_agencyId) && !empty($_agencyId)){
                $_where = " (hk_user.jgid = '".$_agencyId."') ";

                $this->assign('agency_id', $_agencyId);
            }
            $_userName = I('userName','');
            if(isset($_userName) && !empty($_userName)){
                if(isset($_where) && !empty($_where)){
                    $_where = $_where." and ";
                }
                $_where = $_where." (hk_user.username like '%".$_userName."%') ";

                $this->assign('user_name', $_userName);
            }
            $_realName = I('realName','');
            if(isset($_realName) && !empty($_realName)){
                if(isset($_where) && !empty($_where)){
                    $_where = $_where." and ";
                }
                $_where = $_where." (hk_user.realname like '%".$_realName."%') ";
                $this->assign('real_name', $_realName);
            }
            $_mobile = I('mobile','');
            if(isset($_mobile) && !empty($_mobile)){
                if(isset($_where) && !empty($_where)){
                    $_where = $_where." and ";
                }
                $_where = $_where." (mobile like '%".$_mobile."%') ";
                $this->assign('mobile', $_mobile);
            }
            $_user = $_model->show_member($_where);
        }else{
            $_user = $_model->show_member();
        }
        $this->assign('agencies',$_model->loadAllAgencies());
        $this->assign('data',$_user);
        $this->display('list');
    }

    /**
     * 锁定学员用户。
     * @return void.
     */
    public function lock_member(){
        if(APP_DEBUG) trace('锁定学员用户...');
        $_userId = I('UserID','');
        if(isset($_userId) && !empty($_userId)){
            //初始化数据模型
            $_model = D('Member');
            //加载数据
            $_data = $_model->loadMember($_userId);
            //更新数据
            $_update = array(
                'UserID' => $_userId,
                'Lock'   => !$_data['lock'],
            );
            
            if($_model->save($_update)){
                $this->success('锁定学员用户成功', U('Home/Member/member_list'));
            }else{
                $this->error('锁定学员用户失败或已锁定', U('Home/Member/member_list'));
            }
        }else{
            $this->member_list();
        }
    }
    
    
    /**
     * 修改学员。
     * @return void
     */
    public function edit_member(){
        if(APP_DEBUG) trace('调用edit_member...');
        //获取学员用户ID
        $_userId = I('UserID','');
        //初始化数据模型
        $_model = D('Member');
        //更新数据
        if(IS_POST){
            //验证规则
            $_rules = array(
                array('RePassWords','PassWords','确认密码不正确',0,'confirm'),
                array('JGID','require','请选择机构'),
                array('Email','email','邮箱格式不正确',2),
                array('Mobile','tel_validate','联系方式不正确',2,'function')
            );
            //动态验证
            if(!$_result=$_model->validate($_rules)->create()){
                $this->error($_model->getError());
            }else{
                $_data = array_filter($_result);
                $_data['Lock'] = $_result['Lock'];
                if(isset($_data['PassWords']) && !empty($_data['PassWords'])){
                    $_data['Psw'] = I('PassWords');
                    $_data['PassWords'] = md5($_data('PassWords'));
                }
                //更新
                if($_model->save($_data)){
                    $this->success('学员信息更新成功!',U('Home/Member/member_list'));
                }else{
                    $this->error('学员信息更新失败或未更新');
                }
            }
        }else{
            $_data = $_model->loadMember($_userId);
            $this->assign('agencies', $_model->loadAllAgencies($_data['jgid']));
            $this->assign('data',$_data);
            $this->display();
        }
    }
    
    /**
     * 删除学员数据。
     * @return void
     */
    public function del_member(){
        if(APP_DEBUG) trace('调用del_member...');
        $_userId = I('UserID','');
        if(isset($_userId) && !empty($_userId)){
            $_model = D('Member');
            if(is_array($_model)){
                $_model = $_model->where(array('UserID' => array('in',$_userId)));
            }else{
                $_model = $_model->where("`UserID` = '%s'", array($_userId));
            }
            if($_model->delete()){
                $this->success('删除学员成功',U('Home/Member/member_list'));
            }else{
                $this->error('删除学员失败');
            }
        }else{
            $this->error('删除学员失败');
        }
    }
    
    // /**
    //  * 学员简单注册接口
    //  * @param array $data 学员信息 包含 用户名,密码,机构ID
    //  */
    // public function simple_regist($data){
    //     //同一机构用户名已存在
    //     if(self::$model->query_memeber("username='".$data['UserName']."' and jgid=".$data['JGID'],FALSE)){
    //         return FALSE;
    //     }else{
    //         return self::$model->insert_member($data);
    //     }
    // }
    
    // public function simple_del_user($uid){
    //     return self::$model->delete_member($uid);
    // }

    // public function _initialize(){
    //     self::$model=D('Member');
    //     $agency = A('Agency');
    //     $this->agencyList = $agency->getAgencyList();
    // }
}