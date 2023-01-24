<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Booking_List extends TaxiApp_Booking_Abstract
{
		
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected $_playMode = self::PLAY_MODE_HTML;
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Bookings';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
      $this->setViewContent( $this->getList() );		
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();

    $where = array();
    if( ! $this->hasPriviledge( array( 99, 98 ) ) )
    {
      $where['passenger_id'] = Ayoola_Application::getUserInfo( 'user_id' );
    }

    $bookings = TaxiApp_Booking::getInstance()->select( null, $where );

		$list->setData( $bookings );
		$list->setListOptions( 
								array( 
										'Creator' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Manual/\' );" title="">Manual Booking</a>',    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );

    $listInfo = 			array(
      'Booking ID' => array( 'field' => 'booking_id', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
      'destination' => array( 'field' => 'destination', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
      'Booked' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
      'Pick up' => array( 'field' => 'delivery_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
      'Delivery' => array( 'field' => 'pickup_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
      'status' => array( 'field' => 'status', 'value' =>  '%FIELD%', 'value_representation' =>  self::getStatusMeaning() ), 
      '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Info/?' . $this->getIdColumn() . '=%KEY%">Booking Details</a>', 
    );


    if( $this->hasPriviledge( array( 99, 98 ) ) )
    {
      $listInfo += 			array(
        '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Editor/?' . $this->getIdColumn() . '=%KEY%">Edit</a>', 
        '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Delete/?' . $this->getIdColumn() . '=%KEY%">x</a>', 
      );
  
    }
		
		$list->createList( $listInfo );
		return $list;
    } 
	// END OF CLASS
}
