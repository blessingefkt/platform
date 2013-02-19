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

namespace [[namespace]]\Widgets;

use API;
use Theme;

class Example
{
    public function test()
    {
        return 'Hello from ' . __FILE__;
    }

    public function test2()
    {
        // Initiate a data array.
        //
        $data = array(
            'extensions' => API::get('extensions')
        );

        // Show the page.
        //
        return Theme::make('[[slug_designer]]::widgets.example.test2', $data);
    }
}
