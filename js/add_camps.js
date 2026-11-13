document.addEventListener('DOMContentLoaded', () => {
  // File upload dropzone visual logic
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

  // Leaflet map setup
  const mapElement = document.getElementById('campPickerMap');
  if (!mapElement) return;

  let defaultLat = 6.9271;
  let defaultLng = 79.8612;

  const pickerMap = L.map('campPickerMap', {
    maxZoom: 19
  }).setView([defaultLat, defaultLng], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(pickerMap);

  const redCampIcon = L.divIcon({
    className: 'custom-pin',
    html: '<div style="background-color:#ef3446; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.4);"><i class="fa-solid fa-tent" style="font-size:13px;"></i></div>',
    iconSize: [30, 30],
    iconAnchor: [15, 15],
    popupAnchor: [0, -15]
  });

  const marker = L.marker([defaultLat, defaultLng], {
    icon: redCampIcon,
    draggable: true
  }).addTo(pickerMap);

  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  const mapUrlInput = document.getElementById('mapUrl');
  const statusText = document.getElementById('mapStatusText');

  // Update map coordinates and inputs
  function updateCoordinates(lat, lng, sourceMsg = '') {
    latInput.value = parseFloat(lat).toFixed(6);
    lngInput.value = parseFloat(lng).toFixed(6);
    marker.setLatLng([lat, lng]);
    pickerMap.setView([lat, lng], 16);

    if (statusText && sourceMsg !== '') {
      statusText.style.display = 'block';
      statusText.innerHTML = `<span style="color: #16a34a;"><i class="fa-solid fa-circle-check"></i> Location set successfully! ${sourceMsg}</span>`;
    }
  }

  updateCoordinates(defaultLat, defaultLng, '');

  // Update on marker drag
  marker.on('dragend', function () {
    const pos = marker.getLatLng();
    updateCoordinates(pos.lat, pos.lng, '(Pin dragged)');
  });

  // Update on map click
  pickerMap.on('click', function (e) {
    updateCoordinates(e.latlng.lat, e.latlng.lng, '(Selected on map)');
  });

  // Extract coordinates from map URL
  function extractCoords(input) {
    if (!input) return null;
    const text = decodeURIComponent(input.trim());

    let appleMatch = text.match(/[?&](?:ll|sll|q)=(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (appleMatch) {
      return { lat: parseFloat(appleMatch[1]), lng: parseFloat(appleMatch[2]) };
    }

    let gAtMatch = text.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (gAtMatch) {
      return { lat: parseFloat(gAtMatch[1]), lng: parseFloat(gAtMatch[2]) };
    }

    let gQMatch = text.match(/[?&](?:q|ll)=(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (gQMatch) {
      return { lat: parseFloat(gQMatch[1]), lng: parseFloat(gQMatch[2]) };
    }

    let gPlaceMatch = text.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
    if (gPlaceMatch) {
      return { lat: parseFloat(gPlaceMatch[1]), lng: parseFloat(gPlaceMatch[2]) };
    }

    let pathMatch = text.match(/\/(?:search|place|dir)\/(-?\d+\.\d+)[,+](-?\d+\.\d+)/);
    if (pathMatch) {
      return { lat: parseFloat(pathMatch[1]), lng: parseFloat(pathMatch[2]) };
    }

    let rawMatch = text.match(/^(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)$/);
    if (rawMatch) {
      return { lat: parseFloat(rawMatch[1]), lng: parseFloat(rawMatch[2]) };
    }

    return null;
  }

  // Handle URL input and paste
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

  // Validate coordinates on submit
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