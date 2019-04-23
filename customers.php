<?php

// include the class that handles database connections
require "database.php";

// include the class containing functions/methods for "customer" table
// Note: this application uses "customer" table, not "cusotmers" table
require "customers.class.php";
$cust = new Customer();
 

if(isset($_GET["id"]))          $id = $_GET["id"]; 
if(isset($_POST["name"]))       $cust->name = $_POST["name"];
if(isset($_POST["email"]))      $cust->email = $_POST["email"];
if(isset($_POST["mobile"]))     $cust->mobile = $_POST["mobile"];
if(isset($_POST["password"]))     $cust->password = $_POST["password"];

if(isset($_GET["fun"])) {
	$fun = $_GET["fun"];
	if (!isset($_SESSION["user_id"])) { 
		if (!($fun == "display_create_form" || $fun == "check_login" || $fun == "insert_db_record")) { 
			$fun="display_login_view";
		}
	}
}
else $fun = "display_login_view"; 

switch ($fun) {
    case "display_list":        $cust->list_records();
        break;
    case "display_create_form": $cust->create_record(); 
        break;
    case "display_read_form":   $cust->read_record($id); 
        break;
    case "display_update_form": $cust->update_record($id);
        break;
    case "display_delete_form": $cust->delete_record($id); 
        break;
    case "insert_db_record":    $cust->insert_db_record(); 
        break;
    case "update_db_record":    $cust->update_db_record($id);
        break;
    case "delete_db_record":    $cust->delete_db_record($id);
        break;
    case "logout":				$cust->logout();
		     break;
	   case "check_login":			$cust->check_login();
		    break;
	   case "display_login_view":  $cust->login_view();
		      break;
    default: 
        echo "Error: Invalid function call (customer.php)";
        exit();
        break;
}

