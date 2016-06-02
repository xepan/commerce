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

class page_tests_0010tillCustomers extends \xepan\base\Page_Tester {
	
	public $title='Department, POst, User, ContactInfo, Customer Importer';

	public $proper_responses=[
    	'test_checkEmptyRows'=>['department'=>1],
        'test_ImportDepartments'=>['Company','Designing','Offset Printing','Digital Press','Large Format','Screen Printing','Varnish','Lamination','UV','Foil','Cutting','Die Cut','Laser Cut','Binding','Pasting','Frame'],
        'test_importPosts'=>['CEO','Director','HOD','Designer','Operator','HOD','Helper','HOD','Manager'],
        'test_importEmployies'=>12,
        // 'test_defaultCurrency'=>'Default Currency', // Should be in accounts tests now
    	'test_importContactInfos'=>'Assumed_done'
    ];

    function init(){
        set_time_limit(0);
        $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect($this->app->getconfig('dsn2'));
        
        try{
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=0;')->execute();
            $this->app->db->dsql()->expr('SET autocommit=0;')->execute();

            $this->api->db->beginTransaction();
                parent::init();
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->commit();
        }catch(\Exception_StopInit $e){

        }catch(\Exception $e){
            $this->app->db->dsql()->expr('SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $this->app->db->dsql()->expr('SET unique_checks=1;')->execute();
            $this->api->db->rollback();
            throw $e;
        }
    }

    function test_checkEmptyRows(){
    	$result=[];
    	$result['department'] = $this->app->db->dsql()->table('department')->del('fields')->field('count(*)')->getOne();

    	return $result;
    }

    function prepare_ImportDepartments(){
        $old_depts = $this->pdb->dsql()->table('xhr_departments')
                        ->where('name','<>','Company')
                        ->where('production_level','>=',1)
                        ->where('production_level','<=',100)
                        ->get();
        $file_data=['1'=>['new_id'=>$this->add('xepan\hr\Model_Department')->loadBy('name','Company')->get('id')]];

        $new_dept = $this->add('xepan\hr\Model_Department');
        foreach ($old_depts as $dept){
                $new_dept
                ->set('name',$dept['name'])
                ->set('production_level',$dept['production_level'])
                ->set('is_outsourced',$dept['is_outsourced'])
                ->save()
                ;

                $file_data[$dept['id']] = ['new_id'=>$new_dept->id,'name'=>$new_dept['name']];

                $new_dept->unload();
        }

        file_put_contents(__DIR__.'/department_mapping.json', json_encode($file_data));
    }

    function test_ImportDepartments(){
        $new_depts = $this->api->db->dsql()->table('department')->get();

        $result = [];
        foreach ($new_depts as $d) {
            $result[] = $d['name'];
        }

        return $result;
    }

    function prepare_importPosts(){
        $old_posts = $this->pdb->dsql()->table('xhr_posts')
                        ->get();

        $department_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('department');

        $file_data=[];
        $new_post = $this->add('xepan\hr\Model_Post');
        foreach ($old_posts as $op){
                $new_post
                ->set('name',$op['name'])
                ->set('department_id',$department_mapping[$op['department_id']]['new_id']?:$department_mapping['1']['new_id'])
                ->save()
                ;

                $file_data[$op['id']] = ['new_id'=>$new_post->id];

                $new_post->unload();
        }

        file_put_contents(__DIR__.'/post_mapping.json', json_encode($file_data));
    }

    function test_importPosts(){
        $new_posts = $this->api->db->dsql()->table('post')->get();

        $result = [];
        foreach ($new_posts as $p) {
            $result[] = $p['name'];
        }

        return $result;
    }

    function prepare_importUsers(){
        $old_users = $this->pdb->dsql()->table('users')
                        ->get();
        $this->proper_responses['test_importUsers'] = count($old_users)+1;

        $file_data=[];
        $new_user = $this->add('xepan\base\Model_User');
        $auth = $this->app->add('BasicAuth')->usePasswordEncryption('md5');
        $auth->addEncryptionHook($new_user);
        $sno =1;
        foreach ($old_users as $ou){
                $emails = explode(",",$ou['email']);
                $email=trim($emails[0]);
                if( ! filter_var($email, FILTER_VALIDATE_EMAIL)) $email = ($sno++).'@poc.in';

                if($found = $this->app->db->dsql()->table('user')->where('username',$email)->del('fields')->field('count(*)')->getOne()){
                    $email = ($sno++).'_user@poc.in';
                }


                $new_user
                ->set('username',$email)
                ->set('password',rand(1,10000))
                ->set('scope',$ou['type']==100?'SuperUser':$ou['type']==80?'AdminUser':'WebsiteUser')
                ->save()
                ;

                $file_data[$ou['id']] = ['new_id'=>$new_user->id,'emails'=>$ou['email'],'name'=>$ou['name']];

                $new_user->unload();
        }

        file_put_contents(__DIR__.'/user_mapping.json', json_encode($file_data));
    }

    function test_importUsers(){
        return $this->api->db->dsql()->table('user')->del('fields')->field('count(*)')->getOne();
    }

    function prepare_importEmployies(){
        $old_m = $this->pdb->dsql()->table('xhr_employees')
                        ->get();
        $department_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('department');
        $post_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('post');
        $user_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('user');

        $file_data=[];
        $new_m = $this->add('xepan\hr\Model_Employee');
        foreach ($old_m as $om){

                $names = explode(" ", $om['name']);
                if(count($names)>1)
                    $ln = array_pop($names);
                $fn = implode(" ", $names);

                $new_m
                ->set('first_name',$fn)
                ->set('last_name',$ln)
                ->set('department_id',$department_mapping[$om['department_id']]['new_id'])
                ->set('post_id',$post_mapping[$om['post_id']]['new_id'])
                ->set('user_id',$user_mapping[$om['user_id']]['new_id'])
                ->save()
                ;

                $file_data[$om['id']] = ['new_id'=>$new_m->id];

                $new_m->unload();
        }

        file_put_contents(__DIR__.'/employee_mapping.json', json_encode($file_data));
    }

    function test_importEmployies(){
        return $this->add('xepan\hr\Model_Employee')->count()->getOne();
    }

    // Should be in accounts tests now
    // function test_defaultCurrency(){
    //     return $this->app->db->dsql()->table('currency')->del('fields')->field('group_concat(name)')->getOne();
    // }

    function prepare_importCustomers(){
        $old_m = $this->pdb->dsql()->table('xshop_memberdetails')
                        ->get();

        $country = $this->app->db->dsql()->table('country')->get();
        $state = $this->app->db->dsql()->table('state')->get();

        $country_array = [];
        foreach ($country as $key => $data) {
            $country_array[strtoupper($data['name'])] = $data['id'];
        }
        
        $state_array = [];
        foreach ($state as $key => $data) {
            $state_array[strtoupper($data['name'])] = $data['id'];
        }


        $this->proper_responses['test_importCustomers'] = count($old_m);

        $department_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('department');
        $post_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('post');
        $user_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('user');

        $file_data=[];
        $new_m = $this->add('xepan\commerce\Model_Customer');
        $used_user_ids=[];
        $new_user = $this->add('xepan\base\Model_User');
        $sno=1;
        foreach ($old_m as $om){
                $names = explode(" ", $user_mapping[$om['users_id']]['name']);
                $ln='';
                if(count($names)>1)
                    $ln = array_pop($names);
                $fn = implode(" ", $names);

                $new_user_id = $user_mapping[$om['users_id']]['new_id'];
                
                if(in_array($new_user_id,$used_user_ids)){
                    $email = ($sno++).'_customer@poc.in';

                    $new_user
                        ->set('username',$email)
                        ->set('password',rand(1,10000))
                        ->set('scope','WebsiteUser')
                        ->save()
                        ;
                    $new_user_id = $new_user->id;
                    $new_user->unload();
                }   

                // country and state id
                $state_id = $state_array[strtoupper($om['state'])]?:0;
                $country_id = $country_array[strtoupper($om['country'])]?:0;
                
                $new_m
                ->set('first_name',$fn)
                ->set('last_name',$ln)
                ->set('address',$om['address'])
                ->set('city',$om['city'])
                ->set('state_id',$state_id)
                ->set('country_id',$country_id)
                ->set('pin_code',$om['pincode'])
                ->set('user_id',$new_user_id)

                ->set('billing_address',$om['address'])
                ->set('billing_city',$om['city'])
                ->set('billing_state_id',$state_id)
                ->set('billing_country_id',$country_id)
                ->set('billing_pincode',$om['pincode'])

                ->set('same_as_billing_address',true)

                ->set('shipping_address',$om['address'])
                ->set('shipping_city',$om['city'])
                ->set('shipping_state_id',$state_id)
                ->set('shipping_country_id',$country_id)
                ->set('shipping_pincode',$om['pincode'])
                
                ->set('tin_no',$om['tin_no'])
                ->set('pan_no',$om['pan_no'])
                ->set('organization',$om['organization_name'])
                ->set('website',$om['website'])

                ->set('created_at',$om['created_at'])
                ->set('updated_at',$om['updated_at'])

                ->save()
                ;

                $used_user_ids[] = $user_mapping[$om['users_id']]['new_id'];
                $file_data[$om['id']] = ['new_id'=>$new_m->id,'mobile_numbers'=>$om['mobile_number'],'address'=>$om['address'],'city'=>$om['city'],'state'=>$om['state'],'country'=>$om['country'],'pincode'=>$om['pincode']];

                $new_m->unload();
        }

        file_put_contents(__DIR__.'/customer_mapping.json', json_encode($file_data));
    }

    function test_importCustomers(){
        return $this->add('xepan\commerce\Model_Customer')->count()->getOne();
    }

    function prepare_importContactInfos(){
        $user_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('user');
        $customer_mapping = $this->add('xepan\commerce\page_tests_init')->getMapping('customer');

        $new_contact = $this->add('xepan\base\Model_Contact');

        // Emails
        foreach ($user_mapping as $old_id=>$new_info) {
            if($emails = $new_info['emails']){
                $emails = explode(",", $emails);
                foreach ($emails as $email) {
                    if( ! filter_var(trim($email), FILTER_VALIDATE_EMAIL)) continue;
                    $new_contact->loadBy('user_id',$new_info['new_id']);
                    $new_contact->ref('Emails')->set('head','Official')->set('value',trim($email))->saveAndUnload();
                }
            }
        }

        // Phone numbers
        foreach ($customer_mapping as $old_id=>$new_info) {
            if($ph_nos = $new_info['mobile_numbers']){
                $ph_nos = explode(",", $ph_nos);
                foreach ($ph_nos as $phno) {
                    $new_contact->load($new_info['new_id']);
                    $new_contact->ref('Phones')->set('head','Official')->set('value',trim($phno))->saveAndUnload();
                }
            }
        }

    }


    function test_importContactInfos(){
        return "Assumed_done";
    }

}
