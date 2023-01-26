<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_Pay
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Pay.php Wednesday 20th of December 2017 03:23PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_Pay extends TaxiApp_Booking_Abstract
{
			
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected $_playMode = self::PLAY_MODE_HTML;
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Make payment'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            
            if( ! $values = $this->getForm()->getValues() )
            {
                //    NativeApp::populatePostData();
                if( empty( $_GET['booking_id'] ) )
                {
                    $this->_objectData['badnews'] = 'Booking ID for the '  . self::getTerm( 'Trip' ) . ' is not set. Please check the URL and try again.';
                    $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                    return false;
                }    
            }
            $where = array(
                'booking_id' => $_GET['booking_id']
            );
            if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
            {
                $this->_objectData['badnews'] = "We could not retrieve the booking from the database.";
                $this->setViewContent( '<p class="badnews">' . $this->_objectData['badnews'] . '</p>' ); 
                return false;
            }
            
            $totalRate = self::calcRate( $bookingInfo );

            $class = new Application_Subscription();   
            $values['subscription_name'] = __CLASS__;
            $values['subscription_label'] = 'Payment for '  . self::getTerm( 'Trip' ) . ' - ' . $_GET['booking_id'];
            
            $values['price'] = str_replace( array( ',', ' ' ), '', $totalRate );

            $values['cycle_name'] = 'One-time Payment';   
            $values['cycle_label'] = '';
            $values['booking_id'] = $bookingInfo['booking_id'];
            $values['price_id'] = __CLASS__;
            $values['subscription_description'] = $bookingInfo['destination'];
            //
            //	After we checkout this is where we want to come to
            $values['url'] = '' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Info?booking_id=' . $bookingInfo['booking_id'];
            $values['classplayer_link'] = "javascript:;";

            $values['callback'] = __CLASS__;
			$values['cart_item_type'] = __CLASS__;
			$values['cart_password'] = __CLASS__;

            $class->subscribe( $values );

            header( 'Location: ' . Ayoola_Application::getUrlPrefix() . '/cart' );

            // end of widget process
            $this->setViewContent( '<p class="goodnews">' . $this->_objectData['goodnews'] . '</p>' ); 
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) ); 
            $this->setViewContent( self::__( '<p class="badnews">Theres an error in the code</p>' ) ); 
            return false; 
        }
	}

    /**
     * Performs confirmation when user payment is completed
     * 
     * param array Order information
     */
	public static function callback(& $orderInfo )
    {
        switch( strtolower( $orderInfo['order_status'] ) )
        { 
            case 'payment successful':
            case '99':
            case '100':
                
                if( ! empty( $orderInfo['payment_confirmed'] ) )
                {
                    //  don't transfer twice
                    break;
                }
				
                $where = array(
                    'booking_id' => $orderInfo['booking_id']
                );
                if( ! $bookingInfo = TaxiApp_Booking::getInstance()->selectOne( null, $where ) )
                {
                    return false;
                }
                
                $totalRate = self::calcRate( $bookingInfo );

                if( $orderInfo['price'] != $totalRate )
                {
                    return false;
                }

                $orderInfo['payment_confirmed'] = true;

                $bookingInfo['status_info'][5]['time'] = time();

                TaxiApp_Booking::getInstance()->update( array( 'paid' => $orderInfo['price'] ), $where );

			break;
		}
	}

	// END OF CLASS
}
