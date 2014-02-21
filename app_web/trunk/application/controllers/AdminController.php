<?php
	class AdminController extends CustomControllerAction{
		 /* This causes the header to be sent twice on a redirect
		  *  public function init(){	
            parent::init();
			$header=array('title'=>'Administration');
            $this->breadcrumbs->addStep($header, $this->getUrl(null, 'admin'));

            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {// if a user's already logged in, send them to their account home page
            	$this->viewer = new DatabaseObject_User($this->db);
            	$this->actIdent=$auth->getIdentity();
		 		$this->viewer->load($this->actIdent->user_id);
		 		$this->view->viewer=$this->viewer;
        	}
         }*/
		
		public function indexAction(){
			$this->_redirect('/admin/management');	
		}


        public  function emaildesignerAction(){

        }


		public function managementAction(){
            //0= no diff
            //1=new/create file (a left file but no right file)
            //2=file different
            //3=delete file (a right file but no left file)

            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) { //user logged in
                $actIdentity=$auth->getIdentity();
                if($actIdentity->user_type!='developer')
                    $this->_redirect('/');
            }

			$fp=null;
			$request = $this->getRequest();

			$this->viewer= new DatabaseObject_User($this->db);
			$this->viewer->load($this->identity->user_id);
			$this->view->viewer=$this->viewer;
            $this->view->userCount= DatabaseObject_User::GetTotal($this->db);


            $area=$request->getParam('area');
            $func=$request->getParam('func');
            $this->view->filePath="";
            $this->view->area_func="";


            if(strlen($area)>0 && strlen($func)>0){
                $this->view->filePath=sprintf("admin/management/%s/%s.tpl",$area,$func);
                $this->view->area_func=sprintf("%s_%s",$area,$func);
                $this->view->selected=sprintf("%s_%s",$area,$func);
            }else{
                $this->view->filePath='admin/management/users/create_u.tpl';
                $this->view->selected="create_u";
            }

            //Area Func Processing- ExtraVariables- possibly move
            switch ($area){
                case 'debugging':
                    $this->debuggingArea($func);
                break;
                case 'deployment':
                    $this->deploymentArea($func);
                break;
                case 'users':
                    $this->usersArea($func);
                break;
            }

            $this->view->area=$area;
            $this->view->func=$func;
       }


		public function ajaxAction(){
			$request=$this->getRequest();
            $theProcedure=$request->getParam('procedure');

			$adminFunc= new SavCo_AdminFunctions();
			$this->view->tester="Help";
			switch($theProcedure){
				case 'getEventLog':
					//List product item number
					$this->view->eventLogArr=$adminFunc->getEventLog($this->config);
					$this->view->eventLogArr=array(1,2,3);
					$this->view->filename='admin/ajax/eventLog.tpl';
				break;
				default:
					$this->view->eventLogArr=$adminFunc->getEventLog($this->config);
					$this->view->filename='admin/ajax/eventLog.tpl';
				break;	
			}
		}
		
		
		public function logAction()
        {        	        	
			$header=array('title'=>'System Logging');
            $this->breadcrumbs->addStep($header, $this->getUrl(null, 'admin/log'));
			$this->view->selectedAdminNav='log';
		}

        //Areas

        private function debuggingArea($func){
            switch ($func){
                case 'viewlog':
                    $txt_file    = file_get_contents('../data/logs/event.log');
                    $rows        = explode("\n", $txt_file);
                    array_shift($rows);
                    $this->view->logRows=$rows;
                    break;
                case 'viewdata':
                    $dataFiles=SavCo_FunctionsGen::ListFiles('../data/');
                    $this->view->dataFiles=$dataFiles;
                    break;
                case 'viewtmp':
                    $tmpFiles=SavCo_FunctionsGen::ListFiles('../www/tmp/');
                    $this->view->tmpFiles=$tmpFiles;
                    break;

            }
        }

        private function deploymentArea($func){
            if($func=="files"){
                //Process Log Files
                $fromDir='../';
                $fromFiles=SavCo_FunctionsGen::ListFiles($fromDir);
                $this->view->fromFiles=$fromFiles;

                $toDir=$this->config->deploy->to;
                $toFiles=SavCo_FunctionsGen::ListFiles($toDir);
                $this->view->toFiles=$toFiles;

                $indexPosFrom=0;
                $indexPosTo=0;
                $fromToFiles=array();
                $totalToIndexCount=count($toFiles);
                foreach ($fromFiles as $theFile){
                    $cleanedFromPath=str_replace($fromDir,"",$theFile['fullPath']);
                    //If files are the same at this position- then add to this position
                    if(strcmp($cleanedFromPath,str_replace($toDir,"",$toFiles[$indexPosTo]['fullPath']))==0){
                        $theFiles['from']=$theFile;
                        $theFiles['to']=$toFiles[$indexPosTo];

                        //Compare Files- date/size
                        if($theFile['time']==$toFiles[$indexPosTo]['time']){
                            $theFiles['status']=0;
                        }else{
                            $theFiles['status']=1;
                        }


                        $fromToFiles[]=$theFiles;

                        //Move Indexes of both forward
                        //$indexPosFrom++;
                        $indexPosTo++;
                    }else{
                        //
                        //Loop All file names to see if there are files ahead that match the fromDir
                        $toIndexCheck=$indexPosTo;
                        while($toIndexCheck <= $totalToIndexCount){
                            if(strcmp($cleanedFromPath,str_replace($toDir,"",$toFiles[$toIndexCheck]['fullPath']))==0){
                                //Found File
                                //Delete the Previous Files from the $indexPosTo - $totalToIndexCount
                                //Cycle from presemy location up until there is a match.
                                for($i=$indexPosTo;$i<$toIndexCheck;$i++){
                                    $theFiles['to']=$toFiles[$i];
                                    $theFiles['from']='';
                                    $theFiles['status']=3;
                                    $fromToFiles[]=$theFiles;
                                }
                                $indexPosTo=$toIndexCheck; //new location
                                //set the array back for another looping

                                //HACKY- Process the current space- kind of hack- could not reset internal iterator
                                //**************
                                $theFiles['from']=$theFile;
                                $theFiles['to']=$toFiles[$indexPosTo];

                                //Compare Files- date/size
                                if($theFile['time']==$toFiles[$indexPosTo]['time']){
                                    $theFiles['status']=0;
                                }else{
                                    $theFiles['status']=1;
                                }

                                $fromToFiles[]=$theFiles;

                                //Move Indexes of both forward
                                //$indexPosFrom++;
                                $indexPosTo++;
                                //***************



                                break;
                            }else{
                                $toIndexCheck++;
                            }
                        }
                        //File Does not Exist and needs to be added
                        if($toIndexCheck>$totalToIndexCount){
                            $theFiles['from']=$theFile;
                            $theFiles['to']='';
                            $theFiles['status']=2;
                            $fromToFiles[]=$theFiles;
                            //From Increments Automatically but To Doesn't
                        }
                    }
                }

                //What I need to get is firstFile

                $this->view->fromToFiles=$fromToFiles;
            }
        }

        private function usersArea($func){
            switch($func){
                case 'create_u':
                    if($this->identity->user_type!="Admin" && $this->identity->user_type!="developer"){
                        $this->_redirect('/');
                    }
                    $theUser= new DatabaseObject_User($this->db);
                    $theUser->load($this->identity->user_id);

                    $recaptcha = new Zend_Service_ReCaptcha($this->config->recaptcha->pubkey, $this->config->recaptcha->privkey);
                    $this->view->recaptcha=$recaptcha->getHTML();
                    $options=array();
                    //Get Artists
                    $brands=DatabaseObject_Brand::GetBrandsWithOptions($this->db,$options);
                    $this->view->brands=$brands;

                    //CSS JSS LESS
                    $this->jsCode[]=$this->config->url->js."_bootstrap/bootstrap-datepicker.js";
                    $this->cssCode[]=$this->config->url->css."_bootstrap/datepicker.css";
                    //$this->lessCode[]=$this->config->url->css."_bootstrap/datepicker.less";

                    $this->view->menuSelect="brandsandusers";
                break;
            }

        }
		
	}
