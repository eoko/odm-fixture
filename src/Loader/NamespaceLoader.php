<?php

namespace Eoko\ODM\Fixture\Loader;


use Doctrine\Common\Collections\ArrayCollection;
use Eoko\ODM\Fixture\CollectionFixture;

class NamespaceLoader extends AbstractLoader {

    protected $fixtures = [];

    function __construct($config)
    {
        $type = $config['type'];

        foreach($config['options']['namespaces'] as $namespace) {
            $this->fixtures[] = new $type(new $namespace());
        }

        parent::__construct($config);
    }

}