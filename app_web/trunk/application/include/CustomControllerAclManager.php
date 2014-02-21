<?
    class CustomControllerAclManager extends Zend_Controller_Plugin_Abstract{
        // default user role if not logged
        private $_defaultRole ="";

        // the action to dispatch if a user doesn't have sufficient privileges
        //Will be different
        private $_authController = array();

        public function __construct(Zend_Auth $auth)
        {  //SETS UP PERMISSION FOR USERS AND RESOURCES
            $appConfig=Zend_Registry::get('config');

            $this->_defaultRole=$appConfig->access->defaultuser;
            $this->_authController = array('controller' => $appConfig->access->defaultcontroller,
                                            'action' => $appConfig->access->defaultaction);

            $this->auth = $auth;
            $this->acl = new Zend_Acl();

            //****** USER ROLES********			
			//Default User
            $this->acl->addRole(new Zend_Acl_Role($this->_defaultRole));

            //ROLES
            $this->acl->addRole(new Zend_Acl_Role('member'));
            $this->acl->addRole(new Zend_Acl_Role('Admin'), 'member');
            $this->acl->addRole(new Zend_Acl_Role('developer'), 'Admin');

            //******** RESOURCES ************
            /** @noinspection PhpDeprecationInspection */
            /** @noinspection PhpDeprecationInspection */
            $this->acl->add(new Zend_Acl_Resource('index'));
            $this->acl->add(new Zend_Acl_Resource('account'));//General Account Space of All Members
            $this->acl->add(new Zend_Acl_Resource('settings'));
            $this->acl->add(new Zend_Acl_Resource('dashboard'));
            $this->acl->add(new Zend_Acl_Resource('admin')); //Admin space for statistics, etc
            $this->acl->add(new Zend_Acl_Resource('utility'));
            $this->acl->add(new Zend_Acl_Resource('debug'));
            $this->acl->add(new Zend_Acl_Resource('help'));
            $this->acl->add(new Zend_Acl_Resource('social'));
            $this->acl->add(new Zend_Acl_Resource('policy'));



            //PERMISSION ADDS based on default user
            switch ($this->_defaultRole){
                case 'guest':
                    $this->acl->addRole(new Zend_Acl_Role('service'));
                break;
                case 'service':
                    $this->acl->addRole(new Zend_Acl_Role('guest'));
                    $this->acl->deny(null, 'index');
                break;
                default:
                    $this->acl->addRole(new Zend_Acl_Role('service'));
                    $this->acl->addRole(new Zend_Acl_Role('guest'));
                break;
            }

  			//allow access to everything for all users by default
            //this includes the policy pages for terms, privacy, aboutus
            $this->acl->allow();
    		
            //Permission for Controllers

            $this->acl->deny(null, 'account');
           	$this->acl->deny(null, 'admin');
            $this->acl->deny(null, 'dashboard');
            $this->acl->deny(null, 'debug');
            $this->acl->deny(null, 'settings');
            $this->acl->deny(null, 'utility');



            //Deny Apis
			//Public Profile Prevention -Star Profiles
			//$this->acl->deny('newMember', 'user');
			//$this->acl->deny('member', 'user');
			// add an exception so guests can log in or register
            // in order to gain privilege
            $this->acl->allow('guest', 'policy');
            $this->acl->allow('guest', 'account', array('login',
                                                        'register',
                                                        'requestregister',
                                                        'requestregistration',
                                                        'requestlogin',
                                                        'retrievepassword',
                                                        'requestfblogin',
                                                        'fblogin'));
            $this->acl->allow('member', 'account');
            $this->acl->allow('member', 'settings');
            $this->acl->allow('member', 'help');
            $this->acl->allow('member', 'social');

            $this->acl->allow('guest', 'social',array('invite'));
            //---MEMBER PERMISSIONS
            //$this->acl->allow('member', 'dashboard');
           // $this->acl->deny('member', 'dashboard',array('events'));
            $this->acl->allow('member', 'dashboard', array('index',
                                                            'events',
                                                            'app',
                                                            'reports',
                                                            'createbrandevent',
                                                            'requestendevent',
                                                            'eventinfo'));


            $this->acl->allow('member', 'utility');

            //------ADMININISTRATOR PERMISSIONS
            $this->acl->allow('Admin', 'admin');
            $this->acl->allow('Admin', 'dashboard');

			//------Developer PERMISSIONS
            $this->acl->allow('developer', 'admin');
            $this->acl->allow('developer', 'debug');
            $this->acl->allow('developer', 'dashboard');

        }

        /**
         * preDispatch
         *
         * Before an action is dispatched, check if the current user
         * has sufficient privileges. If not, dispatch the default
         * action instead
         *
         * @param Zend_Controller_Request_Abstract $request
         */
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            // check if a user is logged in and has a valid role,
            // otherwise, assign them the default role (guest)
            if ($this->auth->hasIdentity()){
                $identity=$this->auth->getIdentity();
                $role = $identity->user_type;

            }
            else
                $role = $this->_defaultRole;

            if (!$this->acl->hasRole($role))
                $role = $this->_defaultRole;

            // the ACL resource is the requested controller name
            $resource = $request->controller;

            // the ACL privilege is the requested action name
            $privilege = $request->action;

            // if we haven't explicitly added the resource, check
            // the default global permissions
            if (!$this->acl->has($resource))
        				$resource= null;	

            if (!$this->acl->isAllowed($role, $resource, $privilege)) {
                $request->setControllerName($this->_authController['controller']);
                $request->setActionName($this->_authController['action']);
			}						
        }
    }