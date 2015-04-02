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


namespace CanadaPost\Api;

use CanadaPost\Api\Exception\ApiCallException;
use CanadaPost\Api\Response\ResponseApi;
use CanadaPost\CanadaPost;
use CanadaPost\Model\Config\CanadaPostConfigValue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BaseApi
 * @package CanadaPost\Api
 * @author Julien Chanséaume <julien@thelia.net>
 */
abstract class BaseApi
{
    protected $options;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function doRequest($xmlRequest = null)
    {
        if ($this->options['test']) {
            $url = $this->options['url-test'];
        } else {
            $url = $this->options['url'];
        }
        $url = $this->lazyBuildUrl($url);

        $curl = curl_init($url); // Create REST Request

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->options['curlSslVerifyPeer']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->options['curlSslVerifyHost']);
        curl_setopt($curl, CURLOPT_CAINFO, $this->options['curlCaInfo']);
        curl_setopt($curl, CURLOPT_POST, $this->options['method']);
        if ('post' === $this->options['method']) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $this->options['curlReturnTransfer']);
        curl_setopt($curl, CURLOPT_HTTPAUTH, $this->options['curlOptHttpAuth']);
        curl_setopt($curl, CURLOPT_USERPWD, $this->options['username'] . ':' . $this->options['password']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHttpHeader());

        $curlResponse = curl_exec($curl); // Execute REST Request

        $response = $this->getResponseApi();

        if (curl_errno($curl)) {
            $response->addError(curl_errno($curl), curl_error($curl));
        }

        $response->setStatusCode((int) curl_getinfo($curl, CURLINFO_HTTP_CODE));

        curl_close($curl);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/', '', $curlResponse) . '</root>');

        if (!$xml) {
            foreach (libxml_get_errors() as $error) {
                $response->addError($error->code, $error->message);
            }
        } else {
            $response->setXmlResponse($xml);
            if (in_array($response->getStatusCode(), [200, 204, 304])) {
                $data = $this->getXmlAsArray($xml);
                $response->setDataResponse($data);
            } else {
                if ($xml->{'messages'}) {
                    $messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');
                    foreach ($messages as $message) {
                        $response->addError(
                            (string) $message->code,
                            (string) $message->description
                        );
                    }
                }
            }
        }

        if (0 < count($response->getErrors())) {
            throw new ApiCallException($response);
        }

        return $response;
    }

    public function setLocale($locale)
    {
        $this->options['locale'] = $locale;
    }

    protected function getHttpHeader()
    {
        $headers = [];

        foreach ($this->options['curlHttpHeader'] as $header) {
            $headers[] = str_replace('%locale%', $this->options['locale'], $header);
        }

        return $headers;
    }

    /**
     * Get a new ResponseApi object
     *
     * @return ResponseApi
     */
    protected function getResponseApi()
    {
        return new ResponseApi();
    }

    protected function getXmlAsArray($xml)
    {
        return json_decode(json_encode($xml), true);
    }

    protected function lazyBuildUrl($url)
    {
        return $url;
    }

    private function configureOptions(OptionsResolverInterface $resolver)
    {
        if (0 === intval(CanadaPost::getConfigValue(CanadaPostConfigValue::MODE_PRODUCTION))) {
            $username = CanadaPost::getConfigValue(CanadaPostConfigValue::TEST_USERNAME);
            $password = CanadaPost::getConfigValue(CanadaPostConfigValue::TEST_PASSWORD);
        } else {
            $username = CanadaPost::getConfigValue(CanadaPostConfigValue::USERNAME);
            $password = CanadaPost::getConfigValue(CanadaPostConfigValue::PASSWORD);
        }

        $quoteType = (0 === intval(CanadaPost::getConfigValue(CanadaPostConfigValue::QUOTE_TYPE_COMMERCIAL)))
            ? 'counter'
            : 'commercial';

        $resolver->setDefaults(
            [
                'locale' => 'fr-CA',

                'test' => (0 === intval(CanadaPost::getConfigValue(CanadaPostConfigValue::MODE_PRODUCTION))),
                'username' => $username,
                'password' => $password,
                'customerNumber' => CanadaPost::getConfigValue(CanadaPostConfigValue::CUSTOMER_NUMBER),
                'contractId' => CanadaPost::getConfigValue(CanadaPostConfigValue::CONTRACT_ID),
                'origin' => CanadaPost::getConfigValue(CanadaPostConfigValue::ORIGIN_POSTALCODE),
                'quoteType' => $quoteType,

                'url' => null,
                'url-test' => null,
                'method' => 'get',
                'curlHttpHeader' => [],

                'curlSslVerifyPeer' => true,
                'curlSslVerifyHost' => 2,
                'curlCaInfo' => realpath(__DIR__) . '/cert/cacert.pem',
                'curlReturnTransfer' => true,
                'curlOptHttpAuth' => CURLAUTH_BASIC
            ]
        );

        $this->setDefaultOptions($resolver);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }
}
