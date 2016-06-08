<?php

class Router {

    private $routes;

    public function __construct() {
        $routesPath = ROOT . "/config/routes.php";
        $this->routes = include $routesPath;
        $this->run();
    }

    private function getURI() {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return substr(trim($_SERVER['REQUEST_URI']),1  );
        }
    }

    private function run() {
        $uri = $this->getURI();

        foreach ($this->routes as $uriPattern => $path) {
            if (preg_match("~$uriPattern~", $uri)) {
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
                $segments = explode('/', $internalRoute);
                $controllerName = ucfirst(array_shift($segments) . "Controller");
                $actionName = "action" . ucfirst(array_shift($segments));
                $params = $segments;
                break;
            }
        }
        $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            include_once $controllerFile;
        }
        
         $controllerObject = new $controllerName();
         $controllerObject->$actionName();
    }

}
