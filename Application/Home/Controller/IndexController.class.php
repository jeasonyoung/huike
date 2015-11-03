<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    private $rules;
    public function index(){
        $this->display();
    }
    
    //顶部菜单
    public function top(){
        $this->display('Public:top');
    }

    //左菜单
    public function left(){
        $mid = I('mid');
        if(isset($mid) && is_numeric($mid)){
            $db = M('admin_module');
            //用户所拥有的module
            //$userModule = $db->table('hk_admin_rule')->distinct(true)->field('mid')->where('id in ('.$this->rules.')')->find();
            //$data = $db->where('`pid`='.$mid.' and `id` in ('.$userModule['mid'].') and `show`=1')->field('title,id')->order('sortid asc')->select();
            $data = $db->where('`pid`='.$mid.' and `show`=1')->field('title,id')->order('sortid asc')->select();
            foreach ($data as $k => &$v){
                if(is_array($v)){
                    $v['rules'] = $this->getMenu($v['id']);
                }
            }
            $this->assign('module',$data);
        }
        $this->display('Public:left');
    }
    
    public function footer(){
        $this->display('Public:footer');
    }
    
    public function main(){
        $this->display('Public:main');
    }

    //取得用户权限子菜单
    private function getMenu($mid,$menu=array("name" => 'link','title')){
        $db = M('admin_rule');
        $module_name = strtolower(MODULE_NAME);
        // $url_model = C('URL_MODEL');
        // if($url_model!==0){
        //     $menu = array("replace(name,'".$module_name."','')" => 'link','title');
        // }
        //return $db->where('mid='.$mid.' and `status`=1 and `show`=1 and id in ('.$this->rules.')')->order('sortid asc')->field($menu)->select();
        return $db->where('mid='.$mid.' and `status`=1 and `show`=1')->order('sortid asc')->field($menu)->select();
    }
    /*
    public function _initialize(){
        $auth = M('admin_group');
        if(!session('?groupid')){
            session('[destroy]');
            $this->error('非法登陆!',U('Login/index'));
        }else{
            $this->rules = $auth->where('id='.session('groupid'))->getField('rules'); //所拥有菜单权限
        }
    }
     * */
}