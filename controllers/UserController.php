<?php

    class UserController {

        public function landingPage(Request $req, Response $res) {
            $allAppointmentTypes = ['Sprechstunde', 'Belastungs-EKG', 'HNO-Allgemeinuntersuchung','HNO-Allgemeinuntersuchung', 'Belastungs-EKG', 'Sprechstunde','Sprechstunde','HNO-Allgemeinuntersuchung', 'Belastungs-EKG', 'Sprechstunde', 'Belastungs-EKG', 'HNO-Allgemeinuntersuchung'];
            
            echo $res->view('user/landingPage',
                [],
                [],
                [
                    'appointments' => [
                        [
                            'type' => 'Sprechstunde',
                            'start' => '16.06.2021 17:00',
                            'end' => '16.06.2021 18:00',
                        ],
                        [
                            'type' => 'Belastungs-EKG',
                            'start' => '17.06.2021 17:00',
                            'end' => '17.06.2021 18:00',
                        ],
                        [
                            'type' => 'HNO-Allgemeinuntersuchung',
                            'start' => '17.06.2021 20:00',
                            'end' => '17.06.2021 22:00',
                        ]
                    ],
                    'appointmentTypes' => $allAppointmentTypes
                ]
            );
        }

        public function new(Request $req, Response $res){
            $treatments = DB::table("treatment")->get();
            //$allAppointmentTypes = ['b1', 'b2'];
            // $treatments = [ ["name"=>"b1"], ["name"=>"b2"], ["name"=>"b3"] ];
            $view = $res->view('user/new', array(), array(), [
                'appointmentTypes' => $treatments
            ]);
            echo $view;
        }

        public function new2(Request $req, Response $res){
            $data = Form::validateDataType($req->getBody(), ['treatment'=>"existingTreatment"]);
            $treatment = $data['treatment'];
            if (DB::query("SELECT count(*) as Anzahl from treatment where name=:treatment;", [ ":treatment"=>$treatment ])[0]['Anzahl'] != 1) {
                ErrorUI::error(400, 'Treatment not found.');
            }
            
            $allAppointments = DB::query("SELECT a.id, a.start, a.end, b.name FROM appointment a, treatment b WHERE a.`userID` IS NULL AND a.`end` > :end AND a.treatmentID = b.id AND b.name = :name", [':end' => date(DB::DATE_FORMAT), ':name' => $treatment]);

            $view = $res->view('user/new2', [ "treatment"=>$treatment ], array(), [ "appointments"=>$allAppointments ]);
            echo $view;
        }

        public function new3(Request $req, Response $res)
        {
            Middleware::statusBiggerOrEqualTo(2);                           //todo hier muss eine Anmelde möglichkeit geboten werden, die möglichst wieder flüssig zurückführt
            
            $data = Form::validate($req->getBody(), ['id']);
            
            DB::query("UPDATE `appointment` SET `userID` = :userID WHERE `id` = :id", [':id' => $data['id'], ":userID"=>Auth::getUserID()]);

            Path::redirect(Path::ROOT . "user/appointments/overview");
        }

        public function overview(Request $req, Response $res)
        {
            Middleware::statusBiggerOrEqualTo(2);                    

            if($req->getMethod() == "GET"){
                $data = DB::query("SELECT * from appointment, treatment WHERE appointment.treatmentID=treatment.id and appointment.userID=:userID", [":userID"=>Auth::getUser()['id']]);
                $view = $res->view('user/overview', array(), array(), [ "appointments"=>$data ]);
                echo $view;
            } else {

            }
        }

        public function profile(Request $req, Response $res) {
            $userId = Auth::getUserID();
            $user = DB::table('users')->where("id = :id",[':id'=>$userId])->get();
            $account = DB::table('account')->where("userID = :id", [':id'=>$userId])->get();
            if(count($user)>0 && count($account)>0) {
                $user = $user[0];
                $account = $account[0];
            } else {
                $res->errorVisual(500, "Nutzer nicht gefunden");
            }
            $birthday = date('Y-m-d', strtotime($user['birthday']));
            $view = $res->view('user/profile', ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'salutation' => $user['salutation'], 'insurance' => $user['insurance'], 'birthday' => $birthday, 'email' => $account['email']]);
            echo $view;
        }

    }

?>
