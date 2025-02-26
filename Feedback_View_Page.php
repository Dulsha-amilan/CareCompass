<?php
session_start();
include 'db/config.php';

// Get feedback data with user information
$sql = "SELECT f.id, f.message, f.created_at, u.name, u.email 
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.id DESC";

$result = $conn->query($sql);
$feedbacks = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 200px;
            margin: 0 10px 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #667eea;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .stat-label {
            color: #777;
            font-size: 0.9rem;
        }
        .feedback-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feedback-card:hover {
            transform: translateY(-5px);
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .user-name {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 3px;
        }
        .user-email {
            color: #777;
            font-size: 0.9rem;
        }
        .feedback-date {
            color: #777;
            font-size: 0.9rem;
        }
        .feedback-content {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #333;
        }
        .feedback-actions {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }
        .action-btn {
            margin-left: 10px;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .reply-btn {
            background-color: #667eea;
            color: white;
            border: none;
        }
        .reply-btn:hover {
            background-color: #5a6bd6;
        }
        .no-feedback {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .no-feedback i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .no-feedback p {
            color: #777;
            font-size: 1.2rem;
        }
        .search-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .search-input {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e1e5ea;
            font-size: 1rem;
            width: 100%;
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-comments"></i> Feedback Dashboard</h1>
            <p>View and manage all user feedback</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="stat-value"><?php echo count($feedbacks); ?></div>
                <div class="stat-label">Total Feedbacks</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value">
                    <?php 
                    $unique_users = [];
                    foreach ($feedbacks as $feedback) {
                        $unique_users[$feedback['email']] = 1;
                    }
                    echo count($unique_users); 
                    ?>
                </div>
                <div class="stat-label">Unique Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-value">
                    <?php
                    $recent_count = 0;
                    $current_date = date('Y-m-d');
                    $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
                    
                    foreach ($feedbacks as $feedback) {
                        if (isset($feedback['created_at'])) {
                            $feedback_date = date('Y-m-d', strtotime($feedback['created_at']));
                            if ($feedback_date >= $thirty_days_ago && $feedback_date <= $current_date) {
                                $recent_count++;
                            }
                        }
                    }
                    echo $recent_count;
                    ?>
                </div>
                <div class="stat-label">Last 30 Days</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-value">New</div>
                <div class="stat-label">Recent Insights</div>
            </div>
        </div>

        <div class="search-container">
            <input type="text" id="feedbackSearch" class="search-input" placeholder="Search feedbacks by user or content...">
        </div>

        <div id="feedbackList">
            <?php if (count($feedbacks) > 0): ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-card">
                        <div class="feedback-header">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($feedback['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="user-name"><?php echo htmlspecialchars($feedback['name']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($feedback['email']); ?></div>
                                </div>
                            </div>
                            <div class="feedback-date">
                                <?php echo isset($feedback['created_at']) ? date('F j, Y, g:i a', strtotime($feedback['created_at'])) : 'Date not available'; ?>
                            </div>
                        </div>
                        <div class="feedback-content">
                            <?php echo nl2br(htmlspecialchars($feedback['message'])); ?>
                        </div>
                        <div class="feedback-actions">
                            <button class="action-btn reply-btn" onclick="replyToFeedback(<?php echo $feedback['id']; ?>)">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-feedback">
                    <i class="fas fa-inbox"></i>
                    <p>No feedback submissions yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('feedbackSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const feedbackCards = document.querySelectorAll('.feedback-card');
            
            feedbackCards.forEach(card => {
                const userName = card.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = card.querySelector('.user-email').textContent.toLowerCase();
                const content = card.querySelector('.feedback-content').textContent.toLowerCase();
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || content.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Reply to feedback function
        function replyToFeedback(feedbackId) {
            // You can implement a modal or redirect to a reply form
            alert('Reply functionality can be implemented here for feedback #' + feedbackId);
        }
    </script>
</body>
</html>