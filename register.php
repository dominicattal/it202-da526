<?php
require(__DIR__ . "/partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ''; ?>" required />
    </div>
    <div>
        <label for="user">Username</label>
        <input type="text" name="user" value="<?php echo isset($_POST["user"]) ? htmlspecialchars($_POST["user"]) : ''; ?>" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $username = se($_POST, "user", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (empty($username)) {
        echo "username must not be empty";
        $hasError = true;
    }
    if (empty($confirm)) {
        echo "Confirm password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        echo "Passwords must match";
        $hasError = true;
    }

    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();

        function unique_email($db, $email) {
            try {
                $unique_stmt = $db->prepare("SELECT * FROM Users WHERE email=:email");
                $unique_stmt->execute([":email" => $email]);
                $result = $unique_stmt->fetch(PDO::FETCH_ASSOC) == false;
                if (!$result) {
                    echo "Email is not unique";
                    return false;
                }
                return true;
            } catch (Exception $e) {
                echo "There was a problem verifying uniqueness of email";
                return false;
            }
        }

        function unique_username($db, $username) {
            try {
                $unique_stmt = $db->prepare("SELECT * FROM Users WHERE username=:username");
                $unique_stmt->execute([":username" => $username]);
                $result = $unique_stmt->fetch(PDO::FETCH_ASSOC) == false;
                if (!$result) {
                    echo "Username is not unique";
                    return false;
                }
                return true;
            } catch (Exception $e) {
                echo "There was a problem verifying uniqueness of username";
                return false;
            }
        }

        if (unique_email($db, $email) && unique_username($db, $username)) {
            $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES (:email, :password, :username)");
            try {
                $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
                echo "<p>Successfully registered!</p>";
                echo "<p>Welcome, $email</p>";
            } catch (Exception $e) {
                echo "There was a problem registering";
                echo "<pre>" . var_export($e, true) . "</pre>";
            }
        }
    }
}
?>