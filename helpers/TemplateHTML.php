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
                $content = Response::viewObject($navPath, $replaceData);
                if (substr($content, 0, 6) == "Link: ") {
                    $navPath = substr($content, 6, strpos($content, ";", 6)-6);
                }
            }

            $navTemplate = file_get_contents("./public/html/" . $navPath . ".nav");

            return TemplateHTML::interpreteNavTemplate($navTemplate);
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
                    $interpretedLink = TemplateHTML::interpreteLink($tagContent);
                    $html .= "
                        <li class=\"nav-list-item link\">
                            <form action=\"" . $interpretedLink['path'] . "\" method=\"GET\">
                                <input type=\"submit\" value=\"" . $interpretedLink['name'] . "\">
                            </form>
                        </li>
                    ";
                } else if ($tagName == "@dropdown") {
                    $dict = array();
                    if (strpos($tagContent, ';') !== false) {
                        $dict = explode(';', $tagContent);
                        $dropdownData = array();

                        foreach ($dict as $dataPart) {
                            $key = preg_replace('/\s/', '', explode('=', $dataPart)[0]);
                            if (strpos(explode('=', $dataPart)[1], '{{')) {
                                $value = str_replace(['"', '()'], '', explode('=', $dataPart)[1]);
                            } else {
                                $value = preg_replace('/\s/', '', explode('=', $dataPart)[1]);
                            }
                            $dropdownData[$key] = $value;
                        }
                        
                        $dropdownData['items'] = explode(',', $dropdownData['items']);
                        
                        $html .= "
                            <li class=\"nav-list-item dropdown\">
                                <span>$dropdownData[title]</span>
                                <div class=\"dropdown-content\">
                        ";

                        foreach ($dropdownData['items'] as $link) {
                            $interpretedLink = TemplateHTML::interpreteLink($link);

                            $html .= "
                                <form action=\"" . $interpretedLink['path'] . "\" method=\"GET\">
                                    <input type=\"submit\" value=\"" . $interpretedLink['name'] . "\">
                                </form>
                            ";
                        }

                        $html .= "
                                </div>
                            </li>
                        ";
                    }
                } else if ($tagName == "@flipside") {
                    $html .= "</div><div class=\"right\">";
                }
            }
            $html .= "</div></ul></div>";

            return $html;
        }

        public static function interpreteLink(string $tagContent): array
        {
            $tagContent = str_replace('"', '', $tagContent);
            $tagContentStartPath = strpos($tagContent, "(", 0);
            $tagContentEndPath = strpos($tagContent, ")", $tagContentStartPath);

            $tagContentPath = substr($tagContent, $tagContentStartPath +1, $tagContentEndPath - $tagContentStartPath - 1);
            $tagContentName = substr($tagContent, 0, $tagContentStartPath);

            return [
                'path' => $tagContentPath,
                'name' => $tagContentName
            ];
        }

        public static function loadNavFrom(string $navPath): string
        {
            if (is_file("./public/html/" . $navPath . ".nav")) {
                return file_get_contents("./public/html/" . $navPath . ".nav");
            }
            return "<!-- Navbar not found -->";
        }
    }

?>