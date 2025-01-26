<?php

function getCategoryIcon($category) {
    $category = strtolower($category);
    $iconPath = "/Subscribly/public/images/icons/";
    
    $icons = [
        'streaming' => 'streaming.svg',
        'gaming' => 'gaming.svg',
        'music' => 'music.svg',
        'software' => 'software.svg',
        'fitness' => 'fitness.svg',
        'education' => 'education.svg',
        'news' => 'news.svg',
        // Default icon for unknown categories
        'default' => 'software.svg'
    ];
    
    $iconFile = isset($icons[$category]) ? $icons[$category] : $icons['default'];
    return $iconPath . $iconFile;
}

function renderCategoryIcon($category, $classes = '') {
    $iconPath = getCategoryIcon($category);
    return '<img src="' . htmlspecialchars($iconPath) . '" alt="' . htmlspecialchars(ucfirst($category)) . '" class="' . htmlspecialchars($classes) . '">';
}

// Function to get all available categories with their icons
function getAllCategories() {
    return [
        ['id' => 'streaming', 'name' => 'Streaming', 'description' => 'Video and entertainment services'],
        ['id' => 'gaming', 'name' => 'Gaming', 'description' => 'Gaming platforms and services'],
        ['id' => 'music', 'name' => 'Music', 'description' => 'Music streaming and audio services'],
        ['id' => 'software', 'name' => 'Software', 'description' => 'Software and applications'],
        ['id' => 'fitness', 'name' => 'Fitness', 'description' => 'Health and fitness apps'],
        ['id' => 'education', 'name' => 'Education', 'description' => 'Learning platforms and courses'],
        ['id' => 'news', 'name' => 'News', 'description' => 'News and magazine subscriptions']
    ];
}

// Function to get category color class
function getCategoryColorClass($category) {
    $colors = [
        'streaming' => 'text-red-500',
        'gaming' => 'text-purple-500',
        'music' => 'text-green-500',
        'software' => 'text-blue-500',
        'fitness' => 'text-orange-500',
        'education' => 'text-yellow-500',
        'news' => 'text-gray-500',
        'default' => 'text-indigo-500'
    ];
    
    return isset($colors[$category]) ? $colors[$category] : $colors['default'];
}
