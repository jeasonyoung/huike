<?php
namespace Home\Model;
use Think\Model;

class UsersModel extends Model{
    protected $tableName = 'security_users';
    
    /**
     * Regist 用户注册
     * @para array $data 传递的用户注册信息
     * @return 注册状态
     */
    public function Regist($data){
        if(!$data){return null;}
        $list=  $this->where("account='".$data['account']."'")->find();
        if(!$list){
            $uuid = uuid();
            /*插入唯一标识uuid*/
            $data['id'] = $uuid;
            return $this->data($data)->add();
        }else{
            return 'username exist';
        }
    }
    
    /**
     * Userdata 根据用户名取得用户信息
     * @param str $username 用户名
     * @return array 用户信息
     */
    public function UserData($username){
        return $this->where("account='".$username."'")->find();
    }
    
    /**
     * UpdateData 更新用户信息
     * @param $fields 需要更新的字段(数组或者字符串)
     * @param $result 新的字段值(数组或者字符串)
     * @param str $condition 查询条件
     * @return int 成功标识
     */
    public function UpdateData($fields,$result,$condition){
        if(is_array($result)){
            return $this->where($condition)->field($fields)->save($result);
        }else{
            return $this->where($condition)->setField($fields,$result);
        }
    }
}
