<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate_RateService
 * @copyright  Copyright (c) 2023 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: RateService.php Friday 17th of February 2023 12:53PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class TaxiApp_Rate_RateService extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.1';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'rateservice_name' => 'INPUTTEXT',
  'rateservice_description' => 'INPUTTEXT',
);


	// END OF CLASS
}
