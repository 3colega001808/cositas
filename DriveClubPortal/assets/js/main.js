
// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
  const anchors = document.querySelectorAll('a[href^="#"]');
  
  anchors.forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      
      if (targetId !== '#') {
        e.preventDefault();
        
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      }
    });
  });
  
  // Featured Vehicle Rotation
  const featuredVehicles = document.querySelectorAll('.featured-vehicle');
  const indicators = document.querySelectorAll('.indicator');
  
  if (featuredVehicles.length > 0) {
    let currentIndex = 0;
    
    // Initialize rotation
    setInterval(() => {
      currentIndex = (currentIndex + 1) % featuredVehicles.length;
      updateFeaturedVehicle();
    }, 6000);
    
    // Click event for indicators
    indicators.forEach(indicator => {
      indicator.addEventListener('click', function() {
        currentIndex = parseInt(this.getAttribute('data-index'));
        updateFeaturedVehicle();
      });
    });
    
    function updateFeaturedVehicle() {
      featuredVehicles.forEach(vehicle => {
        vehicle.classList.remove('active');
      });
      
      indicators.forEach(indicator => {
        indicator.classList.remove('active');
      });
      
      featuredVehicles[currentIndex].classList.add('active');
      indicators[currentIndex].classList.add('active');
    }
  }
  
  // Add animation on scroll
  const animatedElements = document.querySelectorAll('.feature-card, .plan-card, .why-join-feature');
  
  if (animatedElements.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = 1;
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(element => {
      element.style.opacity = 0;
      element.style.transform = 'translateY(20px)';
      element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      observer.observe(element);
    });
  }
  
  // Password toggle functionality
  const passwordToggles = document.querySelectorAll('.password-toggle');
  
  passwordToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const passwordInput = this.parentElement.querySelector('input');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  });
});

// Scroll-triggered header style change
window.addEventListener('scroll', function() {
  const header = document.querySelector('.header');
  
  if (header) {
    if (window.scrollY > 50) {
      header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
      header.style.boxShadow = 'none';
    }
  }
});
