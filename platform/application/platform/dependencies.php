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


/**
 * --------------------------------------------------------------------------
 * Dependencies Class
 * --------------------------------------------------------------------------
 *
 * Sort the extensions dependencies.
 *
 * @package    Platform
 * @author     Cartalyst LLC
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @license    BSD License (3-clause)
 * @link       http://cartalyst.com
 * @version    1.1
 */
class Dependencies
{
    /**
     * Stores the items.
     *
     * @access   private
     * @var      array
     */
    private static $items = array();

    /**
     * Stores the dependencies.
     *
     * @access   private
     * @var      array
     */
    private static $dependencies = array();


    /**
     * --------------------------------------------------------------------------
     * Function: sort()
     * --------------------------------------------------------------------------
     *
     * Prepares the items dependencies to be sorted.
     *
     * @access   public
     * @param    array
     * @param    string
     * @return   array
     */
    public static function sort($items = null, $key = 'dependencies')
    {
        // Make sure we have items.
        //
        if (is_null($items) or ! is_array($items) or empty($items))
        {
            return false;
        }

        // Spin through the items.
        //
        foreach ($items as $item => $data)
        {
            // Add this item to the aray.
            //
            static::$items[] = $item;

            // Get this item dependencies.
            //
            $dependencies = ( (isset($data[$key]) and is_array($data[$key]) and ! empty($data[$key])) ? $data[$key] : array() );

            // Store this item dependencies.
            //
            static::$dependencies[ $item ] = $dependencies;
        }

        // Return the dependencies in the proper order.
        //
        return static::_sort();
    }


    /**
     * --------------------------------------------------------------------------
     * Function: _sort()
     * --------------------------------------------------------------------------
     *
     * This sorts the extensions dependencies.
     *
     * @access   private
     * @return   array
     */
    private static function _sort()
    {
        // Initiate an empty array, so we can save the sorted dependencies.
        //
        $sorted = array();

        // Initiate a flag.
        //
        $changed = true;

        // Make some checks and loops =)
        //
        while (count($sorted) < count(static::$items) && $changed === true)
        {
            // Mark the flag as false.
            //
            $changed = false;

            // Spin through the dependencies.
            //
            foreach (static::$dependencies as $item => $dependencies)
            {
                // Check if this item has all the dependencies.
                //
                if (static::validate($item, $sorted))
                {
                    $sorted[] = $item;
                    unset(static::$dependencies[ $item ]);
                    $changed = true;
                }
            }
        }

        // Return the sorted dependencies.
        //
        return array_filter($sorted);
    }


    /**
     * --------------------------------------------------------------------------
     * Function: validate()
     * --------------------------------------------------------------------------
     *
     * This validates if an item is valid.
     *
     * @access   private
     * @param    string
     * @param    array
     * @return   boolean
     */
    private static function validate($item = null, $sorted = array())
    {
        // Spin through this item dependencies.
        //
        foreach (static::$dependencies[ $item ] as $dependency)
        {
            // Check if this dependency exists.
            //
            if ( ! in_array($dependency, $sorted))
            {
                // Item is invalid.
                //
                return false;
            }
        }

        // Item is valid.
        //
        return true;
    }
}
