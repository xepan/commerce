<?php

namespace xepan\commerce;
class page_itemtools extends \Page {

	public $title='Item List Grid';

    function init() {
        parent::init();

        $item = $this->add('xepan\commerce\Model_Item');
        $item->tryLoadAny();

        $view = $this->add('xepan\commerce\View_ItemType');
        $view->setModel($item);
    }
}

       