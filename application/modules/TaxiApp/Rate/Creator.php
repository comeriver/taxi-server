<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_Creator
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Creator.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Rate_Creator extends TaxiApp_Rate_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Add new rate for service';

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			$this->createForm( 'Submit...', 'Add new rate' );
			$this->setViewContent( $this->getForm()->view() );

			if( ! $values = $this->getForm()->getValues() ){ return false; }
			
			$values['from_city'] = strtolower( $values['from_city'] );
			$values['to_city'] = strtolower( $values['to_city'] );
			$values['from_state'] = strtolower( $values['from_state'] );
			$values['to_state'] = strtolower( $values['to_state'] );
			$values['from_country'] = strtolower( $values['from_country'] );
			$values['to_country'] = strtolower( $values['to_country'] );

			//	Notify Admin
			$mailInfo = array();
			$mailInfo['subject'] = __CLASS__;
			$mailInfo['body'] = 'Form submitted on your PageCarton Installation with the following information: "' . self::arrayToString( $values ) . '". 
			
			';
			try
			{
				@Ayoola_Application_Notification::mail( $mailInfo );
			}
			catch( Ayoola_Exception $e ){ null; }
			if( $this->insertDb( $values ) )
			{ 
				$this->setViewContent(  '' . self::__( '<div class="goodnews">Rate added successfully. </div>' ) . '', true  ); 
			}
            


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
