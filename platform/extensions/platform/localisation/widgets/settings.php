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

namespace Platform\Localisation\Widgets;


/*
 * --------------------------------------------------------------------------
 * What we can use in this class.
 * --------------------------------------------------------------------------
 */
use Theme\Theme;


/**
 * --------------------------------------------------------------------------
 * Settings > Widget Class
 * --------------------------------------------------------------------------
 *
 * The settings widget class.
 *
 * @package    Platform
 * @author     Cartalyst LLC
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @license    BSD License (3-clause)
 * @link       http://cartalyst.com
 * @version    1.0
 */
class Settings
{
    /**
     * The validation rules.
     *
     * @access   public
     * @var      array
     */
    public static $validation = array(
        'country'  => 'required',
        'language' => 'required',
        'currency' => 'required',
        'timezone' => 'required'
    );


    /**
     * --------------------------------------------------------------------------
     * Function: index()
     * --------------------------------------------------------------------------
     *
     * Shows the settings form.
     *
     * @access   public
     * @param    array
     * @return   View
     */
    public function index($settings = null)
    {
        // Show the form.
        //
        return Theme::make('platform/localisation::widgets.form.settings')->with('settings', $settings);
    }
}
