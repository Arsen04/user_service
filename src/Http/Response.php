<?php

namespace App\Http;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Response
    implements ResponseInterface
{
    public const STATUS_OK = 200;
    public const STATUS_CREATED = 201;
    public const STATUS_ACCEPTED = 202;
    public const STATUS_NO_CONTENT = 204;
    public const STATUS_MOVED_PERMANENTLY = 301;
    public const STATUS_FOUND = 302;
    public const STATUS_NOT_MODIFIED = 304;
    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_METHOD_NOT_ALLOWED = 405;
    public const STATUS_CONFLICT = 409;
    public const STATUS_INTERNAL_SERVER_ERROR = 500;
    public const STATUS_NOT_IMPLEMENTED = 501;
    public const STATUS_BAD_GATEWAY = 502;
    public const STATUS_SERVICE_UNAVAILABLE = 503;

    public const CONTENT_TYPE_JSON = 'application/json';
    public const CONTENT_TYPE_XML = 'application/xml';
    public const CONTENT_TYPE_HTML = 'text/html';
    public const CONTENT_TYPE_TEXT = 'text/plain';
    public const PROTOCOL_VERSION = '1.1';

    public const HEADER_CONTENT_TYPE = 'Content-Type';
    public const HEADER_LOCATION = 'Location';
    public const HEADER_CACHE_CONTROL = 'Cache-Control';
    public const HEADER_EXPIRES = 'Expires';

    public const CACHE_NO_CACHE = 'no-cache, no-store, must-revalidate';
    public const CACHE_PUBLIC = 'public';
    public const CACHE_PRIVATE = 'private';

    public const NO_RECORDS_MESSAGE = 'No records retrieved.';
    public const SUCCESS_STATUS_MESSAGE = 'Success.';
    public const FAILED_STATUS_FAILURE = 'Failure.';

    private int $statusCode = self::STATUS_OK;
    private array $headers = [];
    private ?StreamInterface $body;
    private string $reasonPhrase = '';
    private string $protocolVersion;

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param array $headers
     * @param StreamInterface|null $body
     * @param string $protocolVersion
     */
    public function __construct(
        int $statusCode = 200,
        string $reasonPhrase = 'OK',
        array $headers = [self::HEADER_CONTENT_TYPE => self::CONTENT_TYPE_JSON],
        StreamInterface $body = null,
        string $protocolVersion = self::PROTOCOL_VERSION
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->reasonPhrase = $reasonPhrase;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * Sends the response to the client.
     *
     * @return StreamInterface|string
     */
    public function send(): StreamInterface|string
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    header("{$name}: {$v}", false);
                }
            } else {
                header("{$name}: {$value}");
            }
        }

        if (!isset($this->headers[self::HEADER_CONTENT_TYPE])) {
            $this->headers[self::HEADER_CONTENT_TYPE] = self::CONTENT_TYPE_JSON;
        }

        $body = $this->getBody();
        $jsonData = json_encode($body);

        if ($jsonData === false) {
            throw new \RuntimeException("Failed to encode data to JSON: " . json_last_error_msg());
        }

        return $this->body;
    }

    public function withJson(array $data, int $statusCode = 200, array $headers = []): ResponseInterface
    {
        $jsonData = json_encode($data);

        if ($jsonData === false) {
            throw new \RuntimeException('JSON encoding error: ' . json_last_error_msg());
        }

        $this->headers[self::HEADER_CONTENT_TYPE] = self::CONTENT_TYPE_JSON;
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $jsonData);
        rewind($stream);

        $this->body = new Stream($stream);
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Create a StreamInterface from the provided data.
     *
     * @param string $data
     * @return StreamInterface
     */
    private function createBody(string $data): StreamInterface
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $data);
        rewind($stream);

        return new Stream($stream);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $code
     * @param $reasonPhrase
     * @return $this
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;
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
     * @return array|string[]
     */
    public function getHeader($name): array
    {
        return $this->headers[$name] ?? [];
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
     * Return a new instance with updated headers.
     *
     * @param array $headers
     * @return ResponseInterface
     */
    public function withHeaders(array $headers): ResponseInterface
    {
        $new = clone $this;
        $new->headers = $headers;
        return $new;
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
     * @return $this
     */
    public function withBody(StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
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
}