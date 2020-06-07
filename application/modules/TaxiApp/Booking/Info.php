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
        //    var_export( $where );
            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
            {
                $this->_objectData['badnews'] = "We could not retrieve the booking from the database";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                return false;
            }

            $titleCss = 'margin: 1em 0;';
            $smallTitleCss = 'display:block; font-size:small; font-weight:bold;margin:0.5em 0;';
            $flexContainer = 'display:flex; justify-content:space-between;flex-wrap:wrap;';
            $boxCss = 'border-bottom: 0.1px inset #000; margin: 0.2em 0;padding:1em;width:30%;';
            $this->setViewContent( '<h2 style="' . $titleCss . '">Trip Overview</h2>' ); 

        //    var_export( $bookingInfo['route_info']['routes'][0]['legs'][0] );
            $routeInfo = $bookingInfo['route_info']['routes'][0]['legs'][0];
            $timeRate = TaxiApp_Settings::retrieve( "time_rate" ) * $routeInfo['duration']['value'];
            $distanceRate = TaxiApp_Settings::retrieve( "distance_rate" ) * $routeInfo['distance']['value'];

            $totalRate = $timeRate + $distanceRate;

            $overview = '
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Booking ID:</span> ' . $bookingInfo['booking_id'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Status:</span> ' . self::$_statusMeaning[$bookingInfo['status']] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Destination:</span> ' . $bookingInfo['destination'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Start Address:</span> ' . $routeInfo['start_address'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">End Address:</span> ' . $routeInfo['end_address'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Estimated Rate:</span> ' . Application_Settings_Abstract::getSettings( 'Payments', 'default_currency' ) . $totalRate . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Distance:</span> ' . $routeInfo['distance']['text'] . '</div>
                <div style="' . $boxCss . '"><span style="' . $smallTitleCss . '">Duration:</span> ' . $routeInfo['duration']['text'] . '</div>
            ';
            $this->setViewContent( '<div style="' . $flexContainer . '">' . $overview . '</div>' ); 

            $this->_objectData['goodnews'] = self::$_statusMeaning[$bookingInfo['status']];
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
