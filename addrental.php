<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}
$rentalDeposit=$rentalPrice=$startDate=$endDate='';
$errors = array('rentalDeposit'=>'','rentalPrice'=>'','startDate'=>'','endDate'=>'');

if(isset($_GET['id'])){
    $id= mysqli_real_escape_string($conn, $_GET['id']);
    //create sql query
    $sql = "SELECT * FROM properties WHERE id=$id";
    //get query result
    $result=mysqli_query($conn,$sql);
    //get result and put inside an array
    $property = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
}

if(isset($_POST['submit'])){

	if(empty($_POST['rentalDeposit'])){
		$errors['rentalDeposit']="Deposit is required <br />";
	}else{
		$rentalDeposit= $_POST['rentalDeposit'];
		if(!preg_match('/^([0-9]{1,10})$/',$rentalDeposit)){
			$errors['rentalDeposit']="Deposit can only be in number and not over 10 characters";
		}
	}
	
    if(empty($_POST['rentalPrice'])){
        $errors['rentalPrice'] = "Price of rental is required";
    } else{
        $rentalPrice = $_POST['rentalPrice'];
        if(!preg_match('/^([0-9]{1,10})$/',$rentalPrice)){
			$errors['rentalPrice']="Price of rental can only be in numbers and not over 10 characters";
		}
    }
	
	if(empty($_POST['startDate'])){
		$errors['startDate']="Starting date is required <br />";
	}else{
		$startDate = $_POST['startDate'];
	}

	if(empty($_POST['endDate'])){
		$errors['endDate']="Ending date is required <br />";
	}else{
		$endDate = $_POST['endDate'];
		if($endDate<$startDate){
			$errors['endDate']="Ending date cannot be earlier than starting date";
		}
	}

	if(array_filter($errors)){
		//errors in the form
	}else{
		//form is valid
		//protects database
		$rentalDeposit = mysqli_real_escape_string($conn,$_POST['rentalDeposit']);
		$rentalPrice = mysqli_real_escape_string($conn,$_POST['rentalPrice']);
		$startDate = mysqli_real_escape_string($conn,$_POST['startDate']);
		$endDate = mysqli_real_escape_string($conn,$_POST['endDate']);
		$lastTenantID = mysqli_real_escape_string($conn,$_SESSION['lastTenantID']);
		//createsql
		$sql ="INSERT INTO rentals(tenantID,deposit,price,startDate,endDate)
		 VALUES ('$lastTenantID','$rentalDeposit','$rentalPrice','$startDate','$endDate')";
		//save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
			//last insert work for a million async insert
			$lastRentalID = mysqli_insert_id($conn);
			$sql = 'UPDATE properties SET rentalID='.$lastRentalID.' WHERE id="'.$property['id'].'"';
			mysqli_query($conn,$sql);
			header('Location:details.php?id='.$property['id']);
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}

	}
}

?>

<?php include('template/header.php')?>
<?php if(isset($_SESSION["ownerid"]) AND $_SESSION["ownerid"]==$property['ownerID']){ ?>
<?php if(isset($_SESSION['lastTenantID'])){?>

<div class="container">
	<div class="text-center">
		<h1>Add Your Tenant</h1>
	</div>
<div class="row justify-content-center my-5">
	<div class="col-lg-6">
		<form action="addrental.php?id=<?php echo $property['id']?>" method="POST">
			<label for="rentalDeposit" class="form-label h3">Rental Deposit Paid</label>
			<div class="input-group">
				<span class="input-group-text">RM</span>
			<input type="text" class="form-control" name="rentalDeposit" placeholder="e.g. 500" value="<?php echo htmlspecialchars($rentalDeposit) ?>">
			</div>
				<div class="text-danger mb-4"><?php echo $errors['rentalDeposit']; ?></div>

			<label for="rentalPrice" class="form-label h3">Price of rental<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
			<div class="input-group">
				<span class="input-group-text">RM</span>
			<input type="text" class="form-control" name="rentalPrice" placeholder="e.g. 600" value="<?php echo htmlspecialchars($rentalPrice) ?>">
			</div>
				<div class="text-danger mb-4"><?php echo $errors['rentalPrice']; ?></div>		

		<div class="row">
			<div class="col-6">
				<label for="startDate" class="form-label h3">Starting date of rental<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
				<div class="input-group">
				<input type="date" class="form-control" name="startDate" value="<?php echo htmlspecialchars($startDate) ?>">
				</div>
				<div class="text-danger mb-4"><?php echo $errors['startDate']; ?></div>
			</div>

			<div class="col-6">
				<label for="endDate" class="form-label h3">Ending date of rental<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
				<div class="input-group">
				<input type="date" class="form-control" name="endDate" value="<?php echo htmlspecialchars($endDate) ?>">
				</div>
					<div class="text-danger mb-4"><?php echo $errors['endDate']; ?></div>
			</div>
		</div>
			<div class="mb-5 text-center">
				<button type="submit" name="submit" value="submit" class="btn btn-secondary">Submit</button>
			</div>
		</form>
	</div>
</div>
</div>
<?php	}else{	?>
	<div class="container">
		<div class="text-center m-5">
			<h1 class="text-danger">There was an error getting Tenant ID, Please return to home page and re-enter tenant credentials. </h1>
		</div>
	</div>
<?php	}		?>
<?php	}else{	?>
	<div class="container">
		<div class="text-center m-5">
			<h1 class="text-danger">You do not own the privilege of this property, Please choose your properties from the homepage or the listing page. </h1>
		</div>
	</div>
<?php } ?>
	<?php include('template/footer.php') ?>