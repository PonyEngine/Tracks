<?php
    class Breadcrumbs
    {
        private $_trail = array();

        public function addStep($header, $link = '')
        {
            $this->_trail[] = array('header' => $header,
                                    'link'  => $link);
        }

        public function getTrail()
        {
            return $this->_trail;
        }

        public function getHeader()
        {
            if (count($this->_trail) == 0)
                return null;

            return $this->_trail[count($this->_trail) - 1]['header'];
        }
    }
?>