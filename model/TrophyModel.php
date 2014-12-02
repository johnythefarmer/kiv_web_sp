<?php
    class TrophyModel extends Model{
		
	    /* 
		*  Metoda slouzici k nahrani ulovku na server
		*/ 
		public function post($lovec, $druh, $velikost, $vaha, $datum, $revir, $img){
			$stm = self::$pdo->prepare("insert into trophy.ulovek
									(user_id, druh_id,velikost,vaha,d_chyceni,revir_id, photo_url)
									values(:lovec, :druh, :velikost, :vaha, :datum, :revir, :img)");
			
			 $postOk = $stm->execute(array('lovec' => $lovec, 'druh' => $druh, 'velikost' => $velikost,
										   'vaha' => $vaha, 'datum' => $datum, 'revir' => $revir, 'img' => $img));
			if($postOk){
				return $this->getLastInsertId();
			}else{
				return 0;
			}
			
		}
		
		public function getTop($duration){
			switch($duration){
				case 'day': $stm = self::$pdo->prepare("select * from trophy.topday");break;
				case 'week': $stm = self::$pdo->prepare("select * from trophy.topweek");break;
				case 'month': $stm = self::$pdo->prepare("select * from trophy.topmonth");break;
				case 'year': $stm = self::$pdo->prepare("select * from trophy.topyear");break;
				default: return null;
			}
			
			$stm->execute();
			
			$stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount
											FROM trophy.uzivatel_likes_ulovek
											where ulovek_id = :id group by(ulovek_id);');
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            for($i = 0; $i < count($rows); $i++){
                
                $stmLike->execute(array('id' => $rows[$i]['ulovek_id']));
				
                $like = $stmLike->fetch(PDO::FETCH_ASSOC);
				
				if($like['likeCount'] == null){
					$like['likeCount'] = 0;
				}
				
				
                $trophies[$i] = array("id" => $rows[$i]['ulovek_id'],"index"=> $i, "rod" => $rows[$i]['nazev_rod'], "druh" => $rows[$i]['nazev_druh'],
                                     "velikost" => $rows[$i]['velikost'], "img" => $rows[$i]['photo_url'], "lovec" => $rows[$i]['nickname'],
                                     "datum" => $rows[$i]['datum'], "vaha" => $rows[$i]['vaha'], "revir" => $rows[$i]['nazev_revir'],
                                      "koef" => $rows[$i]['koef'], "like" => $like['likeCount'],"lovecId" => $rows[$i]['user_id'],
									  "revirId" => $rows[$i]['revir_id']);
            }
            
            return $trophies;
		}
		
		public function getAllDruhy(){
			$stm = self::$pdo->prepare("select * from trophy.druh");
			$stm->execute();
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            
            for($i = 0; $i < count($rows); $i++){
                $druhy[$i] = array('id' => $rows[$i]['druh_id'], 'nazev' => $rows[$i]['nazev_rod']. " " . $rows[$i]['nazev_druh']);
            }
            
            return $druhy;
		}
		
		public function getMostRecentTrophyId(){
			$stmId = self::$pdo->prepare("select ul.ulovek_id as last 
										from trophy.ulovek ul 
										order by ulovek_id desc
										limit 1");
			$stmId->execute();
			return $stmId->fetch(PDO::FETCH_ASSOC)['last'];
		}
		
		private function getLastInsertId(){
			$stmId = self::$pdo->prepare("select last_insert_id() as last");
			$stmId->execute();
			return $stmId->fetch(PDO::FETCH_ASSOC)['last'];
		}
		
        public function getTrophyById($trophyId){
             $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
										 ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										 ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										 d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										 uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where ul.ulovek_id = :id;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount
											FROM trophy.uzivatel_likes_ulovek
											where ulovek_id = :id
											group by(ulovek_id);');
            
            $stm->execute(array('id' => $trophyId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            $stmLike->execute(array('id' => $row['ulovek_id']));
            $like = $stmLike->fetch(PDO::FETCH_ASSOC);
            if($row == null){
                return null;
            }
            
			if($like['likeCount'] == null){
				$like['likeCount'] = 0;
			}
            
            
            return array("id" => $row['ulovek_id'], "index"=> 0, "rod"=>$row['nazev_rod'], "druh"=>$row['nazev_druh'],
                         "velikost"=>$row['velikost'], "img"=>$row['photo_url'],
                         "lovec"=>$row['nickname'], "datum"=>$row['datum'],
                         "vaha"=>$row['vaha'], "revir"=>$row['nazev_revir'],
                         "koef"=>$row['koef'],"like"=>$like['likeCount'],
                         "lovecId" => $row['user_id'], "revirId" => $row['revir_id']);
        }
        
        public function getAllLikes($trophyId){
            $stm = self::$pdo->prepare("SELECT uz.nickname, date_format(ulu.d_like, '%e. %c. %Y %T')
										as time_like FROM trophy.uzivatel_likes_ulovek ulu
										inner join trophy.uzivatel uz on ulu.user_id = uz.user_id 
										where ulu.ulovek_id = :id;");
            
            $stm->execute(array("id" => $trophyId));

            return $stm->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function getAllUsersTrophies($userId){
           $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
									   ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where uz.user_id = :id
										order by d_chyceni desc;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount FROM trophy.uzivatel_likes_ulovek where ulovek_id = :id;');
            
            $stm->execute(array('id' => $userId));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            for($i = 0; $i < count($rows); $i++){
                
                $stmLike->execute(array('id' => $rows[$i]['ulovek_id']));
                $like = $stmLike->fetch(PDO::FETCH_ASSOC);
                
				if($like['likeCount'] == null){
					$like['likeCount'] = 0;
				}
				
                $trophies[$i] = array("id" => $rows[$i]['ulovek_id'],"index"=> $i, "rod"=>$rows[$i]['nazev_rod'], "druh"=>$rows[$i]['nazev_druh'],
                                     "velikost"=>$rows[$i]['velikost'], "img"=>$rows[$i]['photo_url'], "lovec"=>$rows[$i]['nickname'],
                                     "datum"=>$rows[$i]['datum'], "vaha"=>$rows[$i]['vaha'], "revir"=>$rows[$i]['nazev_revir'],
                                      "koef"=>$rows[$i]['koef'], "like"=>$like['likeCount'],"lovecId" => $rows[$i]['user_id'],
									  "revirId" => $rows[$i]['revir_id']);
            }
            
            return $trophies;
        }
		
		public function getAllMOTrophies($moId){
           $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
									   ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where r.mo_id = :id
										order by d_chyceni desc;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount FROM trophy.uzivatel_likes_ulovek where ulovek_id = :id;');
            
            $stm->execute(array('id' => $moId));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            for($i = 0; $i < count($rows); $i++){
                
                $stmLike->execute(array('id' => $rows[$i]['ulovek_id']));
                $like = $stmLike->fetch(PDO::FETCH_ASSOC);
                
				if($like['likeCount'] == null){
					$like['likeCount'] = 0;
				}
				
                $trophies[$i] = array("id" => $rows[$i]['ulovek_id'],"index"=> $i, "rod"=>$rows[$i]['nazev_rod'], "druh"=>$rows[$i]['nazev_druh'],
                                     "velikost"=>$rows[$i]['velikost'], "img"=>$rows[$i]['photo_url'], "lovec"=>$rows[$i]['nickname'],
                                     "datum"=>$rows[$i]['datum'], "vaha"=>$rows[$i]['vaha'], "revir"=>$rows[$i]['nazev_revir'],
                                      "koef"=>$rows[$i]['koef'], "like"=>$like['likeCount'],"lovecId" => $rows[$i]['user_id'],
									 "revirId" => $rows[$i]['revir_id']);
            }
            
            return $trophies;
        }
		
		public function getAllFishgroundTrophies($fsgId){
           $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
									   ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where r.revir_id = :id
										order by d_chyceni desc;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount FROM trophy.uzivatel_likes_ulovek where ulovek_id = :id;');
            
            $stm->execute(array('id' => $fsgId));
            
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            $trophies = array();
            for($i = 0; $i < count($rows); $i++){
                
                $stmLike->execute(array('id' => $rows[$i]['ulovek_id']));
                $like = $stmLike->fetch(PDO::FETCH_ASSOC);
                
				if($like['likeCount'] == null){
					$like['likeCount'] = 0;
				}
				
                $trophies[$i] = array("id" => $rows[$i]['ulovek_id'],"index"=> $i, "rod"=>$rows[$i]['nazev_rod'], "druh"=>$rows[$i]['nazev_druh'],
                                     "velikost"=>$rows[$i]['velikost'], "img"=>$rows[$i]['photo_url'], "lovec"=>$rows[$i]['nickname'],
                                     "datum"=>$rows[$i]['datum'], "vaha"=>$rows[$i]['vaha'], "revir"=>$rows[$i]['nazev_revir'],
                                      "koef"=>$rows[$i]['koef'], "like"=>$like['likeCount'],"lovecId" => $rows[$i]['user_id'],
									  "revirId" => $rows[$i]['revir_id']);
            }
            
            return $trophies;
        }
		
		public function getMOBiggestTrophy($moId){
			$stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
										ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id,, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where r.mo_id = :id order by koef desc limit 1;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount
			FROM trophy.uzivatel_likes_ulovek where ulovek_id = :id;');
            
            $stm->execute(array('id' => $moId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            $stmLike->execute(array('id' => $row['ulovek_id']));
			 
            $like = $stmLike->fetch(PDO::FETCH_ASSOC);
            if($row == null){
                return null;
            }
            
			if($like['likeCount'] == null){
				$like['likeCount'] = 0;
			}
            
            
            return array("id" => $row['ulovek_id'], "index"=> 0, "rod"=>$row['nazev_rod'], "druh"=>$row['nazev_druh'],
                         "velikost"=>$row['velikost'], "img"=>$row['photo_url'],
                         "lovec"=>$row['nickname'], "datum"=>$row['datum'],
                         "vaha"=>$row['vaha'], "revir"=>$row['nazev_revir'],
                         "koef"=>$row['koef'],"like"=>$like['likeCount'],
                         "lovecId" => $row['user_id'], "revirId" => $row['revir_id']);
		}
        
        public function getUsersBiggestTrophy($userId){
            $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
										ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										where ul.user_id = :id order by koef desc limit 1;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount
			FROM trophy.uzivatel_likes_ulovek where ulovek_id = :id;');
            
            $stm->execute(array('id' => $userId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
            $stmLike->execute(array('id' => $row['ulovek_id']));
			 
            $like = $stmLike->fetch(PDO::FETCH_ASSOC);
            if($row == null){
                return null;
            }
            
			if($like['likeCount'] == null){
				$like['likeCount'] = 0;
			}
            
            
            return array("id" => $row['ulovek_id'], "index"=> 0, "rod"=>$row['nazev_rod'], "druh"=>$row['nazev_druh'],
                         "velikost"=>$row['velikost'], "img"=>$row['photo_url'],
                         "lovec"=>$row['nickname'], "datum"=>$row['datum'],
                         "vaha"=>$row['vaha'], "revir"=>$row['nazev_revir'],
                         "koef"=>$row['koef'],"like"=>$like['likeCount'],
                         "lovecId" => $row['user_id'], "revirId" => $row['revir_id']);
        }
        
        public function getRecentTrophies(){
            
            $stm = self::$pdo->prepare("SELECT ul.ulovek_id, ul.velikost, ul.vaha,
										ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										uz.nickname, uz.user_id, r.revir_id
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id
										order by d_post desc;");
            
            $stmLike = self::$pdo->prepare('SELECT count(ulovek_id) as likeCount
											FROM trophy.uzivatel_likes_ulovek 
											where ulovek_id = :id;');
            
            $stm->execute();
            
            for($i = 0; $i < 3; $i++){
                $row = $stm->fetch(PDO::FETCH_ASSOC);
                $stmLike->execute(array('id' => $row['ulovek_id']));
                $like = $stmLike->fetch(PDO::FETCH_ASSOC);
				
				if($like['likeCount'] == null){
					$like['likeCount'] = 0;
				}
                
                $trophies[$i] = array("id" => $row['ulovek_id'], "index"=> $i, "rod"=>$row['nazev_rod'], "druh"=>$row['nazev_druh'],
                                     "velikost"=>$row['velikost'], "img"=>$row['photo_url'], "lovec"=>$row['nickname'],
                                     "datum"=>$row['datum'], "vaha"=>$row['vaha'], "revir"=>$row['nazev_revir'],"koef"=>$row['koef'],
                                     "like"=>$like['likeCount'],"lovecId" => $row['user_id'], "revirId" => $row['revir_id']);
            }
            
            return $trophies;
        }
    }
?>