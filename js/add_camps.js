document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('addCampForm');

  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      // Collect form input values
      const campData = {
        name: document.getElementById('campName').value.trim(),
        organization: document.getElementById('orgName').value.trim(),
        date: document.getElementById('campDate').value,
        startTime: document.getElementById('startTime').value,
        endTime: document.getElementById('endTime').value,
        location: document.getElementById('campLocation').value.trim(),
        status: document.getElementById('campStatus').value,
        imageUrl: document.getElementById('imageUrl').value.trim() || 'https://placehold.co/800x400/991b1b/ffffff?text=Blood+Donation+Drive'
      };

      // Retrieve existing camps from localStorage or set empty array
      const existingCamps = JSON.parse(localStorage.getItem('bloodCamps')) || [];

      // Append new camp details
      existingCamps.push(campData);

      // Save updated data to localStorage
      localStorage.setItem('bloodCamps', JSON.stringify(existingCamps));

      // Display confirmation alert and redirect to main page
      alert('Blood Camp added successfully!');
      window.location.href = 'index.html';
    });
  }
});