<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    TaxiApp_Booking
 * @copyright  Copyright (c) 2020 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Booking.php Saturday 6th of June 2020 09:16AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class TaxiApp_Booking extends PageCarton_Table
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
        'destination' => 'INPUTTEXT',
        'driver_id' => 'INT',
        'passenger_id' => 'INPUTTEXT',
        'route_info' => 'JSON',
        'driver_location' => 'JSON',
        'passenger_location' => 'JSON',
        'status' => 'INT',
        'last_status_time' => 'INT',
      );


	// END OF CLASS
}
