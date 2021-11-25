<?php 
session_start();
include ('config/db_connect.php');
$errors = array('logind'=>'','password'=>'','loginsuccess'=>'','loginfail'=>'');

if(isset($_POST['submit'])){
$logind=mysqli_real_escape_string($conn,$_POST['logind']);
$password=mysqli_real_escape_string($conn,$_POST['password']);
$plain = $_POST["password"];

if(empty($_POST['logind'])){
	$errors['logind']='Email address or Contact Number is required <br />';
}

if(empty($_POST['password'])){
$errors['password']='Password is required <br />';
}

if(array_filter($errors)){
    //errors in the form
    }else{
    //form is valid
    //createsql
    $sql ='SELECT * FROM owners WHERE email="'.$logind.'" OR contact="'.$logind.'"';
    $result = mysqli_query($conn, $sql);
    $owners = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if(!empty($owners)){
        $hash = $owners['password'];          
            if(password_verify($plain,$hash)){
                $_SESSION["ownerid"] = $owners["id"];
                $_SESSION["displayname"] = $owners["name"];
                $errors['loginsuccess']='You are logged in, Redirecting you to the front page in 3 Seconds...';
                header("Refresh:3; url=index.php");  
            }else{
                $errors['loginfail']='Wrong Password';
            }
        }else{
            $errors['loginfail']='Wrong Login Details';  
        }
    }
}
?>

<?php include ('template/header.php') ?>

<div class="container-sm text-center">
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
    <div class="mb-3">
        <label for="logind" name="logind" class="form-label">Email address/Contact Number</label>
        <input type="text" name="logind"class="form-control" id="logind">
        <div class="text-danger"><?php echo $errors['logind']; ?></div>
    </div>
    <div class="mb-3">
        <label for="password" name="password"class="form-label">Password</label>
        <input type="password" name="password"class="form-control" id="password">
        <div class="text-danger"><?php echo $errors['password']; ?></div>
    </div>
<button type="submit" name="submit" class="btn btn-primary">Log In</button>
</form>
</div>
<div class="text-danger text-center h2"><?php echo $errors['loginfail'] ?></div>
<div class="text-success text-center h2"><?php echo $errors['loginsuccess'] ?></div>


<?php include('template/footer.php') ?>



