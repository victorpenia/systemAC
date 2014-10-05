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
use Sacraments\Form\BooksForm;
use Sacraments\Form\BooksEditForm;
use Sacraments\Form\BooksparishForm;
use Sacraments\Form\BooksFilter;
use Zend\Authentication\AuthenticationService;

class BooksController extends AbstractActionController {

    protected $booksTable;
    protected $baptismsTable;
    protected $confirmationsTable;
    protected $marriagesTable;
    public $dbAdapter;
    public $authUser;
    public $parishName = "Arzobispado de Cochabamba";

    public function getBooksTable() {
        if (!$this->booksTable) {
            $sm = $this->getServiceLocator();
            $this->booksTable = $sm->get('Sacraments\Model\Entity\Books');
        }
        return $this->booksTable;
    }
    
    public function getBaptismsTable() {
        if (!$this->baptismsTable) {
            $sm = $this->getServiceLocator();
            $this->baptismsTable = $sm->get('Sacraments\Model\Entity\Baptisms');
        }
        return $this->baptismsTable;
    }
    
    public function getConfirmationsTable() {
        if (!$this->confirmationsTable) {
            $sm = $this->getServiceLocator();
            $this->confirmationsTable = $sm->get('Sacraments\Model\Entity\Confirmations');
        }
        return $this->confirmationsTable;
    }
    
    public function getMarriagesTable() {
        if (!$this->marriagesTable) {
            $sm = $this->getServiceLocator();
            $this->marriagesTable = $sm->get('Sacraments\Model\Entity\Marriages');
        }
        return $this->marriagesTable;
    }

    private function authenticationService() {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return false;
        }
        $this->authUser = $auth;
        if ($auth->getIdentity()->idRoles == '3' || $auth->getIdentity()->idRoles == '4') {
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT id, parishName FROM parishes where id = ' . $auth->getIdentity()->idParishes;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $this->parishName = "Parroquia \"" . $res['parishName'] . "\"";
            }
        }
        return true;
    }

    public function getBookSacramentAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $sacrament = $request->getPost('sacrament');
            error_log('logC. Ajx sacrament = ' . $sacrament);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT MAX(book) as bookNumber FROM bookofsacraments where sacramentName = \'' . $sacrament . '\' and idParishes = ' . $this->authUser->getIdentity()->idParishes;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $bookNumber = $res['bookNumber'];
            }
            $bookNumber = $bookNumber + 1;
            $response->setContent($bookNumber);
            $headers = $response->getHeaders();
        }
        return $response;
    }
    
    public function getBookSacramentByParishAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        error_log('Llega ... book');
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $response->setStatusCode(200);
            $sacrament = $request->getPost('sacrament');
            $idParish = $request->getPost('idParish');
            error_log('logC. Ajx sacrament = ' . $sacrament);
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $sql = 'SELECT MAX(book) as bookNumber FROM bookofsacraments where sacramentName = \'' . $sacrament . '\' and idParishes = ' . $idParish;
            $statement = $dbAdapter->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $bookNumber = $res['bookNumber'];
            }
            $bookNumber = $bookNumber + 1;
            $response->setContent($bookNumber);
            $headers = $response->getHeaders();
        }
        return $response;
    }

    public function viewAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = ' . $id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
        }
        try {
            $book = $this->getBooksTable()->getOneBook($id);
            $baptism = $this->getBooksTable()->fetchAllBaptismByBook($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = ' . $exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'data' => $book,
            'baptism' => $baptism
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
        error_log('logC id = ' . $id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
        }
        try {
            $book = $this->getBooksTable()->getOneBookByParish($id, $this->authUser->getIdentity()->idParishes);
            $baptism = $this->getBooksTable()->fetchAllBaptismByBook($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = ' . $exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'data' => $book,
            'baptism' => $baptism
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function indexAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getBooksTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(500);
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'data' => $paginator
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }

    public function indexpAction() {
        if (!$this->authenticationService()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
        }
        $paginator = $this->getBooksTable()->fetchAllByIdParish(true, $this->authUser->getIdentity()->idParishes);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(500);
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'data' => $paginator
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
        $form = new BooksForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $booksFilter = new BooksFilter();
            $form->setInputFilter($booksFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $booksFilter->exchangeArray($form->getData());
                $this->getBooksTable()->addBook($booksFilter);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
            }
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'form' => $form,
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
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new BooksparishForm($this->dbAdapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $booksFilter = new BooksFilter();
            $form->setInputFilter($booksFilter->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $booksFilter->exchangeArray($form->getData());
                $this->getBooksTable()->addpBook($booksFilter, $this->authUser->getIdentity()->idParishes);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
            }
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
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
        $messages = null;
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = ' . $id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
        }
        try {
            $book = $this->getBooksTable()->getOneBook($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = ' . $exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
        }

        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new BooksEditForm($this->dbAdapter);
        $form->bind($book);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($book->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->exitBookofSacramentsInDataBase($book)) {
                    $this->getBooksTable()->updatepBook($book, $book->idParish);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/index');
                }else{
                    $messages .= "<p style='color:#a94442' >Error no se puede cambiar la partida inicial porque ya está siendo usada.</p>";
                }
            }
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
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
        $messages = null;
        $id = (int) $this->params()->fromRoute('id', 0);
        error_log('logC id = ' . $id);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
        }
        try {
            $book = $this->getBooksTable()->fetchOneBook($id);
        } catch (\Exception $exception) {
            error_log('logC error exception = ' . $exception);
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
        }
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $form = new BooksparishForm($this->dbAdapter);
        $form->bind($book);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($book->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->exitBookofSacramentsInDataBase($book)) {
                    $this->getBooksTable()->updatepBook($book, $this->authUser->getIdentity()->idParishes);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/sacraments/books/indexp');
                }else{
                    $messages .= "<p style='color:#a94442' >Error no se puede cambiar la partida inicial porque ya está siendo usada.</p>";
                }
            }
        }
        $values = array(
            'title' => 'LIBROS PARROQUIALES',
            'form' => $form,
            'messages' => $messages,
            'id' => $id,
            'url' => $this->getRequest()->getBaseUrl(),
        );
        $this->layout()->setVariable('authUser', $this->authUser);
        $this->layout()->setVariable('parishName', $this->parishName);
        return new ViewModel($values);
    }
    
    private function exitBookofSacramentsInDataBase($book){
        if($book->sacramentName== 'Bautismos'){
            error_log('bau '. $book->id);
            if ($this->getBaptismsTable()->getIdBookofSacrament($book->id))
                return false;
        }
        if($book->sacramentName== 'Confirmaciones'){
            error_log('confi '. $book->id);
            if($this->getConfirmationsTable()->getIdBookofSacrament($book->id))
                return false;
        }
        if($book->sacramentName== 'Matrimonios'){
            error_log('matri '. $book->id);
            if($this->getMarriagesTable()->getIdBookofSacrament($book->id))
                return false;
        }
        return true;
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
        }
        $this->getParishesTable()->deleteParish($id);
        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/archdiocese/parishes/index');
    }

}
