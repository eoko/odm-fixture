<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 08/09/15
 * Time: 13:39
 */

namespace Eoko\ODM\Fixture\Loader;


use Eoko\ODM\Fixture\CollectionFixture;

abstract class AbstractLoader {

    protected $config;

    protected $fixtures = [];

    function __construct($config)
    {
        $this->config = $config;
    }

    public function getCollection()
    {
        return $this->fixtures;
    }


}