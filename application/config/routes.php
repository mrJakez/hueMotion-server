<?php defined('SYSPATH') or die('No direct script access.');


/**************************************************************
 * Custom Routes
 **************************************************************/


Route::set('worker_index', 'worker/index')
    ->defaults(array(
        'controller' => 'worker',
        'action'     => 'index',
    ));

/**************************************************************
 * Default Router
 **************************************************************/


Route::set('default', '(<controller>(/<action>))')
    ->defaults(array(
        'controller' => 'welcome',
        'action'     => 'index',
    ));