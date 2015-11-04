<?php
namespace Home\Controller;
use Home\Controller\BaseController;

class IndexController extends BaseController {
    private $db;
    private $rules;
    private $module;
    private $group_title;
    private $top_module; //顶部菜单 即顶级模块


    public function index(){
        $default_menu = array_shift($this->top_module);
        $this->assign('default',$default_menu);
        $this->display();
    }
    
    //顶部菜单
    public function top(){
        $this->assign('module',$this->top_module);
        $this->assign('title',$this->group_title);
        $this->display('Public:top');
    }

    //左菜单
    public function left(){
        $mid = I('mid');
        if(isset($mid) && is_numeric($mid)){
            $data = $this->db->where('`pid`='.$mid.' and `id` in ('.$this->module.') and `show`=1')->field('title,id')->order('sortid asc')->select();
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
        $url_model = C('URL_MODEL');
        if($url_model!==0){
            $menu = array("replace(name,'".$module_name."','')" => 'link','title');
        }
        return $db->where('mid='.$mid.' and `status`=1 and `show`=1 and id in ('.$this->rules.')')->order('sortid asc')->field($menu)->select();
    }
    
    public function _initialize(){
        $auth = M('admin_group');
        $this->db = M('admin_module');
        if(!session('?groupid')){
            session('[destroy]');
            $this->error('非法登陆!',U('Login/index'));
        }else{
            $this->rules = $auth->where('id='.session('groupid'))->getField('rules'); //所拥有菜单权限
            $userModule = $this->db->table('hk_admin_rule')->distinct(true)->field('mid')->where('id in ('.$this->rules.')')->select();
            $this->module = implode(',',array_column($userModule,'mid'));   //用户所拥有的二级菜单
            $this->group_title = $auth->where('id='.session('groupid'))->getField('title');
            
            $top_module = $this->db->where('pid <> 0 and id in ('.$this->module.')')->distinct(true)->field('pid')->select();
            foreach (array_column($top_module,'pid') as $k => $v){
                $arr['mod'.$k] = $v;
            }
            
            $this->top_module = $arr;
        }
    }
}