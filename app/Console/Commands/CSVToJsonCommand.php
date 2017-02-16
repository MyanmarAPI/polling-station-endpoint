<?php
/**
 * Command to convert Polling Station CSV to formatted Json 
 * 
 * @package App\Console\Commands
 * @author Li Jia Li <jiali.ninja@gmail.com>
 */

namespace App\Console\Commands;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CSVToJsonCommand extends Command{

    protected $name = 'convert:json {input} {output}';

    protected $description = 'Convert Polling Station CSV to formatted Json.';

    protected $filesystem;

    /**
     * Constructor method for ImportCommand class
     */
    public function __construct()
    {
        parent::__construct();

        $this->filesystem = app('files');
    }

    /**
     * Fire the command
     */
    public function fire()
    {
        
        $path = $this->input->getOption('path');

        $path = (is_null($path)) ? storage_path('data') : storage_path('data/' . $path);

        $input_file = $path . '/' . $this->input->getArgument('input');

        $output_file = $path . '/' . $this->input->getArgument('output');

        if (! $this->filesystem->exists($input_file))
        {
            return $this->line('[ERROR !] File not found - ' . $input_file);
        }

        $json_result = $this->processCSV($input_file);

        $output = fopen($output_file, 'w');
        fwrite($output, $json_result);
        fclose($output);

        $this->info('Json Exported at '. $output_file);

    }

    protected function processCSV($input_file) 
    {
        $reader = Reader::createFromPath($input_file);

        $keys = [];

        $json = [];

        foreach ($reader as $index => $row) {
            if ($index == 0) { //Header Keys
                $keys = $row;
            } else {
                $voting_ward_villages = [];
                if (!empty($row[8])) {
                    array_push($voting_ward_villages, $row[8]);
                }
                if (!empty($row[0])) {
                    //New Row
                    $arr_data = [
                        $keys[1] => $row[1],
                        $keys[2] => $row[2],
                        $keys[3] => $row[3],
                        $keys[4] => $row[4],
                        $keys[5] => $row[5],
                        'polling_stations' => [
                            [
                                'number' => (int)$row[6],
                                'location' => $row[7],
                                'voting_ward_villages' => $voting_ward_villages
                            ]
                        ]
                    ];

                    array_push($json, $arr_data);

                } else {
                    //Get Last Array
                    $last_index = count($json) - 1;
                    $last_ps = count($json[$last_index]['polling_stations']) - 1;

                    if (($json[$last_index]['polling_stations'][$last_ps]['location'] == $row[7] && $json[$last_index]['polling_stations'][$last_ps]['number'] == (int)$row[6]) || (empty($row[6]) && empty($row[7]))) {

                        if (!empty($row[8])) {
                            array_push($json[$last_index]['polling_stations'][$last_ps]['voting_ward_villages'], $row[8]);
                        }

                    } else {

                        array_push($json[$last_index]['polling_stations'], [
                            'number' => (int)$row[6],
                            'location' => $row[7],
                            'voting_ward_villages' => $voting_ward_villages
                        ]);

                    }
                }

            }
        }

        return json_encode($json);
    }

    /**
     * Get command arguments
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['input', InputArgument::REQUIRED, 'Input CSV Filename'],
            ['output', InputArgument::REQUIRED, 'Output Json Filename']
        ];
    }

    /**
     * Get command options
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, '--path="path/to/dir" Directory which contain csv data files']
        ];
    }

}