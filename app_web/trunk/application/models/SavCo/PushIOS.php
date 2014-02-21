<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SavCo
 * Date: 6/9/13
 * Time: 6:38 PM
 * To change this template use File | Settings | File Templates.
 */

	class SavCo_PushIOS extends SavCo{
        private $fp = NULL;
        private $_server;
        private $_certificate;
        private $_passphrase;
        private $_fpError;

        function __construct($config,$fpError=NULL)
        {

            $this->_server = $config->ios->push->server;
            $this->_certificate = $config->ios->push->certificate;
            $this->_passphrase = $config->ios->push->passphrase;
            $this->_fpError=$fpError;
        }

        //Single Push
        public function pushSingle($messageId,$deviceToken,$messagePayload )
        {
            $msg=sprintf('PUSH:Connecting to %s ', $this->_server);
            SavCo_ZendExtend::Log($msg,'notice');



            if (!$this->connectToAPNS())
                return FALSE;

            //while (true)
            //{
                // Do at most 20 messages at a time. Note: we send each message in
                // a separate packet to APNS. It would be more efficient if we
                // combined several messages into one packet, but this script isn't
                // smart enough to do that. ;-)

                if ($this->sendPushNotification($messageId, $deviceToken, $messagePayload))
                {

                }
                else  // failed to deliver
                {
                       $this->reconnectToAPNS();
                }
               // unset($messages);
                //sleep(5);
            //}
            return TRUE;
        }



        // This is the main loop for this script. It polls the database for new
        // messages, sends them to APNS, sleeps for a few seconds, and repeats this
        // forever (or until a fatal error occurs and the script exits).
        function loop($messages)
        {

            SavCo_ZendExtend::Log(sprintf('PUSH:Connecting to %s ', $this->_server),'notice');
            if (!$this->connectToAPNS())
                return false;

            while (true)
            {
                // Do at most 20 messages at a time. Note: we send each message in
                // a separate packet to APNS. It would be more efficient if we
                // combined several messages into one packet, but this script isn't
                // smart enough to do that. ;-)

                foreach ($messages as $message)
                {
                    if ($this->sendNotification($message->message_id, $message->device_token, $message->payload))
                    {

                    }
                    else  // failed to deliver
                    {
                        $this->reconnectToAPNS();
                    }
                }

                unset($messages);
                sleep(5);
            }
        }

        // Opens an SSL/TLS connection to Apple's Push Notification Service (APNS).
        // Returns TRUE on success, FALSE on failure.
        function connectToAPNS()
        {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->_certificate);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);

            $this->fp = stream_socket_client(
                'ssl://' . $this->_server, $err, $errstr, 60,
                STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$this->fp)
            {
                $errorMsg="Failed to connect_1: $err $errstr";
                SavCo_ZendExtend::Log($errorMsg,'warning');
                if($this->_fpError)$this->_fpError->addError('push_class',$errorMsg);
                return FALSE;
            }

            SavCo_ZendExtend::Log("Connection OK",'notice');
            return TRUE;
        }

        // Drops the connection to the APNS server.
        function disconnectFromAPNS()
        {
            fclose($this->fp);
            $this->fp = NULL;
        }

        // Attempts to reconnect to Apple's Push Notification Service. Exits with
        // an error if the connection cannot be re-established after 3 attempts.
        function reconnectToAPNS()
        {
            $this->disconnectFromAPNS();

            $attempt = 1;

            while (true)
            {
                SavCo_ZendExtend::Log('Reconnecting to ' . $this->_server . ", attempt $attempt",'notice');

                if ($this->connectToAPNS())
                    return;

                if ($attempt++ > 3)
                    fatalError('Could not reconnect after 3 attempts');

                sleep(60);
            }
        }

        // Sends a notification to the APNS server. Returns FALSE if the connection
        // appears to be broken, TRUE otherwise.
        function sendNotification($messageId, $deviceToken, $payload)
        {
            if (strlen($deviceToken) != 64)
            {

                $errorMsg="Message $messageId has invalid device token";
                SavCo_ZendExtend::Log($errorMsg,'notice');
                if($this->_fpError)$this->_fpError->addError('push_Class',$errorMsg);
                return TRUE;
            }

            if (strlen($payload) < 10)
            {
                $errorMsg="Message $messageId has invalid payload";
                SavCo_ZendExtend::Log($errorMsg,'notice');
                if($this->_fpError)$this->_fpError->addError('push_Class',$errorMsg);
                return TRUE;
            }


            SavCo_ZendExtend::Log("Sending message $messageId to '$deviceToken', payload: '$payload'",'notice');

            if (!$this->fp)
            {
                $errorMsg='No connection to APNS';
                SavCo_ZendExtend::Log($errorMsg,'notice');
                if($this->_fpError)$this->_fpError->addError('push_Class',$errorMsg);
                return FALSE;
            }

            // The simple format
            $msg = chr(0)                       // command (1 byte)
                . pack('n', 32)                // token length (2 bytes)
                . pack('H*', $deviceToken)     // device token (32 bytes)
                . pack('n', strlen($payload))  // payload length (2 bytes)
                . $payload;                    // the JSON payload

            /*
           // The enhanced notification format
           $msg = chr(1)                       // command (1 byte)
                . pack('N', $messageId)        // identifier (4 bytes)
                . pack('N', time() + 86400)    // expire after 1 day (4 bytes)
                . pack('n', 32)                // token length (2 bytes)
                . pack('H*', $deviceToken)     // device token (32 bytes)
                . pack('n', strlen($payload))  // payload length (2 bytes)
                . $payload;                    // the JSON payload
           */

            $result = @fwrite($this->fp, $msg, strlen($msg));

            if (!$result)
            {
                $errorMsg='Message not delivered';
                SavCo_ZendExtend::Log('Message not delivered','notice');
                return FALSE;
            }

            SavCo_ZendExtend::Log('Message successfully delivered','notice');
            return TRUE;
        }

        public function pushSimpleNotification(DatabaseObject_User $user,$message,$pony=array(),$badgeCount=NULL,$soundName=NULL,FormProcessor $formp=NULL){
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->_certificate);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->_passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client(
                $this->_server, $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp){
                if($formp){
                    $formp->addError('push',"Failed to connect_2: $err $errstr" . PHP_EOL);
                }
                return false;
            }
            echo 'Connected to APNS' . PHP_EOL;

            // Create the payload body
            $body['aps'] = array(
                'pony'=>  $pony,
                'alert' => $message,
                'sound' => 'cheering.wav' // $soundName?$soundName:'default'
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $user->session->device_token) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if (!$result)
                echo 'Message not delivered' . PHP_EOL;
            else
                $formp->addError('send','Message successfully delivered' . PHP_EOL);

            // Close the connection to the server
            fclose($fp);

            return TRUE;
        }

        public function  testpush(){
            // Put your device token here (without spaces):
            $deviceToken = '079d1210a115b74a9c4577598e53a4b8e1d953b725f9b14406c48118b70baae0';
            // Put your private key's passphrase here:
            $passphrase = 'ponyengine';

// Put your alert message here:
            $message = 'Inside of PushIOS!';

////////////////////////////////////////////////////////////////////////////////

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect_3: $err $errstr" . PHP_EOL);

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

    }
