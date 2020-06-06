<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Status
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Status.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Status extends TaxiApp_Booking_Abstract
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
	protected static $_objectTitle = ''; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            
            if( ! $values = $this->getForm()->getValues() )
            {
                //    NativeApp::populatePostData();
                if( empty( $_GET['booking_id'] ) )
                {
                    $this->_objectData['badnews'] = "Booking ID not set";
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                    return false;
                }    
            }
            $where = array(
                'booking_id' => $_GET['booking_id']
            );
        //    var_export( $where );
            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
            {
                $this->_objectData['badnews'] = "We could not retrieve the booking from the database";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                return false;
            }
            $this->_objectData['goodnews'] = self::$_statusMeaning[$bookingInfo['status']];
            $this->_objectData += $bookingInfo;

            // end of widget process
            $this->setViewContent( '<p class="goodnews">' . $this->_objectData['goodnews'] . '</p>' ); 
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
