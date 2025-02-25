<?php
header('Content-Type: text/html');

// Database connection parameters
$host = 'localhost';
$db = 'bridges';
$user = 'postgres';
$pass = 'mysecretpassword';

// Create a connection to the PostgreSQL database
$dsn = "pgsql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Get the fkStructure from the query parameter
$fkStructure = isset($_GET['fkStructure']) ? intval($_GET['fkStructure']) : 0;

// SQL query to get the inspection records for the specific bridge
$sql = "
SELECT *
FROM inspections
WHERE fkstructure = :fkStructure";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':fkStructure', $fkStructure, PDO::PARAM_INT);
$stmt->execute();

$inspections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="description" content="PONTI platform">
    <meta name="author" content="Federica Gaspari">
    <title>PONTI | Inspection History</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/app.css">

    <link rel="apple-touch-icon" sizes="76x76" href="../img/favicon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../img/favicon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../img/favicon-152.png">
    <link rel="icon" sizes="196x196" href="../img/favicon-196.png">
    <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <div class="navbar-icon-container">
                    <a href="#" class="navbar-icon pull-right visible-xs" id="nav-btn"><i
                            class="fa fa-bars fa-lg white"></i></a>
                    <a href="#" class="navbar-icon pull-right visible-xs" id="sidebar-toggle-btn"><i
                            class="fa fa-search fa-lg white"></i></a>
                </div>
                <a class="navbar-brand" href="#">Inspection History for Bridge ID:
                    <?php echo htmlspecialchars($fkStructure); ?></a>
            </div>
            <div class="navbar-collapse collapse">
                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group has-feedback">
                        <input id="searchbox" type="text" placeholder="Search" class="form-control">
                        <span id="searchicon" class="fa fa-search form-control-feedback"></span>
                    </div>
                </form>
            </div><!--/.navbar-collapse -->
        </div>
    </div>
    <div id="container">
        <?php if ($inspections): ?>
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Technician</th>
                        <th>Comments</th>
                        <th>3D Viewer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inspections as $inspection): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inspection['date']); ?></td>
                            <td><?php echo htmlspecialchars($inspection['technician']); ?></td>
                            <td><?php echo htmlspecialchars($inspection['note']); ?></td>
                            <td><button class="viewer-button" data-id="<?php echo $inspection['id']; ?>">3D Data viewer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No inspection records found for this bridge.</p>
        <?php endif; ?>
        <script>
            document.querySelectorAll('.viewer-button').forEach(button => {
                button.addEventListener('click', function () {
                    var inspectionId = this.getAttribute('data-id');
                    window.open('../../viewer/index.php?inspectionId=' + inspectionId, '_blank');
                    console.log("Opening the 3D viewer for virtual inspection...");
                });
            });
        </script>
    </div>

</body>

</html>