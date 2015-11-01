<?php
namespace Home\Controller;
use Think\Controller;

class MemberController extends Controller{
    private static $model;
    private $agencyList;

    //学员列表
    public function member_list(){
        $user = self::$model->show_member();
        $this->assign('agency',$this->agencyList);
        $this->assign('data',$user);
        $this->display('list');
    }
    
    //添加学员
    public function add_member(){
        if(IS_POST){
            $rules=array(
                array('UserName','require','用户名不能为空'),
                array('PassWords','require','密码不能为空'),
                array('RePassWords','PassWords','确认密码不正确',0,'confirm'),
                array('Email','email','邮箱格式不正确',2),
                array('JGID','require','请选择机构')
            );
           
            self::$model->setProperty('_validate',$rules);
            if(!$result=self::$model->create()){
                $this->error(self::$model->getError());
            }else{
                $result['Psw'] = $result['PassWords'];
                $result['PassWords'] = md5($result['PassWords']);
                $result['RegTime'] = date('Y-m-d H:i:s',time());
                if(self::$model->insert_member($result)){
                    $this->success('学员添加成功',U('member/member_list'));
                }else{
                    $this->error('学员添加失败');
                }
            }
        }else{
            $this->assign('agency',$this->agencyList);
            $this->display();
        }
    }
    
    /**
     * 修改学员
     * @param int $UserID 学员ID
     */
    public function edit_member($UserID){
        if(IS_POST){
            $rules = array(
                array('Email','email','邮箱格式不正确',2),
                array('JGID','require','请选择机构'),
                array('Mobile','tel_validate','联系方式不正确',2,'function')
            );
            self::$model->setProperty('_validate',$rules);
            if(!$result=self::$model->create()){
                $this->error(self::$model->getError());
            }else{
                $result=array_filter($result);

                if(isset($result['PassWords']) && !empty($result['PassWords'])){
                    $result['Psw'] = I('PassWords');
                    foreach($result as &$v){
                        if($key=="PassWords"){
                            $v=md5(I('PassWords'));
                        }
                    }
                }
                
                if(self::$model->update_memeber($result)){
                    $this->success('学员信息更新成功!',U('member/member_list'));
                }else{
                    $this->error('学员信息更新失败');
                }
            }
        }else{
            $data = self::$model->query_memeber(array('userid' => $UserID),FALSE);
            $this->assign('agency',$this->agencyList);
            $this->assign('data',$data);
            $this->display();
        }
    }
    
    /**
     * 删除学员
     * @param int $UserID 学员ID
     */
    public function del_member($UserID){
         if(self::$model->delete_member($UserID)){
             $this->success('删除学员成功',U('member/member_list'));
         }else{
             $this->error('删除学员失败');
         }
    }
    
    /**
     * 学员简单注册接口
     * @param array $data 学员信息 包含 用户名,密码,机构ID
     */
    public function simple_regist($data){
        //同一机构用户名已存在
        if(self::$model->query_memeber("username='".$data['UserName']."' and jgid=".$data['JGID'],FALSE)){
            return FALSE;
        }else{
            return self::$model->insert_member($data);
        }
    }
    
    public function simple_del_user($uid){
        return self::$model->delete_member($uid);
    }

    public function _initialize(){
        self::$model=D('Member');
        $agency = A('Agency');
        $this->agencyList = $agency->getAgencyList();
    }
}
