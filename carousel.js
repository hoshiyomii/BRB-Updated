
document.addEventListener('DOMContentLoaded', function(){
    // Initialize the owl carousel if it exists
    if (typeof $.fn.owlCarousel !== 'undefined') {
        $(".owl-carousel").owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            items:1
        });
    }
    
    // Initialize our custom announcement carousel
    initAnnouncementCarousel();
    
    // Add direct event listeners for debugging
    const nextBtn = document.querySelector('.carousel-next');
    const prevBtn = document.querySelector('.carousel-prev');
    
    if (nextBtn) {
        nextBtn.onclick = function() {
            console.log('Next button clicked directly');
        };
    }
    
    if (prevBtn) {
        prevBtn.onclick = function() {
            console.log('Prev button clicked directly');
        };
    }
});

function initAnnouncementCarousel() {
    const carousel = document.querySelector('.announcement-carousel');
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    const announcementText = document.querySelector('.announcement-text');
    
    console.log('Carousel initialized with:', {
        slidesCount: slides.length,
        dotsCount: dots.length,
        prevBtn: prevBtn !== null,
        nextBtn: nextBtn !== null
    });
    
    let currentIndex = 0;
    let slideInterval;
    
    // Function to show a specific slide
    function showSlide(index) {
        // Handle index overflow
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        
        // Update current index
        currentIndex = index;
        
        // Hide all slides and remove active class from dots
        slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show the current slide and active dot
        slides[currentIndex].classList.add('active');
        dots[currentIndex].classList.add('active');
        
        // Update the announcement text content if it exists
        if (announcementText && slides[currentIndex]) {
            const currentSlide = slides[currentIndex];
            const title = currentSlide.getAttribute('data-title');
            const content = currentSlide.getAttribute('data-content');
            const genre = currentSlide.getAttribute('data-genre');
            const date = currentSlide.getAttribute('data-date');
            
            // Update title if element exists
            const titleElement = announcementText.querySelector('.announcement-title');
            if (titleElement && title) {
                titleElement.textContent = title;
            }
            
            // Update content if element exists
            const contentElement = announcementText.querySelector('.announcement-body');
            if (contentElement && content) {
                contentElement.innerHTML = content.replace(/\n/g, '<br>');
            }
            
            // Update tags if they exist
            const tagsContainer = announcementText.querySelector('.announcement-tags');
            if (tagsContainer) {
                tagsContainer.innerHTML = '';
                
                // Add genre tag
                if (genre) {
                    const genreTag = document.createElement('span');
                    genreTag.className = 'announcement-tag';
                    genreTag.textContent = genre;
                    tagsContainer.appendChild(genreTag);
                }
                
                // Add date tag
                if (date) {
                    const dateObj = new Date(date);
                    const formattedDate = dateObj.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    
                    const dateTag = document.createElement('span');
                    dateTag.className = 'announcement-tag';
                    dateTag.textContent = formattedDate;
                    tagsContainer.appendChild(dateTag);
                }
            }
        }
    }
    
    // Set up click events for dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            showSlide(index);
            resetInterval();
            console.log('Dot clicked:', index);
        });
    });
    
    // Set up previous button click
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showSlide(currentIndex - 1);
            resetInterval();
            console.log('Prev button clicked');
        });
    } else {
        console.error('Previous button not found');
    }
    
    // Set up next button click
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            showSlide(currentIndex + 1);
            resetInterval();
            console.log('Next button clicked');
        });
    } else {
        console.error('Next button not found');
    }
    
    // Set up automatic slide rotation
    function startInterval() {
        slideInterval = setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000); // Change slide every 5 seconds
    }
    
    // Reset interval after user interaction
    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }
    
    // Initialize the carousel
    showSlide(0);
    startInterval();
    
    // Add touch support for mobile
    let touchStartX = 0;
    
    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    carousel.addEventListener('touchend', (e) => {
        const touchEndX = e.changedTouches[0].screenX;
        const diff = touchEndX - touchStartX;
        
        // Detect swipe direction
        if (diff > 50) { // Swiped right - go to previous
            showSlide(currentIndex - 1);
            resetInterval();
        } else if (diff < -50) { // Swiped left - go to next
            showSlide(currentIndex + 1);
            resetInterval();
        }
    });
    
    // Pause autoplay on hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });
    
    // Resume autoplay when mouse leaves
    carousel.addEventListener('mouseleave', () => {
        startInterval();
    });
}
