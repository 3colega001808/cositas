document.addEventListener('DOMContentLoaded', function() {
  // Toggle between login and register forms
  const loginTabBtn = document.getElementById('loginTabBtn');
  const registerTabBtn = document.getElementById('registerTabBtn');
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const switchToRegister = document.getElementById('switchToRegister');
  const switchToLogin = document.getElementById('switchToLogin');
  
  // Check if the URL has a register parameter
  const urlParams = new URLSearchParams(window.location.search);
  const showRegister = urlParams.get('registro') === 'true';
  
  if (showRegister) {
    loginForm.style.display = 'none';
    registerForm.style.display = 'block';
    loginTabBtn.classList.remove('active');
    registerTabBtn.classList.add('active');
  }
  
  if (loginTabBtn && registerTabBtn) {
    loginTabBtn.addEventListener('click', function() {
      loginForm.style.display = 'block';
      registerForm.style.display = 'none';
      loginTabBtn.classList.add('active');
      registerTabBtn.classList.remove('active');
    });
    
    registerTabBtn.addEventListener('click', function() {
      loginForm.style.display = 'none';
      registerForm.style.display = 'block';
      loginTabBtn.classList.remove('active');
      registerTabBtn.classList.add('active');
    });
  }
  
  if (switchToRegister) {
    switchToRegister.addEventListener('click', function(e) {
      e.preventDefault();
      loginForm.style.display = 'none';
      registerForm.style.display = 'block';
      loginTabBtn.classList.remove('active');
      registerTabBtn.classList.add('active');
    });
  }
  
  if (switchToLogin) {
    switchToLogin.addEventListener('click', function(e) {
      e.preventDefault();
      loginForm.style.display = 'block';
      registerForm.style.display = 'none';
      loginTabBtn.classList.add('active');
      registerTabBtn.classList.remove('active');
    });
  }
  
  // Toggle password visibility
  const passwordToggles = document.querySelectorAll('.password-toggle');
  passwordToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const passwordField = this.previousElementSibling;
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      
      // Toggle eye icon
      const icon = this.querySelector('i');
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    });
  });
  
  // Password strength meter
  const registerPassword = document.getElementById('registerPassword');
  const strengthIndicator = document.querySelector('.strength-indicator');
  const strengthText = document.querySelector('.strength-text');
  
  if (registerPassword && strengthIndicator && strengthText) {
    registerPassword.addEventListener('input', function() {
      const password = this.value;
      let strength = 0;
      let tips = [];
      
      // Basic length check
      if (password.length >= 8) {
        strength += 25;
      } else {
        tips.push("La contraseña debe tener al menos 8 caracteres");
      }
      
      // Check for uppercase letters
      if (password.match(/[A-Z]/)) {
        strength += 25;
      } else {
        tips.push("Incluye al menos una letra mayúscula");
      }
      
      // Check for numbers
      if (password.match(/[0-9]/)) {
        strength += 25;
      } else {
        tips.push("Incluye al menos un número");
      }
      
      // Check for special characters
      if (password.match(/[^A-Za-z0-9]/)) {
        strength += 25;
      } else {
        tips.push("Incluye al menos un carácter especial");
      }
      
      // Update the strength indicator
      strengthIndicator.style.width = strength + '%';
      
      // Change color based on strength
      if (strength < 50) {
        strengthIndicator.style.backgroundColor = '#e63946';
      } else if (strength < 75) {
        strengthIndicator.style.backgroundColor = '#ffd166';
      } else {
        strengthIndicator.style.backgroundColor = '#2ecc71';
      }
      
      // Update the strength text
      if (tips.length > 0) {
        strengthText.textContent = tips[0];
      } else {
        strengthText.textContent = "Contraseña segura";
      }
    });
  }
  
  // Form submissions
  const forms = document.querySelectorAll('form');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Simulate form submission
      const submitBtn = this.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
      
      // Corregir la ruta de redirección - usar ruta relativa
      setTimeout(() => {
        window.location.href = '/mi-cuenta.html';
      }, 1500);
    });
  });
});
