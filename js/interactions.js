//login.php
/*login page client side validation*/

document.addEventListener("DOMContentLoaded", function() {
//Login form validation to ensure username and password are not empty
const loginForm = document.querySelector('form[action="processes/login-process.php"]');
if (loginForm) {
loginForm.addEventListener('submit', function(e) {
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
if (usernameInput && usernameInput.value.trim() === "") {
e.preventDefault();
alert("Please enter your username before logging in! ");
usernameInput.focus();
return false;
}
if (passwordInput && passwordInput.value.trim() === "") {
e.preventDefault();
alert("Please enter your password to continue! ");
passwordInput.focus();
return false;
}
});
}
if (window.location.href.includes('dashboard.php') && document.referrer.includes('login-process.php')) {
alert('Login successful! Welcome to your ThoughtPad Dashboard. ');
}
});

//login-process.php
/*used for display alert when user successfully logs in*/

document.addEventListener("DOMContentLoaded", function() {
//after login direct to dashboard.php and show alert
if (window.location.href.includes('dashboard.php') && document.referrer.includes('login-process.php')) {
alert('Login successful! Welcome to your ThoughtPad Dashboard. ');
}
});

//register.php
/*register page client side validation*/

document.addEventListener("DOMContentLoaded", function() {
//Register form validation to ensure username, email, and password are not empty
const registerForm = document.getElementById('thoughtpad-register-form');
if (registerForm) {
registerForm.addEventListener('submit', function(e) {
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
if (usernameInput && usernameInput.value.trim() === "") {
e.preventDefault();
alert("Please create a username first! ");
usernameInput.focus();
return false;
}
if (emailInput && emailInput.value.trim() === "") {
e.preventDefault();
alert("Please enter a valid email address! ");
emailInput.focus();
return false;
}
if (passwordInput && passwordInput.value.trim() === "") {
e.preventDefault();
alert("Please secure your account with a password! ");
passwordInput.focus();
return false;
}
// Password length validation
if (passwordInput && passwordInput.value.length < 6) {
e.preventDefault();
alert("Password must be at least 6 characters long for security! ");
passwordInput.focus();
return false;
}
});
}
});

//register-process.php
/*used for display alert when user successfully registers*/

document.addEventListener("DOMContentLoaded", function() {
if (window.location.href.includes('login.php') && document.referrer.includes('register-process.php')) {
alert('Registration successful! Please login to your ThoughtPad account. ');
}
});

//update-settings.php
/*used for display alert when settings are successfully updated*/
document.addEventListener("DOMContentLoaded", function() {
if (window.location.href.includes('dashboard.php') && 
document.referrer.includes('update-settings.php')) {
alert('Settings updated successfully!');
}
});

//blog-process.php
/*used for display alert when blog is successfully submitted*/
document.addEventListener("DOMContentLoaded", function() {
if (window.location.href.includes('dashboard.php') && document.referrer.includes('blog-process.php')) {
alert('Blog published successfully! ');
}
});

//edit-process.php
/*used for display alert when blog is successfully updated*/
document.addEventListener("DOMContentLoaded", function() {
if (window.location.href.includes('dashboard.php') && document.referrer.includes('edit-process.php')) {
alert('Blog updated successfully! ');
}
});

//delete-blog.php
/*used for display alert when blog is successfully deleted*/
document.addEventListener("DOMContentLoaded", function() {
if (window.location.href.includes('dashboard.php') && 
document.referrer.includes('delete-blog.php')) {
alert('Blog deleted successfully! ');
}
});

//view-blog.php
/*view blog page with view sidebar*/
function openInsightsSidebar() {
const sidebar = document.getElementById('sidebar-viewers');
const overlay = document.getElementById('sidebar-overlay');
if (sidebar) sidebar.classList.add('open-sidebar');
if (overlay) overlay.style.display = 'block';
}

function closeInsightsSidebar() {
const sidebar = document.getElementById('sidebar-viewers');
const overlay = document.getElementById('sidebar-overlay');
if (sidebar) sidebar.classList.remove('open-sidebar');
if (overlay) overlay.style.display = 'none';
}