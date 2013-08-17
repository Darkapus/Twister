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
$conn = new TwisterConnection('127.0.0.1','test');
$users = new Twister($conn,'user');

$u1 = new TwisterDust(array('firstname'=>'benjamin', 'lastname'=>'baschet'));
$users->insert($u1);



$val->insert();
//$val->setnom('baschet');
//$val->save();
// insert un doublon
