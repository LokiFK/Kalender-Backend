<?php

    class Middleware {

        public static function auth()
        {
            if (!Auth::isLoggedIn()) {
                ErrorUI::error(401, 'You have to be logged in to see this page.');
                exit;
            }
        }
    }

?>