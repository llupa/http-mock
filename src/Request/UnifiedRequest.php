<?php
namespace InterNations\Component\HttpMock\Request;

use GuzzleHttp\Collection;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Query;
use GuzzleHttp\Stream\StreamInterface;

class UnifiedRequest
{
    /**
     * @var RequestInterface
     */
    private $wrapped;

    /**
     * @var string
     */
    private $userAgent;

    public function __construct(RequestInterface $wrapped, array $params = [])
    {
        $this->wrapped = $wrapped;
        $this->init($params);
    }

    /**
     * Get the user agent of the request
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Get a string representation of the message
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->wrapped;
    }

    /**
     * Get the HTTP protocol version of the message
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->wrapped->getProtocolVersion();
    }

    /**
     * Get the body of the message
     *
     * @return StreamInterface|null
     */
    public function getBody()
    {
        return $this->wrapped->getBody();
    }

    /**
     * Gets all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     * @return array Returns an associative array of the message's headers.
     */
    public function getHeaders()
    {
        return $this->wrapped->getHeaders();
    }

    /**
     * Retrieve a header by the given case-insensitive name.
     *
     * By default, this method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma. Because some header should not be concatenated together using a
     * comma, this method provides a Boolean argument that can be used to
     * retrieve the associated header values as an array of strings.
     *
     * @param string  $header  Case-insensitive header name.
     * @param boolean $asArray Set to true to retrieve the header value as an
     *                        array of strings.
     *
     * @return array|string
     */
    public function getHeader($header, $asArray = false)
    {
        return $this->wrapped->getHeader($header, $asArray = false);
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $header Case-insensitive header name.
     *
     * @return boolean Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($header)
    {
        return $this->wrapped->hasHeader($header);
    }

    /**
     * Gets the request URL as a string.
     *
     * @return string Returns the URL as a string.
     */
    public function getUrl()
    {
        return $this->wrapped->getUrl();
    }

    /**
     * Get the resource part of the the request, including the path, query
     * string, and fragment.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->wrapped->getResource();
    }

    /**
     * Get the collection of key value pairs that will be used as the query
     * string in the request.
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->wrapped->getQuery();
    }

    /**
     * Get the HTTP method of the request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->wrapped->getMethod();
    }

    /**
     * Get the URI scheme of the request (http, https, etc.).
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->wrapped->getScheme();
    }

    /**
     * Get the port scheme of the request (e.g., 80, 443, etc.).
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->wrapped->getPort();
    }

    /**
     * Get the host of the request.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->wrapped->getHost();
    }

    /**
     * Get the path of the request (e.g. '/', '/index.html').
     *
     * @return string
     */
    public function getPath()
    {
        return $this->wrapped->getPath();
    }

    /**
     * Get the request's configuration options.
     *
     * @return Collection
     */
    public function getConfig()
    {
        return $this->wrapped->getConfig();
    }

    private function init(array $params)
    {
        foreach ($params as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
}
