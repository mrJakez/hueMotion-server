<?php defined('SYSPATH') or die('No direct script access.');


/**************************************************************
 * Custom Routes
 **************************************************************/

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