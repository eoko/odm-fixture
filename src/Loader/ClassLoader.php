<?php

namespace Eoko\ODM\Fixture\Loader;

use Doctrine\Common\Collections\ArrayCollection;
use Eoko\ODM\Fixture\CollectionFixture;

class ClassLoader extends AbstractLoader {

    function __construct($config)
    {
        parent::__construct($config);

        foreach((array)$config['options']['classname'] as $classname) {
            $class = new $classname();
            $this->fixtures = array_merge($this->fixtures, $class->load()->toArray());
        }

    }

}