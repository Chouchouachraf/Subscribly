// Animation utilities
const animate = {
    fadeIn: (element, duration = 500) => {
        element.style.opacity = 0;
        element.style.display = 'block';
        element.style.transition = `opacity ${duration}ms ease-in-out`;
        setTimeout(() => element.style.opacity = 1, 10);
    },
    
    slideDown: (element, duration = 500) => {
        element.style.display = 'block';
        const height = element.scrollHeight;
        element.style.height = '0px';
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease-in-out`;
        setTimeout(() => element.style.height = height + 'px', 10);
    },
    
    slideUp: (element, duration = 500) => {
        element.style.height = element.scrollHeight + 'px';
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease-in-out`;
        setTimeout(() => element.style.height = '0px', 10);
        setTimeout(() => element.style.display = 'none', duration);
    }
};

// Form validation and dynamic feedback
const forms = {
    validate: (form) => {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            const wrapper = input.closest('.form-group');
            const errorElement = wrapper.querySelector('.error-message') || document.createElement('div');
            errorElement.className = 'error-message text-sm text-red-600 mt-1 animate-fade-in';
            
            if (!input.value.trim()) {
                isValid = false;
                errorElement.textContent = `${input.getAttribute('data-label') || 'This field'} is required`;
                if (!wrapper.querySelector('.error-message')) {
                    wrapper.appendChild(errorElement);
                }
                input.classList.add('border-red-500');
            } else {
                if (wrapper.querySelector('.error-message')) {
                    wrapper.removeChild(errorElement);
                }
                input.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    },
    
    setupDynamicValidation: () => {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!forms.validate(form)) {
                    e.preventDefault();
                }
            });
            
            form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', () => {
                    const wrapper = input.closest('.form-group');
                    const errorElement = wrapper.querySelector('.error-message');
                    if (errorElement) {
                        wrapper.removeChild(errorElement);
                    }
                    input.classList.remove('border-red-500');
                });
            });
        });
    }
};

// Dynamic notifications
const notifications = {
    show: (message, type = 'success', duration = 3000) => {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        setTimeout(() => {
            notification.style.transform = 'translateX(full)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, duration);
    }
};

// Category selector with animations
const categorySelector = {
    init: () => {
        const categories = document.querySelectorAll('.category-card');
        categories.forEach(card => {
            card.addEventListener('click', () => {
                categories.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                
                // Animate icon
                const icon = card.querySelector('svg');
                icon.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    icon.style.transform = 'scale(1)';
                }, 200);
            });
        });
    }
};

// Charts and data visualization
const charts = {
    createSpendingChart: (canvas, data) => {
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.category),
                datasets: [{
                    data: data.map(d => d.amount),
                    backgroundColor: [
                        '#4F46E5', '#10B981', '#F59E0B', '#EF4444',
                        '#6366F1', '#8B5CF6', '#EC4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
};

// Initialize all components
document.addEventListener('DOMContentLoaded', () => {
    forms.setupDynamicValidation();
    categorySelector.init();
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        tippy(element, {
            content: element.getAttribute('data-tooltip'),
            animation: 'scale'
        });
    });
    
    // Add hover effects to cards
    document.querySelectorAll('.hover-lift').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
});
