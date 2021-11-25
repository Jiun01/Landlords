<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

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

        //rentalincome
        $sql = "SELECT * FROM rentalincomes where rentalID=$rentalid AND isDel='0' ORDER BY datePaid DESC";
        $result=mysqli_query($conn,$sql);
        $rentalincomes = mysqli_fetch_all($result,MYSQLI_ASSOC);
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
    $sql = "SELECT * FROM maintenances WHERE propertyID=$id AND isDel='0' AND status='0'";
    $result=mysqli_query($conn,$sql);
    $maintenances = mysqli_fetch_all($result,MYSQLI_ASSOC);
    mysqli_free_result($result);
}

if(isset($_POST['delete'])){
//delete
$sql = "UPDATE properties SET isDel=1 WHERE id=$id";
    if(mysqli_query($conn,$sql)){
        header('Location:index.php');
    }else{
        echo 'query error:' . mysqli_error($conn);
    }
}


//completed button trigger query
if(isset($_POST['checkbox'])){
    $array = $_POST['checkbox'];
    $listCheck = "'" . implode("','", $array) . "'";
    $statusupdate="UPDATE maintenances SET status=1 WHERE id IN $listCheck ";
    $status = mysqli_query ($conn,$statusupdate);
    header('Refresh: 0 url=details.php?id='.$property['id'].''); 
}

//payment history delete
    if(isset($_POST['checkboxpayment'])){
        $array = $_POST['checkboxpayment'];
        $listCheckPayment = "'" . implode("','", $array) . "'";
        $paymentDelete='UPDATE rentalincomes SET isDel=1 WHERE id IN('.$listCheckPayment.')';
        $paymentDeleteResult = mysqli_query ($conn,$paymentDelete);

        //update lastPaid Date
        $sql1 = "SELECT datePaid FROM rentalincomes where rentalID=$rentalid AND isDel='0' ORDER BY datePaid DESC LIMIT 1";
        $result = mysqli_query($conn,$sql1);
        $lastPaid = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        $updatedLastPaid = $lastPaid['datePaid'];
        $sql2="UPDATE rentals SET lastPaid='$updatedLastPaid' WHERE id=$rentalid";
        mysqli_query($conn,$sql2);
        header('Refresh:0 url=details.php?id='.$property['id'].''); 
    }

if(isset($_POST['markAsPaid'])){
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $today=date("Y-m-d"); 
    $rentalPrice = mysqli_real_escape_string($conn,$rental['price']);
    $sql="INSERT INTO rentalincomes(amount,rentalID)
    VALUES ('$rentalPrice','$rentalid')";
    mysqli_query($conn,$sql);
    $sql2="UPDATE rentals SET lastPaid='$today' WHERE id=$rentalid";
    mysqli_query($conn,$sql2);
    header('Refresh: 0 url=details.php?id='.$property['id'].''); 
}



?>  

