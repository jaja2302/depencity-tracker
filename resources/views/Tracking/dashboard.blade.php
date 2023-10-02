@include('Layout.header')




<div class="container">

    <h2>Select an Option:</h2>

    <br>

    <div class="row stat-cards">
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon primary">
                    <i data-feather="bar-chart-2" aria-hidden="true"></i>
                </div>
                <div class="stat-cards-info">
                    <p class="stat-cards-info__num" id="total_pk"></p>
                    <p class="stat-cards-info__title">Total Pokok Kuning</p>
                    <!-- <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit success">
                            <i data-feather="trending-up" aria-hidden="true"></i>4.07%
                        </span>
                        Last month
                    </p> -->
                </div>
            </article>
        </div>
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon warning">
                    <i data-feather="file" aria-hidden="true"></i>
                </div>
                <div class="stat-cards-info">
                    <p class="stat-cards-info__num" id="sembuh_pk"></p>
                    <p class="stat-cards-info__title">Total Sembuh</p>
                    <!-- <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit success">
                            <i data-feather="trending-up" aria-hidden="true"></i>0.24%
                        </span>
                        Last month
                    </p> -->
                </div>
            </article>
        </div>
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon purple">
                    <i data-feather="file" aria-hidden="true"></i>
                </div>
                <div class="stat-cards-info">
                    <p class="stat-cards-info__num" id="persen_pk"></p>
                    <p class="stat-cards-info__title">Progress</p>
                    <!-- <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit danger">
                            <i data-feather="trending-down" aria-hidden="true"></i>1.64%
                        </span>
                        Last month
                    </p> -->
                </div>
            </article>
        </div>

    </div>
    <br>
    <!-- Mobile version -->
    <style>
        @media (max-width: 768px) {

            /* Adjust the max-width as needed */
            .radio-group {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .option-select {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                margin-top: 10px;
                margin-left: 0;
            }

            .option-select label {
                margin-right: 0;
            }
        }

        .loading-text {
            animation: fadeInOut 2s ease-in-out infinite;
        }
    </style>

    <!-- Windows version (using your existing code) -->
    <style>
        @media (min-width: 769px) {

            /* Adjust the min-width as needed */
            .radio-group {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                /* Align to the left and right */
            }

            .radio-group .radio-options {
                display: flex;
                flex-direction: row;
                align-items: center;
                margin-right: 20px;
                /* Adjust the margin as needed */
            }

            .radio-group label {
                margin: 0;
                /* Remove margin from labels */
            }
        }
    </style>

    <div class="row mt-10">
        <div class="col-lg-12">
            <div class="radio-group">
                <!-- Radio button for "Regional" -->
                <div style="display: inline-block; margin-right: 10px;">
                    <input type="radio" id="regional" name="option" value="Regional">
                    <label class="main-title" for="regional">Regional</label>
                </div>
                <!-- Radio button for "Estate" -->
                <div style="display: inline-block; margin-right: 10px;">
                    <input type="radio" id="estate" name="option" value="Estate">
                    <label class="main-title" for="estate">Estate</label>
                </div>
                <!-- Radio button for "Afdeling" -->
                <div style="display: inline-block; margin-right: 10px;">
                    <input type="radio" id="afdeling" name="option" value="Afdeling">
                    <label class="main-title" for="afdeling">Afdeling</label>
                </div>
                <!-- Radio button for "Blok" -->
                <div style="display: inline-block;">
                    <input type="radio" id="blok" name="option" value="Blok">
                    <label class="main-title" for="blok">Blok</label>
                </div>
                <!-- "Show" button container -->
                <div style="text-align: right;">
                    <button id="btnShow" style="background-color: #007bff; color: white; border: none; padding: 10px 10px; border-radius: 5px;">Show</button>
                </div>

            </div>
            <div class="radio-group">
                <div class="option-select" id="regional-option" style="display: none;">
                    <!-- Additional options for Regional -->
                    <label class="main-title" style="font-size: 18px;">Pilih Regional:</label>
                    {{csrf_field()}}
                    <select class="form-control" id="afdreg" onchange="populateEstateOptions(this.value)">


                    </select>
                </div>
                <div class="option-select" id="estate-option" style="display: none;">
                    <!-- Additional options for Estate -->
                    <label class="main-title" style="font-size: 18px;">Pilih Estate:</label>
                    <select class="form-control" id="est" onchange="populateAfdelingOptions(this.value)">
                    </select>
                </div>

                <div class="option-select" id="afdeling-option" style="display: none;">
                    <!-- Additional options for Afdeling -->
                    <label class="main-title" style="font-size: 18px;">Pilih Afdeling:</label>
                    <select class="form-control" id="afd"></select>
                </div>
                <div class="option-select" id="blok-option" style="display: none;">
                    <!-- Additional options for Blok -->
                    <label class="main-title" style="font-size: 18px;">Pilih Blok:</label>
                    <select class="form-control" id="bloxk"></select>
                </div>
            </div>
        </div>
        <div class="col-lg-12">

            <div id="map" style="height: 540px; z-index: 1;"></div>

        </div>
    </div>



    </body>


    <style>
        /* CSS for legend styling */
        .info.legend {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .info.legend label {
            display: block;
            margin-bottom: 5px;
        }

        .label-blok {
            background-color: transparent;
            /* Set the background color to transparent */
            color: white;
            /* Set the text color to white */
            border: none;
            /* Remove any border */
            font-size: 12px;
            /* Adjust the font size as needed */
            text-align: center;
            width: auto;
            padding: 0;
            /* Adjust padding as needed */
        }
    </style>



</div>

@include('Layout.footer')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
<script type="text/javascript" src="{{ asset('js/Leaflet.Editable.js') }}"></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Leaflet.draw CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

<!-- Leaflet.draw JavaScript -->
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.Default.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js"></script>


<script>
    function openModal(src, komentar) {
        Swal.fire({
            imageUrl: src,
            imageAlt: 'Image',
            imageHeight: '80%',
            imageWidth: '100%',
            width: '50%',
            html: `<p>Komentar:</p><p>${komentar}</p>`
        });
    }


    var opt_reg = <?php echo json_encode($option_reg); ?>;
    var opt_est = <?php echo json_encode($option_est); ?>;
    var opt_afd = <?php echo json_encode($option_afd); ?>;


    var afdregSelect = document.getElementById('afdreg');

    // Function to populate the select element with options
    function populateSelect(options) {
        // Clear existing options
        afdregSelect.innerHTML = '';

        // Create and add new options
        options.forEach(function(option) {
            var optionElement = document.createElement('option');
            optionElement.value = option.id;
            optionElement.textContent = option.nama;
            afdregSelect.appendChild(optionElement);
        });
    }

    populateSelect(opt_reg);

    // Get the default selected Regional option (for example, the first option)
    var defaultSelectedRegionalId = opt_reg[0].id;

    // Populate the Estate options based on the default selected Regional option
    populateEstateOptions(defaultSelectedRegionalId);


    function populateEstateOptions(selectedRegionalId) {
        // Get a reference to the Estate select element
        var estateSelect = document.getElementById('est');

        // Clear existing options
        estateSelect.innerHTML = '';

        // Filter the opt_est array based on the selectedRegionalId
        var filteredEstates = opt_est.filter(function(estate) {
            return estate.regional == selectedRegionalId; // Use '==' for loose equality
        });

        // Create and add new options based on the filtered results
        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate.est;
            optionElement.textContent = estate.nama;
            estateSelect.appendChild(optionElement);
        });
    }

    function populateAfdelingOptions(selectedEstateEst) {
        // Get a reference to the Afdeling select element
        var afdSelect = document.getElementById('afd');

        // Clear existing options
        afdSelect.innerHTML = '';
        // console.log(selectedEstateEst);
        // Filter the opt_afd array based on the selectedEstateEst
        var filteredAfdelings = opt_afd.filter(function(afd) {
            return afd.est === selectedEstateEst; // Use '===' for strict equality


        });

        // Create and add new options based on the filtered results
        filteredAfdelings.forEach(function(afd) {
            var optionElement = document.createElement('option');
            optionElement.value = afd.id;
            optionElement.textContent = afd.nama;
            afdSelect.appendChild(optionElement);
        });
    }



    $(document).ready(function() {



        var group = L.layerGroup();

        // Initialize the map and set its view
        var map = L.map('map', {
            editable: true // Enable editing
        }).setView([-2.2745234, 111.61404248], 11);

        // Define the "Google Satellite" tile layer
        var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Add "Google Satellite" as the only base map
        googleSatellite.addTo(map);

        map.addControl(new L.Control.Fullscreen());

        var areaMapsLayer = L.layerGroup().addTo(map); // Create a layer group for area maps
        var markersLayer = L.markerClusterGroup().addTo(map);
        var markerBlok = L.markerClusterGroup().addTo(map);

        map.addLayer(areaMapsLayer);
        map.removeLayer(markerBlok); // Hide Draw Blok initially

        // Define a unique ID for the legend container
        var legendId = 'legend-container2';

        // Remove the previous legend if it exists
        var existingLegend = document.getElementById(legendId);
        if (existingLegend) {
            existingLegend.remove();
        }

        // Define the legend control
        var legend = L.control({
            position: 'bottomleft'
        });

        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.id = legendId; // Set the unique ID
            div.innerHTML = '<strong>Legend</strong><br>';
            div.innerHTML += '<label for="drawMapsCheckbox"><input type="radio" name="mapToggle" id="drawMapsCheckbox" checked> Draw Maps</label><br>';
            div.innerHTML += '<label for="drawblokCheckbox"><input type="radio" name="mapToggle" id="drawblokCheckbox"> Draw Blok</label><br>';
            return div;
        };

        legend.addTo(map);

        // Add event listeners to the radio buttons
        document.getElementById('drawMapsCheckbox').addEventListener('change', function() {
            if (this.checked) {
                map.addLayer(areaMapsLayer);
                map.removeLayer(markerBlok);
            }
        });

        document.getElementById('drawblokCheckbox').addEventListener('change', function() {
            if (this.checked) {
                map.addLayer(markerBlok);
                // map.removeLayer(areaMapsLayer);
            }
        });



        function drawMaps(regions) {
            areaMapsLayer.clearLayers(); // Clear area maps layer only

            var bounds = new L.LatLngBounds(); // Create a bounds object to store the coordinates

            for (var i = 0; i < regions.length; i++) {
                var regionData = regions[i][1];

                // Check if regionData is an array
                if (Array.isArray(regionData)) {
                    // Initialize an array to store coordinates for the polyline
                    var coordinates = [];

                    // Loop through the array of objects within regionData
                    for (var j = 0; j < regionData.length; j++) {
                        var obj = regionData[j];
                        var lat = obj.lat;
                        var lon = obj.lon;
                        var afd_nama = obj.afd_nama;

                        // Create a LatLng object for each coordinate
                        var latLng = new L.LatLng(lat, lon);

                        // Extend the bounds with the new LatLng object
                        bounds.extend(latLng);
                        coordinates.push(latLng);
                    }

                    // var polygon = L.polygon(coordinates).addTo(areaMapsLayer);

                    var polygon = L.polygon(coordinates, {
                        fillOpacity: 0.2, // Set the fill opacity to a low value
                        opacity: 0.5 // Set the border opacity to a low value
                    }).addTo(areaMapsLayer);
                }
            }

            // Fit the map to the calculated bounds
            map.fitBounds(bounds);
        }

        function drawPokok(pokok) {
            markersLayer.clearLayers(); // Clear markers layer only

            var beratIcon = L.icon({
                iconUrl: '{{ asset("img/berat.png") }}',
                iconSize: [32, 32], // Set the size of the icon
            });

            var ringanIcon = L.icon({
                iconUrl: '{{ asset("img/ringan.png") }}',
                iconSize: [32, 32],
            });

            var sedangIcon = L.icon({
                iconUrl: '{{ asset("img/pucat.png") }}',
                iconSize: [32, 32],
            });
            var defaultIcon = L.icon({
                iconUrl: '{{ asset("img/palm-oil-free.png") }}',
                iconSize: [32, 32],
            });

            var iconSembuh = L.icon({
                iconUrl: '{{ asset("img/sembuhnih.png") }}',
                iconSize: [32, 32],
            });

            var legendId = 'legend-container'; // Unique ID for the legend container

            // Remove the previous legend if it exists
            var existingLegend = document.getElementById(legendId);
            if (existingLegend) {
                existingLegend.remove();
            }

            var legendContainer = L.control({
                position: 'topright'
            });

            legendContainer.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'info legend');
                div.id = legendId; // Set the unique ID
                div.innerHTML += '<strong>Legend</strong><br>';
                div.innerHTML += '<strong>Status:</strong><br>';
                div.innerHTML += '<label><input type="radio" name="statusFilter" value="all" checked> All</label><br>';
                div.innerHTML += '<label><input type="radio" name="statusFilter" value="Sudah"> Sudah</label><br>';
                div.innerHTML += '<strong>Kondisi:</strong><br>';
                div.innerHTML += '<label><input type="radio" name="kondisiFilter" value="all" checked> All</label><br>';
                div.innerHTML += '<label><input type="radio" name="kondisiFilter" value="Berat"> Berat</label><br>';
                div.innerHTML += '<label><input type="radio" name="kondisiFilter" value="Ringan"> Ringan</label><br>';
                div.innerHTML += '<label><input type="radio" name="kondisiFilter" value="Pucat"> Pucat</label><br>';
                return div;
            };

            legendContainer.addTo(map);

            var statusFilter = "all"; // Default status filter
            var kondisiFilter = "all"; // Default kondisi filter

            // Add event listeners for status radio buttons
            var statusFilterRadios = document.getElementsByName('statusFilter');
            for (var i = 0; i < statusFilterRadios.length; i++) {
                statusFilterRadios[i].addEventListener('change', function() {
                    statusFilter = this.value;
                    if (statusFilter !== "all") {
                        // If Status filter is not "All", select "All" in Kondisi filter
                        kondisiFilter = "all";
                        // Update Kondisi filter radio buttons
                        var kondisiFilterRadios = document.getElementsByName('kondisiFilter');
                        for (var j = 0; j < kondisiFilterRadios.length; j++) {
                            kondisiFilterRadios[j].checked = kondisiFilterRadios[j].value === kondisiFilter;
                        }
                    }
                    updateMarkers();
                });
            }

            // Add event listeners for kondisi radio buttons
            var kondisiFilterRadios = document.getElementsByName('kondisiFilter');
            for (var i = 0; i < kondisiFilterRadios.length; i++) {
                kondisiFilterRadios[i].addEventListener('change', function() {
                    kondisiFilter = this.value;
                    if (kondisiFilter !== "all") {
                        // If Kondisi filter is not "All", select "All" in Status filter
                        statusFilter = "all";
                        // Update Status filter radio buttons
                        var statusFilterRadios = document.getElementsByName('statusFilter');
                        for (var j = 0; j < statusFilterRadios.length; j++) {
                            statusFilterRadios[j].checked = statusFilterRadios[j].value === statusFilter;
                        }
                    }
                    updateMarkers();
                });
            }

            function updateMarkers() {
                markersLayer.clearLayers(); // Clear markers layer

                for (var i = 0; i < pokok.length; i++) {
                    var regionData = pokok[i][1];

                    if (Array.isArray(regionData)) {
                        for (var j = 0; j < regionData.length; j++) {
                            var obj = regionData[j];
                            var lat = obj.lat;
                            var lon = obj.lon;
                            var afd_nama = obj.blok;
                            var kondisi = obj.kondisi;
                            var status = obj.status;
                            var foto = obj.foto;
                            var komentar = obj.komentar;
                            var id = obj.id;

                            if ((statusFilter === "all" || statusFilter === status) && (kondisiFilter === "all" || kondisiFilter === kondisi)) {
                                var icon;
                                if (status === 'Sudah') {
                                    icon = iconSembuh;
                                } else {
                                    if (kondisi === 'Berat') {
                                        icon = beratIcon;
                                    } else if (kondisi === 'Ringan') {
                                        icon = ringanIcon;
                                    } else if (kondisi === 'Pucat') {
                                        icon = sedangIcon;
                                    } else {
                                        // Default icon if kondisi doesn't match any category
                                        icon = defaultIcon;
                                    }
                                }

                                var popupContent = `<strong>Dtracking Blok: </strong>${afd_nama}<br/>`;
                                popupContent += `<strong>Kondisi: </strong>${kondisi}<br/>`;
                                popupContent += `<strong>Status: </strong>${status}<br/>`;
                                popupContent += `<strong>ID: </strong>${id}<br/>`;
                                if (foto) {
                                    popupContent += `<img src="https://mobilepro.srs-ssms.com/storage/app/public/deficiency_tracker/${foto}" alt="Foto Temuan" style="max-width:200px; height:auto;" onclick="openModal(this.src, '${komentar}')"><br/>`;
                                }

                                var marker = L.marker([lat, lon], {
                                    icon: icon
                                }).addTo(markersLayer);
                                marker.bindPopup(popupContent);
                            }
                        }
                    }
                }
            }

            updateMarkers(); // Initially display all markers
        }

        function drawblok(drawBlok) {
            markerBlok.clearLayers(); // Clear area maps layer only

            var bounds = new L.LatLngBounds(); // Create a bounds object to store the coordinates

            for (var i = 0; i < drawBlok.length; i++) {
                var regionData = drawBlok[i][1];

                // Check if regionData is an array
                if (Array.isArray(regionData)) {
                    // Initialize an array to store coordinates for the polyline
                    var coordinates = [];

                    // Loop through the array of objects within regionData
                    for (var j = 0; j < regionData.length; j++) {
                        var obj = regionData[j];
                        var lat = obj.lat;
                        var lon = obj.lon;
                        var afd_nama = obj.nama;

                        // Create a LatLng object for each coordinate
                        var latLng = new L.LatLng(lat, lon);

                        // Extend the bounds with the new LatLng object
                        bounds.extend(latLng);
                        coordinates.push(latLng);
                    }

                    var polygon = L.polygon(coordinates, {
                        fillColor: 'red', // Set the fill color to red
                        color: 'red', // Set the border color to red
                        fillOpacity: 0.01, // Set the fill opacity to a low value
                        opacity: 0.01 // Set the border opacity to a low value
                    }).addTo(markerBlok);
                    // Calculate the center of the polygon
                    var polygonCenter = polygon.getBounds().getCenter();

                    // Create a label marker at the center with afd_nama text
                    var label = L.marker(polygonCenter, {
                        icon: L.divIcon({
                            className: 'label-blok',
                            html: afd_nama,
                            iconSize: [50, 10],
                        })
                    }).addTo(markerBlok);
                }
            }

            // Fit the map to the calculated bounds
            map.fitBounds(bounds);
        }

        // end maps 

        function showAdditionalOptions(option) {
            var regionalOption = document.getElementById('regional-option');
            var estateOption = document.getElementById('estate-option');
            var afdelingOption = document.getElementById('afdeling-option');
            var blokOption = document.getElementById('blok-option');

            // Hide all filter options initially
            regionalOption.style.display = 'none';
            estateOption.style.display = 'none';
            afdelingOption.style.display = 'none';
            blokOption.style.display = 'none';

            // Show the selected filter option(s)
            if (option === 'Regional') {
                regionalOption.style.display = 'block';
            } else if (option === 'Estate') {
                regionalOption.style.display = 'block';
                estateOption.style.display = 'block';
            } else if (option === 'Afdeling') {
                regionalOption.style.display = 'block';
                estateOption.style.display = 'block';
                afdelingOption.style.display = 'block';
            } else if (option === 'Blok') {
                regionalOption.style.display = 'block';
                estateOption.style.display = 'block';
                afdelingOption.style.display = 'block';
                blokOption.style.display = 'block';
            } else {
                // Handle any other cases or defaults here
            }
        }
        // Listen for changes in the radio buttons
        $('input[name="option"]').on('change', function() {
            var selectedOption = $(this).val();

            // Call the function to show additional options
            showAdditionalOptions(selectedOption);

            // If "Blok" is selected, populate the Blok options initially
            if (selectedOption === 'Blok') {
                var selectedAfdId = $('#afd').val();

                console.log(selectedAfdId);
                populateBlokOptions(selectedAfdId);
            }
        });
        $('#afd').on('change', function() {
            // Get the selected Afdeling value
            var selectedAfdId = $(this).val();

            // Call the function to populate Blok options when "Blok" is selected
            var selectedOption = $('input[name="option"]:checked').val();
            if (selectedOption === 'Blok') {
                populateBlokOptions(selectedAfdId);
            }
        });

        function populateBlokOptions(selectedAfdId) {
            var blokSelect = $('#bloxk'); // Select the element by ID


            // Send an AJAX request to fetch data based on the selected Afdeling
            $.ajax({
                url: "{{ route('getBlok') }}",
                method: 'GET',
                data: {
                    afd: selectedAfdId
                },
                success: function(result) {
                    var parseResult = JSON.parse(result);
                    var blokArray = parseResult.blok; // Access the "blok" array within the object

                    // Clear any existing options
                    blokSelect.empty();

                    // Iterate over the "blokArray" array and add options to the select element
                    blokArray.forEach(function(blokData) {
                        blokSelect.append($('<option>', {
                            value: blokData.nama,
                            text: blokData.nama
                        }));
                    });

                    // Show the "blok" select element
                    $('#blok-option').show();
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error('AJAX error:', errorThrown);
                }
            });
        }

        $('#btnShow').on('click', function() {
            var selectedOption = $('input[name="option"]:checked').val();
            var requestData = {};
            var dataType = '';

            // Add the selected option and its corresponding value to the requestData object
            if (selectedOption === 'Regional') {
                requestData['regional'] = $('#afdreg').val();
                dataType = 'regional';
            } else if (selectedOption === 'Estate') {
                requestData['regional'] = $('#afdreg').val();
                requestData['estate'] = $('#est').val();
                dataType = 'estate';
            } else if (selectedOption === 'Afdeling') {
                requestData['regional'] = $('#afdreg').val();
                requestData['estate'] = $('#est').val();
                requestData['afdeling'] = $('#afd').val();
                dataType = 'afdeling';
            } else if (selectedOption === 'Blok') {
                requestData['regional'] = $('#afdreg').val();
                requestData['estate'] = $('#est').val();
                requestData['afdeling'] = $('#afd').val();
                requestData['blok'] = $('#bloxk').val();
                dataType = 'blok';
            }

            // Add the dataType to the requestData object
            requestData['dataType'] = dataType;
            Swal.fire({
                title: 'Loading',
                html: '<span class="loading-text">Mohon Tunggu...</span>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }

            });
            // Perform the AJAX request with the requestData
            $.ajax({
                url: "{{ route('drawMaps') }}",
                method: 'GET',
                data: requestData,
                success: function(result) {
                    Swal.close();
                    var plot = JSON.parse(result);
                    const RegResult = Object.entries(plot['blok']);
                    const pokok = Object.entries(plot['pokok']);
                    const drawBlok = Object.entries(plot['drawBlok']);
                    // console.log(result);
                    var totalx = plot['total_pokok'];
                    var total_ditangani = plot['total_ditangani'];
                    var persen_ditangani = plot['persen_ditangani'];
                    // console.log(totalx);

                    const totalPkElement = document.getElementById("total_pk");

                    if (totalPkElement) {
                        totalPkElement.textContent = totalx;
                    } else {
                        console.error("Element with id 'total_pk' not found.");
                    }
                    const total_ditanganix = document.getElementById("sembuh_pk");

                    if (total_ditanganix) {
                        total_ditanganix.textContent = total_ditangani;
                    } else {
                        console.error("Element with id 'total_pk' not found.");
                    }
                    const persen_ditanganix = document.getElementById("persen_pk");

                    if (persen_ditanganix) {
                        persen_ditanganix.textContent = persen_ditangani + '%';
                    } else {
                        console.error("Element with id 'total_pk' not found.");
                    }


                    drawMaps(RegResult);
                    drawPokok(pokok);
                    drawblok(drawBlok);



                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error('AJAX error:', errorThrown);
                }
            });

            // end ajax 
        });

        // end code 
    });

    function drawMaps() {

    }
    var defaultSelectedRegionalId = opt_reg[0].id;


    populateEstateOptions(defaultSelectedRegionalId);


    var defaultSelectedEstateEst = opt_est[0].est;


    populateAfdelingOptions(defaultSelectedEstateEst);

    // edit tool untuk gambar map 
    // var drawnItems = new L.FeatureGroup();
    // map.addLayer(drawnItems);

    // var drawControl = new L.Control.Draw({
    //     edit: {
    //         featureGroup: drawnItems,
    //         poly: {
    //             allowIntersection: false
    //         }
    //     },
    //     draw: {
    //         polygon: {
    //             allowIntersection: false,
    //             showArea: true
    //         }
    //     }
    // });
    // map.addControl(drawControl);
    // map.on('draw:created', function(e) {
    //     var layer = e.layer;
    //     drawnItems.addLayer(layer);

    //     // Access the polygon's coordinates
    //     var polygonCoordinates = layer.getLatLngs();
    //     console.log('Polygon Coordinates:', polygonCoordinates);

    //     $('#saveButton').click(function() {
    //         // Assuming you have the coordinates in an array named 'polygonCoordinates'
    //         var textData = polygonCoordinates.map(function(latLng) {
    //             return 'est' + 'afd' + latLng.lat + ',' + latLng.lng;
    //         }).join('\n');

    //         // Create a Blob with the text data and save it as a TXT file
    //         var blob = new Blob([textData], {
    //             type: 'text/plain;charset=utf-8'
    //         });

    //         // Save the Blob as a TXT file
    //         saveAs(blob, 'coordinates.txt');
    //     });
    // });

    // end edit tool gambar map 
</script>