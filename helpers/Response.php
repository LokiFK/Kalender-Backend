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

        public static function view(string $component, array $data = array(), array $safeData = array(), array $loopData = array())
        {
            $content = Response::load($component, $data, $safeData, $loopData);
            if ($content !== null) { return $content; }
            
            ErrorUI::error(500, 'Error setting up HTML');
            exit;
        }

        public static function load(string $componentName, $data, $safeData, $loopData)
        {
            $pathHTML = "./public/html/" . $componentName . ".html";

            if (is_file($pathHTML)) {
                $component = file_get_contents($pathHTML);
                return Response::processTags($component, $componentName, $data, $safeData, $loopData);
            }
        }

        private static function processTags($component, $componentName, $data, $safeData, $loopData)
        {
            foreach ($safeData as $key => $value) {                                     //vor anderen Tags, damit diese von safeData ausgelöst werden können
                $component = str_replace("{{ " . $key . " }}", $value, $component);         //safeData darf also niemals userInput enthalten!!!!!!!!!!
            }  

            $tagsActivator = ["{","}"];
            $tags = [ ["?php","?"], ["! "," !"], ["% "," %"], ["+ "," +"], ["# extend "," #"], ["[ ", " ]"]];
            $i = 0;
            $template = null;

            while($i < strlen($component)){
                $j = strpos($component, $tagsActivator[0], $i);
                if ($j === false) { break; }

                $i = $j+1;

                foreach ($tags as $tag){
                    if (substr($component, $j + 1, strlen($tag[0])) == $tag[0]) {
                        $endStartTag = $j + strlen($tagsActivator[0]) + strlen($tag[0]);
                        $k = null;

                        if ($tag[0] == "! "){
                            $tiefe = 1;
                            $m = $endStartTag;
                            while($tiefe > 0){
                                $l = strpos($component, $tagsActivator[0].$tag[0], $m);
                                $k = strpos($component, $tag[1].$tagsActivator[1], $m);
                                if ($l === false && $k === false) { break 2; }
                                if ($l === false || $k < $l) {
                                    $m = $k + 1;
                                    $tiefe--;
                                } else {
                                    $m = $l + 1;
                                    $tiefe++;
                                }
                            }
                        } else {
                            $k = strpos($component, $tag[1] . $tagsActivator[1], $endStartTag); // k = index of end
                            if ($k === false) { break; }
                        }

                        $innerData = substr($component, $endStartTag, $k-$endStartTag);

                        //echo "Found tag: ".$tag[0]." innerData $innerData <br>";

                        if ($tag[0] == "# extend ") { //special case needs other treatmand

                            $component = substr_replace($component, "", $j, $k-$j + strlen($tag[1]) + strlen($tagsActivator[1]) );
                            $template = Response::loadTemplate($template, $innerData, $safeData);

                            $i = $j+1;
                        } else {                                            //Group of "replacers"
                            $content = "";

                            if ($tag[0] == "% ") {
                                $content = Response::loadResources($componentName, $innerData);
                            } else if ($tag[0] == "+ ") {
                                $content = Response::loadCodeSnippets($innerData, $safeData);
                            } else if ($tag[0] == "?php") { //Warning: messing around with userinput would be a really dangerous security issue.
                                $content = Response::loadAndExecutePHP($innerData);
                            } else if ($tag[0] == "! ") { 
                                $content = Response::interpretForLoop($innerData, $safeData, $loopData);
                            } else if ($tag[0] == "[ ") {
                                $datasets = explode(", ", $innerData);
                                foreach($datasets as $set) {
                                    $parts = explode("=>", $set, 2);
                                    $safeData[$parts[0]] = $parts[1];
                                    $data[$parts[0]] = $parts[1];
                                }
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

        public static function loadResources($componentName, $innerData)
        {
            if ($innerData == "styles") {
                $path = "./public/css/" . $componentName . ".css";
                if (is_file($path)) {
                    return '<style>' . file_get_contents($path) . '</style>';
                } else {
                    return "<!--Styles konnten nicht geladen werden-->";
                }
            } else if ($innerData == "script") {
                $path = "./public/js/" . $componentName . ".js";
                if (is_file($path)) {
                    return '<script>' . file_get_contents($path) . '</script>';
                } else {
                    return "<!--Script konnten nicht geladen werden-->";
                }
            } else if(substr($innerData, 0, 4) == "css/"){
                $path = "./public/" . $innerData . ".css";
                if (is_file($path)) {
                    return '<style>' . file_get_contents($path) . '</style>';
                } else {
                    return "<!--Styles konnten nicht geladen werden-->";
                }
            } else if (substr($innerData, 0, 3) == "js/"){
                $path = "./public/" . $innerData . ".js";
                if (is_file($path)) {
                    return '<script>' . file_get_contents($path) . '</script>';
                } else {
                    return "<!--Script konnten nicht geladen werden-->";
                }
            }
            return "<!--Fehlerhafter Style oder Script tag-->";
        }

        public static function loadCodeSnippets($innerData, $safeData)
        {
            $innerData = str_replace(' ', '', $innerData);
            if (is_file("./public/html/" . $innerData . ".html")) {
                return Response::view($innerData, array(), $safeData);
            }
            return "<!--CodeSnippet nicht gefunden-->";
        }

        private static function loadAndExecutePHP($innerData) {     //Warnung never user data
            $res = eval($innerData);
            if($res === null){ return ""; }
            return $res;
        }
        
        public static function interpretForLoop($innerData, $safeData, $loopData)
        {
            $content = "";
            $parts = explode(":", $innerData, 2);
            if (array_key_exists($parts[0], $loopData)) {
                $array = $loopData[$parts[0]];
                foreach ($array as $data) {
                    $iteration = Response::processTags($parts[1], "noName", array(), $safeData, $loopData);
                    if(is_string($data)){
                        $iteration = str_replace("{{ " . $parts[0] . " }}", $data, $iteration);
                    } else if(is_array($data)){
                        foreach ($data as $key => $value) {                                    
                            $iteration = str_replace("{{ " . $key . " }}", $value, $iteration);        
                        }    
                    }
                    $content = $content . $iteration;
                }
            }
            return $content;
        }
    }
?>
