<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="login.css" />
	<title>Login/Register</title>
	<style>
		/* Eye Toggle Icon Styles */
.input-field .fa-eye,
.input-field .fa-eye-slash {
    position: absolute;
    right: 10px;
    cursor: pointer;
    color: #007bff;
    transition: color 0.3s ease;
}

.input-field .fa-eye:hover,
.input-field .fa-eye-slash:hover {
  color: #0056b3;
}
	</style>
</head>

<body>

	<?php
	include_once("navbar.php");
	?>

	<div class="container">
		<div class="forms-container">
			<div class="signin-signup">
      <form action="dblogin.php" class="sign-in-form" method="POST">
  <h2 class="title">Sign in</h2>

  <div class="input-field">
    <i class="fas fa-envelope"></i>
    <input type="email" placeholder="Email" name="email" required onkeyup="hideAlertBox()" oninput="this.value = this.value.replace(/^\s+/,'')" />
  </div>

  <div class="input-field">
    <i class="fas fa-lock"></i>
    <input type="password" id="loginPassword" placeholder="Password" name="password" required onkeyup="hideAlertBox()" oninput="this.value = this.value.replace(/^\s+/,'')" />
    <i class="fas fa-eye-slash" id="toggleLoginPassword" style="cursor: pointer;"></i>
  </div>

  <!-- Aligned left and clickable link -->
  <div class="forgot-password" style="text-align: left; margin-top: 10px;">
    <a href="forgot.php" style="color: #1860c3; text-decoration: none;">Forgot password?</a>
  </div>

  <input type="submit" value="Login" class="submit solid" id="loginButton" />

  <?php
  if (isset($_GET['error'])) {
    echo ('
      <div class="alert alert-danger" id="alertbox" role="alert">
        Email or Password is incorrect.
      </div>');
  }
  ?>
</form>

				<form action="dbregister.php" class="sign-up-form" method="POST" id="registerForm">
					<h2 class="title">Sign up</h2>
					<div class="input-field">
  <i class="fas fa-user"></i>
  <input type="text" placeholder="First Name" name="firstName" maxlength="16"
         pattern="^[A-Za-z]{1,16}$"
         title="Only letters allowed, up to 16 characters, no spaces or numbers"
         oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')"
         required />
</div>

<div class="input-field">
  <i class="fas fa-user"></i>
  <input type="text" placeholder="Last Name" name="lastName" maxlength="16"
         pattern="^[A-Za-z]{1,16}$"
         title="Only letters allowed, up to 16 characters, no spaces or numbers"
         oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')"
         required />
</div>



<div class="input-field">
  <i class="fas fa-envelope"></i>
  <input type="text" placeholder="Email" name="email"
         pattern="^[A-Za-z0-9\.]{1,20}@gmail\.com$"
         title="Username can have up to 20 alphanumeric characters and one dot (.) before @gmail.com"
         oninput="validateEmail(this)"
         required />
</div>



<div class="input-field" style="display: flex; align-items: center;">
  <i class="fas fa-phone" style="transform: rotate(90deg); margin-right: 5px;"></i>
  <span style="margin-right: 5px; font-weight: bold;color:gray">+63</span>
  <input type="text" placeholder="Contact No" name="contact"
         onkeyup="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9);"
         maxlength="9" pattern="\d{9}" required />
</div>


<div class="input-field">
  <i class="fas fa-lock"></i>
  <input type="password" id="registerPassword" placeholder="Password" name="password"
         pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,10}$"
         title="8-10 characters, must include uppercase, number, special character, and no spaces"
         required oninput="checkPasswordMatch(); removeSpaces(this); hideAlertBox()" />
  <i class="fas fa-eye-slash" id="toggleRegisterPassword" style="cursor: pointer;"></i>
</div>

<div class="input-field">
  <i class="fas fa-lock"></i>
  <input type="password" id="confirmPassword" placeholder="Confirm Password" name="confirm_password"
         required oninput="checkPasswordMatch(); removeSpaces(this); hideAlertBox()" />
  <i class="fas fa-eye-slash" id="toggleConfirmPassword" style="cursor: pointer;"></i>
</div>

