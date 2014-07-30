<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'guest'=> array(
        '/application/index/index',
        '/application/index/send',
    ),
    'adminParroquia'=> array(
        '/application/index/index',
        '/application/index/logout',
        '/application/index/lead',
        '/users/user/view',
        '/archdiocese/vicarious/index',
        '/archdiocese/vicarious/view',
        '/sacraments/baptisms/indexp',
        '/sacraments/baptisms/editp',
        '/sacraments/baptisms/addp',
        '/sacraments/baptisms/viewp',
        '/sacraments/baptisms/printp',
    ),
    'ayuParroquia'=> array(
        '/application/index/index',
        '/application/index/logout',
        '/application/index/lead',
        '/users/user/view',
        '/archdiocese/vicarious/index',
        '/archdiocese/vicarious/view',
        '/sacraments/baptisms/indexp',
        '/sacraments/baptisms/addp',
        '/sacraments/baptisms/editp',
        '/sacraments/baptisms/viewp',
        '/sacraments/baptisms/printp',
    ),
    'adminArchivos'=> array(
        '/application/index/index',
        '/application/index/logout',
        '/application/index/lead',
        '/users/user/view',
        '/archdiocese/vicarious/index',
        '/archdiocese/vicarious/view',
        '/sacraments/baptisms/index',
        '/sacraments/baptisms/edit',
        '/sacraments/baptisms/add',
        '/sacraments/baptisms/view',
        '/sacraments/baptisms/print',
    ),    
    'adminArzobispado'=> array(
        '/application/index/index',
        '/application/index/send',
        '/application/index/logout',
        '/application/index/lead',
        '/users/user/load',
        '/users/user/index',
        '/users/user/add',
        '/users/user/view',
        '/users/user/edit',
        '/archdiocese/vicarious/index',
        '/archdiocese/vicarious/add',
        '/archdiocese/vicarious/view',
        '/archdiocese/vicarious/edit',
        '/sacraments/baptisms/index',
        '/sacraments/baptisms/add',
        '/sacraments/baptisms/view',
        '/sacraments/baptisms/edit',
        '/sacraments/baptisms/print',
        '/sacraments/baptisms/signDocument',
        '/sacraments/baptisms/getBookSacrament',
        '/sacraments/baptisms/getBornIn',
        '/sacraments/baptisms/getElementBookSacrament',
        '/sacraments/baptisms/indexp',
        '/sacraments/baptisms/viewp',
        '/sacraments/baptisms/addp',
        '/sacraments/baptisms/editp',
        '/sacraments/baptisms/printp',
    ),
);
