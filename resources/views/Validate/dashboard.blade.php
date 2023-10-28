@include('Layout.header')

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

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .label-blok {
        font-size: 15pt;
        color: white;
    }
</style>

<div class="container">
    <div class="row stat-cards-item">
        @if(session('jabatan') == 'Admin')
            <div class="col-lg-12" style="margin-bottom: 0.5%;">
                <label class="main-title" style="font-size: 18px; margin-right: 20px;">Pilih Regional</label>
                <label class="main-title" style="font-size: 18px;">:</label>
                <select class="form-control" id="regOptions">
                    <option value="" selected disabled>Pilih Regional</option>
                    @foreach ($regArrSelected as $option)
                    <option value="{{ $option['id'] }}">{{ $option['nama'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12" style="margin-bottom: 1%;">
                <label class="main-title" style="font-size: 18px; margin-right: 40px;">Pilih Estate</label>
                <label class="main-title" style="font-size: 18px;">:</label>
                <select class="form-control" id="estOptions"></select>
            </div>
            <div class="col-lg-12" style="margin-bottom: 1%;">
                <label class="main-title" style="font-size: 18px; margin-right: 22px;">Pilih Afdeling</label>
                <label class="main-title" style="font-size: 18px;">:</label>
                <select class="form-control" id="afdOptions"></select>
            </div>
            <div class="col-lg-12" style="margin-bottom: 1%;">
                <button class="btn btn-primary mb-3 ml-3" id="showMaps">Show</button>
            </div>
            <div class="col-lg-12">
                <div id="map" style="height: 700px; z-index: 1;"></div>
            </div>
        @else 
            <div class="col-lg-12" style="margin-bottom: 0.5%;">
                <h3>Tidak dapat mengakses halaman ini!</h3>
            </div>
        @endif
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

    .custom-popup {
        text-align: center;
        /* Center-align the content */
    }

    .popup-image {
        max-width: 100%;
        /* Ensure images fit within the popup */
        height: auto;
        /* Maintain aspect ratio */
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
<script type="text/javascript" src="{{ asset('js/Leaflet.Editable.js') }}"></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
    rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Leaflet.draw CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

<!-- Leaflet.draw JavaScript -->
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.css" />
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.Default.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link
    href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/datatables.min.css"
    rel="stylesheet">


<link
    href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/r-2.5.0/rr-1.4.1/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.css"
    rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script
    src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/r-2.5.0/rr-1.4.1/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

@include('Layout.footer')

<script>
    var geoJSONArray = [];
    var markersPk = [];
    var map = null;
    var estateLayerGroup = L.layerGroup();
    var markerGroup = L.layerGroup();
    var titleBlok = new Array();

    $(document).ready(function () {
        map = L.map('map', {
            editable: true
        }).setView([-2.2745234, 111.61404248], 11);

        var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        googleSatellite.addTo(map);
        map.addControl(new L.Control.Fullscreen());

        var select1 = $('#regOptions');
        var select2 = $('#estOptions');
        var select3 = $('#afdOptions');

        var optDefault = $('<option>', {
            value: '',
            text: 'Pilih Estate',
            selected: true,
            disabled: true
        });
        select2.append(optDefault);

        var optDefault1 = $('<option>', {
            value: '',
            text: 'Pilih Afdeling',
            selected: true,
            disabled: true
        });
        select3.append(optDefault1);

        select1.change(function () {
            var selectedOption = select1.val();
            if (selectedOption) {
                $.get('/getOptValidateEst/' + selectedOption, function (data) {
                    select2.empty();
                    select2.append(optDefault);
                    $.each(data, function (key, value) {
                        select2.append($('<option></option>').attr('value', value).text(
                            value));
                    });
                });
            } else {
                select2.empty();
            }
        });

        select2.change(function () {
            var selectedOptAfd = select2.val();
            if (selectedOptAfd) {
                $.get('/getOptValidateAfd/' + selectedOptAfd, function (data) {
                    select3.empty();
                    select3.append(optDefault1);
                    $.each(data, function (key, value) {
                        select3.append($('<option></option>').attr('value', value).text(
                            value));
                    });
                });
            } else {
                select3.empty();
            }
        });

        select3.change(function () {
            geoJSONArray = []
            markersPk = []
            var selectedEst = select2.val();
            var selectedAfd = select3.val();

            $.get('/getCoordinatesValidate/' + selectedEst, function (data) {
                var data1 = data.data1;
                var data2 = data.data2;

                for (let category in data1) {
                    for (let key in data1[category]) {
                        if (data1[category][key].hasOwnProperty('latln')) {
                            if (category == selectedAfd) {
                                var coordinatesString = data1[category][key]['latln']
                                var coordinatePairs = coordinatesString.split("$");
                                var coordinatesArray = [];
                                for (var i = 0; i < coordinatePairs.length; i++) {
                                    var pair = coordinatePairs[i].trim();
                                    var coordinateArray = JSON.parse(pair);
                                    coordinatesArray.push(coordinateArray);
                                }
                                var formattedArray = [coordinatesArray];

                                var geoJSONString = JSON.stringify({
                                    "type": "Feature",
                                    "properties": {
                                        "afdeling": category,
                                        "blok": key
                                    },
                                    "geometry": {
                                        "type": "Polygon",
                                        "coordinates": formattedArray
                                    }
                                });

                                var geoJSONObject = JSON.parse(geoJSONString);
                                geoJSONArray.push(geoJSONObject);
                            }
                        }
                    }
                }

                for (var id in data2) {
                    if (data2.hasOwnProperty(id)) {
                        var item = data2[id];
                        if (item.afd == selectedAfd) {
                            var latlng = item.latln.split(',').map(parseFloat);
                            markersPk.push({
                                id: parseInt(id),
                                blok: item.blok,
                                latlng: latlng
                            });
                        }
                    }
                }
            });
        });
    });

    $('#showMaps').click(function () {
        const regOptions = document.getElementById('regOptions');
        const estOptions = document.getElementById('estOptions');
        const afdOptions = document.getElementById('afdOptions');

        if (regOptions.value === '' || estOptions.value === '' || afdOptions.value === '') {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan masukkan regional/estate/afdeling',
                icon: 'warning',
            });
        } else {
            estateLayerGroup.clearLayers();
            markerGroup.clearLayers();

            for (i = 0; i < titleBlok.length; i++) {
                map.removeLayer(titleBlok[i]);
            }

            if (map === null) {
                map = initializeMap();
            } else {
                map.invalidateSize();
            }

            var estateObj = L.geoJSON(geoJSONArray, {
                style: function (feature) {
                    switch (feature.properties.afdeling) {
                        case 'OA':
                            return {
                                color: "#f39c12"
                            };
                        case 'OB':
                            return {
                                color: "#f9e79f"
                            };
                        case 'OC':
                            return {
                                color: "#abebc6"
                            };
                        case 'OD':
                            return {
                                color: "#d98880"
                            };
                        case 'OE':
                            return {
                                color: "#a9cce3"
                            };
                        case 'OF':
                            return {
                                color: "#d2b4de"
                            };
                    }
                },
                onEachFeature: function (feature, layer) {
                    var label = L.marker(layer.getBounds().getCenter(), {
                        icon: L.divIcon({
                            className: 'label-blok',
                            html: feature.properties.blok,
                            iconSize: [100, 20]
                        })
                    }).addTo(map);
                    titleBlok.push(label)
                }
            }).addTo(map);

            estateLayerGroup.addLayer(estateObj);
            estateLayerGroup.addTo(map);

            map.fitBounds(estateObj.getBounds());

            markersPk.forEach(function (marker) {
                L.marker(marker.latlng)
                    .addTo(markerGroup)
                    .bindPopup('ID: ' + marker.id + '<br>Blok: ' + marker.blok);
            });
            markerGroup.addTo(map)

            estateObj.on('click', function (e) {
                var clickedPolygon = e.layer;

                var category = clickedPolygon.feature.properties.afdeling;
                var key = clickedPolygon.feature.properties.blok;

                var markersInsidePolygon = markersPk.filter(function (marker) {
                    return clickedPolygon.getBounds().contains(L.latLng(marker.latlng));
                });

                var markerIdsInsidePolygon = markersInsidePolygon.map(function (marker) {
                    return marker.id;
                });

                console.log("Polygon Estate:", $('#estOptions').val());
                console.log("Polygon Afdeling:", category);
                console.log("Polygon Blok:", key);
                console.log("Markers inside the polygon:", markerIdsInsidePolygon);

                if (markerIdsInsidePolygon.length === 0) {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Tidak ada data pokok kuning!',
                        icon: 'warning',
                    });
                } else {
                    var csrfToken = $('input[name="_token"]').val();
                    Swal.fire({
                        title: '<h3>Validasi Data</h3>',
                        html: '<div class="swal2-input-container">' +
                            '<div class="swal2-input-col">' +
                            '<p>Total pokok di ' + key + ' : ' + markerIdsInsidePolygon.length +
                            '</p>' +
                            '</div><div class="swal2-input-col">' +
                            '<input id="inputAfdeling" class="swal2-input" placeholder="Masukkan nama afdeling" value="' +
                            category + '">' +
                            '</div>' +
                            '<div class="swal2-input-col">' +
                            '<input id="inputBlok" class="swal2-input" placeholder="Masukkan nama blok" value="' +
                            key + '">' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            const valAfd = document.getElementById('inputAfdeling').value;
                            const valBlok = document.getElementById('inputBlok').value;

                            Swal.fire({
                                title: 'Validasi Data',
                                text: 'Yakin ingin memperbarui data?',
                                showCancelButton: true,
                                confirmButtonText: 'Confirm',
                                cancelButtonText: 'Cancel',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: 'POST',
                                        url: '/processValidate',
                                        data: {
                                            rilAfd: category,
                                            rilBlok: key,
                                            inpAfd: valAfd,
                                            inpBlok: valBlok,
                                            markerIds: markerIdsInsidePolygon
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        success: function (response) {
                                            Swal.fire({
                                                title: 'Success',
                                                text: response
                                                    .message,
                                                icon: 'success',
                                            }).then((result) => {
                                                if (result
                                                    .isConfirmed
                                                    ) {
                                                    const
                                                        routeUrl =
                                                        "{{ route('mainMaps') }}";
                                                    window
                                                        .location
                                                        .href =
                                                        routeUrl
                                                }
                                            });
                                        },
                                        error: function (error) {
                                            Swal.fire({
                                                title: 'Error',
                                                text: 'Gagal memperbarui data!',
                                                icon: 'error',
                                            });
                                        }
                                    });
                                }
                            });
                        },
                    });
                }
            });
        }
    });
</script>