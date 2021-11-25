<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}
$remark=$deadline='';
$errors = array('remark'=>'','deadline'=>'');

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
	
    if(empty($_POST['remark'])){
        $errors['remark'] = "Maintenence or Reminder cannot be empty";
    }else{
        $remark = $_POST['remark'];
        if(!preg_match('/^([0-9a-zA-Z\s]{1,255})$/',$remark)){
			$errors['remark']="Remarks are limited to alphabets, numbers and spaces only";
		}
    }

	if(empty($_POST['deadline'])){
		$errors['deadline']="Ending date is required";
	}else{
        $deadline=$_POST['deadline'];
    }

	if(array_filter($errors)){
		//errors in the form
	}else{
		//form is valid
		//protects database
        $propertyid = mysqli_real_escape_string($conn,$property['id']);
		$remark = mysqli_real_escape_string($conn,$_POST['remark']);
		$deadline = mysqli_real_escape_string($conn,$_POST['deadline']);
		//createsql
		$sql ="INSERT INTO maintenances(propertyID,remark,deadline)
		 VALUES ('$propertyid','$remark','$deadline')";
		//save to db and then checking
		if(mysqli_query($conn,$sql)){
			//no errors
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

<div class="container">
	<div class="text-center">
		<h1>Add a Maintenence or Reminder note.</h1>
	</div>
<div class="row justify-content-center my-5">
		<form action="addmaintenances.php?id=<?php echo $property['id']?>" method="POST">
			
            <label for="remark" class="form-label h3">Maintenence or Reminder:</label>
			<div class="input-group">
				<span class="input-group-text"></span>
			<input type="text" class="form-control" name="remark" placeholder="e.g. Plumbing Leak / Return Safety Deposit" value="<?php echo htmlspecialchars($remark) ?>">
			</div>
				<div class="text-danger mb-4"><?php echo $errors['remark']; ?></div>

			<label for="deadline" class="form-label h3">Deadline of this Maintenence or Reminder<abbr title="This field is mandatory" aria-label="required">*</abbr></label>
			    <div class="input-group">
				    <span class="input-group-text">
					    <i class="bi bi-person-circle"></i>
				    </span>
			<input type="date" class="form-control" name="deadline" value="<?php echo htmlspecialchars($startDate) ?>">
			    </div>
			<div class="text-danger mb-4"><?php echo $errors['deadline']; ?></div>

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
			<h1 class="text-danger">You do not own the privilege of this property, Please choose your properties from the homepage or the listing page. </h1>
		</div>
	</div>
<?php } ?>
	<?php include('template/footer.php') ?>