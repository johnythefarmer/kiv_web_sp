<?php
    class TrophyModel extends Model{
		private $stmLike;
		
		/**
		*	Konstruktor tridy
		*/
		public function __construct(){
			$this->stmLike = self::$pdo->prepare("SELECT count(ulovek_id)
				as likeCount
				FROM trophy.uzivatel_likes_ulovek
				where ulovek_id = :id
				group by(ulovek_id);");
		}
		
		/**
		*	Nahrani ulovku
		*/
		public function post($lovec, $druh, $velikost, $vaha, $datum, $revir, $podrevir, $img){
			$stm = self::$pdo->prepare("insert into trophy.ulovek
				(user_id, druh_id, velikost, vaha, d_chyceni,
				revir_id, podrevir, photo_url, d_post)
				values(:lovec, :druh, :velikost, :vaha,
				:datum, :revir, :podrevir, :img, now())");
			
			$param = array('lovec' => $lovec, 'druh' => $druh, 'velikost' => $velikost,
				'vaha' => $vaha, 'datum' => $datum, 'revir' => $revir,
				'podrevir' => $podrevir, 'img' => $img);
			
			$postOk = $stm->execute($param);
			
			if($postOk){
				return $this->getLastInsertId();
			}else{
				return 0;
			}
			
		}
		
		public function editTrophy($id, $druh, $velikost, $vaha, $datum, $revir, $podrevir, $img = false){
			if($img == false){
				$stm = self::$pdo->prepare("update trophy.ulovek
					set druh_id = :druh, velikost = :velikost,
					vaha = :vaha, d_chyceni = :datum, revir_id = :revir, podrevir = :podrevir
					where ulovek_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "druh" => $druh, "velikost" => $velikost,
										   "vaha" => $vaha, "datum" => $datum, "revir" => $revir,
											"podrevir" => $podrevir));
				/*$error = $stm->errorInfo();
				var_dump($error);*/
			} else {
				$stm = self::$pdo->prepare("update trophy.ulovek
					set druh_id = :druh, velikost = :velikost,
					vaha = :vaha, d_chyceni = :datum, revir_id = :revir, podrevir = :podrevir,
					photo_url = :img
					where ulovek_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "druh" => $druh, "velikost" => $velikost,
										   "vaha" => $vaha, "datum" => $datum, "revir" => $revir,
											"podrevir" => $podrevir, "img" => $img));
/*				$error = $stm->errorInfo();
				var_dump($error);*/
			}
			
			
			return $updateOk;
		}
		
		public function deleteTrophy($id){
			$stm = self::$pdo->prepare("delete from trophy.ulovek where ulovek_id = :id");
			
			$deleteOk = $stm->execute(array("id" => $id));
			return $deleteOk;
		}
		
		public function getAllTrophiesData(){
           $stm = self::$pdo->prepare(SELECT_TROPHY);

            $stm->execute();
			/*$error = $stm->errorInfo();
			var_dump($error);*/
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            
			$n = count($rows);
			for($i = 0; $i < $n; $i++){
                
               	$likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);
				
                $trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
		}
		
		public function getTrophyData($id){
			$stm = self::$pdo->prepare("select * from trophy.ulovek where ulovek_id = :id");
			
			$stm->execute(array("id" => $id));
						  
			$row = $stm->fetch(PDO::FETCH_ASSOC);
			
			if($row == null){
				return null;
			}
			
			return array("id" => $id, "lovecId" => $row['user_id'], "druhId" => $row["druh_id"] ,
						 "velikost" => $row["velikost"], "vaha" => $row["vaha"],
						 "datum" => $row["d_chyceni"], "revirId" => $row["revir_id"], "podrevir" => $row["podrevir"], "img" => $row['photo_url']);
		}

		
		/**
		*	Ziskani nejlepsich ulovku v danem casovem obdobi
		*/
		public function getTop($duration){
			switch($duration){
				case 'day': $stm = self::$pdo->prepare("select * from trophy.topday");break;
				case 'week': $stm = self::$pdo->prepare("select * from trophy.topweek");break;
				case 'month': $stm = self::$pdo->prepare("select * from trophy.topmonth");break;
				case 'year': $stm = self::$pdo->prepare("select * from trophy.topyear");break;
				default: return null;
			}
			
			$stm->execute();
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            
			$trophies = array();
			
			$n = count($rows);
            for($i = 0; $i < count($rows); $i++){
                
                $likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);
				
				
                $trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
		}
		
		/**
		*	Ziska id nejnovejsiho ulovku
		*/
		public function getMostRecentTrophyId(){
			$stmId = self::$pdo->prepare("select ul.ulovek_id as last 
										from trophy.ulovek ul 
										order by ulovek_id desc
										limit 1");
			$stmId->execute();
			return $stmId->fetch(PDO::FETCH_ASSOC)['last'];
		}
		
		/**
		*	Ziska id prave vlozeneho ulovku od predchozi metody se lisi, ze toto lze volat jen bezprostredne po vlozeni
		*/
		private function getLastInsertId(){
			$stmId = self::$pdo->prepare("select last_insert_id() as last");
			$stmId->execute();
			return $stmId->fetch(PDO::FETCH_ASSOC)['last'];
		}
		
		/**
		*	Ziska data o ulovku s danym ID
		*/
        public function getTrophyById($trophyId){
            $stm = self::$pdo->prepare(SELECT_TROPHY."where ul.ulovek_id = :id;");
            
            $stm->execute(array('id' => $trophyId));
			
            $row = $stm->fetch(PDO::FETCH_ASSOC);
			
			$likeCount = $this->getLikeCount($row['ulovek_id']);
			
            return $this->getTrophyFromRow($row, 0, $likeCount);
        }
        
		/**
		*	Ziska seznam vsech liku k danemu ulovku
		*/
        public function getAllLikes($trophyId){
            $stm = self::$pdo->prepare("SELECT uz.nickname, date_format(ulu.d_like, '%e. %c. %Y %T')
										as time_like FROM trophy.uzivatel_likes_ulovek ulu
										inner join trophy.uzivatel uz on ulu.user_id = uz.user_id 
										where ulu.ulovek_id = :id;");
            
            $stm->execute(array("id" => $trophyId));

            return $stm->fetchAll(PDO::FETCH_ASSOC);
        }
        
		/**
		*	Ziska vsechny ulovky daneho uzivatele
		*/
        public function getAllUsersTrophies($userId){
           $stm = self::$pdo->prepare(SELECT_TROPHY."where uz.user_id = :id order by d_chyceni desc;");
			
            $stm->execute(array('id' => $userId));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
			
			$n = count($rows);
            for($i = 0; $i < $n; $i++){
 
				$likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);
				
                $trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
        }
		
		/**
		*	Ziska vsechny ulovky v dane Mistni organizaci
		*/
		public function getAllMOTrophies($moId){
           $stm = self::$pdo->prepare(SELECT_TROPHY."where r.mo_id = :id order by d_chyceni desc;");
            
            $stm->execute(array('id' => $moId));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            
			$trophies = array();
            
			$n = count($rows);
			for($i = 0; $i < $n; $i++){				
				$likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);
				
                $trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
        }
		
		/**
		*	Vrati vsechny ulovky z daneho reviru
		*/
		public function getAllFishgroundTrophies($fsgId, $sub){
           $stm = self::$pdo->prepare(SELECT_TROPHY."where r.revir_id = :id and r.podrevir = :sub order by d_chyceni desc;");

            $stm->execute(array('id' => $fsgId, 'sub' => $sub));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            
			$n = count($rows);
			for($i = 0; $i < $n; $i++){
                
               	$likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);
				
                $trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
        }
		
		
		/**
		*	Vrati nejvetsi ulovek z dane MO
		*/
		public function getMOBiggestTrophy($moId){
			$stm = self::$pdo->prepare(SELECT_TROPHY."where r.mo_id = :id order by koef desc limit 1;");
			
            $stm->execute(array('id' => $moId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            
			$likeCount = $this->getLikeCount($row['ulovek_id']);
			
			return $this->getTrophyFromRow($row, 0, $likeCount);
		}
        
		/**
		*	Vrati nejvetsi ulovek daneho uzivatele
		*/
        public function getUsersBiggestTrophy($userId){
            $stm = self::$pdo->prepare(SELECT_TROPHY."where ul.user_id = :id order by koef desc limit 1;");

            $stm->execute(array('id' => $userId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
			
			$likeCount = $this->getLikeCount($row['ulovek_id']);
			
			return $this->getTrophyFromRow($row, 0, $likeCount);
		}
        
		/**
		*	Ziska nejnovesi ulovky
		*/
        public function getRecentTrophies(){
            
            $stm = self::$pdo->prepare(SELECT_TROPHY."order by d_post desc limit 3;");
            
            $stm->execute();
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			$trophies = array();
            $n = count($rows);
			for($i = 0; $i < $n; $i++){
				$likeCount = $this->getLikeCount($rows[$i]['ulovek_id']);

				$trophies[$i] = $this->getTrophyFromRow($rows[$i], $i, $likeCount);
            }
            
            return $trophies;
        }
		
		/**
		*	Ziska pocet liku daneho ulovku
		*/
		public function getLikeCount($trophyId){
			$this->stmLike->execute(array('id' => $trophyId));
            $like = $this->stmLike->fetch(PDO::FETCH_ASSOC);
                
			if($like['likeCount'] == null){
				$like['likeCount'] = 0;
			}
			
			return $like['likeCount'];
		}
		
		/**
		*	Vytvori pole z radku v tabulce
		*/
		private function getTrophyFromRow($row, $index, $likeCount){
			if($row == null){
				return null;
			}
			
			return array("id" => $row['ulovek_id'], "index" => $index, "rod" => $row['nazev_rod'],
						 "druh" =>$row['nazev_druh'], "velikost" => $row['velikost'],
						 "img" => $row['photo_url'], "lovec" => $row['nickname'],
						 "datum" => $row['datum'], "vaha" => $row['vaha'],
						 "revir"=>$row['nazev_revir'], "koef" => $row['koef'],
						 "like" => $likeCount, "lovecId" => $row['user_id'],
						 "revirId" => $row['revir_id']."-".$row['podrevir']);
		}
		
		
		/******************************************* CRUD nad tabulkou druhy ***********************************/
		
		/**
		*	Ziska vsechny druhy ryb v databazi
		*/
		public function getAllDruhy(){
			$stm = self::$pdo->prepare("select * from trophy.druh");
			$stm->execute();
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            
            for($i = 0; $i < count($rows); $i++){
                $druhy[$i] = array('id' => $rows[$i]['druh_id'], 'nazev' => $rows[$i]['nazev_rod']. " " . $rows[$i]['nazev_druh']);
            }
            
            return $druhy;
		}
		
		public function getAllSpeciesData(){
			$stm = self::$pdo->prepare("select * from trophy.druh");
			
			$stm->execute();
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			$n = count($rows);
			
			$species = array();
			for($i = 0; $i < $n; $i++){
				$species[$i] = array("id" => $rows[$i]['druh_id'],
									 "rod" => $rows[$i]['nazev_rod'],
									 "druh" => $rows[$i]['nazev_druh'],
									 "vahaTrofej" => $rows[$i]['vaha_trofej'],
									 "velikostTrofej" => $rows[$i]['velikost_trofej']);
			}
			
			return $species;
		}
		
		public function editSpecie($id, $rod, $druh, $vaha, $velikost){
			$stm = self::$pdo->prepare("update trophy.druh 
				set nazev_rod = :rod, nazev_druh = :druh, vaha_trofej = :vaha, velikost_trofej = :velikost
				where druh_id = :id");
			
			$updateOk = $stm->execute(array("id" => $id, "rod" => $rod, "druh" => $druh, "vaha" => $vaha, "velikost" => $velikost));
			return $updateOk;
		}
		
		public function createSpecie($rod, $druh, $vaha, $velikost){
			$stm = self::$pdo->prepare("insert into trophy.druh
				(nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
				values(:rod, :druh, :vaha, :velikost)");
			
			$insertOk = $stm->execute(array("rod" => $rod, "druh" => $druh, "vaha" => $vaha, "velikost" => $velikost));
			return $insertOk;
		}
		
		public function deleteSpecie($id){
			$stm = self::$pdo->prepare("delete from trophy.druh where druh_id = :id");
			
			$deleteOk = $stm->execute(array("id" => $id));
			return $deleteOk;
		}
    }
?>