<?php

namespace Eoko\ODM\Fixture\Fixture;

use Eoko\ODM\DocumentManager\Repository\DocumentManager;
use Eoko\ODM\DocumentManager\Repository\DocumentRepository;

class EntityFixture implements FixtureInterface {

    protected $entity;

    protected $priority = 1;

    function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function create(DocumentManager $dm)
    {
        return $dm->getRepository(get_class($this->entity))->add($this->entity);
    }

    public function delete(DocumentManager $dm)
    {
        return $dm->getRepository(get_class($this->entity))->delete($this->entity);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}