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

namespace Platform\Localisation\Model;


/*
 * --------------------------------------------------------------------------
 * What we can use in this class.
 * --------------------------------------------------------------------------
 */
use Crud;


/**
 * --------------------------------------------------------------------------
 * Language model Class
 * --------------------------------------------------------------------------
 *
 * Model to manage languages.
 *
 * @package    Platform
 * @author     Cartalyst LLC
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @license    BSD License (3-clause)
 * @link       http://cartalyst.com
 * @version    1.0
 */
class Language extends Crud
{
    /**
     * Rules to be validated when creating or updating countries.
     *
     * @access   public
     * @param    array
     */
    public static $_rules = array(
        'name'         => 'required',
        'abbreviation' => 'required|size:2|unique:languages,abbreviation',
        'locale'       => 'required',
        'status'       => 'required'
    );


    /**
     * --------------------------------------------------------------------------
     * Function: set_validation()
     * --------------------------------------------------------------------------
     *
     * Updates or adds new rules to be validated.
     *
     * @access   public
     * @param    array
     * @return   boolean
     */
    public static function set_validation($rules)
    {
        // Make sure we have an array.
        //
        if ( ! is_array($rules))
        {
            return false;
        }

        // Loop through the rules.
        //
        foreach ($rules as $value => $rule)
        {
            // Set or update the rule.
            //
            static::$_rules[ $value ] = $rule;
        }

        // Done.
        //
        return true;
    }


    /**
     * --------------------------------------------------------------------------
     * Function: find()
     * --------------------------------------------------------------------------
     *
     * A custom method to find languages, we can use the language id, language
     * abbreviation or the language slug to return language information.
     *
     * @access   public
     * @param    mixed
     * @param    array
     * @param    array
     * @return   object
     */
    public static function find($condition = 'first', $columns = array('*'), $events = array('before', 'after'))
    {
        // Do we have the language id ?
        //
        if (is_numeric($condition) and ! in_array($condition, array('first', 'last')))
        {
            // Execute the query.
            //
            return parent::find(function($query) use ($condition)
            {
                return $query->where('id', '=', $condition);
            }, $columns, $events);
        }

        // Do we have the language abbreviation ?
        //
        elseif (strlen($condition) === 2)
        {
            // Execute the query.
            //
            return parent::find(function($query) use ($condition)
            {
                return $query->where('abbreviation', '=', strtoupper($condition));
            }, $columns, $events);
        }

        // We must have the language slug.
        //
        elseif( ! in_array($condition, array('first', 'last')) )
        {
            // Execute the query.
            //
            return parent::find(function($query) use ($condition)
            {
                return $query->where('slug', '=', strtolower($condition));
            }, $columns, $events);
        }

        // Call parent.
        //
        return parent::find($condition, $columns, $events);
    }
}
