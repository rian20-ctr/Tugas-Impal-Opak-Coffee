document.getElementById("sendResetLink").addEventListener("click", function () {
  const emailField = document.getElementById("email");
  const email = emailField.value;

  if (!email) {
    alert("Please enter a valid email address");
    return;
  }

  const token = generateToken();
  const resetLink = `http://localhost/opak_kopi/html_php/reset_pasword_form.html?token=${token}`;

  Email.send({
    SecureToken: "af987cc8-140b-4030-b282-560b873afad4",
    To: email,
    From: "avjuji@gmail.com",
    Subject: "Password Reset Request",
    Body: `
                    <p>You requested to reset your password. Click the link below to reset it:</p>
                    <a href="${resetLink}">${resetLink}</a>
                `,
  })
    .then((message) => alert("Reset link sent to your email!"))
    .catch((error) => alert("Failed to send email: " + error));

  fetch("save_token.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, token }),
  });
});

function generateToken() {
  return Math.random().toString(36).substr(2);
}
