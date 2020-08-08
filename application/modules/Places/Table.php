<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Place_Table
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Table.php Saturday 6th of June 2020 09:16AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class Places_Table extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.3';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
        'place_id' => 'INPUTTEXT',
        'name' => 'INPUTTEXT',
        'address' => 'INPUTTEXT',
        'long' => 'INPUTTEXT',
        'lat' => 'INPUTTEXT',
        'type' => 'JSON',
      );


	// END OF CLASS
}
