document.addEventListener('DOMContentLoaded', () => {
  // 1. File Upload Dropzone visual logic
  const fileInput = document.getElementById('coverImage');
  const dropzoneTitle = document.querySelector('.dropzone-title');
  const dropzoneHint = document.querySelector('.dropzone-hint');
  const dropzoneIcon = document.querySelector('.dropzone-icon');
  const dropzoneBox = document.querySelector('.file-dropzone');

  if (fileInput) {
    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        // Update dropzone UI with success state
        if (dropzoneIcon) {
          dropzoneIcon.className = 'fa-solid fa-circle-check dropzone-icon';
          dropzoneIcon.style.color = '#16a34a';
        }
        if (dropzoneTitle) {
          dropzoneTitle.textContent = `Uploaded: ${file.name}`;
          dropzoneTitle.style.color = '#16a34a';
          dropzoneTitle.style.fontWeight = '700';
        }
        if (dropzoneHint) {
          dropzoneHint.textContent = `File ready for upload (${fileSizeMB} MB) • Click to change`;
          dropzoneHint.style.color = '#374151';
        }
        if (dropzoneBox) {
          dropzoneBox.style.borderColor = '#16a34a';
          dropzoneBox.style.backgroundColor = '#f0fdf4';
        }
      }
    });
  }

  // 2. Leaflet Map Setup with street-level zoom (Max Zoom: 19)
  const mapElement = document.getElementById('campPickerMap');
  if (!mapElement) return;

  // Default coordinates (Colombo, Sri Lanka)
  let defaultLat = 6.9271;
  let defaultLng = 79.8612;

  // Initialize Leaflet map
  const pickerMap = L.map('campPickerMap', {
    maxZoom: 19
  }).setView([defaultLat, defaultLng], 12);

  // Add OpenStreetMap tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(pickerMap);

  // Custom marker pin design
  const redCampIcon = L.divIcon({
    className: 'custom-pin',
    html: '<div style="background-color:#ef3446; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.4);"><i class="fa-solid fa-tent" style="font-size:13px;"></i></div>',
    iconSize: [30, 30],
    iconAnchor: [15, 15],
    popupAnchor: [0, -15]
  });

  // Add draggable marker to map
  const marker = L.marker([defaultLat, defaultLng], {
    icon: redCampIcon,
    draggable: true
  }).addTo(pickerMap);

  // Get DOM elements
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  const mapUrlInput = document.getElementById('mapUrl');
  const statusText = document.getElementById('mapStatusText');

  // Function to update map coordinates and inputs
  function updateCoordinates(lat, lng, sourceMsg = '') {
    latInput.value = parseFloat(lat).toFixed(6);
    lngInput.value = parseFloat(lng).toFixed(6);
    marker.setLatLng([lat, lng]);
    pickerMap.setView([lat, lng], 16);

    // Show success message only when user updates location (not on page load)
    if (statusText && sourceMsg !== '') {
      statusText.style.display = 'block';
      statusText.innerHTML = `<span style="color: #16a34a;"><i class="fa-solid fa-circle-check"></i> Location set successfully! ${sourceMsg}</span>`;
    }
  }

  // Set default location initially without showing status message
  updateCoordinates(defaultLat, defaultLng, '');

  // Update coordinates when marker is dragged
  marker.on('dragend', function () {
    const pos = marker.getLatLng();
    updateCoordinates(pos.lat, pos.lng, '(Pin dragged)');
  });

  // Update coordinates when map is clicked
  pickerMap.on('click', function (e) {
    updateCoordinates(e.latlng.lat, e.latlng.lng, '(Selected on map)');
  });

  // Function to extract coordinates from various map URL formats
  function extractCoords(input) {
    if (!input) return null;
    const text = decodeURIComponent(input.trim());

    // Apple Maps format
    let appleMatch = text.match(/[?&](?:ll|sll|q)=(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (appleMatch) {
      return { lat: parseFloat(appleMatch[1]), lng: parseFloat(appleMatch[2]) };
    }

    // Google Maps @lat,lng format
    let gAtMatch = text.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (gAtMatch) {
      return { lat: parseFloat(gAtMatch[1]), lng: parseFloat(gAtMatch[2]) };
    }

    // Google Maps query format (?q=lat,lng)
    let gQMatch = text.match(/[?&](?:q|ll)=(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (gQMatch) {
      return { lat: parseFloat(gQMatch[1]), lng: parseFloat(gQMatch[2]) };
    }

    // Google Maps place data format (!3dlat!4dlng)
    let gPlaceMatch = text.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
    if (gPlaceMatch) {
      return { lat: parseFloat(gPlaceMatch[1]), lng: parseFloat(gPlaceMatch[2]) };
    }

    // Path format (/search/lat,lng or /place/lat,lng)
    let pathMatch = text.match(/\/(?:search|place|dir)\/(-?\d+\.\d+)[,+](-?\d+\.\d+)/);
    if (pathMatch) {
      return { lat: parseFloat(pathMatch[1]), lng: parseFloat(pathMatch[2]) };
    }

    // Raw coordinates format (lat, lng)
    let rawMatch = text.match(/^(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)$/);
    if (rawMatch) {
      return { lat: parseFloat(rawMatch[1]), lng: parseFloat(rawMatch[2]) };
    }

    return null;
  }

  // Handle URL input and paste events
  if (mapUrlInput) {
    const handleUrlInput = () => {
      const val = mapUrlInput.value.trim();
      if (!val) {
        if (statusText) {
          statusText.style.display = 'none';
          statusText.innerHTML = '';
        }
        return;
      }

      const coords = extractCoords(val);
      if (coords) {
        updateCoordinates(coords.lat, coords.lng, '(Extracted from link)');
      } else {
        if (statusText) {
          statusText.style.display = 'block';
          statusText.innerHTML = `<span style="color: #dc2626;"><i class="fa-solid fa-triangle-exclamation"></i> Could not detect location from link. Please click on the map.</span>`;
        }
      }
    };

    mapUrlInput.addEventListener('input', handleUrlInput);
    mapUrlInput.addEventListener('paste', () => setTimeout(handleUrlInput, 100));
  }

  // Validate coordinates before form submission
  const campForm = document.querySelector('form');
  if (campForm) {
    campForm.addEventListener('submit', (e) => {
      if (!latInput.value || !lngInput.value) {
        e.preventDefault();
        alert('Enter google map link or click the right location in the map');
        if (mapUrlInput) mapUrlInput.focus();
      }
    });
  }
});