<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}
$propertyName=$address=$city=$zipcode=$propertyType=$notes='';
$errors = array('propertyName'=>'','address'=>'','city'=>'','zipcode'=>'','propertyType'=>'','notes'=>'');

//form validation
if(isset($_POST['submit'])){

	if(empty($_POST['propertyName'])){
		$errors['propertyName']='Property Name is required <br />';
	}else{
		$propertyName= $_POST['propertyName'];
		if(!preg_match('/^[a-zA-Z\s\d]+$/',$propertyName)){
			$errors['propertyName']='Property name must be alphabetical and spaces only.';
		}
	}
	
	if(empty($_POST['address'])){
		$errors['address']='Address is required <br />';
	}else{
		$address = $_POST['address'];
		if(!preg_match('/^(.{1,200})$/',$address)){
			$errors['address']='Address can only be in alphanumeric, spaces ,commas and limited to 200 characters.';
		}
	}
	
	if(empty($_POST['city'])){
		$errors['city']='City name is required <br />';
	}else{
		$city = $_POST['city'];
		if(!preg_match('/^([a-zA-Z\s\d]{1,50})$/',$city)){
			$errors['city']='Address can only be in alphabets and limited to 50 characters.';
		}
	}

	if(empty($_POST['zipcode'])){
		$errors['zipcode']='Zipcode is required <br />';
	}else{
		$zipcode = $_POST['zipcode'];
		if(!preg_match('/^([0-9]{1,10})$/',$zipcode)){
			$errors['zipcode']='Zipcode can only be in numbers and limited to 10 characters.';
		}
	}
	
	if(empty($_POST['propertyType'])){
		$errors['propertyType']='Please choose a property Type.';
	}else{
		$propertyType = $_POST['propertyType'];
		} //already limited choice from the selection tab  

	if(!empty($_POST['notes'])){
		$notes = $_POST['notes'];
		if(!preg_match('/^(.{1,255})$/',$notes)){
			$errors['notes']='Notes are limited to 255 characters.';
		}
	} 
	if(array_filter($errors)){
		//errors in the form
	}else{
		//form is valid
		//protects database
		$propertyName= mysqli_real_escape_string($conn,$_POST['propertyName']);
		$address= mysqli_real_escape_string($conn,$_POST['address']);
		$city= mysqli_real_escape_string($conn,$_POST['city']);
		$zipcode= mysqli_real_escape_string($conn,$_POST['zipcode']);
		$propertyType= mysqli_real_escape_string($conn,$_POST['propertyType']);
		$notes= mysqli_real_escape_string($conn,$_POST['notes']);
		$ownerid= mysqli_real_escape_string($conn,$_SESSION["ownerid"]);
		//createsql
		$sql ="INSERT INTO properties(propertyName,address,city,zipcode,propertyType,notes,ownerID)
		 VALUES ('$propertyName','$address','$city','$zipcode','$propertyType','$notes','$ownerid')";
		//save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
			header('Location:index.php');
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}

	}
}


?>

<?php include('template/header.php') ?>
<div class="container">
			<div class="text-center">
				<h1>Add a new property</h1>
			</div>
      
    <div class="row justify-content-center my-5">

		<div class="col-lg-6">
				<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">

					<label for="propertyName" class="form-label h3">Name of your property<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
					<input type="text" class="form-control" name="propertyName" placeholder="e.g. Menara Tower" value="<?php echo htmlspecialchars($propertyName) ?>">
						<div class="text-danger mb-4"><?php echo $errors['propertyName']; ?></div>
					
					<label for="address" class="form-label h3">Address line 1<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="bi bi-pin-map-fill"></i>
						</span>
							<input type="text" class="form-control" name="address" placeholder="e.g New Lot,Block 11,Tabuan Height Commercial Centre Jalan Song"
							value="<?php echo htmlspecialchars($address) ?>">
						</div>
					<div class="text-danger"><?php echo $errors['address']; ?></div>
					<p class=" mb-4">200 Characters and below</p>

					<label for="city" class="form-label h3">City<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
					<input type="text" class="form-control" name="city" placeholder="e.g. Kuala Lumpur" value="<?php echo htmlspecialchars($city) ?>">
					<div class="text-danger"><?php echo $errors['city']; ?></div>
					<p class="mb-2">50 Characters and below</p>

					<label for="zipcode" class="form-label h3">Zipcode<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
					<input type="text" class="form-control" name="zipcode" placeholder="e.g. 53000" value="<?php echo htmlspecialchars($zipcode) ?>">
					<div class="text-danger mb-4"><?php echo $errors['zipcode']; ?></div>

					<label for="propertyType" class="form-label h3">Property Type<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
					<div class="mb-4 input-group">
						<span class="input-group-text">
						<i class="bi bi-building"></i>
						</span>
							<select class="form-select required" name="propertyType">
								<option value="" selected>Open this menu to choose</option>
								<option value="Landed Property">Landed Property</option>
								<option value="Apartment">Apartment</option>
								<option value="Single Room">Single Room</option>
								<option value="Parking">Parking</option>
							</select>
					</div>
					<div class="text-danger"><?php echo $errors['propertyType']; ?></div>
					
					<div class="form-floating mb-4 mt-4">
						<textarea class="form-control" name="notes" rows="3"  ><?php echo htmlspecialchars($notes) ?></textarea>
						<label for="notes" class="h5">Notes</label>
						<div class="text-danger"><?php echo $errors['notes']; ?></div>
						<p>255 Characters and below</p>
					</div>

					<div class="mb-5 text-center">
						<button type="submit" name="submit" value="submit" class="btn btn-secondary">Submit</button>
					</div>
					<?php if(!isset($_SESSION["ownerid"])){ ?>
						<div class="mb-5 text-center text-danger">
							Data will be all lost since you are not logged in. Log In in order to save the property.
						</div>	
					<?php } ?>

				</form>
		</div>
	</div>
</div>

	<?php include('template/footer.php') ?>

