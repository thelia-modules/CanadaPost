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


namespace CanadaPost\Api\Response;

/**
 * Class ResponseApi
 * @package CanadaPost\Api
 * @author Julien Chanséaume <julien@thelia.net>
 */
class ResponseApi
{
    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $xmlResponse;

    /** @var array */
    protected $dataResponse = [];

    /** @var array */
    protected $errors = [];

    /**
     * @return array
     */
    public function getDataResponse()
    {
        return $this->dataResponse;
    }

    /**
     * @param array $dataResponse
     */
    public function setDataResponse($dataResponse)
    {
        $this->dataResponse = $dataResponse;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     *
     * @param string $errorCode
     * @param string $errorMessage
     */
    public function addError($errorCode, $errorMessage)
    {
        $this->errors[] = [$errorCode, $errorMessage];

        return $this;
    }


    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getXmlResponse()
    {
        return $this->xmlResponse;
    }

    /**
     * @param string $xmlResponse
     */
    public function setXmlResponse($xmlResponse)
    {
        $this->xmlResponse = $xmlResponse;

        return $this;
    }
}
