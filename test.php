<?php

include 'TwisterInclude.php';

class test extends TwisterObject implements TwisterInterface
{
    static private $test='bonjour';
    public function get()
    {
        return $this->test;
    }
    public function serialize()
    {
        return serialize($this);
    }
}
$tc = new TwisterConnection('127.0.0.1','test');
$tf = TwisterFactory::i()->setTwisterConnection($tc);
$dust = $tf->add(new test());


$o = $tf->findOneBy__id('test', $dust->getId());
$o = $dust->getunserialize();
        echo $o->get();


