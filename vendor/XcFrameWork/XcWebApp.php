<?php

namespace XcFrameWork;

class XcWebApp
{
    public function config()
    {
        $num = func_num_args();
        if ($num == 2) {
            $this->config[func_get_arg(0)] = func_get_arg(1);
        } elseif ($num == 1) {
            foreach (func_get_arg(0) as $key => $value) {
                $this->config[$key] = $value;
            }
        }
    }

    protected function init()
    {
        date_default_timezone_set('PRC');
        ob_start();
        session_start();
    }

    public function run()
    {
        $this->init();
    }
}