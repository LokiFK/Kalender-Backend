<?php

    class Response {
        public function json(array $input)
        {
            echo json_encode($input);
        }

        public static function errorJSON($errCode, $msg)
        {
            http_response_code($errCode);
            ErrorUI::errorMsg($errCode, $msg);
        }

        public static function errorVisual($errCode, $msg)
        {
            ErrorUI::error($errCode, $msg);
        }

        public static function view(string $component, array $data = array(), array $safeData = array(), array $loopData = array())
        {
            return Response::viewObject($component, new ReplaceData($data, $safeData, $loopData));
        }
        public static function viewObject($component, $replaceData){
            $content = Response::load($component, $replaceData);
            if ($content !== null) { return $content; }
            
            ErrorUI::error(500, 'Error setting up HTML');
            exit;
        }

        public static function load(string $componentName, $replaceData)
        {
            $pathHTML = "./public/html/" . $componentName . ".html";

            if (is_file($pathHTML)) {
                $component = file_get_contents($pathHTML);
                return Response::processTags($component, $componentName, $replaceData);
            }
        }

        private static function processTags($component, $componentName, $replaceData)
        {
            foreach ($replaceData->safeData as $key => $value) {                                     //vor anderen Tags, damit diese von safeData ausgelöst werden können
                $component = str_replace("{{ " . $key . " }}", $value, $component);         //safeData darf also niemals userInput enthalten!!!!!!!!!!
            }  

            $replaceData->safeData['origin']=$componentName;

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
                            $template = Response::loadTemplate($template, $innerData, $replaceData);

                            $i = $j+1;
                        } else {                                            //Group of "replacers"
                            $content = "";

                            if ($tag[0] == "% ") {
                                $content = Response::loadResources($componentName, $innerData);
                            } else if ($tag[0] == "+ ") {
                                $content = Response::loadCodeSnippets($innerData, $replaceData);
                            } else if ($tag[0] == "?php") { //Warning: messing around with userinput would be a really dangerous security issue.
                                $content = Response::loadAndExecutePHP($innerData, $replaceData);
                            } else if ($tag[0] == "! ") { 
                                $content = Response::interpretForLoop($innerData, $replaceData);
                            } else if ($tag[0] == "[ ") {
                                $datasets = explode(", ", $innerData);
                                foreach($datasets as $set) {
                                    $parts = explode("=>", $set, 2);
                                    $replaceData->safeData[$parts[0]] = $parts[1];
                                    $replaceData->data[$parts[0]] = $parts[1];
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

            foreach ($replaceData->data as $key => $value) {                                     //am Ende werden die "normalen" Daten eingesetzt
                $component = str_replace("{{ " . $key . " }}", $value, $component);        
            }  

            return $component;
        }

        private static function loadTemplate($template, $innerData, $replaceData)
        {
            $containerContents = explode("@", $innerData);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $content = Response::viewObject($containerContents[0], $replaceData);
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

        public static function loadCodeSnippets($innerData, $replaceData)
        {
            $innerData = str_replace(' ', '', $innerData);
            if (is_file("./public/html/" . $innerData . ".html")) {
                return Response::viewObject($innerData, $replaceData);
            }
            return "<!--CodeSnippet nicht gefunden-->";
        }

        private static function loadAndExecutePHP($innerData, $replaceData) {     //Warnung never user data
            $res = eval($innerData);
            if($res === null){ return ""; }
            return $res;
        }
        
        public static function interpretForLoop($innerData, $replaceData)
        {
            $content = "";
            $parts = explode(":", $innerData, 2);
            if (array_key_exists($parts[0], $replaceData->loopData)) {
                $array = $replaceData->loopData[$parts[0]];
                $iterationNr = 1;
                foreach ($array as $data) {
                    if(is_array($data) && array_key_exists("inner", $data)){
                        $replaceData->loopData[$parts[0]."Inner"] = $data["inner"];
                    }    
                    $iteration = Response::processTags($parts[1], "noName", $replaceData);
                    $iteration = str_replace("{{ ".$parts[0]."IterationNr }}", $iterationNr, $iteration);
                    if(is_string($data)){
                        $iteration = str_replace("{{ " . $parts[0] . " }}", $data, $iteration);
                    } else if(is_array($data)){
                        foreach ($data as $key => $value) {  
                            if(is_string($key) && is_string($value)){                                  
                                $iteration = str_replace("{{ " . $key . " }}", $value, $iteration);  
                            }      
                        }  
                    }
                    $content = $content . $iteration;
                    $iterationNr++;
                }
            }
            return $content;
        }


        public static function nav($navContentLink, $absolutOrigin, $replaceData){
            if($navContentLink == "{{ navContentLink }}"){
                if($absolutOrigin == "{{ absolutOrigin }}"){
                    return "<!-- Navigationsleiste konnte leider nicht geladen werden. navContentLink nicht gefunden. -->";
                } 
                $i = 0;        //definitly needs improving
                $j = -1;
                while($j !== false){
                    $i = $j;
                    $j = strpos($absolutOrigin, "/", $j+1);
                }
                $navContentLink = substr($absolutOrigin, 0, $i)."/nav";
            }
            if(is_file("./public/html/".$navContentLink.".html")){
                $content = Response::viewObject($navContentLink, $replaceData);
                if(substr($content, 0, 6) == "Link: "){
                    $navContentLink = substr($content, 6, strpos($content, ";", 6)-6);
                }
            }
            if(!is_file("./public/html/".$navContentLink.".nav")){
                return "<!-- Navigationsleiste konnte leider nicht geladen werden. Datei($navContentLink) nicht gefunden. -->";
            }
            $navContent = file_get_contents("./public/html/".$navContentLink.".nav");
            $lines = explode("\n", $navContent);

            $list = "<ul id=navList>";
            $dropdown = array();
            $script = "window.onload=function(){ window.onclick = function(event){";
            $style = "";

            $width=0;
            if(count($lines)>0 && substr($lines[0], 0, 7) == "width: "){
                $width=intval(substr($lines[0], 7, -1));
                $style = "<style> 
                .navElement, .navDropdown{
                    ". array_shift($lines) .";
                }";
            }
    
            $side = "left";
            $elementNr = -1;
            $umbruch = -1;
            foreach($lines as $line){
                if(substr($line, 0, 1) == "/"){            //Wechsel zur rechten Seite
                    $side = "right";
                    $umbruch = $elementNr;
                } else {
                    $startParam = 0;        //definitly needs improving
                    $tmp = -1;
                    while($tmp !== false){
                        $startParam = $tmp;
                        $tmp = strpos($line, "(", $tmp+1);
                    }
                    $content = substr($line, 0, $startParam);
                    $param = substr($line, $startParam+1, strpos($line, ")", $startParam+1) - 1-$startParam);
                    if(substr($line, 0, 1) == "-"){      //Unterpunkte eines Dropdown
                        if(!array_key_exists($elementNr, $dropdown)){
                            $dropdown[$elementNr] = array();
                        }
                        $line = substr($line, 1);
                        $href="";
                        if($param != ""){
                            $param = explode(", ", $param);
                            if($param[0] != ""){
                            $href="href=\"" . $param[0] . "\"";
                            }
                            if(count($param)>1 && $param[1] != ""){
                                $style = $style."#navDropdownElement".count($dropdown[$elementNr])."{width:" . $param[1] . ";}";
                            }
                        }
                        $content = "<a class=navDropdownElementLink $href>$content</a>";
                        array_push($dropdown[$elementNr], "<li class=\"$side navDropdownElement\" id=navDropdownElement".count($dropdown[$elementNr]).">$content</li>");
                    } else {                                    //NavElemente
                        $elementNr++;
                        $href="";
                        if($param != ""){
                            $param = explode(", ", $param);
                            if($param[0] != ""){
                            $href="href=\"" . $param[0] . "\"";
                            }
                            if(count($param)>1 && $param[1] != ""){
                                $style = $style."#navElement$elementNr{width:" . $param[1] . ";}";
                            }
                        }
                        $content = "<a class=navElementLink $href>$content</a>";
                        $list = $list . "<li class=\"$side navElement\" id=navElement$elementNr>$content</li>";
                    }
                }
            }

            $dropdownHtml = "";
            foreach($dropdown as $key => $value){
                $x = 0;
                $side = "";
                if($key<$umbruch || $umbruch==-1){
                    $side="left";
                    $x=($key)*$width;
                } else {
                    $side="right";
                    $x=($elementNr-$umbruch-1)*$width;
                }
                $style = $style . "#navDropdown$key{margin-$side:$x;}";
                $dropdownHtml = $dropdownHtml . "<div class=\"navDropdown $side\" id=navDropdown$key><ul class=navDropdownList id=navDropdownList$key>";
                foreach($value as $line){
                    $dropdownHtml = $dropdownHtml . $line;
                }
                $dropdownHtml = $dropdownHtml . "</ul></div>";
                $script = $script . 
                 "if(document.getElementById(\"navElement$key\").contains(event.target) && document.getElementById(\"navDropdown$key\").style.display != \"block\"){
                    document.getElementById(\"navDropdown$key\").style.display = \"block\";
                  } else if (document.getElementById(\"navDropdown$key\").style.display == \"block\" && !document.getElementById(\"navDropdown$key\").contains(event.target)) {
                    document.getElementById(\"navDropdown$key\").style.display = \"none\";
                  }";
            }
            $list = $list . "</ul>";
            $script = $script."}}";
            $style = $style . "</style>";
            return "<script>" . $script . "</script>" . $style . $list . $dropdownHtml;
        }
    }

    class ReplaceData {
        public array $data;
        public array $safeData;
        public array $loopData;

        public function __construct($data, $safeData, $loopData)
        {
            $this->data = $data;
            $this->safeData = $safeData;
            $this->loopData = $loopData;
        }
    }
?>
