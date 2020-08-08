<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Creator extends TaxiApp_Booking_Abstract
{
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 1 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Book a taxi'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            $this->createForm();
            $this->setViewContent( $this->getForm()->view() );
            
            if( ! $values = $this->getForm()->getValues() )
            {

                NativeApp::populatePostData();

                if( empty( $_POST ) )
                {
                    return false;
                }
                if( empty( $_POST['destination'] ) )
                {
                    $this->_objectData['badnews'] = "Invalid Destination";
                    return false;
                }
                if( empty( $_POST['passenger_id'] ) )
                {
                    $this->_objectData['badnews'] = ''  . self::getTerm( 'Passenger' ) . ' is not logged in';
                    return false;
                }
                if( ! is_array( $_POST['passenger_location'] ) )
                {
                //    $this->_objectData['badnews'] = "Pick up location not set";
                //    return false;
                }
                $values = $_POST;
    
            }
    
            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->insert( $values ) )
            {
                $this->_objectData['badnews'] = "We could not save the booking into the database";
                return false;
            }
            $this->_objectData['goodnews'] = "Booking successful. Please wait...";
            $this->_objectData += $bookingInfo;

            $this->setViewContent( '<p class="goodnews">' . $this->_objectData['goodnews'] . '</p>', true );
        
			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = __CLASS__;
			$mailInfo['body'] = 'A booking was just made with the following information: <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $bookingInfo['insert_id'] . '">Booking Info</a>';
			try
			{
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
			catch( Ayoola_Exception $e ){ null; }
            


            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
