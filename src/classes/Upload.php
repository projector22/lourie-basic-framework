<?php

/**
 * Handle the uploading of files
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class Upload {

    /**
     * The upload form, the <form></form> elements to upload a file
     * 
     * @param   string  $action     The page this form will execute to, e.g. 'actions.php'                        REQUIRED
     * @param   string  $name       The name element of the tag name='$name'                Set null to bypass    DEFAULT = null
     * @param   string  $id         The name element of the tag id='$id'                    Set null to bypass    DEFAULT = null
     * @param   string  $accept     The name element of the tag accept='$accept'            Set null to bypass    DEFAULT = null
     * @param   string  $class      The name element of the tag class='$class'              Set null to bypass    DEFAULT = null
     * 
     * @since   0.1 Pre-alpha
     */

    public function upload_form( $action, $name=null, $id=null, $accept=null, $class=null ){
        echo "\n<form method='post' action='$action' enctype='multipart/form-data'>";
        $this->upload_form_element( $name, $id, $class );
        echo "<input type='submit'>";
        echo "</form>";
    }

    /**
     * Upload form elements, the actual <input> tag
     * 
     * @param   string  $name       The name element of the tag name='$name'        Set null to bypass    DEFAULT = null
     * @param   string  $id         The name element of the tag id='$id'            Set null to bypass    DEFAULT = null
     * @param   string  $accept     The name element of the tag accept='$accept'    Set null to bypass    DEFAULT = null
     * @param   string  $class      The name element of the tag class='$class'      Set null to bypass    DEFAULT = null
     * 
     * @since   0.1 Pre-alpha
     */

    public function upload_form_element( $name=null, $id=null, $accept=null, $class=null ){
        $show_name = $show_id = $show_class = '';
        $file_form = "<input type='file'";
        
        if ( !is_null( $name ) ){
            $file_form .= " name='$name'";
        }
        if ( !is_null( $id ) ){
            $file_form .= " id='$id'";
        }
        if ( !is_null( $accept ) ){
            $file_form .= " accept='$accept'";
        }
        if ( !is_null( $class ) ){
            $file_form .= " class='$class'";
        }
        $file_form .= ">";
        echo "$file_form\n";
    }

    /**
     * Handle the file that is uploaded
     * 
     * @param   string  $name                   The input name, The name='' part of the <input> tag.                        REQUIRED
     * @param   array   $allowed_file_types     Types of allowed files allowed in an array. Leave blank to allow any file   DEFAULT = []
     * @param   boolean $file_exists_check      Checks if you require a check for if the file has already been uploaded     DEFAULT = False
     * 
     * @return                      If the file has no name defined, return
     * @return  boolean     True    If the file has uploaded successfully
     * @return  boolean     False   If the file upload has failed
     * 
     * @since   0.1 Pre-alpha
     */

    public function handle_upload( $name, $allowed_file_types=[], $file_exists_check=false ){
        if ( isset( $_FILES[$name] ) ){
            $uploaded_file = $_FILES[$name];
            if ( $uploaded_file['name'] == '' ){
                return;
            }
            
            $base_name = basename( $uploaded_file['name'] );
            $target_file = UPLOADS_PATH . $base_name;
            $tmp_name = $uploaded_file['tmp_name'];
            $file_size = $uploaded_file['size'];
            $file_type = strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) );
            $upload_check = $this->upload_check( $file_type, $file_size );
            
            if ( $this->upload_check( $file_size, $file_type, $allowed_file_types, $file_exists_check, $target_file ) ) {
                if ( move_uploaded_file( $tmp_name, $target_file ) ) {
                    return true;
                } else { 
                    return false;
                }//if else
            } else {
                return false;
            }//else
        } else {
            return;
        }
    }

    /**
     * Check the uploaded file to make sure it doesn't break any policies or requirements
     * 
     * @param   string  $file_size              The file size sent from $_FILES[$name]['size']                          REQUIRED
     * @param   string  $file_type_test         The type sent from $_FILES - eg. 'csv' or 'xlsx'                        DEFAULT = False
     * @param   array   $allowed_file_types     Array of file types allowed in the upload for example ['csv','xslc']    DEFAULT = Blank array
     * @param   boolean $file_exists_check      Whether or not to check for the file already uploaded                   DEFAULT = False
     * @param   string  $file_exists_file_path  The file to check if $file_exists_check == true                         DEFAULT = null
     * 
     * @return  boolean $upload_test            Return whether or not an upload is allowed - True = yes, False = no     DEFAULT = False
     * 
     * @since   0.1 Pre-alpha
     */

    private function upload_check( $file_size, $file_type_test=false, $allowed_file_types=[], $file_exists_check=false, $file_exists_file_path=null ) {
        $upload_test = true;
            
        // Check file size
        if ( $file_size > MAX_UPLOAD_SIZE ) {
            $upload_test = false;
        }//if
        
        // Allow certain file formats
        if ( $file_type_test != false ){
            $compare = false;
            foreach( $allowed_file_types as $allowed_type ){
                if ( $file_type_test == $allowed_type ) {
                    $compare = true;
                    break;
                }//if
            }//foreach
            if ( !$compare ){
                $upload_test = false;
            }//$compare == false
        }//$file_type_test != false

        //check if file exists
        if ( $file_exists_check && !is_null( $file_exists_file_path ) ){
            if ( file_exists( $file_exists_file_path ) ){
                $upload_test = false;
            }//if
        }//if

        return $upload_test;
    }

}

/*
Usage

$upload = new Upload;

$upload->upload_form( 'index.php', 'cheese' );

if ( isset( $_FILES['cheese'] ) ){
    if ( $upload->handle_upload( 'cheese', ['png','jpg'] ) ){
        echo "Upload successful";
    } else {
        echo "Upload failed";
    }
}
*/