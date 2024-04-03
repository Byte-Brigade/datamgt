<?php

namespace App\Http\Resources;

use App\Models\GapSto;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $gap_asset_details = $this->gap_asset_details();

        if (isset($gap_asset_details)) {
            if (!is_null($request->input('$y'))) {
                $year = Carbon::createFromDate($request->input('$y'))->startOfYear()->format('Y-m-d');
                $sto = GapSto::where('status', 'Done')->where('periode', $year)->latest()->first();

                $gap_asset_details = $gap_asset_details->where('periode', $sto->periode)->where('semester', $sto->semester);
            } else {
                $sto = GapSto::where('status', 'Done')->latest()->first();
                $gap_asset_details = $gap_asset_details->where('periode', $sto->periode)->where('semester', $sto->semester);
            }

            $gap_asset_details = $gap_asset_details->first();
        }

        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'category' => $this->category,
            'asset_number' => $this->asset_number,
            'asset_description' => $this->asset_description,
            'date_in_place_service' => $this->date_in_place_service,
            'asset_cost' => $this->asset_cost,
            'asset_location' => $this->asset_location,
            'major_category' => $this->major_category,
            'minor_category' => $this->minor_category,
            'depre_exp' => $this->depre_exp,
            'accum_depre' => $this->accum_depre,
            'net_book_value' => $this->net_book_value,
            'branch_name' => $this->branches->branch_name,
            'periode' => $this->periode,

            'slug' => $this->slug,
            'remark' => $this->remark,
            'status' => isset($gap_asset_details) ? $gap_asset_details->status : null,
            'tahun' => isset($gap_asset_details) ? Carbon::parse($gap_asset_details->periode)->year : null,
            'semester' => isset($gap_asset_details) ? $gap_asset_details->semester : null,
            'details' => $this->gap_asset_details,


        ];
    }
}
