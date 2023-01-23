<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: TaxiApp.php Saturday 6th of June 2020 08:59AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Places extends NativeApp
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
	protected static $_objectTitle = 'Get Places'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            $destination = $_GET['q'];
            if( empty( $destination ) )
            {
                $this->_objectData['badnews'] = 'No destination has been typed in for autocomplete';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }
            $proximity = $_GET['proximity'] ? : TaxiApp_Settings::retrieve( "default_location_lat" ) . ',' . TaxiApp_Settings::retrieve( "default_location_long" );
            $apiUrl = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?key=' . TaxiApp_Settings::retrieve( "google_api_key" ) . '&input=' . urlencode( $destination ) . '&location=' . $proximity . '&radius=2000&sessiontoken=' . session_id();
            //    var_export( $apiUrl );
            //    return;
            $response = self::fetchLink( $apiUrl, array( 'time_out' => 60, 'connect_time_out' => 60 ) );
            $response = json_decode( $response, true );
            if( empty( $_GET['raw_response'] ) )
            {
                $ref = array();
                foreach( $response['predictions'] as $each )
                {
                    $ref[] = array( 
                        'id' => $each['place_id'],
                        'text' => $each['description'],
                    );
                }
                $response = array( 'results' => $ref );
            }
            $this->_objectData = $response;
            // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
        //    $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
