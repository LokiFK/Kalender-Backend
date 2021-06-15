<?php

    class GeneralController{

        public function startPage(Request $req, Response $res){
            Path::redirect('/user/appointments/new');
        }
        public function landingPage(Request $req, Response $res){
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

    }

?>