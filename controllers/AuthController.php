<?php

    class AuthController {

        public function createUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createUser');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday']);
                Auth::registerUser(
                    new User(
                        $validatedData['firstname'],
                        $validatedData['lastname'],
                        $validatedData['salutation'],
                        $validatedData['birthday'],
                        $validatedData['insurance'],
                        null
                    )
                );
                Path::redirect('../../../');
            }
        }

        public function createAccount(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createAccount');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['username', 'email', 'password', 'createdAt']);
                $userID = DB::table('users')->where('username = :username', [':username' => $validatedData['username']])->get([], ['id']);
                
                if (count($userID) > 0) {
                    Auth::registerAccount(
                        new Account(
                            $userID[0]['id'],
                            $validatedData['username'],
                            $validatedData['email'],
                            $validatedData['password'],
                            false
                        )
                    );
                }
                
                Path::redirect('../../../');
            }
        }

        public function login(Request $req, Response $res)
        {
            $validatedData = Form::validate($req->getBody(), ['username', 'password']);
            
            $res->json(
                array(
                    'token' => Auth::login($validatedData['username'], $validatedData['password'], true)
                )
            );
        }

        public function permissions(Request $req, Response $res)
        {
            Middleware::auth();

            $userID = Auth::userID();
            
            $adminRelation = DB::table('admin')->where('userID = :userID', [':userID' => $userID])->get();
            return count($adminRelation) > 0
                ? $res->json(array('role' => 'admin'))
                : $res->json(array('role' => 'account'));
        }
    }

?>
