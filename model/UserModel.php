<?php
    Class UserModel extends Model{
        public function __construct(){
            parent::__construct();
        }
		
		public function addLike($userId, $trophyId){
			$stm = self::$pdo->prepare("INSERT INTO trophy.uzivatel_likes_ulovek
				(user_id, ulovek_id, d_like)
				VALUES(:userId, :trophyId, now())
				ON DUPLICATE KEY UPDATE d_like = values(d_like);");
			
			return $stm->execute(array('userId' => $userId, 'trophyId' => $trophyId));
		}
        
        public function checkLogin($nick, $pwd){
            $stm = self::$pdo->prepare("select u.user_id from trophy.uzivatel u where nickname = :nick and password = :pwd;");
            $stm->execute(array('nick' => $nick, 'pwd' => md5($pwd.$nick{0})));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(count($rows) != 1){
                return -1;
            }else {
                return $rows[0]['user_id'];
            }
        }
        
        public function isAdmin($id){
            $stm = self::$pdo->prepare("select u.is_admin from trophy.uzivatel u where user_id = :id");
            $stm->execute(array('id' => $id));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            
            return $row['is_admin'];
        }
		
		public function makeAdmin($id){
			$stm = self::$pdo->prepare("update trophy.uzivatel set is_admin = true where user_id = :id");
			$actionOk = $stm->execute(array("id" => $id));
			
			return $actionOk;
		}
        
        public function register($nick, $email, $first_name, $last_name, $pwd, $web, $mo_id, $photo_url){
            
            $stm = self::$pdo->prepare("insert into trophy.uzivatel
                (nickname, password, email, jmeno, prijmeni, photo_url, is_admin, web, d_registrace, mo_id)
                values(:nick, :pwd, :email, :first_name, :last_name, :photo_url, false, :web, now(), :mo_id)");
            var_dump(md5($pwd.$nick{0}));
            return $stm->execute(array('nick' => $nick, 'pwd' => md5($pwd.$nick{0}), 'email' => $email,
                                'first_name' => $first_name, 'last_name' => $last_name,
                                'photo_url' => $photo_url, 'web' => $web, 'mo_id' => $mo_id));
        }
		
		public function deleteUser($id){
			$stm = self::$pdo->prepare("delete from trophy.uzivatel where user_id = :id");
			
			$deleteOk = $stm->execute(array("id" => $id));
			return $deleteOk;
		}
		
		public function editUser($id, $email, $first_name, $last_name, $web, $mo_id, $img = false){
			if($img == false){
				$stm = self::$pdo->prepare("update trophy.uzivatel
					set email = :email, jmeno = :first_name,
					prijmeni = :last_name, web = :web, mo_id = :mo_id
					where user_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "email" => $email, "first_name" => $first_name,
											   "last_name" => $last_name, "web" => $web, "mo_id" => $mo_id));
				/*$error = $stm->errorInfo();
				var_dump($error);*/
			} else {
				$stm = self::$pdo->prepare("update trophy.uzivatel
					set email = :email, jmeno = :first_name,
					prijmeni = :last_name, web = :web, mo_id = :mo_id,
					photo_url = :img
					where user_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "email" => $email, "first_name" => $first_name,
											   "last_name" => $last_name, "web" => $web, "mo_id" => $mo_id,
											   "img" => $img));
/*				$error = $stm->errorInfo();
				var_dump($error);*/
			}
			
			
			return $updateOk;
		}
        
        public function getUserData($id){
            $stm = self::$pdo->prepare("SELECT u.user_id,
                u.nickname, u.jmeno, u.prijmeni, u.photo_url,
                u.web, u.email, u.mo_id, mo.nazev, u.is_admin
                FROM trophy.uzivatel u inner join trophy.mistni_organizace mo
                on u.mo_id = mo.mo_id 
                where u.user_id = :id;");
            $stm->execute(array('id'=>$id));
//            $errors = $stm->errorInfo();
            //print_r($errors);
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            if($row == null){
                return null;
            }
            return $this->getUserFromRow($row);
        }
		
		public function getAllUsersData(){
			$stm = self::$pdo->prepare("SELECT u.user_id,
                u.nickname, u.jmeno, u.prijmeni, u.photo_url,
                u.web, u.email, u.mo_id, mo.nazev, u.is_admin
                FROM trophy.uzivatel u inner join trophy.mistni_organizace mo
                on u.mo_id = mo.mo_id");
			
			$stm->execute();
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			$n = count($rows);
			$users = array();
			for($i = 0; $i < $n; $i++){
				$users[$i] = $this->getUserFromRow($rows[$i]);
			}
			
			return $users;
		}
		
        
        private function getUserFromRow($row){
            $data = array('id'=>$row["user_id"],'nick'=>$row['nickname'],
                          'firstName'=>$row['jmeno'], 'lastName'=>$row['prijmeni'],
                          'img'=>$row['photo_url'], 'web' => $row['web'], 'email' =>$row['email'],
                          'mo' => $row['nazev'], 'moId' => $row['mo_id'], 'isAdmin' => $row['is_admin']);
            return $data;
        }
    }
?>