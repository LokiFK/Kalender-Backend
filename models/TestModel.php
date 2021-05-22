<?php

    class Tests extends Model implements ManualAttributes {
        protected $tableName = "tests";


        public function columns()
        {
            $this->int('id', 11);
            $this->int('user_id', 11);
            $this->int('token_id', 11);

            $this->create($this->tableName);

            $this->primary('id');
            $this->foreign('token_id', 'tokens', 'id');
            $this->foreign('user_id', 'users', 'id');
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