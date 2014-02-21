<?
	class SavCo_ZendExtend extends SavCo{

      public static function Email_FromName_From_To_Data_Template_isHTML($fromName,$fromEmail,$toEmail,$dataArr,$tpl,$isHTML=false){
              try{
                  //Set values to be used in email
                  $templater= new Templater();
                  $templater->dataArr=$dataArr;
                  $templater->config=Zend_Registry::get('config');

                  //fetch the email body
                  $body= $templater->render('email/'.$tpl);

                  //extract the subject from the first line.
                  list($subject,$body)=preg_split('/\r|\n/',$body,2);

                  //now set-up and send the e-mail
                  $mail= new Zend_Mail();

                  //set the to address and the user's full name in the 'to' line
                  $mail->addTo($toEmail);

                  //get the admin 'from details from the config
                  $mail->setFrom($fromEmail,$fromName);

                  //set the subject and body and send the mail
                  $mail->setSubject(trim($subject));
                  if(!$isHTML){
                      $mail->setBodyText(trim($body));
                  }else{
                      $mail->setBodyHtml(trim($body));
                  }

                  $theMail=$mail->send();
                  $message=sprintf('Email sent to %s',$toEmail);
                  $logger= Zend_Registry::get('logEvent');
                  $logger->warn($message,1);
                  return true;
              }
              catch(Exception $e){
                  $message=sprintf('Not able to email %s:%s',$toEmail,$e->getMessage());
                  $logger= Zend_Registry::get('logEvent');
                  $logger->warn($message,1);
              }
         }

        public static function Email_FromName_From_To_EmailSubject_EmailBody_isHTML($fromName,$fromEmail,$toEmail,$emailSubject,$emailBody,$isHTML=false){
            //There are consistent Items and Variable Items
            //The Variable Items are
            try{
                //now set-up and send the e-mail
                $mail= new Zend_Mail();

                //set the to address and the user's full name in the 'to' line
                $mail->addTo($toEmail);

                //get the admin 'from details from the config
                $mail->setFrom($fromEmail,$fromName);

                //set the subject and body and send the mail
                $mail->setSubject(trim($emailSubject));
                if(!$isHTML){
                    $mail->setBodyText(trim($emailBody));
                }else{
                    $mail->setBodyHtml(trim($emailBody));
                }

                $theMail=$mail->send();
                $message=sprintf('Db Email sent to %s',$toEmail);
                $logger= Zend_Registry::get('logEvent');
                $logger->warn($message,1);
                return true;
            }
            catch(Exception $e){
                $message=sprintf('Not able to email %s:%s',$toEmail,$e->getMessage());
                $logger= Zend_Registry::get('logEvent');
                $logger->warn($message,1);
            }
        }

        public static function Log($message,$logType='notice'){
            $logger= Zend_Registry::get('logEvent');
            switch ($logType){
                case 'notice':
                default:
                    $logger->notice($message,1);
                break;
            }

        }

        public static function FullUrl($url) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;
            return $url;
        }
    }