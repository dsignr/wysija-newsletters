<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_user extends WYSIJA_model{
    
    var $pk="user_id";
    var $table_name="user";
    var $columns=array(
        'user_id'=>array("auto"=>true),
        'wpuser_id' => array("req"=>true,"type"=>"integer"),
        'email' => array("req"=>true,"type"=>"email"),
        'firstname' => array(),
        'lastname' => array(),
        'ip' => array("req"=>true,"type"=>"ip"),
        'keyuser' => array(),
        'status' => array("req"=>true,"type"=>"boolean"),
        'created_at' => array("auto"=>true,"type"=>"date"),
    );

    function WYSIJA_model_user(){
        $this->columns['status']['label']=__('Status',WYSIJA);
        $this->columns['created_at']['label']=__('Created on',WYSIJA);
        $this->WYSIJA_model();
    }
    
    function beforeInsert(){
        /* set the activation key */
        $modelUser=WYSIJA::get("user","model");
        $existid=$modelUser->exists(array("email"=>$this->values['email']));
 
        if($existid){
               
                $this->error(str_replace(array("[link]","[/link]"),array('<a href="admin.php?page=wysija_subscribers&action=edit&id='.$existid[0]['user_id'].'" >',"</a>"),__(' Oops! This user already exists.Find him [link]here[/link].',WYSIJA)),true);
//$this->error(__('Subscriber already exists.',WYSIJA),true);
            return false;
        }

        $this->values['keyuser']=md5($this->values['email'].$this->values['created_at']);
        while($modelUser->exists(array("keyuser"=>$this->values['keyuser']))){
            $this->values['keyuser']=$this->generateKeyuser($this->values['email']);
            $modelUser->reset();
        }
        
        if(!isset($this->values['status'])) $this->values['status']=0;
        
        if(!isset($this->values['ip'])){
            $userHelper=&WYSIJA::get("user","helper");
            /*record the ip and save the user*/
            $this->values['ip']=$userHelper->getIP();
        }
        
        return true;
    }
    
    
    function getDetails($conditions,$stats=false,$subscribedListOnly=false){
        $data=array();
        $this->getFormat=ARRAY_A;
        $array=$this->getOne(false,$conditions);
        if(!$array) return false;
        
        $data['details']=$array;

        /* get the list  that the user subscribed to */
        $modelRECYCLE=&WYSIJA::get("user_list","model");
        $conditions=array("user_id"=>$data['details']['user_id']);
        if($subscribedListOnly){
            $conditions["unsub_date"]=0;
        }

        $data['lists']=$modelRECYCLE->get(false,$conditions);
        
        /* get the user stats if requested */
        if($stats){
            $modelRECYCLE=&WYSIJA::get("email_user_stat","model");
            $modelRECYCLE->setConditions(array("equal"=>array("user_id"=>$data['details']['user_id'])));
            $data['emails']=$modelRECYCLE->count(false);
        }
        
        
        return $data;
    }
    
    function getConfirmLink($userObj=false,$action="subscribe",$text=false,$urlOnly=false){
        if(!$text) $text=__("Click here to subscribe",WYSIJA);
        $userspreview=false;
        if(!$userObj){
            //preview mode
            $this->getFormat=OBJECT;
            $userObj=$this->getOne(false,array('wpuser_id'=>get_current_user_id()));
            $userspreview=true;
        }
        if($userObj){
            if(!$userObj->keyuser){
                $this->getKeyUser($userObj);
            }
            
            $this->reset();
            $modelConf=&WYSIJA::get("config","model");

            $params=array(
                'wysija-page'=>1,
                'controller'=>"confirm",
                'action'=>$action,
                'wysija-key'=>$userObj->keyuser,
                );
            
            //$fullurl=$confirmLink.$charStart.'wysija-page=1&controller=confirm&action='.$action.'&wysija-key='.$userObj->keyuser;
            if($userspreview) $params['demo']=1;
            $fullurl=WYSIJA::get_permalink($modelConf->getValue("confirm_email_link"),$params);
            if($urlOnly) return $fullurl;
            return '<a href="'.$fullurl.'" target="_blank">'.$text.'</a>';
            
        }     
    }
    
    function getEditsubLink($userObj=false,$urlOnly=false){
        return $this->getConfirmLink($userObj,"subscriptions",__("Edit your subscriptions",WYSIJA),$urlOnly);       
    }
    
    function getUnsubLink($userObj=false,$urlOnly=false){
        return $this->getConfirmLink($userObj,"unsubscribe",__("Unsubscribe",WYSIJA),$urlOnly);       
    }
    
    function getKeyUser($user){
        /* generate a user key */
        $user->keyuser=$this->generateKeyuser($user->email);
         while($this->exists(array("keyuser"=>$user->keyuser))){
             $user->keyuser=$this->generateKeyuser($user->email);
         }
        $this->update(array("keyuser"=>$user->keyuser),array('user_id'=>$user->user_id));
    }
    
    function generateKeyuser($email){
        return md5($email.mktime());
    }
    
    function user_id($email){
        $this->getFormat=ARRAY_A;
        if(is_numeric($email)){
            $obj=$this->getOne(array("user_id"),array("wpuser_id"=>$email));
            //$cond = ' wpuser_id = '.$email;
        }else{
            $obj=$this->getOne(array("user_id"),array("email"=>$email));
            //$cond = 'email = '.$this->database->Quote(trim($email));
        }

            //$this->database->setQuery('SELECT subid FROM '.acymailing_table('subscriber').' WHERE '.$cond);
        return $obj['user_id'];
    }
    
    function beforeDelete(){
        
        //delete all the user stats
        $eusM=&WYSIJA::get('email_user_stat','model');
        $eusM->delete($this->conditions);
        return true;
    }

}
