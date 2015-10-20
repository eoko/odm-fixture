<?php

namespace Eoko\ODM\Fixture\Executor;

use Aws\Exception\AwsException;
use Doctrine\Common\Persistence\ObjectManager;
use Eoko\ODM\DocumentManager\Metadata\ClassMetadata;
use Eoko\ODM\DocumentManager\Repository\DocumentManager;
use Eoko\ODM\DocumentManager\Repository\DocumentRepository;
use Eoko\ODM\Fixture\CollectionFixture;
use Eoko\ODM\Fixture\Fixture\EntityFixture;
use Eoko\ODM\Fixture\Fixture\FixtureInterface;
use Eoko\ODM\Fixture\Fixture\SyncFixtureInterface;
use Eoko\ODM\Fixture\Loader\ClassLoader;
use Psr\Log\LoggerInterface;
use Zend\Log\Logger;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Abstract fixture executor.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class Executor
{
    /** Purger instance for purging database before loading data fixtures */
    protected $purger;

    /** @var  \Zend\Log\LoggerInterface */
    protected $logger;

    protected $fixtures;

    protected $repositories = [];

    public function __construct(DocumentManager $dm, $config, $logger = null)
    {
        if ($logger instanceof \Zend\Log\LoggerInterface) {
            $this->setLogger($logger);
        }

        $this->dm = $dm;
        $this->fixtures = [];

        foreach ($config as $key => $loader) {
            $type = isset($loader['loader']) ? $loader['loader'] : ClassLoader::class;
            $loader = new $type($config[$key]);

            foreach ($loader->getCollection() as $fixtures) {
                $this->fixtures[] = $fixtures;
            }
        }

        usort($this->fixtures, function ($a, $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }
            return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
        });
    }

    /**
     * Set the logger callable to execute with the log() method.
     *
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a message using the logger.
     *
     * @param string $message
     */
    public function log($message)
    {
        if (isset($this->logger)) {
            $this->logger->notice($message);
        }
    }

    /**
     * Purges the database before loading.
     */
    public function purge()
    {
        $fixtures = array_reverse($this->fixtures);
        $this->info('We have ' . count($fixtures) . ' fixture to unload');
        $repeat = [];
        while (count($fixtures) > 0) {
            $fixture = array_pop($fixtures);
            $this->debug(get_class($fixture) . ' >>> Unloading fixture `' . get_class($fixture->getEntity()));
            $result = $this->unload($fixture);

            if (!isset($repeat[get_class($fixture->getEntity())])) {
                $repeat[get_class($fixture->getEntity())] = 0;
            }

            switch ($result) {
                case 1:
                    $this->info('The fixture `' . get_class($fixture->getEntity()) . '` is unloaded.');
                    break;
                case 0:
                    if ($repeat[get_class($fixture->getEntity())]++ < 10) {
                        $this->warn('We cannot unload the fixture (retry ' . ($repeat[get_class($fixture->getEntity())]) . '/10)');
                        $fixtures[] = $fixture;
                        sleep((1 + $repeat[get_class($fixture->getEntity())]) * 2);
                    } else {
                        $this->error('We cannot unload the fixture, no more retry.');
                    }
                    break;
                default:
                    $this->error('Cannot unload this fixture : skip.');

            }
        }
        return true;
    }

    /**
     * Logs a message using the logger.
     *
     * @param string $message
     */
    public function info($message)
    {
        if (isset($this->logger)) {
            $this->logger->info($message);
        }
    }

    /**
     * Logs a message using the logger.
     *
     * @param string $message
     */
    public function debug($message)
    {
        if (isset($this->logger)) {
            $this->logger->debug($message);
        }
    }

    /**
     * @param FixtureInterface $fixture
     * @return boolean
     */
    public function unload(FixtureInterface $fixture)
    {
        try {
            $fixture->delete($this->dm);
            return 1;
        } catch (AwsException $e) {
            $this->error($e->getMessage());
            if ($e->getAwsErrorCode() === 'ResourceInUseException') {
                return 0;
            } elseif ($e->getAwsErrorCode() === 'LimitExceededException') {
                return 0;
             } else {
                return -1;
            }
        }
    }

    /**
     * Logs a message using the logger.
     *
     * @param string $message
     */
    public function error($message)
    {
        if (isset($this->logger)) {
            $this->logger->alert($message);
        }
    }

    /**
     * Logs a message using the logger.
     *
     * @param string $message
     */
    public function warn($message)
    {
        if (isset($this->logger)) {
            $this->logger->warn($message);
        }
    }

    /**
     * Executes the given array of data fixtures.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $fixtures = $this->fixtures;
        $this->info('We have ' . count($fixtures) . ' fixture to load');
        $repeat = [];
        while (count($fixtures) > 0) {
            $fixture = array_pop($fixtures);
            $this->debug(get_class($fixture) . ' >>> Loading fixture `' . get_class($fixture->getEntity()));
            $result = $this->load($fixture);

            if (!isset($repeat[get_class($fixture->getEntity())])) {
                $repeat[get_class($fixture->getEntity())] = 0;
            }

            switch ($result) {
                case 1:
                    $this->info('The fixture `' . get_class($fixture->getEntity()) . '` is loaded.');
                    break;
                case 0:
                    if ($repeat[get_class($fixture->getEntity())]++ < 10) {
                        $this->warn('We cannot load the fixture (retry ' . ($repeat[get_class($fixture->getEntity())]) . '/10)');
                        $fixtures[] = $fixture;
                        sleep((1 + $repeat[get_class($fixture->getEntity())]) * 2);
                    } else {
                        $this->error('We cannot load the fixture, no more retry.');
                    }
                    break;
                default:
                    $this->error('Cannot load this fixture : skip.');

            }
        }
        return true;
    }

    public function loadSync(SyncFixtureInterface $fixture) {

        $result = $this->load($fixture);
        if($result === 1) {
            while($fixture->checkCreate($this->dm)) {
                sleep(1);
            }
        }
        return $result;
    }

    public function unLoadSync(SyncFixtureInterface $fixture) {

        $result = $this->unload($fixture);
        if($result === 1) {
            while($fixture->checkDelete($this->dm)) {
                sleep(1);
            }
        }
        return $result;
    }

    /**
     * Load a fixture with the given persistence manager.
     *
     * @param FixtureInterface $fixture
     * @return boolean
     */
    public function load(FixtureInterface $fixture)
    {
        try {
            $fixture->create($this->dm);
            return 1;
        } catch (AwsException $e) {
            $this->error($e->getMessage());
            if ($e->getAwsErrorCode() === 'ResourceInUseException') {
                return 0;
            } elseif ($e->getAwsErrorCode() === 'ResourceNotFoundException' && $fixture instanceof EntityFixture) {
                return 0;
            } elseif ($e->getAwsErrorCode() === 'LimitExceededException') {
                return 0;
            } else {
                return -1;
            }
        }
    }

    /**
     * @return CollectionFixture
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }


}
