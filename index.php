<?php //edycja
	require_once('dbConfig.php');
	$upload_dir = 'uploads/';

	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$sql = "select * from Users where id=".$id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
		}else{
			$errorMsg = 'Nie można wybrać rekordu';
		}
	}

	if(isset($_POST['btnUpdate'])){
		$firstname = $_POST['firstname'];
		$surname = $_POST['surname'];
                $email=$_POST['email'];
                $tel=$_POST['tel'];
                $city=$_POST['city'];
                
                
		$imgName = $_FILES['myfile']['name'];
		$imgTmp = $_FILES['myfile']['tmp_name'];
		$imgSize = $_FILES['myfile']['size'];

		if(empty($firstname)){
			$errorMsg = 'Brak imienia';
		}elseif(empty($surname)){
			$errorMsg = 'Brak nazwiska';
		}elseif(empty($email)){
			$errorMsg = 'Brak maila';
		}elseif(empty($city)){
			$errorMsg = 'Brak miasta';
		}elseif(empty($tel)){
			$errorMsg = 'Brak telefonu';
		}

		//udate image if user select new image
		if($imgName){
			//get image extension
			$imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
			//allow extenstion
			$allowExt  = array('jpeg', 'jpg', 'png', 'gif');
			//random new name for photo
			$userPic = time().'_'.rand(1000,9999).'.'.$imgExt;
			//check a valid image
			if(in_array($imgExt, $allowExt)){
				//check image size less than 5MB
				if($imgSize < 5000000){
					//delete old image
					unlink($upload_dir.$row['photo']);
					move_uploaded_file($imgTmp ,$upload_dir.$userPic);
				}else{
					$errorMsg = 'Zdjecie zajmuje zbyt duzo';
				}
			}else{
				$errorMsg = 'Wybierz inne zdjecie';
			}
		}else{
			//if not select new image - use old image name
			$userPic = $row['photo'];
		}

		//check upload file not error than insert data to database
		if(!isset($errorMsg)){
			$sql = "update Users
									set firstname = '".$firstname."',
										surname = '".$surname."',
                                                                                    email='".$email."',
                                                                                        tel='".$tel."',
                                                                                        city='".$city."',
										photo = '".$userPic."'
					where id=".$id;
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'Zaaktualizowano dane użytkownika';
				header('refresh:2;index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}
		}

	}
?>
<?php //dodawanie
	require_once('dbConfig.php');
	$upload_dir = 'uploads/';

	if(isset($_POST['btnSave'])){
		$firstname = $_POST['firstname'];
		$surname = $_POST['surname'];
               
                $email=$_POST['email'];
                 $tel=$_POST['tel'];
                $city=$_POST['city'];
               

		$imgName = $_FILES['myfile']['name'];
		$imgTmp = $_FILES['myfile']['tmp_name'];
		$imgSize = $_FILES['myfile']['size'];

		if(empty($firstname)){
			$errorMsg = 'Nie wprowadzono imienia';
		}elseif(empty($surname)){
			$errorMsg = 'Nie wprowadzono nazwiska';
		}elseif(empty($imgName)){
			$errorMsg = 'Nie dodano zdjęcia';
		}elseif(empty($email)){
			$errorMsg = 'Nie dodano maila';
		}elseif(empty($city)){
			$errorMsg = 'Nie dodano miasta';
		}elseif(empty($tel)){
			$errorMsg = 'Nie dodano telefonu';
		}
                else{
			//get image extension
			$imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
			//allow extenstion
			$allowExt  = array('jpeg', 'jpg', 'png', 'gif');
			//random new name for photo
			$userPic = time().'_'.rand(1000,9999).'.'.$imgExt;
			//check a valid image
			if(in_array($imgExt, $allowExt)){
				//check image size less than 2MB
				if($imgSize < 2000000){
					move_uploaded_file($imgTmp ,$upload_dir.$userPic);
				}else{
					$errorMsg = 'Zdjęcie zajmuje zbyt duzo';
				}
			}else{
				$errorMsg = 'Wybierz inne zdjęcie';
			}
		}

		//check upload file not error than insert data to database
		if(!isset($errorMsg)){
			$sql = "insert into Users(firstname, surname, email, tel, city, photo)
					values('".$firstname."', '".$surname."', '".$email."', '".$tel."', '".$city."', '".$userPic."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'Dodano użytkownika';
				header('refresh:5;index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}
		}

	}
?>
    
    <?php //usuwanie skrypt
	require_once('dbConfig.php');
	$upload_dir = 'uploads/';
	if(isset($_GET['delete'])){
		$id = $_GET['delete'];

		//select old photo name from database
		$sql = "select photo from Users where id = ".$id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);
			$photo = $row['photo'];
			unlink($upload_dir.$photo);
			//delete record from database
			$sql = "delete from Users where id=".$id;
			if(mysqli_query($conn, $sql)){
				header('location:index.php');
			}
		}
	}
?>


<!DOCTYPE html>
    <html lang="pl-PL">
<head>
    <meta charset="UTF-8">
	<title>Wprowadzone dane</title>
	<link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap-theme.min.css">
        <style type="text/css">
		body{ background-color: #F0F8FF;} 
	</style>
</head>
<body>

<div class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<h3 class="navbar-brand">Zadanie Contelizer</h3>
		</div>
	</div>
</div>
<div class="container">
	<div class="page-header">
            
            	<?php
		if(isset($errorMsg)){		
	?>
		<div class="alert alert-danger">
			<span class="glyphicon glyphicon-info">
				<strong><?php echo $errorMsg; ?></strong>
			</span>
		</div>
	<?php
		}
	?>

	<?php
		if(isset($successMsg)){		
	?>
		<div class="alert alert-success">
			<span class="glyphicon glyphicon-info">
				<strong><?php echo $successMsg; ?> </strong>
			</span>
		</div>
	<?php
		}
	?>

            

<?php if(isset($_GET['id'])){?>
<a class="btn btn-default" href="index.php">
				<span class="glyphicon glyphicon-plus"></span> &nbsp;Dodaj nowego użytkownika
			</a> </br> </br> </br>

<?php } ?>
            
                    	<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
            
            <?php if (isset($_GET['id'])){ ?>
            
      
		<div class="form-group">
			<label for="firstname" class="col-md-2">Imię</label>
			<div class="col-md-10">
                            <input type="text" name="firstname" class="form-control" value="<?php echo $row['firstname']; ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label for="surname" class="col-md-2">Nazwisko</label>
			<div class="col-md-10">
				<input type="text" name="surname" class="form-control" value="<?php echo $row['surname'] ?>"required>
			</div>
		</div>
                            	<div class="form-group">
			<label for="email" class="col-md-2">Email</label>
			<div class="col-md-10">
				<input type="email" name="email" class="form-control" value="<?php echo $row['email'] ?>"required>
			</div>
		</div>
                            <div class="form-group">
			<label for="tel" class="col-md-2">Telefon</label>
			<div class="col-md-10">
				<input type="tel" name="tel" pattern="[0-9]{3}[0-9]{3}[0-9]{3}" class="form-control" value="<?php echo $row['tel'] ?>" required>
			</div>
		</div>
                            
                            	<div class="form-group">
			<label for="city" class="col-md-2">Miejscowość</label>
			<div class="col-md-10">
				<input type="text" name="city" class="form-control" value="<?php echo $row['city'] ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label for="photo" class="col-md-2">Zdjęcie</label>
			<div class="col-md-10">
				<img src="<?php echo $upload_dir.$row['photo'] ?>" width="200">
				<input type="file" name="myfile" >
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2"></label>
			<div class="col-md-10">
				<button type="submit" class="btn btn-success" name="btnUpdate">
					<span class="glyphicon glyphicon-save"></span>Aktualizuj
			</div>
		</div>
	
            
            
            
            <?php }else{ ?>
            
            
             
		<div class="form-group">
			<label for="firstname" class="col-md-2">Imię</label>
			<div class="col-md-10">
				<input type="text" name="firstname" class="form-control" required>
			</div>
		</div>
		<div class="form-group">
			<label for="surname" class="col-md-2">Nazwisko</label>
			<div class="col-md-10">
				<input type="text" name="surname" class="form-control" required>
			</div>
		</div>
                            <div class="form-group">
			<label for="email" class="col-md-2">Email</label>
			<div class="col-md-10">
				<input type="email" name="email" class="form-control" required>
			</div>
		</div>
                                  <div class="form-group">
			<label for="tel" class="col-md-2">Telefon</label>
			<div class="col-md-10">
				
                                <input type="tel" name="tel" placeholder="9 cyfr" pattern="[0-9]{3}[0-9]{3}[0-9]{3}" class="form-control" required/>
			</div>
		</div>
                            <div class="form-group">
			<label for="city" class="col-md-2">Miejscowość</label>
			<div class="col-md-10">
				<input type="text" name="city" class="form-control" required>
			</div>
		</div>
                            
		<div class="form-group">
			<label for="photo" class="col-md-2">Zdjęcie</label>
			<div class="col-md-10">
                            <input type="file" name="myfile" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2"></label>
			<div class="col-md-10">
				<button type="submit" class="btn btn-success" name="btnSave">
					<span class="glyphicon glyphicon-save"></span>Zapisz
				</button>
			</div>
		</div>
	
            
            
            
            
            
            
              <?php  } ?>
                            </form>
            
            
		<h3>Tabela z danymi
			
		</h3>
	</div>
	<table class="table table-bordered table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Imię</th>
					<th>Nazwisko</th>
                                        <th>Email</th>
                                        <th>Telefon</th>
                                        <th>Miejscowość</th>
					<th>Zdjęcie</th>
					<th>Ustawienia</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$sql = "select * from Users";
				$result = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result)){
					while($row = mysqli_fetch_assoc($result)){
			?>
				<tr>
					<td><?php echo $row['id'] ?></td>
					<td><?php echo $row['firstname'] ?></td>
					<td><?php echo $row['surname'] ?></td>
                                        <td><?php echo $row['email'] ?></td>
                                         <td><?php echo $row['tel'] ?></td>
                                        <td><?php echo $row['city'] ?></td>
					<td><img src="<?php echo $upload_dir.$row['photo'] ?>" height="40"></td>
					<td>
						<a class="btn btn-info" href="index.php?id=<?php echo $row['id'] ?>" >  
							<span class="glyphicon glyphicon-edit"></span>Edytuj
						</a>
						<a class="btn btn-danger" href="index.php?delete=<?php echo $row['id'] ?>" onclick="return confirm('Czy na pewno chcesz usunąć?')">
							<span class="glyphicon glyphicon-remove-circle"></span>Usuń
						</a>
					</td>
				</tr>
			<?php
					}
				}
			?>
			</tbody>
	</table>
</div>

</body>
</html>