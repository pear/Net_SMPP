<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Air2Web submit_sm command class
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
 * @since      Release 0.4.5
 * @link       http://pear.php.net/package/Net_SMPP
 */

// Place includes, constant defines and $_GLOBAL settings here.
require_once 'Net/SMPP/Command/submit_sm.php';

/**
 * Air2Web submit_sm class
 *
 * This is the class which has Air2Web-specific optional paramater definitions.
 *
 * @category   Networking
 * @package    Net_SMPP
 * @author     Ian Eure <ieure@php.net>
 * @copyright  (c) Copyright 2005 WebSprockets, LLC.
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @version    CVS:     $Revision$
 * @since      Release 0.4.5
 * @link       http://pear.php.net/package/Net_SMPP
 */
class Net_SMPP_Command_Vendor_Air2Web_submit_sm extends Net_SMPP_Command_submit_sm {

    /**
     * The vendor this class belongs to
     *
     * @var  string
     */
    var $vendor = 'Air2Web';

    // Vendor-specific
    /**
     * Air2Web premium content indicator
     *
     * Note that although this is a string field, you may not put the actual
     * charge amount here, you must use one of the NET_SMPP_AIR2WEB_PREMIUM
     * constants. See Air2Web.php for the values.
     *
     * @var  string
     * @see  Air2Web.php
     */
    var $Premium_content_indicator;

    /**
     * Vendor definitions
     *
     * @var     array
     * @access  protected
     */
    var $_vdefs = array(
        'Premium_content_indicator' => array(
            'type' => 'ostring',
            'max' => 2
        )
    );
}