<?php

    class Middleware {

        public static function auth()
        {
            if (!Auth::isLoggedIn()) {
                ErrorUI::error(401, 'You have to be logged in to see this page.');
                exit;
            }
        }

        public static function status(int $status)
        {
            if (Auth::getStatus() < $status) {
                ErrorUI::error(401, 'You dont\'t have the permission to view this page.');
                exit;
            }
        }
    }

?>