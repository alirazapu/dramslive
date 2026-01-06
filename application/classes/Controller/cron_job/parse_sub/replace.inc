<?php

/* telenor */
 $data['received_body'] = str_replace('Note: Kindly send the numbers collectively, instead of individual requests.','',$data['received_body']);
 $data['received_body'] = str_replace('-','',$data['received_body']);

 /*ufone */         
 $data['received_body'] = str_replace('Please see the given detail.','',$data['received_body']);        
 $data['received_body'] = str_replace('Please find the detail','',$data['received_body']);        
 $data['received_body'] = str_replace('Please see the below details.','',$data['received_body']);        
 $data['received_body'] = str_replace('please','',$data['received_body']);        
 $data['received_body'] = str_replace('Please','',$data['received_body']);   
 $data['received_body'] = str_replace('see the below detail','',$data['received_body']);   
 $data['received_body'] = str_replace('Pease','',$data['received_body']);   
 $data['received_body'] = str_replace('Plesae','',$data['received_body']);   
 $data['received_body'] = str_replace('From','',$data['received_body']);   
 $data['received_body'] = str_replace('Plese','',$data['received_body']);   
 $data['received_body'] = str_replace('Check','',$data['received_body']);   
 $data['received_body'] = str_replace('/passport','',$data['received_body']);   
 $data['received_body'] = str_replace('find the below data','',$data['received_body']);   
 $data['received_body'] = str_replace('Original message','',$data['received_body']);
 $data['received_body'] = str_replace('Please see the given detail.','',$data['received_body']); 
 $data['received_body'] = str_replace('Pleaseseethedetails.','',$data['received_body']); 
 $data['received_body'] = str_replace('–Pleaseseethebelowdetails.','',$data['received_body']); 
 $data['received_body'] = str_replace('Pleaseseethebelowdetail.','',$data['received_body']); 
 $data['received_body'] = str_replace('Pleaseseethebelowdetails.','',$data['received_body']); 
 $data['received_body'] = str_replace('[Akbar]','',$data['received_body']); 
 $data['received_body'] = str_replace('see the given detail','',$data['received_body']); 

 

 ?>