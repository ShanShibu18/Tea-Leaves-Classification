document.getElementById('signupForm').addEventListener('submit', function(event) {
    var form = this;
    event.preventDefault(); // Prevent form from submitting normally

    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            var errorMessageDiv = document.getElementById('error-message');

            if (response.status === "success") {
                form.reset();
                document.getElementById('successModal').style.display = 'block';
                errorMessageDiv.textContent = ''; // Clear any previous error messages
            } else if (response.status === "error") {
                errorMessageDiv.textContent = response.message; // Display error message from server
            }
        } else if (xhr.readyState === 4) {
            alert('Error: ' + xhr.responseText);
        }
    };
    xhr.send(new URLSearchParams(new FormData(form)).toString());
});

var modal = document.getElementById('successModal');
var closeModal = document.getElementsByClassName('close')[0];
var loginBtn = document.getElementById('loginBtn');

closeModal.onclick = function() {
    modal.style.display = 'none';
}

loginBtn.onclick = function() {
    window.location.href = 'login.html';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
