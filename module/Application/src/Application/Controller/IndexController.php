<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\AuthenticationForm;
use Application\Form\AuthenticationFilter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $messages = null;
        $form = new AuthenticationForm("form");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $authenticationFilter = new AuthenticationFilter();
            $form->setInputFilter($authenticationFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $sm = $this->getServiceLocator();
                $dbAdapter = $sm->get('Zend\Db\Adapter');
                $config = $this->getServiceLocator()->get('Config');
                $staticSalt = $config['staticSalt'];
                $authAdapter = new AuthAdapter($dbAdapter, 'users', 'email', 'password', "MD5(CONCAT('$staticSalt', ?, passwordSalt)) AND  status ='Activo'");
                $authAdapter->setIdentity($data['email']);
                $authAdapter->setCredential($data['password']);
                $auth = new AuthenticationService();
                $result = $auth->authenticate($authAdapter);
                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        break;
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        break;
                    case Result::SUCCESS:
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(null, 'password'));
                        $time = 604800; //= 7 days
//                  if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
                        if ($data['rememberme']) {
                            error_log("remember me");
                            $sessionManager = new \Zend\Session\SessionManager();
                            $sessionManager->rememberMe($time);
                        }
                        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/application/dashboard/index');
                        break;
                    default:
                        break;
                }
                foreach ($result->getMessages() as $message) {
                    $messages .= "<p style='color:#ff0000'>El email o contraseña no es correcto.<p>";
                }
            }
        }
        $values = array
            (
            'title' => 'Arzobispado de Cochabamba',
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $view = new ViewModel($values);
        $this->layout('layout/layoutMain');
        return $view;
    }

    public function logoutAction() {
        $auth = new AuthenticationService();

        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
        }
        $auth->clearIdentity();
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        #return $this->redirect()->toRoute('application/default', array('controller' => 'index', 'action' => 'index'));		
    }

}
