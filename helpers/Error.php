<?php

    class ErrorUI {
        public static function error($msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }

        public static function errorView($errCode, $msg)
        {
            http_response_code($errCode);
            if(strpos($msg, "/api")) {
                ErrorUI::error($msg);
            } else {
                echo Response::view("general/error", ["errorCode" => $errCode]);
            }
        }
    }