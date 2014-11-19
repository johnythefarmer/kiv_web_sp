<?php
    Class UserModel extends Model{
        public function checkLogin($nick, $pwd){
            if($nick != "user" || $pwd != "pwd"){
                return -1;
            }
            return 1;
        }
        
        public function isAdmin($id){
            return true;
        }
        
        public function getUserData($id){
            if($id == 20){
                return null;
            }
            $data = array('id'=>$id,'nick'=>'JohnnyTheFarmer', 'firstName'=>'Jirka', 'lastName'=>'Novotny', 'img'=>'avatar.jpg', 'web' => "http://google.com", 'email' =>'frantapepa@google.com', 'mo' => 'MO Stod', 'moId' => '123');
            return $data;
        }
    }
?>