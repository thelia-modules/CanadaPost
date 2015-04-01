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

use CanadaPost\Api\Response\ResponseApi;
use CanadaPost\CanadaPost;
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->options['curlHttpHeader']);

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
                        $response->addError($message->code, $message->description);
                    }
                }
            }
        }

        return $response;
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
        if (0 === intval(CanadaPost::getConfigValue('mode_production'))) {
            $username = CanadaPost::getConfigValue('test_username');
            $password = CanadaPost::getConfigValue('test_password');
        } else {
            $username = CanadaPost::getConfigValue('username');
            $password = CanadaPost::getConfigValue('password');
        }

        $resolver->setDefaults(
            [
                'test' => (0 === intval(CanadaPost::getConfigValue('mode_production'))),
                'username' => $username,
                'password' => $password,
                'customerNumber' => CanadaPost::getConfigValue('customer_number'),
                'origin' => CanadaPost::getConfigValue('origin_postalcode'),

                'url' => null,
                'url-test' => null,
                'method' => 'get',
                'curlHttpHeader' => null,

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
