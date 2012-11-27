<?php
/**
 * @version		$Id: example.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	JFramework
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
require_once( JPATH_SITE .'/includes/hd2/user.php' );
require_once( JPATH_SITE .'/includes/hd2/constants.php' );
require_once( JPATH_SITE .'/includes/hd2/shared.php' );

/**
 * HD2 Authentication Plugin
 *
 * 	!! REPLACE Joomla Plugin !!
 *		can't just be added on, Joomla doesn't work that way, so incorporate Joomla code
 *		and add our own check
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 1.5
 */
class plgAuthenticationHd2 extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object	$subject	The object to observe
	 * @param	array	$config		An array that holds the plugin configuration
	 * @since	1.5
	 */
	function plgAuthenticationHd2(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function authenticateHd2( $id, $user) {
	global $studyID;
	
		$success = true;
		$conf =& JFactory::getConfig();
		$host	= $conf->getValue('config.host');
		$database	= $conf->getValue('config.db');
		
		$password 	= $conf->getValue('config.password');
		$user 	= $conf->getValue('config.user');


		$userMsqlDB = mysql_connect($host, $user, $password, true);
		if (!$userMsqlDB ) {
			return false;
		}
		if ( mysql_select_db( USERDB)) {
				// First verify that this user has been entered in the userInfo table. Else 
				// not a legitimate study participant!
				$sql =  "SELECT studyID FROM userInfo WHERE joomlaID=$id LIMIT 1";
				// die($sql);
				
				$result = mysql_query($sql, $userMsqlDB ) ;
				
				if ($result) {
					// $row = mysql_fetch_row($result);
					$row = mysql_fetch_assoc($result);
					if ($row != false) {
						// print_r($row[0]);
						// Set studyID here so we don't have to select again for login
						$studyID=  $row['studyID'];
						$sql="INSERT into  userLogin  (studyID) VALUES ($studyID)  ";
						$result = mysql_query($sql, $userMsqlDB ) ;
			
			
					
						// add points
						$sql="UPDATE userInfo SET rafflepoints=rafflepoints+" . _HCC_RAFFLE_LOGIN . "  WHERE studyID=$studyID";
						$result = mysql_query($sql, $userMsqlDB ) ;
								

					}
					else {
						$success = false;
					}
				}
				else $success = false;
		}
		else 

			$success = false;
	
		mysql_close($userMsqlDB);
		return $success;
	
	}
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param	array	$credentials	Array holding the user credentials
	 * @param	array	$options		Array of extra options
	 * @param	object	$response		Authentication response object
	 * @return	boolean
	 * @since	1.5
	 */
	function onAuthenticate( $credentials, $options, &$response )
	{
		/*
		 * Here you would do whatever you need for an authentication routine with the credentials
		 *
		 * In this example the mixed variable $return would be set to false
		 * if the authentication routine fails or an integer userid of the authenticated
		 * user if the routine passes
		 */
		
		$success = true;

		// JOOMLA Authentication first
		jimport('joomla.user.helper');

		// Joomla does not like blank passwords
		if (empty($credentials['password']))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Empty password not allowed';
			return false;
		}

		// Initialize variables
		$conditions = '';

		// Get a database object
		$db =& JFactory::getDBO();

		$query = 'SELECT `id`, `password`, `gid`'
			. ' FROM `#__users`'
			. ' WHERE username=' . $db->Quote( $credentials['username'] )
			;
		$db->setQuery( $query );
		$result = $db->loadObject();


		if($result)
		{
			$parts	= explode( ':', $result->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				
				// HD2: Now check HD2 authentication before full authentication ONLY IF front-end login
				if ($options['group'] != 'Public Backend') {
					if ( $this->authenticateHd2($result->id, $user)) {
						$response->email = $user->email;
						$response->fullname = $user->name;
						$response->status = JAUTHENTICATE_STATUS_SUCCESS;
						$response->error_message = '';
					}
					else {
						$response->status = JAUTHENTICATE_STATUS_FAILURE;
						$response->error_message	= 'You are not registered with Healthy Directions';
					}
				}
				else {
				
						$response->email = $user->email;
						$response->fullname = $user->name;
						$response->status = JAUTHENTICATE_STATUS_SUCCESS;
						$response->error_message = '';
				}
			} else {
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Invalid password';
			}
		}
		else
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'User does not exist';
		}
		
		
	}
}
