<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\commerce;

class page_tests_0030itemImport extends \xepan\base\Page_Tester {
	
	public $title='Item Importer';
	
	public $proper_responses=[

        'test_checkEmpty_CustomField'=>['cf'=>0],
        'test_ImportCustomField'=>['cf'=>-1],
       
    	'test_checkEmptyRows'=>['items'=>0],
        'test_ImportItems'=>['count'=>-1]

        // 'test_checkEmpty_Item_CustomField_Association'=>['asso'=>0],
        // 'test_Import_Item_CustomField_Association'=>['asso'=>-1],
        
    ];


    function init(){
        // $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
    }


    function test_checkEmpty_CustomField(){
        return ['cf'=>$this->app->db->dsql()->table('customfield_generic')->del('fields')->field('count(*)')->getOne()];
    }

    function prepare_ImportCustomField(){
        //setting count of old cf
        $this->proper_responses['test_ImportCustomField']['cf'] = $this->pdb->dsql()->table('xshop_custom_fields')->del('fields')->field('count(*)')->getOne();

        $file_data = [];
        $old_custom_fields = $this->pdb->dsql()->table('xshop_custom_fields')->get();
        foreach ($old_custom_fields as $old_custom_field) {
            $new_custom_field = $this->add('xepan\commerce\Model_Item_CustomField');
            
            $new_custom_field
            ->addCondition('name',$old_custom_field['name'])
            ->tryLoadAny();

            if(!$new_custom_field->loaded()){
                $new_custom_field['display_type'] = $old_custom_field['type'];
                $new_custom_field['is_filterable'] = false;
                // $new_custom_field['type'] = "CustomField";
                $new_custom_field->save();
            }

            $file_data[$old_custom_field['id']] = ['new_id'=>$new_custom_field->id];

            $new_custom_field->unload();
        }

        file_put_contents(__DIR__.'/customfield_mapping.json', json_encode($file_data));
    }

    function test_ImportCustomField(){
        $cf_count = $this->app->db->dsql()->table('customfield_generic')->del('fields')->field('count(*)')->getOne();
        return ['cf'=>$cf_count];
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['items'] = $this->app->db->dsql()->table('item')->del('fields')->field('count(*)')->getOne();

    	return $result;
    }

