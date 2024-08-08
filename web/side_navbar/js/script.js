// Example of a simple validation script (optional)

document.querySelector('.booking-form').addEventListener('submit', function(e) {
    const amount = document.getElementById('amount').value;
    if (isNaN(amount) || amount <= 0) {
        alert('Please enter a valid amount.');
        e.preventDefault(); // Prevent form submission
    }
});
