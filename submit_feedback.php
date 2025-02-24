<?php
session_start();
include 'db/config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $rating = isset($_POST['rating']) ? $_POST['rating'] : null;
    $category = $_POST['category'];
    
    // You'll need to have a users table or a way to get the user_id
    // For a simple version, you could store the user's email in the users table and get the ID
    
    // Option 1: If user is logged in and you have the user_id in session
    // $user_id = $_SESSION['user_id'];
    
    // Option 2: If you want to find or create a user based on email
    $user_id = getUserIdByEmail($conn, $email, $name);
    
    // Insert feedback into database
    $sql = "INSERT INTO feedback (user_id, message, created_at) 
            VALUES (?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    
   
    
    $stmt->close();
    $conn->close();
}

// Function to get user ID by email (creates user if not exists)
function getUserIdByEmail($conn, $email, $name) {
    // Check if user exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User exists, get ID
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        // User doesn't exist, create new user
        $sql = "INSERT INTO users (name, email, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        
        // Return the ID of the new user
        return $conn->insert_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegant Feedback Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .form-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            background-color: white;
        }
        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #e1e5ea;
            font-size: 16px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 15px;
            color: #667eea;
        }
        .input-with-icon input, .input-with-icon textarea {
            padding-left: 45px;
        }
        .rating-container {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .rating-item {
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .rating-item:hover {
            transform: scale(1.1);
        }
        .rating-item i {
            font-size: 30px;
            color: #ddd;
        }
        .rating-item.selected i {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-comments"></i> We Value Your Feedback</h2>
                <p>Please share your thoughts with us to help improve our services</p>
            </div>
            <div class="form-body">
                <!-- Use the same file for form processing -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">Your Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label">Your Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="category" class="form-label">Feedback Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Please select a category</option>
                            <option value="product">Product Quality</option>
                            <option value="service">Customer Service</option>
                            <option value="website">Website Experience</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">How would you rate your experience?</label>
                        <div class="rating-container">
                            <div class="rating-item" onclick="selectRating(1)">
                                <i class="far fa-frown"></i>
                                <p>Poor</p>
                            </div>
                            <div class="rating-item" onclick="selectRating(2)">
                                <i class="far fa-meh"></i>
                                <p>Average</p>
                            </div>
                            <div class="rating-item" onclick="selectRating(3)">
                                <i class="far fa-smile"></i>
                                <p>Good</p>
                            </div>
                            <div class="rating-item" onclick="selectRating(4)">
                                <i class="far fa-grin"></i>
                                <p>Very Good</p>
                            </div>
                            <div class="rating-item" onclick="selectRating(5)">
                                <i class="far fa-grin-stars"></i>
                                <p>Excellent</p>
                            </div>
                        </div>
                        <input type="hidden" id="rating" name="rating" value="">
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Feedback</label>
                        <div class="input-with-icon">
                            <i class="fas fa-comment-alt"></i>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Please share your thoughts with us..." required></textarea>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-submit px-5">
                            <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectRating(rating) {
            // Clear all selected ratings
            document.querySelectorAll('.rating-item').forEach(item => {
                item.classList.remove('selected');
                item.querySelector('i').className = item.querySelector('i').className.replace('fas', 'far');
            });
            
            // Set the selected rating
            const selectedItem = document.querySelectorAll('.rating-item')[rating-1];
            selectedItem.classList.add('selected');
            selectedItem.querySelector('i').className = selectedItem.querySelector('i').className.replace('far', 'fas');
            
            // Set the hidden input value
            document.getElementById('rating').value = rating;
        }
    </script>
</body>
</html>