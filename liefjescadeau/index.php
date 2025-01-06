<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe vacay toevoegen!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Great+Vibes&family=La+Belle+Aurore&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Nieuwe vacay toevoegen!</h1>
        <form action="insert_location.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="location">Plaats naam:</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>


            <div id="map" class="mb-4" style="height: 400px;"></div>

            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="text" id="latitude" name="latitude" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="text" id="longitude" name="longitude" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="photos">Photos:</label>
                <input type="file" id="photos" name="photos[]" class="form-control-file" accept="image/*" multiple required>
            </div>

            <div id="photo-preview" class="mb-4"></div>

            <button type="submit" class="btn btn-primary">Add Location</button>
        </form>
    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([50.991, 4.5761], 4);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            fetch('fetch_locations.php')
                .then(response => response.json())
                .then(locations => {
                    locations.forEach(function(location) {
                        var marker = L.marker([location.latitude, location.longitude]).addTo(map);
                        marker.bindPopup("<b>" + location.location + "</b><br>" + location.start_date);
                        marker.on('click', function() {
                            displayPhotos(location.photos);
                        });
                    });
                })
                .catch(error => console.error('Error fetching locations:', error));

            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                var marker = L.marker([lat, lng]).addTo(map);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            });

            document.getElementById('photos').addEventListener('change', function(event) {
                var files = event.target.files;
                var previewContainer = document.getElementById('photo-preview');
                previewContainer.innerHTML = '';
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        previewContainer.appendChild(imgElement);
                    };
                    reader.readAsDataURL(files[i]);
                }
            });

            function displayPhotos(photos) {
                var photoContainer = document.getElementById('photo-container');
                if (!photoContainer) {
                    console.error('Photo container not found!');
                    return;
                }
                photoContainer.innerHTML = '';
                photos.forEach(function(photo) {
                    var imgElement = document.createElement('img');
                    imgElement.src = photo;
                    imgElement.classList.add('photo-img');
                    photoContainer.appendChild(imgElement);
                });
            }
        });
    </script>
</body>

</html>