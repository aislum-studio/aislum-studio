<?php
/**
 * Login View
 * 
 * Displays the login form for users to authenticate
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aislum Studio</title>
    
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
        }
        
        main {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }
        
        article {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
    </style>
</head>
<body>
    <main>
        <article>
            <div class="logo">
                <h1>Aislum Studio</h1>
                <p>Multimedia Design & Document Management</p>
            </div>
            
            <?php
            // Display flash messages
            $loginError = getFlashMessage('login_error');
            $loginSuccess = getFlashMessage('login_success');
            
            if ($loginError) {
                echo '<div class="alert alert-error">' . $loginError['message'] . '</div>';
            }
            
            if ($loginSuccess) {
                echo '<div class="alert alert-success">' . $loginSuccess['message'] . '</div>';
            }
            ?>
            
            <form action="?page=auth&action=login" method="POST">
                <div>
                    <label for="username_email">
                        <strong>Username or Email</strong>
                        <input 
                            type="text" 
                            id="username_email" 
                            name="username_email" 
                            placeholder="Enter your username or email" 
                            required 
                            autocomplete="username"
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
                            placeholder="Enter your password" 
                            required 
                            autocomplete="current-password"
                        >
                    </label>
                </div>
                
                <button type="submit" class="contrast">Login</button>
            </form>
            
            <div class="form-links">
                <small>
                    <a href="#">Forgot your password?</a>
                </small>
                <small>
                    Don't have an account? <a href="?page=auth&action=register">Register here</a>
                </small>
            </div>
        </article>
    </main>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
