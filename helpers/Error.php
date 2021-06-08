<?php

    class ErrorUI {
        public static function errorMsg($msg)
        {
            echo json_encode(
                array(
                    'message' => $msg
                )
            );
        }

        public static function error($errCode, $msg)
        {
            http_response_code($errCode);
            if(strpos($msg, "/api")) {
                ErrorUI::errorMsg($msg);
            } else {
                echo Response::view("general/error", ["errorCode" => $errCode]);
            }
        }
    }