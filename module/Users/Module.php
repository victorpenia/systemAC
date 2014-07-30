<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Users\Model\Entity\Users;
use Users\Form\UserFilter;
use Users\Model\Entity\Information;
use Users\Form\InformationFilter;
use Users\Model\Entity\Certificate;
use Users\Form\CertificateFilter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;


use Zend\Session\SessionManager;
use Zend\Session\Container;


class Module implements AutoloaderProviderInterface {

    public function onBootstrap(MvcEvent $e) {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
//        $serviceManager      = $e->getApplication()->getServiceManager();        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
//        $this->bootstrapSession($e);
        
        $translator=$e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            './vendor/zendframework/zendframework/resources/languages/es/Zend_Validate.php'

        );
        AbstractValidator::setDefaultTranslator($translator);
    }
    
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

//    public function bootstrapSession($e) {
//        $session = $e->getApplication()
//                ->getServiceManager()
//                ->get('Zend\Session\SessionManager');
//        $session->start();
//        $container = new Container('initialized');
//        if (!isset($container->init)) {
//            $session->regenerateId(true);
//            $container->init = 1;
//        }
//    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Users\Model\Entity\Users' => function($sm) {
                    $tableGateway = $sm->get('UsersTableGateway');
                    $table = new Users($tableGateway);
                    return $table;
                },
                'UsersTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserFilter());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                'Users\Model\Entity\Information' => function($sm) {
                    $tableGateway = $sm->get('InformationTableGateway');
                    $table = new Information($tableGateway);
                    return $table;
                },
                'InformationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new InformationFilter());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                'Users\Model\Entity\Certificate' => function($sm) {
                    $tableGateway = $sm->get('CertificateTableGateway');
                    $table = new Certificate($tableGateway);
                    return $table;
                },
                'CertificateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new CertificateFilter());
                    return new TableGateway('certificates', $dbAdapter, null, $resultSetPrototype);
                },
            ),
            
//            'factories' => array(
//                'Zend\Session\SessionManager' => function ($sm) {
//                    $config = $sm->get('config');
//                    if (isset($config['session'])) {
//                        $session = $config['session'];
//
//                        $sessionConfig = null;
//                        if (isset($session['config'])) {
//                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
//                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
//                            $sessionConfig = new $class();
//                            $sessionConfig->setOptions($options);
//                        }
//
//                        $sessionStorage = null;
//                        if (isset($session['storage'])) {
//                            $class = $session['storage'];
//                            $sessionStorage = new $class();
//                        }
//
//                        $sessionSaveHandler = null;
//                        if (isset($session['save_handler'])) {
//                            // class should be fetched from service manager since it will require constructor arguments
//                            $sessionSaveHandler = $sm->get($session['save_handler']);
//                        }
//
//                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
//
//                        if (isset($session['validators'])) {
//                            $chain = $sessionManager->getValidatorChain();
//                            foreach ($session['validators'] as $validator) {
//                                $validator = new $validator();
//                                $chain->attach('session.validate', array($validator, 'isValid'));
//
//                            }
//                        }
//                    } else {
//                        $sessionManager = new SessionManager();
//                    }
//                    Container::setDefaultManager($sessionManager);
//                    return $sessionManager;
//                },
//            ),
        );
    } 
}
