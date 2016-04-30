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

class page_tests_0010departmentImport extends \xepan\base\Page_Tester {
	
	public $title='Department Importer';

	public $proper_responses=[
    	'test_checkEmptyRows'=>['department'=>1],
        'test_ImportDepartments'=>['Company','Designing','Offset Printing','Digital Press','Large Format','Screen Printing','Varnish','Lamination','UV','Foil','Cutting','Die Cut','Laser Cut','Binding','Pasting','Frame'],
        'test_importPosts'=>['CEO','Director','HOD','Designer','Operator','HOD','Helper','HOD','Manager'],
    	'test_importUsers'=>439
    ];

    function init(){
        $this->add('xepan\commerce\page_tests_init')->resetDB();
        $this->pdb = $this->add('DB')->connect('mysql://root:winserver@localhost/prime_gen_1');
        parent::init();
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

                $file_data[$dept['id']] = ['new_id'=>$new_dept->id];

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
                    $email = ($sno++).'_'.$email;
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

}
