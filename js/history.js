document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.querySelector('.filter-input');
  const tableRows = document.querySelectorAll('tbody tr');

  // Live Search Functionality
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase().trim();

      tableRows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});