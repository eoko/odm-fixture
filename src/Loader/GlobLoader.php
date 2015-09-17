<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 08/09/15
 * Time: 11:44
 */

namespace Eoko\ODM\Fixture\Loader;


use Doctrine\Common\Collections\ArrayCollection;
use Zend\Stdlib\Hydrator\ClassMethods;

class GlobLoader extends AbstractLoader {

    protected $path;
    protected $mapping;

    function __construct($config)
    {

        $type = $config['type'];

        foreach(glob($config['options']['glob']) as $filename) {
            $namespace = (isset($config['options']['mapping'][basename($filename)])) ? $config['options']['mapping'][basename($filename)] : basename($filename) ;

            $data = json_decode(file_get_contents($filename), true);
            $hydrator = new ClassMethods();

            foreach((array) $data as $value) {
                $entity = $hydrator->hydrate($value, new $namespace());
                $this->fixtures[] = new $type($entity);
            }

        }

        parent::__construct($config);
    }

}