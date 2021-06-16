<?php

    class AuthController {
        public function test(Request $req, Response $res)
        {
            echo $res->view('auth/login');
        }

        public function createUser(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/createUser');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['firstname', 'lastname', 'salutation', 'insurance', 'birthday']);
                $code = Auth::registerUser(
                    new User(
                        $validatedData['firstname'],
                        $validatedData['lastname'],
                        $validatedData['salutation'],
                        $validatedData['birthday'],
                        $validatedData['insurance'],
                        ""
                    )
                );
                Path::redirect('../../auth/account/create?code=' . $code);
            }
        }

        public function createAccount(Request $req, Response $res)
        {
            if ($req->getMethod() == "GET") {
                $validatedData = Form::validate($req->getBody(), ['code']);
                echo $res->view('auth/createAccount', ['code' => $validatedData['code']]);
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['username', 'email', 'password', 'code']);
                $userID = DB::table('notapproved')->where('`code` = :code', [':code' => $validatedData['code']])->get();

                if (count($userID) > 0) {
                    Auth::registerAccount(
                        new Account(
                            $userID[0]['userID'],
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
            if ($req->getMethod() == "GET") {
                echo $res->view('auth/login');
            } else if ($req->getMethod() == "POST") {
                $validatedData = Form::validate($req->getBody(), ['username', 'password']);
                $remember = false;
                if(isset($req->getBody()['remember']) && $req->getBody()['remember'] == 'on'){
                    $remember = true;
                }
                $token = Auth::login($validatedData['username'], $validatedData['password'], $remember);
                //echo $token;
                if($token!=null){
                    setcookie('token', $token, time() + 60 * 60 * 24 * 30, '/');

                    Path::redirect('../../../');
                } else {
                    echo "wrong username or password";      //todo
                    echo $res->view('auth/login');
                }
            }
        }
        public function logout(Request $req, Response $res){
            if(Auth::logout()!=true){

            }
            Path::redirect('../../../');
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
