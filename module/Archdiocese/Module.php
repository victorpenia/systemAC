<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Archdiocese;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Archdiocese\Model\Entity\Vicarious;
use Archdiocese\Form\VicariousFilter;
use Archdiocese\Model\Entity\Parishes;
use Archdiocese\Form\ParishesFilter;
use Users\Form\UserFilter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Archdiocese\Model\Entity\Vicarious' => function($sm) {
                    $TableGateway = $sm->get('VicariousTableGateway');
                    $table = new Vicarious($TableGateway);
                    return $table;
                },
                'VicariousTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new VicariousFilter());
                    return new TableGateway('vicarious', $dbAdapter, null, $resultSetPrototype);
                },
                'Archdiocese\Model\Entity\Parishes' => function($sm) {
                    $tableGateway = $sm->get('ParishesTableGateway');
                    $table = new Parishes($tableGateway);
                    return $table;
                },
                'ParishesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ParishesFilter());
                    return new TableGateway('parishes', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
