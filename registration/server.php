<?php
session_start();

// initializing variables
$username = "";
$name = "";
$phn_no = "";
$email    = "";
$password = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'registration');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $phn_no = mysqli_real_escape_string($db, $_POST['phn_no']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
	if (empty($name)) 
	{
	  array_push($errors, "Name is required"); 
	}
	
	if (empty($username)) 
	{
	  array_push($errors, "Username is required"); 
	}
	
	if (empty($phn_no)) 
	{
	  array_push($errors, "Phone Number is required"); 
	}

	if (empty($email)) 
	{
		array_push($errors, "Email is required"); 
	}
	
	if (empty($password_1)) 
	{
		array_push($errors, "Password is required"); 
	}

	if ($password_1 != $password_2) 
	{
		array_push($errors, "The two passwords do not match");
	}

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) 
  { // if user exists
	if ($user['username'] === $username) 
	{
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) 
	{
      array_push($errors, "email already exists");
    }
	
	if ($user['phn_no'] === $phn_no) 
	{
      array_push($errors, "Phone Number already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) 
  {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO users (username, name, email, password, phn_no) 
  			  VALUES('$username', '$name', '$email', '$password', '$phn_no')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

if (isset($_POST['login']))
{
	$username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);

	if (empty($username)) 
	{
	  array_push($errors, "Username is required"); 
	}
	
	if (empty($password)) 
	{
		array_push($errors, "Password is required"); 
	}
	
	if (count($errors) == 0) 
	{
		$password = md5($password);
		$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
		$results = mysqli_query($db, $query);
		if (mysqli_num_rows($results) == 1) 
		{
			$_SESSION['username'] = $username;
			$_SESSION['success'] = "You are now logged in";
			header('location: index.php');
		}
		else
			{
				array_push($errors, "Wrong username/password combination");
			}
	}

	if (isset($_GET['logout']))
	{
		session_destroy();
		unset($_SESSION['username']);
		header('location:login.php');
	}
}
?>