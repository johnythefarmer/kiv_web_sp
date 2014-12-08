insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Stod','http://mostod.cz','mo1.svg');
insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Sušice','http://mosusice.cz','mo2.svg');
insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Klatovy','http://moklatovy.cz','mo3.svg');
insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Plzeň','http://moplzen.cz','mo4.svg');
insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Štěnovice','http://mostenovice.cz','mo5.svg');
insert into trophy.mistni_organizace(nazev,website,logo_url) values('MO Rokycany','http://morokycany.cz','mo6.svg');


insert into trophy.reka (nazev) values('Radbuza');
insert into trophy.reka (nazev) values('Mže');
insert into trophy.reka (nazev) values('Úhlava');
insert into trophy.reka (nazev) values('Klabavka');
insert into trophy.reka (nazev) values('Otava');

insert into trophy.revir (revir_id, mo_id, cislo, podrevir, reka_id) values(431046,1,'3A',0,1);
insert into trophy.revir (revir_id, mo_id, cislo, podrevir, reka_id) values(431028,4,'4',0,2);
insert into trophy.revir (revir_id, mo_id, cislo, podrevir, reka_id) values(433034,3,'8A',0,5);
insert into trophy.revir (revir_id, mo_id, cislo, podrevir, reka_id) values(433074,2,'5B',0,2);
insert into trophy.revir (revir_id, mo_id, cislo, podrevir, reka_id) values(431014,6,'3A',1,4);
 
insert into trophy.uzivatel 
(nickname, password,email,jmeno,prijmeni,photo_url,web,is_admin,d_registrace,mo_id)
values(
'Admin',
'2e463de3a23c0d6e33e67313cfc9b5e6',
'jtf@google.com',
'Jan',
'Novak',
'user1.jpg',
'http://jtf.cz',
true,
now(),
1
);

insert into trophy.uzivatel 
(nickname, password,email,jmeno,prijmeni,photo_url,is_admin,web,d_registrace,mo_id)
values(
'User',
'b4092b13f4eb776ee958408bd8d23272',
'devil@hell.com',
'Lucifer',
'Cert',
'user2.jpg',
false,
'https://hell.hll',
now(),
1
);

insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Kapr','Obecný', 20,100);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Candát','Obecný', 8, 80);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Štika','Obecná', 10,100);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Amur','Bílý', 10,100);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Sumec','Velký', 30,180);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Pstruh','Obecný potoční', 2,60);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Pstruh','Duhový', 3, 65);
insert into trophy.druh (nazev_rod, nazev_druh, vaha_trofej, velikost_trofej)
values('Ostatní','', 2,50);

insert into trophy.ulovek (d_chyceni, velikost, vaha, photo_url, druh_id, revir_id, podrevir, user_id, d_post)
values(date('2009-08-12'),118, 15, 'ulovek1.jpg', 3, 431028, 0, 1, now());
insert into trophy.ulovek (d_chyceni, velikost, vaha, photo_url, druh_id, revir_id, podrevir, user_id, d_post)
values(date('2012-09-30'),75, 5, 'ulovek2.jpg', 2, 431028, 0, 2, now());
insert into trophy.ulovek (d_chyceni, velikost, vaha, photo_url, druh_id, revir_id, podrevir, user_id, d_post)
values(date('2010-08-27'),110, 10, 'ulovek3.jpg', 3, 431046, 0, 1, now());
insert into trophy.ulovek (d_chyceni, velikost, vaha, photo_url, druh_id, revir_id, podrevir, user_id, d_post)
values(date('2009-07-18'),60, 3, 'ulovek4.jpg', 2, 431046, 0, 2, now());
insert into trophy.ulovek (d_chyceni, velikost, vaha, photo_url, druh_id, revir_id, podrevir, user_id, d_post)
values(date('2011-12-20'),90, 9, 'ulovek5.jpg', 3, 431014, 1, 2, now());

insert into trophy.uzivatel_likes_ulovek (user_id, ulovek_id, d_like) values(1,2, date('2011-12-20'));
insert into trophy.uzivatel_likes_ulovek (user_id, ulovek_id, d_like) values(2,2, date('2011-12-21'));
insert into trophy.uzivatel_likes_ulovek (user_id, ulovek_id, d_like) values(1,3, date('2011-12-22'));
insert into trophy.uzivatel_likes_ulovek (user_id, ulovek_id, d_like) values(2,3, date('2011-12-23'));