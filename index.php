<?php

try {

    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(
        array(
            './app/controllers/',
            './app/models/',
            './app/views/'
        )
    )->register();

    
    $loader->registerClasses(
        array(
            "PHPMailer"         => "app/ext/PHPMailer/PHPMailer.php",
        )            
    )->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();

     //Setting up the view component
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir('./app/views/');
        return $view;
    });
	
	$di->set('url', function() {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri('http://localhost/resetpwd/');
        return $url;
    });

    /**
     * Load router from external file
     */
    $di->set('router', function(){
        require __DIR__.'/app/config/routes.php';
        return $router;
    });

    /**
     * Start the session the first time some component request the session service
     */
    $di->set('session', function(){
        $session = new Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    });
    
    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di->set('db', function(){
            return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                    "host" => 'localhost',
                    "username" => 'root',
                    "password" => '',
                    "dbname" => 'resetpwd',
            ));
    });

    //Handle the request
    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
     echo "PhalconException: ", $e->getMessage();
}
