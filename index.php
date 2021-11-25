<?php
include('config/db_connect.php');
if(!isset($_SESSION)){
	session_start();
}

if(isset($_SESSION["ownerid"])) {    
    $ownerid=$_SESSION["ownerid"]; 

    //shows all properties
    $sql ="SELECT *, IFNULL(r.price,0) AS price,p.id AS propertyID
    FROM properties p
    LEFT JOIN rentals r ON p.rentalID = r.id
    LEFT JOIN tenants t ON r.tenantID = t.id
    WHERE p.ownerID ='$ownerid'
    AND p.isDel='0'";
    $results = mysqli_query($conn, $sql);
    $properties = mysqli_fetch_all($results, MYSQLI_ASSOC);
    mysqli_free_result($results);

    //Predicted Rental Income
    $sql=
    "SELECT r.price , IFNULL(r.price,0) AS price
    FROM properties p
    LEFT JOIN rentals r ON p.rentalID = r.id
    WHERE p.ownerID ='$ownerid'";
    $results = mysqli_query($conn, $sql);
    $pri = mysqli_fetch_all($results, MYSQLI_ASSOC);
    $pri = array_sum(array_column($pri,'price'));
    mysqli_free_result($results);
    //get payment cycle and create date range

    $sql ="SELECT paymentCycle FROM owners WHERE id=$ownerid";
    $results = mysqli_query($conn, $sql);
    $reccurdate = mysqli_fetch_assoc($results);
    mysqli_free_result($results);
    $reccurdate= $reccurdate['paymentCycle'];
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $today = new DateTime('now');
    $today = $today->format('d');

    switch ($reccurdate) {
        case "start":
            $day = "1";
            $month = date("m"); 
            $year = date("Y");
        break;
        case "half":
            $day = "15";
            $month = date("m"); 
            $year = date("Y");
        break;
        case "end":
            $day = date("t");
            $month = date("m"); 
            $year = date("Y");
        break;
    }
    switch ($reccurdate) {
        case "start":
            $reccurdatestart = new DateTime( "$year-$month-$day");
            $reccurdatestart = $reccurdatestart->format('Y-m-d');
            $monthend = $month+1; 
            $reccurdateend = new DateTime( "$year-$monthend-$day");
            $reccurdateend = $reccurdateend->format('Y-m-d');
          break;
        case "half":
          if($today < 15){
            $reccurdateend = new DateTime( "$year-$month-$day");
            $reccurdateend = $reccurdateend->format('Y-m-d');
            $monthstart = $month-1; 
            $reccurdatestart = new DateTime( "$year-$monthstart-$day");
            $reccurdatestart = $reccurdateend->format('Y-m-d');
            echo $reccurdateend;
          }else{
            $reccurdatestart = new DateTime( "$year-$month-$day");
            $reccurdatestart = $reccurdatestart->format('Y-m-d');
            $monthend = $month+1; 
            $reccurdateend = new DateTime( "$year-$monthend-$day");
            $reccurdateend = $reccurdateend->format('Y-m-d');        
          }
          break;
        case "end":
            $reccurdateend = new DateTime( "$year-$month-$day");
            $reccurdateend = $reccurdateend->format('Y-m-d');
            $monthstart = $month-1; 
            $reccurdatestart = new DateTime( "$year-$monthstart-$day");
            $reccurdatestart = $reccurdatestart->format('Y-m-d');
          break;
      }
    //Curent Collected Total
    $sql="SELECT IFNULL(ri.amount,0) AS amount
    FROM rentalincomes ri
    LEFT JOIN rentals r ON ri.rentalID = r.id
    LEFT JOIN properties p ON p.rentalID = r.id
    WHERE (ri.datePaid BETWEEN '$reccurdatestart' AND '$reccurdateend')
    AND p.ownerID ='$ownerid' AND ri.isDel='0'";
    $results = mysqli_query($conn, $sql);
    $cct = mysqli_fetch_all($results, MYSQLI_ASSOC);
    $cct = array_sum(array_column($cct,'amount'));
    mysqli_free_result($results);
    
    //Total Properties Recorded
    $sql="SELECT id
    FROM properties
    WHERE ownerID='$ownerid' AND isDel='0'";
    $results = mysqli_query($conn, $sql);
    $tpr = mysqli_fetch_all($results, MYSQLI_ASSOC);
    $tpr = count($tpr);
    mysqli_free_result($results);

    //Total Unrented Property
    $sql="SELECT id
    FROM properties
    WHERE ownerID=$ownerid AND availability=1 AND isDel=0";
    $results = mysqli_query($conn, $sql);
    $tup = mysqli_fetch_all($results, MYSQLI_ASSOC);
    $tup = count($tup);
    mysqli_free_result($results);

    //Uncollected Rental
    $sql="SELECT p.propertyName ,r.lastPaid ,p.id
    FROM rentals r
    LEFT JOIN properties p ON p.rentalID = r.id
    WHERE (r.lastPaid NOT BETWEEN '$reccurdatestart' AND '$reccurdateend')
    AND p.ownerID ='$ownerid' AND r.isDel='0'";
    $results = mysqli_query($conn, $sql);
    $urs = mysqli_fetch_all($results, MYSQLI_ASSOC);
    mysqli_free_result($results);

    $dlt = new DateTime('now');
    $dlt = $dlt->format('Y-m-d');
    $dl7 = new DateTime('now');
    $dl7->modify('+7 days');
    $dl7 = $dl7->format('Y-m-d');

    //Reminders Nearing Deadline
    $sql="SELECT p.id AS propertyID,p.propertyName, m.remark, m.deadline
    FROM maintenances m
    LEFT JOIN properties p ON m.propertyID = p.id
    WHERE (m.deadline BETWEEN '$dlt' AND '$dl7')
    AND p.ownerID ='$ownerid' AND m.status='0' AND m.isDel='0'
    ORDER BY deadline ASC";
    $results = mysqli_query($conn, $sql);
    $rnds = mysqli_fetch_all($results, MYSQLI_ASSOC);
    mysqli_free_result($results);

    //Reminders Past Deadline
    $sql="SELECT p.id AS propertyID,p.propertyName, m.remark, m.deadline
    FROM maintenances m
    LEFT JOIN properties p ON m.propertyID = p.id
    WHERE m.deadline < '$dlt'
    AND p.ownerID ='$ownerid' AND m.status='0' AND m.isDel='0'
    ORDER BY deadline ASC";
    $results = mysqli_query($conn, $sql);
    $rpds = mysqli_fetch_all($results, MYSQLI_ASSOC);
    mysqli_free_result($results);


} 
?>


