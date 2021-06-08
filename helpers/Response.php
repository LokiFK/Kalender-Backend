<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public function error(int $errorCode, string $msg)
        {
            ErrorUI::error($errorCode, $msg);
        }

        public static function view(string $component, array $data = array())
        {
            $content = Response::load($component, $data);
            if ($content !== null) { return $content; }
            
            ErrorUI::error(500, 'Error setting up HTML');
            exit;
        }
        
        public static function load(string $componentName, $data) {
            $pathHTML = "./public/html/" . $componentName . ".html";

            if (is_file($pathHTML)) {
                $component = file_get_contents($pathHTML);
                $component = Response::renderData($component, $componentName, $data);

                return $component;
            }
        }

        private static function renderData($component, $componentName, $data)
        {
            // Fill all variables with their data
            foreach ($data as $key => $value) {
                $component = str_replace("{{ " . $key . " }}", $value, $component);
            }

            // Find all listed tags and load their individual data
            foreach (["{% / %}", "{+ / +}", "{# extend / #}", "<?php/?>"] as $needle) {
                $i = 0;
                $j = 0;

                $needlePrefix = explode("/", $needle)[0];
                $needleEnding = explode("/", $needle)[1];

                while ($i <= strlen($component) && ($j <= strlen($component))) {
                    $j = strpos($component, $needlePrefix, $i);  // j = index of start, i = offset from last found
                    if ($j === false) { break; }

                    $j = $j + strlen($needlePrefix); // j = index of start + prefix
                    $k = strpos($component, $needleEnding, $j); // k = index of end
                    if ($k === false) { break; }

                    $i = $k + strlen($needleEnding); // i = index of end + ending

                    $innerData = substr($component, $j, $k - $j);
                    
                    if ($needlePrefix == "{% ") {
                        Response::loadRessources($component, $componentName, $innerData);
                    } else if ($needlePrefix == "{+ ") {
                        Response::loadCodeSnippets($component, $innerData, $data);
                    } else if ($needlePrefix == "{# extend ") {
                        Response::loadLayout($component, $innerData, $data, $j, $k);
                    } else if ($needlePrefix == "<?php") {
                        Response::loadAndExecutePHP($component, $innerData, $j, $k);
                    }
                }
            }

            return $component;
        }

        public static function loadLayout($component, $innerData, $data, $j, $k)
        {
            $containerContents = explode("@", $innerData);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $component = substr_replace($component, "", $j, $k - $j);
                $content = Response::view($containerContents[0], $data);
                $component = str_replace("{# create ".$containerContents[1]." #}", $component, $content);
                $component = str_replace("{# extend  #}", "", $component);
            }
            return $component;
        }

        public static function loadRessources($component, $componentName, $innerData)
        {
            if (strpos($innerData, 'styles') !== false) {
                $path = "./public/css/" . $componentName . ".css";
                if (is_file($path)) {
                    $styles = '<style>' . file_get_contents($path) . '</style>';
                    $component = str_replace('{% styles %}', $styles, $component);
                } else {
                    $component = str_replace('{% styles %}', '', $component);
                }
            } else if (strpos($innerData, 'script') !== false) {
                $path = "./public/js/" . $componentName . ".js";
                if (is_file($path)) {
                    $script = '<script>' . file_get_contents($path) . '</script>';
                    $component = str_replace('{% script %}', $script, $component);
                } else {
                    $component = str_replace('{% script %}', '', $component);
                }
            }
            return $component;
        }

        public static function loadCodeSnippets($component, $innerData, $data)
        {
            $innerData = str_replace(' ', '', $innerData);
            if (is_file("./public/html/" . $innerData . ".html")) {
                $content = Response::view($innerData, $data);
                $component = str_replace("{+ " . $innerData . " +}", $content, $component);
            }
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

        // Private because of potential security leaks
        private static function loadAndExecutePHP($component, $innerData, $j, $k) {
            $res = eval($innerData);

            if ($res !== null) {
                $component = substr_replace($component, $res, $j - 5, $k - $j + 7);
            } else {
                $component = substr_replace($component, "", $j - 5, $k - $j + 7);
            }
        }
    }
?>
