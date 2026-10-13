<?php namespace ClanCats\Station\PHPServer;

class Response {

    protected string $content;
    protected int $status;
    protected array $headers = [];

    /**
     * HTTP Status Phrases
     */
    protected static array $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ];

    public function __construct(string $content, int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Get the status phrase for the current status code
     */
    public function getStatusPhrase(): string
    {
        return static::$phrases[$this->status] ?? 'Unknown Status';
    }

    public function __toString(): string
    {
        $phrase = $this->getStatusPhrase();
        $res = "HTTP/1.1 {$this->status} {$phrase}\r\n";

        $hasContentType = false;
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === 'content-type') {
                $hasContentType = true;
                break;
            }
        }

        if (!$hasContentType) {
            $res .= "Content-Type: text/html; charset=UTF-8\r\n";
        }

        $res .= "Content-Length: " . strlen($this->content) . "\r\n";
        
        foreach ($this->headers as $key => $value) {
            $res .= "{$key}: {$value}\r\n";
        }

        $res .= "\r\n";
        $res .= $this->content;

        return $res;
    }
}
