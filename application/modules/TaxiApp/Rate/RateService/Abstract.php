<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_RateService_Abstract
 * @copyright  Copyright (c) 2023 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Friday 17th of February 2023 12:53PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class TaxiApp_Rate_RateService_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'rateservice_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'rateservice_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'TaxiApp_Rate_RateService';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );


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


		$fieldset = new Ayoola_Form_Element;

        $serviceTypes = TaxiApp_Service::getInstance()->select( 'service_name', null, array( 'row_id_column' => 'service_id' ) );

        if( $serviceTypes )
        {
            $fieldset->addElement( array( 'name' => 'service_id', 'label' => 'Service Type', 'type' => 'Select', 'value' => @$values['service_id'] ? : @$_REQUEST['service_id'] ), $serviceTypes );
        }
        $fieldset->addElement( array( 'name' => 'rateservice_name', 'label' => 'Service Rate Name', 'type' => 'InputText', 'value' => @$values['rateservice_name'] ) ); 
        $fieldset->addElement( array( 'name' => 'rateservice_description', 'label' => 'Service Rate Description', 'type' => 'TextArea', 'value' => @$values['rateservice_description'] ) ); 


		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
