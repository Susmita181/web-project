<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Dashboard</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
    <style>
        #mainSection {
            display: flex;
            justify-content: space-between;
        }
        #profileSection, #groupSection {
            width: 48%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .profile-img img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .groups {
            margin-top: 20px;
        }
        .group {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div id="headerSection">
        <button id="backButton" onclick="window.location.href='../'">Back</button>
        <button id="logoutButton" onclick="window.location.href='../api/logout.php'">Logout</button>
        <h1>Online Voting System</h1>
    </div>
    <hr>
    
    <div id="mainSection">
        <div id="profileSection">
            <div class="profile-img">
                <img src="../api/uploads/<?php echo $_SESSION['user_image']; ?>" alt="Profile Image">
            </div>
            <div class="profile-info">
                <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
                <p><strong>Mobile:</strong> <?php echo $_SESSION['user_mobile']; ?></p>
                <p><strong>Address:</strong> <?php echo $_SESSION['user_address']; ?></p>
                <p><strong>Status:</strong> <?php echo $_SESSION['user_status'] ? 'Voted' : 'Not Voted'; ?></p>
            </div>
        </div>
        
        <div id="groupSection">
            <?php if ($_SESSION['user_role'] == '1' && $_SESSION['user_status'] == 0) { ?>
                <div class="groups">
                    <h2>Available Groups</h2>
                    <?php
                    include("../api/connect.php");
                    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role='2'");
                    while ($group = mysqli_fetch_assoc($groups)) {
                        echo '<div class="group">
                                <p><strong>Group Name:</strong> ' . $group['name'] . '</p>
                                <p><strong>Votes:</strong> ' . $group['votes'] . '</p>
                                <button onclick="voteGroup(' . $group['id'] . ')">Vote</button>
                              </div>';
                    }
                    ?>
                </div>
            <?php } else if ($_SESSION['user_role'] == '1' && $_SESSION['user_status'] == 1) { ?>
                <div class="voted">
                    <h2>You have already voted!</h2>
                </div>
            <?php } else if ($_SESSION['user_role'] == '2') { ?>
                <div class="group-info">
                    <h2>Your Group Status</h2>
                    <p>Votes Received: <?php echo $_SESSION['user_votes']; ?></p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function voteGroup(groupId) {
            if (confirm("Are you sure you want to vote for this group?")) {
                window.location.href = "../api/vote.php?group_id=" + groupId;
            }
        }
    </script>
</body>
</html>