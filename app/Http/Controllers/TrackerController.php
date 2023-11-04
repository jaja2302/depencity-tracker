<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Use Http client for Laravel
use Illuminate\Support\Collection; // Import Laravel's Collection class
use App\Models\DeficiencyTracker;
use App\Models\Estate;
use App\Models\Afdeling;

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

        // MRE MASIH BNYAK SALAH 

        $afd = 'MLE';
        // $reg = '2';
        // $est =
        $plotAfd = DB::connection('mysql2')
            ->table('blok')
            ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
            ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->where('estate.est', '=', $afd)
            // ->whereIn('blok.afdeling', ['1', '2', '3', '4'])
            ->orderBy('nama', 'asc')
            ->get();

        $plotAfd = $plotAfd->groupBy(['afd_nama', 'nama']);
        $plotAfd = json_decode($plotAfd, true);
        $plot_kuning = DB::connection('mysql2')
            ->table('deficiency_tracker')
            ->select('deficiency_tracker.afd', 'deficiency_tracker.blok')
            ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
            ->where('deficiency_tracker.est', '=', $afd)
            // ->whereIn('afdeling.ID', ['1'])
            ->orderBy('blok', 'desc')
            ->get();

        $plot_kuning = $plot_kuning->groupBy(['afd', 'blok']);
        $plot_kuning = json_decode($plot_kuning, true);

        // dd($plot_kuning);
        // function 
        // bge ,ple,RDE,SLE,
        // $key = "G0005";
        // $result = substr_replace($key, '', 1, 1);


        // $string = "P-O029";
        // $result = str_replace(["0", "O", "-"], "", $string);
        // dd($result);

        // dd($plotAfd);

        foreach ($plot_kuning as $key => $value) {
            foreach ($value as $key2 => $value3) {

                if (strlen($key2) === 3 && $afd !== 'NBE' && $afd !== 'MRE') {
                    $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'CBI') !== false && $afd !== 'BKE') {
                    $newKey = str_replace("-CBI", "", $key2);
                    $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'T-') !== false  && $afd !== 'MRE') {
                    $newKey = str_replace("T-", "", $key2);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false  && $afd !== 'MRE' && $afd !== 'MLE') {
                    $newKey = str_replace("P-", "", $key2);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'CBI') !== false) {
                    $newKey = str_replace("-CBI", "", $key2);
                    // $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 3 && $afd == 'NBE' && strpos($key2, 'D') !== false && $key !== 'OA' && $key !== 'OB') {
                    $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 3 && $afd == 'MRE') {
                    $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-P') !== false && $afd == 'MRE') {
                    $newKey = str_replace("-P", "0", $key2);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false && $afd == 'MLE') {
                    $keyx = str_replace("P-", "", $key2);
                    $newKey = substr($keyx, 0, 1) . '0' . substr($keyx, 1);
                    unset($plot_kuning[$key][$key2]);
                    $plot_kuning[$key][$newKey] = $value3;
                }
            }
        }

        // dd($plotAfd);
        foreach ($plotAfd as $key => $value) {
            foreach ($value as $key2 => $value3) {

                if (strlen($key2) === 5 && $afd == 'PDE' && strpos($key2, 'A') !== false) {
                    $newKey = str_replace("A", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 5 && $afd == 'PDE' && strpos($key2, 'B') !== false) {
                    $newKey = str_replace("B", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 6 && $afd == 'PDE' && strpos($key2, 'T-A') !== false) {
                    $newKey = str_replace("T-", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 6 && $afd == 'PDE' && strpos($key2, 'T-A') !== false) {
                    $newKey = str_replace("T-", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-N') !== false && $afd == 'SPE'  && $key !== 'OD') {
                    $newKey = str_replace("P-", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $afd !== 'SPE' && $afd !== 'MLE') {
                    $newKey = str_replace("P-", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false && $afd == 'SPE'  && $key == 'OD') {
                    $newKey = str_replace("P-", "", $key2);
                    $newKey = str_replace("A", "", $newKey);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 7 && $afd == 'MLE' && $key == 'OC') {
                    $kexa = str_replace("P-", "", $key2);
                    $newKey = str_replace("B", "", $kexa);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $afd == 'MLE' && $afd !== 'SCE') {
                    $kexa = str_replace("P-", "", $key2);
                    // $newKey = str_replace("B", "", $kexa);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 5 && $afd == 'SCE') {
                    $newKey = str_replace("B", "", $key2);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                } elseif (strlen($key2) === 3 && in_array($afd, ['BDE', 'KTE', 'MKE', 'PKE', 'BHE', 'BSE', 'BWE', 'GDE'])) {
                    $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                    unset($plotAfd[$key][$key2]);
                    $plotAfd[$key][$newKey] = $value3;
                }
            }
        }


        // dd($plot_kuning['OA']);
        // end function 
        // dd($plot_kuning);

        $pk_kuning = [];
        foreach ($plot_kuning as $key => $value) {
            $secondLevelKeys = array_keys($value);
            $pk_kuning[$key] = $secondLevelKeys;
        }

        $blok_afd = [];
        foreach ($plotAfd as $key => $value) {
            $secondLevelKeys = array_keys($value);
            $blok_afd[$key] = $secondLevelKeys;
        }

        function findNestedArrayDifferences($array1, $array2)
        {
            $result = [];

            // Get unique keys from both arrays
            $allKeys = array_unique(array_merge(array_keys($array1), array_keys($array2)));

            foreach ($allKeys as $key) {
                $nestedArray1 = $array1[$key] ?? [];
                $nestedArray2 = $array2[$key] ?? [];

                $same = array_intersect($nestedArray1, $nestedArray2);
                $difference1 = array_diff($nestedArray1, $nestedArray2);
                $difference2 = array_diff($nestedArray2, $nestedArray1);

                $result[$key] = [
                    'same' => $same,
                    'pokok_beda' => $difference1,
                    'blok_beda' => $difference2,
                ];
            }

            return $result;
        }


        $result = findNestedArrayDifferences($pk_kuning, $blok_afd);
        // dd($blok_afd, $pk_kuning);
        // dd($result);

        // $datatables = json_decode($datatables, true);


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
                    $new_blok[$key]['Belum'] = $jumblok - $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                foreach ($new_blok as $key => $value) {
                    $ktg[] = $key;
                    $sudah[] = $value['Ditangani'];
                    $belum[] = $value['Belum'];
                    $jum_pokok[] = $value['jum_pokok'];
                }

                $arrView['ktg_pk'] = $ktg;
                $arrView['jum_pokok'] = $jum_pokok;
                $arrView['ditangani_pk'] = $sudah;
                $arrView['belum_pk'] = $belum;


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

                $final_ppk = [
                    'rekom1' => '-',
                    'realisasi1' => '-',
                    'progress1' => '-',
                    'rekom2' => '-',
                    'realisasi2' => '-',
                    'progress2' => '-',
                    'status_1' => '-',
                    'status_2' => '-',
                    'status_3' => '-',
                    'status_4' => '-',
                    'status_5' => '-',
                    'status_6' => '-',
                    'status_7' => '-',
                    'status_8' => '-',
                    'status_9' => '-',
                    'status_10' => '-',
                    'status_11' => '-',
                    'status_12' => '-',
                    'status_13' => '-',
                    'status_14' => '-',
                    'status_15' => '-',
                    'status_16' => '-',
                ];
                $arrView['pemupukan'] = $final_ppk;
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

                // foreach ($plot_kuning as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         // dd($value3);
                //         $afd = $value3['afd'];
                //         if (strlen($key) === 3 && $estate !== 'NBE' && $estate !== 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false && $estate !== 'BKE') {
                //             $newKey = str_replace("-CBI", "", $key);
                //             $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'T-') !== false  && $estate !== 'MRE') {
                //             $newKey = str_replace("T-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false  && $estate !== 'MRE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false) {
                //             $newKey = str_replace("-CBI", "", $key);
                //             // $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'NBE' && strpos($key, 'D') !== false && $afd !== 'OA' && $afd !== 'OB') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-P') !== false && $estate == 'MRE') {
                //             $newKey = str_replace("-P", "0", $key);
                //             unset($plot_kuning[$key][$key]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false && $estate == 'MLE') {
                //             $keyx = str_replace("P-", "", $key);
                //             $newKey = substr($keyx, 0, 1) . '0' . substr($keyx, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } else {
                //             $plot_kuning[$key][$key2] = $value3;
                //         }
                //     }
                // }

                // // dd($plot_kuning);
                // $filteredArray = [];

                // foreach ($plot_kuning as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredArray[$key] = $value;
                //     }
                // }

                // foreach ($drawBlok as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         $afd = $value3['afd_nama'];
                //         if (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'A') !== false) {
                //             $newKey = str_replace("A", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'B') !== false) {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-N') !== false && $estate == 'SPE'  && $afd !== 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate !== 'SPE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && $estate == 'SPE'  && $afd == 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             $newKey = str_replace("A", "", $newKey);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 7 && $estate == 'MLE' && $afd == 'OC') {
                //             $kexa = str_replace("P-", "", $key2);
                //             $newKey = str_replace("B", "", $kexa);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate == 'MLE' && $estate !== 'SCE') {
                //             $kexa = str_replace("P-", "", $key2);
                //             // $newKey = str_replace("B", "", $kexa);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'SCE') {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 3 && in_array($estate, ['BDE', 'KTE', 'MKE', 'PKE', 'BHE', 'BSE', 'BWE', 'GDE'])) {
                //             $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } else {
                //             $drawBlok[$key][$key2] = $value3;
                //         }
                //     }
                // }

                // $filteredBlok = [];

                // foreach ($drawBlok as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredBlok[$key] = $value;
                //     }
                // }
                // dd($filteredBlok, $filteredArray);

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {

                        $verif = 0;
                        $statusCount = 0;

                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                $jumblok = count($value3);
                                foreach ($value3 as $key4 => $value4) if ($value4['afd'] == $value2['afd_nama']) {
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }
                                    // Collect lat and lon values

                                }
                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    if ($jumblok >= 1000 && $jumblok < 10000) {
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
                    } elseif ($jumblok >= 6 && $jumblok < 10) {
                        if ($statusCount == 5) {
                            $kategori = 'Hijau';
                            $ket = '10 : 10';
                        }
                    } elseif ($jumblok > 1 && $jumblok < 6) {
                        if ($statusCount >= 5) {
                            $kategori = 'Hijau';
                            $ket = '6 : 5';
                        }
                    }
                    if (empty($lat_lon)) {
                        foreach ($value as $item) {
                            if (isset($item['lat']) && isset($item['lon'])) {
                                $lat = $item['lat'];
                                $lon = $item['lon'];
                                $lat_lon[] = $lat . ';' . $lon;
                            }
                        }
                    }

                    // Rest of your code

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['afdeling'] = $value2['afd_nama'];
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['Belum'] = $jumblok - $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }
                // dd($new_blok);
                $graph = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->where('deficiency_tracker.est', '=', $estate)
                    // ->whereNotIn('id', [353])
                    ->orderBy('id', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $graph = $graph->groupBy(['afd']);
                $graph = json_decode($graph, true);

                $new_graph = array();
                foreach ($graph as $key => $value) {
                    $statusCount = 0;
                    $verif = 0;
                    foreach ($value as $key2 => $value2) {
                        // dd($value2);
                        $jumblok = count($value);
                        // dd($value4);
                        // Check if the 'status' is 'Belum'
                        if (isset($value2['status']) && $value2['status'] == 'Sudah') {
                            $statusCount++;
                        }
                    }
                    $new_graph[$key]['jum'] = $jumblok;
                    $new_graph[$key]['Ditangani'] = $statusCount;
                    $new_graph[$key]['Belum'] = $jumblok - $statusCount;
                }

                // dd($new_graph);


                foreach ($new_graph as $key => $value) {
                    $ktg[] = $key;
                    $sudah[] = $value['Ditangani'];
                    $belum[] = $value['Belum'];
                    $jum_pokok[] = $value['jum'];
                }
                // dd($sudah);
                $arrView['ktg_pk'] = $ktg;
                $arrView['ditangani_pk'] = $sudah;
                $arrView['belum_pk'] = $belum;
                $arrView['jum_pokok'] = $jum_pokok;


                // dd($filteredArray);
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
                        $new_pk[$key][$key1]['blok'] = $key;
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                        $new_pk[$key][$key1]['afd'] = $value1['afd'];
                    }
                }
                $final_ppk = [
                    'rekom1' => '-',
                    'realisasi1' => '-',
                    'progress1' => '-',
                    'rekom2' => '-',
                    'realisasi2' => '-',
                    'progress2' => '-',
                    'status_1' => '-',
                    'status_2' => '-',
                    'status_3' => '-',
                    'status_4' => '-',
                    'status_5' => '-',
                    'status_6' => '-',
                    'status_7' => '-',
                    'status_8' => '-',
                    'status_9' => '-',
                    'status_10' => '-',
                    'status_11' => '-',
                    'status_12' => '-',
                    'status_13' => '-',
                    'status_14' => '-',
                    'status_15' => '-',
                    'status_16' => '-',
                ];
                $arrView['pemupukan'] = $final_ppk;

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['pokok'] = $new_pk;

                break;

            case 'afdeling':
                // dd($plotAfd);


                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)
                    // ->whereNotIn('id', [353])
                    ->orderBy('blok', 'desc') // Sort by 'id' column in descending order
                    ->get();

                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);
                // dd($plot_kuning['G03']);

                // dd($plot_kuning);

                // dd($estate);
                // foreach ($plot_kuning as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         // dd($value3);
                //         $afd = $value3['afd'];
                //         if (strlen($key) === 3 && $estate !== 'NBE' && $estate !== 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false && $estate !== 'BKE') {
                //             $newKey = str_replace("-CBI", "", $key);
                //             $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'T-') !== false  && $estate !== 'MRE') {
                //             $newKey = str_replace("T-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false  && $estate !== 'MRE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false) {
                //             $newKey = str_replace("-CBI", "", $key);
                //             // $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'NBE' && strpos($key, 'D') !== false && $afd !== 'OA' && $afd !== 'OB') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-P') !== false && $estate == 'MRE') {
                //             $newKey = str_replace("-P", "0", $key);
                //             unset($plot_kuning[$key][$key]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false && $estate == 'MLE') {
                //             $keyx = str_replace("P-", "", $key);
                //             $newKey = substr($keyx, 0, 1) . '0' . substr($keyx, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } else {
                //             $plot_kuning[$key][$key2] = $value3;
                //         }
                //     }
                // }

                // // dd($plot_kuning);
                // $filteredArray = [];

                // foreach ($plot_kuning as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredArray[$key] = $value;
                //     }
                // }

                // dd($filteredArray);
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
                        $new_pk[$key][$key1]['blok'] = $key;
                        $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                        $new_pk[$key][$key1]['status'] = $value1['status'];
                        $new_pk[$key][$key1]['foto'] = $value1['foto'];
                        $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                        $new_pk[$key][$key1]['id'] = $value1['id'];
                        $new_pk[$key][$key1]['afd'] = $value1['afd'];
                    }
                }

                // dd($new_pk);

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
                // dd($drawBlok, $outputArray);

                // foreach ($drawBlok as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         $afd = $value3['afd_nama'];
                //         if (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'A') !== false) {
                //             $newKey = str_replace("A", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'B') !== false) {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-N') !== false && $estate == 'SPE'  && $afd !== 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate !== 'SPE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && $estate == 'SPE'  && $afd == 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             $newKey = str_replace("A", "", $newKey);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 7 && $estate == 'MLE' && $afd == 'OC') {
                //             $kexa = str_replace("P-", "", $key2);
                //             $newKey = str_replace("B", "", $kexa);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate == 'MLE' && $estate !== 'SCE') {
                //             $kexa = str_replace("P-", "", $key2);
                //             // $newKey = str_replace("B", "", $kexa);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'SCE') {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 3 && in_array($estate, ['BDE', 'KTE', 'MKE', 'PKE', 'BHE', 'BSE', 'BWE', 'GDE'])) {
                //             $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                //             unset($drawBlok[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } else {
                //             $drawBlok[$key][$key2] = $value3;
                //         }
                //     }
                // }

                // $filteredBlok = [];

                // foreach ($drawBlok as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredBlok[$key] = $value;
                //     }
                // }
                // dd($filteredBlok, $filteredArray['G003']);

                $new_blok = array();

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {

                        $verif = 0;
                        $statusCount = 0;

                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                $jumblok = count($value3);
                                foreach ($value3 as $key4 => $value4) {
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }
                                    // Collect lat and lon values

                                }
                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    if ($jumblok >= 1000 && $jumblok < 10000) {
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
                    } elseif ($jumblok >= 6 && $jumblok < 10) {
                        if ($statusCount == 5) {
                            $kategori = 'Hijau';
                            $ket = '10 : 10';
                        }
                    } elseif ($jumblok > 1 && $jumblok < 6) {
                        if ($statusCount >= 5) {
                            $kategori = 'Hijau';
                            $ket = '6 : 5';
                        }
                    }
                    if (empty($lat_lon)) {
                        foreach ($value as $item) {
                            if (isset($item['lat']) && isset($item['lon'])) {
                                $lat = $item['lat'];
                                $lon = $item['lon'];
                                $lat_lon[] = $lat . ';' . $lon;
                            }
                        }
                    }

                    // Rest of your code

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['Belum'] = $jumblok - $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                // dd($new_blok);
                foreach ($new_blok as $key => $value) {
                    $ktg[] = $key;
                    $sudah[] = $value['Ditangani'];
                    $belum[] = $value['Belum'];
                    $jum_pokok[] = $value['jum_pokok'];
                }

                $final_ppk = [
                    'rekom1' => '-',
                    'realisasi1' => '-',
                    'progress1' => '-',
                    'rekom2' => '-',
                    'realisasi2' => '-',
                    'progress2' => '-',
                    'status_1' => '-',
                    'status_2' => '-',
                    'status_3' => '-',
                    'status_4' => '-',
                    'status_5' => '-',
                    'status_6' => '-',
                    'status_7' => '-',
                    'status_8' => '-',
                    'status_9' => '-',
                    'status_10' => '-',
                    'status_11' => '-',
                    'status_12' => '-',
                    'status_13' => '-',
                    'status_14' => '-',
                    'status_15' => '-',
                    'status_16' => '-',
                ];

                // dd($new_blok);
                $arrView['pemupukan'] = $final_ppk;
                $arrView['ktg_pk'] = $ktg;
                $arrView['jum_pokok'] = $jum_pokok;
                $arrView['ditangani_pk'] = $sudah;
                $arrView['belum_pk'] = $belum;

                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $datatables;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $count;
                $arrView['total_ditangani'] = $count_sudah;
                $arrView['persen_ditangani'] = $percentage_sudah;
                $arrView['blok'] = [];
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

                // dd($afdeling);

                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)
                    ->orderBy('id', 'desc')
                    ->get();


                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);
                // dd($plot_kuning);

                // foreach ($plot_kuning as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         // dd($value3);
                //         $afd = $value3['afd'];
                //         if (strlen($key) === 3 && $estate !== 'NBE' && $estate !== 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false && $estate !== 'BKE') {
                //             $newKey = str_replace("-CBI", "", $key);
                //             $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'T-') !== false  && $estate !== 'MRE') {
                //             $newKey = str_replace("T-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false  && $estate !== 'MRE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false) {
                //             $newKey = str_replace("-CBI", "", $key);
                //             // $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'NBE' && strpos($key, 'D') !== false && $afd !== 'OA' && $afd !== 'OB') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-P') !== false && $estate == 'MRE') {
                //             $newKey = str_replace("-P", "0", $key);
                //             unset($plot_kuning[$key][$key]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false && $estate == 'MLE') {
                //             $keyx = str_replace("P-", "", $key);
                //             $newKey = substr($keyx, 0, 1) . '0' . substr($keyx, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } else {
                //             $plot_kuning[$key][$key2] = $value3;
                //         }
                //     }
                // }
                // $filteredArray = [];



                // foreach ($plot_kuning as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredArray[$key] = $value;
                //     }
                // }


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

                // dd($plot_kuning);

                // foreach ($drawBlok as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         $afd = $value3['afd_nama'];
                //         if (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'A') !== false) {
                //             $newKey = str_replace("A", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'B') !== false) {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-N') !== false && $estate == 'SPE'  && $afd !== 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate !== 'SPE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && $estate == 'SPE'  && $afd == 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             $newKey = str_replace("A", "", $newKey);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 7 && $estate == 'MLE' && $afd == 'OC') {
                //             $kexa = str_replace("P-", "", $key2);
                //             $newKey = str_replace("B", "", $kexa);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate == 'MLE' && $estate !== 'SCE') {
                //             $kexa = str_replace("P-", "", $key2);
                //             // $newKey = str_replace("B", "", $kexa);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'SCE') {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 3 && in_array($estate, ['BDE', 'KTE', 'MKE', 'PKE', 'BHE', 'BSE', 'BWE', 'GDE'])) {
                //             $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } else {
                //             $drawBlok[$key][$key2] = $value3;
                //         }
                //     }
                // }


                // $filteredBlok = [];

                // foreach ($drawBlok as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredBlok[$key] = $value;
                //     }
                // }

                asort($plot_kuning);
                $new_blok = array();

                // dd($filteredBlok, $filteredArray['G003']);

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    $jumblok = 0; // Initialize jumblok to 0
                    $kategori = 'Blue'; // Initialize kategori as 'Blue' by default
                    $ket = '-'; // Initialize kategori as 'Blue' by default

                    foreach ($value as $key2 => $value2) {

                        $verif = 0;
                        $statusCount = 0;

                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                $jumblok = count($value3);
                                foreach ($value3 as $key4 => $value4) {
                                    if (isset($value4['status']) && $value4['status'] == 'Sudah') {
                                        $statusCount++;
                                    }
                                    if (isset($value4['status']) && $value4['status'] == 'Terverifikasi') {
                                        $verif++;
                                    }
                                    // Collect lat and lon values

                                }
                                if (isset($value2['lat']) && isset($value2['lon'])) {
                                    $lat = $value2['lat'];
                                    $lon = $value2['lon'];
                                    $lat_lon[] = $lat . ';' . $lon;
                                }
                            }
                        }
                    }

                    if ($jumblok >= 1000 && $jumblok < 10000) {
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
                    } elseif ($jumblok >= 6 && $jumblok < 10) {
                        if ($statusCount == 5) {
                            $kategori = 'Hijau';
                            $ket = '10 : 10';
                        }
                    } elseif ($jumblok > 1 && $jumblok < 6) {
                        if ($statusCount >= 5) {
                            $kategori = 'Hijau';
                            $ket = '6 : 5';
                        }
                    }

                    // Rest of your code

                    $new_blok[$key]['jum_pokok'] = $jumblok;
                    $new_blok[$key]['afd_nama'] = $key;
                    $new_blok[$key]['Diverif'] = $verif;
                    $new_blok[$key]['kategori'] = $kategori;
                    $new_blok[$key]['Ket'] = $ket;
                    $new_blok[$key]['Ditangani'] = $statusCount;
                    $new_blok[$key]['Belum'] = $jumblok - $statusCount;
                    $new_blok[$key]['lat_lon'] = $lat_lon;
                }

                // Your code continues here
                // dd($new_blok);

                foreach ($new_blok as $key => $value) {
                    $ktg[] = $key;
                    $sudah[] = $value['Ditangani'];
                    $belum[] = $value['Belum'];
                    $jum_pokok[] = $value['jum_pokok'];
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
                // dd($totalpk);

                $new_pk = array();
                foreach ($plot_kuning as $key => $value) {
                    foreach ($new_blok as $key2 => $value2) if ($key == $key2) {
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
                            $new_pk[$key][$key1]['blok'] = $key;
                            $new_pk[$key][$key1]['afd'] = $value1['afd'];
                            $new_pk[$key][$key1]['kondisi'] = $value1['kondisi'];
                            $new_pk[$key][$key1]['status'] = $value1['status'];
                            $new_pk[$key][$key1]['foto'] = $value1['foto'];
                            $new_pk[$key][$key1]['komentar'] = $value1['komentar'];
                            $new_pk[$key][$key1]['id'] = $value1['id'];
                            $new_pk[$key][$key1]['afd'] = $value1['afd'];
                        }
                    }
                }

                // dd($new_pk);


                if ($regional == 1) {
                    $fileName = 'Jqayb4aORkQvs2oEa.KdQ';
                } elseif ($regional == 2) {
                    $fileName = 'cHP0n+ocSaJ9xIIK9E.SCQ';
                } elseif ($regional == 3) {
                    $fileName = 'rT82oE5YsbSSC9tv+AX.iGQ';
                } else {
                    $fileName = 'default.SCQ';
                }

                $filePath = public_path('json/' . $fileName);

                $contents = file_get_contents($filePath);
                // dd($contents);
                $contents = ltrim($contents, "\xEF\xBB\xBF");

                // dd($contents);
                $phpArray = json_decode($contents, true);



                $pupuk_kn = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('blok.nama', '=', $blok)

                    ->orderBy('id', 'desc')
                    ->get();

                $pupuk_kn = $pupuk_kn->groupBy(['nama', 'afd_nama']);
                $pupuk_kn = json_decode($pupuk_kn, true);
                // dd($pupuk_kn);
                $newArr = [];
                if ($phpArray === null) {
                    $newArr = null;
                    $dataLsuArr = null;
                } else {
                    // Convert the PHP array to a Laravel Collection
                    $collection = collect($phpArray);

                    $filteredCollection = $collection->only('Blok');

                    // $new_key 

                    $afdeling = '-';
                    foreach ($filteredCollection['Blok'] as $item) {
                        $newKey = $item[7];
                        $newArr[$newKey][$item[6]] = $item;
                    }

                    $dataLsu = $collection->only('DataLabelBlok');

                    // $new_key 


                    foreach ($dataLsu['DataLabelBlok'] as $item) {
                        // dd($item);
                        // dd($item);
                        $newKey = $item['Tahun LSU'][0];
                        $newKey2 = $item['Tahun LSU Sebelum'][0];
                        $newKey3 = $item['Update'][0];
                        $newKey4 = $item['Bulan Update'][0];
                        // dd($newKey);
                        $dataLsuArr['tahun_mulai'] = $newKey;
                        $dataLsuArr['tahun_sebelum'] = $newKey2;
                        $dataLsuArr['progres'] = $newKey3;
                        $dataLsuArr['realisasi'] = $newKey4;
                    }

                    // dd($newArr);
                }
                // dd($dataLsuArr);
                // dd($pupuk_kn);
                function checkValue($value)
                {
                    if ($value == 0) {
                        return 'Defisiensi';
                    } elseif ($value == 1) {
                        return 'Low';
                    } elseif ($value == 2) {
                        return 'Optimum';
                    } elseif ($value == 3) {
                        return 'High';
                    } elseif ($value == 4) {
                        return 'Exceed';
                    } elseif ($value == 5) {
                        return 'Suff';
                    } else {
                        return '-';
                    }
                }

                // dd($newArr['P-S20'], $pupuk_kn);

                // dd($pupuk_kn);
                $data_pupuk = array();
                foreach ($pupuk_kn as $key => $value) {
                    foreach ($value as $key1 => $value1) {
                        $data_pupuk['rekom1'] = '-';
                        $data_pupuk['realisasi1'] = '-';
                        $data_pupuk['progress1'] = '-';
                        $data_pupuk['rekom2'] = '-';
                        $data_pupuk['realisasi2'] = '-';
                        $data_pupuk['progress2'] = '-';

                        $data_pupuk['status_1'] = '-';
                        $data_pupuk['status_2'] = '-';
                        $data_pupuk['status_3'] = '-';
                        $data_pupuk['status_4'] = '-';
                        $data_pupuk['status_5'] = '-';
                        $data_pupuk['status_6'] = '-';
                        $data_pupuk['status_7'] = '-';
                        $data_pupuk['status_8'] = '-';
                        $data_pupuk['status_9'] = '-';
                        $data_pupuk['status_10'] = '-';
                        $data_pupuk['status_11'] = '-';
                        $data_pupuk['status_12'] = '-';
                        $data_pupuk['status_13'] = '-';
                        $data_pupuk['status_14'] = '-';
                        $data_pupuk['status_15'] = '-';
                        $data_pupuk['status_16'] = '-';
                        foreach ($newArr as $key2 => $value2) {
                            if ($estate == 'MRE') {
                                $modifiedKey = str_replace('0', '', $key);
                                // dd($modifiedKey);
                                if (strpos($modifiedKey, 'P-') !== false) {
                                    $modifiedKey = str_replace('P-', '', $modifiedKey);
                                }
                            } else {
                                $modifiedKey = $key;
                            }
                            // dd($modifiedKey);


                            if ($modifiedKey == $key2) {
                                foreach ($value2 as $key3 => $value3) {
                                    if ($key1 == $key3) {
                                        $data_pupuk['rekom1'] = $value3[98];
                                        $data_pupuk['realisasi1'] = $value3[99];
                                        $data_pupuk['progress1'] = $value3[100];
                                        $data_pupuk['rekom2'] = $value3[126];
                                        $data_pupuk['realisasi2'] = $value3[127];
                                        $data_pupuk['progress2'] = $value3[128];

                                        $data_pupuk['status_1'] = checkValue($value3[101]);
                                        $data_pupuk['status_2'] = checkValue($value3[102]);
                                        $data_pupuk['status_3'] = checkValue($value3[104]);
                                        $data_pupuk['status_4'] = checkValue($value3[103]);
                                        $data_pupuk['status_5'] = checkValue($value3[106]);
                                        $data_pupuk['status_6'] = checkValue($value3[105]);
                                        $data_pupuk['status_7'] = checkValue($value3[111]);
                                        $data_pupuk['status_8'] = checkValue($value3[112]);
                                        $data_pupuk['status_9'] = checkValue($value3[109]);
                                        $data_pupuk['status_10'] = checkValue($value3[110]);
                                        $data_pupuk['status_11'] = checkValue($value3[108]);
                                        $data_pupuk['status_12'] = checkValue($value3[107]);
                                        $data_pupuk['status_13'] = checkValue($value3[114]);
                                        $data_pupuk['status_14'] = checkValue($value3[113]);
                                        $data_pupuk['status_15'] = checkValue($value3[116]);
                                        $data_pupuk['status_16'] = checkValue($value3[115]);
                                        $data_pupuk['tahun_mulai'] = $dataLsuArr['tahun_mulai'];
                                        $data_pupuk['tahun_belum'] = $dataLsuArr['tahun_sebelum'];
                                        $data_pupuk['tahun_progres'] = $dataLsuArr['progres'];
                                        $data_pupuk['tahun_realisasi'] = $dataLsuArr['realisasi'];
                                    }
                                }
                            }
                        }
                    }
                }

                // dd($data_pupuk);

                $final_ppk = $data_pupuk ?? [];

                if (empty($final_ppk)) {
                    $final_ppk = [
                        'rekom1' => '-',
                        'realisasi1' => '-',
                        'progress1' => '-',
                        'rekom2' => '-',
                        'realisasi2' => '-',
                        'progress2' => '-',
                        'status_1' => '-',
                        'status_2' => '-',
                        'status_3' => '-',
                        'status_4' => '-',
                        'status_5' => '-',
                        'status_6' => '-',
                        'status_7' => '-',
                        'status_8' => '-',
                        'status_9' => '-',
                        'status_10' => '-',
                        'status_11' => '-',
                        'status_12' => '-',
                        'status_13' => '-',
                        'status_14' => '-',
                        'status_15' => '-',
                        'status_16' => '-',
                        'tahun_mulai' =>  '-',
                        'tahun_belum' =>  '-',
                        'tahun_progres' =>  '-',
                        'tahun_realisasi' =>  '-',
                    ];
                }
                // dd($new_blok);

                // dd($new_blok['jum_pokok']);
                $jum_pkok = [];
                foreach ($new_blok as $key => $value) {
                    # code...
                    // dd($value);
                    $jum_pkok[] = $value['jum_pokok'];
                    $ditangani[] = $value['Ditangani'];
                }

                // dd($jum_pkok);
                $arrView['pemupukan'] = $final_ppk;
                $values = [];
                $arrView['ktg_pk'] = $ktg;
                $arrView['jum_pokok'] = $jum_pokok;
                $arrView['ditangani_pk'] = $sudah;
                $arrView['belum_pk'] = $belum;
                $arrView['new_blok'] = $new_blok;
                $arrView['datatables'] = $values;
                $arrView['drawBlok'] = $drawBlok;
                $arrView['total_pokok'] = $jum_pkok;
                $arrView['total_ditangani'] = $ditangani;
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

    public function getData(Request $request)
    {

        $regional = $request->input('regional');
        $estate = $request->input('estate');
        $afdeling = $request->input('afdeling');
        $blok = $request->input('blok');
        $dataType = $request->input('dataType'); // Retrieve the dataType

        $arrView = array();
        // dd($regional, $dataType);
        switch ($dataType) {
            case 'regional':

                $arrView['datatables'] = [];
                break;
            case 'estate':
                $data_est = Estate::where('est', $estate)->with('dtracker_est')->first();

                // dd($estate);
                $dtracker_est = $data_est->dtracker_est;
                $dtracker_est = json_decode($dtracker_est, true);

                $arrView['datatables'] = $dtracker_est;
                break;
            case 'afdeling':
                // dd($afdeling);

                // $datatables = DB::connection('mysql2')
                //     ->table('deficiency_tracker')
                //     ->select('deficiency_tracker.*')
                //     ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                //     ->where('deficiency_tracker.est', '=', 'KNE')
                //     ->where('afdeling.id', '=', 1)
                //     ->orderBy('blok', 'desc') // Sort by 'id' column in descending order
                //     ->get();

                // // $datatables = $datatables->groupBy(['blok']);
                // $datatables = json_decode($datatables, true);

                // dd($datatables);

                // dd($plot_kuning['G03']);

                $datatables = DeficiencyTracker::join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', $estate)
                    ->where('afdeling.id', $afdeling)
                    ->orderBy('deficiency_tracker.blok', 'desc')
                    ->get()
                    ->toArray();

                // dd($datatables);

                $arrView['datatables'] = $datatables;
                break;
            case 'blok':
                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->join('afdeling', 'afdeling.nama', '=', 'deficiency_tracker.afd')
                    ->where('deficiency_tracker.est', '=', $estate)
                    ->where('afdeling.id', '=', $afdeling)
                    ->orderBy('id', 'desc')
                    ->get();


                $plot_kuning = $plot_kuning->groupBy(['blok']);
                $plot_kuning = json_decode($plot_kuning, true);
                // dd($plot_kuning);

                // foreach ($plot_kuning as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         // dd($value3);
                //         $afd = $value3['afd'];
                //         if (strlen($key) === 3 && $estate !== 'NBE' && $estate !== 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false && $estate !== 'BKE') {
                //             $newKey = str_replace("-CBI", "", $key);
                //             $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'T-') !== false  && $estate !== 'MRE') {
                //             $newKey = str_replace("T-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false  && $estate !== 'MRE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'CBI') !== false) {
                //             $newKey = str_replace("-CBI", "", $key);
                //             // $newKey = substr($newKey, 0, 1) . '0' . substr($newKey, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'NBE' && strpos($key, 'D') !== false && $afd !== 'OA' && $afd !== 'OB') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strlen($key) === 3 && $estate == 'MRE') {
                //             $newKey = substr($key, 0, 1) . '0' . substr($key, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-P') !== false && $estate == 'MRE') {
                //             $newKey = str_replace("-P", "0", $key);
                //             unset($plot_kuning[$key][$key]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } elseif (strpos($key, 'P-') !== false && $estate == 'MLE') {
                //             $keyx = str_replace("P-", "", $key);
                //             $newKey = substr($keyx, 0, 1) . '0' . substr($keyx, 1);
                //             unset($plot_kuning[$key][$key2]);
                //             $plot_kuning[$newKey][$key2] = $value3;
                //         } else {
                //             $plot_kuning[$key][$key2] = $value3;
                //         }
                //     }
                // }
                // $filteredArray = [];



                // foreach ($plot_kuning as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredArray[$key] = $value;
                //     }
                // }

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

                // dd($drawBlok, $plot_kuning);

                // foreach ($drawBlok as $key => $value) {
                //     foreach ($value as $key2 => $value3) {
                //         $afd = $value3['afd_nama'];
                //         if (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'A') !== false) {
                //             $newKey = str_replace("A", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'PDE' && strpos($key2, 'B') !== false) {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 6 && $estate == 'PDE' && strpos($key2, 'T-A') !== false) {
                //             $newKey = str_replace("T-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-N') !== false && $estate == 'SPE'  && $afd !== 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate !== 'SPE' && $estate !== 'MLE') {
                //             $newKey = str_replace("P-", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && $estate == 'SPE'  && $afd == 'OD') {
                //             $newKey = str_replace("P-", "", $key2);
                //             $newKey = str_replace("A", "", $newKey);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 7 && $estate == 'MLE' && $afd == 'OC') {
                //             $kexa = str_replace("P-", "", $key2);
                //             $newKey = str_replace("B", "", $kexa);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strpos($key2, 'P-') !== false && strlen($key2) === 6 && $estate == 'MLE' && $estate !== 'SCE') {
                //             $kexa = str_replace("P-", "", $key2);
                //             // $newKey = str_replace("B", "", $kexa);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 5 && $estate == 'SCE') {
                //             $newKey = str_replace("B", "", $key2);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } elseif (strlen($key2) === 3 && in_array($estate, ['BDE', 'KTE', 'MKE', 'PKE', 'BHE', 'BSE', 'BWE', 'GDE'])) {
                //             $newKey = substr($key2, 0, 1) . '0' . substr($key2, 1);
                //             unset($plotAfd[$key][$key2]);
                //             $drawBlok[$key][$newKey] = $value3;
                //         } else {
                //             $drawBlok[$key][$key2] = $value3;
                //         }
                //     }
                // }


                // $filteredBlok = [];

                // foreach ($drawBlok as $key => $value) {
                //     if (!empty($value)) {
                //         // Add non-empty arrays to the filtered array
                //         $filteredBlok[$key] = $value;
                //     }
                // }

                asort($plot_kuning);
                $new_blok = array();

                // dd($filteredBlok, $filteredArray['G003']);

                foreach ($drawBlok as $key => $value) {
                    $lat_lon = array(); // Initialize lat_lon as an empty array
                    foreach ($value as $key2 => $value2) {
                        foreach ($plot_kuning as $key3 => $value3) {
                            if ($key === $key3) {
                                foreach ($value3 as $key4 => $value4) {

                                    $new_blok[0]['id'] = $value4['id'];
                                    $new_blok[0]['est'] = $value4['est'];
                                    $new_blok[0]['afd'] = $value4['afd'];
                                    $new_blok[0]['blok'] = $value4['blok'];
                                    $new_blok[0]['kondisi'] = $value4['kondisi'];
                                    $new_blok[0]['status'] = $value4['status'];
                                }
                            }
                        }
                    }
                }

                // dd($new_blok);
                // dd($filteredBlok);
                $arrView['datatables'] = $new_blok;
                break;
            default:
                # code...
                break;
        }

        echo json_encode($arrView);
        exit();
    }
}
