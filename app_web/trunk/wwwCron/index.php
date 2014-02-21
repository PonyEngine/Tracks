<?
  //This is for cron job processing found at
 // /wwwCron/index.php
//{B85DB132-DB74-4F9F-2FCE-D729884999E4}
    $domain='http://tracks.ponyengine.com';

   $html="$domain/api/runapi?userId=1&authToken={B85DB132-DB74-4F9F-2FCE-D729884999E4}&lat=34.017&lon=-118.495";

    //Connect and pull from database whether to run
    //daysbefore
   //daysafter
   //which sports
    if(true){
        $contents= file_get_contents($html);
        echo "Processed Cron job";
        echo $contents;
    }

    sleep(3);
    $htmlPushInvites="$domain/api/ponypushnotifinvites?userId=1&authToken={B85DB132-DB74-4F9F-2FCE-D729884999E4}&lat=34.017&lon=-118.495";
    if(true){
        $contents= file_get_contents($htmlPushInvites);
        echo "Pushed Invites";
        echo $contents;
    }

    sleep(3);
    $htmlPushInvites="$domain/api/ponypushsettling?userId=1&authToken={B85DB132-DB74-4F9F-2FCE-D729884999E4}&lat=34.017&lon=-118.495";
    if(true){
        $contents= file_get_contents($htmlPushInvites);
        echo "Settling Unsettled Bets";
        echo $contents;
    }







