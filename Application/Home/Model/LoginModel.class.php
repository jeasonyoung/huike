<?php
namespace Home\Model;
use Think\Model;

//机构Model层
class LoginModel extends Model{
    protected $tableName = 'settings_agencies';
    /*
     * GetAgency 取得代理机构信息
     * @param $id string 机构ID
     * @return mixd 结构信息
     */
    public function agencyInfo($abbr_en){
        return $this->where("abbr_en='".$abbr_en."' and status=1")->find();
    }
    
    public function getAgencybyhost($host){
        return $this->where("host='$host'")->getField('abbr_en');
    }
    
    /*
     * LoginCheck 注册检查
     * @param $username 用户名
     * @param $password 用户密码
     * @param $angency 机构代码
     */
    public function LoginCheck($username,$password,$angency=''){
        $db = M('security_users');
        $newpwd = md5(md5($angency.$username).$password);
        $parameter = "agencyId=$angency&username=$username&pwd=$newpwd";
        $api = C('login_api').'?'.$parameter;
        $json = DigestClient(null, $api, C('digest_name'), C('digest_pwd'), 0);    //验证用户登陆
        $obj = json_decode($json);      //解析json
        if($obj->success){
            $data = $db->field('account,nickName,imgurl')->where("id='$obj->data'")->find();
            if($data){
                session('username',$data['account']);
                session('userid',$obj->data);
                session('nickname',$data['nickName']);
                session('userhead',$data['imgurl']);
                return true;
            }
        }
		else{
            exit("<script>alert('用户名和密码错误!');history.go(-1);</script>");
        }
    }
}