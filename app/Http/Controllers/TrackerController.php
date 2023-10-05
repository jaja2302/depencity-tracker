<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackerController extends Controller
{
    //
    public function dashboard()
    {

        $optionREg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->whereNotIn('reg.id', [5])
            // ->where('wil.regional', 1)
            ->get();


        $optionREg = json_decode($optionREg, true);


        $filterEst = DB::connection('mysql2')->table('estate')
            ->select('estate.*', 'wil.regional')
            ->whereNotIn('estate.est', ['CWS1', 'NBM', 'REG-1', 'SLM', 'SR', 'TC', 'SRS', 'SGM', 'SYM', 'SKM'])
            ->join('wil', 'wil.id', '=', 'estate.wil')
            ->get();

        $filterEst = json_decode($filterEst, true);

        // dd($filterEst, $optionREg);

        $filterAfd = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*', 'estate.est')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->get();

        $filterAfd = json_decode($filterAfd, true);


        $filterblok = DB::connection('mysql2')->table('blok')
            ->select('blok.*', 'estate.est')
            ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->get();
        // $filterblok = $filterblok->groupBy(['afdeling']);
        $filterblok = json_decode($filterblok, true);


        $uniqueItems = [];
        $uniqueNamas = [];

        foreach ($filterblok as $item) {
            $nama = $item['nama'];
            $afdeling = $item['afdeling'];
            $key = $nama . '_' . $afdeling; // Create a unique key based on "nama" and "afdeling"

            // Check if the key is already in the $uniqueNamas array
            if (!in_array($key, $uniqueNamas)) {
                $uniqueNamas[] = $key; // Add the unique key to the $uniqueNamas array
                $uniqueItems[] = $item; // Add the item to the $uniqueItems array
            }
        }


        // dd($filterAfd);
        $perum = DB::connection('mysql2')->table('perumahan')
            ->select(DB::raw('DISTINCT YEAR(datetime) as year'))
            ->orderBy('year', 'asc')
            ->get();

        $years = [];
        foreach ($perum as $sidak) {
            $years[] = $sidak->year;
        }
        // dd($optionREg, $filterEst);

        // Your dashboard logic goes here
        return view('Tracking.dashboard', [
            'option_reg' => $optionREg,
            'option_est' => $filterEst,
            'option_afd' => $filterAfd,
            'option_blok' => $uniqueItems,
            'list_tahun' => $years
        ]);
    }

    public function info()
    {

        return view('Tracking.info');
    }

    public function getBlok(Request $request)
    {

        $afd = $request->input('afd');

        // Your code to retrieve and decode the data
        $filterAfd = DB::connection('mysql2')->table('blok')
            ->select('blok.afdeling', 'blok.nama')
            ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->where('blok.afdeling', '=', $afd)
            ->get();

        $filterAfd = json_decode($filterAfd, true);

        // Define an empty array to store unique entries
        $uniqueEntries = [];

        // Loop through the $filterAfd array
        foreach ($filterAfd as $entry) {
            $isDuplicate = false;

            // Check if the current entry already exists in $uniqueEntries
            foreach ($uniqueEntries as $uniqueEntry) {
                if ($entry['afdeling'] === $uniqueEntry['afdeling'] && $entry['nama'] === $uniqueEntry['nama']) {
                    $isDuplicate = true;
                    break;
                }
            }

            // If it's not a duplicate, add it to $uniqueEntries
            if (!$isDuplicate) {
                $uniqueEntries[] = $entry;
            }
        }

        // Now, $uniqueEntries contains unique entries based on both "afdeling" and "nama"


        // dd($uniqueEntries);
        $arrView = array();
        $arrView['blok'] =  $uniqueEntries;

        echo json_encode($arrView); //di decode ke dalam bentuk json dalam vaiavel arrview yang dapat menampung banyak isi array
        exit();
    }


    public function drawMaps(Request $request)
    {
        // Retrieve the data sent in the AJAX request
        $regional = $request->input('regional');
        $estate = $request->input('estate');
        $afdeling = $request->input('afdeling');
        $blok = $request->input('blok');
        $dataType = $request->input('dataType'); // Retrieve the dataType

        // dd($afdeling);
        $arrView = array();
        switch ($dataType) {
            case 'regional':

                // untuk draw map 
                $plotEst = DB::connection('mysql2')
                    ->table('estate_plot')
                    ->select('estate_plot.*', 'wil.regional', 'wil.nama as nama')
                    ->join('estate', 'estate.est', '=', 'estate_plot.est')
                    ->join('wil', 'wil.id', '=', 'estate.wil')
                    ->where('wil.regional', '=', $regional)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $plotEst = $plotEst->groupBy(['est']);
                $plotEst = json_decode($plotEst, true);



                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*', 'wil.regional')
                    ->join('estate', 'estate.est', '=', 'deficiency_tracker.est')
                    ->join('wil', 'wil.id', '=', 'estate.wil')
                    ->where('wil.regional', '=', $regional)
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    // ->take(100) // Limit the result to 100 items
                    ->get();

                $plot_kuning = $plot_kuning->groupBy(['est']);
                $plot_kuning = json_decode($plot_kuning, true);

                $count = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] !== "Sudah";
                    }));
                }, 0);

                $count_sudah = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] === "Sudah";
                    }));
                }, 0);

                // $count_sudah = 0;

                if (($count + $count_sudah) !== 0) {
                    $percentage_sudah = round(($count_sudah / ($count + $count_sudah)) * 100, 2);
                } else {
                    $percentage_sudah = 0; // Set a default value (0 or any other suitable value) when the denominator is zero.
                }

                $drawBlok = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    // ->where('blok.nama', '=', $blok)

                    ->orderBy('id', 'desc')
                    ->get();

                $drawBlok = $drawBlok->groupBy(['est']);
                $drawBlok = json_decode($drawBlok, true);

                // dd($plotBlok);
                $values = [];

                $new_blok = [];

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['blok'] = $plotEst;
                $arrView['pokok'] = $plot_kuning;

                // dd($plotEst);
                break;

            case 'estate':

                $plotEst = DB::connection('mysql2')
                    ->table('estate_plot')
                    ->select('estate_plot.*', 'estate.nama as nama')
                    ->join('estate', 'estate.est', '=', 'estate_plot.est')
                    ->where('estate_plot.est', '=', $estate)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $plotEst = $plotEst->groupBy(['est']);
                $plotEst = json_decode($plotEst, true);


                // dd($plotEst);


                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->where('deficiency_tracker.est', '=', $estate)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);

                $count = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] !== "Sudah";
                    }));
                }, 0);

                $count_sudah = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] === "Sudah";
                    }));
                }, 0);

                // dd($plot_kuning);



                if (($count + $count_sudah) !== 0) {
                    $percentage_sudah = round(($count_sudah / ($count + $count_sudah)) * 100, 2);
                } else {
                    $percentage_sudah = 0; // Set a default value (0 or any other suitable value) when the denominator is zero.
                }

                $drawBlok = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    // ->where('blok.nama', '=', $blok)

                    ->orderBy('id', 'desc')
                    ->get();

                $drawBlok = $drawBlok->groupBy(['nama']);
                $drawBlok = json_decode($drawBlok, true);

                // dd($drawBlok);


                $values = [];


                $new_blok = array();
                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0

                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);

                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }

                                    if ($statusCount >= 5 && $jumblok >= 1 && $jumblok <= 5) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 5 && $jumblok >= 6 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount == 10 &&  $jumblok == 10) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 10 && $jumblok >= 11 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 20 && $jumblok >= 30) {
                                        $kategori = 'Hijau';
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    // If lat_lon is still empty, collect all 'lat_lon' values from $value
                    if (empty($lat_lon)) {
                        foreach ($value as $item) {
                            if (isset($item['lat']) && isset($item['lon'])) {
                                $lat = $item['lat'];
                                $lon = $item['lon'];
                                $lat_lon[] = $lat . ';' . $lon;
                            }
                        }
                    }

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }


                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;

                $arrView['blok'] = $plotEst;
                $arrView['pokok'] = $plot_kuning;

                break;

            case 'afdeling':
                # code...
                // dd($afdeling);

                $plotAfd = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('blok.afdeling', '=', $afdeling)
                    ->orderBy('id', 'asc')
                    ->get();

                $plotAfd = $plotAfd->groupBy(['nama']);


                // dd($plotAfd);



                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);

                // dd($plot_kuning);




                $count = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] !== "Sudah";
                    }));
                }, 0);

                $count_sudah = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] === "Sudah";
                    }));
                }, 0);

                // $count_sudah = 0;

                if (($count + $count_sudah) !== 0) {
                    $percentage_sudah = round(($count_sudah / ($count + $count_sudah)) * 100, 2);
                } else {
                    $percentage_sudah = 0; // Set a default value (0 or any other suitable value) when the denominator is zero.
                }



                $drawBlok = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)

                    ->orderBy('id', 'desc')
                    ->get();

                $drawBlok = $drawBlok->groupBy(['nama']);
                $drawBlok = json_decode($drawBlok, true);


                $values = reset($plot_kuning);

                $new_blok = array();
                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0

                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);

                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }

                                    if ($statusCount >= 5 && $jumblok >= 1 && $jumblok <= 5) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 5 && $jumblok >= 6 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount == 10 &&  $jumblok == 10) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 10 && $jumblok >= 11 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 20 && $jumblok >= 30) {
                                        $kategori = 'Hijau';
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    // If lat_lon is still empty, collect all 'lat_lon' values from $value
                    if (empty($lat_lon)) {
                        foreach ($value as $item) {
                            if (isset($item['lat']) && isset($item['lon'])) {
                                $lat = $item['lat'];
                                $lon = $item['lon'];
                                $lat_lon[] = $lat . ';' . $lon;
                            }
                        }
                    }

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }




                // dd($new_blok, $drawBlok, $plot_kuning);


                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['blok'] = $plotAfd;
                $arrView['pokok'] = $plot_kuning;
                break;

            case 'blok':
                $plotBlok = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('blok.nama', '=', $blok)

                    ->orderBy('id', 'desc')
                    ->get();

                $plotBlok = $plotBlok->groupBy(['est']);
                $plotBlok = json_decode($plotBlok, true);

                // dd($blok);
                if ($regional == 2) {
                    $inputString = $blok;
                    $modifiedString = preg_replace('/0/', '', $inputString, 1);
                } else {

                    $modifiedString = $blok;
                }

                // dd($modifiedString);

                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('deficiency_tracker.blok', 'LIKE', '%' . $modifiedString . '%')
                    // ->where('deficiency_tracker.blok', '=', 'D02')
                    ->orderBy('id', 'desc')
                    ->get();


                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);
                // dd($plot_kuning, $blok, $estate);
                $count = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] !== "Sudah";
                    }));
                }, 0);

                $count_sudah = array_reduce($plot_kuning, function ($carry, $items) {
                    return $carry + count(array_filter($items, function ($item) {
                        return $item['status'] === "Sudah";
                    }));
                }, 0);

                // $count_sudah = 0;

                if (($count + $count_sudah) !== 0) {
                    $percentage_sudah = round(($count_sudah / ($count + $count_sudah)) * 100, 2);
                } else {
                    $percentage_sudah = 0; // Set a default value (0 or any other suitable value) when the denominator is zero.
                }


                $drawBlok = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('blok.nama', '=', $blok)

                    ->orderBy('id', 'desc')
                    ->get();

                $drawBlok = $drawBlok->groupBy(['nama']);
                $drawBlok = json_decode($drawBlok, true);
                $values = [];
                $new_blok = array();
                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0

                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);

                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }

                                    if ($statusCount >= 5 && $jumblok >= 1 && $jumblok <= 5) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 5 && $jumblok >= 6 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount == 10 &&  $jumblok == 10) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 10 && $jumblok >= 11 && $jumblok <= 30) {
                                        $kategori = 'Hijau';
                                    } elseif ($statusCount >= 20 && $jumblok >= 30) {
                                        $kategori = 'Hijau';
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    // If lat_lon is still empty, collect all 'lat_lon' values from $value
                    if (empty($lat_lon)) {
                        foreach ($value as $item) {
                            if (isset($item['lat']) && isset($item['lon'])) {
                                $lat = $item['lat'];
                                $lon = $item['lon'];
                                $lat_lon[] = $lat . ';' . $lon;
                            }
                        }
                    }

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['blok'] = $plotBlok;
                $arrView['pokok'] = $plot_kuning;
                break;

            default:
                # code...
                break;
        }
        // Perform any other actions you need based on the selected options

        echo json_encode($arrView);
        exit();
    }

    public function updateUserqc(Request $request)
    {
        $id = $request->input('id');
        $blok = $request->input('blok');

        // dd($id, $blok);

        DB::connection('mysql2')->table('deficiency_tracker')
            ->where('id', $id)->update([
                'blok' => $blok,
            ]);
    }
}
