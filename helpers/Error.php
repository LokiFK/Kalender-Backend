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
                echo Response::view("general/error", ["errorCode" => $errCode, "msg" => $msg]);
            }
        }

        public static function generalError() {
            self::error(random_int(-1000,0), "Unknown Error");
        }
    }