<?php

    class Users extends Model implements ManualAttributes {
        protected $tableName = "users";


        public function columns()
        {
            $this->int('id', 11);
            $this->string('name', 30);
            $this->string('email', 30);
            $this->string('password', 60);

            $this->create();

            $this->primary('id');
        }

        public static function where($conditions, $withForeignRelations=false, $tableName="")
        {
            return parent::where($conditions, $withForeignRelations, static::class);
        }

        public static function fetchAll($tableName="")
        {
            return parent::fetchAll(static::class);
        }

        public static function fetch($id, $tableName="")
        {
            return parent::fetch($id, static::class);
        }

        public static function drop($tableName="")
        {
            return parent::drop(static::class);
        }
    }

?>