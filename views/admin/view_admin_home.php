<?php
session_start();

require "../../model/config.php";

// unset($_SESSION);

    if( ! isset($_SESSION["status"]["is_login"]) || $_SESSION["status"]["is_login"] == null) {
        header("Location: ../../index.php");
    }
    
    $user_id = $_GET["v_id"] ?? false;

    $sql = "SELECT * FROM users";
    $result = query($sql, $mysqli);

    // $user_sql = "SELECT todo_id, status, content, username 
    //              FROM todos
    //              JOIN users ON todos.user_id = users.user_id 
    //              WHERE users.user_id='$user_id'";
    $user_sql = "SELECT todo_id, status, content, u.user_id, username
                 FROM users u LEFT JOIN todos t
                 ON t.user_id = u.user_id
                 WHERE u.user_id='$user_id'
                 UNION
                 SELECT todo_id, status, content, u.user_id, username
                 FROM users u RIGHT JOIN todos t
                 ON t.user_id = u.user_id
                 WHERE u.user_id='$user_id'";

    $user_data = query($user_sql, $mysqli);
    // var_dump(mysqli_fetch_assoc($user_data));die;
    // var_dump($_SERVER["PHP_SELF"]);die;
    //Daghan pa tiwasonon ani dri
?>

<?php require "../template/header.php"; ?>
    <div class="row">
        <div class="col-md-6">
            <h3>Admin Panel</h3>
        </div>
        
        <div class="col-md-6">
            <form action="../../controller/Logout.php" method="post">
                <button class="btn btn-primary float-right" type="submit">Log Out</button>
            </form>
        </div>
    </div>
    

    <?php
        if($user_id != false) {
            $user = mysqli_fetch_assoc($user_data);  
            echo "<h3>Todos from " . $user["username"] . " </h3>";
        }
    ?>
    
    <?php if( ! $user_id) : ?>      <!--- USER list shown --->
        <table class="table table-borderless">
            <tr class="thead-dark">
                <th>User ID</th>
                <th>Username</th>
                <th>Date Created</th>
                <th>Date Updated</th>
                <th>Action</th>
            </tr>
            <?php
                // mysqli_data_seek($result, 0);
                while($data = mysqli_fetch_assoc($result)) :
                    if($data["user_role_id"] == 1) {
                        continue;
                    }
            ?>
            <tr>
                <td><?php echo $data["user_id"]; ?></td>
                <td><?php echo $data["username"]; ?></td>
                <td><?php echo $data["date_created"]; ?></td>
                <td><?php echo $data["date_updated"]; ?></td>
                <td>
                    <a href="<?php echo $_SERVER["PHP_SELF"]; ?>?v_id=<?php echo $data["user_id"];?>" class="btn btn-primary">View</a> |
                    <a href="../../controller/Todo.php?delId=<?php echo $data["user_id"];?>" class="btn btn-danger">DELETE</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?> <!--- Users TODO list shown ---->
        <table class="table table-borderless">
            <tr class="thead-dark">
                <th>Todo ID</th>
                <th>Status</th>
                <th>Title</th>
                <th>Action</th>
            </tr>
            <?php
                if($user_data == false) {
                    header("Location: view_admin_home.php");
                }
                mysqli_data_seek($user_data, 0);
                while($result = mysqli_fetch_assoc($user_data)) :
                    if($result["content"] == null) {
                        continue;
                    }
            ?>
            <tr>
                <td><?php echo $result["todo_id"]; ?></td>
                <td>
                    <?php
                        if($result["status"] == "DONE") {
                            echo "<span class='text-success'>DONE</span>";
                        } else {
                            echo "<span class='text-primary'>IN PROGRESS</span>";
                        }
                    ?>
                </td>
                <td><?php echo $result["content"]; ?></td>
                <td>
                    <a href="../user/view_todo.php?t_id=<?php echo $result["todo_id"];?>&u_id=<?php echo $result["user_id"];?>" class="btn btn-primary">View</a> |
                    <a href="../../controller/Todo.php?delId=<?php echo $result["todo_id"];?>" class="btn btn-danger">DELETE</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <div class="row">
        <div class="col-md-6">
            <a href="view_admin_home.php" class="btn btn-primary">Go back</a>       
        </div>
        <div class="col-md-6">
            <a href="../user/view_add_todo.php?v_id=<?php echo $_GET["v_id"]; ?>" class="btn btn-primary float-right">Add Task</a>
        </div>
    </div>

    <?php endif; ?>

<?php require "../template/footer.php"; ?>