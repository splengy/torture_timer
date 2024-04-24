document.addEventListener('DOMContentLoaded', function() {
    // Initialize the current time
    updateTime();
    setInterval(updateTime, 1000);

    // File upload size restriction
    const uploadField = document.getElementById('profile_picture');
    uploadField.onchange = function() {
        if (this.files[0].size > 307200) { // 300 KB limit
            alert("File is too big!");
            this.value = "";
        };
    };

    // Hover effects on buttons using CSS should also be considered for better performance
    const buttons = document.querySelectorAll('.widget button');
    buttons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#0056b3'; // Change button color on hover
        });
        button.addEventListener('mouseout', function() {
            this.style.backgroundColor = '#0084ff'; // Revert button color after hover
        });
    });

    // Edit form pre-population from table rows
    const editButtons = document.querySelectorAll('input[name="action"][value="edit"]');
    editButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            populateEditForm(this);
        });
    });
});

function populateEditForm(button) {
    const row = button.closest('tr');
    document.querySelector('form input[name="id"]').value = row.querySelector('input[name="id"]').value;
    document.querySelector('form input[name="exercise_number"]').value = row.cells[0].innerText;
    document.querySelector('form input[name="name"]').value = row.cells[1].innerText;
    document.querySelector('form input[name="description"]').value = row.cells[2].innerText;
    
    // Reset all checkboxes
    document.querySelectorAll('form input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Set checkboxes based on values
    row.cells[3].innerText.split(', ').forEach(value => {
        const checkbox = document.querySelector(`form input[name="muscle_groups[]"][value="${value}"]`);
        if (checkbox) checkbox.checked = true;
    });

    row.cells[4].innerText.split(', ').forEach(value => {
        const checkbox = document.querySelector(`form input[name="impact_level[]"][value="${value}"]`);
        if (checkbox) checkbox.checked = true;
    });

    // Media URL, if you want to handle changes or display existing file
    const mediaUrl = row.cells[5].innerText;
    document.querySelector('form input[name="existing_media"]').value = mediaUrl;
    document.querySelector('form [name="action"]').value = 'update'; // Change form to update mode
}

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('current-time').textContent = timeString;
}

function toggleMenu() {
    const menu = document.getElementById('side-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function toggleLoginForm() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registrationForm');
    const loginButton = document.getElementById('loginButton');
    const registerButton = document.getElementById('registerButton');

    if (loginForm.style.display === 'none') {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        loginButton.style.display = 'none';
        registerButton.style.display = 'inline-block';
    } else {
        loginForm.style.display = 'none';
        loginButton.style.display = 'inline-block';
    }
}

function toggleRegisterForm() {
    const registerForm = document.getElementById('registrationForm');
    const loginForm = document.getElementById('loginForm');
    const registerButton = document.getElementById('registerButton');
    const loginButton = document.getElementById('loginButton');

    if (registerForm.style.display === 'none') {
        registerForm.style.display = 'block';
        loginForm.style.display = 'none';
        registerButton.style.display = 'none';
        loginButton.style.display = 'inline-block';
    } else {
        registerForm.style.display = 'none';
        registerButton.style.display = 'inline-block';
    }
}

