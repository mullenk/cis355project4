<?php

class Customer { 
    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $mobile;
    public $fileName ;
    public $tempFileName ;
    public $fileSize ;
    public $fileType ;
    public $fileDescription ;
    public $fileLocation ;
    public $fileFullPath ;
    public $fileExists = false;
    public $content ;
    private $noerrors = true;
    private $nameError = null;
    private $emailError = null;
    private $mobileError = null;
    private $passwordError = null;
    private $title = "Customer";
    private $tableName = "customers4";
    
    function create_record() { // display "create" form
        $this->generate_html_top (1);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("password_hash", $this->passwordError, $this->password_hash);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_html_bottom (1);
    } // end function create_record()
    
    function join_record() {
        $this->generate_html_top (1);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("password_hash", $this->passwordError, $this->password_hash);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        echo"<div class='form-actions'>
            <button type='submit' class='btn btn-success'>Create</button>
            <a class='btn btn-secondary' href='login.php'>Back</a>
        </div>
        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
        
    }
    
    function display_upload_files() {
        $pdo = Database::connect();

        echo "<br>To see all uploaded files, visit: " 
            . "<a href='http://localhost/Prog04/uploads/'>Uploads</a>";
        echo '<br><br>All files in database...<br><br>';
                $sql = 'SELECT * FROM customers4 ' 
                    . 'ORDER BY BINARY filename ASC;';

        foreach ($pdo->query($sql) as $row) {
            $id = $row['id'];
            $sql = "SELECT * FROM customers4 where id=$id"; 
            echo $row['id'] . ' - ' . $row['filename'] 
                    . ' ' . $row['description'] . '<br>'
                . '<img width=100 src="data:image/jpeg;base64,'
                . base64_encode( $row['content'] ).'"/>'
                . '<br><br>';
        }
        echo '<br><br>';

        // disconnect
        Database::disconnect(); 
        echo "<br><a class='btn btn-secondary' href='$this->tableName.php'>Back</a>";
    }
    
    function upload_file($id) {
        $this->select_db_record($id);
        $funNext1 = "upload_1&id=" . $id;
        $funNext2 = "upload_2&id=" . $id;
        $funNext3 = "upload_3&id=" . $id;
        echo"<!DOCTYPE html>
        <html>
            <head>
                <title>Choose Upload Type</title>
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
       
            </head>
            <body>
                <h3>Choose Upload Type </h3>
                <div class='container'>
                    <p class='row'>
                        <a href='$this->tableName.php?fun=$funNext1' class='btn btn-success'>Upload File</a>
                        <a href='$this->tableName.php?fun=$funNext3' class='btn btn-warning'>Upload Personal Image</a>
                        <a href='$this->tableName.php?fun=$funNext2' class='btn btn-danger'>View All Uploads</a>
                        <a class='btn btn-secondary' href='$this->tableName.php'>Back</a>
                    </p>";
          
    }
    
    function display_upload1($id) {
        $funNext1 = "upload1&id=" . $id;
        $funNext4 = "display_upload_form&id=" . $id;
        echo "<html>
    
    <head>
        <title>Upload Files</title>" ;
        echo "
        <meta charset='UTF-8'>
        <meta name='viewport' 
              content='width=device-width, initial-scale=1.0'>
    </head>
    
    <body>
        
        <h1>(1) Upload a file to a server subdirectory</h1>
        <p>This form will perform a simple upload of any file, 
            as long as the file is smaller than 2MB. </p>
        <form method='post' action='$this->tableName.php?fun=$funNext1' 
              enctype='multipart/form-data'>
            <p>File</p>
            <input type='file' 
                name='Filename'> 
            <p>Description</p>
            <textarea rows='10' cols='35' 
                name='Description' disabled></textarea>
            <br/>
            <input TYPE='submit' name='upload' value='Submit'/>
        </form>
        <a class='btn btn-secondary' href='$this->tableName.php?fun=$funNext4'>Back</a>
        
    </body>
    
</html>";
    }
    
