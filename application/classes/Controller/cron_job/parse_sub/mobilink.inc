<?php



//echo '<br>' . 'Mobilink' .'<br>';

$data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                        

  
 
//  if(!empty($login_user->id) && $login_user->id==138){
// 
//        echo '<pre>';
//        print_r($parse_val);
//        exit;
// }
 
 if($data['received_body'][0]=='na')
    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body_raw'])));                        

// if(!empty($login_user->id) && $login_user->id==138){
// 
//        echo '<pre>';
//        print_r($data['received_body']);
//        exit;
// }
 
 //if data not found
 include 'notfound.inc';
 
 
 if ($not_fount!=1){
     
         
 
  

 $data['received_body'] = str_replace('_','',$data['received_body'][0]);
 $data['received_body'] = preg_replace( "/\r|\n/", "", $data['received_body'] );

 $data['received_body'] = str_replace('*','',$data['received_body']);  
 $data['received_body'] = str_replace('=','',$data['received_body']); 
 $data['received_body'] = str_replace('P/O',' PO Box',$data['received_body']); 
 $data['received_body'] = str_replace(' / ',' ',$data['received_body']); 
 $data['received_body'] = str_replace('.','',$data['received_body']); 
 $data['received_body'] = str_replace('ID','',$data['received_body']);
 $data['received_body'] = str_replace(PHP_EOL,' ',$data['received_body']);
 //$parse_val = array_filter(explode(PHP_EOL,strip_tags($data['received_body'])));                        
 $parse_val = array_filter(explode(',',strip_tags($data['received_body'])));
 //$parse_val_2 = array_filter(explode('-',strip_tags($parse_val[0])));

 $parse_final = array();

 foreach ($parse_val as $key => $value) {                            
     if(!empty($parse_val[$key]) && strlen(trim($parse_val[$key]))>2){
         //echo strlen($parse_val[$key]);
         $parse_final[]= trim($parse_val[$key]);
     }
 } 
 $parse_val=  $parse_final;
   


  
/** Block Number */ 
$block_reason = preg_split('/[^a-zA-Z]/', $parse_val[2]);  
    

        
if (strpos(strtoupper($block_reason[0]),'UAO')!==false)
{
     $detail = implode(", ", $parse_val);     
    $block_phone = new Model_Userrequest;    
        $data['numbertype']= 1;
        $data['numbervalue']=trim($parse_val[1]);
        $data['blockreason']=strtoupper($block_reason[0]);
        $data['blockdetails']=$detail;
      //  $block_phone->add_blocked_number($data, 999);   
        $not_fount=1;
        $reference_number = $data['request_id'];
                 $reference_number = Model_Email::email_status($reference_number, 2, 5);
}else{ 
    
   
        
 $i = 0;       
 if (strpos(strtoupper($parse_val[$i]),'ACTIVE')!==false) { 
    $active = 'Active';
}else{    
 $active = trim($parse_val[$i]);
}

  
        
 $i +=1;
 $mobile_number=substr(trim($parse_val[$i]), -10);
 

        
 $i +=1;
 $name = trim($parse_val[$i]);

        
 $i +=1;
 $parse_val[$i] = str_replace('-','',$parse_val[$i]); 
 
  

        
 if(!ctype_digit(trim($parse_val[$i])) && strlen(trim($parse_val[$i]))==13)
 {     
     $cnic_original = trim($parse_val[$i]);     
     $cnic = trim($parse_val[$i]);     
     //$cnic = Helpers_Utilities::check_digit(trim($parse_val[$i]));
     $is_foreigner= 1;
     $i +=1;
 }else{
    if(ctype_digit(trim($parse_val[$i])) && strlen(trim($parse_val[$i]))==13)
    { 
        $cnic = trim($parse_val[$i]);
        $i +=1;
    }
    else{     
        $i +=1;     
        if(ctype_digit(trim($parse_val[$i])))
        {    
           $cnic = trim($parse_val[$i]);
           $i +=1;
        }
    }
 }
 

 if(empty($cnic))
 {
    
     foreach($parse_val as $cnicval)
     {
    
        if(!ctype_digit(trim($cnicval)) && strlen(trim($cnicval))==13)
          {     
              $cnic_original = trim($cnicval); 
              $cnic = trim($cnicval); 
              $is_foreigner= 1;              
              break;
          }elseif(ctype_digit(trim($cnicval)) && strlen(trim($cnicval))==13)
             { 
                 $cnic = trim($cnicval);
                 break;
             }   
     }  
 }    
 //echo ' cnic ' . $cnic_original;
 if (strpos($parse_val[$i], '/') !== false) {    
     
 }
 
 
 if (strpos($parse_val[$i], '/') !== false && strpos($parse_val[$i], ':') !== false) {
     
 list($d, $m, $y) = explode("/", $parse_val[$i]);
 $yy =explode(" ", $y);    
 $y=$yy[0];
 
    if(checkdate($m, $d, $y)){
        $date = trim($parse_val[$i]);
        $i +=1;
        
    } else {
        
        $status = trim($parse_val[$i]);
        $i +=1;
        $date = trim($parse_val[$i]);
        $i +=1;
    }
 }elseif ((strpos($parse_val[$i], '-') !== false) && (strpos($parse_val[$i], ':') !== false)) {
     
 list($d, $m, $y) = explode("-", $parse_val[$i]);
 $yy =explode(" ", $y);    
 $y=$yy[0];
// echo $y;
// exit;
    if(checkdate($m, $d, $y)){
        $date = trim($parse_val[$i]);
        $i +=1;
        
    } else {
        
        $status = trim($parse_val[$i]);
        $i +=1;
        $date = trim($parse_val[$i]);
        $i +=1;
    }
 }else{
     /*$status = trim($parse_val[$i]);
        $i +=1;
        $date = trim($parse_val[$i]);
        $i +=1;*/
     if(!empty($parse_val[$i]))
     $address = $parse_val[$i];
    if(!empty($parse_val[$i+1]))
        $address .= ' ' . $parse_val[$i+1];
     $i = $i + 1;
     
      
    
    if (strpos($parse_val[$i], '/') !== false) {
        
       $parse_val[$i] = str_replace("'",' ',$parse_val[$i]);        
      
        $strhlp = explode(" ", trim($parse_val[$i]));
         
      
        list($y, $m, $d) = explode("-", $strhlp[0]);
        $yy =explode(":", $y);     
          
  
        $y=$yy[0];       
          
        
           if(checkdate($m, $d, $y)){
               $date = trim($strhlp[1]);
//               $i +=1;

           }
           
           
       
       
  
        //   exit;
           /*else {
               echo 'fdf';
               $status = trim($parse_val[$i]);
               $i +=1;
               $date = trim($parse_val[$i]);
               $i +=1;
           }*/
        }
 }
 
    
 
 
 //if(empty($cnic))
     
}


 }
// echo '<pre>';
//    print_r($parse_val);
//    /*warid last lookup data */
//    foreach($parse_val as $valw)
//    {
//       // if(!empty($cnic)
//    }    
//    //echo $cnic;
//    echo '<br>';
//    echo $date;
//    echo '<br>';
//    echo $name;
//    exit;
 $status = (!empty($status) && $active=='Active')?1:0;

//             echo '<br>' ;    
//  print_r($strhlp);
//
//      echo '<br>' ; 
//  print_r($d);
//      echo '<br>' ; 
//  print_r($m);
//      echo '<br>' ; 
//  print_r($y);
//      echo '<br>' ; 
////  print_r($yy);
//      echo '<br>' ; 
//  print_r($date);
//  exit;
 ?>