<?php

namespace Eoko\ODM\Fixture\Test\ClassLoader;

use Eoko\ODM\Fixture\CollectionFixture;
use Eoko\ODM\Fixture\Fixture\EntityFixture;
use Eoko\ODM\Fixture\Loader\ClassloaderInterface;
use Eoko\ODM\Fixture\Test\Entity\UserEntity;

class User implements ClassloaderInterface {

    public function load() {
        return new CollectionFixture($this->getEntities());
    }

    private function getEntities() {
        $user1 = new UserEntity();
        $user1->setUsername('merlin');
        $user1->setEmail('romain@eoko.fr');

        $user2 = new UserEntity();
        $user2->setUsername('rixo');
        $user2->setEmail('me@me.com');

        return [new EntityFixture($user1), new EntityFixture($user2)];
    }

}