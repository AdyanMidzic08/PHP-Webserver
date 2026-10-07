<?php namespace ClanCats\Station\PHPServer;

class Request {

    protected string $method;
    protected string $uri;
    protected array $headers = [];
    protected string $body = '';

    public function __construct(string $method, string $uri, array $headers = [], string $body = '')
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function parse(string $raw): self
    {
        $lines = explode("\r\n", $raw);
        $firstLine = array_shift($lines);
        
        if (!$firstLine) {
            throw new \Exception("Invalid request");
        }

        list($method, $uri) = explode(' ', $firstLine);

        $headers = [];
        while ($line = array_shift($lines)) {
            if (empty($line)) break;
            list($key, $value) = explode(': ', $line, 2);
            $headers[$key] = $value;
        }

        $body = implode("\r\n", $lines);

        return new static($method, $uri, $headers, $body);
    }

    public function getMethod(): string { return $this->method; }
    public function getUri(): string { return $this->uri; }
    public function getHeaders(): array { return $this->headers; }
    public function getBody(): string { return $this->body; }
}
