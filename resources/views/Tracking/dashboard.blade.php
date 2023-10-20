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
</style>


<div class="container">

    <h2>Select an Option:</h2>

    <br>

    <div class="row stat-cards">
        <div class="col-md-6 col-xl-4">
            <article class="stat-cards-item">
                <div class="stat-cards-icon primary">
                    <img src="{{ asset('img/pokok_kuning.png') }}" alt="Custom Icon">
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
                <div class="stat-cards-icon primary">
                    <img src="{{ asset('img/pokok_sembuh.png') }}" alt="Custom Icon">
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
                <div class="stat-cards-icon primary">
                    <img src="{{ asset('img/percens.png') }}" alt="Custom Icon">
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
                    <select class="form-control" id="afd" onchange="populatbloks(this.value)"></select>
                </div>
                <div class="option-select" id="blok-option" style="display: none;">
                    <!-- Additional options for Blok -->
                    <label class="main-title" style="font-size: 18px;">Pilih Blok:</label>
                    <select class="form-control" id="blokxaxa"></select>
                </div>
            </div>
        </div>
        <div class="col-lg-12">

            <div id="map" style="height: 540px; z-index: 1;"></div>

        </div>

        <br>
        @if(session('user_name') == 'Aan Syahputra')
        <div class="col-sm-12">
            <div class="table-responsive">
                <h1 style="text-align: center;">Edit Nama Blok</h1>

                <table class="table table-striped table-bordered" id="user_qc" style="background-color: white;">
                    <thead>
                        <!-- Table header content -->
                    </thead>
                    <tbody>
                        <!-- Table body content will be dynamically generated -->
                    </tbody>
                </table>
            </div>
        </div>
        @endif


    </div>
    <br>

    <div class="row">
        <div class="col-lg-12">
            <div class="top-cat-title">
                <h3>Persentase </h3>
                <p>Perlakuan Pokok Kuning</p>
            </div>
            <div id="chart" style="background-color: white;">
                <!-- Your chart content here -->
            </div>
            <div class="top-cat-title" style="padding-top: 10px;">
                <h3>Persentase </h3>
                <p>Total Pokok Kuning</p>
            </div>
            <div id="chartPie" style="background-color: white;">
                <!-- Your chart content here -->
            </div>
        </div>
    </div>


    <div class="container-fluid" id="blok_pemupukan" style="display: none;">
        <div class="top-cat-title" style="padding-top: 10px;">
            <h3>Pemupukan </h3>
            <p>Data perblok</p>

        </div>
        <div class="row">

            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Rekom
                            <p class="tahun_mulai" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #163D24; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_1" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Realisasi
                            <p class="tahun_progres" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #253272; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_2" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Progress
                            <p class="tahun_realisasi" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #26164C; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_3" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px;">
            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Rekom
                            <p class="tahun_mulai" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #163D24; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_4" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Realisasi
                            <p class="tahun_progres" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #253272; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_5" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            Progress
                            <p class="tahun_realisasi" style="font-size: 15px; border-top: 1px solid black;">2023</p>
                        </div>
                        <div class="card-body" style="background-color: #26164C; border-radius: 20px; text-align: center; height: 100px; display: flex; align-items: center;">
                            <h1 class="card-text" id="pk_6" style="font-size: 1rem; margin: 0 auto;color:white;">-</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="top-cat-title" style="margin-top: 30px;">
            <h3>Status Hara Tanaman</h3>
            <p>(LSU)</p>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #6c7cd2; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#1F2772">N</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #6c7cd2">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #3d3a9f">Nitrogen</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_1" style="background-color: #ff5424; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_1" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_2" style="background-color: #ff5424; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_2" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #6c7cd2; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#1F2772">P</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #6c7cd2">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #1F2772">Fosfor</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_3" style="background-color: #3c58b0; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align:left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_3" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_4" style="background-color: #08948c; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align:left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_4" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #fcdce0; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#aa2227">K</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #fcdce0">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #aa2227">Kalium</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_5" style="background-color: #3c58b0; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_5" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_6" style="background-color: #90c44c; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_6" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #b6f8fb; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#4cbdd1">B</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #b6f8fb">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #4cbdd1">Baron</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_7" style="background-color: #FF6C17; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_7" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_8" style="background-color: #FF6C17; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_8" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row" style="padding-top: 50px;">
            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #faf5c8; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#f6de6f">Mg</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #f6de6f">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #d9b754">Magnesium</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_9" style="background-color: #ff5424; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_9" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_10" style="background-color: #ff5424; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_10" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #faf5c8; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#f6de6f">Ca</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #f6de6f">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #d9b754">Kalsium</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_11" style="background-color: #3c58b0; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_11" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_12" style="background-color: #08948c; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_12" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #bff1c4; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#71c99c">Cu</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #71c99c">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #5aa963">Tembaga</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_13" style="background-color: #3c58b0; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_13" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_14" style="background-color: #90c44c; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_14" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card-wrapper">
                    <div class="card" style="background-color: white; border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                        <div class="card-header" style="text-align: center;">
                            <table style="padding-left: 15px;">
                                <thead>
                                    <tr style="background-color: #bff1c4; border-radius: 20px; height: 15%;">
                                        <th rowspan="3" style="text-align: left;font-size:30px;color:#71c99c">Zn</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center ; color: #71c99c">Hara Makro</th>
                                    </tr>
                                    <tr style="display: flex;">
                                        <th style="text-align: center; color: #5aa963">Zink</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_15" style="background-color: #FF6C17; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_mulai" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_15" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body" id="bg_16" style="background-color: #FF6C17; border-radius: 20px; height: 100px; width: 100px; display: flex; flex-direction: column;color: white; font-size: 1rem; margin: 0 auto;">
                                    <p class="tahun_belum" style="text-align: left;padding-left:10px; font-size: 10px;padding-top :10px">2023</p>
                                    <h1 class="card-text" id="km_16" style="font-size: 10px; color: white; text-align: center; padding-left: 2px;padding-top:30px">-</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- datables  -->
