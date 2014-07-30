<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Archdiocese\Controller\Vicarious' => 'Archdiocese\Controller\VicariousController',
            'Archdiocese\Controller\Parishes' => 'Archdiocese\Controller\ParishesController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'archdiocese' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/archdiocese',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        'routeACL' => '/archdiocese/vicarious',
                        '__NAMESPACE__' => 'Archdiocese\Controller',
                        'controller'    => 'Vicarious',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Archdiocese' => __DIR__ . '/../view',
        ),
    ),
);
