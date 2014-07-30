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
use Sacraments\Form\BaptismsForm;
use Sacraments\Form\BaptismsparishForm;
use Sacraments\Form\BaptismsFilter;
use Zend\Authentication\AuthenticationService;
use Zend\Validator\Db\RecordExists;

class BaptismsController extends AbstractActionController {

    protected $baptismsTable;
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
    
    public function getBaptismsTable() {
        if (!$this->baptismsTable) {
            $sm = $this->getServiceLocator();
            $this->baptismsTable = $sm->get('Sacraments\Model\Entity\Baptisms');
        }
        return $this->baptismsTable;
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
    
    public function getBornInAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $bornIn = $request->getPost('bornIn');
            error_log('logC. Ajx Born in = '.$bornIn);
            $buffer = $this->getProvinceByCity($bornIn);
            $response->setContent($buffer);
            $headers = $response->getHeaders();
        }
        return $response;
    }    

    public function getBookSacramentAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $idParish = $request->getPost('idParish');
            $data = $this->getBookTable()->getBookByIdParish($idParish, 'Bautismos');
            $buffer = "<option value=''>Seleccione un Libro</option>";
            foreach ($data as $item_data) {
                $buffer.='<option value=' . $item_data->id . '>' . $item_data->code . ' (' . $item_data->sacramentName . ' libro ' . $item_data->book . ')' . '</option>';
            }
            $response->setContent($buffer);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function getElementBookSacramentAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $idBook = $request->getPost('idBook');
            error_log('logC. Ajx idBook = '.$idBook);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT MAX(page) as pageNumber, MAX(item) as itemNumber FROM baptisms where idBookofsacraments = '.$idBook;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $pageNumber = $res['pageNumber'];
                $itemNumber = $res['itemNumber'];
            }
            if(empty($itemNumber)){
                error_log('logC Ajx error item...');
                $sqlTwo = 'SELECT startItem FROM bookofsacraments where id = '.$idBook;
                $statementTwo = $dbAdapter->query($sqlTwo);
                $resultTwo = $statementTwo->execute();
                foreach ($resultTwo as $resTwo) {
                    $itemNumber = $resTwo['startItem'];
                }
                
            }else{                 
                $itemNumber = $itemNumber + 1;
            }
            $pageNumber = $pageNumber + 1;
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
            $idBaptism = $request->getPost('bookId');
            error_log('logC Ajx idbaptism = '.$idBaptism);
            if($this->getUserTable()->verifiedPasswordUser($password, $this->authUser->getIdentity()->id)){
                error_log('Password is correct...');
                $buffer= true;
                $this->getBaptismsTable()->signBaptism($idBaptism, $this->authUser->getIdentity()->id);
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
        $paginator = $this->getBaptismsTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
            'data' => $paginator,
            'idRol' => $this->authUser->getIdentity()->idRoles,
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function indexpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getBaptismsTable()->fetchAllByParish(true, $this->authUser->getIdentity()->idParishes);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(1000);
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }
        try {
            $baptisms = $this->getBaptismsTable()->getOneBaptisms($id);
            $priest = $this->getUserTable()->getOnePriest($baptisms->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }
        $values = array(
            'data' => $baptisms,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        try {
            $baptisms = $this->getBaptismsTable()->getOneBaptismsByParish($id, $this->authUser->getIdentity()->idParishes);            
            $priest = $this->getUserTable()->getOnePriest($baptisms->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        $values = array(
            'data' => $baptisms,
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }
        try {
            $baptisms = $this->getBaptismsTable()->getOneBaptisms($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
            'data' => $baptisms
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        try {
            $baptisms = $this->getBaptismsTable()->getOneBaptismsByParish($id, $this->authUser->getIdentity()->idParishes);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
            'data' => $baptisms
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
        $form = new BaptismsForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $baptismsFilter = new BaptismsFilter();
            $form->setInputFilter($baptismsFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $baptismsFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabase($baptismsFilter->ci, $baptismsFilter->firstName, $baptismsFilter->firstSurname, $baptismsFilter->secondSurname)) {
                    $idPerson = $this->getPersonTable()->addPersonBaptisms($baptismsFilter);
                    $this->getBaptismsTable()->addBaptism($baptismsFilter, $idPerson, $this->authUser->getIdentity()->id, $baptismsFilter->idParishes);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
                } else {
                    $messages .= "<p style='color:#a94442' >Error la persona ya realizó el sacramento de bautismo anteriormente.</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
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
        $form = new BaptismsparishForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $baptismsFilter = new BaptismsFilter();
            $form->setInputFilter($baptismsFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $baptismsFilter->exchangeArray($form->getData());
                if ($this->exitsPersonInDatabase($baptismsFilter->ci, $baptismsFilter->firstName, $baptismsFilter->firstSurname, $baptismsFilter->secondSurname)) {
                    $idPerson = $this->getPersonTable()->addPersonBaptisms($baptismsFilter);
                    $this->getBaptismsTable()->addBaptism($baptismsFilter, $idPerson, $this->authUser->getIdentity()->id, $this->authUser->getIdentity()->idParishes);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
                } else {
                    $messages .= "<p style='color:#a94442' >Error la persona ya realizó el sacramento de bautismo anteriormente.</p>";
                }
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
            'form' => $form,
            'messages' => $messages,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    public function exitsPersonInDatabase($CI, $firstName, $firstSurname, $secondSurname) {
        $baptisms = '';
        if(!empty($CI)){
            $baptisms = $this->getBaptismsTable()->getOneBaptismsByPerson($CI);
            if($baptisms == true){
                $baptisms = $this->getBaptismsTable()->getOneBaptismsByPersonName($firstName, $firstSurname, $secondSurname);
            }
        }else{
            $baptisms = $this->getBaptismsTable()->getOneBaptismsByPersonName($firstName, $firstSurname, $secondSurname);
        }
        return $baptisms;
    }

    public function editAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = '.$id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }
        try {
            $baptism = $this->getBaptismsTable()->getOneBaptisms($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
        }

        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new BaptismsForm($this->dbAdapter);
        $form->bind($baptism);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($baptism->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getBaptismsTable()->updateBaptism($baptism);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/index');
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
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
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        try {
            $baptism = $this->getBaptismsTable()->getOneBaptisms($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = '.$exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new BaptismsparishForm($this->dbAdapter, $this->authUser->getIdentity()->idParishes);
        $form->bind($baptism);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($baptism->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getBaptismsTable()->updateBaptism($baptism);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/baptisms/indexp');
            }
        }
        $values = array(
            'title' => 'SACRAMENTO DE BAUTISMO',
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
    
    /* function to obtain provinces by departament */
    public function getProvinceByCity($bornIn){
        $buffer = '';
        if($bornIn == 'Beni'){            
            $buffer = "<option value='Antonio Vaca Díez'>Antonio Vaca Díez</option>";
            $buffer.= "<option value='Cercado'>Cercado</option>";
            $buffer.= "<option value='General José Ballivián '>General José Ballivián </option>";
            $buffer.= "<option value='Iténez'>Iténez</option>";
            $buffer.= "<option value='Mamoré'>Mamoré</option>";
            $buffer.= "<option value='Marbán'>Marbán</option>";
            $buffer.= "<option value='Moxos'>Moxos</option>";
            $buffer.= "<option value='Yacuma'>Yacuma</option>";
        }
        if($bornIn == 'Chuquisaca'){
            $buffer = "<option value='Belisario Boeto'>Belisario Boeto</option>";
            $buffer.= "<option value='Hernando Siles'>Hernando Siles</option>";
            $buffer.= "<option value='Jaime Zudáñez'>Jaime Zudáñez</option>";
            $buffer.= "<option value='Juana Azurduy'>Juana Azurduy</option>";
            $buffer.= "<option value='Luis Calvo'>Luis Calvo</option>";
            $buffer.= "<option value='Nor Cinti'>Nor Cinti</option>";
            $buffer.= "<option value='Oropeza'>Oropeza</option>";
            $buffer.= "<option value='Sud Cinti'>Sud Cinti</option>";            
            $buffer.= "<option value='Tomina'>Tomina</option>";            
            $buffer.= "<option value='Yamparáez'>Yamparáez</option>";
        }
        if($bornIn == 'Cochabamba'){
            $buffer = "<option value='Arani'>Arani</option>";
            $buffer.= "<option value='Arque'>Arque</option>";
            $buffer.= "<option value='Ayopaya'>Ayopaya</option>";            
            $buffer.= "<option value='Bolívar'>Bolívar</option>";
            $buffer.= "<option value='Campero'>Campero</option>";
            $buffer.= "<option value='Capinota'>Capinota</option>";
            $buffer.= "<option value='Carrasco'>Carrasco</option>";
            $buffer.= "<option value='Cercado'>Cercado</option>";
            $buffer.= "<option value='Chapare'>Chapare</option>";
            $buffer.= "<option value='Esteban Arce'>Esteban Arce</option>";
            $buffer.= "<option value='Germán Jordán'>Germán Jordán</option>";
            $buffer.= "<option value='Mizque'>Mizque</option>";
            $buffer.= "<option value='Punata'>Punata</option>";
            $buffer.= "<option value='Quillacollo'>Quillacollo</option>";
            $buffer.= "<option value='Tapacarí'>Tapacarí</option>";
            $buffer.= "<option value='Tiraque'>Tiraque</option>";
        }
        if($bornIn == 'La Paz'){
            $buffer = "<option value='Abel Iturralde'>Abel Iturralde</option>";
            $buffer.= "<option value='Aroma'>Aroma</option>";
            $buffer.= "<option value='Bautista Saavedra'>Bautista Saavedra</option>";
            $buffer.= "<option value='Caranavi'>Caranavi</option>";
            $buffer.= "<option value='Eliodoro Camacho'>Eliodoro Camacho</option>";
            $buffer.= "<option value='Franz Tamayo'>Franz Tamayo</option>";
            $buffer.= "<option value='General José Manuel Pando'>General José Manuel Pando</option>";
            $buffer.= "<option value='Gualberto Villaroel'>Gualberto Villaroel</option>";
            $buffer.= "<option value='Ingavi'>Ingavi</option>";
            $buffer.= "<option value='Inquisivi'>Inquisivi</option>";
            $buffer.= "<option value='José Ramón Loayza'>José Ramón Loayza</option>";
            $buffer.= "<option value='Larecaja'>Larecaja</option>";            
            $buffer.= "<option value='Los Andes'>Los Andes</option>";
            $buffer.= "<option value='Manco Kapac'>Manco Kapac</option>";
            $buffer.= "<option value='Muñecas'>Muñecas</option>";
            $buffer.= "<option value='Nor Yungas'>Nor Yungas</option>";
            $buffer.= "<option value='Omasuyos'>Omasuyos</option>";
            $buffer.= "<option value='Pacajes'>Pacajes</option>";
            $buffer.= "<option value='Pedro Domingo Murillo'>Pedro Domingo Murillo</option>";
            $buffer.= "<option value='Sud Yungas'>Sud Yungas</option>";            
        }
        if($bornIn == 'Oruro'){
            $buffer = "<option value='Carangas'>Carangas</option>";
            $buffer.= "<option value='Cercado'>Cercado</option>";
            $buffer.= "<option value='Eduardo Avaroa'>Eduardo Avaroa</option>";
            $buffer.= "<option value='Ladislao Cabrera'>Ladislao Cabrera</option>";
            $buffer.= "<option value='Litoral'>Litoral</option>";
            $buffer.= "<option value='Mejillones'>Mejillones</option>";
            $buffer.= "<option value='Nor Carangas'>Nor Carangas</option>";
            $buffer.= "<option value='Pantaleón Dalence'>Pantaleón Dalence</option>";
            $buffer.= "<option value='Poopó'>Poopó</option>";
            $buffer.= "<option value='Sabaya'>Sabaya</option>";
            $buffer.= "<option value='Sajama'>Sajama</option>";
            $buffer.= "<option value='San Pedro de Totora'>San Pedro de Totora</option>";
            $buffer.= "<option value='Saucarí'>Saucarí</option>";
            $buffer.= "<option value='Sebastian Pagador'>Sebastian Pagador</option>";
            $buffer.= "<option value='Sud Carangas'>Sud Carangas</option>";
            $buffer.= "<option value='Tomas Barrón'>Tomas Barrón</option>";
        }
        if($bornIn == 'Pando'){
            $buffer = "<option value='Abuná'>Abuná</option>";
            $buffer.= "<option value='Federico Román'>Federico Román</option>";
            $buffer.= "<option value='Madre de Dios'>Madre de Dios</option>";
            $buffer.= "<option value='Manuripi'>Manuripi</option>";
            $buffer.= "<option value='Nicolás Suárez'>Nicolás Suárez</option>";
        }
        if($bornIn == 'Potosi'){
            $buffer = "<option value='Alonso de Ibáñez'>Alonso de Ibáñez</option>";
            $buffer.= "<option value='Antonio Quijarro'>Antonio Quijarro</option>";
            $buffer.= "<option value='Bernardino Bilbao'>Bernardino Bilbao</option>";
            $buffer.= "<option value='Charcas'>Charcas</option>";
            $buffer.= "<option value='Chayanta'>Chayanta</option>";
            $buffer.= "<option value='Cornelio Saavedra'>Cornelio Saavedra</option>";
            $buffer.= "<option value='Daniel Campos'>Daniel Campos</option>";
            $buffer.= "<option value='Enrique Baldivieso'>Enrique Baldivieso</option>";
            $buffer.= "<option value='José María Linares'>José María Linares</option>";
            $buffer.= "<option value='Modesto Omiste'>Modesto Omiste</option>";
            $buffer.= "<option value='Nor Chichas'>Nor Chichas</option>";
            $buffer.= "<option value='Nor Lípez'>Nor Lípez</option>";
            $buffer.= "<option value='Rafael Bustillo'>Rafael Bustillo</option>";
            $buffer.= "<option value='Sud Chichas'>Sud Chichas</option>";
            $buffer.= "<option value='Sud Lípez'>Sud Lípez</option>";
            $buffer.= "<option value='Tomás Frías'>Tomás Frías</option>";
        }
        if($bornIn == 'Santa Cruz'){
            $buffer = "<option value='Andrés Ibáñez'>Andrés Ibáñez</option>";
            $buffer.= "<option value='Angel sandoval'>Angel sandoval</option>";
            $buffer.= "<option value='Chiquitos'>Chiquitos</option>";
            $buffer.= "<option value='Cordillera'>Cordillera</option>";
            $buffer.= "<option value='Florida'>Florida</option>";
            $buffer.= "<option value='German Bush'>German Bush</option>";
            $buffer.= "<option value='Guarayos'>Guarayos</option>";          
            $buffer.= "<option value='Ichilo'>Ichilo</option>";
            $buffer.= "<option value='Manuel Maria Caballero'>Manuel Maria Caballero</option>";
            $buffer.= "<option value='Ñuflo de Chávez'>Ñuflo de Chávez</option>";
            $buffer.= "<option value='Obispo Santisteban'>Obispo Santisteban</option>";
            $buffer.= "<option value='Sara'>Sara</option>";
            $buffer.= "<option value='Velasco'>Velasco</option>";            
            $buffer.= "<option value='Vallegrande'>Vallegrande</option>";
            $buffer.= "<option value='Warnes'>Warnes</option>";
        }
        if($bornIn == 'Tarija'){
            $buffer = "<option value='Aniceto Arce'>Aniceto Arce</option>";
            $buffer.= "<option value='Burdet O'Connor'>Burdet O'Connor</option>";
            $buffer.= "<option value='Cercado'>Cercado</option>";
            $buffer.= "<option value='Eustaquio Méndez'>Eustaquio Méndez</option>";
            $buffer.= "<option value='Gran Chaco'>Gran Chaco</option>";
            $buffer.= "<option value='José María Avilés'>José María Avilés</option>";
        }
        if($bornIn == 'Otros'){
            $buffer = "<option value='Otros'>Otros</option>";
        }
        return $buffer;
    }
}