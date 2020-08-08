<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Cancel
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Cancel.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Cancel extends TaxiApp_Booking_Abstract
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
            NativeApp::populatePostData();
            $result1 = self::cancelDriverBookings();
            $result2 = self::cancelPassengerBookings();

            $this->_objectData['goodnews'] = 'All bookings by user has been canceled.';
            $this->_objectData['debug']['post'] = $_POST;
            $this->_objectData['debug']['driver'] = $result1;
            $this->_objectData['debug']['passenger'] = $result2;
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