<?php include('template/header.php') ?>
<!--display dashboard-->
<?php if(isset($_SESSION["ownerid"])){ ?>

<div class="container-fluid">
	<div class="row h-100 "> 
		<div class="col-xl-3 col-lg-12">
			<div class="row g-3 mb-3 h-100 ">
                <div class="col-6">
                    <div class="card card-block d-flex dashboard">
                        <div class="card-body">
                            <h5 class="card-title text-center">Predicted Monthly Income</h5>
                            <div class="card-body align-items-center justify-content-center d-flex">
                                RM <?php echo htmlspecialchars($pri) ?>
                            </div>                      
                        </div>
                    </div>
				</div>
				<div class="col-6">
                    <div class="card card-block d-flex dashboard">
                        <div class="card-body">
                            <h5 class="card-title text-center">Current Collected Total</h5>
                            <div class="card-body align-items-center justify-content-center d-flex">
                                RM <?php echo htmlspecialchars($cct) ?>
                            </div>                      
                        </div>
                    </div>
				</div>
				<div class="col-6">
                    <div class="card card-block d-flex dashboard">
                        <div class="card-body">
                            <h5 class="card-title text-center">Total Properties Recorded</h5>
                            <div class="card-body align-items-center justify-content-center d-flex">
                                <?php echo htmlspecialchars($tpr) ?> Record
                            </div>                      
                        </div>
                    </div>
				</div>
				<div class="col-6">
                    <div class="card card-block d-flex dashboard">
                        <div class="card-body">
                            <h5 class="card-title text-center">Total Unrented Property</h5>
                            <div class="card-body align-items-center justify-content-center d-flex">
                                <?php echo htmlspecialchars($tup) ?> Unrented
                            </div>                      
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="col-lg-9 col-md-12">
			<div class="row g-3">
				<div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body scroll">
                        <h5 class="card-title text-center">Uncollected Rental</h5>
                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Last Paid</th>
                                            <th scope="col">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($urs as $ur): 
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($ur['propertyName']) . "</td>";
                                    echo "<td>" . $ur['lastPaid'] . "</td>";
                                    echo "<td>" . "<a href=details.php?id=" . $ur['id'] . ">More Info</a>" . "</td>";
                                    endforeach ?>
                                    </tbody>
                                </table>      
                        </div>
                    </div>
			    </div>
				<div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body scroll">
                        <h5 class="card-title text-center">Reminders Nearing Deadline (7 Days)</h5>
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th scope="col">Property Name</th>
                                        <th scope="col">Remark</th>
                                        <th scope="col">Dead Line</th>
                                        <th scope="col">Days Till Deadline</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rnds as $rnd): 
                                    $rndstart = new DateTime($rnd['deadline']);
                                    $rndend = new Datetime($dlt);
                                    $rnddiff = $rndend->diff($rndstart);

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($rnd['propertyName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rnd['remark']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rnd['deadline']) . "</td>";
                                    echo "<td>" . $rnddiff->format('%r%a') . "</td>";
                                    echo "<td>" . "<a href=details.php?id=" . $ur['id'] . ">More Info</a>" . "</td>";
                                    endforeach ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
				</div>
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body scroll">
                        <h5 class="card-title text-center">Reminders Past DeadLine</h5>
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th scope="col">Property Name</th>
                                        <th scope="col">Remark</th>
                                        <th scope="col">Dead Line</th>
                                        <th scope="col">Days Past Deadline</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rpds as $rpd): 
                                    $rpdstart = new DateTime($rpd['deadline']);
                                    $rpdend = new Datetime($dlt);
                                    $rpddiff = $rpdstart->diff($rpdend);

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($rpd['propertyName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rpd['remark']) . "</td>";
                                    echo "<td>" . htmlspecialchars($rpd['deadline']) . "</td>";
                                    echo "<td>" . $rpddiff->format('%r%a') . "</td>";
                                    echo "<td>" . "<a href=details.php?id=" . $ur['id'] . ">More Info</a>" . "</td>";
                                    endforeach ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--display listings of property-->

<?php if(!empty($properties)){?>
<div class="row mx-auto m-4"> 
    <?php foreach($properties as $property):?>
        <div class="col-sm-12 col-md-6 col-lg-4 mb-4">                                                              
            <div class="card index-listings">                                                   
                <div class="card-header <?php if($property['availability']==1){ echo 'text-danger';} ?>"><?php echo htmlspecialchars($property['propertyName']); ?> (<?php echo htmlspecialchars($property['propertyType']); ?>)</div>
                <div class="card-body row ">
                    <div class="col-lg-5">Address: </div><div class="col-lg-7"><?php echo htmlspecialchars($property['address']); ?></div>
                    <div class="col-lg-5">Monthly Price: </div><div class="col-lg-7">RM<?php echo htmlspecialchars($property['price']); ?></div>
                    <div class="col-lg-5">Last Patment Date: </div><div class="col-lg-7"><?php echo htmlspecialchars($property['lastPaid']); ?></div>
                    <div class="col-lg-5">Tenant Name: </div><div class="col-lg-7"><?php echo htmlspecialchars($property['name']); ?></div>
                    <div class="col-lg-5">Tenant Contact: </div><div class="col-lg-7"><?php echo htmlspecialchars($property['contact']); ?></div>
                    <div class="col-lg-5">Notes: </div><div class="col-lg-7"><?php echo htmlspecialchars($property['notes']); ?></div>
                </div>
                    <div class="card-footer text muted d-flex justify-content-between">
                    <div>Created at: <?php echo htmlspecialchars($property['dateCreated']); ?></div>
                    <div><a href="details.php?id=<?php echo $property['propertyID']?>">More Info</a></div>
                </div>  
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php }else{ ?>
    <div class="text-center">
    <a href="addproperty.php"><b>You have not added any properties yet! Click on this text to add some properties!</b></a>
    </div>  
    <?php } }else{ ?>
            <div class="container">
                <div class="text-center h1 text-danger">
                Log In To Access to more features
                </div>
            </div>
        <?php } ?>

<?php include('template/footer.php') ?>
