<?php

    class RoutesController {
        public function a(Request $r)
        {
            print_r(DB::table('tokens')->where('token = :token', [':token' => '2e7a1f378beb13b6ee8ffac59d5e3095c194f8d55f35e0e0e6f828c07b813374'])->get([], ['user_id']));
            UI::send(
                DB::table('tokens')
                    ->get(
                        [new ForeignDataKey('user_id', 'users', 'id')],
                        ['user.email', 'token']
                    )
            );
        }
    }
?>

