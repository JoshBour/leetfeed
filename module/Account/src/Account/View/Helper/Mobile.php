<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Account\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Mobile extends AbstractHelper
{

    public function __invoke()
    {
       return preg_match('/ipad|iphone|itouch|android|blackberry|iemobile/', strtolower($_SERVER['HTTP_USER_AGENT']));
    }

}