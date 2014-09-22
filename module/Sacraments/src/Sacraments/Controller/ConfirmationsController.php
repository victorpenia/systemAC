<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Sacraments\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sacraments\Form\ConfirmationsForm;
use Sacraments\Form\ConfirmationsEditForm;
use Sacraments\Form\ConfirmationsparishForm;
use Sacraments\Form\ConfirmationsparishEditForm;
use Sacraments\Form\ConfirmationsFilter;
use Zend\Authentication\AuthenticationService;

class ConfirmationsController extends AbstractActionController {

    protected $confirmationsTable;
    protected $personTable;
    protected $bookTable;
    protected $userTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";
    
    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Users\Model\Entity\Users');
        }
        return $this->userTable;
    }

    public function getConfirmationsTable() {
        if (!$this->confirmationsTable) {
            $sm = $this->getServiceLocator();
            $this->confirmationsTable = $sm->get('Sacraments\Model\Entity\Confirmations');
        }
        return $this->confirmationsTable;
    }

    public function getPersonTable() {
        if (!$this->personTable) {
            $sm = $this->getServiceLocator();
            $this->personTable = $sm->get('Sacraments\Model\Entity\Person');
        }
        return $this->personTable;
    }

    public function getBookTable() {
        if (!$this->bookTable) {
            $sm = $this->getServiceLocator();
            $this->bookTable = $sm->get('Sacraments\Model\Entity\Books');
        }
        return $this->bookTable;
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

    public function getBookSacramentAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $idParish = $request->getPost('idParish');
            error_log('logC. Ajx idParish = '.$idParish);
            $data = $this->getBookTable()->getBookByIdParish($idParish, 'Confirmaciones');
            $buffer = "<option value='0'>Seleccione un Libro</option>";
            foreach ($data as $item_data) {
                $buffer.='<option value=' . $item_data->id . '>' . $item_data->code . ' (' . $item_data->sacramentName . ' libro ' . $item_data->book . ')' . '</option>';
            }
            $response->setContent($buffer);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function getElementBookSacramentAction() {
        $pageNumber = 0;
        $itemNumber = 0;
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $idBook = $request->getPost('idBook');
            error_log('logC. Ajx idBook = '.$idBook);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT item, page FROM confirmations where item = (SELECT MAX(item) as itemNumber FROM confirmations where idBookofsacraments = '.$idBook.') and idBookofsacraments = '.$idBook;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $pageNumber = $res['page'];
                $itemNumber = $res['item'];
            }
            if(empty($itemNumber)){
                error_log('logC Ajx error item...');
                $sqlTwo = 'SELECT startItem FROM bookofsacraments where id = '.$idBook;
                $statementTwo = $dbAdapter->query($sqlTwo);
                $resultTwo = $statementTwo->execute();
                foreach ($resultTwo as $resTwo) {
                    $itemNumber = $resTwo['startItem'];
                }
                $pageNumber = 1;
                
            }else{                 
                error_log('logC Ajx pageNumber = '.$pageNumber);  
                $itemNumber = $itemNumber + 1;
                $sqlThree = 'SELECT COUNT(page)as countPage FROM confirmations where idBookofsacraments = '.$idBook.' and page = '.$pageNumber;
                $statementThree = $dbAdapter->query($sqlThree);
                $resultThree = $statementThree->execute();
                foreach ($resultThree as $resThree) {
                    $count = $resThree['countPage'];
                }
                error_log('count =  '.$count);
                if($count == 3){
                    $pageNumber = $pageNumber + 1;
                }
            }
            $values = $pageNumber.",".$itemNumber;
            $response->setContent($values);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function signDocumentAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $password = $this->getPassword($request);
            $idConfirmation = $request->getPost('bookId');
            error_log('logC. Ajx idConfirmation = '.$idConfirmation);
            if($this->getUserTable()->verifiedPasswordUser($password, $this->authUser->getIdentity()->id)){
                error_log('Password is correct...');
                $buffer= true;
                $this->getConfirmationsTable()->signConfirmation($idConfirmation, $this->authUser->getIdentity()->id);
            }else{
                error_log('Password is not valid...');
                $buffer = false;
            }
            $response->setContent($buffer);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function getPassword($request){
        $password = $request->getPost('mypassword');
        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['staticSalt'];
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $sql = "SELECT md5(concat('$staticSalt', '$password', passwordSalt)) as pass FROM users where id =" . $this->authUser->getIdentity()->id;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $pass = $res['pass'];
        }
        return $pass;
    }

    public function indexAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getConfirmationsTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'data' => $paginator,
            'idRol' => $this->authUser->getIdentity()->idRoles
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function indexpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getConfirmationsTable()->fetchAllByParish(true, $this->authUser->getIdentity()->idParishes);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'data' => $paginator,
            'idRol' => $this->authUser->getIdentity()->idRoles
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function printAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationById($id);
            $priest = $this->getUserTable()->getOnePriest($confirmation->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        $values = array(
            'data' => $confirmation,
            'priest' => $priest,
            'url' => $this->getRequest()->getBaseUrl()
        );
        $viewModel = new ViewModel($values);
        $viewModel->setVariables(array('key' => 'value'))
                ->setTerminal(true);
        return $viewModel;
    }
    
    public function printpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationByParish($id, $this->authUser->getIdentity()->idParishes);
            $priest = $this->getUserTable()->getOnePriest($confirmation->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
        }
        $values = array(
            'data' => $confirmation,
            'priest' => $priest,
            'url' => $this->getRequest()->getBaseUrl()
        );
        $viewModel = new ViewModel($values);
        $viewModel->setVariables(array('key' => 'value'))
                ->setTerminal(true);
        return $viewModel;
    }

    public function viewAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationById($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'data' => $confirmation
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function viewpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationByParish($id, $this->authUser->getIdentity()->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'data' => $confirmation
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function addAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ConfirmationsForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $confirmationsFilter = new ConfirmationsFilter();
            $form->setInputFilter($confirmationsFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $confirmationsFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabase($confirmationsFilter->ci, $confirmationsFilter->firstName, $confirmationsFilter->firstSurname, $confirmationsFilter->secondSurname)) {
                    $idPerson = $this->getPersonTable()->addPersonConfirmations($confirmationsFilter);
                    $this->getConfirmationsTable()->addConfirmations($confirmationsFilter, $idPerson, $this->authUser->getIdentity()->id, $confirmationsFilter->idParish);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
                } else {
                    $messages .= "<p style='color:#a94442' >Error la persona ya realizó el sacramento de confirmación anteriormente.</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function addpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messages = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ConfirmationsparishForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $confirmationsFilter = new ConfirmationsFilter();
            $form->setInputFilter($confirmationsFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $confirmationsFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabase($confirmationsFilter->ci, $confirmationsFilter->firstName, $confirmationsFilter->firstSurname, $confirmationsFilter->secondSurname)) {
                    $idPerson = $this->getPersonTable()->addPersonConfirmations($confirmationsFilter);
                    $this->getConfirmationsTable()->addConfirmations($confirmationsFilter, $idPerson, $this->authUser->getIdentity()->id, $this->authUser->getIdentity()->idParishes);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
                } else {
                    $messages .= "<p style='color:#a94442' >Error la persona ya realizó el sacramento de confirmación anteriormente.</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    public function exitsPersonInDatabase($CI, $firstName, $firstSurname, $secondSurname) {
        $confirmation = '';
        if(!empty($CI)){
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationByPerson($CI);
            if($confirmation == true){
                $confirmation = $this->getConfirmationsTable()->getOneConfirmationByPersonName($firstName, $firstSurname, $secondSurname);
            }
        }else{
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationByPersonName($firstName, $firstSurname, $secondSurname);
        }
        return $confirmation;
    }

    public function editAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmationAndParish($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ConfirmationsEditForm($this->dbAdapter, $confirmation->attestPriest, $confirmation->baptismParish);
        $form->bind($confirmation);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($confirmation->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getPersonTable()->updatePersonConfirmations($confirmation);
                $this->getConfirmationsTable()->updateConfirmation($confirmation);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/index');
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
            'form' => $form,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function editpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
        }
        try {
            $confirmation = $this->getConfirmationsTable()->getOneConfirmation($id);
            error_log($confirmation->baptismParish);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new ConfirmationsparishEditForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes, $confirmation->attestPriest, $confirmation->baptismParish);
        $form->bind($confirmation);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($confirmation->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getPersonTable()->updatePersonConfirmations($confirmation);
                $this->getConfirmationsTable()->updateConfirmation($confirmation);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/confirmations/indexp');
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE CONFIRMACI&Oacute;N',
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