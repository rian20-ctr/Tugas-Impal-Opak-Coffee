(function () {
  "use strict";
  const codeInputs = document.querySelectorAll(".code-input");

  function setupCodeInputs() {
    codeInputs.forEach((input, index) => {
      input.addEventListener("input", (e) => {
        if (e.target.value && index < codeInputs.length - 1) {
          codeInputs[index + 1].focus();
        }
      });

      input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !e.target.value && index > 0) {
          codeInputs[index - 1].focus();
        }
      });
    });
  }

  function setupFormValidation() {
    var forms = document.querySelectorAll(".needs-validation");

    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener(
        "submit",
        function (event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          let isComplete = true;
          codeInputs.forEach((input) => {
            if (!input.value) {
              isComplete = false;
              input.classList.add("is-invalid");
            } else {
              input.classList.remove("is-invalid");
            }
          });

          if (!isComplete) {
            event.preventDefault();
            form.querySelector(".invalid-feedback").style.display = "block";
          } else {
            form.querySelector(".invalid-feedback").style.display = "none";
          }

          form.classList.add("was-validated");
        },
        false
      );
    });
  }
  function setLoginData() {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const rememberMeCheckbox = document.getElementById("remember");

    const storedEmail = localStorage.getItem("email");
    const storedPassword = localStorage.getItem("password");
    const rememberMe = localStorage.getItem("rememberMe");

    if (storedEmail) {
      emailInput.value = storedEmail;
    }

    if (rememberMe === "true") {
      if (storedPassword) {
        passwordInput.value = storedPassword;
      }
      rememberMeCheckbox.checked = true;
    } else {
      passwordInput.value = "";
      rememberMeCheckbox.checked = false;
    }
  }

  function saveLoginData(event) {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const rememberMeCheckbox = document.getElementById("remember");

    if (rememberMeCheckbox.checked) {
      localStorage.setItem("email", emailInput.value);
      localStorage.setItem("password", passwordInput.value);
      localStorage.setItem("rememberMe", "true");
    } else {
      localStorage.removeItem("email");
      localStorage.removeItem("password");
      localStorage.removeItem("rememberMe");
    }
  }

  function init() {
    setupCodeInputs();
    setupFormValidation();
    setLoginData();

    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
      loginForm.addEventListener("submit", saveLoginData);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
