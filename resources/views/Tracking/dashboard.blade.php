@include('Layout.header')




<div class="container">
    <h2 class="main-title" style="text-align: center;">Tracking Plot Kuning</h2>

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

    <div class="row mt-10">
        <div class="col-lg-12">

            <div class="card p-4">
                <h4 class="main-title" style="font-weight: bold;text-align:center">Tracking Maps </h4>
                <hr>
                <div id="map" style="height: 650px; background-color: white;"></div>
            </div>


            <div class="chart">
                <canvas id="myChart" aria-label="Site statistics" role="img"></canvas>
            </div>

        </div>
    </div>

</div>

@include('Layout.footer')