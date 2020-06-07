<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Login
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Login.php Saturday 6th of June 2020 09:00AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Login extends TaxiApp
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Login to TaxiApp'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            NativeApp::populatePostData();

            if( empty( $_POST['email'] ) )
            {
                $this->_objectData['badnews'] = "Email address is required to login";
                return false;
            }
            if( empty( $_POST['phone_number'] ) )
            {
                $this->_objectData['badnews'] = "Phone number is required to login";
                return false;
            }
            if( ! is_numeric( $_POST['phone_number'] ) )
            {
                $this->_objectData['badnews'] = "Phone number should just contain numbers";
                return false;
            }
            $email = $_POST['email'];
            $phone = $_POST['phone_number'];
            $login = array(
                'email' => $email,
            );

            if( ! $userInfo = Application_User_Abstract::getUserInfo( $login ) )
            {
                $signupParameters = $login + array(
                    'phone_number' => $phone,
                    'username' => '_' . $phone,
                    'password' => Ayoola_Form::hashElementName( $email . $phone ),
                );
                $signup = new Application_User_Creator( array( 'fake_values' => $signupParameters ) );
                $signup->view();
                if( ! $userInfo = Application_User_Abstract::getUserInfo( $login ) )
                {
                    if( $signup->getForm()->getBadnews() )
                    {
                        $badnews = $signup->getForm()->getBadnews();
                        $this->_objectData['badnews'] = array_pop( $badnews );
                        return false;
                    }
                    $this->_objectData['badnews'] = "An unknown error occured, we can't log you in. Please contact support.";
                    return false;
                }
            }

            $authInfo = array();
            $authToken = md5( uniqid( json_encode( $userInfo ), true ) );

            //  save auth info in data
            $table = NativeApp_Authenticate_Table::getInstance();

            $authInfoToSave = array( 
                'user_id' => strval( $userInfo['user_id'] ),
                'email' => strtolower( $userInfo['email'] ),
                'auth_token' => $authToken,
                'device_info' => $_POST['device_info'],
            );

            $table->insert( $authInfoToSave );
            $otherSettings['supported_versions'] = self::$_supportedClientVersions;
            $otherSettings['current_stable_version'] = self::$_currentStableClientVersion;

            $authInfo += $authInfoToSave;
            $authInfo += $userInfo;
        //    $authInfo += $otherSettings;

            $this->_objectData['goodnews'] = "Log in successful";
            $this->_objectData['auth_info'] = $authInfo;
            $this->_objectData += $otherSettings;
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
