<?php

interface IDust
{
    public function getId();
    public function setData($data);
    public function setTwister(Twister $twister);
    public function getData();
    public function getTwister();
}
