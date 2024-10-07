<?php   
include('../includes/db_connection.php');

// Fetch all resorts from the database
$query = "SELECT * FROM resorts GROUP BY name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resorts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4a261;
            color: white;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .action-buttons a:hover {
            color: #0056b3;
        }
        .images img {
            width: 100px;
            height: auto;
            margin-right: 10px;
            cursor: pointer;
        }
        /* Modal styles */
        #imageModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        #modalContent {
            position: relative;
            width: 100%;
            height: 100%;
        }
        #modalImage {
            width: 100%;  /* Full width for better display */
            height: auto; /* Maintain aspect ratio */
            max-height: 90%; /* Limit height for full view */
            object-fit: contain; /* Maintain aspect ratio within bounds */
        }
        #closeModal {
            color: white;
            font-size: 30px;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 30px;
        }
        .prev {
            left: 20px;
        }
        .next {
            right: 20px;
        }
    </style>
</head>
<body>

    <h1>Manage Resorts</h1>
    
    <table>
        <tr>
            <th>Resort ID</th>
            <th>Name</th>
            <th>Location</th>
            <th>Address</th>
            <th>Price Range</th>
            <th>Description</th> <!-- Added Description header -->
            <th>Images</th>
            <th>Google Map</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($resort = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($resort['resort_id']); ?></td>
                <td><?php echo htmlspecialchars($resort['name']); ?></td>
                <td><?php echo htmlspecialchars($resort['location']); ?></td>
                <td><?php echo htmlspecialchars($resort['address']); ?></td>
                <td><?php echo htmlspecialchars($resort['price_range_low']) . ' - ' . htmlspecialchars($resort['price_range_high']); ?></td>
                <td><?php echo htmlspecialchars($resort['description']); ?></td> <!-- Displaying description -->
                <td class="images">
                    <?php 
                    for ($i = 1; $i <= 4; $i++): 
                        $image_url = "../images/resort1/image$i.png";
                        if (file_exists($image_url)): 
                    ?>
                        <img src="<?php echo $image_url; ?>" alt="Image <?php echo $i; ?>" onclick="openModal(<?php echo $resort['resort_id']; ?>, <?php echo $i - 1; ?>)">
                    <?php else: ?>
                        <img src="../images/default.png" alt="No Image Available" onclick="openModal(<?php echo $resort['resort_id']; ?>, 0)">
                    <?php endif; endfor; ?>
                </td>
                <td>
                    <a id="directions-link-<?php echo $resort['resort_id']; ?>" href="#" target="_blank">Get Directions</a>
                    <script>
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var latitude = position.coords.latitude;
                                var longitude = position.coords.longitude;
                                var resortLat = <?php echo $resort['latitude']; ?>;
                                var resortLng = <?php echo $resort['longitude']; ?>;
                                var googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${latitude},${longitude}&destination=${resortLat},${resortLng}&travelmode=driving`;
                                document.getElementById('directions-link-<?php echo $resort['resort_id']; ?>').href = googleMapsUrl;
                            });
                        }
                    </script>
                </td>
                <td class="action-buttons">
                    <a href="../resorts/edit_resort.php?resort_id=<?php echo $resort['resort_id']; ?>">Edit</a>
                    <a href="../resorts/delete_resort.php?resort_id=<?php echo $resort['resort_id']; ?>">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No resorts found.</td> <!-- Adjusted colspan -->
            </tr>
        <?php endif; ?>
    </table>

    <div style="text-align: center;">
        <a href="../resorts/add_resort.php">Add New Resort</a>
    </div>

    <!-- Modal for slideshow -->
    <div id="imageModal">
        <div id="modalContent">
            <span id="closeModal">&times;</span>
            <img id="modalImage" />
            <span class="prev" onclick="changeSlide(-1)">&#10094;</span>
            <span class="next" onclick="changeSlide(1)">&#10095;</span>
        </div>
    </div>

    <script>
        let currentResortId;
        let currentIndex;
        const imageUrls = {};

        function openModal(resortId, index) {
            currentResortId = resortId;
            currentIndex = index;
            loadImages();
            showImage(index);
            document.getElementById('imageModal').style.display = 'flex';
        }

        function loadImages() {
            const images = [];
            for (let i = 1; i <= 4; i++) {
                const imageUrl = `../images/resort1/image${i}.png`; // Adjust path as necessary
                if (fileExists(imageUrl)) { // Check if file exists (simulate)
                    images.push(imageUrl);
                }
            }
            imageUrls[currentResortId] = images;
        }

        function fileExists(url) {
            const xhr = new XMLHttpRequest();
            xhr.open('HEAD', url, false);
            xhr.send();
            return xhr.status !== 404;
        }

        function showImage(index) {
            const images = imageUrls[currentResortId];
            if (images && images.length > 0) {
                document.getElementById('modalImage').src = images[index];
                currentIndex = index;
            }
        }

        function changeSlide(direction) {
            const images = imageUrls[currentResortId];
            currentIndex = (currentIndex + direction + images.length) % images.length;
            showImage(currentIndex);
        }

        document.getElementById('closeModal').onclick = function() {
            document.getElementById('imageModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('imageModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

</body>
</html>
