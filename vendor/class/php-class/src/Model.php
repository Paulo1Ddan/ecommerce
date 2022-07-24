<?php 
    namespace Class;

    class Model{
        private $values = [];

        public function __call($name, $arguments)
        {
            $method = substr($name, 0, 3);

            $fieldName = substr($name, 3, strlen($name));

            if($method === 'get') return (isset($this->values[$fieldName]) ? $this->values[$fieldName] : NULL);
            if($method === 'set') $this->values[$fieldName] = $arguments[0];
        }

        public function setData($data = array())
        {
            foreach ($data as $key => $value) {
                $this->{'set'.$key}($value);
            }
        }

        public function getData()
        {
            return $this->values;
        }
    }
?>