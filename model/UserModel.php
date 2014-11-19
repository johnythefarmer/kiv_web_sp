<?php
    Class UserModel extends Model{
        public function isLogged($nick, $pwd){
            if($nick != "user" || $pwd != "pwd"){
                return false;
            }
            return true;
        }
        
        public function getUserData(){
            $data = array('id'=>1,'nick'=>'JohnnyTheFarmer', 'firstName'=>'Jirka', 'lastName'=>'Novotny', 'img'=>'avatar.jpg');
            return $data;
        }
    }
?>