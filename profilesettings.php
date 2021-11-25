<?php
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

$errors = array('name'=>'','contact'=>'','icNo'=>'','email'=>'','password'=>'','paymentCycle'=>'');

if(isset($_POST['edit'])){
    $edit='1';
}

if(isset($_SESSION["ownerid"])) { 
$id= mysqli_real_escape_string($conn,$_SESSION["ownerid"]);
$sql = "SELECT name,paymentCycle,contact,icNo,email FROM owners WHERE id=$id";
$result=mysqli_query($conn,$sql);
$owner = mysqli_fetch_assoc($result);


if(isset($_POST['submit'])){

    if(empty($_POST['name'])){
		$errors['name']='Your name is required <br />';
	}else{
		$name= $_POST['name'];
		if(!preg_match('/^[a-zA-Z\s\d]+$/',$name)){
			$errors['name']='Your name must be alphabetical and spaces only.';
		}
	}

    if(empty($_POST['paymentCycle'])){
		$errors['paymentCycle']='Reccuring date is required <br />';
	}else{
		$paymentCycle= $_POST['paymentCycle'];
		if(!preg_match('/^[a-zA-Z\s\d]+$/',$paymentCycle)){
			$errors['paymentCycle']='Your name must be alphabetical and spaces only.';
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

    if(empty($_POST['icNo'])){
		$errors['icNo']='IC Number is required <br />';
	}else{
		$icNo = $_POST['icNo'];
		if(!preg_match('/^([0-9]{12,12})$/',$icNo)){
			$errors['icNo']='IC Number can only be in numbers and must be 12 characters.';
		}
	}

	if(array_filter($errors)){
		//errors in the form
        print_r($errors);
	}else{
		//form is valid
        $id=mysqli_real_escape_string($conn, $_SESSION["ownerid"]);
        $name= mysqli_real_escape_string($conn,$_POST['name']);
        $contact = mysqli_real_escape_string($conn,$_POST['contact']);
        $icNo = mysqli_real_escape_string($conn,$_POST['icNo']);
        $paymentCycle=mysqli_real_escape_string( $conn,$_POST['paymentCycle']);
        $sql = "UPDATE owners SET name='$name',paymentCycle='$paymentCycle',contact='$contact',icNo='$icNo' WHERE id=$id";

        //save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
            unset($edit);
            header('Refresh:0; url=profilesettings.php');   
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}
    }
}
}
?>

<?php include('template/header.php') ?>
<!-- ifnoownerid login page -->
<div class="card m-5 p-5 bg-linen">
    <h1 class="card-title">Profile and Settings</h1>
        <div class="card-body">
           <?php if(isset($edit)){ ?> 
                <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">

                    <div class="mb-3">
                        <label for="name" class="form-label"><strong>Name</strong></label>
                        <input type="text" class="form-control" name="name" value="<?php echo $owner['name'] ?>">
                        <div class="text-danger"><?php echo $errors['name']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="paymentCycle" class="form-label"><strong>Reccuring Monthly Rent Timing</strong></label>
                            <select class="form-select required" name="paymentCycle" >
		        				<option value="start" <?php if ($owner['paymentCycle'] == "start") { echo ' selected="selected"'; } ?>>1st of every month</option>
		        				<option value="half" <?php if ($owner['paymentCycle'] == "half") { echo ' selected="selected"'; } ?>>15th of every month</option>
		        				<option value="end" <?php if ($owner['paymentCycle'] == "end") { echo ' selected="selected"'; } ?>>30th of every month</option>
		        			</select>
                        <div class="text-danger"><?php echo $errors['paymentCycle']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label"><strong>Contact Number</strong></label>
                        <input type="text" class="form-control" name="contact" value="<?php echo $owner['contact'] ?>">
                        <div class="text-danger"><?php echo $errors['contact']; ?></div>
                    </div>

                    <div class="mb-3">
                        <label for="icNo" class="form-label"><strong>Ic Number</strong></label>
                        <input type="text" class="form-control" name="icNo" value="<?php echo $owner['icNo'] ?>">
                        <div class="text-danger"><?php echo $errors['icNo']; ?></div>
                    </div>

                    <div>
                        <label for="staticEmail" class="form-label"><strong>Email</strong></label>
                        <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="<?php echo $owner['email'] ?>">
                    </div>

                    <div class="mb-5">
                        <a class="text-danger" href="passwordchange.php">Change Password</a>
                     </div>

                    <div class="text-center mt-3">
                        <button type="submit" name="submit" class="btn btn-success">Save Details and Setting</button>
                     </div>
                </form>  

            <?php }else{ ?> 

                <div class="mb-3">
                        <label for="name" class="form-label"><strong>Name</strong></label>
                        <input type="text" class="form-control-plaintext" name="name" value="<?php echo $owner['name'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="paymentCycle" class="form-label"><strong>Reccuring Monthly Rent Timing</strong></label>
                            <div>
		        				<?php if ($owner['paymentCycle'] == "start") { echo ' Start of every month'; } ?>
		        				<?php if ($owner['paymentCycle'] == "half") { echo ' 15th of every month'; } ?>
		        				<?php if ($owner['paymentCycle'] == "end") { echo ' End of every month'; } ?>
                                </div>
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label"><strong>Contact Number</strong></label>
                        <input type="text" class="form-control-plaintext" name="contact" value="<?php echo $owner['contact'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="icNo" class="form-label"><strong>Ic Number</strong></label>
                        <input type="text" readonly class="form-control-plaintext" name="icNo" value="<?php echo $owner['icNo'] ?>">
                    </div>

                    <div class="mb-3">
                        <label for="staticEmail" class="form-label"><strong>Email</strong></label>
                        <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="<?php echo $owner['email'] ?>">
                    </div>

                    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                        <div class="text-center mb-3">
                            <button type="submit" name="edit" class="btn btn-primary">Edit Details and Setting</button>
                        </div>
                    </form>
            <?php } ?>
        </div>
</div>
<?php include('template/footer.php') ?>
</html>