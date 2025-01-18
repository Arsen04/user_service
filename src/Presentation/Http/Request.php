<?php

namespace App\Presentation\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Request
    implements ServerRequestInterface
{
    public const string METHOD_GET = 'GET';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_OPTIONS = 'OPTIONS';
    public const string METHOD_HEAD = 'HEAD';

    public const string CONTENT_TYPE_JSON = 'application/json';
    public const string CONTENT_TYPE_XML = 'application/xml';
    public const string CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    public const string CONTENT_TYPE_TEXT = 'text/plain';
    public const string CONTENT_TYPE_HTML = 'text/html';

    public const string HEADER_CONTENT_TYPE = 'Content-Type';
    public const string HEADER_ACCEPT = 'Accept';
    public const string HEADER_AUTHORIZATION = 'Authorization';
    public const string HEADER_USER_AGENT = 'User-Agent';
    public const string HEADER_CACHE_CONTROL = 'Cache-Control';

    public const string PARAMS_QUERY = 'query';
    public const string PARAMS_BODY = 'body';

    private string $method;
    private UriInterface $uri;
    private array $headers = [];
    private array $queryParams = [];
    private array $parsedBody = [];
    private string $protocolVersion;
    private string $requestTarget;
    private array $cookies = [];
    private array $uploadedFiles = [];
    private array $attributes = [];
    private StreamInterface $body;

    /**
     * @param string $method
     * @param string $protocolVersion
     * @param UriInterface $uri
     * @param array $headers
     * @param array $queryParams
     * @param array $cookies
     * @param array $uploadedFiles
     * @param array $attributes
     * @param StreamInterface $body
     */
    public function __construct(
        string $method,
        string $protocolVersion,
        UriInterface $uri,
        array $headers,
        array $queryParams,
        array $cookies,
        array $uploadedFiles,
        array $attributes,
        StreamInterface $body
    ) {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->queryParams = $queryParams;
        $this->cookies = $cookies;
        $this->uploadedFiles = $uploadedFiles;
        $this->attributes = $attributes;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param $method
     * @return $this
     */
    public function withMethod($method): self
    {
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param $preserveHost
     * @return $this
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $clone = clone $this;
        $clone->uri = $uri;
        return $clone;
    }

    /**
     * @return array|\string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getHeader($name): mixed
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function withHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = (array) $value;
        return $clone;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @param array $query
     * @return $this
     */
    public function withQueryParams(array $query): self
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    /**
     * @return array
     */
    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }

    /**
     * @param $data
     * @return $this
     */
    public function withParsedBody($data): self
    {
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return MessageInterface
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;
        return $clone;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return isset($this->headers[$name]) ? implode(', ', $this->headers[$name]) : '';
    }

    /**
     * @param string $name
     * @param $value
     * @return MessageInterface
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $clone = clone $this;
        $clone->headers[$name] = array_merge($clone->headers[$name] ?? [], (array) $value);
        return $clone;
    }

    /**
     * @param string $name
     * @return MessageInterface
     */
    public function withoutHeader(string $name): MessageInterface
    {
        $clone = clone $this;
        unset($clone->headers[$name]);
        return $clone;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return MessageInterface
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /**
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    /**
     * @param string $requestTarget
     * @return RequestInterface
     */
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    /**
     * @return array
     */
    public function getServerParams(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     * @return ServerRequestInterface
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->cookies = $cookies;
        return $clone;
    }

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @param array $uploadedFiles
     * @return ServerRequestInterface
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param $value
     * @return ServerRequestInterface
     */
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * @param string $name
     * @return ServerRequestInterface
     */
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }
}