<?php include('template/header.php') ?>
<div class="container-fluid">
<?php if(isset($_SESSION["ownerid"]) AND $_SESSION["ownerid"]==$property['ownerID']){ ?> <!--validate user -->
<div class="d-flex mt-3 mb-3">
    <div class="d-flex flex-grow-1 align-items-center justify-content-start ms-2">
        <h1><?php echo htmlspecialchars($property['propertyName']); ?></h1>
    </div>
    <div class="d-flex align-items-center justify-content-end  ms-2">
        <a class="btn btn-blue" href="editdetails.php?id=<?php echo $property['id']?>">Edit Details</a>   
    </div>
<?php if(empty($rental) && empty($tenant)){ ?>
    <div class="d-flex align-items-center justify-content-end ms-2">
        <a class="btn btn-blue" href="addtenant.php?id=<?php echo $property['id']?>">Add Tenant and Rental</a>
    </div>
<?php }else{ ?>
<div class="d-flex align-items-center justify-content-end ms-2 dropdown">
    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-whatsapp"></i> Send Tenant Whatsapp
    </a>    
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <li><a class="dropdown-item" href="https://wa.me/<?php echo $tenant['contact']?>?text=Hello%20<?php echo $tenant['name']?>%2C%20your%20last%20rental%20paid%20at%20<?php echo $rental['lastPaid']?>%20and%20the%20payment%20for%20this%20month%20is%20<?php echo $rental['price']?>%20." target="_blank">Rental Reminder</a></li>
        <li><a class="dropdown-item" href="https://wa.me/<?php echo $tenant['contact']?>?text=Hello%20<?php echo $tenant['name']?>%2C%20maintenance%20is%20completed%2C%20hope%20you%20have%20a%20great%20day%21" target="_blank">Maintenence Is Done</a></li>
    </ul>
</div>
    
<div class="d-flex align-items-center justify-content-end ms-2">
    <form action="details.php?id=<?php echo $property['id']?>" method="POST">
        <input type="submit" name="markAsPaid" value="Mark as Paid" class="btn btn-success text-end"> 
    </form>
</div>
<?php } ?>
</div>

<?php if($property){ ?>
    <div class="row ms-3 me-3 ">
        <div class="col-md-6 col-sm-12 ps-4 text-grey"><!-- display property data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Property Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Address: </div><div class="col-6"><?php echo htmlspecialchars($property['address']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">City: </div><div class="col-6"><?php echo htmlspecialchars($property['city']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Zipcode: </div><div class="col-6"><?php echo htmlspecialchars($property['zipcode']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Property Type: </div><div class="col-6"><?php echo htmlspecialchars($property['propertyType']); ?></div>
            </div>
            <div class="row pb-2">
                <div class="col-6">Notes: </div><div class="col-6"><?php echo htmlspecialchars($property['notes']); ?></div>
            </div>
        </div>    
<!--if statment to display tenant and rental info -->
<?php if(!empty($rental) && !empty($tenant)){ ?>
    <div class="col-md-6 col-sm-12 ps-4"><!-- display property data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Rental Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Deposit Paid: </div><div class="col-6"><?php echo htmlspecialchars($rental['deposit']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Rental Price: </div><div class="col-6"><?php echo htmlspecialchars($rental['price']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Last Paid Date: </div><div class="col-6"><?php echo htmlspecialchars($rental['lastPaid']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Starting Date: </div><div class="col-6"><?php echo htmlspecialchars($rental['startDate']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Ending Date: </div><div class="col-6"><?php echo htmlspecialchars($rental['endDate']); ?></div>
            </div>
        </div>
    </div>
    <div class="row ms-3 me-3">
        <div class="col-md-6 col-sm-12 ps-4 pb-4"><!-- display property data-->
            <div class="row">
                <div class="col-12 fw-bold p-2 fs-4 text-decoration-underline">Tenant Details: </div>
            </div>
            <div class="row">
                <div class="col-6">Tenant's name: </div><div class="col-6"><?php echo htmlspecialchars($tenant['name']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Tenant's Email: </div><div class="col-6"><?php echo htmlspecialchars($tenant['email']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Tenant's Contact Number: </div><div class="col-6"><?php echo htmlspecialchars($tenant['contact']); ?></div>
            </div>
            <div class="row">
                <div class="col-6">Tenant's Gender: </div><div class="col-6"><?php echo htmlspecialchars($tenant['gender']); ?></div>
            </div>
        </div>
<?php } ?>
<!-- if statement for maintenence -->
<?php if(!empty($maintenances)){ ?>
<div class="col-md-6 col-sm-12 ps-4 pb-4">

    <form name="status" action="details.php?id=<?php echo $property['id']?>" method="POST">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Remarks</th>
          <th scope="col">Deadline</th>
          <th scope="col">Date Created</th>
          <th scope="col">Delete</th>
        </tr>
      </thead>
        <tbody>
    <?php   
    foreach($maintenances as $maintenance):
    echo "<tr>";
    echo "<td>" . htmlspecialchars($maintenance['remark']) . "</td>";
    echo "<td>" . htmlspecialchars($maintenance['deadline']) . "</td>";
    echo "<td>" . htmlspecialchars($maintenance['dateCreated']) . "</td>";
    echo "<td><input type='checkbox' class='form-check-input' name='checkbox[]' value='".$maintenance["id"]."'></td></tr>";
    endforeach ?>
        </tbody>
    </table>
        <div class="text-end">
            <input class="btn btn-danger btn-sm" type="submit" name="statusupdate" value="Set checkbox to completed status"></input>
        </div>
    </form>  

</div>
<?php } ?>
</div>
<!-- Deletion form-->
<div class="d-flex align-items-center justify-content-center m-3">
    <a class="btn btn-blue me-3" href="addmaintenances.php?id=<?php echo $property['id']?>">Add Maintenence or Reminder</a>
    <form action="details.php?id=<?php echo $property['id']?>" method="POST">
        <input type="submit" name="delete" value="Delete Property" class="btn btn-danger text-end">
    </form>
</div>
<?php } 
}else{ ?> 
    <div class="text-center h1 text-danger">This Property does not belong to you</div>
<?php }
if(!empty($rentalincomes)){?>
<h3 class="mt-5">Payment History</h3>
<form name="form1" action="details.php?id=<?php echo $property['id']?>" method="POST">
<table class="table">
  <thead>
    <tr>
      <th scope="col">Amount</th>
      <th scope="col">Date Recorded</th>
      <th scope="col">Delete</th>
    </tr>
  </thead>
    <tbody>
<?php   
foreach($rentalincomes as $rentalincome):
echo "<tr>";
echo "<td>" . htmlspecialchars($rentalincome['amount']) . "</td>";
echo "<td>" . htmlspecialchars($rentalincome['datePaid']) . "</td>";
echo "<td><input type='checkbox' class='form-check-input' name='checkboxpayment[]' value='".$rentalincome['id']."'></td></tr>";
endforeach ?>
    </tbody>
</table>
<div class="text-end p-3 me-3 mb-5">
<input class="btn btn-danger btn-lg" type="submit" name="deletePaymentHistory" value="Delete Selected"></input>
</div>
</form>

<?php }?>
</div>

<?php include('template/footer.php') ?>
