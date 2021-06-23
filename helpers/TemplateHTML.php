<?php

    class TemplateHTML {

        public static function nav(string $navPath, $absolutOrigin, ReplaceData $replaceData): string
        {
            if ($navPath == "{{ navContentLink }}") {
                if ($absolutOrigin == "{{ absolutOrigin }}") {
                    return "<!-- Navigationsleiste konnte leider nicht geladen werden. navContentLink nicht gefunden. -->";
                }
                $i = 0;        //definitly needs improving
                $j = -1;
                while ($j !== false) {
                    $i = $j;
                    $j = strpos($absolutOrigin, "/", $j+1);
                }
                $navPath = substr($absolutOrigin, 0, $i)."/nav";
            }

            if (is_file("./public/html/" . $navPath . ".html")) {
                $content = TemplateHTML::load($navPath, $replaceData);
                if (substr($content, 0, 6) == "Link: ") {
                    $navPath = substr($content, 6, strpos($content, ";", 6)-6);
                }
            }

            if (is_file("./public/html/" . $navPath . ".nav")) {
                $navTemplate = TemplateHTML::load("@nav:".$navPath, $replaceData);
                //echo $navTemplate;
                //return $navTemplate;
                return TemplateHTML::interpreteNavTemplate($navTemplate);
            }
            return "<!-- Navbar not found -->"; 
        }

        public static function interpreteNavTemplate(string $navTemplate): string
        {
            $startTag = 0;
            $endTag = 0;
            $i = 0;

            $html = "<div class=\"nav\"><ul class=\"nav-list\"><div class=\"left\">";

            while (($i + 1) < strlen($navTemplate)) {
                // search for the start / end pair of a tag
                $startTag = strpos($navTemplate, '@', $endTag);
                $endTag = strpos($navTemplate, ']', $startTag);
                $i = $endTag + 1;

                // in the found tag, filter out the name and the content
                $tagNameEnd = strpos($navTemplate, '[', $startTag);
                $tagName = substr($navTemplate, $startTag, $tagNameEnd - $startTag);
                
                $tagContent = substr($navTemplate, $tagNameEnd+1, $endTag - $tagNameEnd - 1);

                // create html for the tag
                if ($tagName == "@link") {
                    
                    $split = explode(', status', $tagContent);
                    $status = $split[1];
                    $tagContent = $split[0];

                    if (TemplateHTML::interpretStatus($status)) {
                        $interpretedLink = TemplateHTML::interpreteNavLink($tagContent);
                        $html .= "
                            <li class=\"nav-list-item link\">
                                <form action=\"" . $interpretedLink['path'] . "\" method=\"GET\">
                                    <input type=\"submit\" value=\"" . $interpretedLink['name'] . "\">
                                </form>
                            </li>
                        ";
                    }
                } else if ($tagName == "@dropdown") {
                    $html = TemplateHTML::interpreteNavDropdown($tagContent, $html);
                } else if ($tagName == "@flipside") {
                    $html .= "</div><div class=\"right\">";
                }
            }
            $html .= "</div></ul></div>";

            return $html;
        }

        public static function interpretStatus($status): bool
        {
            $status = preg_replace('/\s/', '', $status);
            $status = str_replace('status', '', $status);
            
            $statusNr = substr($status, strlen($status)-1, 1);
            $comparator = substr($status, 0, strlen($status)-1);
            
            if ($comparator == "==") {
                return Auth::getStatus() == $statusNr;
            } else if ($comparator == "<") {
                return Auth::getStatus() < $statusNr;
            } else if ($comparator == "<=") {
                return Auth::getStatus() <= $statusNr;
            } else if ($comparator == ">") {
                return Auth::getStatus() > $statusNr;
            } else if ($comparator == ">=") {
                return Auth::getStatus() >= $statusNr;
            } else if ($comparator == "<>") {
                return Auth::getStatus() != $statusNr;
            } else {
                return false;
            }
        }

        public static function interpreteNavDropdown(string $tagContent, string $html): string
        {
            $dict = array();
            if (strpos($tagContent, ';') !== false) {
                $dict = explode(';', $tagContent);
                $dropdownData = array();

                foreach ($dict as $dataPart) {
                    $key = preg_replace('/\s/', '', explode('=', $dataPart)[0]);
                    
                    if (strpos($dataPart, 'status')) {
                        if (!self::interpretStatus($dataPart)) {
                            return $html;
                        }
                    } else if (strpos($dataPart, '{{')) {
                        $value = str_replace(['"', '()'], '', explode('=', $dataPart)[1]);
                    } else {
                        $value = preg_replace('/\s/', '', explode('=', $dataPart)[1]);
                    }

                    $dropdownData[$key] = $value;
                }
                
                $dropdownData['items'] = explode(',', $dropdownData['items']);
                
                $html .= "
                    <li class=\"nav-list-item dropdown\">
                        <p>$dropdownData[title]</p>
                        <div class=\"dropdown-content-wrapper\">
                            <div class=\"dropdown-content\">
                ";

                foreach ($dropdownData['items'] as $link) {
                    $interpretedLink = TemplateHTML::interpreteNavLink($link);

                    $html .= "
                        <form action=\"" . $interpretedLink['path'] . "\" method=\"GET\">
                            <input type=\"submit\" value=\"" . $interpretedLink['name'] . "\">
                        </form>
                    ";
                }

                $html .= "
                            </div>
                        </div>
                    </li>
                ";
            }

            return $html;
        }

        public static function interpreteNavLink(string $tagContent): array
        {
            // format string, find start / end index of the path
            $tagContent = str_replace('"', '', $tagContent);
            $tagContentStartPath = strpos($tagContent, "(", 0);
            $tagContentEndPath = strpos($tagContent, ")", $tagContentStartPath);

            // get path and name as substring of $tagContent
            $tagContentPath = substr($tagContent, $tagContentStartPath +1, $tagContentEndPath - $tagContentStartPath - 1);
            $tagContentName = substr($tagContent, 0, $tagContentStartPath);

            return [
                'path' => $tagContentPath,
                'name' => $tagContentName
            ];
        }

        public static function load(string $componentName, ReplaceData $replaceData): string
        {
            $path = "";
            if (substr($componentName, 0, 5) == "@nav:") {
                $path = "./public/html/". substr($componentName, 5) . ".nav";
            } else {
                $path = "./public/html/" . $componentName . ".html";
            }

            if (is_file($path)) {
                $component = file_get_contents($path);
                $content = TemplateHTML::processTags($component, $componentName, $replaceData);

                if ($content !== null) { 
                    /*if(substr($componentName, 0, 5) == "@nav:"){
                        TemplateHTML::interpreteNavTemplate($content);
                    }*/
                    return $content; 
                }
            }

            ErrorUI::error(500, 'Error setting up HTML');
            exit;
        }
        public static function fileExists($componentName){
            $path = "";
            if(substr($componentName, 0, 5) == "@nav:"){
                $path = "./public/html/". substr($componentName, 5) . ".nav";
            } else {
                $path = "./public/html/" . $componentName . ".html";
            }
            if (is_file($path)) {
                return true;
            }
            return false;    
        }

        private static function replace($key, $value, $component){
            if(is_array($value)){
                foreach($value as $key2 => $value2){
                    $component = TemplateHTML::replace($key.".".$key2, $value2, $component);
                }
                return $component;
            } else {
                if($value==null){
                    return str_replace("{{ " . $key . " }}", "", $component);
                } else {
                    return str_replace("{{ " . $key . " }}", $value, $component);
                }
            }
        }

        private static function processTags(string $component, string $componentName, ReplaceData $replaceData)
        {
            // safeData must be inserted first in order to not run into unwanted user input injections
            foreach ($replaceData->safeData as $key => $value) {
                $component = TemplateHTML::replace($key,$value, $component);
            } 

            $replaceData->safeData['origin'] = $componentName;

            $tagsActivator = ["{","}"];
            $tags = [["?php","?"], ["! "," !"], ["% "," %"], ["+ "," +"], ["# extend "," #"], ["[ ", " ]"]];
            $i = 0;
            $template = null;

            while ($i < strlen($component)) {
                // $j is the beginning index of the tag
                $j = strpos($component, $tagsActivator[0], $i);
                if ($j === false) { break; }

                $i = $j+1;

                foreach ($tags as $tag) {

                    // only proceed if the char on position $j + 1 is a $tag[0]
                    if (substr($component, $j + 1, strlen($tag[0])) == $tag[0]) {
                        $endStartTag = $j + strlen($tagsActivator[0]) + strlen($tag[0]);
                        $k = null;

                        if ($tag[0] == "! ") {
                            // prepare stuff for the for loop, you don't need to understand that Unfall...
                            $tiefe = 1;
                            $m = $endStartTag;
                            while ($tiefe > 0) {
                                // search for the start index of the next beginning tag after the loop started
                                $l = strpos($component, $tagsActivator[0].$tag[0], $m);
                                // search for the end index of the next beginning tag after the loop started
                                $k = strpos($component, $tag[1].$tagsActivator[1], $m);
                                // if there are no more tags, just break
                                if ($l === false && $k === false) { break 2; }
                                if ($l === false || $k < $l) {
                                    // there is no more beginning tag or some weird case where the end is before the beginning of the next tag
                                    $m = $k + 1;
                                    $tiefe--;
                                } else {
                                    // found one more tag, go one deeper
                                    $m = $l + 1;
                                    $tiefe++;
                                }
                            }
                        } else {
                            // $k is set to the index of the end of the current tag
                            $k = strpos($component, $tag[1] . $tagsActivator[1], $endStartTag);
                            if ($k === false) { break; }
                        }

                        $innerData = substr($component, $endStartTag, $k-$endStartTag);
                        $tagContentLength = $k - $j + strlen($tag[1] . $tagsActivator[1]);

                        // Common treatment in else, special cases are taken care of before
                        if ($tag[0] == "# extend ") {

                            $component = substr_replace($component, "", $j, $tagContentLength);
                            $template = TemplateHTML::loadTemplate($template, $innerData, $replaceData);

                            $i = $j+1;
                        } else {
                            $content = "";

                            if ($tag[0] == "% ") {
                                $content = TemplateHTML::loadResources($componentName, $innerData);
                            } else if ($tag[0] == "+ ") {
                                $content = TemplateHTML::loadCodeSnippets($innerData, $replaceData);
                            } else if ($tag[0] == "?php") {
                                $content = TemplateHTML::loadAndExecutePHP($innerData, $replaceData);
                            } else if ($tag[0] == "! ") { 
                                $content = TemplateHTML::interpretForLoop($innerData, $replaceData);
                            } else if ($tag[0] == "[ ") {
                                $datasets = explode(", ", $innerData);
                                foreach($datasets as $set) {
                                    $parts = explode("=>", $set, 2);
                                    $replaceData->safeData[$parts[0]] = $parts[1];
                                    $replaceData->data[$parts[0]] = $parts[1];
                                }
                            }

                            $component = substr_replace($component, $content, $j, $tagContentLength);
                            $i = $j+1 + strlen($content); 
                        }
                    }
                }
            }

            if ($template !== null) {
                $component = str_replace("{# here #}", $component, $template);
            }

            // At the very end, user related data can be inserted
            foreach ($replaceData->data as $key => $value) {
                $component = TemplateHTML::replace($key,$value, $component);
            } 

            return $component;
        }

        private static function loadTemplate($template, $innerData, $replaceData)
        {
            $containerContents = explode("@", $innerData);
            if (is_file("./public/html/".$containerContents[0].".html")) {
                $content = TemplateHTML::load($containerContents[0], $replaceData);
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
            //$innerData = str_replace(' ', '', $innerData);
            if (TemplateHTML::fileExists($innerData)) {
                return TemplateHTML::load($innerData, $replaceData);
            }
            return "<!--CodeSnippet nicht gefunden-->";
        }

        private static function loadAndExecutePHP(string $innerData, ReplaceData $replaceData) {     //Warnung never user data
            $res = eval($innerData);
            if ($res === null) { return ""; }
            return $res;
        }
        
        public static function interpretForLoop($innerData, $replaceData)
        {
            $content = "";
            $parts = explode(":", $innerData, 2);
            $sonderpfad = str_replace(".", "/", $parts[0])."/inner";
            $keys = explode(".", $parts[0]);
            $array = $replaceData->loopData;
            foreach ($keys as $key) {
                if (is_array($array) && array_key_exists($key, $array)) {
                    $array = $array[$key];
                } else {
                    return $content;
                }
            }
            $iterationNr = 1;
            foreach ($array as $key => $data) {
                if(is_array($data)){
                    $replaceData->loopData[$sonderpfad] = $data;
                }    
                $iteration = TemplateHTML::processTags($parts[1], "noName", $replaceData);
                $iteration = str_replace("{{ ".$parts[0].".iterationNr }}", $iterationNr, $iteration);
                $iteration = str_replace("{{ ".$parts[0].".key }}", $key, $iteration);
                if (is_string($data)) {
                    $iteration = str_replace("{{ " . $parts[0] . " }}", $data, $iteration);
                } else if (is_array($data)) {
                    foreach ($data as $dataKey => $value) {  
                        if (is_string($dataKey) && is_string($value)) {
                            $iteration = str_replace("{{ " . $dataKey . " }}", $value, $iteration);
                            // version where you can access dict like in generalcontroller@landingPage
                            $iteration = str_replace("{{ " . $parts[0] . "." . $dataKey . " }}", $value, $iteration);
                        }      
                    }
                }
                $content = $content . $iteration;
                $iterationNr++;
            }
            if(array_key_exists($sonderpfad, $replaceData->loopData)){
                unset($replaceData->loopData[$sonderpfad]);
            }
            return $content;
        }
    }

?>
