<?php
/**
 * User: SavCo
 * Date: 5/24/12
 * Time: 4:31 PM
 * To change this template use File | Settings | File Templates.
 */
    class ServiceindexController extends CustomControllerAction{

        public function indexAction(){
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()){

            }else{

            }
     }


    public function thanksAction(){
        $session = new Zend_Session_Namespace('launchemail');

    }

}