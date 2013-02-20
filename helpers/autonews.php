<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_autonews  extends WYSIJA_object {
    function WYSIJA_help_autonews() {
    }
    function events($key=false,$get=true,$valueSet=array()){
        static $events=array();
        if($get){
            if(!$key){
                return $events;
            }else{
                if(isset($events[$key])) return $events[$key];
                return false;
            }
        }else{
            if(isset($events[$key])) return false;
            $events[$key]=$valueSet;
        }
    }
    function register($keyevent,$event=array()){
        $this->events($keyevent,false,$event);
    }
    function get($fieldKey){
         return $this->events($fieldKey);
    }
    
    function nextSend($email=false){
        if(!$email) return;
        $modelEmail=&WYSIJA::get('email','model');
        if(is_array($email)){
            $emailArr=$modelEmail->getOne(false,array('email_id'=>$email['email_id']));
        }else{
            $emailArr=$modelEmail->getOne(false,array('email_id'=>$email));
        }
        return $modelEmail->giveBirth($emailArr);
    }
    
    function getNextSend($email) {
        $schedule_at = -1;

        if((int)$email['type'] === 2 && isset($email['params']['autonl']['event']) && $email['params']['autonl']['event'] === 'new-articles') {
            $hToolbox =& WYSIJA::get('toolbox','helper');

            $now = $hToolbox->offset_time();
            if(!isset($email['params']['autonl']['nextSend']) || $now > $hToolbox->offset_time($email['params']['autonl']['nextSend'])) {
                switch($email['params']['autonl']['when-article']) {
                    case 'immediate':
                        break;
                    case 'daily':

                        $schedule_at = strtotime($email['params']['autonl']['time']);

                        if($schedule_at < $now) {

                            $schedule_at = strtotime('tomorrow '.$email['params']['autonl']['time']);
                        }
                        break;
                    case 'weekly':

                        $schedule_at = strtotime(ucfirst($email['params']['autonl']['dayname']).' '.$email['params']['autonl']['time']);

                        if($schedule_at < $now) {

                            $schedule_at = strtotime('next '.ucfirst($email['params']['autonl']['dayname']).' '.$email['params']['autonl']['time']);
                        }
                        break;
                    case 'monthly':
                        $timeCurrentDay=date('d',$now);
                        $timeCurrentMonth=date('m',$now);
                        $timeCurrentYear=date('y',$now);

                        if($timeCurrentDay > $email['params']['autonl']['daynumber']) {
                            if((int)$timeCurrentMonth === 12) {

                               $timeCurrentMonth=1;
                               $timeCurrentYear++;
                            }else{

                                $timeCurrentMonth++;
                            }
                        }
                        $schedule_at=strtotime($timeCurrentMonth.'/'.$email['params']['autonl']['daynumber'].'/'.$timeCurrentYear.' '.$email['params']['autonl']['time']);
                        break;
                    case 'monthlyevery': // monthly every X Day of the week
                        $currentDay = date('d', $now);
                        $currentMonth = date('m', $now);
                        $currentYear = date('y', $now);


                        $schedule_at = strtotime(
                            sprintf('%02d/01/%02d %d %s %s',
                            $currentMonth,
                            $currentYear,
                            $email['params']['autonl']['dayevery'],
                            ucfirst($email['params']['autonl']['dayname']),
                            $email['params']['autonl']['time']
                        ));
                        if($schedule_at < $now) {

                            $schedule_at = strtotime(
                                sprintf('+1 month %02d/01/%02d %d %s %s',
                                $currentMonth,
                                $currentYear,
                                $email['params']['autonl']['dayevery'],
                                ucfirst($email['params']['autonl']['dayname']),
                                $email['params']['autonl']['time']
                            ));
                        }
                        break;
                }
            }
        }
        return $schedule_at;
    }
    
    function getNextDay($firstDayOfMonth,$dayname,$whichNumber,$timenow){
        $nameFirstday = strtolower(date('l', $firstDayOfMonth));
        if($nameFirstday == strtolower($dayname)) $whichNumber--;
        for($i=0; $i < $whichNumber;$i++){
            $firstDayOfMonth = strtotime('next '.ucfirst($dayname), $firstDayOfMonth);
        }
        return $firstDayOfMonth;
    }

    
    function checkPostNotif(){
        $modelEmail=&WYSIJA::get('email','model');
        $modelEmail->reset();
        $allEmails=$modelEmail->get(false,array('type'=>'2','status'=>array('1','3','99')));
        if($allEmails){
            $hToolbox=&WYSIJA::get('toolbox','helper');
            
            foreach($allEmails as $email){
                
                if($email['params']['autonl']['event']=='new-articles' && $email['params']['autonl']['when-article']!='immediate'){
                    
                    if(time()>$hToolbox->offset_time($email['params']['autonl']['nextSend']))
                        $modelEmail->giveBirth($email);
                }
            }
        }
    }

    
    function checkScheduled(){
        $modelEmail=&WYSIJA::get('email','model');
        $modelEmail->reset();
        $allEmails=$modelEmail->get(false,array('type'=>'1','status'=>'4'));
        if($allEmails){
            $hToolbox=&WYSIJA::get('toolbox','helper');
            foreach($allEmails as $email){
                
                if(isset($email['params']['schedule']['isscheduled'])){
                    $scheduledate=$email['params']['schedule']['day'].' '.$email['params']['schedule']['time'];
                    $unixscheduledtime=strtotime($scheduledate);
                    
                    
                    if($hToolbox->offset_time($unixscheduledtime)<time()){
                        $modelEmail->reset();
                        $modelEmail->send($email,true);
                    }
                }
            }
        }
    }
}
