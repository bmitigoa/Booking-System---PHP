<?php

// ------------------------
// 1. ROUTER HANDLER
// ------------------------
$page = $_GET['page'] ?? 'home'; // Determine which page to show

?>

<!DOCTYPE html>
<html>
<head>
    <title>Lab Booking System</title>
    <style>
  
    body { 
    font-family: Arial; 
    margin: 0; 
    padding: 0; 
    background-image: url('bg.jpg');  
    
    background-size: cover;            /* make image fill page */
    background-position: center;       /* center the image */
    background-repeat: no-repeat;      /* no repeating tiles */
    background-attachment: fixed;      /* parallax effect */
    }

    body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.55);   /* darkness level */
    z-index: -1;                    /* stay behind page content */
    }

    .container { 
        max-width: 1000px; 
        margin: auto; 
        padding: 20px; 
    }

    h1,h2,h3 { 
        margin-top: 0; 
    }

    .card { 
        background: white;
        padding: 20px; 
        border-radius: 10px; 
        margin-bottom: 20px; 
        
    }

    .btn { 
        padding: 10px 16px; 
        border-radius: 5px; 
        background: dodgerblue;
        color: white;
        text-decoration: none; 
        margin-right: 5px; 
    }

    .btn:hover { 
        opacity: 0.9; 
    }

    .btn-danger { 
        background: crimson;
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
    }

    th, td { 
        border: 1px solid grey; 
        padding: 10px; 
        text-align: left;
        color: white;  
    }

    th { 
        background: dodgerblue;
        color: black;
    }

    a { 
        text-decoration: none; 
    }

    input, select { 
        padding: 6px; 
        width: 100%; 
        margin-bottom: 10px; 
    }

    .nav { 
        margin-bottom: 20px; 
    }

    .nav a { 
        margin-right: 10px; 
    }

    .success { 
        color: green; 
        font-weight: bold; 
    }

    .error { 
        color: red; 
        font-weight: bold; 
    }

</style>

</head>
<body>
<div class="container">

<?php
// ================================
// 2. HOME PAGE
// ================================
if($page == 'home') {
 
    echo "<div class='card' style='background: linear-gradient(135deg, skyblue, royalblue); color: white;'>";
    echo "<h1>Book Your Lab With Us!</h1>";
    echo "<p>Book labs and events easily and see the upcoming events.</p>";
    echo "<a class='btn' href='?page=events_public'>View Events</a>";
    echo "<a class='btn' href='?page=login'>Admin Login</a>";
    echo "</div>";
}

// ================================
// 3. ADMIN LOGIN
// ================================
if($page == 'login') {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            $stmt->bind_result($id, $hashed);
            $stmt->fetch();
            if(password_verify($password, $hashed)){
                $_SESSION['admin']=$id;
                header("Location: ?page=admin");
                exit();
            } else {
                echo "<p class='error'>Wrong password.</p>";
            }
        } else {
            echo "<p class='error'>Admin not found.</p>";
        }
    }
?>
<div class="card">
    <h2>Admin Login</h2>
    <form method="POST">
        Email:<input type="email" name="email" required>
        Password:<input type="password" name="password" required>
        <button class="btn">Login</button>
    </form>
</div>
<?php
}

// ================================
// 4. ADMIN DASHBOARD
// ================================
if($page == 'admin') {
    if(!isset($_SESSION['admin'])) { header("Location: ?page=login"); exit(); }
    echo "<div class='card'>";
    echo "<h2>Admin Dashboard</h2>";
    echo "<div class='nav'>";
    echo "<a class='btn' href='?page=labs'>Manage Labs</a>";
    echo "<a class='btn' href='?page=events'>Manage Events</a>";
    echo "<a class='btn btn-danger' href='?page=logout'>Logout</a>";
    echo "</div></div>";
}

// ================================
// 5. LOGOUT
// ================================
if($page == 'logout') {
    session_destroy();
    header("Location: ?page=home");
    exit();
}

// ================================
// 6. MANAGE LABS (CRUD)
// ================================
if($page=='labs'){
    if(!isset($_SESSION['admin'])) { header("Location:?page=login"); exit(); }

    // ADD LAB
    if(isset($_POST['add_lab'])){
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];
        $stmt=$conn->prepare("INSERT INTO labs (name, capacity) VALUES (?,?)");
        $stmt->bind_param("si",$name,$capacity);
        $stmt->execute();
    }

    // UPDATE LAB
    if(isset($_POST['update_lab'])){
        $id=$_POST['id'];
        $name=$_POST['name'];
        $capacity=$_POST['capacity'];
        $stmt=$conn->prepare("UPDATE labs SET name=?, capacity=? WHERE id=?");
        $stmt->bind_param("sii",$name,$capacity,$id);
        $stmt->execute();
    }

    // DELETE LAB
    if(isset($_GET['delete'])){
        $id=$_GET['delete'];
        $conn->query("DELETE FROM labs WHERE id=$id");
    }

    // DISPLAY FORM
    if(isset($_GET['edit'])){
        $id=$_GET['edit'];
        $lab=$conn->query("SELECT * FROM labs WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <h3>Edit Lab</h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $lab['id'] ?>">
        Name: <input type="text" name="name" value="<?= $lab['name'] ?>" required>
        Capacity: <input type="number" name="capacity" value="<?= $lab['capacity'] ?>" required>
        <button class="btn" name="update_lab">Update Lab</button>
    </form>
</div>
<?php
    }
?>

<div class="card">
    <h3>Add New Lab</h3>
    <form method="POST">
        Name: <input type="text" name="name" required>
        Capacity: <input type="number" name="capacity" required>
        <button class="btn" name="add_lab">Add Lab</button>
    </form>
</div>

<h3 style="color: red;">Existing Labs</h3>
<table>
<tr><th>ID</th><th>Name</th><th>Capacity</th><th>Action</th></tr>
<?php
$result=$conn->query("SELECT * FROM labs");
while($row=$result->fetch_assoc()){
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['capacity']}</td>
        <td>
            <a class='btn' href='?page=labs&edit={$row['id']}'>Edit</a>
            <a class='btn btn-danger' href='?page=labs&delete={$row['id']}'>Delete</a>
        </td>
    </tr>";
}
?>
</table>
<?php
}

