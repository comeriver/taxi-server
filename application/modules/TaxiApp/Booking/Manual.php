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

                //  finalize booking for booking with active service id

                //  update service id
                //var_export( array( 'rateservice_id' => $_REQUEST['rateservice_id'] ) );
                TaxiApp_Booking::getInstance()->update( array( 'rateservice_id' => $_REQUEST['rateservice_id'] ), array( 'booking_id' => $_REQUEST['booking_id'] ) );

                $this->setViewContent( '<h3 class="goodnews">Booking Confirmed</h3>', true );

                $this->setViewContent( '
                <p style="margin:1em 0;">
                Next Steps...
                <ul>
                    <li><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Pay/?booking_id=' . $_REQUEST['booking_id'] . '">Make Payment Online</a></li>
                    <li><a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info/?booking_id=' . $_REQUEST['booking_id'] . '">Check Booking Info</a></li>
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


            //  check if service have rate options

            $instantRates = true;
            if( ! empty( $values['service_id'] ) )
            {
                if( $serviceType = TaxiApp_Service::getInstance()->selectOne( null, array( 'service_id' => $values['service_id'] ) ) )
                {
                    if( in_array( 'no_instant_rates', $serviceType['service_options'] ) )
                    {
                        $instantRates = false;
                    }
                }
            }

            //var_export( $values );
            $this->setViewContent( '<h3 class="pc-notify-info">Select Service Options</h3>', true );

            $this->setViewContent( '<p>
            <br>
            <b>Pick-up Address</b>: <br>' . $values['passenger_location']['name'] .  ' - ' . $values['passenger_location']['address'] .  '<br><br>
            <b>Delivery Address</b>: <br>' . $values['destination_location']['name'] .  ' - ' . $values['destination_location']['address'] .  '
            </p>' );

            if( ! $instantRates )
            {
                $this->setViewContent( '<p>Our team will call you in a bit to provide you with a quote</p>',  );
            }
            else
            {
                
                $serviceOptions = self::calcRateOptions( $bookingInfo + $values );
    
                $currency = Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) ? : '$';
    
                if( $serviceOptions )
                {
                    $html = '<form method="post" action="?booking_id=' . $bookingInfo['booking_id'] .  '" >';
    
                    foreach( $serviceOptions as $serviceId => $service )
                    {
    
                        $html .= '<label for="' . $serviceId . '" class="rateservice" style="display:block; margin: 1em 0; padding: 1em; cursor:pointer; background-color: #ccc; border-radius: 10px; border-color: #333; ">';
                        $html .= '<input value="' . $serviceId . '" onchange="this.form.submit();" name="rateservice_id" type=radio id="' . $serviceId . '"> <div style="margin-left:1em; display:inline-block;"><b>' . $service['rateservice_name'] . ' - ' . $currency . $service['rate'] . '</b> 
                        <div>' . $service['rateservice_description'] . '</div>
                        </div>
                        <br>';
                        $html .= '</label>';
    
                    }
    
    
    
    
                    $html .= '<button type="submit">Confirm Booking</button>';
    
                    $html .= '</form>';
                    $this->setViewContent( $html );
    
                }
    
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
