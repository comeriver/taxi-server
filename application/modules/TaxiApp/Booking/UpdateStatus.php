<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_UpdateStatus
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: UpdateStatus.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_UpdateStatus extends TaxiApp_Booking_Editor
{
		
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected $_playMode = self::PLAY_MODE_HTML;

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

			$this->createForm( 'Save', 'Update Booking Status', $data );

            $this->getForm()->setParameter( array( 'element_whitelist' => 'status' ) );

			$this->setViewContent( $this->getForm()->view(), true );

			if( ! $values = $this->getForm()->getValues() ){ return false; }

            $this->updateBookingInfo( $values, $data );

			if( $this->updateDb( $values ) ){ $this->setViewContent(  '' . self::__( '<div class="goodnews">Booking updated successfully</div>' ) . '', true  ); } 
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
