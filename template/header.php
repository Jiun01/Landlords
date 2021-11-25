
<!DOCTYPE html>
<html>
<head>
<title>Landlords</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/main.min.css" rel="stylesheet">
<link href="css/extcss.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

</head>

<body class="bg-blue">
  <div class="wrapper pb-5">
  <!--navbar-->
	<nav class="navbar navbar-expand-lg navbar-light mb-3 bg-storm" >
    <div class="container-fluid">
      <a href="index.php" class="navbar-brand fs-4"><span class="fw-bold text-light">Landlords</span></a>
    <!--mobile-->  
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
      aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>    
    </button>
      <!--navbar stuffs--> 
      <div class="collapse navbar-collapse justify-content-end align-center" id="main-nav">
        <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-light" href="index.php">Home</a>
          </li>


<?php if(isset($_SESSION['ownerid'])){?>
      <li class="nav-item">
        <a class="nav-link text-light" href="searchbox.php">Search Property or Tenant</a>
      </li>

  <li class="nav-item dropdown">
    <a class="nav-link text-light dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Hello, <?php echo $_SESSION["displayname"];?></a>
      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <li><a class="dropdown-item" href="profilesettings.php">Account Details and Settings</a></li>
        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
      </ul>
  <?php }else{?>
  <li class="nav-item dropdown">
    <a class="nav-link text-light dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Hello,Guest</a>
      <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <li><a class="dropdown-item" href="signin.php">Login</a></li>
        <li><a class="dropdown-item" href="signup.php">Register</a></li>
      </ul>
<?php }?>
          <li class="nav-item d-lg-none"><!--invisible when large screen--> 
            <a class="nav-link text-light" href="addproperty.php">Add a new Property</a>
          </li>
          <li class="nav-item ms-2 d-none d-lg-inline"><!--invisible on small screen--> 
            <a class="btn btn-secondary" href="addproperty.php">Add a new Property</a>
          </li>
          
        </ul>
      </div>
    </div>
  </nav>
