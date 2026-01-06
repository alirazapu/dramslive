
<?php defined('SYSPATH') or die('No direct script access.');
 
/**
 * Model file class
 *
 * @author     Novichkov Sergey(Radik) <novichkovsergey@yandex.ru>
 * @copyright  Copyrights (c) 2012 Novichkov Sergey
 *
 * @property   integer    $id
 * @property   string     $file
 * @property   string     $type
 * @property   integer    $size
 * @property   string     $description
 */
class Model_File extends ORM {
 
    /**
     * Table columns
     *
     * Field name => Label
     *
     * @var array
     */
    protected  $_table_columns = array(
        'id'            => 'id',
        'file'          => 'file',
        'type'          => 'type',
        'size'          => 'size',
        'description'   => 'description',
        'company_name'   => 'company_name',
        'request_type'   => 'request_type',
        'created_by'   => 'created_by',
        'created_on'   => 'created_on',
        'is_deleted'   => 'is_deleted',
        'is_manual' => 'is_manual',
        'phone_number' => 'phone_number',
        'imei' => 'imei',        
    );
 
    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
        return array(
            'file'        => 'File',
            'description' => 'Description',
        );
    }
 
    /**
     * Filter definitions for validation
     *
     * @return array
     */
    public function filters()
    {
        return array(
            TRUE => NULL,
            'description', array(
                array('trim'),
            ),
        );
    }
 
    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'file' => array(
                array('Upload::valid'),
                array('Upload::not_empty'),
                array('Upload::type', array(':value', array('xlsx', 'xls', 'csv'))),
                array(array($this, 'file_save'), array(':value'))
            ),
        );
    }
 
    /**
     * Uploads directory
     *
     * @return string
     */
    private function uploads_dir($file_folder_name)
    {
        //return DOCROOT . 'uploads' . DIRECTORY_SEPARATOR;
        //return DOCROOT . 'uploads\cdr' . DIRECTORY_SEPARATOR . $file_folder_name . DIRECTORY_SEPARATOR;
        //return DOCROOT . 'uploads/cdr/' . $file_folder_name . '/';
        return $file_folder_name;
    }
 
    /**
     * Upload file in upload directory and setup valid filename
     *
     * @param array $file
     *
     * @return boolean
     */
    public function file_save($file)
    {
        
        // upload file
        //$uploaded = Upload::save($file, $file['name'], $this->uploads_dir());
        $uploaded = Upload::save($file, $file['file_new_name'], $this->uploads_dir($file['file_folder_name']));
 
        // if uploaded set file name to save to database
        if ($uploaded)
        {
            // set file name
            //$this->set('file', $file['name']);
            $this->set('file', $file['file_new_name']);
 
            // set file type
            //$this->set('type', strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)));
            $this->set('type', strtolower(pathinfo($file['file_new_name'], PATHINFO_EXTENSION)));
 
            // set file size
            $this->set('size', $file['size']);
        }
 
        // return result
        return $uploaded;
    }
 
} // end Model File class