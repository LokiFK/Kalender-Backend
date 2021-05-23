<?php

    require_once 'Path.php';


    class Routes {
        public const METHOD_POST = 'POST';
        public const METHOD_GET = 'GET';


        private $routes;

        public function __construct()
        {
            $this->routes = array();
        }

        public function add($route, $method, $activator)
        {
            $newRoute = new Route($route, $method, $activator);
            array_push($this->routes, $newRoute);
        }

        public function listen()
        {
            $path = Path::getPath();

            for ($i=0; $i < count($this->routes); $i++) {
                if ($this->routes[$i]->getRoute() == $path) {
                    if ($_SERVER['REQUEST_METHOD'] == $this->routes[$i]->getMethod()) {
                        $activator = $this->routes[$i]->getActivator();
                        $class = new $activator[0]();
                        $method = $activator[1];
                        $class->$method();
                        return;
                    }
                }
            }
        }
    }


    class Route {
        private $route;
        private $method;
        private $activator;

        public function __construct($route, $method, $activator)
        {
            $this->route = $route;
            $this->method = $method;
            $this->activator = $activator;
        }

        public function getRoute()
        {
            return $this->route;
        }

        public function getMethod()
        {
            return $this->method;
        }

        public function getActivator()
        {
            return $this->activator;
        }
    }

?>