    function upload1($id){
        require_once ('functions01.php');

        // set PHP variables from data in HTML form 
        $fileName       = $_FILES['Filename']['name'];
        $tempFileName   = $_FILES['Filename']['tmp_name'];
        $fileSize       = $_FILES['Filename']['size'];
        $fileType       = $_FILES['Filename']['type'];
        // $fileDescription = $_POST['Description']; // not used
        

        // set server location (subdirectory) to store uploaded files
        $fileLocation = "uploads/";
        $fileFullPath = $fileLocation . $fileName; 
        if (!file_exists($fileLocation))
            mkdir ($fileLocation, 0777, true); // create subdirectory, if necessary


        // if file does not already exist, upload it
        if (!file_exists($fileFullPath)) {
            $result = move_uploaded_file($tempFileName, $fileFullPath);
            if ($result) {
                echo "File <b><i>" . $fileName 
                    . "</i></b> has been successfully uploaded.";
                // code below assumes filepath is same as filename of this file
                // minus the 12 characters of this file, "upload01.php"
                // plus the string, $fileLocation, i.e. "uploads/"
                 echo "<br>To see all uploaded files, visit: " 
                         . "<a href='"
                          . substr(get_current_url(), 0, -35)
                            . "$fileLocation'>" 
                           . substr(get_current_url(), 0, -35) 
                           . "$fileLocation</a>";
            } else {
                echo "Upload denied for file. " . $fileName 
                    . "</i></b>. Verify file size < 2MB. ";
               }
        }
        // otherwise, show error message
        else {
            echo "File <b><i>" . $fileName 
                . "</i></b> already exists. Please rename file.";
        }
        echo "<br><a class='btn btn-secondary' href='$this->tableName.php'>Back</a>";
            }
    
    function display_upload2($id){
        $funNext4 = "display_upload_form&id=" . $id;
        $pdo = Database::connect();

        echo "<br>To see all uploaded files, visit: " 
            . "<a href='http://localhost/Prog04/uploads/'>Uploads</a>";
        echo '<br><br>All files in database...<br><br>';
                $sql = 'SELECT * FROM customers4 ' 
                    . 'ORDER BY BINARY filename ASC;';

        foreach ($pdo->query($sql) as $row) {
            $id = $row['id'];
            $sql = "SELECT * FROM customers4 where id=$id"; 
            echo $row['id'] . ' - ' . $row['filename'] 
                    . ' ' . $row['description'] . '<br>'
                . '<img width=100 src="data:image/jpeg;base64,'
                . base64_encode( $row['content'] ).'"/>'
                . '<br><br>';
        }
        echo '<br><br>';

        // disconnect
        Database::disconnect(); 
        echo "<br><a class='btn btn-secondary' href='$this->tableName.php?fun=$funNext4'>Back</a>";
    }
    
            
    
