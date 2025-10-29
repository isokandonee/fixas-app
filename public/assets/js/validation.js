// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    // Add custom validation styles
    form.classList.add('needs-validation');

    // Password strength indicator
    const passwordInput = form.querySelector('input[type="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });
    }

    // Real-time email validation
    const emailInput = form.querySelector('input[type="email"]');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            validateEmail(this);
        });
    }

    // Form submission handler
    form.addEventListener('submit', function(event) {
        if (!validateFormData(this)) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
}

// Validate email format
function validateEmail(input) {
    const email = input.value;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const isValid = emailRegex.test(email);
    
    updateValidationUI(input, isValid, 'Please enter a valid email address');
    return isValid;
}

// Password strength checker
function updatePasswordStrength(password) {
    const strengthMeter = document.getElementById('password-strength');
    if (!strengthMeter) return;

    const strength = calculatePasswordStrength(password);
    const strengthText = getStrengthText(strength);
    const strengthClass = getStrengthClass(strength);

    strengthMeter.textContent = strengthText;
    strengthMeter.className = 'password-strength ' + strengthClass;
}

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^A-Za-z0-9]/)) strength++;

    return strength;
}

// Get strength text based on score
function getStrengthText(strength) {
    const texts = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
    return texts[strength - 1] || 'Very Weak';
}

// Get strength class for styling
function getStrengthClass(strength) {
    const classes = ['very-weak', 'weak', 'medium', 'strong', 'very-strong'];
    return classes[strength - 1] || 'very-weak';
}

// Validate form data before submission
function validateFormData(form) {
    let isValid = true;
    
    // Validate required fields
    form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            updateValidationUI(input, false, 'This field is required');
        }
    });

    // Validate email
    const emailInput = form.querySelector('input[type="email"]');
    if (emailInput && !validateEmail(emailInput)) {
        isValid = false;
    }

    // Validate password
    const passwordInput = form.querySelector('input[name="password"]');
    const confirmInput = form.querySelector('input[name="cpassword"]');
    
    if (passwordInput && confirmInput) {
        if (calculatePasswordStrength(passwordInput.value) < 3) {
            isValid = false;
            updateValidationUI(passwordInput, false, 'Password is too weak');
        }
        
        if (passwordInput.value !== confirmInput.value) {
            isValid = false;
            updateValidationUI(confirmInput, false, 'Passwords do not match');
        }
    }

    return isValid;
}

// Update validation UI
function updateValidationUI(input, isValid, message) {
    input.classList.toggle('is-valid', isValid);
    input.classList.toggle('is-invalid', !isValid);

    let feedback = input.nextElementSibling;
    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.insertBefore(feedback, input.nextSibling);
    }
    feedback.textContent = message;
}

// Anti-spam measures
function addAntiSpamMeasures() {
    // Add honeypot field
    const honeypot = document.createElement('input');
    honeypot.type = 'text';
    honeypot.style.display = 'none';
    honeypot.name = 'website'; // Common honeypot field name
    document.querySelector('form').appendChild(honeypot);

    // Track form filling time
    const startTime = new Date();
    document.querySelector('form').addEventListener('submit', function(event) {
        const endTime = new Date();
        const timeDiff = endTime - startTime;
        
        // If form is filled too quickly (less than 5 seconds), likely a bot
        if (timeDiff < 5000) {
            event.preventDefault();
            return false;
        }
    });
}

// Initialize validation on page load
document.addEventListener('DOMContentLoaded', function() {
    validateForm('registrationForm');
    addAntiSpamMeasures();
});