<?php

namespace Parable\Http;

class Request
{
    /** @var string */
    protected $method;

    /** @var array */
    protected $headers = [];

    /**
     * Set some basic information we're going to need.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders() ?: [];
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->method === $method;
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * @return bool
     */
    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    public function getHeader($key)
    {
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
