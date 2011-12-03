<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_view_front_widget_nl extends WYSIJA_view_front {
    
    function WYSIJA_view_front_widget_nl(){
        $this->model=&WYSIJA::get("user","model");
    }
    
    function display($title="",$params,$echo=true){
        $this->addScripts();
        $data="";
        $formidreal="form-".$params['id_form'];
        if(isset($_POST['wysija']['user']['email']) && isset($_POST['formid'])){
            if($formidreal==$_POST['formid'])    $data.= $this->messages();
        }
        $data.= $title;
        //unset($this->model->columns['email']['req']);
        $classValidate=$this->getClassValidate($this->model->columns['email']);

        $disabledSubmit=$msgsuccesspreview='';
        if(isset($params['preview'])){
            $disabledSubmit='disabled="disabled"';
            $msgsuccesspreview='<div class="allmsgs"><div class="updated">'.$params["success"].'</div></div>';
        }
        
        $data.='<div id="msg-'.$formidreal.'" class="wysija-msg ajax">'.$msgsuccesspreview.'</div>
        <form id="'.$formidreal.'" method="post" action="" class="form-valid-sub">';
            if(isset($params['instruction']))   $data.='<p class="wysija-instruct">'.$params['instruction'].'</p>';
            $data.='<p><input type="text" id="'.$formidreal.'wysija-email" '.$classValidate.' name="wysija[user][email]" />';
            $data.=$this->customFields();
            $data.='<br />';
            $data.='<input type="submit" '.$disabledSubmit.' class="wysija-submit-field" name="submit" value="'.esc_attr($params['submit']).'"/></p>';

            if(!isset($params['preview'])){
                $data.='<input type="hidden" name="formid" value="'.esc_attr($formidreal).'" />
                    <input type="hidden" name="action" value="save" />
                <input type="hidden" name="wysija[user_list][list_ids]" value="'.esc_attr(implode(',',$params["lists"])).'" />
                <input type="hidden" name="message_success" value="'.esc_attr($params["success"]).'" />
                <input type="hidden" name="controller" value="subscribers" />';//$this->secure(array('action'=>'save','controller'=>'subscribers'));
                $data.='<input type="hidden" value="'.wp_create_nonce("wysija_ajax").'" id="wysijax" />';
            }

            
	$data.='</form>';

        if($echo) echo $data;
        else return $data;
    }
    
    function customFields(){
        
    }
    
}
