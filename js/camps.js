document.addEventListener('DOMContentLoaded', () => {

    // Scroll animations
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    
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

    // Carousel buttons
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

    // Dynamic Data Fetch & Expired Badge Integration
    const searchInput = document.getElementById('searchInput');

    async function loadCampsFromDB() {
        if (!slider) return;

        try {
            const response = await fetch('php/get_camps.php');
            const camps = await response.json();

            slider.innerHTML = '';

            if (!camps || camps.length === 0) {
                slider.innerHTML = `<p style="padding: 24px; color: var(--text-muted); font-size: 14px;">No upcoming or active blood camps available right now.</p>`;
                return;
            }

            camps.forEach(camp => {
                const days = parseInt(camp.days_diff, 10);
                const minutesLeft = parseInt(camp.minutes_left, 10);

                let badgeClass = "badge-upcoming";
                let badgeText = "Upcoming";

                if (minutesLeft <= 0) {
                    badgeClass = "badge-ended";
                    badgeText = "Expired";
                } else if (days === 0) {
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

                // Format Time
                const formatTime = (timeStr) => {
                    const [hours, minutes] = timeStr.split(':');
                    const h = parseInt(hours, 10);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    const formattedH = h % 12 || 12;
                    return `${formattedH}:${minutes} ${ampm}`;
                };

                const displayTime = `${formatTime(camp.start_time)} – ${formatTime(camp.end_time)}`;
                const cleanImagePath = camp.cover_image ? camp.cover_image.replace(/^(\.\.\/)+/, '') : 'images/card1.png';

                // Check ownership or admin status for X (Delete) button
                const currentUserId = window.currentUser ? window.currentUser.id : 0;
                const isAdmin = window.currentUser ? window.currentUser.isAdmin : false;
                const isOwnerOrAdmin = isAdmin || (currentUserId && parseInt(currentUserId, 10) === parseInt(camp.created_by, 10));

                const deleteBtnHtml = isOwnerOrAdmin ? `
                    <button type="button" class="card-delete-x" data-id="${camp.id}" title="Delete Camp">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                ` : '';

                const card = document.createElement('div');
                card.className = 'camp-card';
                card.style.position = 'relative';
                card.innerHTML = `
                    ${deleteBtnHtml}
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

                // Handle AJAX Delete on X click
                const deleteBtn = card.querySelector('.card-delete-x');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', async () => {
                        if (!confirm('Do you really want to delete this blood camp?')) return;

                        const campId = deleteBtn.getAttribute('data-id');
                        try {
                            const formData = new FormData();
                            formData.append('camp_id', campId);

                            const res = await fetch('php/delete_camp.php', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await res.json();

                            if (result.status === 'success') {
                                // Smoothly remove card from UI without reloading page
                                card.style.transition = 'all 0.3s ease';
                                card.style.transform = 'scale(0.8)';
                                card.style.opacity = '0';
                                setTimeout(() => card.remove(), 300);
                            } else {
                                alert(result.message || 'Failed to delete camp.');
                            }
                        } catch (err) {
                            console.error('Delete error:', err);
                            alert('An error occurred while deleting.');
                        }
                    });
                }

                slider.appendChild(card);
            });

            // Search filter
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