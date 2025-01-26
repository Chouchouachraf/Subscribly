<?php
$pageTitle = 'Welcome';
ob_start();
?>

<div class="text-center py-12">
    <h1 class="text-4xl font-bold text-accent mb-4">Welcome to Subscribly</h1>
    <p class="text-xl text-gray-600 mb-8">Track and manage all your subscriptions in one place</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto mt-12">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-accent mb-4">Track Subscriptions</h3>
            <p class="text-gray-600">Keep all your subscriptions organized in one place</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-accent mb-4">Get Reminders</h3>
            <p class="text-gray-600">Never miss a renewal with timely notifications</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-accent mb-4">Save Money</h3>
            <p class="text-gray-600">Identify and cancel unused subscriptions</p>
        </div>
    </div>
    
    <div class="mt-12">
        <a href="/Subscribly/public/register" class="bg-accent text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-opacity-90 transition-colors">
            Get Started
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
