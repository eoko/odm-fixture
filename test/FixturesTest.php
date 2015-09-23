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

class FixturesTest extends \PHPUnit_Framework_TestCase
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

        $fixtures = $exectuor->getFixtures();

        while($fixtures) {
            $fixture = array_pop($fixtures);
            $this->assertEquals(1, $exectuor->load($fixture));
            $this->assertEquals(1, $exectuor->unload($fixture));
        }

        $this->assertTrue($exectuor->execute());
        $this->assertTrue($exectuor->purge());

    }
}
