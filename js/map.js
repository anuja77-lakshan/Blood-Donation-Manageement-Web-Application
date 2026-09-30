document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('bloodLinkMap');
    if (!mapElement) return;

    // Sri Lanka Center View
    const map = L.map('bloodLinkMap', {
        maxZoom: 19
    }).setView([7.8731, 80.7718], 7.5);

    // OpenStreetMap Street level High  Zoom Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    setTimeout(() => {
        map.invalidateSize();
    }, 300);

    // 
    // 
    const campIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#ef3446; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.4);"><i class="fa-solid fa-tent" style="font-size:14px;"></i></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, -16]
    });

    // Custom Icon for Hospitals & Blood Banks 
    const hospitalIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#0284c7; color:white; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-hospital" style="font-size:12px;"></i></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -14]
    });

    // Hospital list
    const hospitals = [
        // --- Western Province ---
        { name: "National Blood Center (NBTS)", type: "National Blood Transfusion Service Headquarters", contact: "+94 11 269 8888", coords: [6.8941, 79.8776] },
        { name: "National Hospital of Sri Lanka (NHSL)", type: "National Referral Hospital", contact: "+94 11 269 1111", coords: [6.9186, 79.8687] },
        { name: "Lady Ridgeway Hospital for Children (LRH)", type: "Specialized Pediatric Teaching Hospital", contact: "+94 11 269 3711", coords: [6.9205, 79.8732] },
        { name: "Castle Street Hospital for Women (CSHW)", type: "Specialized Maternity Teaching Hospital", contact: "+94 11 269 6231", coords: [6.9114, 79.8837] },
        { name: "De Soysa Hospital for Women (DMH)", type: "Maternity Teaching Hospital", contact: "+94 11 269 6224", coords: [6.9171, 79.8667] },
        { name: "Apeksha Hospital (National Cancer Institute Maharagama)", type: "Specialized Oncology Teaching Hospital", contact: "+94 11 285 0253", coords: [6.8488, 79.9272] },
        { name: "Colombo South Teaching Hospital (Kalubowila)", type: "Teaching Hospital & Regional Blood Bank", contact: "+94 11 276 3064", coords: [6.8735, 79.8825] },
        { name: "Colombo North Teaching Hospital (Ragama)", type: "Teaching Hospital & Blood Bank", contact: "+94 11 295 9261", coords: [7.0298, 79.9197] },
        { name: "Sri Jayewardenepura General Hospital", type: "General Hospital & Blood Bank", contact: "+94 11 277 8610", coords: [6.8758, 79.9213] },
        { name: "District General Hospital Negombo", type: "District General Hospital", contact: "+94 31 222 2261", coords: [7.2094, 79.8407] },
        { name: "District General Hospital Gampaha", type: "District General Hospital", contact: "+94 33 222 2261", coords: [7.0917, 79.9998] },
        { name: "District General Hospital Kalutara", type: "District General Hospital", contact: "+94 34 222 2261", coords: [6.5854, 79.9607] },
        { name: "Base Hospital Panadura", type: "Base Hospital", contact: "+94 38 223 2261", coords: [6.7135, 79.9074] },
        { name: "Base Hospital Horana", type: "Base Hospital", contact: "+94 34 226 1261", coords: [6.7142, 80.0638] },
        { name: "Base Hospital Avissawella", type: "Base Hospital", contact: "+94 36 222 2261", coords: [6.9536, 80.2078] },
        { name: "Base Hospital Homagama", type: "Base Hospital", contact: "+94 11 285 5261", coords: [6.8417, 80.0039] },
        { name: "Base Hospital Mirigama", type: "Base Hospital", contact: "+94 33 227 3261", coords: [7.2415, 80.1305] },
        { name: "Base Hospital Wathupitiwala", type: "Base Hospital", contact: "+94 33 228 0261", coords: [7.1352, 80.1118] },

        // --- Central Province ---
        { name: "National Hospital Kandy", type: "National Referral & Teaching Hospital", contact: "+94 81 222 2261", coords: [7.2882, 80.6277] },
        { name: "Peradeniya Teaching Hospital", type: "Teaching Hospital & Blood Bank", contact: "+94 81 238 8001", coords: [7.2600, 80.5977] },
        { name: "Sirimavo Bandaranaike Specialized Children's Hospital", type: "Pediatric Teaching Hospital", contact: "+94 81 238 8870", coords: [7.2589, 80.5992] },
        { name: "District General Hospital Matale", type: "District General Hospital", contact: "+94 66 222 2261", coords: [7.4675, 80.6234] },
        { name: "District General Hospital Nuwara Eliya", type: "District General Hospital", contact: "+94 52 222 2261", coords: [6.9708, 80.7712] },
        { name: "Base Hospital Gampola", type: "Base Hospital", contact: "+94 81 235 2261", coords: [7.1645, 80.5694] },
        { name: "Base Hospital Nawalapitiya", type: "Base Hospital", contact: "+94 54 222 2261", coords: [7.0543, 80.5348] },
        { name: "Base Hospital Dambulla", type: "Base Hospital", contact: "+94 66 228 4761", coords: [7.8596, 80.6515] },

        // --- Southern Province ---
        { name: "Karapitiya Teaching Hospital (Galle)", type: "Teaching Hospital & Regional Blood Center", contact: "+94 91 223 2176", coords: [6.0652, 80.2246] },
        { name: "Mahamodara Maternity Hospital (Galle)", type: "Specialized Maternity Hospital", contact: "+94 91 222 2261", coords: [6.0402, 80.2081] },
        { name: "District General Hospital Matara", type: "District General Hospital", contact: "+94 41 222 2261", coords: [5.9496, 80.5469] },
        { name: "District General Hospital Hambantota", type: "District General Hospital", contact: "+94 47 222 0261", coords: [6.1264, 81.1185] },
        { name: "Base Hospital Elpitiya", type: "Base Hospital", contact: "+94 91 229 1261", coords: [6.2573, 80.1418] },
        { name: "Base Hospital Balapitiya", type: "Base Hospital", contact: "+94 91 225 8261", coords: [6.2731, 80.0384] },
        { name: "Base Hospital Tangalle", type: "Base Hospital", contact: "+94 47 224 0261", coords: [6.0242, 80.7942] },

        // --- North Western (Wayamba) Province ---
        { name: "Kurunegala Teaching Hospital", type: "Teaching Hospital & Regional Blood Center", contact: "+94 37 222 2261", coords: [7.4842, 80.3644] },
        { name: "District General Hospital Chilaw", type: "District General Hospital", contact: "+94 32 222 2261", coords: [7.5758, 79.7952] },
        { name: "Base Hospital Kuliyapitiya", type: "Teaching & Base Hospital", contact: "+94 37 228 1261", coords: [7.4688, 80.0401] },
        { name: "Base Hospital Puttalam", type: "Base Hospital", contact: "+94 32 226 5261", coords: [8.0362, 79.8283] },

        // --- North Central Province ---
        { name: "Teaching Hospital Anuradhapura", type: "Teaching Hospital & Regional Blood Center", contact: "+94 25 222 2261", coords: [8.3349, 80.4035] },
        { name: "District General Hospital Polonnaruwa", type: "District General Hospital", contact: "+94 27 222 2261", coords: [7.9403, 81.0188] },
        { name: "Base Hospital Medirigiriya", type: "Base Hospital", contact: "+94 27 224 8261", coords: [8.1472, 80.9723] },

        // --- Uva Province ---
        { name: "Provincial General Hospital Badulla", type: "Provincial General Hospital & Blood Center", contact: "+94 55 222 2261", coords: [6.9895, 81.0557] },
        { name: "District General Hospital Monaragala", type: "District General Hospital", contact: "+94 55 227 6261", coords: [6.8728, 81.3487] },
        { name: "Base Hospital Diyatalawa", type: "Base Hospital", contact: "+94 57 222 9261", coords: [6.8189, 80.9632] },
        { name: "Base Hospital Mahiyanganaya", type: "Base Hospital", contact: "+94 55 225 7261", coords: [7.3168, 80.9982] },

        // --- Sabaragamuwa Province ---
        { name: "Teaching Hospital Ratnapura", type: "Teaching Hospital & Regional Blood Center", contact: "+94 45 222 2261", coords: [6.6961, 80.3956] },
        { name: "District General Hospital Kegalle", type: "District General Hospital", contact: "+94 35 222 2261", coords: [7.2537, 80.3464] },
        { name: "Base Hospital Balangoda", type: "Base Hospital", contact: "+94 45 228 7261", coords: [6.6492, 80.7011] },
        { name: "Base Hospital Karawanella", type: "Base Hospital", contact: "+94 36 226 6261", coords: [7.0191, 80.2588] },
        { name: "Base Hospital Embilipitiya", type: "Base Hospital", contact: "+94 47 223 0261", coords: [6.3382, 80.8517] },

        // --- Eastern Province ---
        { name: "Teaching Hospital Batticaloa", type: "Teaching Hospital & Regional Blood Center", contact: "+94 65 222 2261", coords: [7.7170, 81.7001] },
        { name: "District General Hospital Trincomalee", type: "District General Hospital", contact: "+94 26 222 2261", coords: [8.5711, 81.2335] },
        { name: "District General Hospital Ampara", type: "District General Hospital", contact: "+94 63 222 2261", coords: [7.2831, 81.6747] },
        { name: "Base Hospital Kalmunai (North)", type: "Base Hospital", contact: "+94 67 222 9261", coords: [7.4132, 81.8284] },
        { name: "Base Hospital Kantale", type: "Base Hospital", contact: "+94 26 223 4261", coords: [8.3685, 80.9856] },

        // --- Northern Province ---
        { name: "Teaching Hospital Jaffna", type: "Teaching Hospital & Regional Blood Center", contact: "+94 21 222 3333", coords: [9.6647, 80.0167] },
        { name: "District General Hospital Vavuniya", type: "District General Hospital", contact: "+94 24 222 2261", coords: [8.7542, 80.4982] },
        { name: "District General Hospital Kilinochchi", type: "District General Hospital", contact: "+94 21 228 5261", coords: [9.3958, 80.4042] },
        { name: "District General Hospital Mannar", type: "District General Hospital", contact: "+94 23 222 2261", coords: [8.9810, 79.9044] },
        { name: "District General Hospital Mullaitivu", type: "District General Hospital", contact: "+94 21 229 3261", coords: [9.2671, 80.8142] },
        { name: "Base Hospital Point Pedro", type: "Base Hospital", contact: "+94 21 226 2261", coords: [9.8242, 80.2331] }
    ];

    // Render Hospitals on Map
    hospitals.forEach(hosp => {
        L.marker(hosp.coords, { icon: hospitalIcon })
            .addTo(map)
            .bindPopup(`
                <div style="font-family:'Inter', sans-serif; padding:6px; min-width: 190px;">
                    <span style="color:#0284c7; font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px;">Hospital / Blood Bank</span>
                    <h4 style="margin:4px 0 6px 0; font-size:13px; color:#0f172a; font-weight:700;">${hosp.name}</h4>
                    <p style="margin:2px 0; font-size:11px; color:#475569;"><strong>Facility:</strong> ${hosp.type}</p>
                    <p style="margin:2px 0 6px 0; font-size:11px; color:#475569;"><strong>Contact:</strong> ${hosp.contact}</p>
                    <a href="tel:${hosp.contact}" style="display:inline-flex; align-items:center; gap:4px; font-size:11px; color:#0284c7; font-weight:700; text-decoration:none;">
                        <i class="fa-solid fa-phone" style="font-size:10px;"></i> Call Helpline
                    </a>
                </div>
            `);
    });

    // Load Live Active Donation Camps from Database
    async function loadDynamicCamps() {
        try {
            const response = await fetch('php/get_camps_locations.php');
            if (!response.ok) return;

            const donationCamps = await response.json();

            donationCamps.forEach(camp => {
                const lat = parseFloat(camp.latitude);
                const lng = parseFloat(camp.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const isExpired = camp.minutes_left <= 0;
                    const statusBadge = isExpired 
                        ? '<span style="color:#64748b; font-size:10px; font-weight:700; text-transform:uppercase;">⌛ Camp Ended (Expired)</span>'
                        : '<span style="color:#ef3446; font-size:10px; font-weight:700; text-transform:uppercase;">🩸 Active Donation Camp</span>';

                    L.marker([lat, lng], { 
                        icon: campIcon,
                        zIndexOffset: 1000 
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-family:'Inter', sans-serif; padding:6px; min-width: 190px;">
                            ${statusBadge}
                            <h4 style="margin:4px 0 6px 0; font-size:13px; color:#0f172a; font-weight:700;">${camp.camp_name}</h4>
                            <p style="margin:2px 0; font-size:11px; color:#475569;"><strong>Organizer:</strong> ${camp.org_name || 'Organization'}</p>
                            <p style="margin:2px 0; font-size:11px; color:#475569;"><strong>Date:</strong> ${camp.camp_date}</p>
                            <p style="margin:2px 0 6px 0; font-size:11px; color:#475569;"><strong>Venue:</strong> ${camp.location}</p>
                            <a href="camps.php" style="display:inline-block; font-size:11px; color:#ef3446; font-weight:bold; text-decoration:none;">View Camp Details &rarr;</a>
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