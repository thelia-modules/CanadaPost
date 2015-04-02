<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace CanadaPost\Api\Exception;

use CanadaPost\Api\Response\ResponseApi;

/**
 * Class ApiCallException
 * @package CanadaPost\Api\Exception
 * @author Julien Chanséaume <julien@thelia.net>
 */
class ApiCallException extends \RuntimeException
{

    /** @var ResponseApi */
    protected $response;

    public function __construct(ResponseApi $responseApi)
    {
        parent::__construct();

        $this->response = $responseApi;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->response->getErrors();
    }

    public function getErrorsAsString($separator = "\n")
    {
        $errorMessage = '';

        /** @var \Exception $error */
        foreach ($this->response->getErrors() as $code => $message) {
            $errorMessage .= sprintf("[%s] %s%s", $code, $message, $separator);
        }

        return rtrim($errorMessage, $separator);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
