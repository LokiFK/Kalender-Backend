<?php


    class Path {
        const ROOT = "../../../../";

        public static function getPath()
        {
            $url = $_SERVER['REQUEST_URI'];

            if (isset($url)) {
                if ($url[strlen($url)-1] == '/') {
                    $url = substr($url, 0, -1);
                }
                if (strpos($url, '?')) {
                    $url = substr($url, 0, strpos($url, "?"));
                }
                return $url;
            } else {
                ErrorUI::error(404, 'Page not found');
            }
        }

        public static function redirect(string $path)
        {
            header('Location: ' . $path);
        }
    }

?>
