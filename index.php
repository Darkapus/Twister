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
$twist = new Twister($conn,'fichier');
$val = $twist->findOneBynom('Kungel');

$val->insert();
//$val->setnom('baschet');
//$val->save();
// insert un doublon
