<?php
/**
 * This file is part of candidate project
 * 
 * @package App\Transformers
 * @author Li Jia Li <limonster.li@gmail.com>
 */

namespace App\Transformers;


use App\Model\Party;
use App\Transformers\Contracts\TransformerInterface;
use League\Fractal\TransformerAbstract;

class PollingStationTransformer extends TransformerAbstract implements TransformerInterface
{

    protected $fields = [];

    public function __construct($fields = [])
    {
        $this->fields = $fields;
    }

    public function transform($data)
    {
        if ( ! empty($this->fields)) {
            return $this->transformFieldOnly($this->fields, $data);
        }

        return [
           '_id'                => (string)$data->_id,
           'state_region'       => $data->state_region,
           'ST_PCODE'           => $data->ST_PCODE,
           'township'           => $data->township,
           'TS_PCODE'           => $data->TS_PCODE,
           'ward_village'       => $data->ward_village,
           'polling_stations'   => $data->polling_stations
        ];
    }

    protected function transformFieldOnly($fields, $data)
    {
        $result = [];

        foreach ($fields as $f) {            
            $result[$f] = $data->{$f};
        }

        return $result;
    }

}