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

class Places_Route extends Places
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
	protected static $_objectTitle = 'Get Route to Place'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            $destination = $this->getParameter( 'destination' ) ? : $_GET['destination'];
            $origin = $this->getParameter( 'origin' ) ? : $_GET['origin'];
            if( empty( $destination ) )
            {
                $this->_objectData['badnews'] = 'No destination is set';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }
            if( empty( $origin ) )
            {
                $this->_objectData['badnews'] = 'No origin is set';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }
            $apiUrl = 'https://maps.googleapis.com/maps/api/directions/json?key=' . TaxiApp_Settings::retrieve( "google_api_key" ) . '&origin=' . $origin . '&destination=' . $destination;
            //    var_export( $apiUrl );
            //    return;
            $response = self::fetchLink( $apiUrl, array( 'time_out' => 60, 'connect_time_out' => 60 ) );
        //    var_export( $response );
            $response = json_decode( $response, true );
            if( empty( $response['routes'][0]['legs'][0] ) )
            {
                $this->_objectData['badnews'] = "No route is found to the selected destination";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
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
