document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.getElementById('mobile-menu-button');
    const menuPanel = document.getElementById('mobile-menu-panel');
    const menuClose = document.getElementById('mobile-menu-close');
    const menuOverlay = document.getElementById('mobile-menu-overlay');
    const liveStreamContent = document.getElementById('live-stream-content');
    const liveStreamIframe = document.querySelector('#live-stream-content iframe');

    const openMenu = () => {
        if (menuPanel) menuPanel.classList.remove('-translate-x-full');
        if (menuOverlay) menuOverlay.classList.remove('hidden');
    };

    const closeMenu = () => {
        if (menuPanel) menuPanel.classList.add('-translate-x-full');
        if (menuOverlay) menuOverlay.classList.add('hidden');
    };

    if (menuButton) menuButton.addEventListener('click', openMenu);
    if (menuClose) menuClose.addEventListener('click', closeMenu);
    if (menuOverlay) menuOverlay.addEventListener('click', closeMenu);

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, {
        threshold: 0.1
    });

    async function loadLiveStream() {
        try {
            const response = await fetch('api/live-stream-url.php');
            const data = await response.json();

            if (response.ok && data.url) {
                if (liveStreamIframe) {
                    liveStreamIframe.src = data.url;
                }
            } else if (liveStreamContent) {
                liveStreamContent.innerHTML = '<p class="text-red-500">Could not load the live stream. Please try again later.</p>';
            }
        } catch (error) {
            console.error('Error fetching live stream URL:', error);
            if (liveStreamContent) {
                liveStreamContent.innerHTML = '<p class="text-red-500">Network error. Please check your internet connection.</p>';
            }
        }
    }

    async function loadEvents() {
        const eventsContainer = document.getElementById('events-container');
        if (!eventsContainer) return;

        eventsContainer.innerHTML = '<p class="text-gray-600">Loading events...</p>';

        try {
            const response = await fetch('api/events.php');
            const events = await response.json();

            eventsContainer.innerHTML = '';

            if (response.ok && Array.isArray(events) && events.length > 0) {
                events.forEach(event => {
                    const eventCard = `
                        <div class="bg-white rounded-lg sleek-shadow overflow-hidden fade-in-section">
                            ${event.image_url ? `<img src="${event.image_url}" alt="${event.title}" class="w-full h-48 object-cover">` : ''}
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-2" style="color: var(--primary-blue);">${event.title}</h3>
                                <p class="text-gray-600 mb-2">
                                    ${event.date ? `<strong>Date:</strong> ${event.date}<br>` : ''}
                                    ${event.time ? `<strong>Time:</strong> ${event.time}<br>` : ''}
                                    ${event.location ? `<strong>Location:</strong> ${event.location}` : ''}
                                </p>
                                <p class="text-gray-700 text-sm">${event.description}</p>
                                ${event.learn_more_link ? `<a href="${event.learn_more_link}" class="mt-4 inline-block font-semibold" style="color: var(--accent-orange);">Learn More &rarr;</a>` : ''}
                            </div>
                        </div>
                    `;

                    eventsContainer.insertAdjacentHTML('beforeend', eventCard);
                    const insertedCard = eventsContainer.lastElementChild;
                    if (insertedCard) observer.observe(insertedCard);
                });
            } else {
                eventsContainer.innerHTML = '<p class="text-gray-600">No upcoming events found.</p>';
            }
        } catch (error) {
            console.error('Error fetching events:', error);
            eventsContainer.innerHTML = '<p class="text-red-500">Failed to load events. Please try again later.</p>';
        }
    }

    if (document.body.classList.contains('live-page')) {
        loadLiveStream();
    }

    if (window.location.pathname.includes('/events.html')) {
        loadEvents();
    }

    const sections = document.querySelectorAll('.fade-in-section');
    sections.forEach(section => {
        observer.observe(section);
    });
});
