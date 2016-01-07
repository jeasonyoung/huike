<?php
/**
 * 机构学员数据模型
 */
namespace Api\Model;
use Think\Model;
use Org\Util\String;

class UserModel extends Model{
    //定义表名
    protected $tableName = 'user';

    /**
     * 学员注册。
     * @param  string $agencyId 机构ID
     * @param  string $username 学员账号
     * @param  string $pwd      学员密码
     * @param  string $realname 真实姓名
     * @param  string $phone    手机号码
     * @param  string $email    邮箱地址
     * @param  string $terminal 终端类型(2:苹果,3-安卓)
     * @return json             反馈数据
     */
    public function register($agencyId,$username,$pwd,$realname,$phone,$email,$terminal){
        if(APP_DEBUG)trace("学员用户注册[agencyId=>$agencyId,username=>$username,terminal=>$terminal]...");
        //检查所属机构ID
        if(empty($agencyId)){
            return build_callback_error(-300,'机构ID为空!');
        }
        //检查用户名
        if(empty($username)){
            return build_callback_error(-301,'用户名为空!');
        }
        //检查密码
        if(empty($pwd)){
            return build_callback_error(-302,'密码为空!');
        }
        //检查机构用户是否已被注册
        $_total = $this->where("`jgid` = '%s' and username = '%s'",array($agencyId,$username))
                       ->count();
        if($_total > 0)return build_callback_error(-310,'注册失败,用户名已存在!');
        $data = array('JGID'      => $agencyId,
                      'UserName'  => $username,
                      'Psw'       => $pwd,
                      'PassWords' => md5($pwd),
                      'RealName'  => $realname,
                      'Mobile'    => $phone,
                      'Email'     => $email,
                      'RegTime'   => date('Y-m-d H:i:s'),
                      'Source'    => ($terminal == 2 ? 'iOS':'andorid'),
                      'UserType'  => ($terminal == 2 ? 5:6));
        //添加学员数据
        if($this->add($data)){
            //注册成功
            return build_callback_success();
        }else{
            //注册失败
            return build_callback_error(-311,'注册失败,服务器发生错误，请联系管理员');
        }
    }

    /**
     * 学员用户登录。
     * @param  string $agencyId 机构ID
     * @param  string $username 用户名
     * @param  string $pwd      加密密码:md5(学员用户账号+学员用户输入明文密码)
     * @param  int    $terminal 终端(2-苹果,3-安卓,4-其他)
     * @return json             反馈数据
     */
    public function login($agencyId,$username,$pwd,$terminal=2){
        if(APP_DEBUG)trace("学员用户登录[agencyId=>$agencyId][username=>$username][pwd=>$pwd]...");
        //检查所属机构ID
        if(!isset($agencyId) || empty($agencyId)){
            return build_callback_error(-300,'机构ID为空!');
        }
        //检查用户名
        if(!isset($username) || empty($username)){
            return build_callback_error(-301,'用户名为空!');
        }
        //检查密码
        if(!isset($pwd) || empty($pwd)){
            return build_callback_error(-302,'密码为空!');
        }
        //查询数据
        $_data = $this->field('userid,realname,passwords,lock')
                      ->where("`jgid` = '%s' and username = '%s'",array($agencyId,$username))
                      ->find();
        if(!$_data){
            if(APP_DEBUG) trace("用户名[$username]不存在!");
            return build_callback_error(-303,'用户名不存在!');
        }else if(isset($_data['lock']) && !empty($_data['lock'])){
            if(APP_DEBUG) trace("用户账号[$username]已被锁定!");
            return build_callback_error(-304,'用户已被锁定!');
        }else if(isset($_data['passwords'])){//0.校验密码
            //1.加密计算
            $_encypt_pwd = md5($username.$_data['passwords']);
            if($_encypt_pwd != $pwd){
                if(APP_DEBUG) trace("密码错误[$_encypt_pwd != $pwd]..");
                return build_callback_error(-305,'密码错误!');
            }else{
                $_userId = $_data['userid'];
                //验证成功，生成随机用户ID(用于限制一个账号多处登录)
                $_rand_user_id = String::uuid();
                //更新成功
                if($this->save(array(
                        'UserID'        => $_userId,
                        'app_random_id' => $_rand_user_id,
                        'LoginTime'     => date('Y-m-d H:i:s', time()),
                    ))){
                    //更新登录次数
                    $this->where("`userid`='%s'", array($_userId))
                         ->setInc('loginnum');
                    //写入登录日志
                    M('UserLog')->add(array(
                        'UID'           => $_userId,
                        'LoginType'     => intval($terminal),
                        'LoginIP'       => get_client_ip(),
                        'create_time'   => date('Y-m-d H:i:s', time()),
                        //'Browser'       => get_client_browser(),
                    ));
                    //返回数据
                    return build_callback_success(array(
                        'agencyId'     => $agencyId,
                        'randUserId'  => $_rand_user_id,
                        'realName'     => $_data['realname'],

                    ));

                }else{
                    if(APP_DEBUG) trace("随机用户ID[$_rand_user_id=>$_userId]写入数据失败:$_update");
                    return build_callback_error(-306,'更新随机用户ID失败,请联系管理员!');
                }
            }
        }else{
            return build_callback_error(-307,'未知错误，请联系管理员!');
        }
    }


    /**
     * 根据随机用户ID获取真实用户ID。
     * @param  string $randUserId 随机用户ID
     * @return mixed              返回数据
     */
    public function loadRealUserIdByRandUserId($randUserId){
        if(APP_DEBUG) trace("根据随机用户ID[$randUserId]获取真实用户ID...");
        if(!isset($randUserId) || empty($randUserId)) return null;
        $_model = $this->field('userid')
                       ->where("`app_random_id` = '%s'", array($randUserId))
                       ->find();
        if($_model){
            if(APP_DEBUG) trace("用户ID:".$_model['userid']);
            return $_model['userid'];
        }
        return null;
    }


}