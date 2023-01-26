<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Info
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Info.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Info extends TaxiApp_Booking_Abstract
{
			
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected $_playMode = self::PLAY_MODE_HTML;
	
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
	protected static $_objectTitle = 'Booking Info'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            //    NativeApp::populatePostData();
            if( empty( $_GET['booking_id'] ) )
            {
                $this->_objectData['badnews'] = "Booking ID not set";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                return false;
            }    
            $where = array(
                'booking_id' => $_GET['booking_id']
            );
            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
            {
                $this->_objectData['badnews'] = "We could not retrieve the booking from the database";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                return false;
            }

            $titleCss = 'margin: 1em 0; display:flex;';
            $smallTitleCss = 'display:block; font-size:small; font-weight:bold;margin-bottom:1em;';
            $flexContainer = 'display:flex; justify-content:space-between;flex-wrap:wrap;';
            $boxCss = 'border-bottom: 0.1px solid #ccc; margin: 0.2em 0;padding:1em;min-width:150px;width:25%;';
            $boxCss2 = 'border: 0.2px solid #ccc; margin: 0.2em 0;padding:1em;flex-basis:45%;';
            $smallText = 'font-size:small; font-weight:light;margin: 0 0.4em;';

            //  overview
            $routeInfo = $bookingInfo['route_info']['routes'][0]['legs'][0];
            $this->setViewContent( '<h2 style="' . $titleCss . '">'  . self::getTerm( 'Trip' ) . ' Overview</h2>' ); 

            $totalRate = self::calcRate( $bookingInfo );
            
            $overview = '
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Booking ID:</span> ' . $bookingInfo['booking_id'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Status:</span> ' . self::getStatusMeaning( $bookingInfo['status'] ) . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Destination:</span> ' . $bookingInfo['destination'] . ' <br><span style="font-size:small;"> ' . date( 'g:ia, D jS M Y', $bookingInfo['delivery_time'] ) . '</span></div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Start Address:</span> ' . $routeInfo['start_address'] . ' <br> <span style="font-size:small;">' . date( 'g:ia, D jS M Y', $bookingInfo['pickup_time'] ) . '</span></div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">End Address:</span> ' . $routeInfo['end_address'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Estimated Rate:</span> ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . $totalRate . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Distance:</span> ' . $routeInfo['distance']['text'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Duration:</span> ' . $routeInfo['duration']['text'] . '</div>
            ';
            $this->setViewContent( '<div style="' . $flexContainer . '">' . $overview . '</div>' ); 

            //  Links
            $this->setViewContent( '
                <div style="' . $titleCss . '"> 
                    <a style="flex-basis:100%" class="pc-btn" target="_blank" href="https://www.google.com/maps/dir/?api=1&destination=' . $bookingInfo['passenger_location']['latitude'] . ',' . $bookingInfo['passenger_location']['longitude'] . '">Get Direction to Pick-up Point <i class="fa fa-map-o fa-map" style="margin: 1em;"> </i></a>
                    <a style="flex-basis:100%" class="pc-btn" target="_blank" href="https://www.google.com/maps/dir/?api=1&destination=' . $routeInfo['end_location']['lat'] . ',' . $routeInfo['end_location']['lng'] . '">Get Direction to Destination <i class="fa fa-map-marker" style="margin: 1em;"> </i></a>
                    <a style="flex-basis:100%" class="pc-btn" target="" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Pay?booking_id=' . $bookingInfo['booking_id'] . '">Make Payment <i class="fa fa-money" style="margin: 1em;"> </i></a>
                </div>
            ' ); 


            //  timeline

            $this->setViewContent( '<h3 style="' . $titleCss . '">Timeline</h3>' ); 
            $timeline = null;
            $bookingInfo['status_info'][0]['time'] = $bookingInfo['creation_time'];
            ksort( $bookingInfo['status_info'] );

            foreach( $bookingInfo['status_info'] as $status => $info )
            {
                $timeline .= '<li style="margin: 1em 1em;">';
                $timeline .= self::getStatusMeaning( $status );
                $timeline .= ' <span style="' . $smallText . '">(';
                if( isset( $lastStatus ) )
                {
                    $waitingTime =  intval( $bookingInfo['status_info'][$status]['time'] ) - intval( $bookingInfo['status_info'][$lastStatus]['time'] );
                    $timeline .= '+' . Ayoola_Filter_Time::splitSeconds( $waitingTime );
                }
                else
                {
                    $timeline .= date( 'g:ia, D jS M Y', $bookingInfo['status_info'][$status]['time'] );
                }
                $timeline .= ')</span> ';
                $timeline .= '</li>';
                $lastStatus = $status;
            }
            $this->setViewContent( '<ul style="margin: 1em 0;">' . $timeline . '</ul>' ); 

            //var_export( $bookingInfo );


            $passengerInfo = Application_User_Abstract::getUserInfo( $bookingInfo['passenger_id'] );
            $driverInfo = Application_User_Abstract::getUserInfo( $bookingInfo['driver_id'] );


            //  people
            $this->setViewContent( '<h3 style="' . $titleCss . '">People</h3>' ); 


            $people = '
                <div style="' . $boxCss2 . '"><span style="' . $smallTitleCss . '">
                ' . trim( $passengerInfo['firstname'] . ' ' . $passengerInfo['lastname'] ) . ' ('  . self::getTerm( 'Passenger' ) . '):</span> 
                    <a href="mailto:' . $passengerInfo['email'] . '">' . $passengerInfo['email'] . '</a><br>
                    <a href="tel:' . $passengerInfo['phone_number'] . '">' . $passengerInfo['phone_number'] . '</a><br>
                </div>
                <div style="' . $boxCss2 . '"><span style="' . $smallTitleCss . '">
                ' . trim( $driverInfo['firstname'] . ' ' . $driverInfo['lastname'] ) . ' ('  . self::getTerm( 'Driver' ) . '):</span> 
                    <a href="mailto:' . $driverInfo['email'] . '">' . $driverInfo['email'] . '</a><br>
                    <a href="tel:' . $driverInfo['phone_number'] . '">' . $driverInfo['phone_number'] . '</a><br>
                </div>
            ';
            $this->setViewContent( '<div style="' . $flexContainer . '">' . $people . '</div>' ); 

            $this->_objectData['goodnews'] = self::getStatusMeaning( $bookingInfo['status'] );
            $this->_objectData += $bookingInfo;

            // end of widget process

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
