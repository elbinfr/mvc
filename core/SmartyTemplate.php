<?php

namespace Core;

class SmartyTemplate extends \Smarty
{
    public function __construct()
    {
        parent::__construct();        
        $this->setCacheDir(DIR_SMARTY_CACHE);
        $this->setConfigDir(DIR_SMARTY_CONFIG);
        $this->setCompileDir(DIR_SMARTY_COMPILE);
        $this->setTemplateDir(DIR_SMARTY_TEMPLATE);
    }   
}
