const form = document.getElementById("registerForm");
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirmPassword");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const alamatInput = document.getElementById("alamat");

form.addEventListener("submit", function (event) {
  let valid = true;
  form.querySelectorAll(".invalid-feedback").forEach(function (feedback) {
    feedback.style.display = "none";
  });

  if (passwordInput.value !== confirmPasswordInput.value) {
    confirmPasswordInput.classList.add("is-invalid");
    confirmPasswordInput.nextElementSibling.style.display = "block";
    valid = false;
  }
  if (!nameInput.value) {
    nameInput.classList.add("is-invalid");
    nameInput.nextElementSibling.style.display = "block";
    valid = false;
  }

  if (!emailInput.value || !validateEmail(emailInput.value)) {
    emailInput.classList.add("is-invalid");
    emailInput.nextElementSibling.style.display = "block";
    valid = false;
  }

  if (!alamatInput.value) {
    alamatInput.classList.add("is-invalid");
    alamatInput.nextElementSibling.style.display = "block";
    valid = false;
  }

  if (!valid) {
    event.preventDefault();
  }
});

function validateEmail(email) {
  const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  return regex.test(email);
}
