<?php
require_once '../includes/Auth.php';
require_once '../includes/utils.php';
require_once '../includes/theme.php';

$pageTitle = 'Register';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!validate_email($email)) $errors[] = "Invalid email format";
    if (empty($password)) $errors[] = "Password is required";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match";

    if (empty($errors)) {
        $auth = new Auth();
        [$success, $message] = $auth->register($name, $email, $password);
        
        if ($success) {
            set_flash_message('success', $message);
            redirect('/login');
        } else {
            set_flash_message('error', $message);
        }
    } else {
        set_flash_message('error', implode('<br>', $errors));
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Subscribly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full bg-pattern">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md animate-fade-in">
            <img class="mx-auto h-16 w-auto" src="/Subscribly/public/images/logo.png" alt="Subscribly">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account?
                <a href="/Subscribly/public/login" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-150">
                    Sign in
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md animate-slide-up">
            <div class="bg-white py-8 px-4 shadow-xl shadow-indigo-50 sm:rounded-lg sm:px-10 border border-gray-100">
                <?php 
                $flash = get_flash_message();
                if ($flash): 
                ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $flash['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'; ?> animate-fade-in">
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/Subscribly/public/register" class="space-y-6">
                    <div>
                        <label for="name" class="<?php echo getLabelClasses(); ?>">
                            Full name
                        </label>
                        <input type="text" id="name" name="name" autocomplete="name" required
                               class="<?php echo getInputClasses(); ?>"
                               placeholder="John Doe">
                    </div>

                    <div>
                        <label for="email" class="<?php echo getLabelClasses(); ?>">
                            Email address
                        </label>
                        <input type="email" id="email" name="email" autocomplete="email" required
                               class="<?php echo getInputClasses(); ?>"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="password" class="<?php echo getLabelClasses(); ?>">
                            Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="<?php echo getInputClasses(); ?>"
                               placeholder="••••••••">
                        <p class="mt-1 text-sm text-gray-500">Must be at least 8 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="<?php echo getLabelClasses(); ?>">
                            Confirm password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="<?php echo getInputClasses(); ?>"
                               placeholder="••••••••">
                    </div>

                    <div>
                        <button type="submit" class="<?php echo getPrimaryButtonClasses(); ?> w-full flex justify-center group">
                            <span>Create account</span>
                            <svg class="ml-2 -mr-1 w-5 h-5 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Or continue with
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div>
                            <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12.0003 2C6.47731 2 2.00031 6.477 2.00031 12C2.00031 16.991 5.65731 21.128 10.4373 21.878V14.89H7.89831V12H10.4373V9.797C10.4373 7.291 11.9323 5.907 14.2153 5.907C15.3103 5.907 16.4543 6.102 16.4543 6.102V8.562H15.1913C13.9503 8.562 13.5633 9.333 13.5633 10.124V12H16.3363L15.8933 14.89H13.5633V21.878C18.3433 21.128 22.0003 16.991 22.0003 12C22.0003 6.477 17.5233 2 12.0003 2Z"/>
                                </svg>
                            </a>
                        </div>

                        <div>
                            <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
