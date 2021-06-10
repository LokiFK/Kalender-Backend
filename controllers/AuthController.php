<?php

    class AuthController {

        public function createUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createUser');
            } else if ($req->getMethod() == "POST") {
                $validatedData = FormValidate::validate($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday']);
                DB::query("INSERT INTO `users` (`firstname`, `lastname`, `salutation`, `insurance`, `birthday`, `patientID`) VALUES ($validatedData[firstname], $validatedData[lastname], $validatedData[salutation], $validatedData[insurance], $validatedData[birthday], null);");
                header('Location: ./');
            }
        }
    }

?>
