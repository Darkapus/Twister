MongoReader
===========


### create connections

    $db = new \Twister\Connection('127.0.0.1','test');
    $files = new \Twister\Collection($db,'files'); // connect to files collection
    $users = new \Twister\Collection($db,'users'); // connect to users collection

###create

    $u1 = $users->getEmptyDocument(); 
    $u1->firstname = 'benjamin';
    $u1->lastname = 'baschet';
    $users->insert($u1); // insert


###update

    $u1 = $users->findOne();
    $u1->setfirstname('benjamine');
    $users->save($u1); // save


### duplicate

    $u1 = $users->findOne();
    $u2 clone $u1;
    $users->insert($u2); // double
