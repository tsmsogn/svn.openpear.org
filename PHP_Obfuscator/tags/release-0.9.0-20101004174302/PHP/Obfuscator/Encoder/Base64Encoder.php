<?php
/**
 * PHP_Obfuscator_Encoder_Base64Encoder class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN: $Id$
 * @link      http://openpear.org/package/PHP_Obfuscator
 */

require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';

/**
 * PHP_Obfuscator_Encoder_Base64Encoder class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Encoder_Base64Encoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{

    /**
     * Return encoded previous code
     *
     * @return string Return encoded code
     * @access public
     */
    public function encode() {
        return str_replace('=', '', base64_encode($this->str));
    }

    /**
     * Return the code for decoding
     *
     * @return string The code for decoding in 'sprintf/printf' format
     * @access public
     */
    public function decode() {
        return 'base64_decode(%s)';
    }
}
