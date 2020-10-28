<?php
namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

class DefaultController {

    public function index(): ResponseInterface
    {
        return new HtmlResponse('<h1>_framework !</h1>');
    }

}
