<?php
/***********************************************************
  ..

  Reference:
  http://agiletoolkit.org/doc/ref

==ATK4===================================================
   This file is part of Agile Toolkit 4
    http://agiletoolkit.org/

   (c) 2008-2013 Agile Toolkit Limited <info@agiletoolkit.org>
   Distributed under Affero General Public License v3 and
   commercial license.

   See LICENSE or LICENSE_COM for more information
=====================================================ATK4=*/
namespace xepan\commerce;
class Form_Field_RangeSlider extends \Form_Field_Line {
    public $min = 0;
    public $max = 10;
    public $step = 1;
    public $left = 'Min';
    public $right = 'Max';
    public $selected_min = 0;
    public $selected_max = 10;
    function setStep($n){
        $this->step = $n;
        return $this;
    }
    function setLabels($left,$right){
        $this->left = $left;
        $this->right = $right;
        return $this;
    }
    function getInput(){
        $s = $this->name.'_slider';
        $this->js(true)->_selector('#'.$s)->slider(array(
                    'min' => $this->min,
                    'max' => $this->max,
                    'step' => $this->step,
                    'range'=>true,
                    'values'=>array($this->selected_min,$this->selected_max),
                    'change' => $this->js()->_enclose()->val(
                        $this->js()->_selector('#'.$s)->slider('values')
                        )->change()
                    ));
        $this->setAttr('style','display: none');

        return '<div class="atk-cells"><div class="atk-cell atk-align-left">'.
            $this->left.'</div>'.
            '<div class="atk-cell atk-align-right">'.$this->right.'</div>'.
            '</div>'.
            ''.parent::getInput().'<div id="'.$s.'"></div>'.
            '';
    }
}
