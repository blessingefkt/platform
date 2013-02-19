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
        'name'        => 'Themes',
        'author'      => 'Cartalyst LLC',
        'description' => 'Manages your website themes.',
        'version'     => '1.1',
        'is_core'     => true
    ),


    /*
     * -----------------------------------------
     * Extension dependencies.
     * -----------------------------------------
     */
    'dependencies' => array(
        'platform.menus',
        'platform.settings'
    ),


    /*
     * -----------------------------------------
     * Events
     * -----------------------------------------
     */
    'events' => array(
        'theme.create',
        'theme.update',
        'theme.delete'
    ),


    /*
     * -----------------------------------------
     * Rules
     * -----------------------------------------
     */
    'rules' => array(
        'platform/themes::admin.themes@frontend',
        'platform/themes::admin.themes@backend',
        'platform/themes::admin.themes@edit',
        'platform/themes::admin.themes@activate'
    )
);
