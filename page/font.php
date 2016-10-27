<?php
namespace xepan\commerce;
class page_font extends \xepan\commerce\page_configurationsidebar{
  public $title = 'Fonts';
	function init(){
		parent::init();

    $config_m = $this->add('xepan\base\Model_ConfigJsonModel',
    [
      'fields'=>[
            'font_family'=>'text',
            ],
        'config_key'=>'COMMERCE_DESIGNER_TOOL_FONT_FAMILY',
        'application'=>'commerce'
    ]);

    $config_m->add('xepan\hr\Controller_ACL');
    $config_m->tryLoadAny();

    $this->add('View')->set('Enter comma seperated font family with no space');
    $form=$this->add('Form');
    $form->setModel($config_m,['font_family']);
    $form->addSubmit('Save')->addClass('btn btn-primary');
    
    if($form->isSubmitted()){
      $form->save();
      $form->js(null,$form->js()->reload())->univ()->successMessage('Saved')->execute();
    }

    // Custom font upload
    $font_model = $this->add('xepan\commerce\Model_DesignerFont');
    $crud = $this->add('xepan\hr\CRUD');
    $crud->setModel($font_model);
	}
}


  // return;
  // $com_btn=$this->add('xepan\commerce\View_Designer_FontUpload');
  // $this->app->addLocation([
  //                         'ttf'=>['../vendor/xepan/commerce/templates/fonts']
  //                         ]
  //                         )->setParent($this->api->pathfinder->base_location);

  // $p=$this->app->pathfinder->searchDir('ttf','.');

  // sort($p);
  // $font_array=[];
  // foreach ($p as  $junk) {
  //    $font_name = explode(".", $junk);
  //    // $font_name = $font_name[0];
  //    if(!in_array($font_name, $font_array)){
  //        $font_array[]=$font_name;
  //    }
  // }
      
  // if(count($font_array)){
  //     $font_array=array_combine(range(1, count($font_array)), $font_array);
  // }

  // $m= $this->add('Model');
  // $m->setSource('Array',$font_array);
  
  // $grid = $this->add('Grid');
  // $grid->setModel($m);
  // if($font_name= $_GET['delete']){
  //     $delete_font = $font_array[$_GET['delete']][0];
  //     $delete_font = trim($delete_font).".ttf";
  //     $font_path = getcwd().DIRECTORY_SEPARATOR.'../vendor'.DIRECTORY_SEPARATOR.'xepan'.DIRECTORY_SEPARATOR.'commerce'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.$delete_font;
  //     if (file_exists($font_path)){
  //       unlink($font_path);       
  //       $grid->js(true,$grid->js()->univ()->errorMessage("Deleted"))->univ()->reload()->execute();
  //     }
  // }
        
  // $grid->addColumn("Font");
  // $grid->addMethod('format_Font',function($g,$f)use($font_array){
  //  $g->current_row_html[$f] = $font_array[$g->model->id][0];
  // });       
  // $grid->addFormatter('Font','Font');
  // $grid->addColumn('Button','Delete');
  // // $grid->addPaginator(100);