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
            try {
                self::error(random_int(-700, -600), "Unknown Error");
            } catch (Exception $e) {
                self::error(601, $e);
            }
        }

        public static function errorFiveHundred($errCode)
        {
            ErrorUI::error(500, 'Error: ' . $errCode->getMessage());
        }
    }