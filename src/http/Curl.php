<?php
namespace onix\http;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;

class Curl extends Component
{
    /**
     * CURLOPT_USERAGENT
     * @var string
     */
    public $userAgent = "Yii2-Curl-Agent";

    /**
     * CURLOPT_TIMEOUT
     * @var int
     */
    public $timeout = 30;

    /**
     * CURLOPT_CONNECTTIMEOUT
     * @var int
     */
    public $connectTimeout = 30;

    /**
     * CURLOPT_RETURNTRANSFER
     * @var bool
     */
    public $returnTransfer = true;

    /**
     * CURLOPT_HEADER
     * @var bool
     */
    public $outputHeader = false;

    /**
     * CURLOPT_PROXY
     * @var string|bool
     */
    public $proxy = false;

    /**
     * CURLOPT_PROXYTYPE
     * @var int
     */
    public $proxyType = CURLPROXY_HTTP;

    /**
     * CURLOPT_PROXYAUTH
     * @var int
     */
    public $proxyAuth = 0;

    /**
     * CURLOPT_PROXYUSERPWD
     * @var string
     */
    public $proxyUserPwd = "";

    /**
     * Holds response data right after sending a request.
     * @var string
     */
    public $response = null;

    /**
     * This value will hold HTTP-Status Code. False if request was not successful.
     * @var integer HTTP-Status Code
     */
    public $responseCode = null;

    /**
     * Custom options holder
     * @var array HTTP-Status Code
     */
    private $options = [];


    /**
     * Holds cURL-Handler
     * @var resource
     */
    private $curl = null;

    /**
     * Default curl options
     * @var array default curl options
     */
    private $defaultOptions = [
        CURLOPT_USERAGENT,
        CURLOPT_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT,
        CURLOPT_RETURNTRANSFER,
        CURLOPT_HEADER,
    ];

    /**
     * Holds HTTP headers
     * @var array
     */
    private $headers = [];

    /**
     * Start performing GET-HTTP-Request
     *
     * @param string $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     *
     * @throws Exception
     */
    public function get($url, $raw = true)
    {
        return $this->httpRequest('GET', $url, $raw);
    }


    /**
     * Start performing HEAD-HTTP-Request
     *
     * @param string $url
     *
     * @return mixed response
     *
     * @throws Exception
     */
    public function head($url)
    {
        return $this->httpRequest('HEAD', $url);
    }


    /**
     * Start performing POST-HTTP-Request
     *
     * @param string $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     *
     * @throws Exception
     */
    public function post($url, $raw = true)
    {
        return $this->httpRequest('POST', $url, $raw);
    }


    /**
     * Start performing PUT-HTTP-Request
     *
     * @param string $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     *
     * @throws Exception
     */
    public function put($url, $raw = true)
    {
        return $this->httpRequest('PUT', $url, $raw);
    }


    /**
     * Start performing DELETE-HTTP-Request
     *
     * @param string $url
     * @param boolean $raw if response body contains JSON and should be decoded
     *
     * @return mixed response
     *
     * @throws Exception
     */
    public function delete($url, $raw = true)
    {
        return $this->httpRequest('DELETE', $url, $raw);
    }

    /**
     * @param int $key
     * @param mixed $value
     */
    private function setDefaultOption($key, $value)
    {
        switch ($key) {
            case CURLOPT_USERAGENT:
                $this->userAgent = $value;
                break;
            case CURLOPT_TIMEOUT:
                $this->timeout = $value;
                break;
            case CURLOPT_CONNECTTIMEOUT:
                $this->connectTimeout = $value;
                break;
            case CURLOPT_RETURNTRANSFER:
                $this->returnTransfer = $value;
                break;
            case CURLOPT_HEADER:
                $this->outputHeader = $value;
                break;
        }
    }

    /**
     * @return array
     */
    private function getDefaultOptions()
    {
        $options = [
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_RETURNTRANSFER => $this->returnTransfer,
            CURLOPT_HEADER => $this->outputHeader
        ];

        if ($this->proxy) {
            $options[CURLOPT_PROXY] = $this->proxy;
            $options[CURLOPT_PROXYTYPE] = $this->proxyType;
            if ($this->proxyAuth) {
                $options[CURLOPT_PROXYAUTH] = $this->proxyAuth;
                $options[CURLOPT_PROXYUSERPWD] = $this->proxyUserPwd;
            }
        }

        return $options;
    }


