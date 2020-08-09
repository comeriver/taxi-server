<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Abstract
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Saturday 6th of June 2020 09:16AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class TaxiApp_Booking_Abstract extends TaxiApp
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'booking_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'booking_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'TaxiApp_Booking';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var array
     */
	protected static $_accessLevel = array( 99, 98 );

    /**
     * 
     */
	public static function cancelDriverBookings( array $identifier = null )  
    {
        if( empty( $identifier ) )
        {
            $identifier = $_POST;
        }
        return TaxiApp_Booking::getInstance()->update( array( 'status' => -1 ), array( 'driver_id' => $identifier['driver_id'], 'status' => array( 0, 1, 2, 3 ) ) );
    }

    /**
     * 
     */
	public static function getStatusMeaning( $key = null )  
    {
    
        $meaning = array( 
            -2 => ''  . self::getTerm( 'Trip' ) . ' canceled by '  . self::getTerm( 'Passenger' ) . '',
            -1 => ''  . self::getTerm( 'Trip' ) . ' canceled by '  . self::getTerm( 'Driver' ) . '',
            0 => ''  . self::getTerm( 'Passenger' ) . ' requested a pick-up',
            1 => ''  . self::getTerm( 'Passenger' ) . ' was matched with a '  . self::getTerm( 'Driver' ) . '',
            2 => ''  . self::getTerm( 'Driver' ) . ' arrived at '  . self::getTerm( 'Passenger' ) . ' location',
            3 => ''  . self::getTerm( 'Trip' ) . ' started',
            4 => ''  . self::getTerm( 'Trip' ) . ' completed',
            5 => ''  . self::getTerm( 'Passenger' ) . ' paid for '  . self::getTerm( 'Trip' ) . '',
         );
         if( is_null( $key ) )
         {
             return $meaning;
         }
         return $meaning[$key];
    }

    /**
     * 
     */
	public static function calcRate( $bookingInfo )  
    {
        $routeInfo = $bookingInfo['route_info']['routes'][0]['legs'][0];
        $timeRate = TaxiApp_Settings::retrieve( "time_rate" ) * $routeInfo['duration']['value'];
        $distanceRate = TaxiApp_Settings::retrieve( "distance_rate" ) * $routeInfo['distance']['value'];

        $totalRate = $timeRate + $distanceRate;
        return $totalRate;
    }

    /**
     * 
     */
	public static function cancelPassengerBookings( array $identifier = null )  
    {
        if( empty( $identifier ) )
        {
            $identifier = $_POST;
        }
        return TaxiApp_Booking::getInstance()->update( array( 'status' => -2 ), array( 'passenger_id' => $identifier['passenger_id'], 'status' => array( 0, 1, 2, 3 ) ) );
    }

    /**
     * creates the form for creating and editing page
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		$form->submitValue =  'Request Pickup';

        $fieldset = new Ayoola_Form_Element;
        $widgets = Ayoola_Object_Embed::getWidgets();
        if( empty( $widgets[@$values['class_name']] ) )
        {
            $widgets[@$values['class_name']] = $values['class_name'];
        }

        $fieldset->addElement( array( 'name' => 'pickup_place_id', 'label' => 'Set Pick-up Location', 'config' => array( 
            'ajax' => array( 
                'url' => '/taxi/widgets/Places',
                'delay' => 1000
            ),
            'placeholder' => 'e.g. 2 Adekanbi St, Ikeja',
            'minimumInputLength' => 1,
        ), 'type' => 'Select2', 'value' => @$values['pickup_place_id'] ) ); 
        $fieldset->addElement( array( 'name' => 'destination_place_id', 'label' => 'Set Destination', 'config' => array( 
            'ajax' => array( 
                'url' => '/taxi/widgets/Places',
                'delay' => 1000
            ),
            'placeholder' => 'e.g. Palms Mall, Ibadan',
            'minimumInputLength' => 1,
        ), 'type' => 'Select2', 'value' => @$values['destination_place_id'] ) ); 
        $fieldset->addRequirements( array( 'NotEmpty' => null ) );

        $fieldset->addElement( array( 'name' => 'notes', 'type' => 'TextArea', 'placeholder' => 'Enter any further '  . self::getTerm( 'Trip' ) . ' or contact details here... (Optional)', 'value' => @$values['notes'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
