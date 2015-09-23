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
use Eoko\ODM\Fixture\Loader\ClassloaderInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class FixtureController extends AbstractConsoleController
{
    private function generic( $one, $action = 'load') {

        $oneAction = ($action === 'unload') ? 'unload' : 'load';
        $executorAction = ($action === 'unload') ? 'purge' : 'execute';

        /** @var Executor $ex */
        $ex = $this->getServiceLocator()->get(Executor::class);

        if ($one) {

            if (!class_exists($one)) return 'The class does not exist.';

            $one = new $one();

            if (!$one instanceof ClassloaderInterface) return 'The class does not implement the correct interface.';

            $one = $one->load();

            if (!is_array($one)) {
                $one = [$one];
            }

            while ($one) {
                $fixture = array_pop($one);
                if ($fixture instanceof FixtureInterface || $fixture instanceof CollectionFixture) {
                    if (!$ex->$oneAction($fixture)) {
                        array_push($one, $fixture);
                    }
                }
            }

        } else {
            $ex->$executorAction();
        }
    }

    public function loadAction()
    {
        $one = $this->params('one', false);
        return $this->generic($one, 'load');
    }

    public function unloadAction()
    {
        $one = $this->params('one', false);
        return $this->generic($one, 'unload');
    }
}