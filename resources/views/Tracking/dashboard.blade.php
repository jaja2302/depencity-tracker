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
                    <p class="stat-cards-info__num">1478 286</p>
                    <p class="stat-cards-info__title">Total Pokok Kuning</p>
                    <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit success">
                            <i data-feather="trending-up" aria-hidden="true"></i>4.07%
                        </span>
                        Last month
                    </p>
                </div>
            </article>
        </div>
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon warning">
                    <i data-feather="file" aria-hidden="true"></i>
                </div>
                <div class="stat-cards-info">
                    <p class="stat-cards-info__num">1478 286</p>
                    <p class="stat-cards-info__title">Total Sembuh</p>
                    <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit success">
                            <i data-feather="trending-up" aria-hidden="true"></i>0.24%
                        </span>
                        Last month
                    </p>
                </div>
            </article>
        </div>
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon purple">
                    <i data-feather="file" aria-hidden="true"></i>
                </div>
                <div class="stat-cards-info">
                    <p class="stat-cards-info__num">1478 286</p>
                    <p class="stat-cards-info__title">Progress</p>
                    <p class="stat-cards-info__progress">
                        <span class="stat-cards-info__profit danger">
                            <i data-feather="trending-down" aria-hidden="true"></i>1.64%
                        </span>
                        Last month
                    </p>
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
            }

            .option-select {
                display: flex;
                flex-direction: row;
                align-items: center;
                margin-top: 10px;
                margin-left: 20px;
            }

            .option-select label {
                margin-right: 20px;
            }
        }
    </style>

    <div class="row mt-10">
        <div class="col-lg-12">
            <div class="card p-4">
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
                        <button class="btn btn-primary mb-3" id="btnShow" style="padding: 10px 20px; background-color: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">Show</button>

                        <!-- <button class="btn btn-primary mb-3" id="saveButton">Save Maps</button> -->
                    </div>

                </div>

                <div class="radio-group">
                    <div class="option-select" id="regional-option" style="display: none;">
                        <!-- Additional options for Regional -->
                        <label class="main-title">Pilih Regional:</label>
                        {{csrf_field()}}
                        <select class="form-control" id="afdreg" onchange="populateEstateOptions(this.value)">


                        </select>
                    </div>
                    <div class="option-select" id="estate-option" style="display: none;">
                        <!-- Additional options for Estate -->
                        <label class="main-title">Pilih Estate:</label>
                        <select class="form-control" id="est" onchange="populateAfdelingOptions(this.value)">
                        </select>
                    </div>

                    <div class="option-select" id="afdeling-option" style="display: none;">
                        <!-- Additional options for Afdeling -->
                        <label class="main-title">Pilih Afdeling:</label>
                        <select class="form-control" id="afd"></select>
                    </div>
                    <div class="option-select" id="blok-option" style="display: none;">
                        <!-- Additional options for Blok -->
                        <label class="main-title">Pilih Blok:</label>
                        <select class="form-control" id="bloxk"></select>
                    </div>
                </div>
                <div class="radio-group">
                    <div class="button-container">

                        <button id="loadPrevPageButton" class="custom-button" style="padding: 10px 20px; background-color: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">Prev Page</button>
                        <button id="loadNextPageButton" class="custom-button" style="padding: 10px 20px; background-color: #007BFF; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">Next Page</button>
                    </div>
                </div>



            </div>

            <div class="chart">
                <div id="map" style="height: 500px; z-index: 1;"></div>
            </div>

            <!--<div class="chart">-->
            <!--    <canvas id="myChart" aria-label="Site statistics" role="img"></canvas>-->
            <!--</div>-->
        </div>
    </div>



    </body>



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

    // Call the populateSelect function with the opt_reg array
    // Call the populateSelect function with the opt_reg array
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

        // Rest of your code remains the same

        var googleStreet = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png");
        var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        googleStreet.addTo(map); // Add "Google Street" as the default base map

        var baseMaps = {
            "Google Street": googleStreet,
            "Google Satellite": googleSatellite
        };

        L.control.layers(baseMaps).addTo(map);
        map.addControl(new L.Control.Fullscreen());

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems,
                poly: {
                    allowIntersection: false
                }
            },
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true
                }
            }
        });
        map.addControl(drawControl);
        map.on('draw:created', function(e) {
            var layer = e.layer;
            drawnItems.addLayer(layer);

            // Access the polygon's coordinates
            var polygonCoordinates = layer.getLatLngs();
            console.log('Polygon Coordinates:', polygonCoordinates);

            $('#saveButton').click(function() {
                // Assuming you have the coordinates in an array named 'polygonCoordinates'
                var textData = polygonCoordinates.map(function(latLng) {
                    return 'est' + 'afd' + latLng.lat + ',' + latLng.lng;
                }).join('\n');

                // Create a Blob with the text data and save it as a TXT file
                var blob = new Blob([textData], {
                    type: 'text/plain;charset=utf-8'
                });

                // Save the Blob as a TXT file
                saveAs(blob, 'coordinates.txt');
            });
        });

        var areaMapsLayer = L.layerGroup().addTo(map); // Create a layer group for area maps
        var markersLayer = L.markerClusterGroup().addTo(map); // Create a marker cluster group for markers

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

                    var polygon = L.polygon(coordinates).addTo(areaMapsLayer);
                }
            }

            // Fit the map to the calculated bounds
            map.fitBounds(bounds);
        }

        // function drawPokok(pokok) {
        //     markersLayer.clearLayers(); // Clear markers layer only

        //     for (var i = 0; i < pokok.length; i++) {
        //         var regionData = pokok[i][1];

        //         if (Array.isArray(regionData)) {
        //             for (var j = 0; j < regionData.length; j++) {
        //                 var obj = regionData[j];
        //                 var lat = obj.lat;
        //                 var lon = obj.lon;
        //                 var afd_nama = obj.afd_nama;

        //                 var marker = L.marker([lat, lon]).addTo(markersLayer);
        //                 marker.bindPopup(afd_nama); // Optionally, you can add popups to markers
        //             }
        //         }
        //     }
        // }




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

            // // Perform the AJAX request with the requestData
            // $.ajax({
            //     url: "{{ route('drawMaps') }}",
            //     method: 'GET',
            //     data: requestData,
            //     success: function(result) {
            //         var plot = JSON.parse(result);
            //         const RegResult = Object.entries(plot['blok']);
            //         const pokok = Object.entries(plot['pokok']);

            //         drawMaps(RegResult);
            //         drawPokok(pokok);


            //         const pokok_kuning = Object.entries(plot['pokok_kuning']);

            //         console.log(pokok_kuning);
            //     },
            //     error: function(xhr, textStatus, errorThrown) {
            //         console.error('AJAX error:', errorThrown);
            //     }
            // });

            // Define a variable to keep track of the current page
            let currentPage = 1;

            // Function to fetch data for a given page
            function fetchData(page) {
                // Perform the AJAX request with the requestData
                $.ajax({
                    url: "{{ route('drawMaps') }}",
                    method: 'GET',
                    data: {
                        ...requestData,
                        page
                    }, // Include the page parameter in the request data
                    success: function(result) {
                        var plot = JSON.parse(result);
                        const RegResult = Object.entries(plot['blok']);
                        const pokok = Object.entries(plot['pokok']);
                        markersLayer.clearLayers(); // Clear markers layer only
                        drawMaps(RegResult);
                        // drawPokok(pokok);

                        const pokok_kuning = Object.entries(plot['pokok_kuning']);
                        const data = pokok_kuning[1][1];

                        // drawPokok(pokok);
                        // console.log(data);

                        // Assuming your JSON data is stored in a variable called data
                        for (var j = 0; j < data.length; j++) {
                            var obj = data[j];
                            var lat = obj.lat;
                            var lon = obj.lon;
                            var afd_nama = obj.blok;
                            var kondisi = obj.kondisi;
                            var status = obj.status;

                            // Create an HTML string for the popup content
                            var popupContent = `
                                <strong>Blok:</strong> ${afd_nama}<br>
                                <strong>Kondisi:</strong> ${kondisi}<br>
                                <strong>Status:</strong> ${status}
                            `;

                            var marker = L.marker([lat, lon]).addTo(markersLayer);

                            // Set the HTML content as the popup for the marker
                            marker.bindPopup(popupContent);
                        }
                        currentPage = page;
                        // console.log(`Page: ${page}`);
                        // console.log(pokok_kuning);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('AJAX error:', errorThrown);
                    }
                });
            }

            $('#loadNextPageButton').click(function() {
                // Increment the current page number
                currentPage++;

                // Fetch data for the next page
                fetchData(currentPage);
            });

            // Event listener for the "Load Prev Page" button
            $('#loadPrevPageButton').click(function() {
                // Check if currentPage is greater than 1 to avoid going to negative page numbers
                if (currentPage > 1) {
                    // Decrement the current page number
                    currentPage--;

                    // Fetch data for the previous page
                    fetchData(currentPage);
                }
            });
            // Initial data fetch (page 1)
            fetchData(currentPage);


            // end ajax 
        });

        // end code 
    });


    var defaultSelectedRegionalId = opt_reg[0].id;


    populateEstateOptions(defaultSelectedRegionalId);


    var defaultSelectedEstateEst = opt_est[0].est;


    populateAfdelingOptions(defaultSelectedEstateEst);
</script>