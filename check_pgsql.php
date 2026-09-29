<?php
$host     = "127.0.0.200";       // or IP like 127.0.0.1
$port     = "5432";            // default PostgreSQL port
$dbname   = "erpaicountly_erpunivaic";
$user     = "erpaicountly_uerpunivaic";
$password = "I9dQ~XE}ohiRykqs";

// Create connection string (DSN)
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Try connecting
$conn = pg_connect($conn_string);
$ff = pg_query($conn, "ALTER TABLE drftvchrec 
  ALTER COLUMN draft_vch_rec_id 
  SET DEFAULT nextval('drftvchrec_draft_vch_rec_id_seq');");
print_r($ff);
if (!$conn) {
    echo "❌ Connection failed.";
} else {
    echo "✅ Connected successfully to PostgreSQL database!";
    
    // Optional: run a test query
    //$result = pg_query($conn, "SELECT version();");
    //$row = pg_fetch_row($result);
    //echo "<br><br>PostgreSQL version: " . $row[0];
    
    //pg_close($conn); // Close connection
}
?>