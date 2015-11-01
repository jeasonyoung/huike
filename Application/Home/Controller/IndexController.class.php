<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    
    //顶部菜单
    public function top(){
        
        $this->display('Public:top');
    }
    
    /**
     * 显示相应左边菜单
     * @param int $mid 系统模块id(hk_module)
     */
    public function show_menu($mid){
        
    }


    //左菜单
    public function left(){
        $mid = I('mid');
        if(isset($mid) && is_numeric($mid)){
            $db = M('AdminModule');
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
        $url_model = C('URL_MODEL');
        if($url_model!==0){
            $menu = array("replace(name,'".$module_name."','')" => 'link','title');
        }
        return $db->where('mid='.$mid.' and `status`=1 and `show`=1')->order('sortid asc')->field($menu)->select();
    }
}