<?php
    class TrophyModel extends Model{
        public function getAllUsersTrophies($userId){
            $trophies[0] = array("index"=>"1", "rod"=>"Štika", "druh"=>"obecná", "velikost"=>"89", "img"=>"stika.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => $userId);
            $trophies[1] = array("index"=>"1", "rod"=>"Candát", "druh"=>"obecný", "velikost"=>"89", "img"=>"candat.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => $userId);
            $trophies[2] = array("index"=>"1", "rod"=>"Štika", "druh"=>"obecná", "velikost"=>"89", "img"=>"stika.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => $userId);
            return $trophies;
        }
        
        public function getUsersBiggestTrophy($userId){
            return array("index"=>"1", "rod"=>"Štika", "druh"=>"obecná", "velikost"=>"89", "img"=>"stika.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => $userId);
        }
        
        public function getRecentTrophies(){
            //TODO tady udelat databazovy cteni
            $trophies[0] = array("index"=>"1", "rod"=>"Štika", "druh"=>"obecná", "velikost"=>"89", "img"=>"stika.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => "123");
            $trophies[1] = array("index"=>"1", "rod"=>"Candát", "druh"=>"obecný", "velikost"=>"89", "img"=>"candat.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => "123");
            $trophies[2] = array("index"=>"1", "rod"=>"Štika", "druh"=>"obecná", "velikost"=>"89", "img"=>"stika.jpg", "lovec"=>"Johnny", "datum"=>"19.6.2014", "vaha"=>"9.5", "revir"=>"Olsovka 12","koef"=>"0.987","like"=>"13","lovecId" => "123");
            return $trophies;
        }
    }
?>