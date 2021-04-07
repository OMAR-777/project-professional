<?php
require_once("config/db.php");

if (!(isset($_GET['pID']) && isset($_GET['pName']))) {
    header('location: projects.php');
} else {
    if (ProjectIDNotExist($_GET['pID'])) {
        header('location: projects.php');
    }
    if (ProjectNameNotExist($_GET['pName'])) {
        header('location: projects.php');
    }
}
$projectID = $_GET['pID'];
$projectName = $_GET['pName'];
$errors = array();
$messages = array();
$resName = "";

if (isset($_POST['deleteRes'])) {
    $resID = $_POST['deleteRes'];
    deleteRes($resID);
}
if (isset($_POST['resName']) && isset($_POST['resType']) && isset($_POST['resCommit']) && isset($_POST['resType'])) {
    $resName = $_POST['resName'];
    $resType = $_POST['resType'];
    $resCommit = $_POST['resCommit'];
    $resRate = $_POST['resRate'];

    if (resNameNotExist($resName)) {
        insertRes($resName, $resType, $resCommit, $resRate, $projectID);
    } else {
        $errors['resName'] = "Resource name already exists";
    }
}
if (isset($_POST['editedName']) && isset($_POST['editedType']) && isset($_POST['editedCommit']) && isset($_POST['editedRate']) && isset($_POST['editID'])) {
    $resID = $_POST['editID'];
    $resName = $_POST['editedName'];
    $resType = $_POST['editedType'];
    $resCommit = $_POST['editedCommit'];
    $resRate = $_POST['editedRate'];

        if (resNameNotExistEdit($resName, $resID)) {
            editRes($resName, $resType, $resCommit,$resRate, $resID);
        } else {
            $errors['resName'] = "Resource name already exists";
        }
    
}
function deleteRes($resID)
{
    global $conn, $messages, $errors;
    $resultQuery = mysqli_query($conn, "DELETE FROM resource WHERE id='$resID'");
    $resultAff = mysqli_affected_rows($conn);
    if ($resultQuery == true) {
        if ($resultAff > 0) {
            $messages['resDelete'] = 'Resource with id ' . $resID . ' has been deleted';
        } else {
            $errors['resAffect'] = 'No rows are affected' . $resID;
        }
    } else {
        $errors['resDelete'] = 'Unable to delete Resource' . $resID;
    }
}
function editRes($resName, $resType, $resCommit, $resRate, $resID)
{
    global $conn, $errors, $messages;

    $resultRes = mysqli_query($conn, "UPDATE resource SET name='$resName', type='$resType', max='$resCommit', rate='$resRate' WHERE id='$resID'");
    if ($resultRes == true) {
        $messages['resInsert'] = 'Resource edited successfully!';
    } else {
        $errors['resInsert'] = 'Unable to edit the resource';
    }
}

function insertRes($resName, $resType, $resCommit, $resRate, $projectID)
{
    global $conn, $errors, $messages;

    $resultRes = mysqli_query($conn, "INSERT INTO resource(name, type, max, rate, project_id) VALUES('$resName','$resType','$resCommit','$resRate',$projectID)");
    if ($resultRes == true) {
        $messages['resInsert'] = 'Resource added successfully!';
    } else {
        $errors['resInsert'] = 'Unable to add the Resource';
    }
}
function resNameNotExist($resName)
{
    global $conn, $projectID;
    $result = mysqli_query($conn, "SELECT * FROM resource WHERE name='$resName' AND project_id='$projectID' LIMIT 1");
    $nameCount = mysqli_num_rows($result);
    if ($nameCount == 0) {
        return true;
    } else {
        return false;
    }
}
function resNameNotExistEdit($resName, $id)
{
    global $conn, $projectID;
    $result = mysqli_query($conn, "SELECT * FROM resource WHERE name='$resName' AND project_id='$projectID' AND id<>'$id' LIMIT 1");
    $nameCount = mysqli_num_rows($result);
    if ($nameCount == 0) {
        return true;
    } else {
        return false;
    }
}
function ProjectNameNotExist($name)
{
    global $conn;
    $nameQuery = mysqli_query($conn, "SELECT * FROM project WHERE name='$name' LIMIT 1");
    $nameCount = mysqli_num_rows($nameQuery);
    if ($nameCount == 0) {
        return true;
    } else {
        return false;
    }
}

