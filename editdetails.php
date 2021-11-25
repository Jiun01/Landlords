<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

$errors = array('tenantName'=>'','tenantEmail'=>'','tenantContact'=>'','tenantGender'=>'','rentalDeposit'=>'','rentalPrice'=>'','startDate'=>'','endDate'=>'','propertyName'=>'','address'=>'','city'=>'','zipcode'=>'','propertyType'=>'','notes'=>'');
$saved = '';
//check get request id parameter
if(isset($_GET['id'])){
    //property info
    $id= mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM properties WHERE id=$id AND isDel=0";
    $result=mysqli_query($conn,$sql);
    $property = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    
    if(!empty($property['rentalID'])){
        //rental info
        $rentalid = mysqli_real_escape_string($conn,$property['rentalID']);
        $sql = "SELECT * FROM rentals WHERE id=$rentalid AND isDel='0'";
        $result=mysqli_query($conn,$sql);
        $rental = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        //tenant info
        $tenantid = mysqli_real_escape_string($conn,$rental['tenantID']);
        $sql = "SELECT * FROM tenants WHERE id=$tenantid AND isDel='0'";
        $result=mysqli_query($conn,$sql);
        $tenant = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }

    //maintenence info
    $id= mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM maintenances WHERE propertyID=$id AND isDel='0'";
    $result=mysqli_query($conn,$sql);
    $maintenances = mysqli_fetch_all($result,MYSQLI_ASSOC);
    mysqli_free_result($result);
    
}

//edit and save
if(isset($_POST['save'])){

    //tenant regex
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
		if(!preg_match('/^([0-9]{10,11})$/',$tenantContact)){
			$errors['tenantContact']="Tenant's contact can only be in numbers";
		}
	}

	if(empty($_POST['tenantGender'])){
		$errors['tenantGender']='Must choose a gender for tenant';
	}else{
		$tenantGender = $_POST['tenantGender'];
		} //already limited choice from the selection tab  

    //rental regex
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
      
    //property regex
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
		$tenantName = mysqli_real_escape_string($conn,$_POST['tenantName']);
		$tenantEmail = mysqli_real_escape_string($conn,$_POST['tenantEmail']);
		$tenantContact = mysqli_real_escape_string($conn,str_pad($_POST['tenantContact'], 11, '6', STR_PAD_LEFT));
		$tenantGender= mysqli_real_escape_string($conn,$_POST['tenantGender']);
        $tenantid = mysqli_real_escape_string($conn,$tenant['id']);

        $rentalDeposit = mysqli_real_escape_string($conn,$_POST['rentalDeposit']);
        $rentalPrice = mysqli_real_escape_string($conn,$_POST['rentalPrice']);
        $startDate = mysqli_real_escape_string($conn,$_POST['startDate']);
        $endDate = mysqli_real_escape_string($conn,$_POST['endDate']);
        $rentalid = mysqli_real_escape_string($conn,$rental['id']);

        $propertyName = mysqli_real_escape_string($conn,$_POST['propertyName']);
        $address = mysqli_real_escape_string($conn,$_POST['address']);
        $city = mysqli_real_escape_string($conn,$_POST['city']);
        $zipcode = mysqli_real_escape_string($conn,$_POST['zipcode']);
        $propertyType= mysqli_real_escape_string($conn,$_POST['propertyType']);
        $notes = mysqli_real_escape_string($conn,$_POST['notes']);
        $propertyid = mysqli_real_escape_string($conn,$property['id']);


		$tenantsql ="UPDATE tenants SET name='$tenantName',email = '$tenantEmail',contact='$tenantContact',gender='$tenantGender' WHERE id = $tenantid";
        $rentalsql ="UPDATE rentals SET deposit='$rentalDeposit',price='$rentalPrice',startDate='$startDate',endDate='$endDate' WHERE id=$rentalid";
        $propertysql ="UPDATE properties SET propertyName='$propertyName',address='$address',city='$city',zipcode='$zipcode',propertyType='$propertyType',notes='$notes' WHERE id=$propertyid";
		
        $tenantresult=mysqli_query($conn,$tenantsql);
        $rentalresult=mysqli_query($conn,$rentalsql);
        $propertyresult=mysqli_query($conn,$propertysql);

        //save to db and then checking
		if(mysqli_query($conn,$tenantsql) AND mysqli_query($conn,$rentalsql) AND mysqli_query($conn,$propertysql)){
			//no errors
            $saved = 'Details Successfully saved';
            header('Refresh:1; url=details.php?id='.$property['id'].'');   
		}else{
			//error in form
			echo'query error: ' . mysqli_error($conn);
		}
    }
}
?>