<small id="passwordMismatch" style="color: red; display: none;">Passwords do not match.</small>

					<input type="submit" class="submit" value="Sign up" id="registerButton" />



				</form>
			</div>
		</div>

		<div class="panels-container">
			<div class="panel left-panel">
				<div class="content">
					<h3>New to Our Restaurant?</h3>
					<p>
						Join us today and enjoy the convenience of online ordering. Get exclusive offers and track your orders easily.
					</p>
					<button class="btn transparent" id="sign-up-btn">
						Sign up
					</button>
				</div>
				<img src="images/form-pic.png" class="image" alt="" />
			</div>
			<div class="panel right-panel">
				<div class="content">
					<h3>Our Customer?</h3>
					<p>
						Sign in to continue enjoying our delicious meals and manage your orders seamlessly.
					</p>
					<button class="btn transparent" id="sign-in-btn">
						Sign in
					</button>
				</div>
				<img src="images/form-pic2.png" class="image" alt="" style="margin-bottom: 400px" />
			</div>
		</div>
	</div>

	<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
	<script>
		// Toggle password visibility for login form
const toggleLoginPassword = document.querySelector('#toggleLoginPassword');
const loginPassword = document.querySelector('#loginPassword');

toggleLoginPassword.addEventListener('click', function() {
    const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    loginPassword.setAttribute('type', type);

    // Toggle between eye and eye-slash icons
    if (type === 'password') {
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
    } else {
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
    }
});

// Toggle password visibility for register form
const toggleRegisterPassword = document.querySelector('#toggleRegisterPassword');
const registerPassword = document.querySelector('#registerPassword');

toggleRegisterPassword.addEventListener('click', function() {
    const type = registerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    registerPassword.setAttribute('type', type);

    // Toggle between eye and eye-slash icons
    if (type === 'password') {
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
    } else {
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
    }
});

	</script>

	<script>
		const sign_in_btn = document.querySelector("#sign-in-btn");
		const sign_up_btn = document.querySelector("#sign-up-btn");
		const container = document.querySelector(".container");

		sign_up_btn.addEventListener("click", () => {
			container.classList.add("sign-up-mode");
		});

		sign_in_btn.addEventListener("click", () => {
			container.classList.remove("sign-up-mode");
		});
	</script>
	<script>
		function hideAlertBox() {
			const alertBox = document.getElementById('alertbox');
			alertBox.style.display = 'none';
		}
    function validateEmail(input) {
    let value = input.value
        .replace(/^\s+/, '')                // Trim leading spaces
        .replace(/[^A-Za-z0-9.@]/g, '')     // Allow only letters, numbers, dot, @
        .replace(/\s/g, '');                // Remove all spaces

    // Extract username part before @gmail.com
    let username = value.split('@')[0];

    // Only allow one dot in the username
    let dotCount = (username.match(/\./g) || []).length;
    if (dotCount > 1) {
        username = username.replace(/\./g, (match, offset, string) => {
            // Keep the first dot, remove the rest
            return string.indexOf('.') === offset ? '.' : '';
        });
    }

    // Trim to max 20 characters
    username = username.slice(0, 20);

    input.value = username + '@gmail.com';
}

function checkPasswordMatch() {
  const password = document.getElementById('registerPassword').value;
  const confirm = document.getElementById('confirmPassword').value;
  const mismatchNotice = document.getElementById('passwordMismatch');

  mismatchNotice.style.display = (confirm.length > 0 && password !== confirm) ? 'block' : 'none';
}

// Prevent spaces while typing
function removeSpaces(input) {
  input.value = input.value.replace(/\s/g, '');
}

// Toggle show/hide password
document.getElementById("toggleRegisterPassword").onclick = function () {
  const field = document.getElementById("registerPassword");
  field.type = field.type === "password" ? "text" : "password";
  this.classList.toggle("fa-eye");
  this.classList.toggle("fa-eye-slash");
};

document.getElementById("toggleConfirmPassword").onclick = function () {
  const field = document.getElementById("confirmPassword");
  field.type = field.type === "password" ? "text" : "password";
  this.classList.toggle("fa-eye");
  this.classList.toggle("fa-eye-slash");
};
	</script>

</body>

</html>