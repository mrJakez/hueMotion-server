<?php defined('SYSPATH') or die('No direct script access.');


/**************************************************************
 * Custom Routes
 **************************************************************/

Route::set('Configuration', 'api/configuration')
    ->defaults(array(
        'controller' => 'configuration',
        'action'     => 'index',
    ));

Route::set('action', 'api/action(/<action_id>)')
    ->defaults(array(
        'controller' => 'action',
        'action'     => 'index',
    ));

Route::set('actionGroup', 'api/actiongroup')
    ->defaults(array(
        'controller' => 'actionGroup',
        'action'     => 'index',
    ));

Route::set('Variable', 'api/variable(/<variable_id>)')
    ->defaults(array(
        'controller' => 'variable',
        'action'     => 'index',
    ));




Route::set('worker_initialize', 'worker/initialize')
    ->defaults(array(
        'controller' => 'worker',
        'action'     => 'initialize',
    ));

Route::set('worker_process', 'worker/process')
    ->defaults(array(
        'controller' => 'worker',
        'action'     => 'process',
    ));

/**************************************************************
 * Default Router
 **************************************************************/


Route::set('default', '(<controller>(/<action>))')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));