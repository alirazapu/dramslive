<?php

abstract class Helpers_Escapstr{
     
        public static function mres($value)
        {
            //include 'gmail/mdsecurecon.inc';
            include  getcwd() . DIRECTORY_SEPARATOR . 'application/mdsecurecon.inc';
            
            $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
            $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
            $value=  str_replace($search, $replace, $value);
            
            $search = array("union","select","insert","cast","drop","delete",".php","declare","drop","benchmark","show","database","create","char","convert","alter","declare","order","script","benchmark","encode","LoginAttempts");
            $replace = array("","","","","","","","","","","","","","","","","","","","","","","","","","","");
            $value=  str_replace($search, $replace, $value);
            
            $value = mysqli_real_escape_string($con, $value); 
            return $value;
        }


}

?>
