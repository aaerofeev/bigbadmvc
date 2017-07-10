<?php

namespace Framework\Model;


class Model
{
    private $storage;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::getInstance();
    }

    /**
     * @return Storage|null
     */
    public function getStorage()
    {
        return $this->storage;
    }
}