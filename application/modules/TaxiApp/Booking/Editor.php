<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Editor
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Editor.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Editor extends TaxiApp_Booking_Abstract
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
			$this->createForm( 'Save', 'Edit Bookings', $data );
			$this->setViewContent( $this->getForm()->view(), true );

			if( ! $values = $this->getForm()->getValues() ){ return false; }

            if( empty( $values['pickup_place_id'] ) )
            {
                $this->_objectData['badnews'] = ''  . self::getTerm( 'Passenger' ) . ' pick up location is required';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }

            if( empty( $values['destination_place_id'] ) )
            {
                $this->_objectData['badnews'] = ''  . self::getTerm( 'Trip' ) . ' destination is required';
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>', true );
                $this->setViewContent( $this->getForm()->view() );
                return false;
            }
            
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
