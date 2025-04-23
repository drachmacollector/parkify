<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Add Parking</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
<a href="admin_panel.php" style="display: inline-block; margin-bottom: 20px; text-decoration: none;">
  <button id="goback">
    ← Back to Admin Panel
  </button>
</a>
  <h2>Click on the map to add a parking spot</h2>
  <div id="map"></div>

  <form id="parkingForm" action="add_parking.php" method="post">
    <input type="text" name="name" placeholder="Parking Name" required />
    <input type="text" name="city" placeholder="City" required />
    <input type="text" name="area" placeholder="Area" required />
    <input type="number" name="total_slots" placeholder="Total Slots" required />
    <input type="number" name="available_slots" placeholder="Available Slots" required />
    <input type="hidden" name="lat" />
    <input type="hidden" name="lon" />
    <button type="submit" id="add">➕ Add Parking Spot</button>
  </form>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([20.5937, 78.9629], 5); // Centered on India

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    map.on('click', function(e) {
      const {
        lat,
        lng
      } = e.latlng;
      document.querySelector('[name="lat"]').value = lat;
      document.querySelector('[name="lon"]').value = lng;

      if (marker) {
        marker.setLatLng(e.latlng);
      } else {
        marker = L.marker(e.latlng).addTo(map);
      }
    });

    document.getElementById('parkingForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('add_parking.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(msg => {
          alert(msg);
          this.reset();
          if (marker) map.removeLayer(marker);
        });
    });
  </script>
  <canvas id="particles"></canvas>
  <script src="particles.js"></script>

</body>

</html>