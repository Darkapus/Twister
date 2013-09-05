<?php

interface IDust
{
    public function setData($data);
    public function setTwister(Twister $twister);
    public function getData();
    public function getTwister();
}