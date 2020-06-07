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
		$fieldset = new Ayoola_Form_Element;

        //  Sample Text Field Retrieving E-mail Address
		$fieldset->addElement( array( 'name' => 'time_rate', 'label' => 'Rate per second', 'value' => @$settings['time_rate'], 'type' => 'InputText' ) );
		$fieldset->addElement( array( 'name' => 'distance_rate', 'label' => 'Rate per km', 'value' => @$settings['distance_rate'], 'type' => 'InputText' ) );

        //  Check box
		$authLevel = new Ayoola_Access_AuthLevel;
		$authLevel = $authLevel->select();
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'auth_level', 'auth_name');
		$authLevel = $filter->filter( $authLevel );

        $fieldset->addElement( array( 'name' => 'driver_user_group', 'label' => 'What user-group can act as driver', 'value' => @$settings['driver_user_group'], 'type' => 'Select' ), $authLevel );
		
		$fieldset->addLegend( 'TaxiApp Plugin Settings' ); 
               
		$form->addFieldset( $fieldset );
		$this->setForm( $form );
    } 
	// END OF CLASS
}
