<?php

/**
 * Created by PhpStorm.
 * User: TIGER
 * Date: 22.07.2018
 * Time: 11:37
 */
class Router
{
    public $arConfig;

    public function __construct()
    {
        $this->arConfig = require_once (ROOT.'/config/router.php');
    }

    public function getURI() {
        return trim($_SERVER['REQUEST_URI'], '/');
    }

    public function run($mode = '') {
        $uri = $this->getURI();

        foreach ($this->arConfig as $pattern => $path) {

            if(preg_match("#$pattern#", $uri)) {
                $internalRoute = preg_replace("#$pattern#", $path, $uri);
                $arSegments = explode('/', $internalRoute);

                $controllerName = ucfirst(array_shift($arSegments))."Controller";
                $actionName = $mode."action".ucfirst(array_shift($arSegments));
             //   echo $actionName;
                $controllerFile = ROOT . '/controllers/'.$controllerName.'.php';
                if(!file_exists($controllerFile)) break;
                require_once $controllerFile;

                $objController = new $controllerName;
                if(!method_exists($objController, $actionName)) break;

                call_user_func_array(array($objController, $actionName), $arSegments);

                break;
            }
       }
    }

   public static function makeAjax($data) {
        echo json_encode($data);
        exit;
   }

   public static function makeAjaxError($message) {
        echo json_encode(array('result' => 0, 'message' => $message));
   }

    public static function makeView($module, $view, $data = []) {

        require ROOT . '/template/frontend/index.php';
    }
}