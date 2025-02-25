<?php
header('Content-Type: application/json');

// Load database credentials from config.php
$config = include 'config.php';

$host = $config['DB_HOST'];
$db = $config['DB_NAME'];
$user = $config['DB_USER'];
$pass = $config['DB_PASS'];

// Create a connection to the PostgreSQL database
$dsn = "pgsql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// SQL query to get the GeoJSON data from the PostGIS table
$sql = "
SELECT json_build_object(
    'type', 'FeatureCollection',
    'features', json_agg(ST_AsGeoJSON(t.*)::json)
) AS geojson
FROM (
    SELECT id, name, type, owner, ST_Transform(geom, 4326) AS geometry
    FROM structures
) t";

$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['geojson'];
