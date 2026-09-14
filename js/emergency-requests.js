// some fake starting data so the page isn't empty
let requests = [
    {
        id: 1,
        patientName: "Sarah Jenkins",
        bloodType: "O-",
        units: 3,
        urgency: "Critical",
        location: "Mercy General Hospital, ICU",
        contact: "555-0192",
        notes: "Surgery scheduled for tonight.",
        timestamp: new Date(Date.now() - 1000 * 60 * 15) // 15 mins ago
    },
    {
        id: 2,
        patientName: "Michael Chen",
        bloodType: "A+",
        units: 2,
        urgency: "High",
        location: "St. Jude Medical Center",
        contact: "555-8834",
        notes: "Needed for accident victim.",
        timestamp: new Date(Date.now() - 1000 * 60 * 45) // 45 mins ago
    },
    {
        id: 3,
        patientName: "Elena Rodriguez",
        bloodType: "AB-",
        units: 1,
        urgency: "High",
        location: "City Health Clinic",
        contact: "555-2211",
        notes: "Rare blood type needed urgently.",
        timestamp: new Date(Date.now() - 1000 * 60 * 120) // 2 hours ago
    }
];

let currentFilter = 'all';

// setup everything once the page loads
document.addEventListener('DOMContentLoaded', () => {
    renderRequests();

    // what happens when you click submit on the form
    document.getElementById('newRequestForm').addEventListener('submit', function(e) {
        e.preventDefault(); // stop the page from refreshing
        
        // grab all the details from the form
        const newRequest = {
            id: Date.now(),
            patientName: document.getElementById('patientName').value,
            bloodType: document.getElementById('bloodType').value,
            units: document.getElementById('units').value,
            urgency: document.getElementById('urgency').value,
            location: document.getElementById('location').value,
            contact: document.getElementById('contact').value,
            notes: document.getElementById('notes').value,
            timestamp: new Date()
        };

        // put the new request at the top of our list
        requests.unshift(newRequest);
        
        // clear the form out for the next time
        this.reset();
        
        // show a quick success message where the feed title is
        const feedHeader = document.querySelector('h2');
        const originalText = feedHeader.innerText;
        feedHeader.innerText = "REQUEST POSTED SUCCESSFULLY!";
        feedHeader.classList.add('text-green-600');
        
        // change it back to normal after 3 seconds
        setTimeout(() => {
            feedHeader.innerText = originalText;
            feedHeader.classList.remove('text-green-600');
        }, 3000);

        // draw the updated list on the screen
        renderRequests();
    });
});

// triggers when they use the dropdown to filter blood types
function filterRequests() {
    currentFilter = document.getElementById('bloodTypeFilter').value;
    renderRequests();
}

// calculates how long ago a request was posted
function formatTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " years ago";
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " months ago";
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " days ago";
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hours ago";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " mins ago";
    return "Just now";
}

// gives negative blood types a purple tint, and positive ones a red tint
function getBloodTypeColor(type) {
    if(type.includes('-')) return 'bg-purple-50 text-purple-800 border-purple-200';
    return 'bg-red-50 text-blood-700 border-red-200';
}

// handles copying the phone number to the clipboard when they click 'contact'
function handleContactClick(contact, id) {
     const btn = document.getElementById(`contact-btn-${id}`);
     const originalHTML = btn.innerHTML;
     
     // make the button turn green to show it worked
     btn.innerHTML = `<i class="fa-solid fa-check"></i> ${contact}`;
     btn.classList.replace('bg-blood-100', 'bg-green-100');
     btn.classList.replace('text-blood-700', 'text-green-700');
     
     // actually copy the text
     try {
         navigator.clipboard.writeText(contact);
     } catch(e) {
        // backup way to copy just in case the browser is older
        const tempInput = document.createElement("input");
        tempInput.value = contact;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
     }

     // change the button back after 3 seconds
     setTimeout(() => {
         btn.innerHTML = originalHTML;
         btn.classList.replace('bg-green-100', 'bg-blood-100');
         btn.classList.replace('text-green-700', 'text-blood-700');
     }, 3000);
}

