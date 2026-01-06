<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Apache Helper
 * @category   Helpers
 */
//require_once 'LogParser\LogParser.php';

abstract class Helpers_Apachelog {
        //    get all tags data
    public static function get_parse_log($id=null) {               
        //$lines
        /* for http */
          //$lines
        ini_set('memory_limit', -1);
        ini_set('memory_limit', '9999999990024M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        
        //$file_log= __DIR__ . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR . 'access_log'; 
        //$file_log= '/var'. DIRECTORY_SEPARATOR . 'www'. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR . 'aies_log'. DIRECTORY_SEPARATOR . 'access_log'; 
        //linux
        $file_log= '/var'. DIRECTORY_SEPARATOR . 'www'. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR .'aies_log' . DIRECTORY_SEPARATOR . 'access_log'; 
        
        //window 
        //$file_log= dirname(dirname(getcwd())) . DIRECTORY_SEPARATOR .'apache' . DIRECTORY_SEPARATOR .'logs' . DIRECTORY_SEPARATOR . 'access.log';
        //$ac_arr  = file($file_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ac_arr  = file($file_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        $i = 1;
        $each_rec = 0;
        $ip_log_array = array();
        $ip_unique = array();
        $ip_unique_count = array();
        
        foreach ($ac_arr as $line) {
            if (strpos($line, '::1 - -') !== false) {             
                continue;
            }          
           
            $regex = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) "([^"]*)" "([^"]*)"$/';
            preg_match($regex ,$line, $matches);
            if(empty($matches[4]) && empty($matches[5]))        
                continue;
            $access_time = $matches[4] . ' ' . $matches[5];
            $access_time = str_replace("/","-",$access_time);
            if(!empty($access_time)) 
            {            
              $current_date = strtotime(date('Y-m-d H:i:s', strtotime('-5 minutes')));
              $file_date = strtotime(date('Y-m-d H:i:s', strtotime(trim($access_time))));
               if($file_date < $current_date)
                {
                      continue;
                }
            }
            if(empty($matches[1]))
                continue;
            if(!empty($matches[1]) && $matches[1]=='50.0.0.3')
                continue;
            
            
            $ip_unique[$i] = 'ip : '; 
            $ip_unique[$i] .= !empty($matches[1])?$matches[1]:'';
            $ip_log_array[$i]['acces_time']= $access_time;
            $ip_unique[$i].= ': acces_time ' . $access_time;
            $ip_log_array[$i]['page']= !empty($matches[8])?$matches[8]:'';
            $ip_unique[$i].= 'page ' .  !empty($matches[8])?$matches[8]:'';
            $ip_log_array[$i]['type']= !empty($matches[7])?$matches[7]:'';
            $ip_log_array[$i]['success_code']= !empty($matches[10])?$matches[10]:'';
            $ip_log_array[$i]['bytes_transferred']= !empty($matches[11])?$matches[11]:'';
            $ip_log_array[$i]['reference']= !empty($matches[9])?$matches[9]:'';
            $ip_log_array[$i]['browser']= !empty($matches[13])?$matches[13]:'';
        
            $i= $i+1;
          //$entry = $parser->parse($line);            
           
        }
        
        $vals = array_count_values($ip_unique);
        
        $unique_ip_attack = array();
        foreach($vals as $key => $vals)
        {
            if($vals>=7)
            {
                $ip_address = explode(":",$key);                 
                $unique_ip_attack[]= trim($ip_address[1]);
             }
        }    
        $new_arr = array_unique($unique_ip_attack, SORT_REGULAR);        
        
        
        foreach($new_arr as $blocklist)
        {
            $check_ip_exist = Helpers_Utilities::checkblockIPforever($blocklist);
            if($check_ip_exist==1)
            {
                //echo ' already exist : ' . $blocklist;
                echo '';
            }else {
               // echo ' not exist exist : ' . $blocklist;
                $check_ip_exist = Helpers_Utilities::addblockIPapache($blocklist);
                
            }
        } 
        
        /* For SSL log */
        /*
        ini_set('memory_limit', -1);
        ini_set('memory_limit', '9999999990024M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        
        //$file_log= __DIR__ . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR . 'access_log'; 
        //$file_log= '/var'. DIRECTORY_SEPARATOR . 'www'. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR . 'aies_log'. DIRECTORY_SEPARATOR . 'access_log'; 
        $file_log= '/var'. DIRECTORY_SEPARATOR . 'www'. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR . 'aies_log'. DIRECTORY_SEPARATOR . 'ssl_access_log'; 
        $ac_arr  = file($file_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $astring = join("", $ac_arr);
        $astring = preg_replace("/(\n|\r|\t)/", "", $astring);
        $records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $astring, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sizerecs = sizeof($records);
        
        // now split into records
        $i = 1;
        $each_rec = 0;
        $ip_log_array = array();
        $ip_unique = array();
        $ip_unique_count = array();
        
        
        while($i<$sizerecs) {
          $ip = '';   $access_time = '';  $link[0]= '';  $link[1]= '';
          $success_code= '';   $bytes= '';    $ref= '';  $browser= '';
          
        //foreach ($records as $recod){    
          $ip = $records[$i];
          $all = $records[$i+1];
          // parse other fields
          preg_match("/\[(.+)\]/", $all, $match);      
          if(!empty($match))
          { $access_time = !empty($match[1])?$match[1]:$match[0];
            $all = str_replace($match[1], "", $all);
          }
          
          if(!empty($access_time)) 
          {
              $current_date = strtotime(date('Y-m-d H:i:s', strtotime('-15 minutes')));
              $file_date = strtotime(date('Y-m-d H:i:s', strtotime($access_time)));
            if($file_date < $current_date)
            {
                $i = $i + 2;
                  $each_rec++;
                  continue;

            }
          }
          preg_match("/\"[A-Z]{3,7} (.[^\"]+)/", $all, $match);
          if(!empty($match))
          {    
            $http = $match[1];
            $link = explode(" ", $http);
            $all = str_replace("\"[A-Z]{3,7} $match[1]\"", "", $all);
          }
          preg_match("/([0-9]{3})/", $all, $match);
          $success_code = $match[1];
          $all = str_replace($match[1], "", $all);
          preg_match("/\"(.[^\"]+)/", $all, $match);
          if(!empty($match))
          {    
            $ref = $match[1];
            $all = str_replace("\"$match[1]\"", "", $all);
          }
          preg_match("/\"(.[^\"]+)/", $all, $match);
          if(!empty($match))
          {
            $browser = $match[1];
            $all = str_replace("\"$match[1]\"", "", $all);
          }
          preg_match("/([0-9]+\b)/", $all, $match);
          if(!empty($match))
          {    
            $bytes = $match[1];
            $all = str_replace($match[1], "", $all);
          }
          //print("<br>IP: $ip<br>Access Time: $access_time<br>Page: $link[0]<br>Type: $link[1]<br>Success Code: $success_code<br>Bytes Transferred: $bytes<br>Referer: $ref <br>Browser: $browser<hr>");
          $ip_log_array[$i]['ip']= $ip;
          
          $ip_unique[$i] = 'ip: ' . $ip;///
          $ip_log_array[$i]['acces_time']= $access_time;
          $ip_unique[$i].= ': acces_time' . $access_time;
          $ip_log_array[$i]['page']= $link[0];
          $ip_unique[$i].= 'page ' .  $link[0];
          $ip_log_array[$i]['type']= !empty($link[1])?$link[1]:'';
          $ip_log_array[$i]['success_code']= $success_code;
          $ip_log_array[$i]['bytes_transferred']= $bytes;
          $ip_log_array[$i]['reference']= $ref;
          $ip_log_array[$i]['browser']= $browser;
          // advance to next record
          
//          if($ip== '202.125.145.98')
//          {
//              echo '<br/>';
//              echo 'ip:   ' . $ip . ' => Access Time: ' . $access_time;
//              echo ' => Page:   ' . $link[0] . ' => Type: ' . !empty($link[1])?$link[1]:'';
//              echo ' => success_code:   ' . $success_code . ' => bytes_transferred: ' . $bytes;
//              echo ' => reference:   ' . $ref . ' => browser: ' . $browser;
//          }   
          
          $i = $i + 2;
          $each_rec++;
        }
        
        //exit;
        //$new_arr = array_unique($ip_unique, SORT_REGULAR);
        
        $vals = array_count_values($ip_unique);
        $unique_ip_attack = array();
        foreach($vals as $key => $vals)
        {
            if($vals>=8)
            {
                $ip_address = explode(":",$key);
                $unique_ip_= $ip_address[1];
                $unique_ip_= explode(".",$unique_ip_);               
                $unique_ip_attack[]= substr($unique_ip_[0], -3). '.' . $unique_ip_[1] . '.' . $unique_ip_[2] . '.' . $unique_ip_[3];
             }
        }    
        $new_arr = array_unique($unique_ip_attack, SORT_REGULAR);
        
        foreach($new_arr as $blocklist)
        {
            $check_ip_exist = Helpers_Utilities::checkblockIPforever($blocklist);
            if($check_ip_exist==1)
            {
                //echo ' already exist : ' . $blocklist;
                echo '';
            }else {
               // echo ' not exist exist : ' . $blocklist;
                $check_ip_exist = Helpers_Utilities::addblockIPapache($blocklist);
                
            }
        }    */
       // echo '<pre>';
       // print_r($new_arr);        
       // exit;
        
    }
    
}

?>
