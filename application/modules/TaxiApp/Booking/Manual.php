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

class TaxiApp_Booking_Manual extends TaxiApp_Booking_Creator
{
		
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected $_playMode = self::PLAY_MODE_HTML;
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Request a pickup'; 

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
                return false;
            }
            $this->createForm();
            if( empty( $values['passenger_location'] ) )
            {
                if( empty( $values['pickup_place_id'] ) )
                {
                    $this->_objectData['badnews'] = 'Pick up location is required';
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    $this->setViewContent( $this->getForm()->view() );
                    return false;
                }

                if( ! $placeInfo = Places_Details::viewInLine( array( 'place_id' => $values['pickup_place_id'], 'return_object_data' => true ) ) OR ! empty( $placeInfo['badnews'] ) )
                {
                    $this->_objectData['badnews'] = 'Invalid Pick-up Location. ' . @$placeInfo['badnews'];
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    $this->setViewContent( $this->getForm()->view() );
                    return false;
                }
                $values['passenger_location'] = $placeInfo;
            }

            if( empty( $values['destination_location'] ) )
            {
                if( empty( $values['destination_place_id'] ) )
                {
                    $this->_objectData['badnews'] = 'Destination is required';
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    $this->setViewContent( $this->getForm()->view() );
                    return false;
                }
                if( ! $placeInfo = Places_Details::viewInLine( array( 'place_id' => $values['destination_place_id'], 'return_object_data' => true ) ) OR ! empty( $placeInfo['badnews'] ) )
                {
                    $this->_objectData['badnews'] = 'Invalid Destination. ' . @$placeInfo['badnews'];
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    $this->setViewContent( $this->getForm()->view() );
                    return false;
                }
                $values['destination_location'] = $placeInfo;
                $values['destination'] = $placeInfo['name'] ? : $placeInfo['address'];
            }

            if( empty( $values['route_info'] ) )
            {
                if( ! $routeInfo = Places_Route::viewInLine( array( 'destination' => 'place_id:' . $values['destination_location']['place_id'], 'origin' => 'place_id:' . $values['passenger_location']['place_id'], 'return_object_data' => true ) ) OR ! empty( $routeInfo['badnews'] ) )
                {
                    $this->_objectData['badnews'] = 'No route found from pick-up location to destination. Please change either the pick-up or destination location and try again. ' . @$placeInfo['badnews'];
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    $this->setViewContent( $this->getForm()->view() );
                    return false;
                }
                $values['route_info'] = $routeInfo;
            }

            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->insert( $values ) )
            {
                $this->_objectData['badnews'] = "We could not save the booking into the database";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }
            $this->_objectData['goodnews'] = "Pick-up booking successful. Connecting in a moment...";
            $this->_objectData += $bookingInfo;

            $this->setViewContent( '<h2 class="goodnews">Booking Confirmed</h2>', true );
            $this->setViewContent( '<p style="margin:1em 0;">' . $this->_objectData['goodnews'] . ' <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $bookingInfo['insert_id'] . '">Check Booking Info</a></p>' );
        
			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = 'Pick-up booking confirmation';
            $mailInfo['body'] = 'A booking for a pick-up was just made. This is a confirmation of that booking. 
            
            <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $bookingInfo['insert_id'] . '">Check Booking Info</a>';
			try
			{
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
            catch( Ayoola_Exception $e ){ null; }
            
            //  send mail to the current user
            $mailInfo['to'] = Ayoola_Application::getUserInfo( 'email' );
            self::sendMail( $mailInfo );
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->_objectData['badnews'] = $e->getMessage();
            $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
            return false; 
        }
	}
	// END OF CLASS
}