    function display_upload3($id){
        $funNext4 = "display_upload_form&id=" . $id;
        $funNext3 = "upload3&id=" . $id;
        echo "<!DOCTYPE html>
<!--            ****************************************
File:           upload03.html
Description:    Uploads image file as BLOB in MySQL table. 
                See also: upload03.php.
SQL table name: upload03: 
                id (int), filename (varchar), filetype (varchar), 
                filesize (int), filecontents (blob), description (varchar)
Source:         Code modified from:
                http://codereview.stackexchange.com/questions/27796/php-upload-to-database
                **************************************** -->
<html>
    
    <head>
        <title>Upload Personal Image</title>
        <meta charset='UTF-8'>
        <meta name='viewport' 
              content='width=device-width, initial-scale=1.0'>
    </head>
    
    <body>
        
        <h1>(2 & 3) Upload an image file, and store as a personal image</h1>
        <p>This form will insert an image file (png/jpg/gif) 
            as a binary large object (BLOB), 
            as long as the file is smaller than 2MB. 
            Filename, file contents, and other file information
            will be stored in the MySQL table
            <br>As well the file will be added to a subdirectory
        </p>
        <form method='post' action='$this->tableName.php?fun=$funNext3' 
              onsubmit='return Validate(this);'
              enctype='multipart/form-data'>
            <p>File</p>
            <input type='file' required
                name='Filename'> 
            <p>Description</p>
            <textarea rows='10' cols='35' 
                name='Description'></textarea>
            <br/>
            <input TYPE='submit' name='upload' value='Submit'/>
        </form>
        <a class='btn btn-success' href='$this->tableName.php?fun=$funNext4'>Back</a>
        
        <script>
            var _validFileExtensions = ['.jpg', '.jpeg', '.gif', '.png'];    
            function Validate(oForm) {
                var arrInputs = oForm.getElementsByTagName('input');
                for (var i = 0; i < arrInputs.length; i++) {
                    var oInput = arrInputs[i];
                    if (oInput.type == 'file') {
                        var sFileName = oInput.value;
                        if (sFileName.length > 0) {
                            var blnValid = false;
                            for (var j = 0; j < _validFileExtensions.length; j++) {
                                var sCurExtension = _validFileExtensions[j];
                                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                                    blnValid = true;
                                    break;
                                }
                            }

                            if (!blnValid) {
                                alert('Sorry, ' + sFileName + ' is invalid, allowed extensions are: ' + _validFileExtensions.join(', '));
                                return false;
                            }
                        }
                    }
                }

                return true;
            }
        </script>
        
