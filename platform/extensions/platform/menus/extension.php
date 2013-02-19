<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    1.1.4
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */


/*
 * --------------------------------------------------------------------------
 * Return the extension data.
 * --------------------------------------------------------------------------
 */
return array(
    /*
     * -----------------------------------------
     * Extension information.
     * -----------------------------------------
     */
    'info' => array(
        'name'        => 'Menus',
        'author'      => 'Cartalyst LLC',
        'description' => 'Manages all menus throughout the website admin.',
        'version'     => '1.1.1',
        'is_core'     => true
    ),


    /*
     * -----------------------------------------
     * Events
     * -----------------------------------------
     */
    'events' => array(
        'menu.create',
        'menu.update',
        'menu.delete'
    ),


    /*
     * -----------------------------------------
     * Rules
     * -----------------------------------------
     */
    'rules' => array(
        'platform/menus::admin.menus@index',
        'platform/menus::admin.menus@create',
        'platform/menus::admin.menus@edit',
        'platform/menus::admin.menus@delete'
    )
);
