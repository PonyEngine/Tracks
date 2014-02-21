<?php
class HelpController extends CustomControllerAction {

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) { //user logged in
            $this->_redirect('/');
        }
    }
}
?>