    </body>
    
</html>
";
    }
    
    function upload3($id){

        // set PHP variables from data in HTML form 
        $this->id = $id;
        $this->fileDescription = $_POST['Description']; 
        $this->fileName       = $_FILES['Filename']['name'];
        $this->tempFileName   = $_FILES['Filename']['tmp_name'];
        $this->fileSize       = $_FILES['Filename']['size'];
        $this->fileType       = $_FILES['Filename']['type'];

        // abort if no filename
        
         if (!$this->fileName) {
            die("No filename.");
        }

        // abort if file is not an image
        // never assume the upload succeeded
        if ($_FILES['Filename']['error'] !== UPLOAD_ERR_OK) {
            die("Upload failed with error code " . $_FILES['file']['error']);
        }
        $info = getimagesize($_FILES['Filename']['tmp_name']);
        if ($info === FALSE) {
            die("Error Unable to determine <i>image</i> type of uploaded file");
        }
        if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) 
        && ($info[2] !== IMAGETYPE_PNG)) {
           die("Not a gif/jpeg/png");
        }

        // abort if file is too big
        if($this->fileSize > 200000) { echo "Error: file exceeds 2MB."; exit(); }
       
        $link = mysqli_connect("localhost", "root", "", "projects");
        
        // fix slashes in $fileType variable, if necessary
       $this->fileType=(get_magic_quotes_gpc()==0 ? mysqli_real_escape_string($link,
       $_FILES['Filename']['type']) : mysqli_real_escape_string($link,
       stripslashes ($_FILES['Filename'])));
       
       mysqli_close($link);

        // put the content of the file into a variable, $content
        $fp      = fopen($this->tempFileName, 'r');
        $content = fread($fp, filesize($this->tempFileName));
        $this->content = addslashes($content);
        fclose($fp);

        if(!get_magic_quotes_gpc()) { $this->fileName = addslashes($this->fileName); }

        
        $fileLocation = "uploads/";
        $this->fileFullPath = $fileLocation . $this->fileName; 
        if (!file_exists($fileLocation))
            mkdir ($fileLocation); // create subdirectory, if necessary

        // connect to database
        $pdo = Database::connect();

        // exit, if requested file already exists -- in the database table 
        $fileExists = false;
        $sql = "SELECT filename FROM customers4 WHERE filename='$this->fileName'";
        foreach ($pdo->query($sql) as $row) {
            if ($row['filename'] == $this->fileName) {
                $fileExists = true;
            }
        }
        if ($fileExists) {
            echo "File <html><b><i>" . $this->fileName 
                . "</i></b></html> already exists in DB. Please rename file.";
            exit(); 
        }

        // exit, if requested file already exists -- in the subdirectory 
        if(file_exists($this->fileFullPath)) {
            echo "File <html><b><i>" . $this->fileName 
                . "</i></b></html> already exists in file system, "
                . "but not in database table. Cannot upload.";
            exit(); 
        }

        // if all of above is okay, then upload the file
        $result = move_uploaded_file($this->tempFileName, $this->fileFullPath);
        
        // if upload was successful, then add a record to the SQL database

        
       
        
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($result) {
            echo "Your file <html><b><i>" . $this->fileName 
                . "</i></b></html> has been successfully uploaded";

            $sql = "UPDATE $this->tableName  set filename = ?, filesize = ?,filetype = ?, description = ?, content = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->fileName,$this->fileSize,$this->fileType,$this->fileDescription,$content,$this->id));


        // otherwise, report error
        } else {
            echo "Upload denied for this file. Verify file size < 2MB. ";
        }


        // list all uploads in database 
        // ORDER BY BINARY filename ASC (sorts case-sensitive, like Linux)
        echo '<br><br>All files in database...<br><br>';
        $sql = 'SELECT * FROM customers4 ' 
            . 'ORDER BY BINARY filename ASC;';

        foreach ($pdo->query($sql) as $row) {
            $id = $row['id'];
            $sql = "SELECT * FROM customers4 where id=$id"; 
            echo $row['id'] . ' - ' . $row['filename'] 
                    . ' ' . $row['description'] . '<br>'
                . '<img width=100 src="data:image/jpeg;base64,'
                . base64_encode( $row['content'] ).'"/>'
                . '<br><br>';
        }
        echo '<br><br>';

        // disconnect
        Database::disconnect(); 
        echo "<br><a class='btn btn-secondary' href='$this->tableName.php'>Back</a>";
    }
    
    function read_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(2);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("password_hash", $this->passwordError, $this->password_hash, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(2);
    } // end function read_record()
    
    function update_record($id) { // display "update" form
        if($this->noerrors) $this->select_db_record($id);
        $this->generate_html_top(3, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus onfocus='this.select()'");
        $this->generate_form_group("email", $this->emailError, $this->email);
                $this->generate_form_group("password_hash", $this->passwordError, $this->password_hash);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_html_bottom(3);
    } // end function update_record()
    
    function delete_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(4, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("password_hash", $this->passwordError, $this->password_hash, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(4);
    } // end function delete_record()
    
    /*
     * This method inserts one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to Create form, with errors
     * - Input: user data from Create form
     * - Processing: INSERT (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     *   Note that $id will NOT be set because the record 
     *   will be a new record so the SQL database will "auto-number"
     * - Postcondition: New record is added to the database table, 
     *   and user is redirected to the List screen (if no errors), 
     *   or Create form (if errors)
     */
    function insert_db_record () {
        if ($this->fieldsAllValid () && $this->emailValid()) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO $this->tableName (name,email,password_hash,mobile) values(?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->name,$this->email,$this->password_hash,$this->mobile));
            Database::disconnect();
            header("Location: $this->tableName.php"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->create_record(); 
        }
    } // end function insert_db_record
    
    private function select_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM $this->tableName where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password_hash = $data['password_hash'];
        $this->mobile = $data['mobile'];
    } // function select_db_record()
    
    function update_db_record ($id) {
        $this->id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE $this->tableName  set name = ?, email = ?,password_hash = ?, mobile = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->name,$this->email,$this->password_hash,$this->mobile,$this->id));
            Database::disconnect();
            header("Location: $this->tableName.php");
        }
        else {
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
    } // end function update_db_record 
    
    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: $this->tableName.php");
    } // end function delete_db_record()
    
    private function generate_html_top ($fun, $id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create"; $funNext = "insert_db_record"; 
                break;
            case 2: // read
                $funWord = "Read"; $funNext = "none"; 
                break;
            case 3: // update
                $funWord = "Update"; $funNext = "update_db_record&id=" . $id; 
                break;
            case 4: // delete
                $funWord = "Delete"; $funNext = "delete_db_record&id=" . $id; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_top()"; 
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    "; 
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord a $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->tableName.php?fun=$funNext' method='post'>                        
                    ";
    } // end function generate_html_top()
    
    private function generate_html_bottom ($fun) {
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Create</button>"; 
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>"; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_bottom()"; 
                exit();
                break;
        }
        echo " 
                            <div class='form-actions'>
                                $funButton
                                <a class='btn btn-secondary' href='$this->tableName.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    } // end function generate_html_bottom()
    
    private function generate_form_group ($label, $labelError, $val, $modifier="") {
        echo "<div class='form-group'";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        //echo "<div class='controls'>";
        echo "<input "
            . "name='$label' "
            . "type='text' "
            . "$modifier "
            . "placeholder='$label' "
            . "value='";
        echo !empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'
        echo "</div>"; // end div: class='form-group'
    } // end function generate_form_group()
    
    private function emailValid() {
        $valid = true ;
        $pdo = Database::connect();
	$sql = "SELECT * FROM $this->tableName";
	foreach($pdo->query($sql) as $row) {
		if($this->email == $row['email']) {
			$this->emailError = 'Email has already been registered!';
			$valid = false;
		}
	}
	Database::disconnect();
        
        return $valid ;
        }
        
    private function fieldsAllValid () {
        $valid = true;
        if (empty($this->name)) {
            $this->nameError = 'Please enter Name';
            $valid = false;
        }
        if (empty($this->email)) {
            $this->emailError = 'Please enter Email Address';
            $valid = false;
        } 
        else if ( !filter_var($this->email,FILTER_VALIDATE_EMAIL) ) {
            $this->emailError = 'Please enter a valid email address: me@mydomain.com';
            $valid = false;
        }

        if (empty($this->mobile)) {
            $this->mobileError = 'Please enter Mobile phone number';
            $valid = false;
        }
        return $valid;
    } // end function fieldsAllValid() 
    
    function list_records() {
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$this->title" . "s" . "</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";  
        echo "
            </head>
            <body>
                <a href='https://github.com/mullenk/cis355project4' target='_blank'>Github</a><br />
                <a href='http://kmullen-cis255.000webhostapp.com/cis355/project2/sourcefiles2/diagrams.html' target='_blank'>Diagrams P2</a><br />
                <a href='http://kmullen-cis255.000webhostapp.com/cis355/project3/diagrams.html' target='_blank'>Diagrams P3</a><br />
                <a href='http://kmullen-cis255.000webhostapp.com/cis355/project4/diagrams.html' target='_blank'>Diagrams P4</a><br />
                <div class='container'>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                    </p>
                    <p>
                        <a href='$this->tableName.php?fun=display_create_form' class='btn btn-success'>Create</a>
                        <a href='$this->tableName.php?fun=display_uploads' class='btn btn-success'>View All Uploads</a>
                        <a href='logout.php' class='btn btn-success'>Logout</a>
                    </p>
                    
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM $this->tableName ORDER BY id DESC";
        foreach($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". '<img width=150 height=150 src="data:image/jpeg;base64,'
                . base64_encode( $row['content'] ).'"/>' . "</td>";
            echo "<td>". $row["name"] . "</td>";
            echo "<td>". $row["email"] . "</td>";
            echo "<td>". $row["password_hash"] . "</td>";
            echo "<td>". $row["mobile"] . "</td>";
            echo "<td width=350>";
            echo "<a class='btn btn-info' href='$this->tableName.php?fun=display_read_form&id=".$row["id"]."'>Read</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-warning' href='$this->tableName.php?fun=display_update_form&id=".$row["id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_delete_form&id=".$row["id"]."'>Delete</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-success' href='$this->tableName.php?fun=display_upload_form&id=".$row["id"]."'>Upload</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();        
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>

            </body>

        </html>
                    ";  
    } // end function list_records()
    
} // end class Customer

?>