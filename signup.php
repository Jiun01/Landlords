<?php 
include ('config/db_connect.php');
$name=$contact=$icno=$email=$password='';
$errors = array('name'=>'','contact'=>'','icno'=>'','email'=>'','password'=>'');
$signup='';

if(isset($_POST['submit'])){


    if(empty($_POST['name'])){
		$errors['name']='Your name is required <br />';
	}else{
		$name= $_POST['name'];
		if(!preg_match('/^[a-zA-Z\s\d]+$/',$name)){
			$errors['name']='Your name must be alphabetical and spaces only.';
		}
	}

	if(empty($_POST['contact'])){
		$errors['contact']='Contact is required <br />';
	}else{
		$contact = $_POST['contact'];
		if(!preg_match('/^([0-9]{10,11})$/',$contact)){
			$errors['contact']='Contact can only be in numbers and typed in without area code';
		}
	}

    if(empty($_POST['icno'])){
		$errors['icno']='IC Number is required <br />';
	}else{
		$icno = $_POST['icno'];
		if(!preg_match('/^([0-9]{12,12})$/',$icno)){
			$errors['icno']='IC Number can only be in numbers and must be 12 characters.';
		}
	}

    if(empty($_POST['email'])){
        $errors['email'] = 'An email is required';
    } else{
        $email = $_POST['email'];
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors['email'] = 'Email must be a valid email address';
        }else{
            $sql='SELECT * FROM owners WHERE email="'.$email.'"';
            $result = mysqli_query($conn,$sql);
            if(mysqli_fetch_assoc($result)){
                $errors['email'] = 'Email already Used';
            }
        }
    }

    if(empty($_POST['password'])){
		$errors['password']='Password is required is required <br />';
	}else{
		$password = $_POST['password'];
		if(!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',$password)){
			$errors['password']='Password must be, Atleast 8 Characters,1 Lowercase, 1 Uppercase letter and 1 number';
		}
	}

	if(array_filter($errors)){
		//errors in the form
   	}else{
		//form is valid
		//protects database
        $name=mysqli_real_escape_string($conn,$_POST['name']);
        $contact=mysqli_real_escape_string($conn,$_POST['contact']);
        $icno=mysqli_real_escape_string($conn,$_POST['icno']);
        $email=mysqli_real_escape_string($conn,$_POST['email']);
        $password=mysqli_real_escape_string($conn,$_POST['password']);
        $hashedPwd= password_hash($password, PASSWORD_DEFAULT);

		//createsql
		$sql ="INSERT INTO owners(name,contact,icNo,email,password)
		 VALUES ('$name','$contact','$icno','$email','$hashedPwd')";
		//save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
            $signup='Successfully Signed Up Redirecting To Login Page in 3 Seconds...';  
            mysqli_free_result($result);
            mysqli_close($conn);
            header("Refresh:3; url=SignIn.php");    
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}

	}

}

?>

<?php include ('template/header.php') ?>

<div class="container-sm text-center">
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name) ?>">
        <div class="text-danger"><?php echo $errors['name']; ?></div>
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact Number</label>
        <input type="text" class="form-control" name="contact" value="<?php echo htmlspecialchars($contact) ?>">
        <div class="text-danger"><?php echo $errors['contact']; ?></div>
    </div>
    <div class="mb-3">
        <label for="icno" class="form-label">Ic Number</label>
        <input type="text" class="form-control" name="icno" value="<?php echo htmlspecialchars($icno) ?>">
        <div class="text-danger"><?php echo $errors['icno']; ?></div>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($email) ?>">
        <div class="text-danger"><?php echo $errors['email']; ?></div>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password">
        <div class="text-danger"><?php echo $errors['password']; ?></div>
    </div>
<button type="submit" name="submit" class="btn btn-primary">Sign Up</button>
</form>
</div>
<div class="text-success text-center h2"><?php echo $signup ?></div>

<?php include('template/footer.php') ?>