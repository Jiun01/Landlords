<?php 
include ('config/db_connect.php');
if(!isset($_SESSION)) {session_start();}

$sql ='SELECT * FROM properties WHERE ownerID="'.$_SESSION["ownerid"].'" AND isDel="0"';
$result = mysqli_query($conn,$sql);
$rows = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);


//delete button trigger query
if(isset($_POST['checkbox']))
{
$array = $_POST['checkbox'];
$listCheck = "'" . implode("','", $array) . "'";
$isdelupdate='UPDATE properties SET isDel=1 WHERE bookingid IN('.$listCheck.')';
$delete = mysqli_query ($conn,$isdelupdate);
}

//date searcher
if(isset($_GET['datesearchbtn']))
{
$datesearchsql = 
'SELECT * 
FROM bookings 
WHERE isDel="0" AND (startDate = "'.$_GET['datesearch'].'" OR endDate = "'.$_GET['datesearch'].'")';
$result = mysqli_query($conn,$datesearchsql);
$rows = mysqli_fetch_all($result,MYSQLI_ASSOC);
mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('template/header.php') ?>
  <span>
    <div class="text-end mt-1 me-3">
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
      <input id="datesearch" name="datesearch" type="date">
      <input id="datesearchbtn" name="datesearchbtn" type="submit" value="Search by Date">
    </div>
  </span>
</form>
<form name="form1" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
<table class="table">
  <thead>
    <tr>
      <th scope="col">Property Name</th>
      <th scope="col">Address</th>
      <th scope="col">City</th>
      <th scope="col">Zipcode</th>
      <th scope="col">Property Type</th>
      <th scope="col">Notes</th>
      <th scope="col">Delete</th>
    </tr>
  </thead>
    <tbody>
<?php   
foreach($rows as $row):
echo "<tr>";
echo "<td>" . htmlspecialchars($row['propertyName']) . "</td>";
echo "<td>" . htmlspecialchars($row['address']) . "</td>";
echo "<td>" . htmlspecialchars($row['city']) . "</td>";
echo "<td>" . htmlspecialchars($row['zipcode']) . "</td>";
echo "<td>" . htmlspecialchars($row['propertyType']) . "</td>";
echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
echo "<td><input type='checkbox' class='form-check-input' name='checkbox[]' value='".$row["id"]."'></td></tr>";
endforeach ?>
    </tbody>
</table>
<div class="text-end p-3 me-3">
<input class="btn btn-danger btn-lg" type="submit" name="delete" value="Delete Selected"></input>
</div>
</form>

<?php include('template/footer.php') ?>
</html>