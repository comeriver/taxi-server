<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_Editor
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Editor.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Rate_Editor extends TaxiApp_Rate_Abstract
{

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			if( ! $data = $this->getIdentifierData() ){ return false; }
			$this->createForm( 'Save', 'Edit Rate', $data );
			$this->setViewContent( $this->getForm()->view(), true );
			if( ! $values = $this->getForm()->getValues() ){ return false; }

			
			$values['from_city'] = strtolower( $values['from_city'] );
			$values['to_city'] = strtolower( $values['to_city'] );
			$values['from_state'] = strtolower( $values['from_state'] );
			$values['to_state'] = strtolower( $values['to_state'] );
			$values['from_country'] = strtolower( $values['from_country'] );
			$values['to_country'] = strtolower( $values['to_country'] );

			if( $this->updateDb( $values ) ){ $this->setViewContent(  '' . self::__( '<div class="goodnews">Rate data updated successfully</div>' ) . '', true  ); } 

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}
	// END OF CLASS
}
