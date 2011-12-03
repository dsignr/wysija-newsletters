<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_uninstall extends WYSIJA_object{
    
    function WYSIJA_help_uninstall(){
        require_once(ABSPATH . 'wp-admin'.DS.'includes'.DS.'upgrade.php');
    }
    
    function uninstall(){
        $filename = dirname(__FILE__).DS."uninstall.sql";
        $handle = fopen($filename, "r");
        $query = fread($handle, filesize($filename));
        fclose($handle);
        $modelObj=&WYSIJA::get("user","model");
        $queries=str_replace("DROP TABLE `","DROP TABLE `".$modelObj->getPrefix(),$query);
        
        $queries=explode("-- QUERY ---",$queries);
        $modelWysija=new WYSIJA_model();
        global $wpdb;
        foreach($queries as $query)
            $modelWysija->query($query);

        delete_option("wysija");
        $this->wp_notice(__("Wysija has been uninstalled with success.",WYSIJA));
        
        return true;
    }
    
   
}
