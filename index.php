<?php
include('Exception.php');
include('IDust.php');
include('Object.php');
include('Connection.php');
include('Collection.php');
include('Cursor.php');
include('Document.php');

// create connections
$files = new \Twister\Collection(new \Twister\Connection('127.0.0.1','test'),'files');
$users = new \Twister\Collection(new \Twister\Connection('127.0.0.1','test'),'users');


//create
//$u1 = $users->getDust(); 
// or $user->getDust(array('firstname'=>'toto', 'lastname'=>'titi'));
//$u1->setfirstname('benjamin');
//$u1->setlastname('baschet');
//$u1->insert(); // insert

$u1 = $users->find();

foreach($u1 as $u){
	var_dump($u);
}
/*
$u2 = $users->getEmptyDocument();

$u2->firstname = 'toto';

$users->insert($u2);

$u2->firstname = 'botox';

$users->save($u2);

$users->delete($u2);*/

$f1 = $files->findOne();

//var_dump($files->findOne()->user);
// create a file with link to user
//$f1 = $files->getDust();// or $f1 = $files->getDust(array('path'=>'/var/www/myface.png', 'name'=>'myface', 'user'=>$u1->getId()));
//$f1->setpath('/var/www/myface.png')->setname('mtface')->setuser($u1)->insert();

//var_dump($files->findOne()->getuser());

// show data
//var_dump($files->findOne()->getuser()); // search and take user relation

