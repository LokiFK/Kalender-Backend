<?php

    require_once 'Path.php';


    class Routes {
        private $routes;


        public function __construct()
        {
            $this->routes = array();
        }

        public function get($route, $activator, $neededStatus = 0)
        {
            $newRoute = new Route($route, 'GET', $activator, $neededStatus);
            array_push($this->routes, $newRoute);
        }

        public function post($route, $activator, $neededStatus = 0)
        {
            $newRoute = new Route( $route, 'POST', $activator, $neededStatus);
            array_push($this->routes, $newRoute);
        }

        public function listen()
        {
            $path = Path::getPath();
            if ($path == "") {
                $path = "/";
            }

            for ($i=0; $i < count($this->routes); $i++) {
                if ($this->routes[$i]->getRoute() == $path) {
                    if ($_SERVER['REQUEST_METHOD'] == $this->routes[$i]->getMethod()) {
                        if($this->routes[$i]->getNeededStatus() <= Auth::getStatus()){
                            $activator = explode('@', $this->routes[$i]->getActivator());
                            $class = new $activator[0]();
                            $method = $activator[1];
                            $class->$method(new Request($_REQUEST, $_SERVER['REQUEST_METHOD']), new Response());
                            return;
                        } else {
                            echo "Bitte bei Account mit dne notwendigen Berechtigungen anmelden.";
                            exit();    
                        }
                    }
                }
            }
            ErrorUI::error(404, 'Page not found');
        }
    }


    class Route {
        private $route;
        private $method;
        private $activator;
        private $neededStatus;

        public function __construct($route, $method, $activator, $neededStatus)
        {
            $this->route = $route;
            $this->method = $method;
            $this->activator = $activator;
            $this->neededStatus = $neededStatus;
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

        public function getNeededStatus()
        {
            return $this->neededStatus;
        }
    }

    

?>
