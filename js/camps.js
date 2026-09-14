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

    // 3. Dynamic Data Fetch & Search Filter Integration
    const searchInput = document.getElementById('searchInput');

    async function loadCampsFromDB() {
        if (!slider) return;

        try {
            const response = await fetch('php/get_camps.php');
            const camps = await response.json();

            slider.innerHTML = '';

            if (!camps || camps.length === 0) {
                slider.innerHTML = `<p style="padding: 24px; color: var(--text-muted); font-size: 14px;">No upcoming blood camps available right now.</p>`;
                return;
            }

            camps.forEach(camp => {
                // Calculate Dynamic Badges based on day difference & current time
                const days = parseInt(camp.days_diff, 10);
                let badgeClass = "badge-upcoming";
                let badgeText = "Upcoming";

                if (days === 0) {
                    const now = new Date();
                    const currentHours = String(now.getHours()).padStart(2, '0');
                    const currentMinutes = String(now.getMinutes()).padStart(2, '0');
                    const currentTime = `${currentHours}:${currentMinutes}:00`;

                    if (currentTime < camp.start_time) {
                        badgeClass = "badge-today";
                        badgeText = "Today";
                    } else if (currentTime >= camp.start_time && currentTime <= camp.end_time) {
                        badgeClass = "badge-open";
                        badgeText = "Open";
                    } else {
                        // Camp time expired today
                        badgeClass = "badge-ended";
                        badgeText = "Ended";
                    }
                } else if (days === 1) {
                    badgeClass = "badge-tomorrow";
                    badgeText = "Tomorrow";
                } else {
                    badgeClass = "badge-upcoming";
                    badgeText = "Upcoming";
                }

                // Format Date
                const dateObj = new Date(camp.camp_date + 'T00:00:00');
                const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                // Format Time to 12-hour AM/PM
                const formatTime = (timeStr) => {
                    const [hours, minutes] = timeStr.split(':');
                    const h = parseInt(hours, 10);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    const formattedH = h % 12 || 12;
                    return `${formattedH}:${minutes} ${ampm}`;
                };

                const displayTime = `${formatTime(camp.start_time)} – ${formatTime(camp.end_time)}`;

                // Generate Card Element
                const cleanImagePath = camp.cover_image ? camp.cover_image.replace(/^(\.\.\/)+/, '') : 'images/card1.png';

                const card = document.createElement('div');
                card.className = 'camp-card';
                card.innerHTML = `
                    <div class="card-img-wrap">
                      <img src="${cleanImagePath}" alt="${camp.camp_name}" onerror="this.src='images/card1.png'">
                      <span class="badge ${badgeClass}">${badgeText}</span>
                      <div class="location-overlay">
                        <i class="fa-solid fa-location-dot"></i> ${camp.location}
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="org-title">${camp.org_name}</div>
                      <h3 class="camp-name">${camp.camp_name}</h3>
                      <div class="meta-row">
                        <i class="fa-regular fa-calendar"></i> ${formattedDate}
                      </div>
                      <div class="meta-row">
                        <i class="fa-regular fa-clock"></i> ${displayTime}
                      </div>
                    </div>
                `;
                slider.appendChild(card);
            });

            // Rebind Search filter to dynamically generated cards
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const campCards = slider.querySelectorAll('.camp-card');
                    
                    campCards.forEach(card => {
                        const cardText = card.innerText.toLowerCase();
                        card.style.display = cardText.includes(query) ? 'block' : 'none';
                    });
                });
            }

        } catch (error) {
            console.error('Error loading camps:', error);
            slider.innerHTML = `<p style="padding: 20px; color: #ef3446;">Failed to load camps from database.</p>`;
        }
    }

    loadCampsFromDB();
});