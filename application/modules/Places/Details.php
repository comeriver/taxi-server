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

class Places_Details extends Places
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
	protected static $_objectTitle = 'Get Place Detail'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            $destination = $this->getParameter( 'place_id' ) ? : $_GET['place_id'];
            if( empty( $destination ) )
            {
                $this->_objectData['badnews'] = 'Place ID is not set';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                return false;
            }
            if( ! $info = Places_Table::getInstance()->selectOne( null, array( 'place_id' => $destination ) ) )
            {
                $apiUrl = 'https://maps.googleapis.com/maps/api/place/details/json?key=' . TaxiApp_Settings::retrieve( "google_api_key" ) . '&place_id=' . urlencode( $destination ) . '&fields=address_component,adr_address,business_status,formatted_address,geometry,icon,name,photo,place_id,plus_code,type,url,utc_offset,vicinity&sessiontoken=' . session_id();
                //    var_export( $apiUrl );
                //    return;
                $response = self::fetchLink( $apiUrl, array( 'time_out' => 60, 'connect_time_out' => 60 ) );
                $response = json_decode( $response, true );
                if( empty( $response['result']['geometry']['location']['lng'] ) )
                {
                    $this->_objectData['badnews'] = 'Invalid Place ID';
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                    return false;
                }
                $info['name'] = $response['result']['name'];
                $addressComponents = (array) $response['result']['address_components'];
                //krsort( $addressComponents );
                foreach( $addressComponents as $eachAdd )
                {
                    if( isset( $eachAdd['types'] ) )
                    {
                        if( in_array( 'country', $eachAdd['types'] ) )
                        {
                            $info['country'] = $eachAdd['long_name'];
                        }
                        elseif( in_array( 'administrative_area_level_1', $eachAdd['types'] ) )
                        {
                            $info['state'] = $eachAdd['long_name'];
                        }
                        elseif( in_array( 'administrative_area_level_2', $eachAdd['types'] ) )
                        {
                            $info['lga'] = $eachAdd['long_name'];
                        }
                        elseif( in_array( 'locality', $eachAdd['types'] ) )
                        {
                            $info['city'] = $eachAdd['long_name'];
                        }
                        elseif( in_array( 'neighborhood', $eachAdd['types'] ) )
                        {
                            $info['neighborhood'] = $eachAdd['long_name'];
                        }
                        elseif( in_array( 'postal_code', $eachAdd['types'] ) )
                        {
                            $info['postal_code'] = $eachAdd['long_name'];
                        }
                    }
                }
                //var_export( $info );
                //var_export( $addressComponents );
                //exit();

                $info['address'] = $response['result']['formatted_address'];
                $info['longitude'] = $response['result']['geometry']['location']['lng'];
                $info['latitude'] = $response['result']['geometry']['location']['lat'];
                $info['place_id'] = $response['result']['place_id'];
                $info['type'] = $response['result']['types'];
                Places_Table::getInstance()->insert( $info );    
            }
            $this->_objectData = $info;
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
