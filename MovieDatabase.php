<?php
ini_set('display_errors', 'On');
include 'password.php';

$mysqli = new mysqli("localhost", "root", $password, "vid_inventory");

if($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli -> connect_errno . ") " . $mysqli -> connect_error;
} else {
    //echo "Connection worked! <br>";
}?>

<!DOCTYPE html>
<html>
<head>
  <title>Movie Database</title>
  <meta charset="UTF-8">
</head>
<body>
<h1>Movie Database</h1>
<h3>Please enter a movie:</h3>
<form action="http://savvyg.me/Test%20Stuff/test.php" method="post">
    <p>Name:
      <input type="text" name="name">
      Category:
      <input type="text" name="category">
      Length (min):
      <input type="text" name="length">
      <input type="submit" name = "adding" onClick="parent.location='http://savvyg.me/Test%20Stuff/test.php'" value='Add Video'>
    </p>
</form>

<?php
//if (!$mysqli->query("DROP TABLE IF EXISTS inventory") ||
//   !$mysqli->query("CREATE TABLE inventory(id INT auto_increment PRIMARY KEY, 
//    name VARCHAR(255) NOT NULL UNIQUE, category VARCHAR(255), length INT unsigned, rented BIT NOT NULL default 0)")) {
//    echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
//}

if(isset($_POST['delete'])){
    //echo $_POST['delete1'];
    $toDelete = $_POST['delete1'];
    if(!($stmt = $mysqli->prepare("DELETE FROM `inventory` WHERE `id` = ?"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param("d", $toDelete)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

if(isset($_POST['deleteAll'])){
    //echo "Deleting all";
    $mysqli->query("TRUNCATE TABLE `inventory`");
}

if(isset($_POST['checked'])){
    if ($_POST['checked'] == "Available"){
        //echo "Change this to checked out";
        $checkID = $_POST['availability'];
        $value = 1;
        if (!($stmt = $mysqli->prepare("UPDATE inventory SET rented = ? WHERE id = ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("dd", $value, $checkID)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        //echo $_POST['checked'];
    } else if ($_POST['checked'] == "Checked Out"){
        //echo "Change this to available";
        $checkID = $_POST['availability'];
        $value = 0;
        if (!($stmt = $mysqli->prepare("UPDATE inventory SET rented = ? WHERE id = ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("dd", $value, $checkID)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        //echo $_POST['checked'];
    }
    //echo $_POST['availability'];
}

if(isset($_POST['adding'])){
    if($_POST['name'] == '' || !is_string($_POST['name'])){
        if($_POST['name'] == ''){
            echo "A name is required to add the video.<br>";
        }else{
            echo "Sorry, invalid input. Please enter a valid title.<br>";
        }
    } else{
        $name = mysqli_real_escape_string($mysqli, $_POST['name']);
    }
    if(!is_string($_POST['category'])){
        echo "Sorry, invalid input. Please enter a valid category.<br>";
    } else{
        $category = mysqli_real_escape_string($mysqli, $_POST['category']);
    }
    if($_POST['length'] != '' && !ctype_digit($_POST['length'])){
        if (($_POST['length']) < 0){
            echo "Sorry, length must be positive.<br>";
        } else {
        echo "Sorry, invalid input. Please enter a number for the length.<br>";
        }
    } else{
        $length = mysqli_real_escape_string($mysqli, $_POST['length']);
    }
    if(isset($name) && isset($category) && isset($length)){
        if (!($stmt = $mysqli->prepare("INSERT INTO inventory(name, category, length) 
        VALUES (?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->bind_param("ssd", $name, $category, $length)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
    }
}

if(isset($_POST['filter'])){
    $selection = $_POST['selection'];
    //echo $selection;
    if($selection == "'allMovies'"){
        //echo "It was set to $selection";
        if (!($stmt = $mysqli->prepare("SELECT * FROM inventory"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $out_id = NULL;
        $out_name = NULL;
        $out_category = NULL;
        $out_length = NULL;
        $out_rented = NULL;
        if (!$stmt->bind_result($out_id, $out_name, $out_category, $out_length, $out_rented)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }else {
        //echo "It was set to else $selection";
        if (!($stmt = $mysqli->prepare("SELECT * FROM inventory WHERE `category` = $selection"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        
        $out_id = NULL;
        $out_name = NULL;
        $out_category = NULL;
        $out_length = NULL;
        $out_rented = NULL;
        if (!$stmt->bind_result($out_id, $out_name, $out_category, $out_length, $out_rented)) {
            echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    }
} else{
    if (!($stmt = $mysqli->prepare("SELECT * FROM inventory"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    $out_id = NULL;
    $out_name = NULL;
    $out_category = NULL;
    $out_length = NULL;
    $out_rented = NULL;
    if (!$stmt->bind_result($out_id, $out_name, $out_category, $out_length, $out_rented)) {
        echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}

echo "<table>";
echo "<tbody>";
echo "<tr><td>Id</td><td>Name</td><td>Category</td><td>Length (min)</td><td>Rented</td></tr>";
while ($stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . $out_id . "</td>";
    echo "<td>" . $out_name . "</td>";
    echo "<td>" . $out_category . "</td>";
    echo "<td>" . $out_length . "</td>";
    if($out_rented == 0) {
        $availability = "Available";
    }else{
        $availability = "Checked Out";
    }
    echo "<td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type=\"hidden\" name=\"availability\" value=\"".$out_id."\"/>
    <input type =\"submit\" name=\"checked\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='$availability'></form></td>";
    echo "<td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type=\"hidden\" name=\"delete1\" value=\"".$out_id."\"/>
    <input type =\"submit\" name=\"delete\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='Delete'></form></td>";
    echo "</tr>";
}
echo "<tr><td></td><td></td><td></td><td></td><td></td><td><form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">
    <input type =\"submit\" name=\"deleteAll\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php\" 
    value='Delete All'></form>";
echo "</tbody>";
echo "</table>";

$queryusers = "SELECT DISTINCT `category` FROM `inventory` WHERE `category` IS NOT NULL AND TRIM(`category`) <> ''";
$db = mysqli_query($mysqli, $queryusers);

echo"<form action=\"http://savvyg.me/Test%20Stuff/test.php\" method=\"post\">";

echo "<select name=\"selection\">";
while ($d=mysqli_fetch_assoc($db)) {
  echo "<option value=\"'".$d['category']."'\">".$d['category']."</option>";
}
echo "<option value=\"'allMovies'\">All Movies</option>";
echo "</select>";

echo "<input type=\"submit\" name =\"filter\" onClick=\"parent.location='http://savvyg.me/Test%20Stuff/test.php'\" value='Filter'>";
echo "</form>";
?>