document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('bloodLinkMap');
    if (!mapElement) return;

    // Sri Lankan Map
    const map = L.map('bloodLinkMap').setView([7.8731, 80.7718], 7.5);

    // ArcGIS World Street Map Layer
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    // Stop Render glitch 
    setTimeout(() => {
        map.invalidateSize();
    }, 300);

    // Custom HTML Icons
    const campIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#ef3446; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.4);"><i class="fa-solid fa-tent" style="font-size:14px;"></i></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    const hospitalIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#0284c7; color:white; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-hospital" style="font-size:12px;"></i></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -14]
    });

    // Main Hospitals and Base Hospitals
    const hospitals = [
        // Western Province
        { name: "National Blood Center", type: "National Blood Transfusion Service", contact: "+94 11 269 8888", coords: [6.8941, 79.8776] },
        { name: "National Hospital of Sri Lanka (Colombo)", type: "National Referral Hospital", contact: "+94 11 269 1111", coords: [6.9186, 79.8687] },
        { name: "Colombo South Teaching Hospital (Kalubowila)", type: "Teaching Hospital", contact: "+94 11 276 3064", coords: [6.8735, 79.8825] },
        { name: "Colombo North Teaching Hospital (Ragama)", type: "Teaching Hospital", contact: "+94 11 295 9261", coords: [7.0298, 79.9197] },
        { name: "District General Hospital Negombo", type: "District General Hospital", contact: "+94 31 222 2261", coords: [7.2094, 79.8407] },
        { name: "District General Hospital Gampaha", type: "District General Hospital", contact: "+94 33 222 2261", coords: [7.0917, 79.9998] },
        { name: "Base Hospital Avissawella", type: "Base Hospital", contact: "+94 36 222 2261", coords: [6.9536, 80.2078] },
        { name: "Base Hospital Panadura", type: "Base Hospital", contact: "+94 38 223 2261", coords: [6.7135, 79.9074] },
        { name: "Base Hospital Horana", type: "Base Hospital", contact: "+94 34 226 1261", coords: [6.7142, 80.0638] },

        // Central Province
        { name: "National Hospital, Kandy", type: "Teaching Hospital", contact: "+94 81 222 2261", coords: [7.2882, 80.6277] },
        { name: "Peradeniya Teaching Hospital", type: "Teaching Hospital", contact: "+94 81 238 8001", coords: [7.2600, 80.5977] },
        { name: "District General Hospital Matale", type: "District General Hospital", contact: "+94 66 222 2261", coords: [7.4675, 80.6234] },
        { name: "District General Hospital Nawalapitiya", type: "District General Hospital", contact: "+94 54 222 2261", coords: [7.0544, 80.5342] },
        { name: "District General Hospital Nuwara Eliya", type: "District General Hospital", contact: "+94 52 222 2261", coords: [6.9695, 80.7718] },
        { name: "Base Hospital Gampola", type: "Base Hospital", contact: "+94 81 235 2261", coords: [7.1633, 80.5692] },
        { name: "Base Hospital Dambulla", type: "Base Hospital", contact: "+94 66 228 4761", coords: [7.8731, 80.6517] },

        // Southern Province
        { name: "Karapitiya Teaching Hospital", type: "Teaching Hospital & Blood Bank", contact: "+94 91 223 2176", coords: [6.0652, 80.2246] },
        { name: "District General Hospital Matara", type: "District General Hospital", contact: "+94 41 222 2261", coords: [5.9496, 80.5469] },
        { name: "District General Hospital Hambantota", type: "District General Hospital", contact: "+94 47 222 0261", coords: [6.1246, 81.1185] },
        { name: "Base Hospital Balapitiya", type: "Base Hospital", contact: "+94 91 225 8261", coords: [6.2731, 80.0406] },
        { name: "Base Hospital Tangalle", type: "Base Hospital", contact: "+94 47 224 0261", coords: [6.0244, 80.7941] },
        { name: "Base Hospital Elpitiya", type: "Base Hospital", contact: "+94 91 229 1261", coords: [6.2568, 80.1417] },

        // North Western Province
        { name: "Teaching Hospital Kurunegala", type: "Teaching Hospital", contact: "+94 37 222 2261", coords: [7.4842, 80.3644] },
        { name: "District General Hospital Chilaw", type: "District General Hospital", contact: "+94 32 222 2261", coords: [7.5758, 79.7953] },
        { name: "Base Hospital Kuliyapitiya", type: "Base Hospital", contact: "+94 37 228 1261", coords: [7.4689, 80.0401] },
        { name: "Base Hospital Puttalam", type: "Base Hospital", contact: "+94 32 226 5261", coords: [8.0362, 79.8283] },

        // North Central Province
        { name: "Anuradhapura Teaching Hospital", type: "Teaching Hospital & Regional Blood Center", contact: "+94 25 222 2261", coords: [8.3349, 80.4035] },
        { name: "District General Hospital Polonnaruwa", type: "District General Hospital", contact: "+94 27 222 2261", coords: [7.9403, 81.0188] },
        { name: "Base Hospital Medirigiriya", type: "Base Hospital", contact: "+94 27 224 8261", coords: [8.1512, 80.9723] },
        { name: "Base Hospital Thambuttegama", type: "Base Hospital", contact: "+94 25 227 6261", coords: [8.1534, 80.2976] },

        // Uva Province
        { name: "Provincial General Hospital Badulla", type: "Provincial General Hospital", contact: "+94 55 222 2261", coords: [6.9895, 81.0557] },
        { name: "District General Hospital Monaragala", type: "District General Hospital", contact: "+94 55 227 6261", coords: [6.8728, 81.3507] },
        { name: "Base Hospital Bandarawela", type: "Base Hospital", contact: "+94 57 222 2261", coords: [6.8259, 80.9982] },
        { name: "Base Hospital Mahiyanganaya", type: "Base Hospital", contact: "+94 55 225 7261", coords: [7.3167, 80.9833] },

        // Sabaragamuwa Province
        { name: "Teaching Hospital Ratnapura", type: "Teaching Hospital", contact: "+94 45 222 2261", coords: [6.6961, 80.3956] },
        { name: "District General Hospital Kegalle", type: "District General Hospital", contact: "+94 35 222 2261", coords: [7.2513, 80.3464] },
        { name: "Base Hospital Balangoda", type: "Base Hospital", contact: "+94 45 228 7261", coords: [6.6508, 80.7011] },
        { name: "Base Hospital Karawanella", type: "Base Hospital", contact: "+94 36 226 7261", coords: [7.0215, 80.2589] },
        { name: "Base Hospital Embilipitiya", type: "Base Hospital", contact: "+94 47 223 0261", coords: [6.3431, 80.8514] },

        // Eastern Province
        { name: "Teaching Hospital Batticaloa", type: "Teaching Hospital", contact: "+94 65 222 2261", coords: [7.7170, 81.7001] },
        { name: "District General Hospital Trincomalee", type: "District General Hospital", contact: "+94 26 222 2261", coords: [8.5711, 81.2335] },
        { name: "District General Hospital Ampara", type: "District General Hospital", contact: "+94 63 222 2261", coords: [7.2912, 81.6747] },
        { name: "Base Hospital Kalmunai", type: "Base Hospital", contact: "+94 67 222 9261", coords: [7.4124, 81.8267] },

        // Northern Province
        { name: "Teaching Hospital Jaffna", type: "Teaching Hospital", contact: "+94 21 222 3333", coords: [9.6647, 80.0167] },
        { name: "District General Hospital Vavuniya", type: "District General Hospital", contact: "+94 24 222 2261", coords: [8.7514, 80.4971] },
        { name: "District General Hospital Kilinochchi", type: "District General Hospital", contact: "+94 21 228 5328", coords: [9.3803, 80.3985] },
        { name: "District General Hospital Mannar", type: "District General Hospital", contact: "+94 23 222 2261", coords: [8.9810, 79.9044] },
        { name: "District General Hospital Mullaitivu", type: "District General Hospital", contact: "+94 21 229 0024", coords: [9.2671, 80.8142] },
        { name: "Base Hospital Point Pedro", type: "Base Hospital", contact: "+94 21 226 2261", coords: [9.8242, 80.2333] }
    ];

    //Adding Hospital Marks
    hospitals.forEach(hosp => {
        L.marker(hosp.coords, { icon: hospitalIcon })
            .addTo(map)
            .bindPopup(`
                <div style="font-family:'Inter', sans-serif; padding:5px; min-width: 170px;">
                    <span style="color:#0284c7; font-size:10px; font-weight:bold; text-transform:uppercase;">Blood Bank / Hospital</span>
                    <h4 style="margin:4px 0; font-size:14px; color:#0f172a;">${hosp.name}</h4>
                    <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Facility:</strong> ${hosp.type}</p>
                    <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Contact:</strong> ${hosp.contact}</p>
                </div>
            `);
    });

    // Read Camps in Database Then It Pin to Map
    async function loadDynamicCamps() {
        try {
            const response = await fetch('/bloodlink/php/get_camps_locations.php');
            if (!response.ok) {
                console.error("HTTP Error fetching camps:", response.status);
                return;
            }

            const donationCamps = await response.json();
            console.log("Found Camps:", donationCamps.length, donationCamps);

            donationCamps.forEach(camp => {
                const lat = parseFloat(camp.latitude);
                const lng = parseFloat(camp.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    // zIndexOffset: 1000 Highlight the camp locations
                    L.marker([lat, lng], { 
                        icon: campIcon,
                        zIndexOffset: 1000 
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-family:'Inter', sans-serif; padding:6px; min-width: 190px;">
                            <span style="color:#ef3446; font-size:11px; font-weight:700; text-transform:uppercase;">🩸 Active Donation Camp</span>
                            <h4 style="margin:4px 0; font-size:14px; color:#0f172a;">${camp.camp_name}</h4>
                            <p style="margin:2px 0; font-size:12px; color:#475569;"><strong>Organizer:</strong> ${camp.org_name || 'Organization'}</p>
                            <p style="margin:2px 0; font-size:12px; color:#475569;"><strong>Date:</strong> ${camp.camp_date}</p>
                            <p style="margin:2px 0; font-size:12px; color:#475569;"><strong>Venue:</strong> ${camp.location}</p>
                            <a href="camps.php" style="display:inline-block; margin-top:6px; font-size:11px; color:#ef3446; font-weight:bold; text-decoration:none;">View Camp Details &rarr;</a>
                        </div>
                    `);
                }
            });
        } catch (error) {
            console.error("Error loading live camps on map:", error);
        }
    }

    loadDynamicCamps();
});