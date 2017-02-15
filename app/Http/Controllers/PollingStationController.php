<?php
/**
 * Polling Station Controller
 * 
 * @package App\Http\Controllers
 * @author Li Jia Li [limonster.li@gmail.com]
 */

namespace App\Http\Controllers;

use MongoDate;
use Carbon\Carbon;
use App\Model\PollingStation;
use App\Transformers\PollingStationTransformer;

class PollingStationController extends Controller
{

    /**
     * Get candidate list
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function psList()
    {
        $fields = $this->getRequestFields(app('request'));

        $data = $this->transform($this->query($fields), new PollingStationTransformer($fields), true);

        return response_ok($data);
    }

    /**
     * Get Polling Station by ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     **/
    public function getByID($id)
    {
        $data = (new PollingStation())->find($id);

        if (!$data) {
            return response_missing();
        }

        $item = $this->transform($data, new PollingStationTransformer($this->getRequestFields(app('request'))), false);

        return response_ok($item);
    }

    protected function query($fields = [])
    {
        $request = app('request');

        $model = new PollingStation();

        if ($st_pcode = $request->input('st_pcode')) {
            $model = $model->where('ST_PCODE', $st_pcode);
        }

        if ($ts_pcode = $request->input('ts_pcode')) {
            $model = $model->where('TS_PCODE', $ts_pcode);
        }

        if ($ward_village = $request->input('ward_village')) {
            $model = $model->where('ward_village', $ward_village);
        }

        return $model->paginate($fields);
    }

    /**
     * Get response fields lists from request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function getRequestFields($request)
    {
        if ( ! $request->has('fields')) {
            return [];
        }

        return explode(',', $request->input('fields'));
    }
}