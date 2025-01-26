<?php
// Theme configuration
$theme = [
    'colors' => [
        'primary' => 'indigo',
        'secondary' => 'purple',
        'accent' => 'pink',
        'success' => 'emerald',
        'warning' => 'amber',
        'error' => 'red'
    ]
];

// Common CSS classes
function getLabelClasses() {
    return 'block text-sm font-medium text-gray-700 mb-1';
}

function getInputClasses() {
    return 'block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors duration-200';
}

function getPrimaryButtonClasses() {
    return 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200';
}

function getSecondaryButtonClasses() {
    return 'inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200';
}

function getDangerButtonClasses() {
    return 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200';
}

function getCardClasses() {
    return 'bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100';
}

function getBadgeClasses($type = 'default') {
    $classes = [
        'default' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800'
    ];
    
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    return $baseClasses . ' ' . ($classes[$type] ?? $classes['default']);
}

function getAlertClasses($type = 'info') {
    $classes = [
        'info' => 'bg-blue-50 text-blue-700 border-blue-100',
        'success' => 'bg-green-50 text-green-700 border-green-100',
        'warning' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
        'error' => 'bg-red-50 text-red-700 border-red-100'
    ];
    
    return 'p-4 rounded-lg border ' . ($classes[$type] ?? $classes['info']);
}

// Add common CSS styles
function getCommonStyles() {
    return '
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-slide-up {
            animation: slideUp 0.5s ease-out;
        }
        
        .animate-scale {
            animation: scale 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes scale {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .hover-scale {
            transition: transform 0.2s ease-out;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        .hover-lift {
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%234F46E5\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    ';
}

// Function to render loading spinner
function getLoadingSpinner($size = 'md', $color = 'indigo') {
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8'
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    
    return '
    <svg class="animate-spin ' . $sizeClass . ' text-' . $color . '-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    ';
}

// Function to format currency
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Function to format date
function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}
?>
