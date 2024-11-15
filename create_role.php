<?php
//note we need to go up 1 more directory
require(__DIR__ . "/partials/nav.php");

if (!has_role("Admin")) {
    echo "You don't have permission to view this page";
    die(header("Location: home.php"));
}

if (isset($_POST["name"]) && isset($_POST["description"])) {
    $name = se($_POST, "name", "", false);
    $desc = se($_POST, "description", "", false);
    if (empty($name)) {
        echo "Name is required";
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Roles (name, description, is_active) VALUES(:name, :desc, 1)");
        try {
            $stmt->execute([":name" => $name, ":desc" => $desc]);
            echo "Successfully created role $name!";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                echo "A role with this name already exists, please try another";
            } else {
                echo var_export($e->errorInfo, true);
            }
        }
    }
}
?>
<h1>Create Role</h1>
<form method="POST">
    <div>
        <label for="name">Name</label>
        <input id="name" name="name" required />
    </div>
    <div>
        <label for="d">Description</label>
        <textarea name="description" id="d"></textarea>
    </div>
    <input type="submit" value="Create Role" />
</form>