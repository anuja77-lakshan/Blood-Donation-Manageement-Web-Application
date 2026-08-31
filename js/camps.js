document.addEventListener('DOMContentLoaded', () => {

    // 1. Scroll animations
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    
                    // Refresh map view
                    if (entry.target.classList.contains('map-section')) {
                        setTimeout(() => {
                            window.dispatchEvent(new Event('resize'));
                        }, 300);
                    }
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        revealElements.forEach(el => el.classList.add('is-visible'));
    }

    // 2. Carousel buttons
    const slider = document.getElementById('campsSlider');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (slider && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -350, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: 350, behavior: 'smooth' });
        });
    }

    // 3. Search filter
    const searchInput = document.getElementById('searchInput');
    const campCards = document.querySelectorAll('.camp-card');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            campCards.forEach(card => {
                const cardText = card.innerText.toLowerCase();
                card.style.display = cardText.includes(query) ? 'block' : 'none';
            });
        });
    }
});