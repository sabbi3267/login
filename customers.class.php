<?php

class Customer { 
    public $id;
    public $name;
    public $email;
    public $mobile;
    private $noerrors = true;
    private $nameError = null;
    private $emailError = null;
    private $mobileError = null;
    private $title = "Customer";
    private $tableName = "customers";
    public $password; 
    public $password_hashed; 
    
	/*
     * This method displays the create page form, 
     * - Input: click incedent
     * - Processing: process HTML code
     * - Output: HTML code for create page
     * - Pre-condition: If there is nothing in the list takes it to it this page automatically
     * - Post-conditon: After the input goes back to customers.php
     */
    
    function create_record() { // display "create" form
        $this->generate_html_top (1);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_form_group("password", $this->passwordError, $this->password, "", "password");
        $this->generate_html_bottom (1);
    } // end function create_record()
    
	/*
     * This method displays the read page form, 
     * - Input: click incedent
     * - Processing: process HTML code
     * - Output: HTML code for display page
     * - Pre-condition: If there is nothing in the list wouldnot show the button
     * - Post-conditon: The back button would take back to main page
     */
    function read_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(2);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(2);
    } // end function read_record()
    
	/*
     * This method displays the update page form, 
     * - Input: click incedent
     * - Processing: process HTML code
     * - Output: HTML code for update page
     * - Pre-condition: If there is nothing in the list takes it to it this page automatically
     * - Post-conditon: After the input goes back to customers.php (main page)
     */
    function update_record($id) { // display "update" form
        if($this->noerrors) $this->select_db_record($id);
        $this->generate_html_top(3, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus onfocus='this.select()'");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_html_bottom(3);
    } // end function update_record()
    
	/*
     * This method displays the delete page form, 
     * - Input: click incedent
     * - Processing: process HTML code
     * - Output: HTML code for delete page
     * - Pre-condition: If there is nothing in the list the button wouldnt showup
     * - Post-conditon: After the input goes back to customers.php
     */
    function delete_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(4, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
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
        if ($this->fieldsAllValid()) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->password_hashed = MD5($this->password);
			// safe code
            $sql = "INSERT INTO $this->tableName (name,email,mobile, password_hash) values(?, ?, ?, ?)";
			
            $q = $pdo->prepare($sql);
			// safe code
            $q->execute(array($this->name, $this->email, $this->mobile, $this->password_hashed));
			
            Database::disconnect();
            header("Location: $this->tableName.php"); 
        }
        else {
            
            $this->create_record(); 
        }
    } // end function insert_db_record
	
	/*
     * This method displays the selected input in to a form,  
     * - Input: click event
     * - Processing: select (SQL)
     * - Output:fills up the data fields in the form
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     * - Postcondition: Records are selected and inserted into the data fields of the page
     */ 
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
        $this->mobile = $data['mobile'];
    } // function select_db_record()
	
    /*
     * This method updates one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to update form, with errors
     * - Input: user data from update form
     * - Processing: update (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     * - Postcondition: Any changes made are updated into the database and sends back to main page
     */
    function update_db_record ($id) {
        $this->id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE $this->tableName  set name = ?, email = ?, mobile = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->name,$this->email,$this->mobile,$this->id));
            Database::disconnect();
            header("Location: $this->tableName.php");
        }
        else {
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
    } // end function update_db_record 
    
	/*
     * This method deletes one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to delete form, with errors
     * - Input: user data from Create form
     * - Processing: delete (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     * - Postcondition: The record is removed from the database table, 
     *   and user is redirected to the List screen (if no errors), 
     */
    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: $this->tableName.php");
    } // end function delete_db_record()
    
	/*
     * This method generates the buttons to act on a list,
     * - Input: opening the home page
     * - Processing: php
     * - Output: creates the button to redirect to pages
     * - Pre-condition: If there are data in the database would create a table with the list
     * - Post-conditon: displays all the lists in the database in the table and the buttons for CRUD
     */
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
    
	/*
     * This method inserts generated the button the create, update and delete forms, 
     * - Input: click event
     * - Processing: php
     * - Output: creats the buttons in the respective CRUD pages
     * - Pre-condition: If there are no data then would show up at the create page
     * - Post-conditon: displays the buttons for CRUD pages
     */
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
    
	/*
     * This method generates the label for the tables, 
     * - Input: opening the page
     * - Processing: php
     * - Output: creates the labes for the table
     * - Pre-condition: If there are data in the database would create a table labels
     * - Post-conditon: displays the table labels
     */
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
    
	/*
     * This method checks if all the fields are valid
     * - Input: name, email and mobile number
     * - Processing: php
     * - Output: checks if they are valid
     * - Pre-condition: Data type has to be same sathe type chosen for type checking
     * - Post-conditon: If the type is valid the redirect the funciton as valid
     */
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
    
	/*
     * This method list all the records there is in the database 
     * - Input: loading the page
     * - Processing: query (SQL)
     * - Output: displays all the records in the database in the table
     */
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
               <a button type='submit' class='btn btn-success'href='https://github.com/sabbi3267/crud00' target='_blank'>Github</a><br />
				<a button type='submit' class='btn btn-success'href='http://csis.svsu.edu/~nalam/cis355/crudoo/uml' target='_blank'>Uml</a><br />
				
				<a button type='submit' class='btn btn-success' href='http://csis.svsu.edu/~nalam/cis355/crudoo/ufg' target='_blank'>User Flow Diagram</a><br />
                <div class='container'>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                    </p>
                    <p>
                        <a href='$this->tableName.php?fun=display_create_form' class='btn btn-success'>Create</a>
                        <a href='logout.php' class='btn btn-warning'>Logout</a> 
                    </p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM $this->tableName ORDER BY id DESC";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". $row["name"] . "</td>";
            echo "<td>". $row["email"] . "</td>";
            echo "<td>". $row["mobile"] . "</td>";
            echo "<td width=250>";
            echo "<a class='btn btn-info' href='$this->tableName.php?fun=display_read_form&id=".$row["id"]."'>Read</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-warning' href='$this->tableName.php?fun=display_update_form&id=".$row["id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_delete_form&id=".$row["id"]."'>Delete</a>";
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
