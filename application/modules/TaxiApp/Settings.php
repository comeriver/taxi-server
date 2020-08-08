<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    PageCarton_Table_Sample
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Settings.php Saturday 6th of June 2020 08:59AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class TaxiApp_Settings extends PageCarton_Settings
{


	
    /**
     * creates the form for creating and editing
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
    {
		if( ! $settings = unserialize( @$values['settings'] ) )
		{
			if( is_array( $values['data'] ) )
			{
				$settings = $values['data'];
			}
			elseif( is_array( $values['settings'] ) )
			{
				$settings = $values['settings'];
			}
			else
			{
				$settings = $values;
			}
		}
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$form->submitValue = $submitValue ;
        $form->oneFieldSetAtATime = true;

        //  billing
		$fieldset = new Ayoola_Form_Element;
        $fieldset->addLegend( 'Billing Settings' );    
		$fieldset->addElement( array( 'name' => 'time_rate', 'label' => 'Rate per second', 'value' => @$settings['time_rate'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'distance_rate', 'label' => 'Rate per km', 'value' => @$settings['distance_rate'], 'type' => 'InputText' ) );
        $form->addFieldset( $fieldset );

        //  API
        $fieldset = new Ayoola_Form_Element;
        $fieldset->addLegend( 'API Settings' );       
        $form->addFieldset( $fieldset );
        $fieldset->addElement( array( 'name' => 'google_api_key', 'label' => 'Google API Key', 'value' => @$settings['google_api_key'], 'type' => 'InputText' ) );
        $form->addFieldset( $fieldset );
      
        //  Riders
        $fieldset = new Ayoola_Form_Element;
		$fieldset->addLegend( 'Rider Settings' );       
		$authLevel = new Ayoola_Access_AuthLevel;
		$authLevel = $authLevel->select();
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'auth_level', 'auth_name');
		$authLevel = $filter->filter( $authLevel );
        $fieldset->addElement( array( 'name' => 'driver_user_group', 'label' => 'What user-group can act as '  . TaxiApp::getTerm( 'Driver' ) . '', 'value' => @$settings['driver_user_group'], 'type' => 'Select' ), $authLevel );
        $form->addFieldset( $fieldset );

        //  location
        $fieldset = new Ayoola_Form_Element;
        $fieldset->addLegend( 'Location Settings' ); 
        $fieldset->addElement( array( 'name' => 'default_location_lat', 'label' => 'Default Location Latitude', 'value' => @$settings['default_location_lat'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'default_location_long', 'label' => 'Default Location Longitude', 'value' => @$settings['default_location_long'], 'type' => 'InputText' ) );
        $form->addFieldset( $fieldset );
        

        //  Terms
        $fieldset = new Ayoola_Form_Element;
        $fieldset->addLegend( 'Term Settings' ); 
        $fieldset->addElement( array( 'name' => 'passenger_term', 'label' => 'What to call clients', 'placeholder' => 'e.g. Passenger', 'value' => @$settings['passenger_term'], 'type' => 'InputText' ) );
        $fieldset->addElement( array( 'name' => 'driver_term', 'label' => 'What to call riders', 'placeholder' => 'e.g. Driver', 'value' => @$settings['driver_term'], 'type' => 'InputText' ) );

        $fieldset->addElement( array( 'name' => 'trip_term', 'label' => 'What to call the trip', 'placeholder' => 'e.g. Ride', 'value' => @$settings['trip_term'], 'type' => 'InputText' ) );
        $form->addFieldset( $fieldset );
        

		$this->setForm( $form );
    } 
	// END OF CLASS
}
