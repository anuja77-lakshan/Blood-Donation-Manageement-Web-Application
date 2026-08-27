document.addEventListener('DOMContentLoaded', () => {
  // 1. Slider Functionality (Arrow buttons)
  const slider = document.getElementById('campsSlider');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  if (slider && prevBtn && nextBtn) {
    nextBtn.addEventListener('click', () => {
      const card = slider.querySelector('.camp-card');
      if (card) {
        const cardWidth = card.offsetWidth + 24;
        slider.scrollBy({ left: cardWidth, behavior: 'smooth' });
      }
    });

    prevBtn.addEventListener('click', () => {
      const card = slider.querySelector('.camp-card');
      if (card) {
        const cardWidth = card.offsetWidth + 24;
        slider.scrollBy({ left: -cardWidth, behavior: 'smooth' });
      }
    });
  }

  // 2. Leaflet Map Setup
  const map = L.map('map').setView([12.9716, 77.5946], 11);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const camps = [
    { name: "City Central Blood Drive", coords: [12.9716, 77.5946], info: "Community Hall, MG Road" },
    { name: "Hope & Heal Blood Camp", coords: [12.9900, 77.6100], info: "Nehru Stadium" },
    { name: "United We Donate Drive", coords: [12.9352, 77.6245], info: "Rajiv Gandhi Hall" },
    { name: "Youth Life Saver Drive", coords: [6.9271, 79.8612], info: "Town Hall, Colombo" },
    { name: "Universal Hope Donation", coords: [7.2906, 80.6337], info: "City Center, Kandy" }
  ];

  camps.forEach(camp => {
    L.marker(camp.coords).addTo(map)
      .bindPopup(`<strong>${camp.name}</strong><br>${camp.info}`);
  });

  // Search by Camp Name, Location, and Organization Name
  const searchInput = document.getElementById('searchInput');
  const cards = document.querySelectorAll('.camp-card');

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const term = e.target.value.toLowerCase().trim();

      cards.forEach(card => {
        // Camp Name
        const nameEl = card.querySelector('.camp-name');
        const campName = nameEl ? nameEl.textContent.toLowerCase() : '';

        // Organization Name
        const orgEl = card.querySelector('.org-title');
        const orgName = orgEl ? orgEl.textContent.toLowerCase() : '';

        // Location
        const locationEl = card.querySelector('.location-overlay');
        const campLocation = locationEl ? locationEl.textContent.toLowerCase() : '';

        const isMatch = campName.includes(term) || campLocation.includes(term) || orgName.includes(term);

        if (isMatch) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
});