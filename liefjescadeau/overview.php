<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Overview</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Great+Vibes&family=La+Belle+Aurore&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h1 class="mb-4">Liefjes vakantie!</h1>
        <div class="text-center mb-4">
            <a href="index.php">
                <button class="btn" id="valentineButton">Vakantie toevoegen!</button>
            </a>
        </div>
        <div class="row">
            <!-- Map Section -->
            <div class="col-md-6">
                <div id="map"></div>
            </div>

            <!-- Location Details Section -->
            <div class="col-md-6" id="location-details" style="display: none;">
                <h4 id="location-title"></h4>
                <h5><strong>Startdatum:</strong> <span id="location-start_date"></span></h5>
                <h5><strong>Einddatum:</strong> <span id="location-end_date"></span></h5>
                <div class="photos-container" id="location-photos"></div>
            </div>
        </div>
    </div>

    <!-- Modal for fullscreen image view -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button type="button" id="prevButton" class="btn btn-light">←</button>
                    <img id="modal-image" class="img-fluid" src="" alt="Image Preview">
                    <button type="button" id="nextButton" class="btn btn-light">→</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([50.991, 4.5761], 4); // Center map

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Fetch locations from the PHP server
            fetch('fetch_locations.php')
                .then(response => response.json())
                .then(locations => {
                    locations.forEach(function(location) {
                        // Create a custom icon with pink color
                        var pinkIcon = L.icon({
                            iconUrl: 'https://static.vecteezy.com/system/resources/thumbnails/018/887/903/small_2x/location-map-icon-png.png', // Replace with any pink icon
                            iconSize: [25, 41], // Size of the marker
                            iconAnchor: [12, 41], // Anchor point of the marker
                            popupAnchor: [1, -34], // Popup position relative to the icon
                            shadowSize: [41, 41] // Size of the shadow
                        });

                        // Add marker with the custom pink icon
                        var marker = L.marker([location.latitude, location.longitude], {
                            icon: pinkIcon
                        }).addTo(map);
                        marker.bindPopup("<b>" + location.location + "</b><br>" + location.start_date);

                        // When marker is clicked, show location details in the right section
                        marker.on('click', function() {
                            showLocationDetails(location);
                        });
                    });
                })
                .catch(error => console.error('Error fetching locations:', error));

            // Function to show location details in the right section
            function showLocationDetails(location) {
                document.getElementById('location-title').textContent = location.location;
                document.getElementById('location-start_date').textContent = location.start_date;
                document.getElementById('location-end_date').textContent = location.end_date;

                // Show photos in the right section
                var photosContainer = document.getElementById('location-photos');
                photosContainer.innerHTML = ''; // Clear previous photos

                if (location.photos && Array.isArray(location.photos)) {
                    location.photos.forEach(function(photo, index) {
                        // Create an image element with click to enlarge functionality
                        var photoElement = document.createElement('img');
                        photoElement.src = photo; // Thumbnail
                        photoElement.classList.add('img-thumbnail'); // Optional: Add some styling
                        photoElement.style.width = '100%'; // Optional: Set photo size

                        // When clicked, open the photo in the modal
                        photoElement.addEventListener('click', function() {
                            openModal(location.photos, index);
                        });

                        // Append the photo to the photos container
                        photosContainer.appendChild(photoElement);
                    });
                }

                // Show the location details section
                document.getElementById('location-details').style.display = 'block';
            }

            // Function to open the image in the modal
            function openModal(photos, currentIndex) {
                var modalImage = document.getElementById('modal-image');
                modalImage.src = photos[currentIndex]; // Set the image source to the clicked image's src

                // Setup the navigation buttons
                document.getElementById('prevButton').onclick = function() {
                    if (currentIndex > 0) {
                        currentIndex--;
                        modalImage.src = photos[currentIndex];
                    }
                };
                document.getElementById('nextButton').onclick = function() {
                    if (currentIndex < photos.length - 1) {
                        currentIndex++;
                        modalImage.src = photos[currentIndex];
                    }
                };

                $('#imageModal').modal('show'); // Open the modal
            }
        });
    </script>
</body>

</html>