<!-- conversation.php -->

<?php
// Include the database connection file
require_once '../includes/db_connection.php';

// Check if the user is logged in. Redirect to login page if not.
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the user ID from the session
$current_user_id = $_SESSION['user_id'];

// Check if the conversation ID is provided in the URL
if (!isset($_GET['conversation_id'])) {
    header('Location: conversations.php'); // Redirect to conversations page if no conversation ID provided
    exit;
}

$conversation_id = $_GET['conversation_id'];

try {
    // Prepare a select statement to retrieve the conversation details
    $stmt = $pdo->prepare("SELECT user1_id, user2_id FROM conversations WHERE conversation_id = :conversation_id");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);

    // Execute the prepared statement
    $stmt->execute();

    // Fetch the conversation details as an associative array
    $conversation = $stmt->fetch();
} catch (PDOException $e) {
    // Handle any database errors
    die("Error fetching conversation: " . $e->getMessage());
}

// Check if the conversation exists and involves the current user
if (!$conversation || ($conversation['user1_id'] !== $current_user_id && $conversation['user2_id'] !== $current_user_id)) {
    header('Location: conversations.php'); // Redirect to conversations page if conversation doesn't exist or doesn't involve the user
    exit;
}

// Get the other user ID in the conversation
$other_user_id = ($conversation['user1_id'] === $current_user_id) ? $conversation['user2_id'] : $conversation['user1_id'];

try {
    // Prepare a select statement to retrieve the messages in the conversation
    $stmt = $pdo->prepare("SELECT message_id, sender_id, content, created_at FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);

    // Execute the prepared statement
    $stmt->execute();

    // Fetch all messages in the conversation as an associative array
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle any database errors
    die("Error fetching messages: " . $e->getMessage());
}

try {
    // Prepare a select statement to retrieve the user's profile information
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $other_user_id, PDO::PARAM_INT);

    // Execute the prepared statement
    $stmt->execute();

    // Fetch the user's profile as an associative array
    $other_user = $stmt->fetch();
} catch (PDOException $e) {
    // Handle any database errors
    die("Error fetching user data: " . $e->getMessage());
}

// Process sending new messages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the message content is not empty
    if (isset($_POST['message_content']) && !empty(trim($_POST['message_content']))) {
        $message_content = trim($_POST['message_content']);

        try {
            // Prepare an insert statement to add the new message to the conversation
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, content, created_at) VALUES (:conversation_id, :sender_id, :content, NOW())");
            $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
            $stmt->bindParam(':sender_id', $current_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $message_content, PDO::PARAM_STR);

            // Execute the prepared statement
            $stmt->execute();
        } catch (PDOException $e) {
            // Handle any database errors
            die("Error sending message: " . $e->getMessage());
        }

        // Redirect to the same page to avoid resubmission of the form
        header("Location: conversation.php?conversation_id=$conversation_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Conversation with <?php echo $other_user['username']; ?></title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Connectify</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="explore.php">Explore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post.php">Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="conversations.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4"><?php echo $other_user['username']; ?></h2>
        <div class="mb-3">
            <?php foreach ($messages as $message) : ?>
                <p class="<?php echo ($message['sender_id'] === $current_user_id) ? 'text-end' : 'text-start'; ?>">
                    <?php echo ($message['sender_id'] === $current_user_id) ? 'You' : $other_user['username']; ?>: <?php echo $message['content']; ?>
                </p>
            <?php endforeach; ?>
        </div>

        <!-- Message Form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?conversation_id=' . $conversation_id); ?>" method="post">
            <div class="mb-3">

                <textarea class="form-control" id="message_content" name="message_content" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>

    <!-- Add Bootstrap JS (Popper.js and Bootstrap's JavaScript) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>