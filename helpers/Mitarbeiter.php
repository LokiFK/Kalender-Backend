<?php

    class Mitarbeiter{

        public $userID;
        public $workhours;
        public $additionals;
        public $blocks;

        const WOCHENTAGE = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
        const WEEKDAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        public function __construct($userID)
        {
            $this->userID = $userID;

            $workhours = DB::query("SELECT * FROM workhours WHERE patientID=:userID ORDER BY day, start", [":userID"=>$userID]);
            $blocks = DB::query("SELECT * FROM workhoursblock WHERE patientID=:userID ORDER BY isBlock, day, start", [":userID"=>$userID]);

            foreach(Mitarbeiter::WOCHENTAGE as $wochentag){
                $this->workhours[$wochentag] = array();
            }
            foreach($workhours as $workhour){
                array_push($this->workhours[$workhour['day']], $workhour);
            }
            foreach($blocks as $block){
                if($block['isBlock']){
                    if(!isset($this->blocks[$block['day']]) || $this->blocks[$block['day']] == null){
                        $this->blocks[$block['day']] = array();
                    }
                    array_push($this->blocks[$block['day']], $block);
                } else {
                    if(!isset($this->additionals[$block['day']]) || $this->additionals[$block['day']] == null){
                        $this->additionals[$block['day']] = array();
                    }
                    array_push($this->additionals[$block['day']], $block);
                }
            }
        }

    }

?>