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

        public static function view(string $component, array $data = array())
        {
            $pathHTML = "./public/$component.html";
            $pathCSS = "./public/css/$component.css";
            $pathDefaultCSS = "./public/css/default-styles.css";
            if (is_file($pathHTML)) {
                $content = replaceFiles( file_get_contents($pathHTML) );
                foreach ($data as $key => $value) {
                    $content = str_replace('{{ ' . $key . ' }}', $value, $content);
                }
                if (is_file($pathCSS)) {
                    $styles = '<style>' . file_get_contents($pathCSS) . file_get_contents($pathDefaultCSS) . '</style>';
                    $content = str_replace('{% styles %}', $styles, $content);
                } else {
                    $content = str_replace('{% styles %}', '', $content);
                }
                return $content;
            }
            $res = new Response();
            $res->error('Error setting up HTML');
            $res->errorCode(500);
        }
        
        public static function replaceFiles(string $component){
            $i = 0;
            while(true){
               $j = strpos($component, "{{%", $i) + 3; 
               if($j==false){
                 return $component;  
               } 
               $k = strpos($component, "%}}", $j);
               if($k==false){
                 return $component;  
               }    
               $i = $k;
               $path = substr($component, $j, $k-$j);
               if(substr($component, -2)=="()"){
                   $activator = substr($component, 0, -2);
                   $activator = explode('@', $activator);
                        $class = new $activator[0]();
                        $method = $activator[1];
                        $content = $class->$method();
                   $component = substr_replace($component, $content, $j-3, $k-$j+6);
               }elseif(is_file($path)){
                 $content = replaceFiles( file_getcontents($path) );
                 $component = substr_replace($component, $content, $j-3, $k-$j+6);
               }    
               }    
            }   
        }
    }
