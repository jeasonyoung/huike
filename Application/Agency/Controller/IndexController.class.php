<?php
namespace Agency\Controller;
use Agency\Controller\AdminController;
class IndexController extends AdminController {
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
            switch ($mid){
                case 1:
                    $this->display('Public:left_1');
                    break; 
                case 2:  
                    $this->display('Public:left_2');
                    break; 
                case 3:
                    $this->display('Public:left_3');
                    break;
                case 4:  
                    $this->display('Public:left_4');
                    break;
                case 5:
                    $this->display('Public:left_5');
                  break; 
                default :
                    $this->display('Public:left_1');
                  break; 
            }               
        }
        
    }
    
    public function footer(){
        $this->display('Public:footer');
    }
    
    public function main(){
        $this->display('Public:main');
    }
}