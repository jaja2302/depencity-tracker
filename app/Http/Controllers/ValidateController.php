<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidateController extends Controller
{
    public function processSynchronize(Request $request)
    {
        function isPointInPolygon($point, $polygon)
        {
            $splPoint = explode(',', $point);
            $x = $splPoint[0];
            $y = $splPoint[1];

            $vertices = array_map(function ($vertex) {
                return explode(',', $vertex);
            }, explode('$', $polygon));

            $numVertices = count($vertices);
            $isInside = false;

            for ($i = 0, $j = $numVertices - 1; $i < $numVertices; $j = $i++) {
                $xi = $vertices[$i][0];
                $yi = $vertices[$i][1];
                $xj = $vertices[$j][0];
                $yj = $vertices[$j][1];

                if (($yi < $y && $yj >= $y || $yj < $y && $yi >= $y) && ($xi + ($y - $yi) / ($yj - $yi) * ($xj - $xi) < $x)) {
                    $isInside = !$isInside;
                }
            }

            return $isInside;
        }

        $polygon = "-2.265517895,111.6618414$-2.268859042,111.6617089$-2.268085126,111.6525868$-2.265298213,111.6526788$-2.265517895,111.6618414";
        $point = "-2.26664281015,111.654028622";
        $test = isPointInPolygon($point, $polygon);
        dd($test);

        $reg = $request->input('regVal');
        $est = $request->input('estVal');

        $estateQuery = DB::connection('mysql2')->table('estate')
            ->select('*')
            ->join('afdeling', 'afdeling.estate', '=', 'estate.id')
            ->where('estate.est', $est)
            ->get();
        $estateQuery = json_decode($estateQuery, true);

        $listIdAfd = array();
        foreach ($estateQuery as $key => $value) {
            $listIdAfd[] = $value['id'];
        }

        $blokEstate = DB::connection('mysql2')->table('blok')
            ->select(DB::raw('DISTINCT nama, MIN(id) as id, afdeling'))
            ->whereIn('afdeling', $listIdAfd)
            ->groupBy('nama', 'afdeling')
            ->get();
        $blokEstate = json_decode($blokEstate, true);

        $blokEstateFix = array();
        foreach ($blokEstate as $key => $value) {
            $blokEstateFix[$value['afdeling']][] = $value['nama'];
        }

        $qrAfd = DB::connection('mysql2')->table('afdeling')
            ->select('*')
            ->get();
        $qrAfd = json_decode($qrAfd, true);

        $blokEstNewFix = array();
        foreach ($blokEstateFix as $key => $value) {
            foreach ($qrAfd as $key1 => $value1) {
                if ($value1['id'] == $key) {
                    $afdelingNama = $value1['nama'];
                }
            }
            $blokEstNewFix[$afdelingNama] = $value;
        }

        $queryBlok = DB::connection('mysql2')->table('blok')
            ->select('*')
            ->whereIn('afdeling', $listIdAfd)
            ->get();
        $queryBlok = json_decode($queryBlok, true);

        $blokLatLn = array();
        $inc = 0;
        foreach ($blokEstNewFix as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $latln = '';
                foreach ($queryBlok as $key3 => $value4) {
                    if ($value4['nama'] == $value1) {
                        $latln .= $value4['lat'] . ',' . $value4['lon'] . '$';
                    }
                }

                $blokLatLn[$inc]['afd'] = $key;
                $blokLatLn[$inc]['blok'] = $value1;
                $blokLatLn[$inc]['latln'] = rtrim($latln, '$');
                $inc++;
            }
        }

        $dtQuery = DB::connection('mysql2')->table('deficiency_tracker')
            ->select('*')
            ->where('est', $est)
            ->get();
        $dtQuery = json_decode($dtQuery, true);

        $pkLatLn = array();
        $incr = 0;
        foreach ($dtQuery as $key => $value) {
            $pkLatLn[$incr]['id'] = $value['id'];
            $pkLatLn[$incr]['latln'] = $value['lat'] . ',' . $value['lon'];
            $incr++;
        }

        // dd($blokLatLn, $pkLatLn);


        $messageResponse = [];
        foreach ($blokLatLn as $value) {
            foreach ($pkLatLn as $marker) {
                if (isPointInPolygon($marker['latln'], $value['latln'])) {
                    DB::connection('mysql2')->table('deficiency_tracker')
                        ->where('id', $marker['id'])
                        ->update([
                            'afd' => $value['afd'],
                            'blok' => $value['blok']
                        ]);
                    $messageResponse[] = "Inside";
                } else {
                    $messageResponse[] = "Outside";
                }
            }
        }

        $response = [
            'message' => 'Berhasil sinkronisasi data!',
            'data' => $messageResponse
        ];

        return response()->json($response);
    }

    public function sinkronMaps()
    {
        $selectReg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->whereNotIn('reg.id', [5])
            ->get();
        $selectReg = json_decode($selectReg, true);

        $regArrSelected = [];
        foreach ($selectReg as $key => $value) {
            $regArrSelected[$key]['id'] = $value['id'];
            $regArrSelected[$key]['nama'] = $value['nama'];
        }
        // dd($regArrSelected);

        return view('Validate.synchronize', compact('regArrSelected'));
    }

    public function mainMaps()
    {
        $selectReg = DB::connection('mysql2')->table('reg')
            ->select('reg.*')
            ->whereNotIn('reg.id', [5])
            ->get();
        $selectReg = json_decode($selectReg, true);

        $regArrSelected = [];
        foreach ($selectReg as $key => $value) {
            $regArrSelected[$key]['id'] = $value['id'];
            $regArrSelected[$key]['nama'] = $value['nama'];
        }
        // dd($regArrSelected);

        return view('Validate.dashboard', compact('regArrSelected'));
    }

    public function getOptValidateEst($id)
    {
        $selectWil = DB::connection('mysql2')->table('wil')
            ->select('wil.*')
            ->where('regional', $id)
            ->pluck('id');
        $selectWil = json_decode($selectWil, true);

        $selectEst = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->whereIn('wil', $selectWil)
            ->where('est', 'LIKE', '%E')
            ->pluck('est');
        $selectEst = json_decode($selectEst, true);

        return response()->json($selectEst);
    }

    public function getOptValidateAfd($id)
    {
        $selectEst = DB::connection('mysql2')->table('estate')
            ->select('estate.*')
            ->where('est', $id)
            ->pluck('id');
        $selectEst = json_decode($selectEst, true);

        $selectAfd = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*')
            ->whereIn('estate', $selectEst)
            ->pluck('nama');
        $selectAfd = json_decode($selectAfd, true);

        return response()->json($selectAfd);
    }

    public function getCoordinatesValidate($est)
    {
        $estateQuery = DB::connection('mysql2')->table('estate')
            ->select('*')
            ->join('afdeling', 'afdeling.estate', '=', 'estate.id')
            ->where('estate.est', $est)
            ->get();
        $estateQuery = json_decode($estateQuery, true);

        $listIdAfd = array();
        foreach ($estateQuery as $key => $value) {
            $listIdAfd[] = $value['id'];
        }

        $blokEstate = DB::connection('mysql2')->table('blok')
            ->select(DB::raw('DISTINCT nama, MIN(id) as id, afdeling'))
            ->whereIn('afdeling', $listIdAfd)
            ->groupBy('nama', 'afdeling')
            ->get();
        $blokEstate = json_decode($blokEstate, true);

        $blokEstateFix = array();
        foreach ($blokEstate as $key => $value) {
            $blokEstateFix[$value['afdeling']][] = $value['nama'];
        }

        $qrAfd = DB::connection('mysql2')->table('afdeling')
            ->select('*')
            ->get();
        $qrAfd = json_decode($qrAfd, true);

        $blokEstNewFix = array();
        foreach ($blokEstateFix as $key => $value) {
            foreach ($qrAfd as $key1 => $value1) {
                if ($value1['id'] == $key) {
                    $afdelingNama = $value1['nama'];
                }
            }
            $blokEstNewFix[$afdelingNama] = $value;
        }

        $queryBlok = DB::connection('mysql2')->table('blok')
            ->select('*')
            ->whereIn('afdeling', $listIdAfd)
            ->get();
        $queryBlok = json_decode($queryBlok, true);

        $blokLatLn = array();
        foreach ($blokEstNewFix as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $latln = '';
                foreach ($queryBlok as $key3 => $value4) {
                    if ($value4['nama'] == $value1) {
                        $latln .= '[' . $value4['lon'] . ', ' . $value4['lat'] . ']$';
                    }
                }

                $blokLatLn[$key][$value1]['latln'] = rtrim($latln, '$');
            }
        }

        $dtQuery = DB::connection('mysql2')->table('deficiency_tracker')
            ->select('*')
            ->where('est', $est)
            ->get();
        $dtQuery = json_decode($dtQuery, true);

        $pkLatLn = array();
        foreach ($dtQuery as $key => $value) {
            $pkLatLn[$value['id']]['afd'] = $value['afd'];
            $pkLatLn[$value['id']]['blok'] = $value['blok'];
            $pkLatLn[$value['id']]['latln'] = $value['lat'] . ', ' . $value['lon'];
        }

        $response = [
            'data1' => $blokLatLn,
            'data2' => $pkLatLn,
        ];

        return response()->json($response);
    }

    public function processValidate(Request $request)
    {
        $rilAfd = $request->input('rilAfd');
        $rilBlok = $request->input('rilBlok');
        $inpAfd = $request->input('inpAfd');
        $inpBlok = $request->input('inpBlok');
        $markerIds = $request->input('markerIds');

        $queryUpdate = DB::connection('mysql2')->table('deficiency_tracker')
            ->whereIn('id', $markerIds)
            ->update([
                'afd' => $inpAfd,
                'blok' => $inpBlok
            ]);

        if ($queryUpdate > 0) {
            return response()->json(['message' => 'Berhasil memperbarui data!']);
        } else {
            return response()->json();
        }
    }
}
