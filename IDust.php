<?php
namespace Twister;
interface IDust
{
    public function getId();
    public function setData($data);
    public function setCollection(Collection $twister);
    public function getData();
    public function getCollection();
}
