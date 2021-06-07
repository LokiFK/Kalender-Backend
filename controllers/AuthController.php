<?php

    class AuthController {

        public function register(Request $req, Response $res)
        {
            echo $res->view('auth/register');
        }

        public function registerAccount(Request $req, Response $res)
        {
            echo $res->view('auth/registerAccount', ['code' => $req->getBody()['code']]);
        }

        public function createUser(Request $req, Response $res)
        {
            $requestBody = $req->getBody();
            Auth::registerUser(new User($requestBody['firstName'], $requestBody['lastName'], $requestBody['salutation'], $requestBody['birthday'], "f", 1));
        }

        public function createAccount(Request $req, Response $res)
        {
            $requestBody = $req->getBody();
            $code = DB::table('notapproved')->where('code = :code', [':code' => $requestBody['code']])->get()[0];
            Auth::registerAccount(new Account($code['userID'], $requestBody['username'], $requestBody['email'], $requestBody['password'], false));
        }

        public function test(Request $req, Response $res)
        {
            $res->json(['hHlasdjfoiewofwh']);
        }
    }

?>
