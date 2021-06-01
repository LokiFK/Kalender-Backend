<?php


    class Path {
        public static function getPath()
        {
            $url = $_SERVER['REQUEST_URI'];

            if (isset($url)) {
                if ($url[strlen($url)-1] == '/') {
                    $url = substr($url, 0, -1);
                }
                if (strpos($url, '?') !== false) {
                    $url = substr($url, 0, strpos($url, "?"));
                }
                return $url;
            } else {
                ErrorUI::errorCode(404);
                ErrorUI::error('Page not found');
            }
        }
    }

?>
