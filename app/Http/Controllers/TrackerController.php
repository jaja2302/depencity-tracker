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

        $filterAfd = DB::connection('mysql2')->table('afdeling')
            ->select('afdeling.*', 'estate.est')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->get();

        $filterAfd = json_decode($filterAfd, true);



        // dd($filterEst, $filterAfd);

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
                    ->select('estate_plot.*', 'wil.regional')
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
                    ->get();

                $plot_kuning = $plot_kuning->groupBy(['est']);
                $plot_kuning = json_decode($plot_kuning, true);



                $arrView['blok'] = $plotEst;
                $arrView['pokok'] = $plot_kuning;

                // dd($plotEst);
                break;

            case 'estate':

                $plotEst = DB::connection('mysql2')
                    ->table('estate_plot')
                    ->select('estate_plot.*')
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

                $plot_kuning = $plot_kuning->groupBy(['est']);
                $plot_kuning = json_decode($plot_kuning, true);

                $arrView['blok'] = $plotEst;
                $arrView['pokok'] = $plot_kuning;

                break;

            case 'afdeling':
                # code...


                $plotAfd = DB::connection('mysql2')
                    ->table('blok')
                    ->select('blok.*', 'estate.est', 'afdeling.nama as afd_nama')
                    ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
                    ->join('estate', 'estate.id', '=', 'afdeling.estate')
                    ->where('estate.est', '=', $estate)
                    ->where('blok.afdeling', '=', $afdeling)
                    ->orderBy('id', 'asc')
                    ->get();

                $plotAfd = $plotAfd->groupBy(['est']);




                // $plotAfd = DB::connection('mysql2')
                //     ->table('afdeling_plot')
                //     ->select('afdeling_plot.*')
                //     ->where('afdeling_plot.est', '=', $estate)
                //     ->where('afdeling_plot.afd', '=', $afdeling)
                //     ->orderBy('id', 'asc')
                //     ->get();

                // $plotAfd = $plotAfd->groupBy(['est']);
                // $plotAfd = json_decode($plotAfd, true);

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

                $plot_kuning = $plot_kuning->groupBy(['est']);
                $plot_kuning = json_decode($plot_kuning, true);

                // dd($plot_kuning, $afdeling);

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

                $plot_kuning = DB::connection('mysql2')
                    ->table('deficiency_tracker')
                    ->select('deficiency_tracker.*')
                    ->where('deficiency_tracker.est', '=', $estate)
                    // ->where('deficiency_tracker.afd', '=', $afdeling)
                    ->where('deficiency_tracker.blok', 'LIKE', '%' . $blok . '%') // Use LIKE with % to match partial strings
                    ->orderBy('id', 'desc')
                    ->get();


                $plot_kuning = $plot_kuning->groupBy(['est']);
                $plot_kuning = json_decode($plot_kuning, true);
                // dd($plot_kuning, $blok);

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
}