<link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    let user_name = "{{ session('user_name') }}";
    var options = {
        series: [{
            name: '-',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
        }, {
            name: '-',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
        }, {
            name: '-',
            data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
            title: {
                text: 'Pokok'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {

        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);

    chart.render();


    var DonutChart = {
        series: [44, 55, 13, 43, 22],
        chart: {
            height: '80%', // Set the height to 50% of its container
            width: '100%', // Set the width to 100% of its container
            type: 'pie',
        },
        labels: ['-', '-', '-', '-', '-'],
        responsive: [{
            breakpoint: 480,
            DonutChart: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var Dunut = new ApexCharts(document.querySelector("#chartPie"), DonutChart);
    Dunut.render();

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
    var opt_blok = <?php echo json_encode($option_blok); ?>;

    // console.log(opt_blok);

    var regional = document.getElementById('afdreg');
    var estate_sl = document.getElementById('est');
    var afdeling_sl = document.getElementById('afd');
    var blok_sl = document.getElementById('blokxaxa');



    // Function to populate a select element with options
    function populateSelect(selectElement, options) {
        // Clear existing options
        selectElement.innerHTML = '';

        // Create and add new options
        options.forEach(function(option) {
            var optionElement = document.createElement('option');
            optionElement.value = option.id;
            optionElement.textContent = option.nama;
            selectElement.appendChild(optionElement);
        });
    }

    populateSelect(regional, opt_reg);




    function populateEstateOptions(estateSelcted) {
        // Clear existing options

        // console.log(selectedWilIdx);
        estate_sl.innerHTML = '';

        // Filter the opt_est array based on the selectedWilIdx
        var filteredEstates = opt_est.filter(function(estate) {
            return estate.regional == estateSelcted;
        });
        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate.est;
            optionElement.textContent = estate.nama;
            estate_sl.appendChild(optionElement);
        });



        estate_sl.dispatchEvent(new Event('change'));
    }

    function populateAfdelingOptions(afdelingSelected) {
        // Clear existing options

        // console.log(selectedWilIdx);
        afdeling_sl.innerHTML = '';

        // Filter the opt_est array based on the selectedWilIdx
        var filteredEstates = opt_afd.filter(function(estate) {
            return estate.est == afdelingSelected;
        });
        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate.id;
            optionElement.textContent = estate.nama;
            afdeling_sl.appendChild(optionElement);
        });



        afdeling_sl.dispatchEvent(new Event('change'));
    }

    function populatbloks(afdelingSelected) {
        // Clear existing options
        blok_sl.innerHTML = '';
        // console.log(afdelingSelected);
        // Filter the opt_blok array based on the selected afdeling
        var filteredEstates = opt_blok.filter(function(estate) {
            return estate.afdeling == afdelingSelected;
        });

        // console.log(filteredEstates);

        filteredEstates.forEach(function(estate) {
            var optionElement = document.createElement('option');
            optionElement.value = estate['nama']; // Use estate['nama'] to access the property
            optionElement.textContent = estate['nama']; // Use estate['nama'] to access the property
            blok_sl.appendChild(optionElement);
        });

        blok_sl.dispatchEvent(new Event('change'));
    }




    $(document).ready(function() {



        var group = L.layerGroup();

        // Initialize the map and set its view
        var map = L.map('map', {
            editable: true // Enable editing
        }).setView([-2.2745234, 111.61404248], 11);

        // Define the "Google Satellite" tile layer
        var googleSatellite = L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 22, // Increase the maxZoom value to 22 or any desired value
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Add "Google Satellite" as the only base map
        googleSatellite.addTo(map);

        map.addControl(new L.Control.Fullscreen());

        var areaMapsLayer = L.layerGroup().addTo(map); // Create a layer group for area maps
        var markersLayer = L.markerClusterGroup().addTo(map);
        var markerBlok = L.markerClusterGroup().addTo(map);

        map.addLayer(areaMapsLayer);




        // function drawMaps(regions) {
        //     areaMapsLayer.clearLayers(); // Clear area maps layer only

        //     var bounds = new L.LatLngBounds(); // Create a bounds object to store the coordinates

        //     for (var i = 0; i < regions.length; i++) {
        //         var regionData = regions[i][1];

        //         // Check if regionData is an array
        //         if (Array.isArray(regionData)) {
        //             // Initialize an array to store coordinates for the polyline
        //             var coordinates = [];

        //             // Loop through the array of objects within regionData
        //             for (var j = 0; j < regionData.length; j++) {
        //                 var obj = regionData[j];
        //                 var lat = obj.lat;
        //                 var lon = obj.lon;
        //                 var afd_nama = obj.nama;

        //                 // Create a LatLng object for each coordinate
        //                 var latLng = new L.LatLng(lat, lon);

        //                 // Extend the bounds with the new LatLng object
        //                 bounds.extend(latLng);
        //                 coordinates.push(latLng);
        //             }

        //             // var polygon = L.polygon(coordinates).addTo(areaMapsLayer);

        //             var polygon = L.polygon(coordinates, {
        //                 fillOpacity: 0.05, // Set the fill opacity to a low value
        //                 opacity: 0.5 // Set the border opacity to a low value
        //             }).addTo(areaMapsLayer);

        //             var polygonCenter = polygon.getBounds().getCenter();

        //             var label = L.marker(polygonCenter, {
        //                 icon: L.divIcon({
        //                     className: 'label-blok',
        //                     html: afd_nama,
        //                     iconSize: [50, 10],
        //                 })
        //             }).addTo(areaMapsLayer);
        //         }
        //     }

        //     // Fit the map to the calculated bounds
        //     map.fitBounds(bounds);
        // }

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
                iconUrl: '{{ asset("img/circle_sudah.png") }}',
                iconSize: [32, 32],
            });
            var verified = L.icon({
                iconUrl: '{{ asset("img/verified.png") }}',
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
                div.innerHTML += '<label><input type="radio" name="statusFilter" value="Terverifikasi"> Terverifikasi</label><br>';
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
                            var pupuk = obj.pupuk;
                            var afd = obj.afd;

                            if ((statusFilter === "all" || statusFilter === status) && (kondisiFilter === "all" || kondisiFilter === kondisi)) {
                                var icon;
                                if (status === 'Sudah') {
                                    icon = iconSembuh;
                                } else if (status === 'Terverifikasi') {
                                    icon = verified;
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
                                var popupContent = `<div class="custom-popup"><strong>Dtracking Blok: </strong>${afd_nama}<br/>`;
                                popupContent += `<strong>Kondisi: </strong>${kondisi}<br/>`;
                                popupContent += `<strong>Status: </strong>${status}<br/>`;
                                popupContent += `<strong>ID: </strong>${id}<br/>`;
                                popupContent += `<strong>Afdeling: </strong>${afd}<br/>`;

                                if (foto) {
                                    var fotoArray = foto.split('$');
                                    var komentararray = komentar.split('$');
                                    for (var k = 0; k < fotoArray.length; k++) {
                                        var imageSrc = `https://mobilepro.srs-ssms.com/storage/app/public/deficiency_tracker/${fotoArray[k]}`;
                                        var comment = komentararray[k];

                                        popupContent += `<img class="popup-image" src="${imageSrc}" alt="Foto Temuan" onclick="openModal(this.src, '${comment}')"><br/>`;
                                    }
                                }

                                if (pupuk) {
                                    var komentpupuk = pupuk.split('$');
                                    popupContent += `<strong>Perlakuan: </strong><br/>`;
                                    for (var k = 0; k < komentpupuk.length; k++) {
                                        popupContent += `- ${komentpupuk[k]}<br/>`;

                                    }
                                }
                                popupContent += '</div>'; // Close the custom-popup div

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

        function drawMap(newData) {
            markerBlok.clearLayers(); // Clear area maps layer only

            var bounds = new L.LatLngBounds(); // Create a bounds object to store the coordinates

            for (var key in newData) {
                if (newData.hasOwnProperty(key)) {
                    var regionData = newData[key];

                    for (var i = 0; i < regionData.length; i++) {
                        var data = regionData[i].lat_lon;

                        if (Array.isArray(data)) {
                            // Initialize the coordinates array for each polygon
                            var coordinates = [];

                            for (var j = 0; j < data.length; j++) {
                                var latLon = data[j].split(';'); // Split the lat_lon string by ';'
                                var lat = parseFloat(latLon[0]);
                                var lon = parseFloat(latLon[1]);

                                if (!isNaN(lat) && !isNaN(lon)) {
                                    var latLng = new L.LatLng(lat, lon);

                                    // Extend the bounds with the new LatLng object
                                    bounds.extend(latLng);
                                    coordinates.push(latLng);
                                }
                            }

                            // Get other properties from your data
                            var afd_nama = regionData[i].afd_nama;
                            var jum_pokok = regionData[i].jum_pokok;
                            var Ditangani = regionData[i].Ditangani;
                            var Diverif = regionData[i].Diverif;
                            var kategori = regionData[i].kategori;
                            var pokok_namablok = regionData[i].pokok_namablok;
                            var Ket = regionData[i].Ket;
                            // Define a default style for the polygon
                            var polygonStyle = {
                                fillOpacity: 0.05,
                                opacity: 0.5
                            };

                            // Conditionally set the background color and opacity based on the 'kategori' value
                            if (kategori === 'Hijau') {
                                polygonStyle.fillColor = 'green';
                                polygonStyle.fillOpacity = 0.2; // Set the fill opacity for 'Hijau'
                                polygonStyle.opacity = 0.5; // Set the border opacity for 'Hijau'
                            } else {
                                polygonStyle.fillColor = 'blue';
                                polygonStyle.fillOpacity = 0.01; // Set the fill opacity for other categories
                                polygonStyle.opacity = 0.5; // Set the border opacity for other categories
                            }

                            var polygon = L.polygon(coordinates, polygonStyle).addTo(markerBlok);

                            var polygonCenter = polygon.getBounds().getCenter();

                            var popupContent = `<div class="custom-popup"><strong>Dtracking Blok: </strong>${afd_nama}<br/>`;
                            popupContent += `<strong>Jumlah Pokok: </strong>${jum_pokok}<br/>`;
                            popupContent += `<strong>Di tangani: </strong>${Ditangani}<br/>`;
                            popupContent += `<strong>DI Vertifikasi: </strong>${Diverif}<br/>`;
                            popupContent += `<strong>Kategori: </strong>${kategori}<br/>`;
                            popupContent += `<strong>Keterangan: </strong>${Ket}<br/>`;
                            // popupContent += `<strong>Nama blok pokok: </strong>${pokok_namablok}<br/>`
                            popupContent += '</div>'; // Close the custom-popup div

                            var label = L.marker(polygonCenter, {
                                icon: L.divIcon({
                                    className: 'label-blok',
                                    html: afd_nama,
                                    iconSize: [50, 10],
                                })
                            }).addTo(markerBlok);

                            label.bindPopup(popupContent);
                        }
                    }
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

            var container = document.getElementById("blok_pemupukan");

            // Hide all filter options initially
            regionalOption.style.display = 'none';
            estateOption.style.display = 'none';
            afdelingOption.style.display = 'none';
            blokOption.style.display = 'none';
            container.style.display = 'none';

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
                container.style.display = 'block';
            } else {
                // Handle any other cases or defaults here
            }
        }


        // Listen for changes in the radio buttons
        $('input[name="option"]').on('change', function() {
            var selectedOption = $(this).val();

            // Call the function to show additional options
            showAdditionalOptions(selectedOption);


        });



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
                requestData['blok'] = $('#blokxaxa').val();
                dataType = 'blok';
            }

            // Add the dataType to the requestData object
            if ($.fn.DataTable.isDataTable('#user_qc')) {
                $('#user_qc').DataTable().destroy();
            }
            requestData['dataType'] = dataType;


            // Swal.fire({
            //     title: 'Loading',
            //     html: '<span class="loading-text">Mohon Tunggu...</span>',
            //     allowOutsideClick: false,
            //     showConfirmButton: false,
            //     willOpen: () => {
            //         Swal.showLoading();
            //     }

            // });

            // Perform the AJAX request with the requestData
            $.ajax({
                url: "{{ route('drawMaps') }}",
                method: 'GET',
                data: requestData,
                success: function(result) {
                    Swal.close();
                    var plot = JSON.parse(result);

                    const pokok = Object.entries(plot['pokok']);
                    const drawBlok = Object.entries(plot['drawBlok']);
                    const new_blok = Object.entries(plot['new_blok']);
                    // console.log(result);
                    var totalx = plot['total_pokok'];
                    var total_ditangani = plot['total_ditangani'];
                    var persen_ditangani = plot['persen_ditangani'];
                    var pemupukan = plot['pemupukan'];
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
                    // console.log(pemupukan);

                    const pk1 = document.getElementById("pk_1");
                    pk1.textContent = pemupukan['rekom1'] + ' ' + 'ton/blok';
                    const pk2 = document.getElementById("pk_2");
                    pk2.textContent = pemupukan['realisasi1'] + ' ' + 'ton/blok';
                    const pk3 = document.getElementById("pk_3");
                    pk3.textContent = pemupukan['progress1'] + ' ' + '%';
                    const pk4 = document.getElementById("pk_4");
                    pk4.textContent = pemupukan['rekom2'] + ' ' + 'kg/blok';
                    const pk5 = document.getElementById("pk_5");
                    pk5.textContent = pemupukan['realisasi2'] + ' ' + 'kg/blok';
                    const pk6 = document.getElementById("pk_6");
                    pk6.textContent = pemupukan['progress2'] + ' ' + '%';


                    const checkVal = (value) => {
                        if (value === 'Defisiensi') {
                            return {
                                backgroundColor: '#ff5424'
                            };
                        } else if (value === 'Low') {
                            return {
                                backgroundColor: '#ffc404'
                            };
                        } else if (value === 'Optimum') {
                            return {
                                backgroundColor: '#4454bc'
                            };
                        } else if (value === 'High') {
                            return {
                                backgroundColor: '#8cc454'
                            };
                        } else if (value === 'Exceed') {
                            return {
                                backgroundColor: '#08948c'
                            };
                        } else if (value === 'Suff') {
                            return {
                                backgroundColor: '#734b4b'
                            };
                        } else {
                            return {
                                backgroundColor: '#484444'
                            };
                        }
                    };


                    const km_1 = document.getElementById("km_1");
                    const bg_1 = document.getElementById("bg_1");
                    km_1.textContent = pemupukan['status_1'];
                    bg_1.style.backgroundColor = checkVal(pemupukan['status_1']).backgroundColor;
                    const km_2 = document.getElementById("km_2");
                    const bg_2 = document.getElementById("bg_2");
                    km_2.textContent = pemupukan['status_2'];
                    bg_2.style.backgroundColor = checkVal(pemupukan['status_2']).backgroundColor;
                    const km_3 = document.getElementById("km_3");
                    const bg_3 = document.getElementById("bg_3");
                    km_3.textContent = pemupukan['status_3'];
                    bg_3.style.backgroundColor = checkVal(pemupukan['status_3']).backgroundColor;
                    const km_4 = document.getElementById("km_4");
                    const bg_4 = document.getElementById("bg_4");
                    km_4.textContent = pemupukan['status_4'];
                    bg_4.style.backgroundColor = checkVal(pemupukan['status_4']).backgroundColor;
                    const km_5 = document.getElementById("km_5");
                    const bg_5 = document.getElementById("bg_5");
                    km_5.textContent = pemupukan['status_5'];
                    bg_5.style.backgroundColor = checkVal(pemupukan['status_5']).backgroundColor;
                    const km_6 = document.getElementById("km_6");
                    const bg_6 = document.getElementById("bg_6");
                    km_6.textContent = pemupukan['status_6'];
                    bg_6.style.backgroundColor = checkVal(pemupukan['status_6']).backgroundColor;
                    const km_7 = document.getElementById("km_7");
                    const bg_7 = document.getElementById("bg_7");
                    km_7.textContent = pemupukan['status_7'];
                    bg_7.style.backgroundColor = checkVal(pemupukan['status_7']).backgroundColor;
                    const km_8 = document.getElementById("km_8");
                    const bg_8 = document.getElementById("bg_8");
                    km_8.textContent = pemupukan['status_8'];
                    bg_8.style.backgroundColor = checkVal(pemupukan['status_8']).backgroundColor;
                    const bg_9 = document.getElementById("bg_9");
                    const km_9 = document.getElementById("km_9");
                    bg_9.style.backgroundColor = checkVal(pemupukan['status_9']).backgroundColor;
                    km_9.textContent = pemupukan['status_9'];
                    const bg_10 = document.getElementById("bg_10");
                    const km_10 = document.getElementById("km_10");
                    bg_10.style.backgroundColor = checkVal(pemupukan['status_10']).backgroundColor;
                    km_10.textContent = pemupukan['status_10'];
                    const bg_11 = document.getElementById("bg_11");
                    const km_11 = document.getElementById("km_11");
                    bg_11.style.backgroundColor = checkVal(pemupukan['status_11']).backgroundColor;
                    km_11.textContent = pemupukan['status_11'];
                    const bg_12 = document.getElementById("bg_12");
                    const km_12 = document.getElementById("km_12");
                    bg_12.style.backgroundColor = checkVal(pemupukan['status_12']).backgroundColor;
                    km_12.textContent = pemupukan['status_12'];
                    const bg_13 = document.getElementById("bg_13");
                    const km_13 = document.getElementById("km_13");
                    bg_13.style.backgroundColor = checkVal(pemupukan['status_13']).backgroundColor;
                    km_13.textContent = pemupukan['status_13'];
                    const bg_14 = document.getElementById("bg_14");
                    const km_14 = document.getElementById("km_14");
                    bg_14.style.backgroundColor = checkVal(pemupukan['status_14']).backgroundColor;
                    km_14.textContent = pemupukan['status_14'];
                    const bg_15 = document.getElementById("bg_15");
                    const km_15 = document.getElementById("km_15");
                    bg_15.style.backgroundColor = checkVal(pemupukan['status_15']).backgroundColor;
                    km_15.textContent = pemupukan['status_15'];
                    const bg_16 = document.getElementById("bg_16");
                    const km_16 = document.getElementById("km_16");
                    bg_16.style.backgroundColor = checkVal(pemupukan['status_16']).backgroundColor;
                    km_16.textContent = pemupukan['status_16'];


                    const th_ml = document.getElementsByClassName("tahun_mulai");
                    const th_bl = document.getElementsByClassName("tahun_belum");
                    const tahun_progres = document.getElementsByClassName("tahun_progres");
                    const tahun_realisasi = document.getElementsByClassName("tahun_realisasi");

                    // Loop through the elements and update their text content
                    for (let i = 0; i < th_ml.length; i++) {
                        th_ml[i].textContent = pemupukan['tahun_mulai'];
                    }
                    for (let i = 0; i < th_bl.length; i++) {
                        th_bl[i].textContent = pemupukan['tahun_belum'];
                    }
                    for (let i = 0; i < tahun_progres.length; i++) {
                        tahun_progres[i].textContent = pemupukan['tahun_progres'];
                    }
                    for (let i = 0; i < tahun_realisasi.length; i++) {
                        tahun_realisasi[i].textContent = pemupukan['tahun_realisasi'];
                    }


                    let type = requestData['dataType'];

                    // console.log(type);

                    // if (type == 'regional') {
                    //     drawMaps(RegResult);
                    // } else {
                    //     drawMap(new_blok);
                    // }
                    drawMap(new_blok);

                    drawPokok(pokok);




                    var listQC = $('#user_qc').DataTable({
                        columns: [{
                                title: 'ID',
                                data: 'id',
                            },
                            {
                                title: 'Estate',
                                data: 'est',
                            },
                            {
                                title: 'Afdeling',
                                data: 'afd',
                            },
                            {
                                title: 'Blok',
                                data: 'blok',
                            },
                            {
                                // -1 targets the last column
                                title: 'Actions',
                                visible: (user_name === 'Aan Syahputra'),
                                render: function(data, type, row, meta) {
                                    var buttons =
                                        '<button class="edit-btn">Edit</button>'
                                    return buttons;
                                }
                            }
                        ],

                    });

                    // Populate DataTable with data
                    listQC.clear().rows.add(plot['datatables']).draw();

                    $('#user_qc').on('click', '.edit-btn', function() {
                        var rowData = listQC.row($(this).closest('tr')).data();
                        var rowIndex = listQC.row($(this).closest('tr')).index();
                        editqc(rowIndex);
                    });

                    function editqc(id) {
                        selectedRowIndex = id;
                        var rowData = listQC.row(id).data();
                        var blok = rowData.blok;
                        var _token = $('input[name="_token"]').val();

                        Swal.fire({
                            title: 'Masukan Blok',
                            input: 'text',
                            inputLabel: 'Edit nama Blok',
                            inputValue: blok,
                            inputAttributes: {
                                maxlength: 50,
                                autocapitalize: 'off',
                                autocorrect: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Save', // Change the text of the confirm button to "Save"
                            showLoaderOnConfirm: true, // Show a loading spinner on the confirm button
                            preConfirm: (newBlok) => { // Handle the confirmation
                                return $.ajax({
                                    type: 'POST',
                                    url: '{{ route("updateUserqc") }}',
                                    data: {
                                        id: rowData.id,
                                        blok: newBlok, // Use the new value entered by the user
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': _token
                                    }
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading() // Prevent closing the dialog while loading
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire('Disimpan!', 'User QC sudah diupdate!', 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 3000); // 3000 milliseconds = 3 seconds
                            } else if (result.isDenied) {
                                Swal.fire('Batal', 'Pengeditan dibatalkan.', 'info');
                            }
                        });
                    }



                    var ktg_pk = plot['ktg_pk'];
                    var ditangani_pk = plot['ditangani_pk'];
                    var belum_pk = plot['belum_pk'];
                    var jum_pokok = plot['jum_pokok'];

                    // console.log(ktg_pk);
                    chart.updateSeries([{
                            name: 'Sudah Di tangani',
                            data: ditangani_pk
                        },
                        {
                            name: 'Belum Di tangani',
                            data: belum_pk
                        }
                    ])

                    chart.updateOptions({
                        xaxis: {
                            categories: ktg_pk
                        },

                    })

                    // Assuming jum_pokok is an array of absolute values
                    // Update the series data with the absolute values
                    Dunut.updateSeries(jum_pokok);


                    // Update the chart options
                    Dunut.updateOptions({
                        // You can change any option you want here
                        chart: {
                            // For example, you can change the width
                            width: '80%',
                        },
                        labels: ktg_pk
                    });




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