    function prepare_ImportItems(){

        // $custom_field_value_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('custom_field_value');
        //set old item count to check
        $this->proper_responses['test_ImportItems']['count'] = $this->pdb->dsql()->table('xshop_items')->where('application_id',1)->del('fields')->field('count(*)')->getOne();
        
        $new_item = $this->add('xepan\commerce\Model_Item');

        $old_items = $this->pdb->dsql()->table('xshop_items')
                    ->where('application_id',1)
                    ->get()
                    ;
        $file_data = [];
        $file_qtyset_data = [];
        foreach ($old_items as $old_item){

                $new_item
                ->set('designer_id',$old_item['designer_id'])
                ->set('name',$old_item['name'])
                ->set('sku',$old_item['sku'])
                ->set('display_sequence',$old_item['rank_weight'])
                ->set('description',$old_item['description'])
                ->set('original_price',$old_item['original_price'])
                ->set('sale_price',$old_item['sale_price'])
                ->set('expiry_date',$old_item['expiry_date'])
                ->set('minimum_order_qty',$old_item['minimum_order_qty'])
                ->set('maximum_order_qty',$old_item['maximum_order_qty'])
                ->set('qty_unit',$old_item['qty_unit'])
                ->set('qty_from_set_only',$old_item['qty_from_set_only'])
                ->set('is_party_publish',$old_item['is_party_publish'])
                ->set('is_saleable',$old_item['is_saleable'])
                ->set('is_allowuploadable',$old_item['allow_uploadedable'])
                ->set('is_purchasable',$old_item['is_purchasable'])
                ->set('maintain_inventory',$old_item['mantain_inventory'])
                ->set('allow_negative_stock',$old_item['allow_negative_stock'])
                ->set('is_dispatchable',$old_item['is_outsourced'])
                ->set('negative_qty_allowed',$old_item['negative_qty_allowed'])
                ->set('is_visible_sold',$old_item['is_visible_sold'])
                ->set('is_servicable',$old_item['is_servicable'])
                ->set('is_productionable',$old_item['is_productionable'])
                ->set('website_display',$old_item['website_display'])
                ->set('is_downloadable',$old_item['is_downloadable'])
                ->set('is_rentable',$old_item['is_rentable'])
                ->set('is_designable',$old_item['is_designable'])
                ->set('is_template',$old_item['is_template'])
                ->set('is_attachment_allow',$old_item['is_attachment_allow'])
                ->set('warranty_days',$old_item['warrenty_days'])
                ->set('show_detail',$old_item['show_detail'])
                ->set('show_price',$old_item['show_price'])
                ->set('is_new',$old_item['new'])
                ->set('is_feature',$old_item['feature'])
                ->set('is_mostviewed',$old_item['mostviewed'])
                ->set('is_enquiry_allow',$old_item['is_enquiry_allow'])
                ->set('enquiry_send_to_admin',$old_item['enquiry_send_to_admin'])
                ->set('item_enquiry_auto_reply',$old_item['Item_enquiry_auto_reply'])
                ->set('is_comment_allow',$old_item['allow_comments'])
                ->set('comment_api',$old_item['comment_api'])
                ->set('add_custom_button',$old_item['add_custom_button'])
                ->set('custom_button_label',$old_item['custom_button_label'])
                ->set('custom_button_url',$old_item['custom_button_url'])

                ->set('watermark_text',$old_item['watermark_text'])
                ->set('watermark_position',$old_item['watermark_position'])
                ->set('watermark_opacity',$old_item['watermark_opacity'])
                ->set('meta_title',$old_item['meta_title'])
                ->set('meta_description',$old_item['meta_description'])
                ->set('tags',$old_item['tags'])
                ->set('designs',$old_item['designs'])
                ->set('terms_and_conditions',$old_item['terms_condition'])
                ->set('duplicate_from_item_id',$old_item['duplicate_from_item_id'])
                ->set('upload_file_label',$old_item['upload_file_lable'])
                ->set('item_specific_upload_hint',$old_item['item_specific_upload_hint'])
                ->set('to_customer_id',$old_item['to_customer_id'])
                ->set('status',$old_item['is_publish']?"Published":"UnPublished")
                ->set('search_string',$old_item['search_string'])
                ->save()
                ;

            $file_data[$old_item['id']] = ['new_id'=>$new_item->id];
            
            //Item Quantity Set
            $new_qty_set = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$new_item->id);

            $old_qty_sets = $this->pdb->dsql()->table('xshop_item_quantity_sets')
                            ->where('item_id',$old_item['id'])
                            ->get()
                            ;

            $new_qty_set_array = [];

            foreach ($old_qty_sets as $old_qty_set) {

                // $new_qty_set_array[] = ['item_id'=>$new_item->id,
                //                         'name'=>$old_qty_set['name'],
                //                         'qty'=>$old_qty_set['qty'],
                //                         'old_price'=>$old_qty_set['old_price'],
                //                         'price'=>$old_qty_set['price'],
                //                         'is_default'=>$old_qty_set['is_default'],
                //                         'shipping_charge'=>$old_qty_set['shipping_charge']
                //                         ];
                $new_qty_set
                ->set('name',$old_qty_set['name'])
                ->set('qty',$old_qty_set['qty'])
                ->set('old_price',$old_qty_set['old_price'])
                ->set('price',$old_qty_set['price'])
                ->set('is_default',$old_qty_set['is_default'])
                ->set('shipping_charge',$old_qty_set['shipping_charge'])
                ->save();

                $file_qtyset_data[$old_qty_set['id']] = ['new_id'=>$new_qty_set->id];
                $new_qty_set->unload();
            }

            //insert all qty set with
            $this->app->db->dsql()->table('quantity_set')->insertAll($new_qty_set_array);

            $new_item->unload();


        }
        file_put_contents(__DIR__.'/item_mapping.json', json_encode($file_data));
        file_put_contents(__DIR__.'/item_qty_set_mapping.json', json_encode($file_qtyset_data));
    }

    function test_ImportItems(){
        $new_item_count = $this->app->db->dsql()->table('item')->del('fields')->field('count(*)')->getOne();
        return ['count'=>$new_item_count];
    }

    function preparenext_Import_Item_CustomField_Association (){
        //setting count of all association
        $this->proper_responses['test_Import_Item_CustomField_Association']['asso'] = $this->pdb->dsql()->table('xshop_item_customfields_assos')->del('fields')->field('count(*)')->getOne();

        $file_data = [];
        $old_asso = $this->pdb->dsql()->table('xshop_item_customfields_assos')->get();
        foreach ($old_custom_fields as $old_custom_field) {
            $new_custom_field = $this->add('xepan\commerce\Model_Item_CustomField');
            
            $new_custom_field
            ->addCondition('name',$old_custom_field['name'])
            ->tryLoadAny();

            if(!$new_custom_field->loaded()){
                $new_custom_field['display_type'] = $old_custom_field['type'];
                $new_custom_field['is_filterable'] = false;
                // $new_custom_field['type'] = "CustomField";
                $new_custom_field->save();
            }

            $file_data[$old_custom_field['id']] = ['new_id'=>$new_custom_field->id];

            $new_custom_field->unload();
        }

        file_put_contents(__DIR__.'/customfield_mapping.json', json_encode($file_data));
    }


    function testnext_checkEmpty_Item_CustomField_Association(){
        return ['asso'=>$this->app->db->dsql()->table('customfield_association')->del('fields')->field('count(*)')->getOne()];
    }

    // // Qty Set Condition
    //             $new_qty_set_condition = $this->add('xepan\commerce\Model_Item_Quantity_Condition')
    //                                         ->addCondition('quantity_set_id',$new_qty_set->id);

    //             $old_qty_set_conditions = $this->pdb()->dsql()
    //                                         ->table('xshop_item_quantity_set_conditions')
    //                                             ->where('quantityset_id',$old_qty_set['id'])
    //                                             ;
    //             foreach ($old_qty_set_conditions as $old_qty_set_condition) {
    //                 $new_qty_set_condition
    //                     ->set('customfield_value_id',$custom_field_value_mapping[$old_qty_set_condition['customfield_value_id']]['new_id'])
    //                     ->saveAndUnload()
    //                     ;
    //             }
    //Item_CustomField_Association
    //Item_Department_Association
    //CategoryItemAssociation
    //Item_Template_Design == ItemMemberDesign
    //Store_TransactionRow
    //QSP_Detail
    //Item_Image
    //Item_Taxation_Association
}
