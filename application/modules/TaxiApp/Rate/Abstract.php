<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_Abstract
 * @copyright  Copyright (c) 2023 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Thursday 26th of January 2023 06:42PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class TaxiApp_Rate_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'rate_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'rate_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'TaxiApp_Rate';
	
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

        $rateServices = TaxiApp_Rate_RateService::getInstance()->select( 'rateservice_name', null, array( 'row_id_column' => 'rateservice_id' ) ); 
        
        $fieldset->addElement( array( 'name' => 'rate', 'placeholder' => 'Enter amount... (numbers only) e.g. 100', 'type' => 'InputText', 'value' => @$values['rate'] ) );         
        $fieldset->addElement( array( 'name' => 'rateservice_id', 'label' => 'Service Type', 'type' => 'Select', 'value' => @$values['rateservice_id'] ? : @$_REQUEST['rateservice_id'] ), $rateServices );         
        $fieldset->addElement( array( 'name' => 'from_city', 'type' => 'InputText', 'value' => @$values['from_city'] ? : '*' ) );         
        $fieldset->addElement( array( 'name' => 'to_city', 'type' => 'InputText', 'value' => @$values['to_city'] ? : '*' ) );         
        $fieldset->addElement( array( 'name' => 'from_lga', 'type' => 'InputText', 'value' => @$values['from_lga'] ? : '*' ) );         
        $fieldset->addElement( array( 'name' => 'to_lga', 'type' => 'InputText', 'value' => @$values['to_lga'] ? : '*' ) );         
        $fieldset->addElement( array( 'name' => 'from_state', 'type' => 'InputText', 'value' => @$values['from_state'] ) );         
        $fieldset->addElement( array( 'name' => 'to_state', 'type' => 'InputText', 'value' => @$values['to_state'] ) );         
        $fieldset->addElement( array( 'name' => 'from_country', 'type' => 'InputText', 'value' => @$values['from_country'] ) );         
        $fieldset->addElement( array( 'name' => 'to_country', 'type' => 'InputText', 'value' => @$values['to_country'] ) ); 

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
