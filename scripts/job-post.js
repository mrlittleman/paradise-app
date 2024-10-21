document.getElementById("submissionForm").addEventListener("submit", function(event) {
    event.preventDefault();

    var xhr = new XMLHttpRequest();
    var formData = new FormData(this);

    xhr.open("POST", this.action, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById("successMessage").style.display = "block";
        }
    };
    xhr.send(formData);
});

function closePopup() {
    document.getElementById("successMessage").style.display = "none";
    document.getElementById("submissionForm").reset(); // Clear all form fields
}
