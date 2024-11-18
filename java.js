const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('error-message');

// Add event listener to the form on submit
loginForm.addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form from submitting

    // Get the username and password values
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Basic validation
    if (username === '' || password === '') {
        errorMessage.textContent = 'Please fill out both fields.';
    } else {
        errorMessage.textContent = '';
        // Perform further actions here, like sending the data to a server
        alert('Login successful!');
    }
});