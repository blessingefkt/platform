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
use Installer\Installer;


/**
 * --------------------------------------------------------------------------
 * Installer Class
 * --------------------------------------------------------------------------
 *
 * The Platform Installer.
 *
 * @package    Platform
 * @author     Ben Corlett
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @license    BSD License (3-clause)
 * @link       http://cartalyst.com
 * @version    1.1
 */
class Installer_Install_Controller extends Installer_Base_Controller
{
    /**
     * --------------------------------------------------------------------------
     * Function: before()
     * --------------------------------------------------------------------------
     *
     * This function is called before the action is executed.
     *
     * @access   public
     * @return   mixed
     */
    public function before()
    {
        // Call the parent.
        //
        parent::before();

        // Check if Platform is already installed.
        //
        if (Platform::is_installed() and URI::segment(3) !== 'step_4')
        {
            Redirect::to('installer/install/step_4')->send();
            exit;
        }

        // If we're not prepared for installation.
        //
        if ( ! Installer::is_prepared() and ! in_array(URI::segment(3, 'step_1'), array('step_1', 'permissions')))
        {
            Redirect::to('installer')->send();
            exit;
        }
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_step_1()
     * --------------------------------------------------------------------------
     *
     * This step is a pre-installtion checklist to make sure the system is
     * prepare to be installed.
     *
     * @access   public
     * @return   View
     */
    public function get_step_1()
    {
        // Prepare our database.
        //
        Installer::prepare();

        // Default drivers.
        //
        $drivers = array(
            'Native',
            'Ftp'
        );

        // Show the page.
        //
        return View::make('installer::install.step_1')
            ->with('drivers', $drivers)
            ->with('permissions', Installer::permissions());
    }


    /**
     * -----------------------------------------
     * Function: post_ftp_test()
     * -----------------------------------------
     *
     * Used to test FTP credentials.
     *
     * @access   public
     * @return   object
     */
    public function post_ftp_test()
    {
        // Connect to ftp server.
        //
        $connection_id = @ftp_connect(Input::get('ftp_server'), Input::get('ftp_port'), 90);

        // Now login.
        //
        $response = @ftp_login($connection_id, Input::get('ftp_user'), Input::get('ftp_password'));

        // Send the response back to the client.
        //
        return json_encode(array(
            'connected' => ($response) ? true : false
        ));
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_step_1()
     * --------------------------------------------------------------------------
     *
     * Not used just now. Developers may attach a license agreement or other form
     * data to get_step_1() and process it here.
     *
     * @access   public
     * @return   Redirect
     */
    public function post_step_1()
    {
        // Save the data.
        //
        Installer::remember_step_data(1, Input::get());

        // Continue to step 2.
        //
        return Redirect::to('installer/install/step_2');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_step_2()
     * --------------------------------------------------------------------------
     *
     * This step is to get the database credentials and check if have a sucessful
     * database connection.
     *
     * @access   public
     * @return   View
     */
    public function get_step_2()
    {
        // Initiate the data array.
        //
        $credentials = array(
            'driver'   => null,
            'host'     => null,
            'username' => null,
            'database' => null
        );

        // Check for session data.
        //
        $credentials = array_merge($credentials, Installer::get_step_data(2, function()
        {
            // Look for existing config data.
            //
            $connections = Config::get('database.connections', array());
            $connection = reset($connections);
            return ( is_array($connection) ? $connection : array() );
        }));

        // Show the page.
        //
        return View::make('installer::install.step_2')->with('drivers', Installer::database_drivers())->with('credentials', $credentials);
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_step_2()
     * --------------------------------------------------------------------------
     *
     * Stores the database credentials to the session.
     *
     * @access   public
     * @return   Redirect
     */
    public function post_step_2()
    {
        // Save the data.
        //
        Installer::remember_step_data(2, Input::get());

        // Continue to step 3.
        //
        return Redirect::to('installer/install/step_3');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_step_3()
     * --------------------------------------------------------------------------
     *
     * Get the administrator credentials.
     *
     * @access   public
     * @return   View
     */
    public function get_step_3()
    {
        // Show the page.
        //
        return View::make('installer::install.step_3');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_step_3()
     * --------------------------------------------------------------------------
     *
     * Stores the admin credentials to the session.
     *
     * @method   post
     * @access   public
     * @return   Redirect
     */
    public function post_step_3()
    {
        // Save the data.
        //
        Installer::remember_step_data(3, Input::get());

        // Now install Platform !
        //
        return Redirect::to('installer/install/install');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_install()
     * --------------------------------------------------------------------------
     *
     * Actually does the install process.
     *
     * @access   public
     * @return   void
     */
    public function get_install()
    {
        // Create the Filesystem Config first so we can use for other file creations
        //
        Installer::create_filesystem_config(Installer::get_step_data(1, function() {
            Redirect::to('installer/install/step_1')->send();
            exit;
        }));

        // 1) Create the database config file.
        //
        Installer::create_database_config(Installer::get_step_data(2, function() {
            Redirect::to('installer/install/step_2')->send();
            exit;
        }));

        // 1.1) Update config for this request instance.
        //
        $step2_data = Installer::get_step_data(2);
        Config::set('database.connections.' . $step2_data['driver'], array(
            'driver'   => $step2_data['driver'],
            'host'     => $step2_data['host'],
            'database' => $step2_data['database'],
            'username' => $step2_data['username'],
            'password' => $step2_data['password'],
            'charset'  => 'utf8',
            'prefix'   => ''
        ));

        // 2) Generate the application random key.
        //
        Installer::generate_key();

        // 3) Install extensions.
        //
        Installer::install_extensions();

        // 4) Create the admin user.
        //
        try
        {
            // Get the admin user data.
            //
            $user = Installer::get_step_data(3, function() {
                Redirect::to('installer/install/step_3')->send();
                exit;
            });
            $user = array(
                'email'                 => $user['email'],
                'password'              => $user['password'],
                'password_confirmation' => $user['password_confirmation'],
                'groups'                => array('admin', 'users'),
                'metadata'              => array(
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name']
                ),
                'permissions' => array(
                    Config::get('sentry::sentry.permissions.superuser') => 1
                )
            );

            // Create the admin user.
            //
            API::post('users', $user);

            // Use admin email as default.
            //
            $settings = array(
                'vendor'     => 'platform',
                'extension'  => 'settings',
                'type'       => 'site',
                'name'       => 'email',
                'value'      => $user['email']
            );

            // Update the settings.
            //
            API::put('settings', array('settings' => $settings));

            // Now everything is installed, let's
            // arrange the menu items how we need them.
            $desired_order = array(
                'admin-dashboard',
                'admin-pages',
                'admin-media',
                'admin-users',
                'admin-menus',
                'admin-system' => array(
                    'admin-settings',
                    'admin-extensions',
                    'admin-themes',
                    'admin-localisation',
                    'admin-developers',
                ),
            );

            /**
             * Update the menus order.
             *
             * @todo     Work out why we can't use API::put('menus/admin')
             */
            Installer::order_menu($desired_order);
        }
        catch (APIClientException $e)
        {
            // Redirect to the step 3.
            //
            return Redirect::to('installer/install/step_3');
        }

        // Redirect to the final step.
        //
        return Redirect::to('installer/install/step_4');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: get_step_4()
     * --------------------------------------------------------------------------
     *
     * The completion step.
     *
     * @access   public
     * @return   View
     */
    public function get_step_4()
    {
        //
        //
        Session::forget(Config::get('installer::installer.session_key', 'installer'));

        // Show the page.
        //
        return View::make('installer::install.step_4')->with('license', Platform::license());
    }


    /**
     * Returns a JSON encoded array of filesystem
     * permissions.
     *
     * @return  Response
     */
    public function get_permissions()
    {
        if ( ! Request::ajax())
        {
            return $this->get_index();
        }

        return new Response(json_encode(Installer::permissions()));
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_confirm_db()
     * --------------------------------------------------------------------------
     *
     * Confirm database - Step 1
     *
     * @access   public
     * @return   Response
     */
    public function post_confirm_db()
    {
        // If this is not an Ajax request.
        //
        if ( ! Request::ajax())
        {
            return Event::fire('404');
        }

        try
        {
            Installer::check_database_connection(array(
                'driver'   => Input::get('driver'),
                'host'     => Input::get('host'),
                'database' => Input::get('database'),
                'username' => Input::get('username'),
                'password' => Input::get('password')
            ));
        }
        catch (Exception $e)
        {
            // Error 1146 is actually good, because it means we connected fine,
            // just couldn't get the contents of the random table above.
            // For some reason this exception has a code of "0"
            // whereas all of the other exceptions match the
            // database errors. Life goes on.
            if ($e->getCode() !== 0)
            {
                return new Response(json_encode(array(
                    'message' => $e->getMessage()
                )), API::STATUS_BAD_REQUEST);
            }
        }

        return json_encode(array(
            'message' => 'Successfully connected to the database'
        ));
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_confirm_user()
     * --------------------------------------------------------------------------
     *
     * Confirm user - Step 3
     *
     * @access   public
     * @return   Response
     */
    public function post_confirm_user()
    {
        // If this is not an Ajax request.
        //
        if ( ! Request::ajax())
        {
            return Event::fire('404');
        }

        // Prepare the user data.
        //
        $user = array(
            'email'                 => Input::get('email'),
            'password'              => Input::get('password'),
            'password_confirmation' => Input::get('password_confirmation'),
            'metadata'              => array(
                'first_name' => Input::get('first_name'),
                'last_name'  => Input::get('last_name')
            )
        );

        // Declare the rules for the validator.
        //
        $rules = array(
            'metadata.first_name'   => 'required',
            'metadata.last_name'    => 'required',
            'email'                 => 'required|email',
            'password_confirmation' => 'same:password'
        );

        // Run the validator.
        //
        $validation = Validator::make($user, $rules);

        // Validation failed ?
        //
        if ($validation->fails())
        {
            return json_encode(array(
                'error'   => true,
                'message' => $validation->errors->all(':message')
            ));
        }

        // Admin user created with success.
        //
        return json_encode(array(
            'error'   => false,
            'message' => array('Successfully validated user')
        ));
    }
}