// ================================
// 7. MANAGE EVENTS (CRUD)
// ================================
if($page=='events'){
    if(!isset($_SESSION['admin'])) { header("Location:?page=login"); exit(); }

    // ADD EVENT
    if(isset($_POST['add_event'])){
        $title=$_POST['title'];
        $date=$_POST['date'];
        $lab_id=$_POST['lab_id'];
        $stmt=$conn->prepare("INSERT INTO events (title,event_date,lab_id) VALUES (?,?,?)");
        $stmt->bind_param("ssi",$title,$date,$lab_id);
        $stmt->execute();
    }

    // UPDATE EVENT
    if(isset($_POST['update_event'])){
        $id=$_POST['id'];
        $title=$_POST['title'];
        $date=$_POST['date'];
        $lab_id=$_POST['lab_id'];
        $stmt=$conn->prepare("UPDATE events SET title=?, event_date=?, lab_id=? WHERE id=?");
        $stmt->bind_param("ssii",$title,$date,$lab_id,$id);
        $stmt->execute();
    }

    // DELETE EVENT
    if(isset($_GET['delete'])){
        $id=$_GET['delete'];
        $conn->query("DELETE FROM events WHERE id=$id");
    }

    // EDIT FORM
    if(isset($_GET['edit'])){
        $id=$_GET['edit'];
        $event=$conn->query("SELECT * FROM events WHERE id=$id")->fetch_assoc();
?>
<div class="card">
    <h3>Edit Event</h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
        Title: <input type="text" name="title" value="<?= $event['title'] ?>" required>
        Date: <input type="date" name="date" value="<?= $event['event_date'] ?>" required>
        Lab:
        <select name="lab_id" required>
        <?php
        $labs=$conn->query("SELECT * FROM labs");
        while($lab=$labs->fetch_assoc()){
            $sel=($lab['id']==$event['lab_id'])?'selected':'';
            echo "<option value='{$lab['id']}' $sel>{$lab['name']}</option>";
        }
        ?>
        </select>
        <button class="btn" name="update_event">Update Event</button>
    </form>
</div>
<?php
    }
?>

<div class="card">
    <h3>Add New Event</h3>
    <form method="POST">
        Title: <input type="text" name="title" required>
        Date: <input type="date" name="date" required>
        Lab:
        <select name="lab_id" required>
        <?php
        $labs=$conn->query("SELECT * FROM labs");
        while($lab=$labs->fetch_assoc()){
            echo "<option value='{$lab['id']}'>{$lab['name']}</option>";
        }
        ?>
        </select>
        <button class="btn" name="add_event">Add Event</button>
    </form>
</div>

<h3 style="color: red;">Existing Events</h3>
<table>
<tr><th>ID</th><th>Title</th><th>Date</th><th>Lab</th><th>Action</th></tr>
<?php
$result=$conn->query("SELECT events.*, labs.name as lab_name FROM events JOIN labs ON events.lab_id=labs.id");
while($row=$result->fetch_assoc()){
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['title']}</td>
        <td>{$row['event_date']}</td>
        <td>{$row['lab_name']}</td>
        <td>
            <a class='btn' href='?page=events&edit={$row['id']}'>Edit</a>
            <a class='btn btn-danger' href='?page=events&delete={$row['id']}'>Delete</a>
        </td>
    </tr>";
}
?>
</table>
<?php
}

// ================================
// 8. PUBLIC EVENT VIEW
// ================================
if($page=='events_public'){
    echo "<h2 style='color: red;'>Upcoming Events</h2>";
    $result=$conn->query("SELECT events.*, labs.name as lab_name FROM events JOIN labs ON events.lab_id=labs.id ORDER BY event_date ASC");
    echo "<table>";
    echo "<tr><th>Title</th><th>Date</th><th>Lab</th><th>Book</th></tr>";
    while($row=$result->fetch_assoc()){
        echo "<tr>
            <td>{$row['title']}</td>
            <td>{$row['event_date']}</td>
            <td>{$row['lab_name']}</td>
            <td><a class='btn' href='?page=book&id={$row['id']}'>Book</a></td>
        </tr>";
    }
    echo "</table>";
}

// ================================
// 9. BOOKING FORM
// ================================
if($page=='book'){
    $event_id=$_GET['id'];
    $event=$conn->query("SELECT events.*, labs.name as lab_name FROM events JOIN labs ON events.lab_id=labs.id WHERE events.id=$event_id")->fetch_assoc();

    if(isset($_POST['book'])){
        $name=$_POST['name'];
        $email=$_POST['email'];
        $conn->query("INSERT INTO bookings (event_id,name,email) VALUES ($event_id,'$name','$email')");
        echo "<p class='success'>Booking successful for {$event['title']}!</p>";
        echo "<a class='btn' href='?page=events_public'>Back to Events</a>";
        exit();
    }
?>
<div class="card">
    <h3>Book Event: <?= $event['title'] ?> (<?= $event['lab_name'] ?>)</h3>
    <form method="POST">
        Name: <input type="text" name="name" required>
        Email: <input type="email" name="email" required>
        <button class="btn" name="book">Book Now</button>
    </form>
</div>
<?php
}
?>
</div>
</body>
</html>
