<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 08/09/15
 * Time: 16:52
 */

namespace Eoko\ODM\Fixture\Factory;


use Eoko\ODM\DocumentManager\Repository\DocumentManager;
use Eoko\ODM\Fixture\Executor\Executor;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExecutorFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var DocumentManager $dm */
        $dm = $serviceLocator->get('Eoko\ODM\DocumentManager');

        $config = $serviceLocator->get('Config');
        $loaders = $config['eoko']['odm']['fixtures']['loaders'];

        $logger = null;

        if(isset($config['eoko']['odm']['fixtures']['logger'])) {
            if($serviceLocator->has($config['eoko']['odm']['fixtures']['logger'])) {
                $logger = $serviceLocator->get($config['eoko']['odm']['fixtures']['logger']);
            }
        }

        return new Executor($dm, $loaders, $logger);
    }

}