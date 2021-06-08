<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public function errorCode(int $errorCode)
        {
            http_response_code($errorCode);
        }

        public function error(string $msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }

        public static function view(string $component, array $data = array())       //dont allow other userinput than $data without checking loadingWithPHP
        {
            //echo "view: $component <br>";
            $pathHTML = "./public/html/$component.html";
            $pathCSS = "./public/css/$component.css";
            $pathJS = "./public/js/$component.js";
            $pathDefaultCSS = "./public/css/default-styles.css";
            if (is_file($pathHTML)) {
                $content = Response::load($pathHTML, $data);
                foreach ($data as $key => $value) {
                    $content = str_replace('{{ ' . $key . ' }}', $value, $content);
                }
                if (is_file($pathCSS)) {
                    $styles = '<style>' . file_get_contents($pathCSS) . file_get_contents($pathDefaultCSS) . '</style>';
                    $content = str_replace('{% styles %}', $styles, $content);
                } else {
                    $content = str_replace('{% styles %}', '', $content);
                }
                if (is_file($pathJS)) {
                    $script = '<script>' . file_get_contents($pathJS) . '</script>';
                    $content = str_replace('{% script %}', $script, $content);
                } else {
                    $content = str_replace('{% script %}', '', $content);
                }
                return $content;
            }
            $res = new Response();
            $res->error('Error setting up HTML');
            $res->errorCode(500);
        }
        
        public static function load(string $pathHTML, $data) {
            $component = file_get_contents($pathHTML);           //in this Method should never get componentparts from the User. It has to be directly out of a trusted file!!!!!!
            $component = Response::executePHP($component);
            $component = Response::loadContainers($component, $data);
            $component = Response::loadLinkedFiles($component, $data);
            return $component;
        }

        public static function loadContainers($component, $data)
        {
            $i = 0;
            if ($i > strlen($component)) return $component;
            $j = strpos($component, "{# extend ", $i);
            if ($j === false) {
                return $component;
            }
            $j=$j+10;
            if ($j > strlen($component)) return $component;
            $k = strpos($component, " #}", $j);
            if ($k === false) {
                return $component;
            }    
            $i = $k+3;
            $containerContents = substr($component, $j, $k-$j);
            $containerContents = explode("@", $containerContents);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $component = substr_replace($component, "", $j-10, $k-$j+13);
                $content = Response::view($containerContents[0], $data);
                $component = str_replace("{# create ".$containerContents[1]." #}",$component,$content);
            }
            
            return $component;
        }

        private static function loadLinkedFiles(string $component, $data){         
            //echo "component: $component<br>";
            $i = 0;
            while (true) {
                if ($i > strlen($component)) return $component;
                $j = strpos($component, "{{% ", $i); 
                if ($j === false) {
                    return $component;
                } 
                $j=$j+4;
                if ($j > strlen($component)) return $component;
                $k = strpos($component, " %}}", $j);
                if ($k === false) {
                    return $component;
                }    
                $i = $k+4;
                $path = substr($component, $j, $k-$j);
                //echo ">>>>>>>>>>>>>j: $j, k: $k path: $path<<<<<<<<<<<<";
                /*if (substr($component, -2) == "()") {
                    $activator = substr($component, 0, -2);
                    $activator = explode('@', $activator);
                            $class = new $activator[0]();
                            $method = $activator[1];
                            $content = $class->$method();
                    $component = substr_replace($component, $content, $j-4, $k-$j+8);
                } else*/ 
                if (is_file("./public/html/".$path.".html")) {
                    $content = Response::view($path, $data);
                    $component = substr_replace($component, $content, $j-4, $k-$j+8);
                }
            }    
        }
        private static function executePHP($component){             //aus Sicherheitsgründen nur von loadingWithPHP() aufrufen!!!
            $si = 0;
            while (true) {
                if ($si > strlen($component)) return $component;
                $sj = strpos($component, "<?php", $si);
                if ($sj === false) {
                    return $component;
                } 
                $sj=$sj+5;
                if ($sj > strlen($component)) return $component;
                $sk = strpos($component, "?>", $sj);
                if ($sk === false) {
                    return $component;
                }    
                $si = $sk+2;
                $code = substr($component, $sj, $sk-$sj);
                $res = eval($code);                                 //Achtung Variablen werden geteilt, verwenden Sie in den Dateien keine hier genutzten Variablen
                if ($res!=null) {
                    $component = substr_replace($component, $res, $sj-5, $sk-$sj+7);
                } else {
                    $component = substr_replace($component, "", $sj-5, $sk-$sj+7);
                }
            } 
        }
    }
?>
