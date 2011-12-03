<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_campaigns extends WYSIJA_control{
    
    function WYSIJA_control_back_campaigns(){
        $modelC=&WYSIJA::get("config","model");
        if(!current_user_can($modelC->getValue("role_campaign")))  die("Action is forbidden.");
        parent::WYSIJA_control();
    }
    
    function switch_theme() {
        if(isset($_POST['wysijaData'])) {
            $data = json_decode(stripslashes($_POST['wysijaData']), TRUE);
            $theme = (isset($data['theme'])) ? $data['theme'] : 'default';
            
            $wjEngine =& WYSIJA::get('wj_engine', 'helper');
            $res['templates'] = $wjEngine->renderTheme($theme);
            $res['styles'] = $wjEngine->renderThemeStyles($theme);
        } else {
            $res['msg'] = __("The theme you selected could not be loaded.",WYSIJA);
            $res['result'] = false;
        }
        return $res;
    }
    
    function save_editor() {
        // decode json data and convert to array
        $rawData = '';
        if(isset($_POST['wysijaData'])) {
            $rawData = json_decode(stripslashes($_POST['wysijaData']), TRUE);
        }

        $wjEngine =& WYSIJA::get('wj_engine', 'helper');
        $wjEngine->setData($rawData);
        $result = false;

        // get campaign id
        $campaign_id = $_REQUEST['campaignID'];
        $modelEmail =& WYSIJA::get('email', 'model');
        $emailData=$modelEmail->getOne(array('wj_styles', 'subject'),array("campaign_id"=>$campaign_id));
        
        $wjEngine->setStyles($emailData['wj_styles'], true);

        $values = array(
            'wj_data' => $wjEngine->getEncoded('data'),
            'body' => $wjEngine->renderEmail($emailData['subject'])
        );
        
        // update data in DB
        $result = $modelEmail->update($values, array('campaign_id' => $campaign_id));
        
        if(!$result) {
            // throw error
            $this->error(__("Your email could not be saved", WYSIJA));
        } else {
            // save successful
            $this->notice(__("Your email has been saved", WYSIJA));
        }
        
        return array('result' => $result);
    }
    
    function save_styles() {
        // decode json data and convert to array
        $rawData = '';
        if(isset($_POST['wysijaStyles'])) {
            $rawData = json_decode(stripslashes($_POST['wysijaStyles']), TRUE);
        }
        
        if(array_key_exists('a-underline', $rawData) === false) {
            $rawData['a-underline'] = -1;
        }
        
        $wjEngine =& WYSIJA::get('wj_engine', 'helper');
        $wjEngine->setStyles($wjEngine->formatStyles($rawData));
        
        $result = false;

        $values = array(
            'wj_styles' => $wjEngine->getEncoded('styles')
        );

        // get campaign id
        $campaign_id = $_REQUEST['campaignID'];
            
        // update data in DB
        $modelEmail =& WYSIJA::get('email', 'model');
        $result = $modelEmail->update($values, array('campaign_id' => $campaign_id));
            
        if(!$result) {
            // throw error
            $this->error(__("Styles could not be saved", WYSIJA));
        } else {
            // save successful
            $this->notice(__("Styles have been saved", WYSIJA));
        }
        
        return array(
            'styles' => $wjEngine->renderStyles(),
            'result' => true
        );
    }
    
    function deleteimg(){

        if(isset($_REQUEST['imgid']) && $_REQUEST['imgid']>0){
            /* delete the image with id imgid */
             $result=wp_delete_attachment($_REQUEST['imgid'],true);
             if($result){
                 $this->notice(__("Image has been deleted.",WYSIJA));
             }
        }

        $res=array();
        $res['result'] = $result;
        return $res;
    }
    
    
    function save_IQS() {
        // decode json data and convert to array
        $wysijaIMG = '';
        if(isset($_POST['wysijaIMG'])) {
            $wysijaIMG = json_decode(stripslashes($_POST['wysijaIMG']), TRUE);
        }
        $values = array(
            'params' => base64_encode(serialize(array('quickselection'=>$wysijaIMG)))
        );

        // get campaign id
        $campaign_id = (int)$_REQUEST['campaignID'];

        // update data in DB
        $modelEmail =& WYSIJA::get('email', 'model');
        $result = $modelEmail->update($values, array('campaign_id' => $campaign_id));

        if(!$result) {
            // throw error
            $this->error(__("Image selection has not been saved.", WYSIJA));
        } else {
            // save successful
            $this->notice(__("Image selection has been saved.", WYSIJA));
        }
        
        return array('result' => $result);
    }
    
    
    function view_NL() {
        // get campaign id
        $campaign_id = (int)$_REQUEST['id'];

        // update data in DB
        $modelEmail =& WYSIJA::get('email', 'model');
        $result = $modelEmail->getOne(false,array('campaign_id' => $campaign_id));

        echo $result['body'];
        exit;
    }
    
    function getarticles(){
 
        $model=&WYSIJA::get("user","model");
        global $wpdb;
        
        if(isset($_REQUEST['search'])){
            $querystr = "SELECT $wpdb->posts.ID , $wpdb->posts.post_title, $wpdb->posts.post_content
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_title like '%".addcslashes(mysql_real_escape_string($_REQUEST['search']), '%_' )."%' 
            AND $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post'
            AND $wpdb->posts.post_date < NOW()
            ORDER BY $wpdb->posts.post_date DESC";
        }else{
            $querystr = "SELECT $wpdb->posts.ID , $wpdb->posts.post_title, $wpdb->posts.post_content
            FROM $wpdb->posts
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post'
            AND $wpdb->posts.post_date < NOW()
            ORDER BY $wpdb->posts.post_date DESC
            LIMIT 0,10";
        }

        
        
        
        $res=array();
        $res['posts']=$model->query("get_res",$querystr);

        $helper_engine=&WYSIJA::get("wj_engine","helper");
        
        
        if($res['posts']){
            $res['result'] = true;
            foreach($res['posts'] as $k =>$v){

                /* get the featured image and if there is no featured image get the first image in the post */
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $v['ID'] ), 'single-post-thumbnail' );
                //htmlentities fucks up the accents so we use str_replace instead
                $res['posts'][$k]['post_title']=  str_replace(array("<",">"),array("&lt;","&gt;"),$res['posts'][$k]['post_title']);
                if($image){
                    $res['posts'][$k]['post_firstimage']["src"] = $image[0];
                    $res['posts'][$k]['post_firstimage']["width"]=$image[1];
                    $res['posts'][$k]['post_firstimage']["height"]=$image[2];

                }else{
                    $matches=$matches2=array(); 
                    
                    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $v['post_content'], $matches);
                    
                    if(isset($matches[0][0])){
                        preg_match_all('/(src|height|width|)="([^"]*)"/i',$matches[0][0], $matches2);
                    
                        if(isset($matches2[1])){
                           foreach($matches2[1] as $k2 =>$v2){
                                if(in_array($v2, array("src","width","height"))){
                                    $res['posts'][$k]['post_firstimage'][$v2]=$matches2[2][$k2];
                                }
                            } 
                        }else{
                            $res['posts'][$k]['post_firstimage']=null;
                        }
                    }else{
                        $res['posts'][$k]['post_firstimage']=null;
                    }
                    
                    
                }
                
                if(isset($res['posts'][$k]['post_firstimage']["src"])){
                    $res['posts'][$k]['post_firstimage']["alignment"]="left";
                    $res['posts'][$k]['post_firstimage']["url"]=  get_permalink($v['ID']);
                }else{
                    $res['posts'][$k]['post_firstimage']=null;
                }
                
                
                
                // convert new lines into <p>
                $content = wpautop($res['posts'][$k]['post_content'], false);
                
                // remove images
                $content = preg_replace('/<img[^>]+./','', $content);
                
                // remove shortcodes
                $content = preg_replace('/\[.*\]/', '', $content);
                
                // remove wysija nl shortcode
                $content= preg_replace('/\<div class="wysija-register">(.*?)\<\/div>/','',$content);
                
                // remove iframes and replace with links
                $content = preg_replace('/<iframe.*?src=\"(.+?)\".*><\/iframe>/', '<a href="$1">'.__('Click here to view media.', WYSIJA).'</a>', $content);
                
                // convert h4 h5 h6 to h3
                $content = preg_replace('/<([\/])?h[456](.*?)>/', '<$1h3$2>', $content);
                
                // strip useless tags
                $content = strip_tags($content, '<p><em><b><strong><i><h1><h2><h3><a><ul><ol><li>');
                
                // set post title if present
                if(strlen(trim($res['posts'][$k]['post_title'])) > 0) {
                    $content = '<h1>'.  $res['posts'][$k]['post_title'].'</h1>'.$content;
                }
                
                // add read online link
                $content .= '<p><a href="'.get_permalink($v['ID']).'">'.__('Read online.', WYSIJA).'</a></p>';

                $block = array(
                  'position' => 1,
                  'type' => 'content',
                  'text' => array(
                      'value' => $content
                  ),
                  'image' => $res['posts'][$k]['post_firstimage'],
                  'alignment' => 'left'
                );
                unset($res['posts'][$k]['post_content']);

                $res['posts'][$k]['html']=base64_encode($helper_engine->renderEditorBlock($block));
            }
            
        }else {
           
            $res['msg'] = __("There are no posts corresponding to that search.",WYSIJA);
            $res['result'] = false;
        }
        
        
        return $res;
    }
    
    function send_preview(){
        $mailer=&WYSIJA::get("mailer","helper");
        $campaign_id = $_REQUEST['campaignID'];

        
        // update data in DB
        $modelEmail =& WYSIJA::get('email', 'model');
        $modelEmail->getFormat=OBJECT;
        $emailObject = $modelEmail->getOne(false,array('campaign_id' => $campaign_id));
        $mailer->testemail=true;
        

        
        if(isset($_REQUEST['data'])){
           $dataTemp=$_REQUEST['data'];
            $_REQUEST['data']=array();
            foreach($dataTemp as $val) $_REQUEST['data'][$val["name"]]=$val["value"];
            $dataTemp=null;
            foreach($_REQUEST['data'] as $k =>$v){
                $newkey=str_replace(array("wysija[email][","]"),"",$k);
                $configVal[$newkey]=$v;
            }   
            $params=array(
                    'from_name'=>$configVal['from_name'],
                    'from_email'=>$configVal['from_email'],
                    'replyto_name'=>$configVal['replyto_name'],
                    'replyto_email'=>$configVal['replyto_email']);
            $emailObject->subject=$configVal['subject'];
        }else $params=array();

        //$mailer->report=false;
        //$res=$mailer->sendOne($emailObject,$_POST['receiver']);
        $res=$mailer->sendSimple($_REQUEST['receiver'],$emailObject->subject,$emailObject->body,$params);
        if($res)    $this->notice(sprintf(__('Your email preview has been sent to %1$s', WYSIJA),$_REQUEST['receiver']));

        
        return array('result' => $res);
    }
}