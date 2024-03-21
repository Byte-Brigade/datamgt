<?php

namespace App\Http\Resources\Inquery;

use App\Models\GapSto;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetSTOResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $gap_asset_details = $this->gap_asset_details->where('gap_hasil_sto_id', $request->gap_hasil_sto_id)->first();

        if (!isset($gap_asset_details)) {
            $latestSTO = GapSto::where('status', 'On Progress')
                ->latest()
                ->first();
            if (isset($latestSTO)) {
                $gap_asset_details = $this->gap_asset_details->where('periode', $latestSTO->periode)->where('semester', $latestSTO->semester)->first();
            }
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
            'semester' => isset($gap_asset_details) ? $gap_asset_details->semester : null,
            'semestawdaer' => $this->gap_asset_details,


        ];
    }
}
