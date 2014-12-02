<?php
    Class MOModel extends Model{
        public function __construct(){
            parent::__construct();
        }
		
        public function getAllMO(){
            $stm = self::$pdo->prepare("select * from trophy.mistni_organizace");
            $stm->execute();
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
            
            for($i = 0; $i < count($rows); $i++){
                $mo[$i] = array('id' => $rows[$i]['mo_id'], 'nazev' => $rows[$i]['nazev'],
								'img' => $rows[$i]['logo_url'], 'web' => $rows[$i]['website']);
            }
            
            return $mo;
        }
		
		public function getMOData($moId){
			$stm = self::$pdo->prepare("select * from trophy.mistni_organizace where mo_id = :id");
            $stm->execute(array('id' => $moId));
            $row = $stm->fetch(PDO::FETCH_ASSOC);
			
			if($row == null){
                return null;
            }
			
			return array('id' => $row['mo_id'], 'nazev' => $row['nazev'], 'img' => $row['logo_url'],
						 'web' => $row['website']);
		}		
		
		public function getFishgroundData($fgId){
			$stm = self::$pdo->prepare("select r.revir_id, re.nazev, r.cislo, r.podrevir
				from trophy.revir r inner join trophy.reka re on r.reka_id = re.reka_id
				where revir_id = :id;");
				$stm->execute(array('id' => $fgId));
			
				$row = $stm->fetch(PDO::FETCH_ASSOC);
			
			if($row == null){
				return null;
			}
			
			$nazev = $row['nazev'] . " " . $row['cislo'];
			if($row['podrevir'] != 0){
				$nazev .= " (". $row['podrevir'] . ")";
			}
            return array('id' => $row['revir_id'], 'nazev' => $nazev);
		}
		
		public function getAllFishgrounds($moId = false){
			if($moId == false){
				$stm = self::$pdo->prepare("select r.revir_id, re.nazev, r.cislo, r.podrevir
				from trophy.revir r inner join trophy.reka re on r.reka_id = re.reka_id;");
				$stm->execute();
			}else {
				$stm = self::$pdo->prepare("select r.revir_id, re.nazev, r.cislo, r.podrevir
				from trophy.revir r inner join trophy.reka re on r.reka_id = re.reka_id
				where r.mo_id = :id;");
				$stm->execute(array('id' => $moId));
			}
            $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
            $revir = null;
            for($i = 0; $i < count($rows); $i++){
				$nazev = $rows[$i]['nazev'] . " " . $rows[$i]['cislo'];
				if($rows[$i]['podrevir'] != 0){
					$nazev .= " (". $rows[$i]['podrevir'] . ")";
				}
                $revir[$i] = array('id' => $rows[$i]['revir_id'], 'nazev' => $nazev);
            }
            
            return $revir;
		}
    }
?>