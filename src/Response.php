<?php namespace ClanCats\Station\PHPServer;

class Response {

    protected string $content;
    protected int $status;
    protected array $headers = [];

    public function __construct(string $content, int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function __toString(): string
    {
        $res = "HTTP/1.1 {$this->status} OK\r\n";
        $res .= "Content-Type: text/html\r\n";
        $res .= "Content-Length: " . strlen($this->content) . "\r\n";
        
        foreach ($this->headers as $key => $value) {
            $res .= "{$key}: {$value}\r\n";
        }

        $res .= "\r\n";
        $res .= $this->content;

        return $res;
    }
}