    /**
     * Set curl option
     *
     * @param int $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        //set value
        if (in_array($key, $this->defaultOptions) && $key !== CURLOPT_WRITEFUNCTION) {
            $this->setDefaultOption($key, $value);
        } else {
            $this->options[$key] = $value;
        }

        //return self
        return $this;
    }

    /**
     * Set curl options
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options + $this->options;

        return $this;
    }

    /**
     * @param string|array $name
     * @param string|null $value
     */
    public function addHeaders($name, $value = null)
    {
        if (is_array($name)) {
            $this->headers = array_merge($this->headers, $name);
        } else {
            $this->headers[$name] = $value;
        }
    }

    /**
     * Convert name=>value headers to strings
     * @return array
     */
    private function getHeaders()
    {
        $result = [];
        foreach ($this->headers as $name => $value) {
            $result[] = sprintf("%s: %s", $name, $value);
        }

        return $result;
    }

    /**
     * Unset a single curl option
     *
     * @param string $key
     *
     * @return $this
     */
    public function unsetOption($key)
    {
        //reset a single option if its set already
        if (isset($this->options[$key])) {
            unset($this->options[$key]);
        }

        return $this;
    }


    /**
     * Unset all curl option, excluding default options.
     *
     * @return $this
     */
    public function unsetOptions()
    {
        //reset all options
        $this->options = [];
        $this->headers = [];

        return $this;
    }


    /**
     * Total reset of options, responses, etc.
     *
     * @return $this
     */
    public function reset()
    {
        if ($this->curl !== null) {
            curl_close($this->curl); //stop curl
        }

        //reset all options
        $this->unsetOptions();

        //reset response & status code
        $this->curl = null;
        $this->response = null;
        $this->responseCode = null;

        return $this;
    }


    /**
     * Return a single option
     *
     * @param string|integer $key
     * @return mixed|boolean
     */
    public function getOption($key)
    {
        //get merged options depends on default and user options
        $mergesOptions = $this->getOptions();

        //return value or false if key is not set.
        return isset($mergesOptions[$key]) ? $mergesOptions[$key] : false;
    }


    /**
     * Return merged curl options and keep keys!
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        if (sizeof($this->headers) > 0) {
            $options = [
                CURLOPT_HTTPHEADER => $this->getHeaders()
            ];
        }

        return $options + $this->options + $this->getDefaultOptions();
    }


    /**
     * Get curl info according to http://php.net/manual/de/function.curl-getinfo.php
     *
     * @param int $opt
     * @return mixed
     */
    public function getInfo($opt = null)
    {
        if ($this->curl !== null && $opt === null) {
            return curl_getinfo($this->curl);
        } elseif ($this->curl !== null && $opt !== null) {
            return curl_getinfo($this->curl, $opt);
        } else {
            return [];
        }
    }


    /**
     * Performs HTTP request
     *
     * @param string  $method
     * @param string  $url
     * @param boolean $raw if response body contains JSON and should be decoded -> helper.
     *
     * @throws Exception if request failed
     *
     * @return mixed
     */
    private function httpRequest($method, $url, $raw = false)
    {
        //set request type and writer function
        $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $this->setOption(CURLOPT_NOBODY, true);
            $this->unsetOption(CURLOPT_WRITEFUNCTION);
        }

        //setup error reporting and profiling
        Yii::debug('Start sending cURL-Request: '.$url."\n", __METHOD__);
        Yii::beginProfile($method.' '.$url.'#'.md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        /**
         * proceed curl
         */
        $this->curl = curl_init($url);
        curl_setopt_array($this->curl, $this->getOptions());
        $body = curl_exec($this->curl);

        //check if curl was successful
        if ($body === false) {
            switch (curl_errno($this->curl)) {
                case 7:
                    $this->responseCode = 'timeout';
                    return false;
                    break;

                default:
                    throw new Exception('curl request failed: ' . curl_error($this->curl), curl_errno($this->curl));
                    break;
            }
        }

        //retrieve response code
        $this->responseCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->response = $body;

        //end yii debug profile
        Yii::endProfile($method.' '.$url .'#'.md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        //check responseCode and return data/status
        if ($this->getOption(CURLOPT_CUSTOMREQUEST) === 'HEAD') {
            return true;
        } else {
            $this->response = $raw ? $this->response : Json::decode($this->response);
            return $this->response;
        }
    }
}
