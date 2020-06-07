<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Driver
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Driver.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Driver extends TaxiApp_Booking_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = ''; 
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );

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
                NativeApp::populatePostData();

                if( empty( $_POST['driver_id'] ) )
                {
                    $this->_objectData['badnews'] = "Driver ID is not provided";
                    return false;
                }
                if( ! is_array( $_POST['driver_location'] ) )
                {
                //    $this->_objectData['badnews'] = "Pick up location not set";
                    return false;
                }
                $values = $_POST;
    
            }


            $where = array(
                'driver_id' => array( '', $_POST['driver_id'] )
            );
            if( empty( $_POST['booking_id'] ) )
            {
                //  We are looking for passenger
                $where['status'] = 0;
            }
            else
            {
                $where['booking_id'] = $_POST['booking_id'];
            }

            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
            {
                $this->_objectData['goodnews'] = "No passenger online";
                //    $this->_objectData['badnews'] = "Booking not found in the the database";
                $this->_objectData['debug'] = $_POST;
                $this->_objectData['debug'] = $where;
            //    return false;
            }
            else
            {
                $this->_objectData['goodnews'] = "Booking found...";
            }
            if( ! empty( $_POST['status'] ) )
            {
                $update = array();
                $update['status'] = $_POST['status'];
                $update['driver_id'] = $_POST['driver_id'];
                $update['driver_location'] = $_POST['driver_location'];
                $update['status_info'][$update['status']]['time'] = time();
                $update['last_status_time'] = time();

                TaxiApp_Booking::getInstance()->update( $update, $where );
                $bookingInfo = $update + $bookingInfo;
                $this->_objectData['goodnews'] = "Booking status updated";
            }
            $this->_objectData += $bookingInfo;

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
