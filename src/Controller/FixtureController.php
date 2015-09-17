<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 08/09/15
 * Time: 16:38
 */

namespace Eoko\ODM\Fixture\Controller;

use Eoko\ODM\Fixture\CollectionFixture;
use Eoko\ODM\Fixture\Executor\Executor;
use Eoko\ODM\Fixture\Fixture\FixtureInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class FixtureController extends AbstractConsoleController {

    public function loadAction() {
        $one = $this->params('one', false);

        /** @var Executor $ex */
        $ex = $this->getServiceLocator()->get(Executor::class);

        if($one && class_exists($one)) {
            $one = new $one();
            if($one instanceof FixtureInterface || $one instanceof CollectionFixture) {
                while((array) $one) {
                    $fixture = array_pop($one);
                    if(!$ex->load($fixture)) {
                        array_push($one, $fixture);
                    }
                }
            }
        } else {
            $ex->execute();
        }
    }

    public function unloadAction() {
        $one = $this->params('one', false);

        /** @var Executor $ex */
        $ex = $this->getServiceLocator()->get(Executor::class);

        if($one && class_exists($one)) {
            $one = new $one();
            if($one instanceof FixtureInterface || $one instanceof CollectionFixture) {
                foreach((array) $one as $fixture) {
                    if(!$ex->unload($fixture)) {
                        array_push($one, $fixture);
                    }
                }
            }
        } else {
            $ex->purge();
        }
    }
}