<?php include('template/header.php') ?>
<form action="editdetails.php?id=<?php echo $property['id']?>" method="POST">
<div class="container-fluid">
<?php if(isset($_SESSION["ownerid"]) AND $_SESSION["ownerid"]==$property['ownerID']){ ?> <!--validate user -->
<div class="row ms-3">
<div class="col-lg-3 col-md-6 col-sm-12">
    <input type="text" class="form-control form-control-lg" name="propertyName" value="<?php echo htmlspecialchars($property['propertyName']) ?>">
    <div class="text-danger"><?php echo $errors['propertyName']; ?></div>
</div> 
</div>
<?php if($property){ ?>
    <div class="row ms-3 me-3 ">
        <div class="col-md-6 col-sm-12 ps-4"><!-- display property data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Property Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Address: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($property['address']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['address']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">City: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($property['city']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['city']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">Zipcode: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="zipcode" value="<?php echo htmlspecialchars($property['zipcode']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['zipcode']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">Property Type: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="propertyType" value="<?php echo htmlspecialchars($property['propertyType']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['propertyType']; ?></div>
            </div>
            <div class="row pb-2">
                <div class="col-6">Notes: </div>
                    <div class="col-6">
                        <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($property['notes']) ?></textarea>
                    </div>
                <div class="text-danger"><?php echo $errors['notes']; ?></div>
            </div>
        </div>    
<!--if statment to display tenant and rental info -->
<?php if(!empty($rental) && !empty($tenant)){ ?>
    <div class="col-md-6 col-sm-12 ps-4"><!-- display rental data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Rental Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Deposit Paid: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="rentalDeposit" value="<?php echo htmlspecialchars(floatval($rental['deposit'])) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['rentalDeposit']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">Rental Price: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="rentalPrice" value="<?php echo htmlspecialchars(floatval($rental['price'])) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['rentalPrice']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">Last Paid Date: </div>
                    <div class="col-6">
                        <?php echo htmlspecialchars($rental['lastPaid']); ?>
                    </div>                   
            </div>
            <div class="row">
                <div class="col-6">Starting Date: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="startDate" value="<?php echo htmlspecialchars($rental['startDate']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['startDate']; ?></div>
            </div>
            <div class="row">
                <div class="col-6">Ending Date: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="endDate" value="<?php echo htmlspecialchars($rental['endDate']) ?>">
                    </div>
                <div class="text-danger"><?php echo $errors['endDate']; ?></div>
            </div>
        </div>
    </div>
    <div class="row ms-3 me-3">
        <div class="col-md-6 col-sm-12 ps-4 pb-4"><!-- display tenant data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Tenant Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Tenant's name: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="tenantName" value="<?php echo htmlspecialchars($tenant['name']) ?>"></div>
                    </div>
                <div class="text-danger"><?php echo $errors['tenantName']; ?></div>  
            <div class="row">
                <div class="col-6">Tenant's Email: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="tenantEmail" value="<?php echo htmlspecialchars($tenant['email']) ?>"></div>
                    </div>
                <div class="text-danger"><?php echo $errors['tenantEmail']; ?></div>
            <div class="row">
                <div class="col-6">Tenant's Contact Number: </div>
                    <div class="col-6">
                        <input type="text" class="form-control" name="tenantContact" value="<?php echo htmlspecialchars($tenant['contact']) ?>"></div>
                    </div>
                <div class="text-danger"><?php echo $errors['tenantContact']; ?></div>
            <div class="row">
                <div class="col-6">Tenant's Gender: </div>
                    <div class="col-6">
                        <select class="form-select required" name="tenantGender" >
							<option value="Male" <?php if ($tenant['gender'] == "Male") { echo ' selected="selected"'; } ?>>Male</option>
							<option value="Female" <?php if ($tenant['gender'] == "Female") { echo ' selected="selected"'; } ?>>Female</option>
							<option value="Others" <?php if ($tenant['gender'] == "Others") { echo ' selected="selected"'; } ?>>Others</option>
							<option value="Prefers not to say" <?php if ($tenant['gender'] == "Prefers not to say") { echo ' selected="selected"'; } ?>>Prefers not to say</option>
						</select>
                    </div>
                </div>
                <div class="text-danger"><?php echo $errors['tenantGender']; ?></div>
            </div>
<?php } ?>
<!-- if statement for maintenence -->
<?php if(!empty($maintenances)){ ?>
<div class="col-md-6 col-sm-12 ps-4 pb-4">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Remarks</th>
          <th scope="col">Deadline</th>
          <th scope="col">Date Created</th>
        </tr>
      </thead>
        <tbody>
    <?php   
    foreach($maintenances as $maintenance):
    echo "<tr>";
    echo "<td>" . htmlspecialchars($maintenance['remark']) . "</td>";
    echo "<td>" . htmlspecialchars($maintenance['deadline']) . "</td>";
    echo "<td>" . htmlspecialchars($maintenance['dateCreated']) . "</td>";
    endforeach ?>
        </tbody>
    </table>
</div>
<?php } ?>
</div>

<div class="d-flex align-items-center justify-content-center fs-2 text-success">
 <?php echo $saved; ?>
</div>
<!-- discard or save -->
<div class="d-flex align-items-center justify-content-center m-3">
    <a class="btn btn-danger me-3" href="details.php?id=<?php echo $property['id']?>">Discard</a>
        <input type="submit" name="save" value="Save" class="btn btn-success text-end">
    </form>
</div>

<?php } ?>

<?php }else{ ?> 
    <div class="text-center h1 text-danger">This Property does not belong to you</div>
<?php } ?>
</div>

<?php include('template/footer.php') ?>
