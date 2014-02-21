<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 6/9/13
 * Time: 7:27 PM
 * To change this template use File | Settings | File Templates.
 */

class FormProcessor_SendPushIOS extends FormProcessor
{
    protected $_db = null;
    protected $_config=null;
    private $_user=null;
    private $_sendToUserId=null;
    private $_sendToUser=null;
    private $_sendMsg=null;
    private $_sendBadgeCount='';
    private $_sendSoundURL='';


    public function __construct(DatabaseObject_User $user,$config)
    {
        parent::__construct();
        $this->_user= $user;
        $this->_db = $user->getDb();
        $this->_config=$config;
    }

    /*
    * USED WITH AJAX CALL
    */
    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    /*
    * ESTABLISHED MOBILE AWARENESS
    * BECAUSE SESSIONS ARE HANDLED DIFFRENTLY (STATELESS)
    */
    public function apiLogin($flag){
        $this->_apiLogin=(bool)$flag;
        $this->_clearIdentity=(bool)!$flag;
    }


    public function process(Zend_Controller_Request_Abstract $request)
    {
        //Supports JSON and Simple REST
        if($theJSON=$request->getPost('json')){
            $phpNative = Zend_Json::decode(stripslashes($theJSON), Zend_Json::TYPE_OBJECT);
        }else{
            $this->_sendToUserId=strlen($request->getParam("sendToUserId"))>0?$request->getParam("sendToUserId"):null;
            $this->_sendMsg=strlen($request->getParam("sendMsg"))>0?$request->getParam("sendMsg"):null;  //Optional
            $this->_sendBadgeCount=strlen($request->getParam("sendBadgeCount"))>0?$request->getParam("sendBadgeCount"):null;  //Optional
        }

        //Check
        if  (strlen($this->_sendToUserId) == 0 )$this->addError('sendToUserId','A SendTo User Id is required.');

        if (!$this->hasError()){
            $this->_sendToUser= new DatabaseObject_User($this->_db);
            if($this->_sendToUser->load((int)$this->_sendToUserId)){
                $pushNotification= new SavCo_PushIOS($this->_config,$this);
                if(!$result=$pushNotification->pushSimpleNotification($this->_sendToUser,$this->_sendMsg,$this->_sendBadgeCount,$this->_sendSoundURL,$this->_config)){
                    $this->addError('push','Could not send the push notification');
                }
            }else{
                $this->addError('sendToUser','No Such User');
            }

        }
        //$feedback=$this->send_feedback_request();

        //$this->testpush($this->_config);
        return !$this->hasError();
    }

    public function  testpush($config=null){
        // Put your device token here (without spaces):
        $deviceToken = '791f76adc9b03abbe182a443127fbd463701c66d05c384b20daeba33f62cca7e';
        // Put your private key's passphrase here:
        $passphrase = $config->ios->push->passphrase;

// Put your alert message here:
        $message = 'Test Message from Server';

////////////////////////////////////////////////////////////////////////////////

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $config->ios->push->certificate);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(
            $config->ios->push->server , $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect_4: $err $errstr" . PHP_EOL);

        echo 'Connected to APNS' . PHP_EOL;

        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
            echo 'Message not delivered' . PHP_EOL;
        else
            echo 'Message successfully delivered' . PHP_EOL;

        // Close the connection to the server
        fclose($fp);
    }

    function send_feedback_request() {
        //connect to the APNS feedback servers
        //make sure you're using the right dev/production server & cert combo!
        //0   - No errors encountered
       /* 1   - Processing error
2   - Missing device token
3   - Missing topic
4   - Missing payload
5   - Invalid token size
6   - Invalid topic size
7   - Invalid payload size
8   - Invalid token
255 - None (unknown)*/
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', 'myboi.pem');
        $apns = stream_socket_client('ssl://feedback.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        if(!$apns) {
            echo "ERROR $errcode: $errstr\n";
            return;
        }


        $feedback_tokens = array();
        //and read the data on the connection:
        while(!feof($apns)) {
            $data = fread($apns, 38);
            if(strlen($data)) {
                $feedback_tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
            }
        }
        fclose($apns);
        return $feedback_tokens;
    }


}

?>