<?php

namespace Eoko\ODM\Fixture\Test;

use Eoko\ODM\DocumentManager\Repository\DocumentManager;
use Eoko\ODM\DocumentManager\Repository\DocumentRepository;
use Eoko\ODM\Metadata\Annotation\AnnotationDriver;
use Eoko\ODM\Fixture\CollectionFixture;
use Eoko\ODM\Fixture\Executor\Executor;
use Eoko\ODM\Fixture\Fixture\DocumentFixture;
use Eoko\ODM\Fixture\Fixture\EntityFixture;
use Eoko\ODM\Fixture\Fixtures;
use Eoko\ODM\Fixture\Loader\ClassLoader;
use Eoko\ODM\Fixture\Loader\FileLoader;
use Eoko\ODM\Fixture\Loader\GlobLoader;
use Eoko\ODM\Fixture\Loader\NamespaceLoader;
use Eoko\ODM\DocumentManager\Factory\DocumentManagerFactory;
use Eoko\ODM\Fixture\Test\ClassLoader\User;
use Eoko\ODM\Fixture\Test\Entity\UserEntity;
use Zend\ServiceManager\ServiceManager;

class TestFixtures extends \PHPUnit_Framework_TestCase
{


    public function testLoad()
    {
        $config = [
            [
                'loader' => NamespaceLoader::class,
                'type' => DocumentFixture::class,
                'options' => [
                    'namespaces' => [
                        UserEntity::class,
                    ],
                ],
            ],
            [
                'loader' => GlobLoader::class,
                'type' => EntityFixture::class,
                'options' => [
                    'glob' => __DIR__ . '/json/*.json',
                    'mapping' => [
                        'user.json' => UserEntity::class,
                    ],
                ],
            ],
            [
                'loader' => ClassLoader::class,
                'options' => [
                    'classname' => User::class
                ],
            ],
        ];

        $dm = \Mockery::mock(DocumentManager::class);
        $repository = \Mockery::mock(DocumentRepository::class);

        $repository->shouldReceive('createTable')->andReturn(true);
        $repository->shouldReceive('deleteTable')->andReturn(true);
        $repository->shouldReceive('add')->andReturn(true);
        $repository->shouldReceive('delete')->andReturn(true);

        $dm->shouldReceive('getRepository')->withArgs([UserEntity::class])->andReturn($repository);

        $exectuor = new Executor($dm, $config);

        $this->assertTrue($exectuor->load($exectuor->getFixtures()->first()));
        $this->assertTrue($exectuor->unload($exectuor->getFixtures()->first()));

        $this->assertTrue($exectuor->execute());
        $this->assertTrue($exectuor->purge());

    }
}
