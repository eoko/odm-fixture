<?php

namespace Eoko\ODM\Fixture\Fixture;

use Eoko\ODM\DocumentManager\Repository\DocumentManager;

interface FixtureInterface {

    function __construct($entity);
    public function getPriority();
    public function create(DocumentManager $documentManager);
    public function delete(DocumentManager $documentManager);
    public function getEntity();

}