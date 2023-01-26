<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Rate
 * @copyright  Copyright (c) 2023 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Rate.php Thursday 26th of January 2023 06:42PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class TaxiApp_Rate extends PageCarton_Table
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
  'rate' => 'INPUTTEXT',
  'from_city' => 'INPUTTEXT',
  'to_city' => 'INPUTTEXT',
  'from_state' => 'INPUTTEXT',
  'to_state' => 'INPUTTEXT',
  'from_country' => 'INPUTTEXT',
  'to_country' => 'INPUTTEXT',
);


	// END OF CLASS
}
