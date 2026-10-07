# PHP Raw Socket Web Server

A lightweight, zero-dependency HTTP web server built from scratch in pure PHP using native socket functions (`ext-sockets`). 

Designed for educational purposes and understanding how web servers, TCP sockets, and HTTP protocols operate under the hood without relying on external web servers like Nginx, Apache, or PHP-FPM.

## Features

- **Raw Socket Handling:** Binds to TCP ports, accepts incoming client connections, and manages socket read/write streams.
- **HTTP Request Parsing:** Parses raw HTTP request strings into structured method, URI, headers, and body objects.
- **HTTP Response Serialization:** Formats status codes, headers, content length, and response bodies into valid HTTP/1.1 response packets.
- **CLI Executable:** Simple command-line runner script with configurable port binding.

## Requirements

- PHP 8.0 or higher
- PHP `sockets` extension enabled (`ext-sockets`)
- Composer (for autoloading)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/php-webserver.git
   cd php-webserver
   ```

2. Install dependencies (PSR-4 autoloader):
   ```bash
   composer install
   ```

## Usage

Start the server using the CLI script specifying an optional port (defaults to port 80):

```bash
php server 8080
```

Once running, open your browser or use `curl`:
```bash
curl http://localhost:8080
```

## Project Structure

```text
├── server               # CLI entry point script
├── src/
│   ├── Server.php       # TCP socket server listener & event loop
│   ├── Response.php     # HTTP response builder
└── public/
    └── index.php        # Public web root entry
```

## How It Works

1. **Initialization:** `Server` creates a TCP socket (`socket_create`), binds it to the specified host and port (`socket_bind`), and listens for incoming connections (`socket_listen`).
2. **Request Cycle:** Inside an infinite event loop, the server accepts client connections (`socket_accept`), reads the raw HTTP payload (`socket_read`), and parses it into a `Request` object.
3. **Handling:** The request is passed to your callback closure where you can inspect it and return a `Response` object.
4. **Response Transmission:** The `Response` object is cast to a string (formatting headers and body according to HTTP/1.1 specifications) and written back to the client socket (`socket_write`), after which the connection is closed.

## License

MIT