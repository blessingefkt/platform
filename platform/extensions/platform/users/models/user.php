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

namespace Platform\Users;

use Crud;
use Event;
use Sentry;
use SentryException;

class User extends Crud
{

	/**
	 * The primary key for the model on the database table.
	 *
	 * @var string
	 */
	protected static $_key = 'id';

	/**
	 * Indicates if the model has update and creation timestamps.
	 *
	 * @var bool
	 */
	protected static $_timestamps = true;

	/**
	 * @var  array  $rules  Validation rules for model attributes
	 */
	protected static $_rules = array(
		'metadata.first_name'   => 'required',
		'metadata.last_name'    => 'required',
		'email'                 => 'required|unique:users',
		'password'              => 'required',
		'password_confirmation' => 'required|same:password',
	);

	// we use this to set metdata
	protected static $_fields = array(
		'id', 'email', 'password', 'password_reset_hash', 'temp_password',
		'remember_me', 'activation_hash', 'last_login', 'updated_at', 'created_at',
		'status', 'suspended_timestamp', 'activated', 'count', 'groups', 'metadata'
	);

	/**
	 * Save the model instance to the database.
	 *
	 * @return bool
	 */
	public function save($events = array('before', 'after'))
	{
		// First check if we want timestamps as this will append to attributes
		if (static::$_timestamps)
		{
			$this->timestamp();
		}

		// now we grab the attributes
		$attributes = $this->attributes();

		// run validation if rules are set
		if ( ! empty(static::$_rules))
		{
			$validated = $this->run_validation($attributes, static::$_rules);

			if ( ! $validated )
			{
				return false;
			}
		}

		// See if creation is a registration
		$register = (isset($attributes['register'])) ? $attributes['register'] : false;

		// Prep attribute values after validation is done
		$attributes = $this->prep_attributes($attributes);

		$groups = array();

		if (array_key_exists('groups', $attributes))
		{
			if ( ! empty($attributes['groups']))
			{
				$groups = $attributes['groups'];
			}

			if ( ! is_array($attributes['groups']) and ! empty($attributes['groups']))
			{
				$groups = array($groups);
			}

			unset($attributes['groups']);
		}

		// If the model exists, we only need to update it in the database, and the update
		// will be considered successful if there is one affected row returned from the
		// fluent query instance. We'll set the where condition automatically.
		if ( ! $this->is_new())
		{
			// make sure a key is set then grab and remove it from the attributes array
			if ( ! isset($attributes[static::key()]) or empty($attributes[static::key()]))
			{
				// the key is not set or empty, throw an exception
				throw new \Exception('A primary key is required to update.');
			}

			// grab the key and remove it from the attributes array
			$key = $attributes[static::key()];
			unset($attributes[static::key()]);

			try
			{
				$user = Sentry::user((int) $key);

				// get user groups
				$user_groups = array();
				$current_groups = $user->groups();
				foreach ($current_groups as $group)
				{
					$user_groups[] = $group['name'];
				}

				// get the difference in group arrays
				$add_groups = array_diff($groups, $user_groups);
				$remove_groups = array_diff($user_groups, $groups);

				// add user to groups
				foreach ($add_groups as $group)
				{
					$user->add_to_group($group);
				}

				// remove user from groups
				foreach ($remove_groups as $group)
				{
					$user->remove_from_group($group);
				}

				if (in_array('before', $events))
				{
					list($query, $attributes) = $this->before_update(null, $attributes);
				}

				$result = Sentry::user((int) $key)->update($attributes) === true;

				if (in_array('after', $events))
				{
					$result = $this->after_update($result);
				}

				if (static::$_events)
				{
					// fire update event
					Event::fire(static::event().'.update', array($this));
				}
			}
			catch (SentryException $e)
			{
				throw new \Exception($e->getMessage());
			}

		}

		// If the model does not exist, we will insert the record and retrieve the last
		// insert ID that is associated with the model. If the ID returned is numeric
		// then we can consider the insert successful.
		else
		{
			try
			{
				if (in_array('before', $events))
				{
					list($query, $attributes) = $this->before_insert(null, $attributes);
				}

				if ($register)
				{
					$result = Sentry::user()->register($attributes);

					if (in_array('after', $events))
					{
						$result = $this->after_insert($result);
					}

					$user_id = (int) $result['id'];
				}
				else
				{
					$result = Sentry::user()->create($attributes);

					if (in_array('after', $events))
					{
						$result = $this->after_insert($result);
					}

					$user_id = (int) $result;
				}

				$user = Sentry::user($user_id);

				// add user to groups
				foreach ($groups as $group)
				{
					$user->add_to_group($group);
				}

				$this->is_new( ! (bool) $user_id);

				$attributes['id'] = (int) $user_id;
				$this->fill($attributes);

				if (static::$_events)
				{
					// fire create event
					Event::fire(static::event().'.create', array($this));
				}
			}
			catch (SentryException $e)
			{
				throw new \exception($e->getMessage());
			}

		}

		return $result;
	}

	/**
	 * Delete a model from the datatabase
	 *
	 * @return  bool
	 */
	public function delete($events = array('before', 'after'))
	{
		// make sure a key is set then grab and remove it from the attributes array
		if ( ! isset($this->{static::key()}) or empty($this->{static::key()}))
		{
			// the key is not set or empty, throw an exception
			throw new \Exception('A primary key is required to delete.');
		}

		try
		{
			if (in_array('before', $events))
			{
				$this->before_delete(null);
			}

			$result = Sentry::user((int) $this->{static::key()})->delete();

			if (static::$_events)
			{
				// fire delete event
				Event::fire(static::event().'.delete', array($this));
			}

			if (in_array('after', $events))
			{
				$result = $this->after_delete($result);
			}

			return $result;
		}
		catch(SentryException $e)
		{
			throw new \Exception($e->getMessage());
		}
	}

