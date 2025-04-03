
document.addEventListener('DOMContentLoaded', function() {
  // Screen transitions
  const transitions = {
    fadeIn: function(element, callback) {
      element.style.opacity = 0;
      element.style.display = 'block';
      
      setTimeout(() => {
        element.style.transition = 'opacity 0.5s ease';
        element.style.opacity = 1;
        
        if (callback && typeof callback === 'function') {
          setTimeout(callback, 500);
        }
      }, 10);
    },
    
    fadeOut: function(element, callback) {
      element.style.transition = 'opacity 0.5s ease';
      element.style.opacity = 0;
      
      setTimeout(() => {
        element.style.display = 'none';
        
        if (callback && typeof callback === 'function') {
          callback();
        }
      }, 500);
    },
    
    slideIn: function(element, callback) {
      element.style.transform = 'translateX(100%)';
      element.style.display = 'block';
      
      setTimeout(() => {
        element.style.transition = 'transform 0.5s ease';
        element.style.transform = 'translateX(0)';
        
        if (callback && typeof callback === 'function') {
          setTimeout(callback, 500);
        }
      }, 10);
    },
    
    slideOut: function(element, callback) {
      element.style.transition = 'transform 0.5s ease';
      element.style.transform = 'translateX(-100%)';
      
      setTimeout(() => {
        element.style.display = 'none';
        
        if (callback && typeof callback === 'function') {
          callback();
        }
      }, 500);
    }
  };
  
  // Screen initialization
  const initScreen = {
    Home: function() {
      console.log('Home screen initialized');
      // Add any specific home screen initialization
    },
    
    Vehicles: function() {
      console.log('Vehicles screen initialized');
      // Add any specific vehicles screen initialization
    },
    
    Plans: function() {
      console.log('Plans screen initialized');
      // Add any specific plans screen initialization
    },
    
    Account: function() {
      console.log('Account screen initialized');
      // Add any specific account screen initialization
    },
    
    Login: function() {
      console.log('Login screen initialized');
      // Add any specific login screen initialization
    },
    
    Vehicle: function() {
      console.log('Vehicle detail screen initialized');
      // Add any specific vehicle detail screen initialization
    }
  };
  
  // Determine current screen
  const path = window.location.pathname;
  let currentScreen = 'Home';
  
  if (path.includes('vehiculos.html')) {
    currentScreen = 'Vehicles';
  } else if (path.includes('planes.html')) {
    currentScreen = 'Plans';
  } else if (path.includes('mi-cuenta.html')) {
    currentScreen = 'Account';
  } else if (path.includes('login.html')) {
    currentScreen = 'Login';
  } else if (path.includes('vehiculo.html')) {
    currentScreen = 'Vehicle';
  }
  
  // Initialize current screen
  if (initScreen[currentScreen]) {
    initScreen[currentScreen]();
  }
  
  // Add page transition effect
  document.body.style.opacity = 0;
  document.body.style.transition = 'opacity 0.5s ease';
  
  setTimeout(() => {
    document.body.style.opacity = 1;
  }, 100);
  
  // Add transition effects to internal links
  const internalLinks = document.querySelectorAll('a[href^="/"]:not([target="_blank"])');
  
  internalLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      
      // Skip if modifier keys are pressed or it's a download link
      if (e.metaKey || e.ctrlKey || e.shiftKey || this.getAttribute('download')) {
        return;
      }
      
      e.preventDefault();
      
      document.body.style.opacity = 0;
      
      setTimeout(() => {
        window.location.href = href;
      }, 500);
    });
  });
});
