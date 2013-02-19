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
 * What we can use in this class.
 * --------------------------------------------------------------------------
 */
use Platform\Localisation\Model\Country;


/**
 * --------------------------------------------------------------------------
 * Localisation > Countries > API Class
 * --------------------------------------------------------------------------
 *
 * Manage the countries.
 *
 * @package    Platform
 * @author     Cartalyst LLC
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @license    BSD License (3-clause)
 * @link       http://cartalyst.com
 * @version    1.0
 */
class Platform_Localisation_API_Countries_Controller extends API_Controller
{
    /**
     * --------------------------------------------------------------------------
     * Function: get_index()
     * --------------------------------------------------------------------------
     *
     * Returns an array of all the countries.
     *
     * If you want to retrieve information about a specific country, you can
     * pass the country iso code 2 or the country slug as the last parameter.
     *
     *  <code>
     *      $all_countries = API::get('localisation/countries');
     *      $gb_country    = API::get('localisation/country/232');
     *      $gb_country    = API::get('localisation/country/gb');
     *      $gb_country    = API::get('localisation/country/united-kingdom');
     *  </code>
     *
     * @access   public
     * @param    mixed
     * @return   Response
     */
    public function get_index($country_code = false)
    {
        // If we have the country code, we return the information about that country.
        //
        if ($country_code != false)
        {
            // Get this country information.
            //
            $country = Country::find($country_code);

            // Check if the country exists.
            //
            if (is_null($country))
            {
                // Country not found.
                //
                return new Response(array(
                    'message' => Lang::line('platform/localisation::countries/message.not_found', array('country' => $country_code))->get()
                ), API::STATUS_NOT_FOUND);
            }

            // Get the list of regions of this country.
            //
            #$regions = API::get('localisation/regions/' . $country_code);
            $regions = array();

            // Check if this country is being used by the system.
            //
            $country['default'] = ( $country['iso_code_2'] === strtoupper(Platform::get('platform/localisation::site.country')) ? true : false );

            // Add the regions to the country array.
            //
            $country['regions'] = $regions;

            // Return the country information.
            //
            return new Response($country);
        }

        // Get and return all the countries.
        //
        return new Response(Country::all());
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_index()
     * --------------------------------------------------------------------------
     *
     * Creates a new country.
     *
     *  <code>
     *      API::post('localisation/country');
     *  </code>
     *
     * @access   public
     * @return   Response
     */
    public function post_index()
    {
        // Create the country.
        //
        $country = new Country();

        // Update the country data.
        //
        $country->name               = Input::get('name');
        $country->slug               = \Str::slug(Input::get('name'));
        $country->iso_code_2         = Input::get('iso_code_2');
        $country->iso_code_3         = Input::get('iso_code_3');
        $country->iso_code_numeric_3 = Input::get('iso_code_numeric_3');
        $country->region             = Input::get('region');
        $country->subregion          = Input::get('subregion');
        $country->currency           = Input::get('currency');
        $country->status             = Input::get('status');

        try
        {
            // Save the country.
            //
            if ($country->save())
            {
                // Return a response.
                //
                return new Response(array(
                    'message' => Lang::line('platform/localisation::countries/message.create.success', array('country' => $country->name))->get(),
                    'slug'    => $country->slug
                ), API::STATUS_CREATED);
            }

            // An error occurred.
            //
            else
            {
                // Return a response.
                //
                return new Response(array(
                    'message' => Lang::line('platform/localisation::countries/message.create.fail')->get(),
                    'errors'  => ($country->validation()->errors->has()) ? $country->validation()->errors->all() : array()
                ), ($country->validation()->errors->has()) ? API::STATUS_BAD_REQUEST : API::STATUS_UNPROCESSABLE_ENTITY);
            }
        }
        catch (Exception $e)
        {
            // Return a response.
            //
            return new Response(array(
                'message' => $e->getMessage()
            ), API::STATUS_BAD_REQUEST);
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Function: put_index()
     * --------------------------------------------------------------------------
     *
     * Edits a given country using the provided country id, country code
     * or by using the country slug.
     *
     *  <code>
     *      $country = API::put('localisation/country/232');
     *      $country = API::put('localisation/country/gb');
     *      $country = API::put('localisation/country/united-kingdom');
     *  </code>
     *
     * @access   public
     * @param    mixed
     * @return   Response
     */
    public function put_index($country_code)
    {
        // Get this country information.
        //
        $country = Country::find($country_code);

        // Now update the rules.
        //
        Country::set_validation(array(
            'iso_code_2'         => 'required|size:2|unique:countries,iso_code_2,' . $country->iso_code_2 . ',iso_code_2',
            'iso_code_3'         => 'required|size:3|unique:countries,iso_code_3,' . $country->iso_code_3 . ',iso_code_3',
            'iso_code_numeric_3' => 'required|numeric|unique:countries,iso_code_numeric_3,' . $country->iso_code_numeric_3 . ',iso_code_numeric_3'
        ));

        // Update the country data.
        //
        $country->name               = Input::get('name');
        $country->slug               = \Str::slug(Input::get('name'));
        $country->iso_code_2         = Input::get('iso_code_2');
        $country->iso_code_3         = Input::get('iso_code_3');
        $country->iso_code_numeric_3 = Input::get('iso_code_numeric_3');
        $country->region             = ( Input::get('region') ?: $country['region'] );
        $country->subregion          = ( Input::get('subregion') ?: $country['subregion'] );
        $country->currency           = ( Input::get('currency') ?: $country['currency'] );
        $country->status             = ( $country['iso_code_2'] === Platform::get('platform/localisation::site.country') ? 1 : Input::get('status') );

        try
        {
            // Update the country.
            //
            if ($country->save())
            {
                // Return a response.
                //
                return new Response(array(
                    'slug'    => $country->slug,
                    'message' => Lang::line('platform/localisation::countries/message.update.success', array('country' => $country['name']))->get()
                ));
            }
            else
            {
                // Return a response.
                //
                return new Response(array(
                    'message' => Lang::line('platform/localisation::countries/message.update.fail', array('country' => $country['name']))->get(),
                    'errors'  => ($country->validation()->errors->has()) ? $country->validation()->errors->all() : array()
                ), ($country->validation()->errors->has()) ? API::STATUS_BAD_REQUEST : API::STATUS_UNPROCESSABLE_ENTITY);
            }
        }
        catch (Exception $e)
        {
            // Return a response.
            //
            return new Response(array(
                'message' => $e->getMessage()
            ), API::STATUS_BAD_REQUEST);
        }
    }



    /**
     * --------------------------------------------------------------------------
     * Function: delete_index()
     * --------------------------------------------------------------------------
     *
     * Deletes a given country using the provided country id, country code
     * or by using the country slug.
     *
     *  <code>
     *      $country = API::delete('localisation/country/232');
     *      $country = API::delete('localisation/country/gb');
     *      $country = API::delete('localisation/country/united-kingdom');
     *  </code>
     *
     * @access   public
     * @param    mixed
     * @return   Response
     */
    public function delete_index($country_code)
    {
        try
        {
            // Get this country information.
            //
            $country = Country::find($country_code);
        }
        catch (Exception $e)
        {
            // Return a response.
            //
            return new Response(array(
                'message' => Lang::line('platform/localisation::countries/message.not_found', array('country' => $country_code))->get()
            ), API::STATUS_NOT_FOUND);
        }

        // Check if this is a default country.
        //
        if ($country['iso_code_2'] === Platform::get('platform/localisation::site.country'))
        {
            // Return a response.
            //
            return new Response( array(
                'message' => Lang::line('platform/localisation::countries/message.delete.being_used')->get()
            ), API::STATUS_BAD_REQUEST);
        }

        // Try to delete the country.
        //
        try
        {
            // Delete the country.
            //
            $country->delete();

            // Return a response.
            //
            return new Response(array(
                'message' => Lang::line('platform/localisation::countries/message.delete.success', array('country' => $country->name))->get()
            ));
        }
        catch (Exception $e)
        {
            // Return a response.
            //
            return new Response( array(
                'message' => Lang::line('platform/localisation::countries/message.delete.fail', array('country' => $country->name))->get()
            ), API::STATUS_BAD_REQUEST);
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_datatable()
     * --------------------------------------------------------------------------
     *
     * Returns fields required for a Platform.table.
     *
     *  <code>
     *      API::get('localisation/countries/datatable');
     *  </code>
     *
     * @access   public
     * @param    string
     * @return   Response
     */
    public function get_datatable()
    {
        // Get the default country.
        //
        $default_country = strtoupper(Platform::get('platform/localisation::site.country'));


        $defaults = array(
            'select'   => array(
                'countries.id'         => 'ID',
                'countries.name'       => 'Name',
                'countries.iso_code_2' => 'ISO Code 2',
                'countries.slug'       => 'slug'
            ),
            'where'    => array(),
            'order_by' => array('countries.name' => 'asc')
        );

        // Count the total of countries.
        //
        $count_total = Country::count();

        // get the filtered count
        // we set to distinct because a user can be in multiple groups
        $count_filtered = Country::count_distinct('countries.id', function($query) use ($defaults)
        {
            // sets the where clause from passed settings
            return Table::count($query, $defaults);
        });

        // Set the pagination.
        //
        $paging = Table::prep_paging($count_filtered, 20);

        // Get the countries.
        //
        $items = Country::all(function($query) use ($defaults, $paging)
        {
            list($query, $columns) = Table::query($query, $defaults, $paging);

            return $query->select($columns);
        });


        // Return our data.
        //
        return new Response(array(
            'rows'            => ( $items ?: array() ),
            'count'           => $count_total,
            'count_filtered'  => $count_filtered,
            'paging'          => $paging,
            'default_country' => $default_country
        ));
    }


    /**
     * --------------------------------------------------------------------------
     * Function: put_primary()
     * --------------------------------------------------------------------------
     *
     * Makes a country the primary country.
     *
     *  <code>
     *      $country = API::put('localisation/country/primary/232');
     *      $country = API::put('localisation/country/primary/gb');
     *      $country = API::put('localisation/country/primary/united-kingdom');
     *  </code>
     *
     * @access   public
     * @param    mixed
     * @return   Response
     */
    public function put_primary($country_code)
    {
        // Get this country information.
        //
        $country = Country::find($country_code);

        // Check if the country exists.
        //
        if (is_null($country))
        {
            // Return a response.
            //
            return new Response(array(
                'message' => Lang::line('platform/localisation::countries/message.not_found', array('country' => $country_code))->get()
            ), API::STATUS_NOT_FOUND);
        }

        // Is this country the default already ?
        //
        if ($country['iso_code_2'] === Platform::get('platform/localisation::site.country'))
        {
            // Return a response.
            //
            return new Response(array(
                'message' => Lang::line('platform/localisation::countries/message.update.already_primary', array('country' => $country['name']))->get()
            ));
        }

        // Update the settings table.
        //
        DB::table('settings')
            ->where('extension', '=', 'localisation')
            ->where('type', '=', 'site')
            ->where('name', '=', 'country')
            ->update(array('value' => $country['iso_code_2']));

        // Return a response.
        //
        return new Response(array(
            'message' => Lang::line('platform/localisation::countries/message.update.primary', array('country' => $country->name))->get()
        ));
    }
}
