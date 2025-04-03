
document.addEventListener('DOMContentLoaded', function() {
  // Reservation date picker functionality
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  
  if (startDateInput && endDateInput) {
    // Set minimum date to today
    const today = new Date();
    const isoDate = today.toISOString().split('T')[0];
    startDateInput.min = isoDate;
    
    // Update end date based on start date
    startDateInput.addEventListener('change', function() {
      if (this.value) {
        const minEndDate = new Date(this.value);
        minEndDate.setDate(minEndDate.getDate() + 1);
        endDateInput.min = minEndDate.toISOString().split('T')[0];
        
        if (!endDateInput.value || new Date(endDateInput.value) <= new Date(this.value)) {
          const defaultEndDate = new Date(this.value);
          defaultEndDate.setDate(defaultEndDate.getDate() + 7);
          endDateInput.value = defaultEndDate.toISOString().split('T')[0];
        }
      }
    });
    
    // Initialize with some default dates
    const defaultStartDate = new Date();
    defaultStartDate.setDate(defaultStartDate.getDate() + 3);
    startDateInput.value = defaultStartDate.toISOString().split('T')[0];
    
    const defaultEndDate = new Date();
    defaultEndDate.setDate(defaultEndDate.getDate() + 10);
    endDateInput.value = defaultEndDate.toISOString().split('T')[0];
    endDateInput.min = startDateInput.value;
  }

  // Add class to vehicle detail page wrapper and sections for responsive ordering
  const vehicleDetailContainer = document.querySelector('.vehicle-detail-section .container > .row');
  const mainContentColumn = document.querySelector('.vehicle-detail-section .container > .row > .col-lg-8');
  const sidebarColumn = document.querySelector('.vehicle-detail-section .container > .row > .col-lg-4');
  
  if (vehicleDetailContainer && mainContentColumn && sidebarColumn) {
    vehicleDetailContainer.classList.add('vehicle-detail-container');
    mainContentColumn.classList.add('vehicle-content-section');
    sidebarColumn.classList.add('reservation-sidebar-section');
    
    // Handle mobile-first ordering
    function updateColumnOrder() {
      if (window.innerWidth < 992) {
        // Mover sidebar después de las imágenes pero antes de las especificaciones técnicas
        const imagesSection = mainContentColumn.querySelector('.vehicle-images');
        const techSpecsSection = mainContentColumn.querySelector('.technical-specs');
        
        if (imagesSection && techSpecsSection && sidebarColumn.parentNode) {
          // Primero devolvemos el sidebar a su posición original si estuviera en otra posición
          vehicleDetailContainer.appendChild(sidebarColumn);
          
          // Ahora lo insertamos después de las imágenes
          mainContentColumn.insertBefore(sidebarColumn, techSpecsSection);
        }
      } else {
        // En desktop - restaurar orden original
        if (sidebarColumn.parentNode === mainContentColumn) {
          vehicleDetailContainer.appendChild(sidebarColumn);
        }
      }
    }
    
    // Initial call
    updateColumnOrder();
    
    // Add event listener for window resize
    window.addEventListener('resize', updateColumnOrder);
  }
  
  // Add event listener for featured vehicles buttons
  const carouselButtons = document.querySelectorAll('.carousel-control-prev, .carousel-control-next');
  carouselButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const direction = this.classList.contains('carousel-control-next') ? 1 : -1;
      const carousel = document.getElementById('vehicleCarousel');
      const activeItem = carousel.querySelector('.carousel-item.active');
      let nextItem = direction === 1 ? 
        activeItem.nextElementSibling || carousel.querySelector('.carousel-item:first-child') : 
        activeItem.previousElementSibling || carousel.querySelector('.carousel-item:last-child');
      
      activeItem.classList.remove('active');
      nextItem.classList.add('active');
    });
  });
});
