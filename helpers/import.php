<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_import extends WYSIJA_object{
    
    function WYSIJA_help_import(){
        
    }
    
    /* all the basic information concerning each plugin to make the match works */
    function getPluginsInfo(){
        $pluginsTest=array(
            'newsletter'=>array(
                "name"=>"Newsletter Pro",
                "pk"=>"id",
                "matches"=>array("name"=>"firstname","email"=>"email","surname"=>"lastname","ip"=>"ip")
            ),
            'wpmlsubscribers'=>array(
                "name"=>"WordPress Newsletter Plugin",
                "pk"=>"id",
                "matches"=>array("email"=>"email","ip_address"=>"ip")
            )
        );
        
        
        return $pluginsTest;
    }
    
    /**
     * this function is run only once at install to test which plugins are possible to import
     * @global type $wpdb 
     */
    function testPlugins(){
        $modelWysija=new WYSIJA_model();
        $possibleImport=array();
        foreach($this->getPluginsInfo() as $tableName =>$pluginInfos){
            $result=$modelWysija->query("SHOW TABLES like '".$modelWysija->wpprefix.$tableName."';");

            if($result){
                $result=$modelWysija->query("get_row", "SELECT COUNT(`".$pluginInfos['pk']."`) as total FROM `".$modelWysija->wpprefix.$tableName."`;", ARRAY_A);
                
                if((int)$result['total']>0){
                    /* there is a possible import to do */
                    $pluginInfos['total']=(int)$result['total'];
                    $possibleImport[$tableName]=$pluginInfos;
                    
                }
            }
        }

        /* if we found some plugins to import from we just save their details in the config */
        if($possibleImport){
            $modelConfig=&WYSIJA::get("config","model");
            $modelConfig->save(array("pluginsImportable"=>$possibleImport));
        }

    }
    
    
    function import($tablename,$plugInfo,$issyncwp=false){
        global $wpdb;
        /* insert the list corresponding to that import */
        $model=&WYSIJA::get("list","model");
        if($issyncwp)   $listname="<b><i>".__("Synched",WYSIJA)."</i></b> ".$plugInfo["name"];
        else $listname=sprintf(__('%1$s\'s import list',WYSIJA),$plugInfo["name"]);

        $defaultListId=$model->insert(array(
            "name"=>$listname,
            "description"=>sprintf(__('The list created automatically on import of the plugin\'s subscribers : "%1$s',WYSIJA),$plugInfo["name"]),
            "is_enabled"=>0));
        
        /* prepare the table transfer query*/
        $colsPlugin=array_keys($plugInfo["matches"]);
        $mktime=mktime();
        $extracols=$extravals="";
        
        if(isset($plugInfo["matchesvar"])){
            $extracols=",`".implode("`,`",array_keys($plugInfo["matchesvar"]))."`";
            $extravals=",".implode(",",$plugInfo["matchesvar"]);
        }
        
        $fields="(`".implode("`,`",$plugInfo["matches"])."`,`created_at` ".$extracols." )";
        $values="`".implode("`,`",$colsPlugin)."`,".$mktime.$extravals;

//        $query="SELECT $values FROM ".$wpdb->prefix.$tablename;
//        $query="SELECT `ID`,`user_email`,`display_name`,1321529559,1 FROM wp_users";
//        $resul=$model->query("get_res",$query);
//        echo $query;
//        echo "<pre>";
//        print_r($resul);
//        exit;
        /* query to save the plugins subscribers into wysija subsribers*/
        if($tablename=='users') {
            $query="INSERT IGNORE INTO `".$model->getPrefix()."user` $fields SELECT $values FROM ".$wpdb->base_prefix.$tablename;
        }else    $query="INSERT IGNORE INTO `".$model->getPrefix()."user` $fields SELECT $values FROM ".$model->wpprefix.$tablename;
        
        $model->query($query);
        
        /* query to save the fresshly inserted subscribers into wysija new imported list*/
        $query="INSERT IGNORE INTO `".$model->getPrefix()."user_list` (`user_id`,`list_id`,`sub_date`) SELECT `user_id`, ".$defaultListId.", ".mktime()." FROM ".$model->getPrefix()."user WHERE created_at='".$mktime."'";
        $model->query($query);
        
        $query="SELECT COUNT(user_id) as total FROM ".$model->getPrefix()."user WHERE created_at='".$mktime."'";
        $result=$wpdb->get_row($query, ARRAY_A);
        
        $helperU=&WYSIJA::get("user","helper");
        $helperU->refreshUsers();
        
        $this->wp_notice(sprintf(__('%1$s subscribers from %2$s have been imported into the new list %3$s',WYSIJA),"<strong>".$result['total']."</strong>","<strong>".$plugInfo['name']."</strong>","<strong>".$listname."</strong>"));
        
        return $defaultListId;
    }
    
    
    function importWP(){

        $infosImport=array("name"=>"Wordpress",
                "pk"=>"ID",
                "matches"=>array("ID"=>"wpuser_id","user_email"=>"email","display_name"=>"firstname"),
                "matchesvar"=>array("status"=>1));
        
        $tablename='users';
        
        return $this->import($tablename,$infosImport,true);
    }
    

}

