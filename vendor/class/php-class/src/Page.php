<?php 
    namespace Class;

    use Rain\Tpl;
    class Page
    {
        private $tpl;
        private $default = [
            "header" => true,
            'footer' => true,
            'data' => []
        ];
        private $options = array();

        public function __construct($opts = array(), $tplDir = 'views/site')
        {
            $this->options = array_merge($this->default, $opts);
            $conf = array(
                "tpl_dir" => $tplDir,
                'cache_dir' => 'views-cache/',
                'debug' => false
            );

            Tpl::configure($conf);

            $this->tpl = new Tpl();

            foreach ($this->options['data'] as $key => $val){
                $this->tpl->assign($key, $val);
            }
            if($this->options['header']) $this->tpl->draw("header");
        }

        public function setData($data = array())
        {
            foreach ($data as $key => $value) {
                $this->tpl->assign($key, $value);
            }
        }
        public function setTpl($name, $data = array(), $returnHtml = false)
        {
            if(isset($data)) $this->setData($data);

            return $this->tpl->draw($name, $returnHtml);
        }
        

        public function __destruct()
        {
            if($this->options['footer']) return $this->tpl->draw("footer");
        }
    }
?>