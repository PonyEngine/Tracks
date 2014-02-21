<?
class CustomControllerAction extends Zend_Controller_Action
{
    public $db;
    public $breadcrumbs;
    public $messenger;
    public $viewer;
    public $config;
    public $facebook;
    public $fbId;
    public $jsCode=array();
    public $cssCode=array();
    public $lessCode=array();
    protected $auth=null;
    protected $identity=null;

    function init()
    {
        $this->db = Zend_Registry::get('db');
        $this->config=Zend_Registry::get('config'); //Setting up config
        $this->breadcrumbs = new Breadcrumbs();
        $header=array('title'=>'Home');
        $this->breadcrumbs->addStep($header, $this->getUrl(null, 'index'));
        $this->messenger = $this->_helper->_flashMessenger;

        $this->facebook = new Facebook_Access(array(
            'appId'  => $this->config->facebook->appid,
            'secret' => $this->config->facebook->secret,
            'cookie' => true,
            'scope' =>'email,publish_actions,offline_access,publish_stream',
        ));

        $this->fbId=$this->facebook->getUser();

    }

    public function getUrl($action = null, $controller = null)
    {    //for some reasson the getBaseUrl is not returning any value
        // $url  = rtrim($this->getRequest()->getBaseUrl(), '/') . '/';
        $url  = rtrim('http://'.$_SERVER['HTTP_HOST'], '/') ;
        $url .= $this->_helper->url->simple($action, $controller);

        return $url;
    }


    public function preDispatch()
    {
        $this->auth = Zend_Auth::getInstance();

        if ($this->auth->hasIdentity()) {
            //echo "Has identity";
            $this->view->authenticated = true;
            $this->identity = $this->auth->getIdentity();
            $this->view->identity = $this->auth->getIdentity();
        }
        else{
            //echo "NO IDENTITY";
            $this->view->authenticated = false;
            $this->viewer = null;
            $this->view->viewer = null;
        }
        //Facebook Login
        if($this->fbId){
            //echo "The Fbid is".$this->fbId;
        }else{
            //echo "NO FBID";
        }

    }


    public function postDispatch()
    {
        $this->view->breadcrumbs = $this->breadcrumbs;
        $this->view->header = $this->breadcrumbs->getHeader();
        $this->view->messages = $this->messenger->getMessages();
        $this->view->config=Zend_Registry::get('config');
        $this->view->jsCode=$this->jsCode;
        $this->view->cssCode=$this->cssCode;
        $this->view->lessCode=$this->lessCode;
        $this->view->auth=$this->auth;
        // $this->view->identity=$this->auth->hasIdentity()?$this->auth->getIdentity():null;

        //Zend_Registry::get  set('mongodb',$mongodb);
        // $mongodb=Zend_Registry::get('mongodb');
        // $mongodb->close();
    }


    /**
     * @NOTE Savalas says this sucked implementing.
     *
     * @param $data
     * @return void
     */
    public function sendJson($data)
    {
        $this->_helper->viewRenderer->setNoRender();
        //$this->getResponse()->setHeader('content-type','text/json');
        $this->getResponse()->setHeader('content-type','application/json');
        echo Zend_Json::encode($data);
    }

    public function sendJsonText($data)
    {
        $this->_helper->viewRenderer->setNoRender();
        //Complaint about modifying the header
        //$this->getResponse()->setHeader('content-type', 'application/json');
        echo Zend_Json::encode($data);
    }


    /**
     * Application XML
     *
     * @param $data
     * @return void
     */
    public function sendXml2($data)
    {
        //disable autorendering since we're outputting kml feed
        $this->getResponse()->setHeader('content-type', 'application/xml');

        $this->_helper->viewRenderer->setNoRender();

        echo trim($data);
    }

    /**
     * Google XML version
     *
     * @param $data
     * @return void
     */
    public function sendXML($data)
    {
        //disable autorendering since we're outputting kml feed
        $this->getResponse()->setHeader('content-type', 'application/vnd.google-earth.kml+xml');

        $this->_helper->viewRenderer->setNoRender();

        echo trim($data);
    }


    /**
     * RSS FEED Format
     *
     * @param $data
     * @return void
     */
    public function sendGeoRss($data)
    {
        $feed= Zend_Feed::importArray($data,'rss');
        //disable autorendering since we're outputting kml feed
        $this->_helper->viewRenderer->setNoRender();
        $feed->send();
    }

}