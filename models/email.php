<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_email extends WYSIJA_model{
    
    var $pk="email_id";
    var $table_name="email";
    var $columns=array(
        'email_id'=>array("auto"=>true),
        'campaign_id' => array("type"=>"integer"),
        'subject' => array("req"=>true),
        'body' => array("req"=>true,"html"=>1),
        'from_email' => array("req"=>true),
        'from_name' => array("req"=>true),
        'replyto_email' => array(),
        'replyto_name' => array(),
        'attachments' => array(),
        'status' => array("type"=>"integer"),
        'type' => array("type"=>"integer"),
        'number_sent'=>array("type"=>"integer"),
        'number_opened'=>array("type"=>"integer"),
        'number_clicked'=>array("type"=>"integer"),
        'number_unsub'=>array("type"=>"integer"),
        'number_bounce'=>array("type"=>"integer"),
        'number_forward'=>array("type"=>"integer"),
        'sent_at' => array("type"=>"date"),
        'created_at' => array("type"=>"date"),
        'params' => array(),
        'wj_data' => array(),
        'wj_styles' => array()
    );
    /*var $escapeFields=array('subject','body');
    var $escapingOn=true;*/
    
    
    
    
    function WYSIJA_model_email(){
        $this->WYSIJA_model();
    }
    
    function beforeInsert(){
        $modelConfig=&WYSIJA::get("config","model");
        if(!isset($this->values["from_email"])) $this->values["from_email"]=$modelConfig->getValue("from_email");
        if(!isset($this->values["from_name"])) $this->values["from_name"]=$modelConfig->getValue("from_name");
        if(!isset($this->values["replyto_email"])) $this->values["replyto_email"]=$modelConfig->getValue("replyto_email");
        if(!isset($this->values["replyto_name"])) $this->values["replyto_name"]=$modelConfig->getValue("replyto_name");
        
        return true;
    }  


}
