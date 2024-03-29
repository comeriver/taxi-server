<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class TaxiApp_Rate_List extends TaxiApp_Rate_Abstract
{
 	
		/**
		 * 
		 * 
		 * @var string 
		 */
		protected static $_objectTitle = 'Rate List';   

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

			$rateId = '';
			$rateService = '';
			if( ! empty( $_REQUEST['rateservice_id'] ) )
			{
				$this->_dbWhereClause['rateservice_id'] = $_REQUEST['rateservice_id'];
				$rateId = '&rateservice_id=' . $_REQUEST['rateservice_id'];
				$rateService = ' (' . TaxiApp_Rate_RateService::getInstance()->selectOne( 'rateservice_name', array( 'rateservice_id' => $_REQUEST['rateservice_id'] ) ) . ')';

			}

			$list->pageName = $this->getObjectName();
			$list->listTitle = self::getObjectTitle() . $rateService;
			$list->setData( $this->getDbData() );
			$list->setListOptions( 
									array( 
											'Creator' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Rate_Creator/?' . $rateId . '\', \'page_refresh\' );" title="">Add Rate</a>',    
										) 
								);
			$list->setKey( $this->getIdColumn() );
			$list->setNoRecordMessage( 'No rate data has been added yet.' );
			
			$list->createList
			(
				array(
						'rate' => array( 'field' => 'rate', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
						'from_city' => array( 'field' => 'from_city', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
						'to_city' => array( 'field' => 'to_city', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
						'from_state' => array( 'field' => 'from_state', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
						'to_state' => array( 'field' => 'to_state', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     
						'from_country' => array( 'field' => 'from_country', 'value' =>  '%FIELD%', 'filter' =>  '' ),                     'to_country' => array( 'field' => 'to_country', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
						'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
						'%FIELD% <a style="font-size:smaller;"  href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Rate_Editor/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>', 
						'%FIELD% <a style="font-size:smaller;" href="javascript:" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/TaxiApp_Rate_Delete/?' . $this->getIdColumn() . '=%KEY%\', \'' . $this->getObjectName() . '\' );"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
					)
			);
			return $list;
		} 
	// END OF CLASS
}
