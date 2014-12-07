<?php
    Class MOModel extends Model{
        public function __construct(){
            parent::__construct();
        }
		
		
		/******************************************** CRUD nad tabulkou mistni_organizace ***************/
        
		public function createMO($nazev, $web){
			$stm = self::$pdo->prepare("insert into trophy.mistni_organizace
				(nazev, website, logo_url) values (:nazev, :web, 'mo1.svg');");
			
			$insertOk = $stm->execute(array("nazev" => $nazev, "web" => $web));

			return $insertOk;
		}
		
		public function editMO($id, $nazev, $web, $img = false){
			if($img == false){
				$stm = self::$pdo->prepare("update trophy.mistni_organizace
					set nazev = :nazev, website = :web
					where mo_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "nazev" => $nazev, "web" => $web));
			} else {
				$stm = self::$pdo->prepare("update trophy.mistni_organizace
					set nazev = :nazev, website = :web, logo_url = :img
					where mo_id = :id");
				$updateOk = $stm->execute(array("id" => $id, "nazev" => $nazev, "web" => $web, "img" => $img));
			}
			
			
			return $updateOk;
		}
									  
		public function deleteMO($id){
			$stm = self::$pdo->prepare("delete from trophy.mistni_organizace where mo_id = :id");
			
			$deleteOk = $stm->execute(array("id" => $id));
//			var_dump($stm->errorInfo());
			return $deleteOk;
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
		
		
		/********************* CRUD nad tabulkou reviry *********************************************/
		
		
		public function getFishgroundData($fgId, $subfgId){
			$stm = self::$pdo->prepare("select r.revir_id, re.nazev, r.cislo, r.podrevir
				from trophy.revir r inner join trophy.reka re on r.reka_id = re.reka_id
				where revir_id = :id and podrevir = :sub;");
				$stm->execute(array('id' => $fgId, 'sub' => $subfgId));

				$row = $stm->fetch(PDO::FETCH_ASSOC);
			
			if($row == null){
				return null;
			}
			
			$nazev = $row['nazev'] . " " . $row['cislo'];
			if($row['podrevir'] != 0){
				$nazev .= " PR".$row['podrevir'];
			}
            return array('id' => $row['revir_id'], 'nazev' => $nazev);
		}
		
		public function deleteFishground($fgId, $subfg){
			$stm = self::$pdo->prepare("delete from trophy.revir where revir_id = :fgId and podrevir = :subfg");
			$deleteOk = $stm->execute(array("fgId" => $fgId, "subfg" => $subfg));
			
			return $deleteOk;
		}
		
		public function editFishground($fgId, $riverId, $name, $subfg, $moId){
			$stm = self::$pdo->prepare("update trophy.revir
				set reka_id=:riverId, cislo=:name, mo_id=:moId
				where revir_id = :fgId and podrevir = :subfg");
			
			$updateOk = $stm->execute(array("fgId" => $fgId, "riverId" => $riverId, "name" => $name, "subfg" => $subfg, "moId" => $moId));
			return $updateOk;
		}
		
		public function createFishground($fgId, $riverId, $name, $subfg, $moId){
			$stm = self::$pdo->prepare("insert into trophy.revir
				(revir_id, reka_id, cislo, podrevir, mo_id)
				values(:fgId, :riverId, :name, :subfg, :moId)");
			
			$insertOk = $stm->execute(array("fgId" => $fgId, "riverId" => $riverId, "name" => $name, "subfg" => $subfg, "moId" => $moId));
			return $insertOk;
		}
		
		
		
		public function getAllFishgroundsData(){
			$stm = self::$pdo->prepare("select r.*, mo.nazev as mo_nazev, reka.nazev as reka_nazev
				from trophy.revir r inner join trophy.mistni_organizace mo
				on r.mo_id = mo.mo_id
				inner join trophy.reka reka
				on r.reka_id = reka.reka_id;");
			
			$stm->execute();
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			$n = count($rows);
			
			$rivers = array();
			for($i = 0; $i < $n; $i++){
				$rivers[$i] = array("id" => $rows[$i]['revir_id'], 'rekaId' => $rows[$i]['reka_id'],
									"moId" => $rows[$i]['mo_id'], "cislo" => $rows[$i]['cislo'],
									"podrevir" => $rows[$i]['podrevir'], "mo" => $rows[$i]['mo_nazev'],
									"reka" => $rows[$i]['reka_nazev']);
			}
			
			return $rivers;
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
					$nazev .= " PR". $rows[$i]['podrevir'];
				}
                $revir[$i] = array('id' => $rows[$i]['revir_id'], 'fullId' => $rows[$i]['revir_id']."-".$rows[$i]['podrevir'], 'nazev' => $nazev);
            }
            
            return $revir;
		}
		
		
		/*********************************************CRUD nad tabulkou reky***************************************/
		
		
		public function getAllRiversData(){
			$stm = self::$pdo->prepare("select * from trophy.reka");
			
			$stm->execute();
			
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			
			$n = count($rows);
			
			$rivers = array();
			for($i = 0; $i < $n; $i++){
				$rivers[$i] = array("id" => $rows[$i]['reka_id'], "nazev" => $rows[$i]['nazev']);
			}
			
			return $rivers;
		}
		
		public function createRiver($name){
			$stm = self::$pdo->prepare("insert into trophy.reka (nazev) values(:name)");
			
			$insertOk = $stm->execute(array('name' => $name));
			return $insertOk;
		}
		
		public function editRiver($id, $name){
			$stm = self::$pdo->prepare("update trophy.reka set nazev = :name where reka_id = :id");
			
			$updateOk = $stm->execute(array('id' => $id, 'name' => $name));
			return $updateOk;
		}
		
		public function deleteRiver($id){
			$stm = self::$pdo->prepare("delete from trophy.reka where reka_id = :id");
			
			$deleteOk = $stm->execute(array('id' => $id));
			return $deleteOk;
		}
    }
?>