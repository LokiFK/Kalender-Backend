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
            $this->workhours = array();
            $this->additionals = array();
            $this->blocks = array();

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

        public function hasTime($date, $start, $end) {   //Date string("YYYY/MM/DD") TIME("HH:MM:SS")
            $weekday = date("l",strtotime($date));
            foreach(Mitarbeiter::WEEKDAYS as $key=>$aWeekday){
                if($weekday == $aWeekday){
                    $wochentag = Mitarbeiter::WOCHENTAGE[$key];
                }
            }
            if($this->workhours!=null){
            foreach($this->workhours as $day=>$workhours){
                if($day==$wochentag){
                    foreach($workhours as $workhour){
                        if($start>$workhour['start'] && $end<$workhour['end']){
                            return $this->isNotBlocked($date, $start, $end);
                        }
                    }
                }
            }
            }
            if($this->additionals!=null){
            foreach($this->additionals as $aDate=>$additionals){
                if($date==$aDate){
                    foreach($additionals as $additional){
                        if($start>$additional['start'] && $end<$additional['end']){
                            return $this->isNotBlocked($date, $start, $end);
                        }
                    }
                }
            }
        }
            return false;
        }
        public function isNotBlocked($date, $start, $end){
            if($this->blocks!=null){
            foreach($this->blocks as $aDate=>$blocks){
                if($date==$aDate){
                    foreach($blocks as $block){
                        if($end>$block['start'] && $start<$block['end']){
                            return false;
                        }
                    }
                }
            }
            }
            $appointments = DB::query("SELECT count(*) as Anzahl FROM appointment a,appointment_admin b WHERE a.id=b.appointmentID and b.adminID=:userID and day=:day and start<:end and end>:start", [":userID"=>$this->userID, ":day"=>$date, ":start"=>$start, ":end"=>$end]);
            if($appointments[0]['Anzahl']>0){
                return false;
            }
            return true;
        }

        public static function isRoomFree($number, $date, $start, $end){
            $appointments = DB::query("SELECT count(*) as Anzahl FROM appointment a,room b WHERE a.roomID=b.id and b.number=:number and day=:day and start<:end and end>:start", [":number"=>$number, ":day"=>$date, ":start"=>$start, ":end"=>$end]);
            if($appointments[0]['Anzahl']>0){
                return false;
            }
            return true;
        }
    }

?>