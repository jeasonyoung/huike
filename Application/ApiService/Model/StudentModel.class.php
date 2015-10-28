<?php
/**
 * 机构学员数据模型
 */
namespace ApiService\Model;
use Think\Model;

class StudentModel extends Model{
    //定义表名
    protected $tableName = 'user';
    /**
     * 验证学生用户
     * @param  string $agencyId 所属机构ID
     * @param  string $username 用户名
     * @param  string $pwd      密码md5(md5(agencyId+username)+md5(password))
     * @return array            返回数据类型
     */
    public function verifyStudentUser($agencyId=null,$username=null,$pwd=null){
        if(APP_DEBUG) trace("验证学生用户:[agency=$agencyId,username=$username,pwd=$pwd]");
        if(!isset($agencyId) || empty($agencyId)) return build_callback_data(false,null,'所属机构ID为空!');
        if(!isset($username) || empty($username)) return build_callback_data(false,null,'用户账号为空!');

        $data = $this->field('userid,passwords,lock')->where("`jgid`='%s' and username='%s'", array($agencyId,$username))->find();
        if(!$data){
            if(APP_DEBUG) trace('学生用户不存在!');
            return build_callback_data(false,null,'账号不存在!');
        }
        if($data['lock']){
            if(APP_DEBUG) trace('学生用户账号已被锁定(lock:'.$data['lock'].')!');
            return build_callback_data(false,null,'账号已被锁定!');
        }
        $_encypt_pwd = md5(md5($agencyId.$username).$data['passwords']);
        if($_encypt_pwd != $pwd){
            if(APP_DEBUG) trace("密码错误($pwd => $_encypt_pwd)!");
            return build_callback_data(false,null,'密码错误!');
        }
        return build_callback_data(true,$data['userid']);
    }

}