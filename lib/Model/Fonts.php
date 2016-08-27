<?php


namespace xepan\commerce;

class Model_Fonts extends \Model {
	public $dir='templates/fonts';
    public $namespace='xepan\commerce';
    public $path="";
    
    function init(){
        parent::init();
        $this->addField('name');

        /**
         * This model automatically sets its source by traversing 
         * and searching for suitable files
         */
        $path = $this->path = $this->api->pathfinder->base_location->base_path.'/./vendor/'.str_replace("\\","/",$this->namespace)."/".$this->dir;
        $p = scandir($path); 
        unset($p[0]);
        unset($p[1]);

        asort($p);
        $i=2;
        
        foreach ($p as $file) {
            // $temp = explode(".", explode("-", $file)[1]);
            $temp = explode(".",$file);
            if(strpos($file, ".ttf")===false) unset($p[$i]);
            $i++;
        }

        asort($p);
        $this->setSource('Array',$p);

        $this->addHook('beforeDelete',$this);

        return $this;
    }

    function beforeDelete($m){
        // throw new \Exception($this->id, 1);
        if(file_exists($this->path.'/'.$this['name'])){
            unlink($this->path.'/'.$this['name']);
        }
    }
}