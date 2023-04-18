<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Service
 * @copyright  Copyright (c) 2023 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Service.php Tuesday 18th of April 2023 09:56AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class TaxiApp_Service extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.0';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'service_name' => 'INPUTTEXT',
  'service_options' => 'JSON',
);


	// END OF CLASS
}
