<?php

    class Path {
        public static function getPath()
        {
            $url = $_GET['url'];
            if ($url[strlen($url)-1] == '/') {
                $url = substr($url, 0, -1);
            }
            return $url;
        }
    }