<?php
	define("SELECT_TROPHY", "SELECT ul.ulovek_id, ul.velikost, ul.vaha,
										 ul.photo_url, date_format(ul.d_chyceni, '%e. %c. %Y') as datum,
										 ((ul.vaha/d.vaha_trofej) + (ul.velikost/d.velikost_trofej))/2 as koef,
										 d.nazev_rod, d.nazev_druh, concat(reka.nazev,' ', r.cislo) as nazev_revir,
										 uz.nickname, uz.user_id, ul.revir_id, ul.podrevir
										FROM trophy.ulovek ul inner join trophy.revir r on ul.revir_id = r.revir_id
										inner join uzivatel uz on ul.user_id = uz.user_id
										inner join druh d on ul.druh_id = d.druh_id
										inner join reka on r.reka_id = reka.reka_id ");

	define("DB_SERVER","localhost");
	define("DB_USER","root");
	define("DB_PASSWORD", "");
	define("DB_NAME", "trophy");
?>