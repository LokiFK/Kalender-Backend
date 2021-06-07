<?php

    require_once 'Path.php';


    class Routes {
        private $routes;


        public function __construct()
        {
            $this->routes = array();
        }

        public function get($route, $activator)
        {
            $newRoute = new Route( $route, 'GET', $activator);
            array_push($this->routes, $newRoute);
        }

        public function post($route, $activator)
        {
            $newRoute = new Route( $route, 'POST', $activator);
            array_push($this->routes, $newRoute);
        }

        public function listen()
        {
            $path = Path::getPath();
            if (strpos($path, '/Calendar/server') !== false) {
                $path = str_replace('/Calendar/server', '', $path);
            }

            for ($i=0; $i < count($this->routes); $i++) {
                if ($this->routes[$i]->getRoute() == $path) {
                    if ($_SERVER['REQUEST_METHOD'] == $this->routes[$i]->getMethod()) {
                        $activator = explode('@', $this->routes[$i]->getActivator());
                        $class = new $activator[0]();
                        $method = $activator[1];
                        $class->$method(new Request($_REQUEST, $_SERVER['REQUEST_METHOD']), new Response());
                        return;
                    }
                }
            }
            ErrorUI::errorCode(404);
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
