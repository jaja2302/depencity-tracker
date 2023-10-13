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
                    ->table('estate_plot')
                    ->select('estate_plot.*', 'wil.regional', 'wil.nama as nama')
                    ->join('estate', 'estate.est', '=', 'estate_plot.est')
                    ->join('wil', 'wil.id', '=', 'estate.wil')
                    ->where('wil.regional', '=', $regional)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $drawBlok = $drawBlok->groupBy(['est']);
                $drawBlok = json_decode($drawBlok, true);

                // dd($drawBlok);
                $values = [];

                $outputArray = [];

                foreach ($plot_kuning as $key => $value) {
                    if ($regional == 2 && $estate == 'NKE') {
                        $newKey = preg_replace('/^P-/', '', $key);

                        // Merge the arrays if the key already exists in the output array
                        if (isset($outputArray[$newKey])) {
                            $outputArray[$newKey] = array_merge($outputArray[$newKey], $value);
                        } else {
                            $outputArray[$newKey] = $value;
                        }
                    } else {
                        // If conditions are not met, keep the original key and value
                        $outputArray[$key] = $value;
                    }
                }

                // dd($outputArray);

                $new_blok = array();

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        $modifiedKey = '-';
                        if ($regional == 2 && $estate !== 'NKE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else if ($regional == 1 && $estate == 'PLE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else {
                            if (strpos($key, 'CBI') !== false && strlen($key) == 9) {
                                $sliced = substr($key, 0, -6);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else if (strpos($key, 'CBI') !== false) {
                                $modifiedKey = substr($key, 0, -4);
                            } else if (strpos($key, 'CB') !== false) {
                                $replace = substr_replace($key, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $modifiedKey = $key;
                            }
                        }

                        foreach ($outputArray as $key3 => $value3) {

                            // $test = 'N38-CBI';
                            $newKey = '-';
                            if (strpos($key3, 'CBI') !== false) {
                                $parts = explode('-CBI', $key3);
                                $newKey = $parts[0];
                                // dd($newKey);
                            } else if (strpos($key3, 'CB') !== false) {
                                $replace = substr_replace($key3, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $newKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $newKey = $key3;
                            }

                            // dd($newKey);
                            if ($modifiedKey === $newKey) {
                                $foundNewKey = $newKey;
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);
                                    // dd($value4);
                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }


                                    if ($jumblok >= 1000 && $jumblok < 1000) {
                                        if ($statusCount >= 500) {
                                            $kategori = 'Hijau';
                                            $ket = '1000 : 500';
                                        }
                                    } elseif ($jumblok >= 30 && $jumblok < 100) {
                                        if ($statusCount >= 20) {
                                            $kategori = 'Hijau';
                                            $ket = '100 : 20';
                                        }
                                    } elseif ($jumblok >= 10 && $jumblok <= 30) {
                                        if ($statusCount >= 10) {
                                            $kategori = 'Hijau';
                                            $ket = '11 : 10';
                                        }
                                    } elseif ($jumblok  >= 6 && $jumblok < 10) {
                                        if ($statusCount = 5) {
                                            $kategori = 'Hijau';
                                            $ket = '10 : 10';
                                        }
                                    } elseif ($jumblok  == 1 && $jumblok < 6) {
                                        if ($statusCount >= 5) {
                                            $kategori = 'Hijau';
                                            $ket = '6 : 5';
                                        }
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }

                                $new_blok[$key]['pokok_namablok'] = $value4['blok'];
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
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                // dd($new_blok);


                $new_pk = array();
                foreach ($plot_kuning as $key => $value) {
                    foreach ($value as $key1 => $value1) {

                        if ($value1['jenis_pupuk_id'] != null) {
                            $pupukx = explode('$', $value1['jenis_pupuk_id']);
                            // dd($pupukx);
                            $pupuk = DB::connection('mysql2')
                                ->table('pupuk')
                                ->select('pupuk.*')
                                ->whereIn('pupuk.id', $pupukx) // Pass the array directly to whereIn
                                ->orderBy('id', 'desc')
                                ->pluck('nama');

                            $pupuk = json_decode(json_encode($pupuk), true); // Convert the result to an array
                            $new_ppk = implode("$", $pupuk);
                            $komnt_ppk = implode(" - ", $pupuk);
                        } else {
                            $new_ppk = null;
                            $komnt_ppk = null;
                        }




                        // dd($new_ppk);
                        $new_pk[$key][$key1]['pupuk'] = $new_ppk;
                        $new_pk[$key][$key1]['ppk_kmn'] = $komnt_ppk;
                        $new_pk[$key][$key1]['lat'] = $value1['lat'];
                        $new_pk[$key][$key1]['lon'] = $value1['lon'];
                        $new_pk[$key][$key1]['blok'] = $value1['blok'];
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                    }
                }

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                // $arrView['blok'] = $plotEst;
                $arrView['pokok'] = $new_pk;

                // dd($plotEst);
                break;

            case 'estate':



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

                $outputArray = [];

                foreach ($plot_kuning as $key => $value) {
                    if ($regional == 2 && $estate == 'NKE') {
                        $newKey = preg_replace('/^P-/', '', $key);

                        // Merge the arrays if the key already exists in the output array
                        if (isset($outputArray[$newKey])) {
                            $outputArray[$newKey] = array_merge($outputArray[$newKey], $value);
                        } else {
                            $outputArray[$newKey] = $value;
                        }
                    } else {
                        // If conditions are not met, keep the original key and value
                        $outputArray[$key] = $value;
                    }
                }
                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        $modifiedKey = '-';
                        if ($regional == 2 && $estate !== 'NKE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else if ($regional == 1 && $estate == 'PLE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else {
                            if (strpos($key, 'CBI') !== false && strlen($key) == 9) {
                                $sliced = substr($key, 0, -6);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else if (strpos($key, 'CBI') !== false) {
                                $modifiedKey = substr($key, 0, -4);
                            } else if (strpos($key, 'CB') !== false) {
                                $replace = substr_replace($key, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $modifiedKey = $key;
                            }
                        }

                        foreach ($outputArray as $key3 => $value3) {

                            // $test = 'N38-CBI';
                            $newKey = '-';
                            if (strpos($key3, 'CBI') !== false) {
                                $parts = explode('-CBI', $key3);
                                $newKey = $parts[0];
                                // dd($newKey);
                            } else if (strpos($key3, 'CB') !== false) {
                                $replace = substr_replace($key3, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $newKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $newKey = $key3;
                            }

                            // dd($newKey);
                            if ($modifiedKey === $newKey) {
                                $foundNewKey = $newKey;
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);
                                    // dd($value4);
                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }


                                    if ($jumblok >= 1000 && $jumblok < 1000) {
                                        if ($statusCount >= 500) {
                                            $kategori = 'Hijau';
                                            $ket = '1000 : 500';
                                        }
                                    } elseif ($jumblok >= 30 && $jumblok < 100) {
                                        if ($statusCount >= 20) {
                                            $kategori = 'Hijau';
                                            $ket = '100 : 20';
                                        }
                                    } elseif ($jumblok >= 10 && $jumblok <= 30) {
                                        if ($statusCount >= 10) {
                                            $kategori = 'Hijau';
                                            $ket = '11 : 10';
                                        }
                                    } elseif ($jumblok  >= 6 && $jumblok < 10) {
                                        if ($statusCount = 5) {
                                            $kategori = 'Hijau';
                                            $ket = '10 : 10';
                                        }
                                    } elseif ($jumblok  == 1 && $jumblok < 6) {
                                        if ($statusCount >= 5) {
                                            $kategori = 'Hijau';
                                            $ket = '6 : 5';
                                        }
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }

                                $new_blok[$key]['pokok_namablok'] = $value4['blok'];
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
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }


                $new_pk = array();
                foreach ($plot_kuning as $key => $value) {
                    foreach ($value as $key1 => $value1) {

                        if ($value1['jenis_pupuk_id'] != null) {
                            $pupukx = explode('$', $value1['jenis_pupuk_id']);
                            // dd($pupukx);
                            $pupuk = DB::connection('mysql2')
                                ->table('pupuk')
                                ->select('pupuk.*')
                                ->whereIn('pupuk.id', $pupukx) // Pass the array directly to whereIn
                                ->orderBy('id', 'desc')
                                ->pluck('nama');

                            $pupuk = json_decode(json_encode($pupuk), true); // Convert the result to an array
                            $new_ppk = implode("$", $pupuk);
                            $komnt_ppk = implode(" - ", $pupuk);
                        } else {
                            $new_ppk = null;
                            $komnt_ppk = null;
                        }




                        // dd($new_ppk);
                        $new_pk[$key][$key1]['pupuk'] = $new_ppk;
                        $new_pk[$key][$key1]['ppk_kmn'] = $komnt_ppk;
                        $new_pk[$key][$key1]['lat'] = $value1['lat'];
                        $new_pk[$key][$key1]['lon'] = $value1['lon'];
                        $new_pk[$key][$key1]['blok'] = $value1['blok'];
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                    }
                }

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['pokok'] = $new_pk;

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
                // dd($plot_kuning['G03']);

                $new_pk = array();
                foreach ($plot_kuning as $key => $value) {
                    foreach ($value as $key1 => $value1) {

                        if ($value1['jenis_pupuk_id'] != null) {
                            $pupukx = explode('$', $value1['jenis_pupuk_id']);
                            // dd($pupukx);
                            $pupuk = DB::connection('mysql2')
                                ->table('pupuk')
                                ->select('pupuk.*')
                                ->whereIn('pupuk.id', $pupukx) // Pass the array directly to whereIn
                                ->orderBy('id', 'desc')
                                ->pluck('nama');

                            $pupuk = json_decode(json_encode($pupuk), true); // Convert the result to an array
                            $new_ppk = implode("$", $pupuk);
                            $komnt_ppk = implode(" - ", $pupuk);
                        } else {
                            $new_ppk = null;
                            $komnt_ppk = null;
                        }




                        // dd($new_ppk);
                        $new_pk[$key][$key1]['pupuk'] = $new_ppk;
                        $new_pk[$key][$key1]['ppk_kmn'] = $komnt_ppk;
                        $new_pk[$key][$key1]['lat'] = $value1['lat'];
                        $new_pk[$key][$key1]['lon'] = $value1['lon'];
                        $new_pk[$key][$key1]['blok'] = $value1['blok'];
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                    }
                }
                // dd($new_pk);

                $outputArray = [];

                foreach ($plot_kuning as $key => $value) {
                    if ($regional == 2 && $estate == 'NKE') {
                        $newKey = preg_replace('/^P-/', '', $key);

                        // Merge the arrays if the key already exists in the output array
                        if (isset($outputArray[$newKey])) {
                            $outputArray[$newKey] = array_merge($outputArray[$newKey], $value);
                        } else {
                            $outputArray[$newKey] = $value;
                        }
                    } else {
                        // If conditions are not met, keep the original key and value
                        $outputArray[$key] = $value;
                    }
                }

                // dd($outputArray);

                $datatables = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();


                $datatables = json_decode($datatables, true);



                // dd($datatables);


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


                // dd($drawBlok);
                $new_blok = array();

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        $modifiedKey = '-';
                        if ($regional == 2 && $estate !== 'NKE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else if ($regional == 1 && $estate == 'PLE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else {
                            if (strpos($key, 'CBI') !== false && strlen($key) == 9) {
                                $sliced = substr($key, 0, -6);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else if (strpos($key, 'CBI') !== false) {
                                $modifiedKey = substr($key, 0, -4);
                            } else if (strpos($key, 'CB') !== false) {
                                $replace = substr_replace($key, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $modifiedKey = $key;
                            }
                        }

                        foreach ($outputArray as $key3 => $value3) {

                            // $test = 'N38-CBI';
                            $newKey = '-';
                            if (strpos($key3, 'CBI') !== false) {
                                $parts = explode('-CBI', $key3);
                                $newKey = $parts[0];
                                // dd($newKey);
                            } else if (strpos($key3, 'CB') !== false) {
                                $replace = substr_replace($key3, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $newKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $newKey = $key3;
                            }

                            // dd($newKey);
                            if ($modifiedKey === $newKey) {
                                $foundNewKey = $newKey;
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);
                                    // dd($value4);
                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }


                                    if ($jumblok >= 1000 && $jumblok < 1000) {
                                        if ($statusCount >= 500) {
                                            $kategori = 'Hijau';
                                            $ket = '1000 : 500';
                                        }
                                    } elseif ($jumblok >= 30 && $jumblok < 100) {
                                        if ($statusCount >= 20) {
                                            $kategori = 'Hijau';
                                            $ket = '100 : 20';
                                        }
                                    } elseif ($jumblok >= 10 && $jumblok <= 30) {
                                        if ($statusCount >= 10) {
                                            $kategori = 'Hijau';
                                            $ket = '11 : 10';
                                        }
                                    } elseif ($jumblok  >= 6 && $jumblok < 10) {
                                        if ($statusCount = 5) {
                                            $kategori = 'Hijau';
                                            $ket = '10 : 10';
                                        }
                                    } elseif ($jumblok  == 1 && $jumblok < 6) {
                                        if ($statusCount >= 5) {
                                            $kategori = 'Hijau';
                                            $ket = '6 : 5';
                                        }
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }

                                $new_blok[$key]['pokok_namablok'] = $value4['blok'];
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
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $datatables;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['blok'] = $plotAfd;
                $arrView['pokok'] = $new_pk;
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
                if ($regional == 2 && $estate !== 'NKE') {
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

                $outputArray = [];

                foreach ($plot_kuning as $key => $value) {
                    if ($regional == 2 && $estate == 'NKE') {
                        $newKey = preg_replace('/^P-/', '', $key);

                        // Merge the arrays if the key already exists in the output array
                        if (isset($outputArray[$newKey])) {
                            $outputArray[$newKey] = array_merge($outputArray[$newKey], $value);
                        } else {
                            $outputArray[$newKey] = $value;
                        }
                    } else {
                        // If conditions are not met, keep the original key and value
                        $outputArray[$key] = $value;
                    }
                }
                // dd($drawBlok, $plot_kuning);
                $new_blok = array();

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {
                        $statusCount = 0;
                        $verif = 0;
                        $modifiedKey = '-';
                        if ($regional == 2 && $estate !== 'NKE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else if ($regional == 1 && $estate == 'PLE') {
                            $modifiedKey =  preg_replace('/0/', '', $key, 1);
                        } else {
                            if (strpos($key, 'CBI') !== false && strlen($key) == 9) {
                                $sliced = substr($key, 0, -6);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else if (strpos($key, 'CBI') !== false) {
                                $modifiedKey = substr($key, 0, -4);
                            } else if (strpos($key, 'CB') !== false) {
                                $replace = substr_replace($key, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $modifiedKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $modifiedKey = $key;
                            }
                        }

                        foreach ($outputArray as $key3 => $value3) {

                            // $test = 'N38-CBI';
                            $newKey = '-';
                            if (strpos($key3, 'CBI') !== false) {
                                $parts = explode('-CBI', $key3);
                                $newKey = $parts[0];
                                // dd($newKey);
                            } else if (strpos($key3, 'CB') !== false) {
                                $replace = substr_replace($key3, '', 1, 1);
                                $sliced = substr($replace, 0, -3);
                                $newKey = substr_replace($sliced, '0', 1, 0);
                            } else {
                                $newKey = $key3;
                            }

                            // dd($newKey);
                            if ($modifiedKey === $newKey) {
                                $foundNewKey = $newKey;
                                foreach ($value3 as $key4 => $value4) {
                                    // Calculate jum_blok for the current key
                                    $jumblok = count($value3);
                                    // dd($value4);
                                    // Check if the 'status' is 'Belum'
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }


                                    if ($jumblok >= 1000 && $jumblok < 1000) {
                                        if ($statusCount >= 500) {
                                            $kategori = 'Hijau';
                                            $ket = '1000 : 500';
                                        }
                                    } elseif ($jumblok >= 30 && $jumblok < 100) {
                                        if ($statusCount >= 20) {
                                            $kategori = 'Hijau';
                                            $ket = '100 : 20';
                                        }
                                    } elseif ($jumblok >= 10 && $jumblok <= 30) {
                                        if ($statusCount >= 10) {
                                            $kategori = 'Hijau';
                                            $ket = '11 : 10';
                                        }
                                    } elseif ($jumblok  >= 6 && $jumblok < 10) {
                                        if ($statusCount = 5) {
                                            $kategori = 'Hijau';
                                            $ket = '10 : 10';
                                        }
                                    } elseif ($jumblok  == 1 && $jumblok < 6) {
                                        if ($statusCount >= 5) {
                                            $kategori = 'Hijau';
                                            $ket = '6 : 5';
                                        }
                                    }
                                }

                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }

                                $new_blok[$key]['pokok_namablok'] = $value4['blok'];
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
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }


                // dd($new_blok);

                foreach ($new_blok as $key => $value) {
                    # code...
                    $totalpk = $value['jum_pokok'];
                    $Ditangani = $value['Ditangani'];
                    if ($totalpk != 0) {
                        $progres = round(($Ditangani / $totalpk) * 100, 2);
                    } else {
                        // Handle the case where $totalpk is zero (division by zero error)
                        $progres = 0; // You can set it to zero or handle it differently based on your requirements.
                    }
                }

                $new_pk = array();
                foreach ($plot_kuning as $key => $value) {
                    foreach ($value as $key1 => $value1) {

                        if ($value1['jenis_pupuk_id'] != null) {
                            $pupukx = explode('$', $value1['jenis_pupuk_id']);
                            // dd($pupukx);
                            $pupuk = DB::connection('mysql2')
                                ->table('pupuk')
                                ->select('pupuk.*')
                                ->whereIn('pupuk.id', $pupukx) // Pass the array directly to whereIn
                                ->orderBy('id', 'desc')
                                ->pluck('nama');

                            $pupuk = json_decode(json_encode($pupuk), true); // Convert the result to an array
                            $new_ppk = implode("$", $pupuk);
                            $komnt_ppk = implode(" - ", $pupuk);
                        } else {
                            $new_ppk = null;
                            $komnt_ppk = null;
                        }




                        // dd($new_ppk);
                        $new_pk[$key][$key1]['pupuk'] = $new_ppk;
                        $new_pk[$key][$key1]['ppk_kmn'] = $komnt_ppk;
                        $new_pk[$key][$key1]['lat'] = $value1['lat'];
                        $new_pk[$key][$key1]['lon'] = $value1['lon'];
                        $new_pk[$key][$key1]['blok'] = $value1['blok'];
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                    }
                }
                $values = [];
                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $totalpk;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $progres;
                $arrView['blok'] = $plotBlok;
                $arrView['pokok'] = $new_pk;
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
