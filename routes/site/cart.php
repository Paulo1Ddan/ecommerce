<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Class\Page;
use Class\PageAdmin;
use Class\Model\User;
use Class\Model\Cat;
use Class\Model\Product;
use Class\Model\Cart;

$app->get("/ecommerce/cart", function (Request $request, Response $response) {
    $cart = Cart::getFromSession();

    $page = new Page();

    $page->setTpl("cart", array());
});
