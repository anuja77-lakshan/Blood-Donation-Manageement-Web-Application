document.addEventListener('DOMContentLoaded', () => {
    //Initialize Map centered on Sri Lanka
    const map = L.map('bloodLinkMap').setView([7.8731, 80.7718], 8);

    // OpenStreetMap Tile Layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Size Refresh for Map rendaring
    setTimeout(() => {
        map.invalidateSize();
    }, 250);

    //Custom HTML Icons for Leaflet
    const campIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#ef3446; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-tent"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    const hospitalIcon = L.divIcon({
        className: 'custom-pin',
        html: '<div style="background-color:#0284c7; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3);"><i class="fa-solid fa-hospital"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Blood Donation Camps
    const donationCamps = [
        {
            title: "Anuradhapura General Mobilization Drive",
            date: "2026-08-25",
            location: "Faculty of Technology, RUSL",
            coords: [8.3585, 80.5050]
        },
        {
            title: "Colombo Community Blood Drive",
            date: "2026-08-28",
            location: "Red Cross Center, Colombo 07",
            coords: [6.9271, 79.8612]
        },
        {
            title: "Kandy Youth Blood Camp",
            date: "2026-09-02",
            location: "City Center, Kandy",
            coords: [7.2906, 80.6337]
        },
        {
            title: "Galle Fort Blood Donation Campaign",
            date: "2026-09-10",
            location: "Town Hall, Galle",
            coords: [6.0328, 80.2168]
        }
    ];

    //Data Set for Hospitals & Blood Banks
    const hospitals = [
        {
            name: "Anuradhapura Teaching Hospital Blood Bank",
            type: "National Blood Transfusion Service",
            contact: "+94 25 222 2261",
            coords: [8.3349, 80.4035]
        },
        {
            name: "National Blood Center (Narahenpita)",
            type: "Central Blood Bank",
            contact: "+94 11 269 8888",
            coords: [6.8941, 79.8776]
        },
        {
            name: "Peradeniya Teaching Hospital",
            type: "Regional Blood Center",
            contact: "+94 81 238 8001",
            coords: [7.2600, 80.5977]
        },
        {
            name: "Jaffna Teaching Hospital Blood Bank",
            type: "Northern Regional Center",
            contact: "+94 21 222 3333",
            coords: [9.6647, 80.0167]
        },
        {
            name: "Karapitiya Teaching Hospital Blood Bank",
            type: "Southern Provincial Blood Center",
            contact: "+94 91 223 2176",
            coords: [6.0652, 80.2246]
        }
    ];

    //Adding Camp Marks
    donationCamps.forEach(camp => {
        const popupContent = `
            <div style="font-family:'Inter', sans-serif; padding:5px;">
                <span style="color:#ef3446; font-size:10px; font-weight:bold; text-transform:uppercase;">Donation Camp</span>
                <h4 style="margin:4px 0; font-size:14px; color:#0f172a;">${camp.title}</h4>
                <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Date:</strong> ${camp.date}</p>
                <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Venue:</strong> ${camp.location}</p>
                <a href="camps.html" style="display:inline-block; margin-top:6px; font-size:11px; color:#ef3446; font-weight:bold; text-decoration:none;">Pledge for Drive &rarr;</a>
            </div>
        `;
        L.marker(camp.coords, { icon: campIcon }).addTo(map).bindPopup(popupContent);
    });

    //Adding Hospital Marks
    hospitals.forEach(hosp => {
        const popupContent = `
            <div style="font-family:'Inter', sans-serif; padding:5px;">
                <span style="color:#0284c7; font-size:10px; font-weight:bold; text-transform:uppercase;">Blood Bank / Hospital</span>
                <h4 style="margin:4px 0; font-size:14px; color:#0f172a;">${hosp.name}</h4>
                <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Facility:</strong> ${hosp.type}</p>
                <p style="margin:2px 0; font-size:12px; color:#64748b;"><strong>Contact:</strong> ${hosp.contact}</p>
            </div>
        `;
        L.marker(hosp.coords, { icon: hospitalIcon }).addTo(map).bindPopup(popupContent);
    });
});