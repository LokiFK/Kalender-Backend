<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public static function errorJSON($errCode, $msg)
        {
            http_response_code($errCode);
            ErrorUI::errorMsg($msg);
        }

        public static function errorVisual($errCode, $msg)
        {
            ErrorUI::error($errCode, $msg);
        }

        public static function view(string $component, array $data = array(), array $safeData = array())
        {
            $content = Response::load($component, $data, $safeData);
            if ($content !== null) { return $content; }
            
            ErrorUI::error(500, 'Error setting up HTML');
            exit;

            return "";
        }
        
        public static function load(string $componentName, $data, $safeData) {
            $pathHTML = "./public/html/" . $componentName . ".html";

            if (is_file($pathHTML)) {
                $component = file_get_contents($pathHTML);
                $component = Response::processTags($component, $componentName, $data, $safeData);

                return $component;
            }
        }

        private static function processTags($component, $componentName, $data, $safeData)
        {
            foreach ($safeData as $key => $value) {                                     //vor anderen Tags, damit diese von safeData ausgelöst werden können
                $component = str_replace("{{ " . $key . " }}", $value, $component);         //safeData darf also niemals userInput enthalten!!!!!!!!!!
            }  

            $tagsActivator = ["{","}"];
            $tags = [ ["?php","?"], ["! @for "," !"], ["% "," %"], ["+ "," +"], ["# extend "," #"]];
            $i = 0;
            $template = null;

            while($i < strlen($component)){
                $j = strpos($component, $tagsActivator[0], $i);
                if ($j === false) { break; }

                $i = $j+1;

                foreach ($tags as $tag){
                    if( substr($component, $j+1, strlen($tag[0])) == $tag[0]){
                        $endStartTag = $j + strlen($tagsActivator[0]) + strlen($tag[0]);
                        $k = strpos($component, $tag[1] . $tagsActivator[1], $endStartTag); // k = index of end
                        if ($k === false) { break; }

                        $innerData = substr($component, $endStartTag, $k-$endStartTag);

                        //echo "Found tag: ".$tag[0]." innerData $innerData";

                        if ($tag[0] == "# extend ") { //special case needs other treatmand

                            $component = substr_replace($component, "", $j, $k-$j + strlen($tag[1]) + strlen($tagsActivator[1]) );
                            $template = Response::loadTemplate($template, $innerData, $safeData);

                            $i = $j+1;
                        } else if ($tag[0] == "! @for ") {                 //Whatever is goping to happen here

                            $component = Response::interpreteForLoop($component, $j, $k);

                            $i = $j+1;
                        } else {                                            //Group of "replacers"
                            $content = "";

                            if ($tag[0] == "% ") {
                                $content = Response::loadRessources($component, $componentName, $innerData);
                            } else if ($tag[0] == "+ ") {
                                $content = Response::loadCodeSnippets($component, $innerData, $safeData);
                            } else if ($tag[0] == "?php") { //Warning: messing around with userinput would be a really dangerous security issue.
                                $content = Response::loadAndExecutePHP($component, $innerData);
                            }

                            $component = substr_replace($component, $content, $j, $k-$j + strlen($tag[1]) + strlen($tagsActivator[1]));
                            $i = $j+1 + strlen($content); 
                        }
                    }
                }
            }

            if($template !== null){
                $component = str_replace("{# here #}", $component, $template);
            }

            foreach ($data as $key => $value) {                                     //am Ende werden die "normalen" Daten eingesetzt
                $component = str_replace("{{ " . $key . " }}", $value, $component);        
            }  

            return $component;
        }

        private static function loadTemplate($template, $innerData, $safeData)
        {
            $containerContents = explode("@", $innerData);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $content = Response::view($containerContents[0], array(), $safeData);
                $content = str_replace("{# create ".$containerContents[1]." #}", "{# here #}", $content);
            } else {
                $content = "<!--Container not found-->";
            }
            if($template === null){
                $template = $content;
            } else {
               $template = str_replace("{# here #}", $content, $template); 
            }
            return $template;
        }

        public static function interpreteForLoop($component, $j, $k)
        {
            return $component;
        }

        public static function loadRessources($component, $componentName, $innerData)
        {
            if (strpos($innerData, 'styles') !== false) {
                $path = "./public/css/" . $componentName . ".css";
                if (is_file($path)) {
                    $styles = '<style>' . file_get_contents($path) . '</style>';
                    return $styles;
                } else {
                    return "<!--Styles konnten nicht geladen werden-->";
                }
            } else if (strpos($innerData, 'script') !== false) {
                $path = "./public/js/" . $componentName . ".js";
                if (is_file($path)) {
                    $script = '<script>' . file_get_contents($path) . '</script>';
                    return $script;
                } else {
                    return "<!--Script konnten nicht geladen werden-->";
                }
            }
            return "<!--Fehlerhafter Style oder Script tag-->";
        }

        public static function loadCodeSnippets($component, $innerData, $safeData)
        {
            $innerData = str_replace(' ', '', $innerData);
            if (is_file("./public/html/" . $innerData . ".html")) {
                $content = Response::view($innerData, array(), $safeData);
                return $content;
            }
            return "<!--CodeSnippet nicht gefunden-->";
        }

        private static function loadAndExecutePHP($component, $innerData) {     //Warnung never user data
            $res = eval($innerData);
            if($res === null){ return ""; }
            return $res;
        }









        /*private static function renderData($component, $componentName, $data)
        {
            // Find all listed tags and load their individual data
            foreach (["<?php/?>", "{! @for / !}", "{% / %}", "{+ / +}", "{# extend / #}"] as $needle) {            //order is important!!!! (refer caes <?php)
                $i = 0;
                $j = 0;

                $needlePrefix = explode("/", $needle)[0];
                $needleSuffix = explode("/", $needle)[1];

                while ($i <= strlen($component) && ($j <= strlen($component))) {
                    $j = strpos($component, $needlePrefix, $i);  // j = index of start, i = offset from last found
                    if ($j === false) { break; }

                    $k = strpos($component, $needleSuffix, $j + strlen($needlePrefix)); // k = index of end
                    if ($k === false) { break; }

                    $innerData = substr($component, $j + strlen($needlePrefix), $k - ($j + strlen($needlePrefix)));

                    if ($needlePrefix == "{# extend ") { //special case needs other treatmand
                        $component = Response::loadLayout($component, $innerData, $data, $j, $k, $needleSuffix);
                        break;  //more than one container doesn't have a particular usecase.
                    } else if ($needlePrefix == "{! @for ") {
                        $component = Response::interpreteForLoop($component, $j, $k);
                    } else {
                        $content = "";
                        if ($needlePrefix == "{% ") {
                            $content = Response::loadRessources($component, $componentName, $innerData);
                        } else if ($needlePrefix == "{+ ") {
                            $content = Response::loadCodeSnippets($component, $innerData, $data);
                        } else if ($needlePrefix == "<?php") { //Warning: messing around with userinput would be a really dangerous security issue.
                            $content = Response::loadAndExecutePHP($component, $innerData, $j, $k);
                        }
                        $component = substr_replace($component, $content, $j, $k - $j + strlen($needleSuffix));
                        $i = $j + strlen($content); 
                    }
                }
            }

            // Fill all variables with their data
            foreach ($data as $key => $value) {
                $component = str_replace("{{ " . $key . " }}", $value, $component);
            }

            return $component;
        }

        public static function interpreteForLoop($component, $j, $k)
        {
            return $component;
        }

        public static function loadLayout($component, $innerData, $data, $j, $k, $needleSuffix)
        {
            $containerContents = explode("@", $innerData);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $component = substr_replace($component, "", $j, $k - $j + strlen($needleSuffix));
                $content = Response::view($containerContents[0], $data);
                $component = str_replace("{# create ".$containerContents[1]." #}", $component, $content);
            } else {
                $content = "<!--Container not found-->";
                $component = substr_replace($component, $content, $j, $k + strlen($needleSuffix));
            }
            return $component;
        }*/


        /*public static function loadContainers($component, $data)
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
        }*/
    }
?>
