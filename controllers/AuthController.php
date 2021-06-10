<?php

    class AuthController {

        public function registerUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/registerUser');
            } else if ($req->getMethod() == "POST") {
                DB::query('INSERT INTO `users` (`firstname`, `lastname`, `salutation`, `insurance`, `birthday`, `patientID`) VALUES ( ')
            }
        }
    }

?>
