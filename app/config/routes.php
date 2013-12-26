<?php

$router = new Phalcon\Mvc\Router();

$router->add("/set-language/{language:[a-z]+}", array(
    'controller' => 'index',
    'action' => 'setLanguage'
));

