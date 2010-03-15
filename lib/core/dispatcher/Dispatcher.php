<?php

class Dispatcher extends Object {
    public function dispatch() {
        $path = Mapper::parse();
        $path['controller'] = Inflector::hyphenToUnderscore($path['controller']);
        $path['action'] = Inflector::hyphenToUnderscore($path['action']);
        $controller_name = Inflector::camelize($path['controller']) . 'Controller';
        $view_path = $path['controller'] . '/' . $path['action'] . '.' . $path['extension'];
        if($controller =& ClassRegistry::load($controller_name, 'Controller')):
            if(!can_call_method($controller, $path['action']) && !App::path('View', $view_path)):
                $this->error('missingAction', array(
                    'controller' => $path['controller'],
                    'action' => $path['action']
                ));
                return false;
            endif;
        else:
            if(App::path('View', $view_path)):
                $controller =& ClassRegistry::load('AppController', 'Controller');
            else:
                $this->error('missingController', array(
                    'controller' => $path['controller']
                ));
                return false;
            endif;
        endif;
        $controller->params = $path;
        $controller->componentEvent('initialize');
        $controller->beforeFilter();
        $controller->componentEvent('startup');
        if(in_array($path['action'], $controller->methods) && can_call_method($controller, $path['action'])):
            $params = $path['params'];
            if(!is_null($path['id'])) $params = array_merge(array($path['id']), $params);
            call_user_func_array(array(&$controller, $path['action']), $params);
        endif;
        if($controller->autoRender):
            $controller->render();
        endif;
        $controller->componentEvent('shutdown');
        echo $controller->output;
        $controller->afterFilter();
        return $controller;
    }
}