	/**
	 * Called right after validation before inserting/updating to the database
	 *
	 * @param   array  $attributes  attribute array
	 * @return  array
	 */
	protected function prep_attributes($attributes)
	{
		// unset confirmation values
		unset($attributes['password_confirmation']);
		unset($attributes['email_confirmation']);
		unset($attributes['register']);

		if ( ! $this->is_new() and empty($attributes['password']))
		{
			unset($attributes['password']);
		}

		return $attributes;
	}

	/**
	 * Dynamically set the value of an attribute.
	 */
	public function __set($key, $value)
	{
		if ($key == 'metadata')
		{
			if (is_array($value))
			{
				foreach ($value as $key => $val)
				{
					$this->metadata[$key] = $val;
				}
			}
		}
		else
		{
			$this->{$key} = $value;
		}
	}

	/**
	 * Helper Methods
	 */

	/**
	 * Gets called before find() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @return  array  $query object and $columns array
	 */
	protected function before_find($query, $columns)
	{
		$query = $query
			->left_join('users_metadata', 'users.id', '=', 'users_metadata.user_id')
			->left_join('users_groups', 'users.id', '=', 'users_groups.user_id')
			->left_join('groups', 'users_groups.group_id', '=', 'groups.id')
			->group_by('users.id');

		$columns = array('*', 'users.id as id', 'groups.id as groups_id');

		return array($query, $columns);
	}

	/**
	 * Gets call after the find() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   Query  $query
	 * @param   array  $columns
	 * @return  array
	 */
	protected function after_find($result)
	{
		if ($result)
		{
			$result->metadata = array();

			foreach ($result as $key => $val)
			{
				if ( ! in_array($key, static::$_fields))
				{
					$result->metadata[$key] = $val;
					unset($result->{$key});
				}
				elseif (in_array($key, array('status', 'activated')))
				{
					$result->$key = (bool) $val;
				}
			}
		}

		return $result;
	}

	/**
	 * Gets called before the validation is ran.
	 *
	 * @param   array  $data  The validation data
	 * @return  array
	 */
	protected function before_validation($data, $rules)
	{
		// if not new, adjust rules
		if ( ! $this->is_new())
		{
			// add id to the unique clause to prevent errors if same email
			$rules['email'] .= ',email,'.$data['id'];

			// if password is empty, remove it from rules and dataset, not updating
			if (empty($data['password']))
			{
				unset($rules['password']);
				unset($rules['password_confirmation']);
			}
		}

		// if registering, add email confirm
		if (isset($data['register']) and $data['register'])
		{
			$rules['email'] = 'required|email';
			$rules['email_confirmation'] = 'required|email|same:email';
		}

		// if just updating permissions, remove rules
		if (isset($data['id']) and isset($data['permissions']) and count($data) == 3)
		{
			$rules = array();
		}

		return array($data, $rules);
	}

	/**
	 * Gets called before all() is executed to modify the query
	 * Must return an array of the query object and columns array($query, $columns)
	 *
	 * @return  array  $query object and $columns array
	 */
	protected static function before_all($query, $columns)
	{
		$query = $query
			->left_join('users_metadata', 'users.id', '=', 'users_metadata.user_id')
			->left_join('users_groups', 'users.id', '=', 'users_groups.user_id')
			->left_join('groups', 'users_groups.group_id', '=', 'groups.id')
			->group_by('users.id');

		return array($query, $columns);
	}

	/**
	 * Gets called after the all() query is exectuted to modify the result
	 * Must return a proper result
	 *
	 * @param   array  $results
	 * @return  array  $results
	 */
	protected static function after_all($results)
	{
		foreach ($results as &$user)
		{
			$user->metadata = array();
			foreach ($user as $key => $val)
			{
				if ( ! in_array($key, static::$_fields))
				{
					$user->metadata[$key] = $val;
					unset($user->{$key});
				}
				elseif (in_array($key, array('status', 'activated')))
				{
					$user->$key = (bool) $val;
				}
			}
		}

		return $results;
	}

	/**
	 * Custom Methods
	 */

	/**
	 * Find Custom
	 *
	 * @param  array  select
	 * @param  array  where
	 * @param  array  order by
	 * @param  int    take/limit
	 * @param  int    skip/offset
	 * @return object Model Object
	 */
	public static function find_custom($select, $where, $order_by, $take, $skip)
	{
		$model = new static;

		list($query, $select) = $model->before_all($model->query(), $select);

		if ( ! empty($where))
		{
			if (is_array($where[0]))
			{
				foreach ($where as $where)
				{
					$query = $query->where($where[0], $where[1], $where[2]);
				}
			}
			else
			{
				$where = $where;
				$query = $query->where($where[0], $where[1], $where[2]);
			}
		}

		if ( is_array($order_by))
		{
			foreach ($order_by as $_field => $_direction)
			{
				$query = $query->order_by($_field, $_direction);
			}
		}
		else
		{
			$query = $query->order_by($order_by);
		}

		if ($take !== null)
		{
			$query = $query->take($take)->skip($skip);
		}

		$result = $query
			->get($select);

		$models = array();
		foreach($result as $r)
		{
			$models[] = new static($r, true);
		}

		return $model->after_all($models);
	}

}
