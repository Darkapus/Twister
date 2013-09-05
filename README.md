MongoReader
===========
<code>
// create connections
$files = new Twister(new TwisterConnection('127.0.0.1','test'),'files');
$users = new Twister(new TwisterConnection('127.0.0.1','test'),'users');

// add relational link
$files->addRelation('user', $users, '_id');

//create
$u1 = $users->getDust(); 
// or $user->getDust(array('firstname'=>'benjamin', 'lastname'=>'baschet'));
$u1->setfirstname('benjamin');
$u1->setlastname('baschet');
$u1->insert(); // insert

//update
$u1->setfirstname('benjamine');
$u1->save(); // save

// duplicate
$u1->setfirstname('benjamin');
$u1->insert(); // double

// create a file with link to user
$f1 = $files->getDust();// or $f1 = $files->getDust(array('path'=>'/var/www/myface.png', 'name'=>'myface', 'user'=>$u1->getId()));
$f1->setpath('/var/www/myface.png')->setname('mtface')->setuser($u1)->insert();

// show data
var_dump($files->findOne()->getuser()); // search and take user relation

</code>
