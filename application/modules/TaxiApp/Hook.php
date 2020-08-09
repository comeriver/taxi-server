<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    NativeApp_Authenticate
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Authenticate.php Monday 23rd of March 2020 09:36AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Hook extends NativeApp
{
    
    
    /**
     * Converts youtube links in output to embedded video
     * 
     */
	public function hook( $class, $method, & $content )
    {
        $widget = $class;
        if( is_object( $widget ) )
        {
            $widget = get_class( $widget );
        }

        switch( $widget )
        {
            case 'Application_SiteInfo':
                switch( strtolower( $method ) )
                {
                    case 'view':
                        if( ! is_array( $content ) )
                        {
                            continue;
                        }
                        $settingsOptions = array(
                            'driver_user_group',
                            'default_location_lat',
                            'default_location_long',
                            'passenger_term',
                            'driver_term',
                            'trip_term',
                        );
                        foreach( $settingsOptions as $each )
                        {
                            $content[$each] = TaxiApp_Settings::retrieve( $each );
                        }
                    break;
                }
            break;
        }
    }



	// END OF CLASS
}
