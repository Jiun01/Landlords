<?php 
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

$searchoption=$searchquery=$error="";
$errorcount1=$errorcount2=$errorcount="0";
if(isset($_GET['search'])){

    if(empty($_GET['searchoption'])){
        $errorcount1=1;
    }else{
        $searchoption = $_GET['searchoption'];
    }

    if(empty($_GET['searchquery'])){
        $errorcount2=2;
    }else{
        $searchquery = $_GET['searchquery'];
    }

    $sumerror = $errorcount2+$errorcount1;

    if($sumerror==1){
        $error="Please Select an option at the dropdown box on the left";
    }elseif($sumerror==2){
        $error="Please enter something in order to search";
    }elseif($sumerror==3){
        $error="You have to select search type and enter data to search";
    }
  
    if(!empty($error)){
		//errors in the form
	}else{
		//form is valid
		//protects database
		$searchquery= mysqli_real_escape_string($conn,$_GET['searchquery']);
        $ownerid= mysqli_real_escape_string($conn,$_SESSION["ownerid"]);
		
        if($_GET['searchoption']=="tenant"){
            $sql="SELECT * , p.id AS propertyID
            FROM properties p
            LEFT JOIN rentals r ON p.rentalID = r.id
            LEFT JOIN tenants t ON r.tenantID = t.id
            WHERE t.name LIKE '%$searchquery%'
            AND p.ownerID ='$ownerid'
            AND t.isDel='0'";
            $result=mysqli_query($conn,$sql);
            $searchresults=mysqli_fetch_all($result,MYSQLI_ASSOC);
            mysqli_free_result($result);
        
        }elseif($_GET['searchoption']=="property"){
            $sql="SELECT * ,p.id AS propertyID
            FROM properties p
            LEFT JOIN rentals r ON p.rentalID = r.id
            LEFT JOIN tenants t ON r.tenantID = t.id
            WHERE p.propertyName LIKE '%$searchquery%'
            AND p.ownerID ='$ownerid'
            AND t.isDel='0'";
            $result=mysqli_query($conn,$sql);
            $searchresults=mysqli_fetch_all($result,MYSQLI_ASSOC);
            mysqli_free_result($result);
        }
	}
}

?>

<?php include('template/header.php') ?>
<!--display dashboard-->
<?php if(isset($_SESSION["ownerid"])){ ?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
    <div class="container-fluid">
        <div class="input-group">
            <i>
                <select class="form-select" name="searchoption">
                    <option value="" <?php if(empty($searchoption)) { echo ' selected="selected"'; } ?>>Select Search Type</option>
                    <option value="tenant" <?php if ($searchoption == "tenant") { echo ' selected="selected"'; } ?>>Tenant's Name</option>
                    <option value="property" <?php if ($searchoption == "property") { echo ' selected="selected"'; } ?>>Property's Name</option>
                </select>  
            </i> 
			<input type="text" class="form-control" name="searchquery" placeholder="Something..."value="<?php echo $searchquery ?>">
                <button name="search" class="btn btn-outline-secondary">Search</button>
		</div>
        <div class="text-danger"><?php echo $error; ?></div>
    </div>
</form>

<?php if(!empty($searchresults)){?>
<div class="row mx-auto m-4"> 
    <?php foreach($searchresults as $searchresult):?>
        <div class="col-sm-12 col-md-6 col-lg-4 mb-4">                                                              
            <div class="card index-listings">                                                   
                <div class="card-header"><?php echo htmlspecialchars($searchresult['propertyName']); ?> (<?php echo htmlspecialchars($searchresult['propertyType']); ?>)</div>
                <div class="card-body row ">
                    <div class="col-lg-5">Address: </div><div class="col-lg-7"><?php echo htmlspecialchars($searchresult['address']); ?></div>
                    <div class="col-lg-5">Monthly Price: </div><div class="col-lg-7">RM<?php echo htmlspecialchars($searchresult['price']); ?></div>
                    <div class="col-lg-5">Last Patment Date: </div><div class="col-lg-7"><?php echo htmlspecialchars($searchresult['lastPaid']); ?></div>
                    <div class="col-lg-5">Tenant Name: </div><div class="col-lg-7"><?php echo htmlspecialchars($searchresult['name']); ?></div>
                    <div class="col-lg-5">Tenant Contact: </div><div class="col-lg-7"><?php echo htmlspecialchars($searchresult['contact']); ?></div>
                    <div class="col-lg-5">Notes: </div><div class="col-lg-7"><?php echo htmlspecialchars($searchresult['notes']); ?></div>
                </div>
                    <div class="card-footer text muted d-flex justify-content-between">
                    <div>Created at: <?php echo htmlspecialchars($searchresult['dateCreated']); ?></div>
                    <div><a href="details.php?id=<?php echo $searchresult['propertyID']?>">More Info</a></div>
                </div>  
            </div>
        </div>
    <?php endforeach; }?>
</div>

<?php }else{ ?>
        <div class="container">
            <div class="text-center h1 text-danger">
            Log In To Access to more features
            </div>
        </div>
    <?php } ?>

<?php include('template/footer.php') ?>