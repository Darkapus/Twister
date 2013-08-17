<?php
include('TwisterObject.php');
include('TwisterConnection.php');
include('Twister.php');
include('TwisterBag.php');
include('TwisterDust.php');


//$conn->create('fichier');

//$twist = new Twister($conn, 'test');
//$dust = new TwisterDust(array('prenom'=>'benjamin', 'nom'=>'baschet'));
//$twist->insert($dust);

$files = new Twister(new TwisterConnection('127.0.0.1','test'),'files');
$users = new Twister(new TwisterConnection('127.0.0.1','test'),'users');
$files->addRelation('user', $users, '_id');
/*     
$u1 = $users->getDust(array('firstname'=>'benjamin', 'lastname'=>'baschet'));
$u1->insert(); // insert
$u1->setfirstname('benjamine');
$u1->save(); // save
$u1->setfirstname('benjamin');
$u1->insert(); // double

$f1 = $files->getDust(array('path'=>'/var/www/myface.png', 'name'=>'myface', 'user'=>$u1->getId()));
$f1->insert();
*/
var_dump($files->findOne()->getuser()); // search and take user relation

