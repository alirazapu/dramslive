<?php defined('SYSPATH') or die('No direct script access.');
 
class Controller_Download extends Controller_Working
{    
    public function action_index()
    {
        try{
        
        ///var/www/html/aies/application/classes/Controller/Personprofile.php download function
//        $id = $this->request->param('id');
//        echo $id;
//        $id = $this->request->param('id2');
//        echo $id;
//        exit;
        
        // We'll be outputting a PDF
      //  header('Content-type: application/pdf');
        // It will be called downloaded.pdf
      //  header('Content-Disposition: attachment; filename="downloaded.pdf"');
        // The PDF source is in original.pdf
      //  readfile('original.pdf');
        /*
        if (!empty($_POST['fid'])) {
                $file_link = !empty(base64_decode($_POST['fid'])) ? Helpers_Upload::get_request_data_path(base64_decode($_POST['fid'])) : '';
                 $target = $file_link;
//            } else {
//                $target = 'uploads/cdr/mail/';
            }*/
            
        
            $name = 'rqt61794fid30931.rar';
            $folders= '30001-35000';
            $nodeload = "/home/aiesfiles/requests-data/".$folders."/" . $name;
            //$nodeload = "/srv/bindings/".$_SERVER['USER']."/files/private/" . $name;
           /* if (file_exists($nodeload)) {
                $nodeload = "/srv/bindings/".$_SERVER['USER']."/files/" . $nodeload;
            }else{
                $nodeload = "/srv/bindings/".$_SERVER['USER']."/files/" . $nodeload;
            }*/
            $tmp = explode(".", $name);
            switch (strtolower($tmp[count($tmp) - 1])) {                
                case "exe": $ctype = "application/octet-stream";
                    break;
                case "zip": $ctype = "application/zip";
                    break;                
                case "rar": $ctype = "application/rar";
                    break;                
                case "csv":
                case "xls":
                case "xlsx": $ctype = "application/vnd.ms-excel";
                    break;
                
                default: $ctype = "application/force-download";
            }

            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Description: File Transfer");
            header("Content-Type: " . $ctype);
            header('Content-Disposition: attachment; filename="' . basename($name) . '"');
            //header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($nodeload));
            readfile($nodeload);
    } catch (Exception $e){
    
}
        
    }
}