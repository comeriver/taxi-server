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

            if( ! empty( $_REQUEST['booking_id'] ) && ! empty( $_REQUEST['rateservice_id'] ) )
            {

                //  update service id
                TaxiApp_Booking::getInstance()->update( array( 'rateservice_id' => $_REQUEST['rateservice_id'] ), array( 'booking_id' => $_REQUEST['booking_id'] ) );

                $this->setViewContent( '<h3 class="goodnews">Booking Confirmed</h3>', true );

                $this->setViewContent( '
                <p style="margin:1em 0;">
                Next Steps...
                <ul>
                    <li><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Pay/?booking_id=' . $bookingInfo['insert_id'] . '">Make Payment Online</a></li>
                    <li><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $bookingInfo['insert_id'] . '">Check Booking Info</a></li>
                </ul>
                </p>' 
                );

                return true;


            }

            $this->createForm();

            $this->setViewContent( $this->getForm()->view() );
            
            if( ! $values = $this->getForm()->getValues() )
            {
                return false;
            }
            $this->createForm();
            $values['passenger_id'] = Ayoola_Application::getUserInfo( 'user_id' );

            $this->updateBookingInfo( $values );

            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->insert( $values ) )
            {
                $this->_objectData['badnews'] = "We could not save the booking into the database";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }

            $this->_objectData['goodnews'] = ''  . self::getTerm( 'Passenger' ) . ' pick-up booking successful. Connecting '  . self::getTerm( 'Trip' ) . ' in a moment...';
            $this->_objectData += $bookingInfo;


            $this->setViewContent( '<h3 class="goodnews">Select Service Options</h3>', true );

            
            if( $serviceOptions = self::calcRateOptions( $bookingInfo + $values ) )
            {
                $html = '<form action="?booking_id=' . $bookingInfo['booking_id'] .  '" >';

                foreach( $serviceOptions as $serviceId => $service )
                {
                    $html .= '<input onchange="this.form.submit();" name="rateservice_id" type=radio id="' . $serviceId . '"> <label for"' . $serviceId . '"><b>' . $service['rateservice_name'] . '</b> <p>' . $service['rateservice_description'] . '</p></label>';
                }


                $html .= '</form>';
                $this->setViewContent( $html, true );

            }

        
			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = 'Pick-up booking confirmation';
            $mailInfo['body'] = ''  . self::getTerm( 'Passenger' ) . ' booking for a '  . self::getTerm( 'Trip' ) . ' was just made and a pick-up location was set successfully. This is a confirmation of that booking. 
            
            <a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $bookingInfo['insert_id'] . '">Track '  . self::getTerm( 'Trip' ) . ' Booking Info</a>';

			try
			{
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
            catch( Ayoola_Exception $e ){ null; }
            
            //  send mail to the current user
            if( Ayoola_Application::getUserInfo( 'email' ) )
            {
                $mailInfo['to'] = Ayoola_Application::getUserInfo( 'email' );
                self::sendMail( $mailInfo );
            }
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
