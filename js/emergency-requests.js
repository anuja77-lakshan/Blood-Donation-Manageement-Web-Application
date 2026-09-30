let allRequestsData = [];

//get data from the backend
function loadRequests() {
    fetch('php/get_emergency_requests.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            allRequestsData = Array.isArray(data) ? data : [];
            filterRequests(); //render feed after data given
        })
        .catch(err => {
            console.error('Error loading requests:', err);
            let container = document.getElementById('requestsFeed');
            if (container) {
                container.innerHTML = '<p class="text-center py-6 text-red-500 font-medium">Failed to load emergency requests.</p>';
            }
        });
}

// render using blood group
function filterRequests() {
    let container = document.getElementById('requestsFeed');
    if (!container) return;

    let filterElem = document.getElementById('bloodTypeFilter');
    let selectedGroup = filterElem ? filterElem.value.trim().toLowerCase() : 'all';

    container.innerHTML = '';

    let filteredList = allRequestsData.filter(item => {
        if (selectedGroup === 'all' || selectedGroup === 'all types') {
            return true;
        }
        return item.blood_group && item.blood_group.toLowerCase() === selectedGroup;
    });

    if (filteredList.length === 0) {
        container.innerHTML = '<p class="text-center py-6 text-gray-500 font-medium">No emergency requests found for this blood group.</p>';
        return;
    }

    filteredList.forEach(item => {
        let isCritical = item.status && item.status.toLowerCase().includes('critical');
        let badgeClass = isCritical ? 'bg-red-100 text-red-700 border-red-200' : 'bg-orange-100 text-orange-700 border-orange-200';
        let badgeText = isCritical ? 'Critical' : 'High';
        let icon = isCritical ? 'fa-triangle-exclamation' : 'fa-clock';

        let card = `
            <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all flex items-center justify-between mb-3">
        <div class="flex items-center gap-4">
         <div class="w-12 h-12 bg-red-100 text-blood-600 font-black rounded-xl flex items-center justify-center text-lg border border-red-200">
                        ${item.blood_group}
         </div>
            <div>
         <div class="flex items-center gap-2">
            <h4 class="font-bold text-gray-900">${item.patient_name}</h4>
                 <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border ${badgeClass} flex items-center gap-1">
                 <i class="fa-solid ${icon}"></i> ${badgeText}
                </span>
         </div>
                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                <i class="fa-solid fa-hospital text-gray-400"></i> ${item.hospital}
                </p>
        </div>
        </div>
                <div>
                    <a href="tel:${item.contact_no}" class="border border-blood-600 text-blood-600 hover:bg-blood-50 px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                        <i class="fa-solid fa-phone"></i> Contact
                    </a>
                </div>
            </div>
        `;
        container.innerHTML += card;
    });
}

// initial setup
document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
    setInterval(loadRequests, 15000); // refresh every 15 sec

    // Filter dropdown 
    let filterElem = document.getElementById('bloodTypeFilter');
    if (filterElem) {
        filterElem.addEventListener('change', filterRequests);
    }

    // Form Submit 
    let form = document.getElementById('newRequestForm');
    if (form) {
        form.onsubmit = function(e) {
            e.preventDefault();

            let patientName = document.getElementById('patientName') ? document.getElementById('patientName').value.trim() : '';
            let bloodType   = document.getElementById('bloodType') ? document.getElementById('bloodType').value : '';
            let location    = document.getElementById('location') ? document.getElementById('location').value.trim() : '';
            let contact     = document.getElementById('contact') ? document.getElementById('contact').value.trim() : '';
            let urgencyElem = document.getElementById('Emergency');
            let urgency     = urgencyElem ? urgencyElem.value : 'Critical';

            if (!patientName || !bloodType || !location || !contact) {
                alert('Please fill all required fields!');
                return;
            }

            let requestData = {
                patientName: patientName,
                bloodType: bloodType,
                urgency: urgency,
                location: location,
                contact: contact
            };

            fetch('php/add_emergency_request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Request posted successfully!');
                    form.reset();
                    loadRequests(); // live feed update after submit
                } else {
                    alert('Backend Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Request failed: ' + error.message);
            });
        };
    }
});