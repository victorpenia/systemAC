<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Sacraments;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Sacraments\Model\Entity\Person;
use Sacraments\Form\PersonFilter;
use Sacraments\Model\Entity\Books;
use Sacraments\Form\BooksFilter;
use Sacraments\Model\Entity\Baptisms;
use Sacraments\Form\BaptismsFilter;
use Sacraments\Model\Entity\Marriages;
use Sacraments\Form\MarriagesFilter;
use Sacraments\Model\Entity\Confirmations;
use Sacraments\Form\ConfirmationsFilter;
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
                'Sacraments\Model\Entity\Books' => function($sm) {
                    $tableGateway = $sm->get('BooksTableGateway');
                    $table = new Books($tableGateway);
                    return $table;
                },
                'BooksTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BooksFilter());
                    return new TableGateway('bookofsacraments', $dbAdapter, null, $resultSetPrototype);
                },
                'Sacraments\Model\Entity\Baptisms' => function($sm) {
                    $tableGateway = $sm->get('BaptismsTableGateway');
                    $table = new Baptisms($tableGateway);
                    return $table;
                },
                'BaptismsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new BaptismsFilter());
                    return new TableGateway('baptisms', $dbAdapter, null, $resultSetPrototype);
                },
                'Sacraments\Model\Entity\Marriages' => function($sm) {
                    $tableGateway = $sm->get('MarriagesTableGateway');
                    $table = new Marriages($tableGateway);
                    return $table;
                },
                'MarriagesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new MarriagesFilter());
                    return new TableGateway('marriages', $dbAdapter, null, $resultSetPrototype);
                },
                'Sacraments\Model\Entity\Confirmations' => function($sm) {
                    $tableGateway = $sm->get('ConfirmationsTableGateway');
                    $table = new Confirmations($tableGateway);
                    return $table;
                },
                'ConfirmationsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ConfirmationsFilter());
                    return new TableGateway('confirmations', $dbAdapter, null, $resultSetPrototype);
                },
                'Sacraments\Model\Entity\Person' => function($sm) {
                    $tableGateway = $sm->get('PersonTableGateway');
                    $table = new Person($tableGateway);
                    return $table;
                },
                'PersonTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PersonFilter());
                    return new TableGateway('person', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
