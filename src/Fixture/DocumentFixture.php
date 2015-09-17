<?php

namespace Eoko\ODM\Fixture\Fixture;

use Eoko\ODM\DocumentManager\Repository\DocumentManager;
use Eoko\ODM\DocumentManager\Repository\DocumentRepository;

class DocumentFixture implements FixtureInterface {

    protected $entity;

    protected $priority = 100;

    function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function create(DocumentManager $documentManager)
    {
        return $documentManager->getRepository(get_class($this->entity))->createTable();
    }

    public function delete(DocumentManager $documentManager)
    {
        return $documentManager->getRepository(get_class($this->entity))->deleteTable();
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}