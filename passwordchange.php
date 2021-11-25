<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

$errors = array('oldpword'=>'','newpword'=>'');
$success='';
if(isset($_POST['submit'])){

if(empty($_POST['oldpword'])){
    $errors['oldpword']='Password is required <br />';
    }else{
        $plain = mysqli_real_escape_string($conn,$_POST["oldpword"]);
        $ownerid= mysqli_real_escape_string($conn,$_SESSION["ownerid"]);
    
        $sql ="SELECT * FROM owners WHERE id=$ownerid";
        $result = mysqli_query($conn, $sql);
        $owners = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if(!empty($owners)){
            $hash = $owners['password'];          
                if(!password_verify($plain,$hash)){
                    $errors['oldpword']='Wrong Password';
                }
            }
        }
    

if(empty($_POST['newpword'])){
    $errors['newpword']='Password is required';
}else{
    $newpword = $_POST['newpword'];
    if(!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',$newpword)){
        $errors['newpword']='Password must be, Atleast 8 Characters,1 Lowercase, 1 Uppercase letter and 1 number';
    }
}
    
if(array_filter($errors)){
    //errors in the form
    }else{
    //form is valid
    //createsql
    $ownerid= mysqli_real_escape_string($conn,$_SESSION["ownerid"]);
    $hashedPwd= password_hash($_POST["newpword"], PASSWORD_DEFAULT);
    $sql="UPDATE owners SET password='$hashedPwd' WHERE id='$ownerid'";
    if(mysqli_query($conn,$sql)){
        $success="Password change completed, Redirecting you in 3 seconds...";
        header('Refresh:2; url=profilesettings.php'); 
    }else{
        //error in form
		echo'query error: ' . mysqli_error($conn);
    }

    }
}
?>

<?php include('template/header.php') ?>

<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center ">
    <div class="rounded p-5 border border-dark">
    <h2 class="text-center mb-5">Change Password</h2>    
        <div>    
        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
            <div class="mb-3">
                <label for="oldpword" class="form-label">Old Password</label>
                <input type="password" name="oldpword"class="form-control" id="oldpword">
            <div class="text-danger"><?php echo $errors['oldpword']; ?></div>
            </div>

            <div class="mb-3">
                <label for="newpword" class="form-label">New Password</label>
                <input type="password" class="form-control" name="newpword">
            <div class="text-danger"><?php echo $errors['newpword']; ?></div>
            </div>
            <div class="text-center pt-3">
                <input type="submit" value="Change Password" name="submit" class="btn btn-primary"></input>
            <div class="text-center text-success"><?php echo $success ?></div>
            </div>
        </form>
        </div> 
    </div>
</div>

<?php include('template/footer.php') ?>