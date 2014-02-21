<?php

 class SavCo_DataFeeds extends SavCo
    {
		var $text;
		var $arrays, $keys, $node_flag, $depth, $xml_parser;
		/*Converts an array to an xml string*/



		public function arraytoKML($array,$name="spontts"){
			$header='<?xml version="1.0" encoding="UTF-8"?>';
			/*$header.='<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:atom="http://
www.w3.org/2005/Atom" xmlns:xal="urn:oasis:names:tc:ciq:xsdschema:xAL:
2.0" xmlns:gx="http://www.google.com/kml/ext/2.2">';*/ 
			$header.='<Document><name><![CDATA['.$name.']]></name>';
	 		$footer='</Document></kml></xml>';
			
			$this->text=$header;
			$this->text.=$this->array_transform($array);
			$this->text.=$footer;
			return $this->text;
		}


		function array_transform($array){
			//global $array_text;
			foreach($array as $key => $value){
				if(!is_array($value)){
 					$this->text .= "<$key>$value</$key>";
 				} else {
 					$this->text.="<$key>";
 					$this->array_transform($value);
 					$this->text.="</$key>";
 				}
			}
			return $array_text;
		}

}