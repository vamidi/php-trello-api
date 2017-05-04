# php-trello-api

A Simple Object Oriented wrapper for the Trello API, written in PHP7. Uses [Trello API v1](https://trello.com/docs/index.html). With oauth1 authentication

## Features
* 

## Requirements

* PHP >= 7.0.0 with [cURL](http://php.net/manual/en/book.curl.php) extension

## Installation

Using git:
```bash
$ git clone https://github.com/vamidi/php-trello-api.git

```

## Basic usage

```php
use Trello\Client;

$trello = new Trello();
$trello->getToken();

$boards = $trello->rest("GET", array("path" => 'members/me/boards')));
```

The `$trello` object gives you access to the entire Trello API.

## Documentation
* Official [API documentation](https://trello.com/docs/index.html).

## FUTURE PLANS
* Create classes for i.e boards, cards, lists etc.
* Webhook handlers

## Contributing

Feel free to make any comments, file issues.

## License

`php-trello-api` is licensed under the MIT License - see the LICENSE file for details

## Credits

- Largely inspired by the excellent [php-trello](https://bitbucket.org/mattzuba/php-trello) developed by the guy(s) at [mattzuba](https://bitbucket.org/mattzuba/php-trello)
- Thanks to Trello for the API and documentation.
