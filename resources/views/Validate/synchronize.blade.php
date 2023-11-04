@include('Layout.header')

<style>
    .btn {
        appearance: none;
        -webkit-appearance: none;
        font-family: sans-serif;
        cursor: pointer;
        padding: 10px;
        min-width: 10px;
        border: 0px;
        -webkit-transition: background-color 100ms linear;
        -ms-transition: background-color 100ms linear;
        transition: background-color 100ms linear;
    }

    .btn:focus,
    .btn.focus {
        outline: 0;
    }

    .btn-round-1 {
        border-radius: 5px;
    }

    .btn-primary {
        background: #007bff;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #1066c2;
        color: #ffffff;
    }

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
        <div class="col-lg-12">
            <button class="btn btn-primary btn-round-1" id="syncButton">Proses Data</button>
        </div>
        <div id="loadingAnimation" style="display: none;"></div>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/datatables.min.css" rel="stylesheet">


<link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/r-2.5.0/rr-1.4.1/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/r-2.5.0/rr-1.4.1/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

@include('Layout.footer')

<script>
    $(document).ready(function() {
        var select1 = $('#regOptions');
        var select2 = $('#estOptions');

        var optDefault = $('<option>', {
            value: '',
            text: 'Pilih Estate',
            selected: true,
            disabled: true
        });
        select2.append(optDefault);

        select1.change(function() {
            var selectedOption = select1.val();
            if (selectedOption) {
                $.get('/getOptValidateEst/' + selectedOption, function(data) {
                    select2.empty();
                    select2.append(optDefault);
                    $.each(data, function(key, value) {
                        select2.append($('<option></option>').attr('value', value).text(
                            value));
                    });
                });
            } else {
                select2.empty();
            }
        });
    });

    $('#syncButton').click(function() {
        const regOptions = document.getElementById('regOptions');
        const estOptions = document.getElementById('estOptions');

        if (regOptions.value === '' || estOptions.value === '') {
            Swal.fire({
                title: 'Peringatan',
                text: 'Silakan masukkan regional atau estate terlebih dahulu!',
                icon: 'warning',
            });
        } else {
            Swal.fire({
                title: 'Sinkronisasi Data',
                text: 'Yakin ingin sinkronisasi data pokok kuning?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mohon tunggu..',
                        text: 'Proses sinkronisasi sedang berjalan',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    var csrfToken = $('input[name="_token"]').val();
                    $.ajax({
                        type: 'GET',
                        url: '/processSynchronize',
                        data: {
                            regVal: regOptions.value,
                            estVal: estOptions.value
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            Swal.close()
                            Swal.fire({
                                title: 'Success',
                                text: response
                                    .message,
                                allowOutsideClick: false,
                                icon: 'success',
                            }).then((result) => {
                                if (result
                                    .isConfirmed
                                ) {
                                    const
                                        routeUrl =
                                        "{{ route('sinkronMaps') }}";
                                    window
                                        .location
                                        .href =
                                        routeUrl
                                }
                            });

                            response.data.forEach(function(message) {
                                console.log(message)
                            });
                        },
                        error: function(error) {
                            Swal.close()
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal sinkronisasi data!',
                                icon: 'error',
                            });
                        }
                    });
                }
            });
        }
    });
</script>