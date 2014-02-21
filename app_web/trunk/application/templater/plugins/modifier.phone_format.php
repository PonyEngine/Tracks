<?php 
 /**
 
 * @desc<pre>Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.phone_format.php
 * Type:     modifier
 * Name:     format_phone
 * Purpose:  format a US-style 10-digit phone number
 * -------------------------------------------------------------
 *</pre>
 * @param string $number a phone number
 * @param $format a printf style format string
 * @return string $number, the number formatted if 10 digits, else original untouched.
 * @author dmintz@davidmintz.org
 */


function smarty_modifier_phone_format($number, $format="(%s) %s-%s") {
   $original = $number;
   $number = preg_replace("/\D/","",$number);
   if (strlen($number) != 10) return $original;
   return  sprintf(
         $format,
         substr($number,0,3),
         substr($number,3,3),
         substr($number,6,4)
      );
} 
?>