// builds the actual HTML cards for the feed
function renderRequests() {
    const feedContainer = document.getElementById('requestsFeed');
    feedContainer.innerHTML = ''; // clear the old stuff out
    
    // check if we are filtering
    const filteredRequests = currentFilter === 'all' 
        ? requests 
        : requests.filter(r => r.bloodType === currentFilter);

    // if no requests match the filter, show a friendly message
    if (filteredRequests.length === 0) {
        feedContainer.innerHTML = `
            <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300 animate-fade-in-up">
                <i class="fa-solid fa-face-smile-beam text-4xl text-gray-300 mb-3"></i>
                <h3 class="text-lg font-medium text-gray-600">No active requests</h3>
                <p class="text-gray-400 text-sm mt-1">There are currently no urgent requests for this blood type.</p>
            </div>
        `;
        return;
    }

    // draw a card for each request
    filteredRequests.forEach((req, index) => {
        const isCritical = req.urgency === 'Critical';
        const urgencyColor = isCritical ? 'text-red-600 bg-red-50 border-red-200' : 'text-orange-600 bg-orange-50 border-orange-200';
        const urgencyIcon = isCritical ? 'fa-triangle-exclamation pulse-animation' : 'fa-clock';
        const typeStyle = getBloodTypeColor(req.bloodType);
        
        // Stagger the animation delay so they load one after another beautifully
        const animationDelay = (index * 0.1) + 0.3; 

        // the html structure of a single request card
        const cardHTML = `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow relative overflow-hidden group animate-fade-in-up" style="animation-delay: ${animationDelay}s;">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    
                    <div class="flex sm:flex-col items-center sm:items-start gap-4 sm:gap-2 sm:w-24 shrink-0">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center font-bold text-2xl border ${typeStyle} shadow-inner">
                            ${req.bloodType}
                        </div>
                        <div class="text-xs font-semibold px-2 py-1 rounded border ${urgencyColor} flex items-center gap-1 w-full justify-center sm:justify-start">
                            <i class="fa-solid ${urgencyIcon}"></i> ${req.urgency}
                        </div>
                    </div>
                    
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="font-bold text-lg text-gray-900">${req.patientName} <span class="text-sm font-semibold text-gray-500 ml-2">Needs ${req.units} Unit${req.units > 1 ? 's' : ''}</span></h3>
                            <span class="text-xs text-gray-400 font-medium flex items-center gap-1 whitespace-nowrap">
                                <i class="fa-regular fa-clock"></i> ${formatTimeAgo(req.timestamp)}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-600 space-y-1 mb-3">
                            <p class="flex items-start gap-2">
                                <i class="fa-solid fa-location-dot mt-1 text-gray-400 w-4 text-center"></i> 
                                <span>${req.location}</span>
                            </p>
                            ${req.notes ? `
                            <p class="flex items-start gap-2">
                                <i class="fa-solid fa-file-medical mt-1 text-gray-400 w-4 text-center"></i> 
                                <span class="italic text-gray-500">${req.notes}</span>
                            </p>` : ''}
                        </div>
                    </div>

                    <div class="flex sm:flex-col gap-2 justify-end sm:justify-center min-w-[120px]">
                        <button id="contact-btn-${req.id}" onclick="handleContactClick('${req.contact}', ${req.id})" class="w-full bg-blood-100 text-blood-700 hover:bg-blood-200 hover:text-blood-800 font-bold py-2.5 px-3 rounded-full transition-colors text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-phone"></i> Contact
                        </button>
                    </div>
                </div>
            </div>
        `;
        // drop the card into the feed container
        feedContainer.insertAdjacentHTML('beforeend', cardHTML);
    });
}

// keep checking every 60 seconds so the "time ago" numbers stay accurate
setInterval(renderRequests, 60000);