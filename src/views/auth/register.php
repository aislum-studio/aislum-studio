<?php
/**
 * Registration View
 * 
 * Displays the registration form for new users
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Aislum Studio</title>
    
    <!-- PICO CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/custom.css">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
        }
        
        main {
            width: 100%;
            max-width: 450px;
        }
        
        article {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo h1 {
            margin: 0;
            color: #667eea;
        }
        
        .logo p {
            margin: 0.5rem 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        form {
            margin-top: 1.5rem;
        }
        
        button {
            width: 100%;
            margin-top: 1rem;
        }
        
        .form-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .form-links small {
            display: block;
            margin: 0.5rem 0;
        }
        
        .form-links a {
            color: #667eea;
            text-decoration: none;
        }
        
        .form-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <main>
        <article>
            <div class="logo">
                <h1>Aislum Studio</h1>
                <p>Create Your Account</p>
            </div>
            
            <?php
            // Display flash messages
            $regError = getFlashMessage('registration_error');
            $regSuccess = getFlashMessage('registration_success');
            
            if ($regError) {
                echo '<div class="alert alert-error">' . $regError['message'] . '</div>';
            }
            
            if ($regSuccess) {
                echo '<div class="alert alert-success">' . $regSuccess['message'] . '</div>';
            }
            ?>
            
            <form action="?page=auth&action=register" method="POST" id="registerForm">
                <div>
                    <label for="full_name">
                        <strong>Full Name</strong>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            placeholder="Enter your full name" 
                            required
                        >
                    </label>
                </div>
                
                <div>
                    <label for="username">
                        <strong>Username</strong>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Choose a username" 
                            required
                            autocomplete="username"
                        >
                    </label>
                </div>
                
                <div>
                    <label for="email">
                        <strong>Email</strong>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address" 
                            required
                            autocomplete="email"
                        >
                    </label>
                </div>
                
                <div>
                    <label for="password">
                        <strong>Password</strong>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Create a password" 
                            required
                            autocomplete="new-password"
                        >
                        <div class="password-requirements">
                            Password must be at least 6 characters long.
                        </div>
                    </label>
                </div>
                
                <div>
                    <label for="confirm_password">
                        <strong>Confirm Password</strong>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm your password" 
                            required
                            autocomplete="new-password"
                        >
                    </label>
                </div>
                
                <button type="submit" class="contrast">Register</button>
            </form>
            
            <div class="form-links">
                <small>
                    Already have an account? <a href="?page=auth&action=login">Login here</a>
                </small>
            </div>
        </article>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                var password = $('#password').val();
                var confirmPassword = $('#confirm_password').val();
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long.');
                    return false;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
