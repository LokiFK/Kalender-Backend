<?php

    class Path {
        public static function getPath()
        {
            if (isset($_GET['url'])) {
                $url = $_GET['url'];
                if ($url[strlen($url)-1] == '/') {
                    $url = substr($url, 0, -1);
                }
                return $url;
            } else {
                UI::error(404, 'Page not found');
            }
        }
    }

?>
