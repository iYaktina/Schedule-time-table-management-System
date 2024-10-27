<?php
    include_once "include/dbh.inc.php";
    include('menu.php');

    if (!isset($_SESSION['ID'])) {
        echo "<script>alert('Please Login!');</script>";
        header("Refresh: 0;URL=login.php");
    }
    if($_SESSION['Usertype'] != 'Admin'){
        echo "<script>alert('Classified for admins only');</script>";
        header("Refresh: 0;URL=index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="./css/admin.css">
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(form => form.style.display = 'none');
            document.getElementById(formId).style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="profile-container">
    <div class="header">
        <h2>Admin Panel</h2>
        <img src="./images/22.png" alt="Profile Icon" class="profile-icon">
    </div>

    <div class="button-container">
        <button onclick="showForm('addForm')">Add User</button>
        <button onclick="showForm('deleteForm')">Delete User</button>
        <button onclick="showForm('userList')">Get Users</button>
    </div>

    <div id="addForm" class="form-container">
        <h3>Add User</h3>
        <form action="add_user.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" required>
            
            <label for="usertype">Usertype</label>
            <input type="text" id="usertype" name="usertype" placeholder="Enter Usertype" required>

            <button type="submit">Add User</button>
                  <button type="button" onclick="window.location.href='?';">Back</button>
        </form>
    </div>
        <div id="deleteForm" class="form-container">
        <h3>Delete User</h3>
        <form action="delete_user.php" method="POST">
            <label for="user_id">Select User</label>
            <select id="user_id" name="user_id">
                <?php
                    $result = $conn->query("SELECT ID, Username FROM user");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['ID']}'>{$row['Username']}</option>";
                    }
                ?>
            </select>

            <button type="submit">Delete User</button>
                  <button type="button" onclick="window.location.href='?';">Back</button>
        </form>
    </div>

    <div id="userList" class="form-container">
    <h3>All Users</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
$result = $conn->query("SELECT ID, Username, Email, Usertype FROM user");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['ID']}</td>";
                    echo "<td>{$row['Username']}</td>";
                    echo "<td>{$row['Email']}</td>";
                    echo "<td>{$row['Usertype']}</td>";
                    echo "<td><a href='?user_id={$row['ID']}' class='edit-button'>Edit</a></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
          <button type="button"  onclick="window.location.href='?';">Back</button>
</div>
</body>
</html>
