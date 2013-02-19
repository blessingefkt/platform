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

class [[namespace_underscore]]_Admin_[[extension_classified]]_Controller extends Admin_Controller
{
    /**
     * Index view, responds to the main admin route for
     * the extension.
     */
    public function get_index()
    {
        $data = array(
            'unique_id' => uniqid(),
        );

        // Show the page.
        //
        return View::make('[[slug_designer]]::index', $data);
    }
}