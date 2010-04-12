<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * mBlox data_sm command class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.2.5dev7
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command/data_sm.php';

/**
 * mBlox data_sm class
 *
 * This is the class which has mBlox-specific optional paramater definitions.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.2.5dev7
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_Vendor_mBlox_data_sm extends Net_SMPP_Command_data_sm {

    /**
     * The vendor this class belongs to
     *
     * @var  string
     */
    var $vendor = 'mBlox';

    /**
     * SMS application associated with this message
     *
     * This is the service_type required by mBlox.
     *
     * @var  string
     */
    var $service_type = 3115;

    // Vendor-specific
    /**
     * mBlox operator specification
     *
     * This contains an internal mBlox carrier id, and must be set correctly
     * for the message to be delivered. Set it to one of the
     * NET_SMPP_MBLOX_OPERATOR_* constants.
     *
     * @var  string
     */
    var $mblox_operator;

    /**
     * mBlox PSMS tariff
     *
     * If non-zero, this determines the amount which will be charged to the
     * recipient's wireless bill.
     *
     * @var  string
     */
    var $mblox_tariff;

    /**
     * mBlox session ID
     *
     * This is required for some PSMS implementations. Check the mBlox
     * documentation.
     *
     * @var  string
     */
    var $mblox_sessionid;

    /**
     * Vendor definitions
     *
     * @var     array
     * @access  protected
     */
    var $_vdefs = array(
        'mblox_tariff' => array(
            'type' => 'ostring',
            'max' => 5
        ),
        'mblox_operator' => array(
            'type' => 'ostring',
            'size' => 5
        ),
        'mblox_sessionid' => array(
            'type' => 'string',
            'size' => 45
        )
    );
}