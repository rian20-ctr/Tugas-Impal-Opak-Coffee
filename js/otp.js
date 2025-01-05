
  const otpInputs = document.querySelectorAll(".code-input");

  otpInputs.forEach((input, index) => {
    input.addEventListener("input", (e) => {
      if (input.value.length === input.maxLength && index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }

      if (e.inputType === "deleteContentBackward" && index > 0 && !input.value) {
        otpInputs[index - 1].focus();
      }
    });
  });

  function verifyOTP() {
    const enteredOTP = Array.from(otpInputs)
      .map((input) => input.value)
      .join("");

    const storedOTP = sessionStorage.getItem("otp"); 

    if (enteredOTP === storedOTP) {
      alert("OTP verified successfully!");
      window.location.href = "success.html";
    } else {
      alert("Invalid OTP. Please try again.");
    }
  }

