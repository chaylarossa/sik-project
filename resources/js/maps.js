(() => {
    const mapElement = document.getElementById('map');
    const form = document.getElementById('map-filters');

    if (!mapElement || !form || typeof L === 'undefined') return;

    const map = L.map('map').setView([-2.5, 118], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);

    const buildQuery = () => {
        const params = new URLSearchParams(new FormData(form));
        return params.toString();
    };

    const renderMarkers = (points) => {
        markersLayer.clearLayers();

        points.forEach((point) => {
            const marker = L.marker([point.lat, point.lng]).addTo(markersLayer);
            const popup = `
                <div class="text-sm">
                    <div><strong>${point.type}</strong></div>
                    <div>Urgensi: ${point.urgency}</div>
                    <div>Status: ${point.status}</div>
                    <div>Waktu: ${point.occurred_at}</div>
                    <div class="mt-1"><a href="/reports/${point.id}" class="text-indigo-600">Detail</a></div>
                </div>
            `;
            marker.bindPopup(popup);
        });
    };

    const loadPoints = async () => {
        const query = buildQuery();
        const url = query ? `/api/internal/maps/crisis-points?${query}` : '/api/internal/maps/crisis-points';

        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to load map data');
            }

            const payload = await response.json();
            renderMarkers(payload.data || []);
        } catch (error) {
            console.error(error);
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadPoints();
    });

    loadPoints();
})();
