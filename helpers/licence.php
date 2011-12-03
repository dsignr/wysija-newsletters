<?php
defined('WYSIJA') or die('Restricted access');
/**
 * class managing the admin vital part to integrate wordpress
 */
class WYSIJA_help_licence extends WYSIJA_help{
    
    function WYSIJA_help_licence(){
        
        parent::WYSIJA_help();

    }
    function check($js=false){
        $data=array();
        if(isset($_SERVER['HTTP_REFERER'])) $url=$_SERVER['HTTP_REFERER'];
        else $url=admin_url('admin.php');
        $data['domain_name']=WYSIJA::_make_domain_name($url);
        $data['url']=$url;
        $data[uniqid()]=uniqid('WYSIJA');

        $data=base64_encode(serialize($data));
        
        if(!$js) {
            WYSIJA::update_option("wysijey",$data);
        }
        $res['domain_name']=$data;
        /* remotely connect to host */
        $jsonResult = file_get_contents('http://www.wysija.com/?wysijap=checkout&wysijashop-page=1&controller=customer&action=checkDomain&data='.$data);
//$jsonResult=false;
        $res['nocontact']=false;
        if($jsonResult){
            $decoded=json_decode($jsonResult);

            if(isset($decoded->msgs))   $this->error($decoded->msgs);
            if($decoded->result){
                $res['result']=true;
                //set premium to true
                $dataconf=array('premium_key'=>base64_encode(get_option('home').mktime()),'premium_val'=>mktime());
                $this->notice(__("Premium version is valid for your site.",WYSIJA));
                WYSIJA::update_option("wysicheck",false);
            }else{
                $dataconf=array('premium_key'=>"",'premium_val'=>"");
                $this->error(__("Premium version licence does not exists for your site.",WYSIJA),1);
            }
            $modelConf=&WYSIJA::get("config","model");
            $modelConf->save($dataconf);

        }else{
            $res['nocontact']=true;
             WYSIJA::update_option("wysicheck",true);
            //$this->error(__("We cannot contact wysija.com to verify your licence.",WYSIJA),true);
        }
        return $res;
    }
   
}

