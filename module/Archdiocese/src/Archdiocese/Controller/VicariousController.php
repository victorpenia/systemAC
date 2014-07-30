<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Archdiocese\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Archdiocese\Form\VicariousForm;
use Archdiocese\Form\VicariousFilter;
use Zend\Authentication\AuthenticationService;

class VicariousController extends AbstractActionController {

    protected $vicariousTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

    public function getVicariousTable() {
        if (!$this->vicariousTable) {
            $sm = $this->getServiceLocator();
            $this->vicariousTable = $sm->get('Archdiocese\Model\Entity\Vicarious');
        }
        return $this->vicariousTable;
    }

    private function authenticationService() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return false;
        }
        $this->authUser = $auth;
        if($auth->getIdentity()->idRoles == '3' || $auth->getIdentity()->idRoles == '4' ){
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT id, parishName FROM parishes where id = '.$auth->getIdentity()->idParishes;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $this->parishName = "Parroquia \"".$res['parishName']."\"";
            }           
        }
        return true;
    }

    public function indexAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getVicariousTable()->fetchAll();
        $values = array(
            'title' => 'VICAR&Iacute;AS FOR&Aacute;NEAS',
            'data' => $paginator,
            'idRole' => $this->authUser->getIdentity()->idRoles 
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function viewAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
        }
        try {
            $vicariou = $this->getVicariousTable()->fetchOneVicariou($id);
            $parishes = $this->getVicariousTable()->fetchAllParishes($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
        }
        $values = array(
            'title' => 'VICAR&Iacute;AS FOR&Aacute;NEAS',
            'data' => $vicariou,
            'parishes' => $parishes,
            'idRole' => $this->authUser->getIdentity()->idRoles 
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function addAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new VicariousForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $vicariousFilter = new VicariousFilter();
            $form->setInputFilter($vicariousFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $vicariousFilter->exchangeArray($form->getData());
                $this->getVicariousTable()->addVicariou($vicariousFilter);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
            }
        }
        $values = array(
            'title' => 'VICAR&Iacute;AS FOR&Aacute;NEAS',
            'form' => $form,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function editAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
        }
        try {
            $vicariou = $this->getVicariousTable()->fetchOneVicariou($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new VicariousForm($this->dbAdapter);
        $form->bind($vicariou);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($vicariou->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getVicariousTable()->updateVicariou($vicariou);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
            }
        }
        $values = array(
            'title' => 'VICAR&Iacute;AS FOR&Aacute;NEAS',
            'form' => $form,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
        }
        $this->getVicariousTable()->deleteVicariou($id);
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/vicarious/index');
    }
}
