<?php

    class Middleware {

        public static function auth()
        {
            if (!Auth::isLoggedIn()) {
                ErrorUI::errorCode(401);
                ErrorUI::error('You have to be logged in to see this page.');
                exit;
            }
        }
    }

?>