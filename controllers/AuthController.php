<?php


    class AuthController {

        public function login()
        {
            $test = Tests::where(
                array(
                    "control" => "1 AND 2",
                    1 => new Condition("user_id", "=", "1"),
                    2 => new Condition("token_id", "=", "2"),
                ), 
                Tests::INCLUDE_FOREIGN_DATA, 
                ""
            );

            $f = Users::fetch(2);

            print_r($f);
        }

        public function logout()
        {
            print_r(Auth::userID());
        }
    }

?>