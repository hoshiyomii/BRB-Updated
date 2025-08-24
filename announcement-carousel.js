document.addEventListener('DOMContentLoaded', function() {
    initCarousel();
});

function initCarousel() {
    const carousel = document.querySelector('.announcement-carousel');
    if (!carousel) {
        console.error('Carousel container not found');
        return;
    }
    
    const slides = carousel.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    const announcementText = document.querySelector('.announcement-text');
    
    console.log('Carousel elements:', {
        slidesCount: slides.length,
        dotsCount: dots.length,
        prevBtn: prevBtn !== null,
        nextBtn: nextBtn !== null
    });
    
    let currentIndex = 0;
    let slideInterval;
    
    function showSlide(index) {
        // Log the action
        console.log('Changing to slide:', index);
        
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
        if (slides[currentIndex]) {
            slides[currentIndex].classList.add('active');
            dots[currentIndex]?.classList.add('active');
            
            // Update announcement text
            if (announcementText) {
                updateAnnouncementText(slides[currentIndex]);
            }
        }
    }
    
    function updateAnnouncementText(slide) {
        const title = slide.getAttribute('data-title');
        const content = slide.getAttribute('data-content');
        const genre = slide.getAttribute('data-genre');
        const date = slide.getAttribute('data-date');
        
        // Update title with simple fade
        const titleElement = announcementText.querySelector('.announcement-title');
        if (titleElement && title) {
            titleElement.style.opacity = '0';
            
            setTimeout(() => {
                titleElement.textContent = title;
                titleElement.style.opacity = '1';
            }, 200);
        }
        
        // Update content with simple fade
        const contentElement = announcementText.querySelector('.announcement-body');
        if (contentElement && content) {
            contentElement.style.opacity = '0';
            
            setTimeout(() => {
                // Limit content length if needed
                let displayContent = content;
                if (content.length > 300) {
                    displayContent = content.substring(0, 300) + '...';
                }
                
                contentElement.innerHTML = displayContent.replace(/\n/g, '<br>');
                contentElement.style.opacity = '1';
            }, 200);
        }
        
        // Update tags
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
    
    // Set up button click events
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Previous button clicked');
            showSlide(currentIndex - 1);
            resetInterval();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Next button clicked');
            showSlide(currentIndex + 1);
            resetInterval();
        });
    }
    
    // Set up dot click events
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Dot clicked:', index);
            showSlide(index);
            resetInterval();
        });
    });
    
    // Auto rotation
    function startInterval() {
        slideInterval = setInterval(function() {
            showSlide(currentIndex + 1);
        }, 5000);
    }
    
    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }
    
    // Initialize
    showSlide(0);
    startInterval();
    
    // Mobile touch support
    let touchStartX = 0;
    
    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    carousel.addEventListener('touchend', function(e) {
        const touchEndX = e.changedTouches[0].screenX;
        const diff = touchEndX - touchStartX;
        
        if (diff > 50) { // Swiped right - go to previous
            showSlide(currentIndex - 1);
            resetInterval();
        } else if (diff < -50) { // Swiped left - go to next
            showSlide(currentIndex + 1);
            resetInterval();
        }
    });
}
