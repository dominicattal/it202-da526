<?php
require(__DIR__ . "/partials/nav.php");
?>
<h1>Home</h1>
<?php
if (is_logged_in()) {
    echo "<p>Welcome, " . get_username() . "</p>";
    if (isset($_SESSION["access"])) {
        echo "<p>You do not have access to that page</p>";
        unset($_SESSION["access"]);
    }
} else {
    echo "<p>You're not logged in</p>";
}
?>