<?php


    class AuthController {

        public function login()
        {
            //$test = Tokens::where(['id', '=', 1], Tokens::INCLUDE_FOREIGN_DATA)->get();
            $user = Auth::user();
            print_r($user);
            //print_r($test);

            //Auth::login(4);
        }

        public function logout()
        {
        }
    }

?>
