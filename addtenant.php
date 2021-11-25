<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}
$tenantName=$tenantEmail=$tenantContact=$tenantGender='';
$errors = array('tenantName'=>'','tenantEmail'=>'','tenantContact'=>'','tenantGender'=>'');

if(isset($_GET['id'])){
    $id= mysqli_real_escape_string($conn, $_GET['id']);
    //create sql query
    $sql = "SELECT * FROM properties WHERE id=$id";
    //get query result
    $result=mysqli_query($conn,$sql);
    //get result and put inside an array
    $property = mysqli_fetch_assoc($result);
}

if(isset($_POST['submit'])){

	if(empty($_POST['tenantName'])){
		$errors['tenantName']="Tenant's name is required <br />";
	}else{
		$tenantName= $_POST['tenantName'];
		if(!preg_match('/^[a-zA-Z\s]+$/',$tenantName)){
			$errors['tenantName']="Tenant's name must be alphabetical and spaces only.";
		}
	}
	
    if(empty($_POST['tenantEmail'])){
        $errors['tenantEmail'] = "Tenant's email is required";
    } else{
        $tenantEmail = $_POST['tenantEmail'];
        if(!filter_var($tenantEmail, FILTER_VALIDATE_EMAIL)){
            $errors['tenantEmail'] = "Tenant's email must be a valid email address";
        }else{
			$tenantEmail = $_POST['tenantEmail'];
        }
    }
	
	if(empty($_POST['tenantContact'])){
		$errors['tenantContact']="Tenant's contact is required <br />";
	}else{
		$tenantContact = $_POST['tenantContact'];
		if(!preg_match('/^([0-9]{10,10})$/',$tenantContact)){
			$errors['tenantContact']="Tenant's contact can only be in numbers and typed in without area code";
		}
	}

	if(empty($_POST['tenantGender'])){
		$errors['tenantGender']='Must choose a gender for tenant';
	}else{
		$tenantGender = $_POST['tenantGender'];
		} //already limited choice from the selection tab  

	if(array_filter($errors)){
		//errors in the form
	}else{
		//form is valid
		//protects database
		$tenantName= mysqli_real_escape_string($conn,$_POST['tenantName']);
		$tenantEmail= mysqli_real_escape_string($conn,$_POST['tenantEmail']);
		$tenantContact= mysqli_real_escape_string($conn,str_pad($_POST['tenantContact'], 11, '6', STR_PAD_LEFT));
		$tenantGender= mysqli_real_escape_string($conn,$_POST['tenantGender']);
		$propertyid= mysqli_real_escape_string($conn,$_GET['id']);
		
		//createsql
		$sql ="INSERT INTO tenants(name,email,contact,gender)
		 VALUES ('$tenantName','$tenantEmail','$tenantContact','$tenantGender')";
		//save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
			//last insert work for a million async insert
			$lastTenantID = mysqli_insert_id($conn);
			$_SESSION['lastTenantID'] = $lastTenantID;
			$updateAvailability= "UPDATE properties SET availability='0' WHERE id=$propertyid";
			$results = mysqli_query($conn,$updateAvailability);
			header('Location:addrental.php?id='.$property['id']);
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}

	}
}
?>

<?php include('template/header.php') ?>
<?php if(isset($_SESSION["ownerid"]) AND $_SESSION["ownerid"]==$property['ownerID']){ ?>
<div class="container">
			<div class="text-center">
				<h1>Add Your Tenant</h1>
			</div>
    <div class="row justify-content-center my-5">
		<div class="col-lg-6">
			<form action="addtenant.php?id=<?php echo $property['id']?>" method="POST">

				<label for="tenantName" class="form-label h3">Name of your tenant<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="bi bi-person-circle"></i>
					</span>
				<input type="text" class="form-control" name="tenantName" placeholder="e.g. John Doe" value="<?php echo htmlspecialchars($tenantName) ?>">
				</div>
				<div class="text-danger mb-4"><?php echo $errors['tenantName']; ?></div>

				<label for="tenantEmail" class="form-label h3">Email of your tenant</label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="bi bi-envelope"></i>
					</span>
				<input type="text" class="form-control" name="tenantEmail" placeholder="e.g. John@gmail.com" value="<?php echo htmlspecialchars($tenantEmail) ?>">
				</div>
				<div class="text-danger mb-4"><?php echo $errors['tenantEmail']; ?></div>

				<label for="tenantContact" class="form-label h3">Contact Number of your tenant<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
				<div class="input-group">
					<span class="input-group-text">
						<i class="bi bi-phone"> +6</i>
					</span>
				<input type="text" class="form-control" name="tenantContact" placeholder="e.g. 0105384989" value="<?php echo htmlspecialchars($tenantContact) ?>">
				</div>
				<div class="text-danger mb-4"><?php echo $errors['tenantContact']; ?></div>

				<label for="tenantGender" class="form-label h3">Gender of your tenant<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-gender-ambiguous"></i>
							</span>
							<select class="form-select required" name="tenantGender">
								<option value="" selected>Open this menu to choose</option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
								<option value="Others">Others</option>
								<option value="Prefers not to say">Prefers not to say</option>
							</select>
						</div>
				<div class="text-danger mb-4"><?php echo $errors['tenantGender']; ?></div>
				
				<div class="mb-5 text-center">
					<button type="submit" name="submit" value="submit" class="btn btn-secondary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php }else{ ?>
	<div class="container">
		<div class="text-center m-5">
			<h1 class="text-danger">You do not own the privilege of this property, Please choose your properties from the homepage or the listing page. </h1>
		</div>
	</div>
<?php } ?>
	<?php include('template/footer.php') ?>