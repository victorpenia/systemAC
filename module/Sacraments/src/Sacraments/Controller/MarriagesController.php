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
use Sacraments\Form\MarriagesForm;
use Sacraments\Form\MarriagesEditForm;
use Sacraments\Form\MarriagesparishForm;
use Sacraments\Form\MarriagesparishEditForm;
use Sacraments\Form\MarriagesFilter;
use Zend\Authentication\AuthenticationService;
use Zend\Validator\Db\RecordExists;

class MarriagesController extends AbstractActionController {

    protected $MarriagesTable;
    protected $personTable;
    protected $bookTable;
    protected $userTable;
    public $dbAdapter;
    
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

    public function getMarriagesTable() {
        if (!$this->MarriagesTable) {
            $sm = $this->getServiceLocator();
            $this->MarriagesTable = $sm->get('Sacraments\Model\Entity\Marriages');
        }
        return $this->MarriagesTable;
    }
    
    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Users\Model\Entity\Users');
        }
        return $this->userTable;
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
            error_log('logC Ajx idParish = '.$idParish);
            $data = $this->getBookTable()->getBookByIdParish($idParish, 'Matrimonios');
            $buffer = "<option value=''>Seleccione un libro</option>";
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
            error_log('logC. Ajx. idBook = '.$idBook);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT item, page FROM marriages where item = (SELECT MAX(item) as itemNumber FROM marriages where idBookofsacraments = '.$idBook.') and idBookofsacraments = '.$idBook;
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
                $sqlThree = 'SELECT COUNT(page)as countPage FROM marriages where idBookofsacraments = '.$idBook.' and page = '.$pageNumber;
                $statementThree = $dbAdapter->query($sqlThree);
                $resultThree = $statementThree->execute();
                foreach ($resultThree as $resThree) {
                    $count = $resThree['countPage'];
                }
                error_log('count =  '.$count);
                if($count == 2){
                    $pageNumber = $pageNumber + 1;
                } 
            }
            $values = $pageNumber.",".$itemNumber;
            $response->setContent($values);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function getElementPersonAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $CI = $request->getPost('CI');
            error_log('logC Ajx ci = '.$CI);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT * FROM person where ci = '.$CI;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            $response->setContent($result);
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
            $idMarriage = $request->getPost('bookId');
            error_log('logC Ajx idMarrige = '.$idMarriage);
            if($this->getUserTable()->verifiedPasswordUser($password, $this->authUser->getIdentity()->id)){
                error_log('Password is correct...');
                $buffer= true;
                $this->getMarriagesTable()->signMarriage($idMarriage, $this->authUser->getIdentity()->id);
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
        $paginator = $this->getMarriagesTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
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
        $paginator = $this->getMarriagesTable()->fetchAllByParish(true, $this->authUser->getIdentity()->idParishes);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        try {
            $marriage = $this->getMarriagesTable()->getOneMarriageById($id);
            $priest = $this->getUserTable()->getOnePriest($marriage->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        $values = array(
            'data' => $marriage,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        try {
            $marriage = $this->getMarriagesTable()->getOneMarriageByParish($id, $this->authUser->getIdentity()->idParishes);
            $priest = $this->getUserTable()->getOnePriest($marriage->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        $values = array(
            'data' => $marriage,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        try {
            $marriage = $this->getMarriagesTable()->getOneMarriageById($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'data' => $marriage,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        try {
            $marriage = $this->getMarriagesTable()->getOneMarriageByParish($id, $this->authUser->getIdentity()->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'data' => $marriage,
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function addAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $messagesOne = null;
        $messagesTwo = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new MarriagesForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $marriageFilter = new MarriagesFilter();
            $form->setInputFilter($marriageFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $marriageFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabaseMale($marriageFilter->ciMale, $marriageFilter->firstNameMale, $marriageFilter->firstSurnameMale, $marriageFilter->secondSurnameMale)) {
                    if ($this->exitsPersonInDatabaseFemale($marriageFilter->ciFemale, $marriageFilter->firstNameFemale, $marriageFilter->firstSurnameFemale, $marriageFilter->secondSurnameFemale)) {
                        error_log('post...');
                        $idPersonMale = $this->getPersonTable()->addPersonMarriagesMale($marriageFilter);
                        $idPersonFemale = $this->getPersonTable()->addPersonMarriagesFemale($marriageFilter);
                        $this->getMarriagesTable()->addMarriage($marriageFilter, $idPersonMale, $idPersonFemale, $this->authUser->getIdentity()->id, $marriageFilter->idParish);
                        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
                    } else {
                        $messagesTwo .= "<p style='color:#a94442' >Error la persona (Mujer) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    }
                } else {
                    $messagesOne .= "<p style='color:#a94442' >Error la persona (Hombre) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    if (!$this->exitsPersonInDatabaseFemale($marriageFilter->ciFemale, $marriageFilter->firstNameFemale, $marriageFilter->firstSurnameFemale, $marriageFilter->secondSurnameFemale)) {
                        $messagesTwo .= "<p style='color:#a94442' >Error la persona (Mujer) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    }
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'form' => $form,
            'messagesOne' => $messagesOne,
            'messagesTwo' => $messagesTwo,
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
        $messagesOne = null;
        $messagesTwo = null;
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new MarriagesparishForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $marriageFilter = new MarriagesFilter();
            $form->setInputFilter($marriageFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $marriageFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabaseMale($marriageFilter->ciMale, $marriageFilter->firstNameMale, $marriageFilter->firstSurnameMale, $marriageFilter->secondSurnameMale)) {
                    if ($this->exitsPersonInDatabaseFemale($marriageFilter->ciFemale, $marriageFilter->firstNameFemale, $marriageFilter->firstSurnameFemale, $marriageFilter->secondSurnameFemale)) {
                        $idPersonMale = $this->getPersonTable()->addPersonMarriagesMale($marriageFilter);
                        $idPersonFemale = $this->getPersonTable()->addPersonMarriagesFemale($marriageFilter);
                        $this->getMarriagesTable()->addMarriage($marriageFilter, $idPersonMale, $idPersonFemale, $this->authUser->getIdentity()->id, $this->authUser->getIdentity()->idParishes);
                        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
                    }else{
                        $messagesTwo .= "<p style='color:#a94442' >Error la persona (Mujer) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    }
                } else {
                    $messagesOne .= "<p style='color:#a94442' >Error la persona (Hombre) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    if (!$this->exitsPersonInDatabaseFemale($marriageFilter->ciFemale, $marriageFilter->firstNameFemale, $marriageFilter->firstSurnameFemale, $marriageFilter->secondSurnameFemale)) {
                        $messagesTwo .= "<p style='color:#a94442' >Error la persona (Mujer) ya realizó el sacramento de matrimonio anteriormente.</p>";
                    }
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'form' => $form,
            'messagesOne' => $messagesOne,
            'messagesTwo' => $messagesTwo,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function exitsPersonInDatabaseMale($CI, $firstName, $firstSurname, $secondSurname) {
        $marriage = '';
        if(!empty($CI)){
            $marriage = $this->getMarriagesTable()->getOneMarriageByPersonCIMale($CI);
            if($marriage == true){
                $marriage = $this->getMarriagesTable()->getOneMarriageByPersonNameMale($firstName, $firstSurname, $secondSurname);
            }
        }else{
            $marriage = $this->getMarriagesTable()->getOneMarriageByPersonNameMale($firstName, $firstSurname, $secondSurname);
        }
        return $marriage;
    }
    
    public function exitsPersonInDatabaseFemale($CI, $firstName, $firstSurname, $secondSurname) {
        $marriage = '';
        if(!empty($CI)){
            $marriage = $this->getMarriagesTable()->getOneMarriageByPersonCIFemale($CI);
            if($marriage == true){
                $marriage = $this->getMarriagesTable()->getOneMarriageByPersonNameFemale($firstName, $firstSurname, $secondSurname);
            }
        }else{
            $marriage = $this->getMarriagesTable()->getOneMarriageByPersonNameFemale($firstName, $firstSurname, $secondSurname);
        }
        return $marriage;
    }

    public function editAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        $messages = null;
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        try {
            $marriage = $this->getMarriagesTable()->getOneMariageAndParish($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new MarriagesEditForm($this->dbAdapter, $marriage->marriagePriest, $marriage->attestPriest, $marriage->baptismParishMale, $marriage->baptismParishFemale);
        $form->bind($marriage);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($marriage->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->exitsCiInDatabaseEdit($marriage->ciMale, $marriage->idPersonMale) || $this->exitsCiInDatabaseEdit($marriage->ciFemale, $marriage->idPersonFemale)) {
                    error_log('yes yesyasdasdas');
                    $this->getPersonTable()->updatePersonMarriagesMale($marriage);
                    $this->getPersonTable()->updatePersonMarriagesFemale($marriage);
                    $this->getMarriagesTable()->updateMarriages($marriage);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/index');
                } else {
                    error_log(' nooooooooo');
                    $messages .= "<p style='color:#a94442' >El CI ya existe en la base de datos</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'form' => $form,
            'messages' => $messages,
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
        $messages = null;
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        try { 
            $marriage = $this->getMarriagesTable()->getOneMariage($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new MarriagesparishEditForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes, $marriage->marriagePriest, $marriage->attestPriest, $marriage->baptismParishMale, $marriage->baptismParishFemale);
        $form->bind($marriage);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($marriage->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->exitsCiInDatabaseEdit($marriage->ciMale, $marriage->idPersonMale) || $this->exitsCiInDatabaseEdit($marriage->ciFemale, $marriage->idPersonFemale)) {
                    error_log('yes yesyasdasdas');
                    $this->getPersonTable()->updatePersonMarriagesMale($marriage);
                    $this->getPersonTable()->updatePersonMarriagesFemale($marriage);
                    $this->getMarriagesTable()->updateMarriages($marriage);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/marriages/indexp');
                } else {
                    error_log(' nooooooooo');
                    $messages .= "<p style='color:#a94442' >El CI ya existe en la base de datos</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE MATRIMONIO',
            'form' => $form,
            'messages' => $messages,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function exitsCiInDatabaseEdit($ci, $idPerson) {
        error_log('entraaaa '.$idPerson);
        $validator = new RecordExists(
            array(
                'table' => 'person',
                'field' => 'ci',
                'adapter' => $this->dbAdapter,
                'exclude' => array(
                    'field' => 'id',
                    'value' => $idPerson,
                )
            )
        );
        if ($validator->isValid($ci)) {
            error_log('exit');
            return false;
        } else {
            error_log('no exit');
            return true;
        }
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