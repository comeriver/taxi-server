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
     * Access level for player. Defaults to everyone
     *
     * @var array
     */
	protected static $_accessLevel = array( 1 );

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
            if( 
				( TaxiApp_Settings::retrieve( "driver_user_group" ) && TaxiApp_Settings::retrieve( "driver_user_group" ) == Ayoola_Application::getUserInfo( 'access_level' ) )
				|| $this->getParameter( 'driver_mode' )
			)
            {
				$list->listTitle = 'Booking Management';

				$where['driver_id'] = Ayoola_Application::getUserInfo( 'user_id' );
				$editKey = ' <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Info/?' . $this->getIdColumn() . '=%KEY%">Booking Details</a>';
            }
			else
			{
				$where['passenger_id'] = Ayoola_Application::getUserInfo( 'user_id' );
			}
		}

		$allBookings = TaxiApp_Booking::getInstance()->select( null, $where );


		$listOptions = array();

		if( $pendingPickups = TaxiApp_Booking::getInstance()->select( null, $where + array( 'status' => array( 0, 1 ) ) ) )
		{
			$listOptions += array( 
				'pickup' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_List/?view_type=pickup\', \'' . $this->getObjectName() . '\' );" title="">Pending Pick-ups ( ' . count( $pendingPickups ) . ' )</a>',    
			);
		}

		if( $pendingDeliveries = TaxiApp_Booking::getInstance()->select( null, $where + array( 'status' => array( 2,3 ) ) ) )
		{
			$listOptions += array( 
				'Delivery' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_List/?view_type=delivery\', \'' . $this->getObjectName() . '\' );" title="">Pending Deliveries ( ' . count( $pendingDeliveries ) . ' )</a>',    
			);
		}

		if( $unpaid = TaxiApp_Booking::getInstance()->select( null, $where + array( 'paid' => '' ) ) )
		{
			$listOptions += array( 
				'Unpaid' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_List/?view_type=unpaid\', \'' . $this->getObjectName() . '\' );" title="">Unpaid ( ' . count( $unpaid ) . ' )</a>',    
			);
		}

		$listOptions += array( 
			'Creator' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/widgets/TaxiApp_Booking_Manual/\', \'' . $this->getObjectName() . '\' );" title="">Manual Booking</a>',    
		);

		if( isset( $_REQUEST['view_type'] ) )
		{
			if( $_REQUEST['view_type'] == 'pickup' )
			{
				$allBookings = $pendingPickups;
				$list->listTitle = 'Pending Pickups';

			}
			if( $_REQUEST['view_type'] == 'delivery' )
			{
				$allBookings = $pendingDeliveries;
				$list->listTitle = 'Pending Deliveries';
			}
			if( $_REQUEST['view_type'] == 'unpaid' )
			{
				$allBookings = $unpaid;
				$list->listTitle = 'Unpaid Deliveries';
			}
			$listOptions = array( 'Creator' => '' );
		}

		$list->setData( $allBookings );
		$list->setListOptions( 
								$listOptions
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No bookings that match your criteria here.' );

		$listInfo = 			array(
		'Booking ID' => array( 'field' => 'booking_id', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
		'to' => array( 'field' => 'destination', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
		'Booked' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
		'Pick up' => array( 'field' => 'pickup_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
		'Delivery' => array( 'field' => 'delivery_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
		'status' => array( 'field' => 'status', 'value' =>  '%FIELD%', 'value_representation' =>  self::getStatusMeaning() ), 
		'paid' => array( 'field' => 'paid', 'value' =>  '%FIELD%' ), 
		'<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Info/?' . $this->getIdColumn() . '=%KEY%">Details</a>', 
		);


		if( $this->hasPriviledge( array( 99, 98 ) ) )
		{
			$listInfo += 			array(
				' ' => '<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Editor/?' . $this->getIdColumn() . '=%KEY%">Edit</a>', 
				'   ' => '<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_Delete/?' . $this->getIdColumn() . '=%KEY%">x</a>', 
			);
		}

		if( ( TaxiApp_Settings::retrieve( "driver_user_group" ) && TaxiApp_Settings::retrieve( "driver_user_group" ) == Ayoola_Application::getUserInfo( 'access_level' ) )
		|| $this->getParameter( 'driver_mode' ) )
		{
			$listInfo += 			array(
				' ' => '<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Booking_UpdateStatus/?' . $this->getIdColumn() . '=%KEY%">Status</a>', 
			);

		}
		
		$list->createList( $listInfo );
		return $list;
    } 
	// END OF CLASS
}
