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
            $treatments = DB::query("SELECT * from treatment;");
            // $treatments = [ ["name"=>"b1"], ["name"=>"b2"], ["name"=>"b3"] ];
            $view = $res->view('user/new', array(), array(), [ "treatments"=>$treatments ]);
            echo $view;
        }
        public function new2(Request $req, Response $res){
            $data = Form::validate($req->getBody(), ['treatment']);
            $treatment = $data['treatment'];
            if(DB::query("SELECT count(*) as Anzahl from treatment where name=:treatment;", [ ":treatment"=>$treatment ])[0]['Anzahl']!=1){
                ErrorUI::error(400, 'Treatment not found.');
            }
            //$termine = 
            //$termine = [ [ "datum"=>"14.06.21", "inner"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]] ], [ "datum"=>"15.06.21", "inner"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]] ] ];
            $termine = [ "14.06.2021"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]], "15.06.2021"=>[["start"=>"13:00", "end"=>"13:15" ], ["start"=>"14:00", "end"=>"14:15" ]]];
            $view = $res->view('user/new2', [ "treatment"=>$treatment ], array(), [ "termine"=>$termine ]);
            echo $view;
        }

        public function overview(Request $req, Response $res)
        {
            Middleware::statusBiggerOrEqualTo(2);

            if($req->getMethod() == "GET"){
                $data = DB::query("SELECT * from appointment, treatment WHERE appointment.treatmentID=treatment.id and appointment.userID=:userID", [":userID"=>Auth::getUser()['id']]);
                $view = $res->view('user/overview', array(), array(), [ "appointments"=>$data ]);
                echo $view;
                /*$view = $res->view('user/overview',
                    [],
                    [],
                    [
                        'appointments' => [
                            [
                                'name' => 'Sprechstunde',
                                'start' => '16.06.2021 17:00',
                                'end' => '16.06.2021 18:00',
                                'status' => 'Approved',
                                'statusColor' => 'green'
                            ],
                            [
                                'type' => 'Belastungs-EKG',
                                'start' => '17.06.2021 17:00',
                                'end' => '17.06.2021 18:00',
                                'status' => 'Waiting',
                                'statusColor' => 'orange'
                            ],
                            [
                                'type' => 'HNO-Allgemeinuntersuchung',
                                'start' => '17.06.2021 20:00',
                                'end' => '17.06.2021 22:00',
                                'status' => 'Not Approved',
                                'statusColor' => 'red'
                            ]
                        ],
                    ]
                );
                echo $view;*/
            } else {

            }
        }

        public function profile(Request $req, Response $res) {
            $view = $res->view('user/profile');
            echo $view;
        }

        public function get(Request $req, Response $res) {
            $userId = Auth::getUserID();
            $user = DB::table('users')->where("id = :id",[':id'=>$userId])->get();
            if(count($user)>0) {
                $user = $user[0];
            } else {
                $res->errorVisual(507, "Error");
            }
            $view = $res->view('user/profile', ['firstname' => $user['firstname'], 'lastname' => $user['lastname'], 'salutation' => $user['salutation'], 'insurance' => $user['insurance'], 'birthday' => $user['birthday']]);
            echo $view;
        }

    }

?>