function ProjectIDNotExist($id)
{
    global $conn;
    $nameQuery = mysqli_query($conn, "SELECT * FROM project WHERE id='$id' LIMIT 1");
    $nameCount = mysqli_num_rows($nameQuery);
    if ($nameCount == 0) {
        return true;
    } else {
        return false;
    }
}
function printErrors()
{
    global $errors;
    if (count($errors) > 0) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</div>';
    }
}
function printMessages()
{
    global $messages;
    if (count($messages) > 0) {
        echo '<div class="alert alert-success">';
        foreach ($messages as $message) {
            echo '<li>' . $message . '</li>';
        }
        echo '</div>';
    }
}
function printRes()
{
    global $conn, $projectID, $projectName;
    $sqlRes = "SELECT * FROM resource WHERE project_id='$projectID'";
    $result = mysqli_query($conn, $sqlRes);
    $num = mysqli_num_rows($result);
    if ($num != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['type'] . "</td>
            <td>" . $row['max'] . "</td>
            <td>" . $row['rate'] . "</td>
            <td>
            <button id='btn-edit' data-toggle='modal' data-target='#myModal' class='btn btn-light btn-sm' type='submit' name=''><i class='fas fa-edit'></i> Edit</button>
            <form class='d-inline' action='resources.php?pID=" . $projectID . "&pName=" . $projectName . "' method='post'>
            <button class='btn btn-delete btn-danger btn-sm' type='submit' name='deleteRes' value='$id'><i class='fas fa-trash'></i> Delete</button></form></td>
          </tr>";
        }
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link rel="stylesheet" href="styles.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- font awesome icons-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">



    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <title>Resources | Project Professional</title>
</head>

<body>
    <!-- navbar-->
    <?php require_once 'nav.php';
    printNavbar('restab', $projectID, $projectName); ?>

    <div class="container-fluid up-footer">
        <h1 class="text-center">Resources</h1>
        <div class="form-add col-sm-10 offset-1">
            <?php
            printErrors();
            printMessages();
            ?>
            <form class="form-inline" action="resources.php?<?php echo "pID=" . $projectID . "&pName=" . $projectName; ?>" method="post">
                <div class="form-group">
                    <label class="font-weight-bold">Resource name: </label>
                    <input type="text" class="form-control" id="text" placeholder="Resource name" name="resName" required>
                </div>
                <div class="form-group">
                    <label for="sel1">Type:</label>
                    <select class="form-control" name="resType" required>
                        <option>Work</option>
                        <option>Material</option>
                        <option>Cost</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Max commitment </label>
                    <input type="number" class="form-control" placeholder="Max" name="resCommit" min="0" max="100" required>
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">St.Rate: </label>
                    <input type="number" class="form-control" placeholder="St.Rate" name="resRate" required>
                    <div class="input-group-append">
                        <span class="input-group-text">$</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Resource</button>
            </form>
        </div>
        <hr>
        <div class="table-project col-sm-10 offset-1">
            <table id="example" class="table table-striped table-bordered table-project" style="width:100%">
                <thead>
                    <tr>
                        <th>Resource ID</th>
                        <th>Resource Name</th>
                        <th>Type</th>
                        <th>Max commitment (%)</th>
                        <th>St.Rate ($/hr)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    printRes();
                    ?>


                </tbody>
                <tfoot>
                    <tr>
                        <th>Resource ID</th>
                        <th>Resource Name</th>
                        <th>Type</th>
                        <th>Max commitment (%)</th>
                        <th>St.Rate ($/hr)</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="editTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form class="form" action="resources.php?<?php echo "pID=" . $projectID . "&pName=" . $projectName; ?>" method="post">
                        <div class="form-group">
                            <label class="font-weight-bold">Resource ID: </label>
                            <input type="text" class="form-control" id="editID" name='editID' readonly>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Resource name: </label>
                            <input type="text" class="form-control" id="editName" placeholder="Resource name" name="editedName" required>
                        </div>
                        <div class="form-group">
                            <label for="sel1">Type:</label>
                            <select class="form-control" name="editedType" id="editType" required>
                                <option>Work</option>
                                <option>Material</option>
                                <option>Cost</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Max commitment </label>
                            <input type="number" class="form-control" placeholder="Max" name="editedCommit" id="editCommit" min="0" max="100" required>
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">St.Rate: </label>
                            <input type="number" class="form-control" placeholder="St.Rate" name="editedRate" id="editRate" required>
                            <div class="input-group-append">
                                <span class="input-group-text">$</span>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                    </form>
                </div>

                <!-- Modal footer
        <div class="modal-footer">
        
        </div> -->

            </div>
        </div>
    </div>


    <!-- Footer -->
    <?php printFooter($projectID, $projectName); ?>

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
        $('body').on('click', '.btn-delete', function() {
            var id = $(this).parents('tr').find('td:eq(0)').html();
            console.log(id);
        });

        $('body').on('click', '#btn-edit', function() {

            var id = $(this).parents('tr').find('td:eq(0)').html();
            var name = $(this).parents('tr').find('td:eq(1)').html();
            var type = $(this).parents('tr').find('td:eq(2)').html();
            var max = $(this).parents('tr').find('td:eq(3)').html();
            var rate = $(this).parents('tr').find('td:eq(4)').html();

            console.log(id);
            console.log(name);
            console.log(type);
            console.log(max);
            console.log(rate);
            document.getElementById("editTitle").innerHTML = "Edit resource: " + name;
            $('#editID').val(id);
            $('#editName').val(name);
            $('#editType').val(type);
            $('#editCommit').val(max);
            $('#editRate').val(rate);

        });
    </script>


</body>

</html>