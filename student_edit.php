<?php
 session_start();
 if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
 }
 include("connection.php");
 $arr_month = [
   "1"=>"Januari",
   "2"=>"Februari",
   "3"=>"Maret",
   "4"=>"April",
   "5"=>"Mei",
   "6"=>"Juni",
   "7"=>"Juli",
   "8"=>"Agustus",
   "9"=>"September",
   "10"=>"Oktober",
   "11"=>"Nopember",
   "12"=>"Desember"
 ];
 if (isset($_POST["submit"])) {
   $nim = htmlentities(strip_tags(trim($_POST["nim"])));
   $name = htmlentities(strip_tags(trim($_POST["name"])));
   $birth_city = htmlentities(strip_tags(trim($_POST["birth_city"])));
   $faculty = htmlentities(strip_tags(trim($_POST["faculty"])));
   $department = htmlentities(strip_tags(trim($_POST["department"])));
   $gpa = htmlentities(strip_tags(trim($_POST["gpa"])));
   $birth_date = htmlentities(strip_tags(trim($_POST["birth_date"])));
   $birth_month = htmlentities(strip_tags(trim($_POST["birth_month"])));
   $birth_year = htmlentities(strip_tags(trim($_POST["birth_year"])));
   $error_message = "";
   if (empty($name)) {
     $error_message .= "- Nama belum diisi <br>";
   }
   if (empty($birth_city)) {
     $error_message .= "- Tempat lahir belum diisi <br>";
   }
   if (empty($department)) {
     $error_message .= "- Jurusan belum diisi <br>";
   }
   $select_ftib = ($faculty == 'FTIB') ? "selected" : "";
   $select_fteic = ($faculty == 'FTEIC') ? "selected" : "";
   if (empty($gpa)) {
     $error_message .= "- IPK belum diisi <br>";
   }
   else if (!is_numeric($gpa) || ($gpa < 0 || $gpa > 4)) {
     $error_message .= "- IPK harus berupa angka antara 0.00 s/d 4.00 <br>";
   }
   if ($error_message === "") {
     $nim = mysqli_real_escape_string($connection, $nim);
     $name = mysqli_real_escape_string($connection, $name);
     $birth_city = mysqli_real_escape_string($connection, $birth_city);
     $faculty = mysqli_real_escape_string($connection, $faculty);
     $department = mysqli_real_escape_string($connection, $department);
     $gpa = (float) $gpa;
     $formatted_date = "$birth_year-" . str_pad($birth_month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($birth_date, 2, "0", STR_PAD_LEFT);
     $query = "UPDATE student SET 
                 name = '$name',
                 birth_city = '$birth_city',
                 birth_date = '$formatted_date',
                 faculty = '$faculty',
                 department = '$department',
                 gpa = '$gpa'
               WHERE nim = '$nim'";
     $result = mysqli_query($connection, $query);

     if ($result) {
       $message = urlencode("Data mahasiswa dengan NIM <b>$nim</b> berhasil diupdate.");
       header("Location: student_view.php?message={$message}");
       exit();
     }
     else {
       die("Query gagal dijalankan: " . mysqli_errno($connection) . " - " . mysqli_error($connection));
     }
   }
 }
 else if (isset($_GET["nim"])) {
   $nim = htmlentities(strip_tags(trim($_GET["nim"])));
   $nim = mysqli_real_escape_string($connection, $nim);
   $query = "SELECT * FROM student WHERE nim='$nim'";
   $result = mysqli_query($connection, $query);

   if (!$result || mysqli_num_rows($result) === 0) {
     header("Location: student_view.php");
     exit();
   }
   $data = mysqli_fetch_assoc($result);
   $name = $data["name"];
   $birth_city = $data["birth_city"];
   $faculty = $data["faculty"];
   $department = $data["department"];
   $gpa = $data["gpa"];

   $timestamp = strtotime($data["birth_date"]);
   $birth_date = (int) date("d", $timestamp);
   $birth_month = (string) (int) date("m", $timestamp);
   $birth_year = (int) date("Y", $timestamp);

   $select_ftib = ($faculty == 'FTIB') ? "selected" : "";
   $select_fteic = ($faculty == 'FTEIC') ? "selected" : "";
   $error_message = "";
 }
 else {
   header("Location: student_view.php");
   exit();
 }
?>
<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <title>Edit Data Mahasiswa</title>
 <link href="assets/style.css" rel="stylesheet" >
</head>
<body>
 <div class="container">
   <div id="header">
     <h1 id="logo">Data Mahasiswa</h1>
   </div>
   <hr>
   <nav>
     <ul>
       <li><a href="student_view.php">Tampil</a></li>
       <li><a href="student_add.php">Tambah</a></li>
       <li><a href="logout.php">Logout</a></li>
     </ul>
   </nav>
   <h2>Edit Data Mahasiswa</h2>
   <?php
     if ($error_message !== "") {
         echo "<div class='error'>$error_message</div>";
     }
   ?>
   <form id="form_mahasiswa" action="student_edit.php" method="post">
     <fieldset>
       <legend>Edit Mahasiswa</legend>
       <p>
         <label for="nim">NIM : </label>
         <input type="text" name="nim" id="nim" value="<?php echo $nim ?>" readonly style="background-color: #EEE; cursor: not-allowed;"> (NIM tidak dapat diubah)
       </p>
       <p>
         <label for="name">Nama : </label>
         <input type="text" name="name" id="name" value="<?php echo $name ?>">
       </p>
       <p>
         <label for="birth_city">Tempat Lahir : </label>
         <input type="text" name="birth_city" id="birth_city" value="<?php echo $birth_city ?>">
       </p>
       <p>
         <label for="birth_date">Tanggal Lahir : </label>
           <select name="birth_date" id="birth_date">
             <?php
               for ($i = 1; $i <= 31; $i++) {
                 $selected = ($i == $birth_date) ? "selected" : "";
                 $padded = str_pad($i, 2, "0", STR_PAD_LEFT);
                 echo "<option value='$i' $selected>$padded</option>";
               }
             ?>
           </select>
           <select name="birth_month">
             <?php
               foreach ($arr_month as $key => $value) {
                 $selected = ($key == $birth_month) ? "selected" : "";
                 echo "<option value='$key' $selected>$value</option>";
               }
             ?>
           </select>
           <select name="birth_year">
             <?php
               for ($i = 1990; $i <= 2005; $i++) {
                 $selected = ($i == $birth_year) ? "selected" : "";
                 echo "<option value='$i' $selected>$i</option>";
               }
             ?>
           </select>
       </p>
       <p>
         <label for="faculty">Fakultas : </label>
           <select name="faculty" id="faculty">
             <option value="FTIB" <?php echo $select_ftib ?>>FTIB</option>
             <option value="FTEIC" <?php echo $select_fteic ?>>FTEIC</option>
           </select>
       </p>
       <p>
         <label for="department">Jurusan : </label>
         <input type="text" name="department" id="department" value="<?php echo $department ?>">
       </p>
       <p>
         <label for="gpa">IPK : </label>
         <input type="text" name="gpa" id="gpa" value="<?php echo $gpa ?>" placeholder="Contoh: 3.50"> (desimal dipisah ".")
       </p>
     </fieldset>
     <p>
       <input type="submit" name="submit" value="Update Data">
       <a href="student_view.php" style="margin-left: 10px; color: #666; text-decoration: none;">Batal</a>
     </p>
   </form>
 </div>
</body>
</html>
<?php
 mysqli_close($connection);
?>
