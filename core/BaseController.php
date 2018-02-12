<?php

namespace Core;

class BaseController
{
    protected $template;

	public function __construct()
	{
        $this->template = new SmartyTemplate();		
	}
    
	public function view($view, $data)
	{
        
		foreach ($data as $key => $value) {
            $this->template->assign($key, $value);
		}

		$viewPath = dirname(__DIR__) . "/app/views/templates/$view.tpl";
        $this->template->display($viewPath);
	}

	public function redirect($action = DEFAULT_ACTION)
	{
		header("location:index.php/$action");
	}
}
