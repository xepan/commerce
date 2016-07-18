<?php
namespace xepan\commerce;
class page_font extends \Page{
	function init(){
		parent::init();

	      $com_btn=$this->add('xepan\commerce\View_Designer_FontUpload');
    		$this->app->addLocation([
                                'ttf'=>['../vendor/xepan/commerce/templates/fonts']
                                ]
                                )->setParent($this->api->pathfinder->base_location);

        $p=$this->app->pathfinder->searchDir('ttf','.');

        sort($p);
        $font_array=[];
        foreach ($p as  $junk) {
           $font_name = explode(".", $junk);
           // $font_name = $font_name[0];
           if(!in_array($font_name, $font_array)){
               $font_array[]=$font_name;
           }
        }
           	
        if(count($font_array)){
           	$font_array=array_combine(range(1, count($font_array)), $font_array);
        }

        $m= $this->add('Model');
        $m->setSource('Array',$font_array);
        
        $grid = $this->add('Grid');
        $grid->setModel($m);
        if($font_name= $_GET['delete']){
          	$delete_font = $font_array[$_GET['delete']][0];
           	$delete_font = trim($delete_font).".ttf";
    		    $font_path = getcwd().DIRECTORY_SEPARATOR.'../vendor'.DIRECTORY_SEPARATOR.'xepan'.DIRECTORY_SEPARATOR.'commerce'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.$delete_font;
            if (file_exists($font_path)){
    		      unlink($font_path);				
       			  $grid->js(true,$grid->js()->univ()->errorMessage("Deleted"))->univ()->reload()->execute();
            }
        }
           		
    	  $grid->addColumn("Font");
    	  $grid->addMethod('format_Font',function($g,$f)use($font_array){
    	   $g->current_row_html[$f] = $font_array[$g->model->id][0];
    	  });       
        $grid->addFormatter('Font','Font');
    	  $grid->addColumn('Button','Delete');
        // $grid->addPaginator(100);
	}
}