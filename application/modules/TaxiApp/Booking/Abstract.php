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
     * Access level for player. Defaults to everyone
     *
     * @var array
     */
	protected static $_statusMeaning = array( 
        -2 => 'Trip canceled by passenger',
        -1 => 'Trip canceled by rider operator',
        0 => 'Passenger requested a ride',
        1 => 'Passenger was matched with a ride',
        2 => 'Ride arrived at passenger location',
        3 => 'Trip started',
        4 => 'Trip completed',
        5 => 'Passenger paid for ride',
     );

    /**
     * 
     */
	protected static function cancelDriverBookings( array $identifier = null )  
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
	protected static function cancelPassengerBookings( array $identifier = null )  
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
		$form->submitValue = $submitValue ;
//		$form->oneFieldSetAtATime = true;

		$fieldset = new Ayoola_Form_Element;
	//	$fieldset->placeholderInPlaceOfLabel = false;       
        $fieldset->addElement( array( 'name' => 'destination', 'type' => 'InputText', 'value' => @$values['destination'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
