<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class MergeController extends Controller
{
    public function show()
    {
        if (Auth::user()->isAdmin()) {
            $media = Medium::all();
            $media_groups = array();

            for ($ref_idx = 0; $ref_idx < $media->count(); $ref_idx++) {
                $reference_file_size = filesize(storage_path('app/public/' . $media[$ref_idx]->name . '.' . $media[$ref_idx]->extension));

                $media_groups[$ref_idx] = [
                    'name' => $media[$ref_idx]->name,
                    'extension' => $media[$ref_idx]->extension,
                    'mimeType' => $media[$ref_idx]->mime_type,
                    'duplicates' => array()
                ];
                
                for ($insp_idx = $ref_idx + 1; $insp_idx < $media->count();) {
                    $inspected_file_size = filesize(storage_path('app/public/' . $media[$insp_idx]->name . '.' . $media[$insp_idx]->extension));

                    if ($reference_file_size == $inspected_file_size) {
                        $media_groups[$ref_idx]['duplicates'][] = [
                            'name' => $media[$insp_idx]->name,
                            'extension' => $media[$insp_idx]->extension
                        ];

                        $media->splice($insp_idx, 1);
                    } else {
                        $insp_idx++;
                    }
                }
            }

            for ($i = 0; $i < count($media_groups);) {
                if (count($media_groups[$i]['duplicates']) == 0) {
                    array_splice($media_groups, $i, 1);
                } else {
                    $i++;
                }
            }

            return view('merge', compact('media_groups'));
        } else {
            return redirect()->route('welcome');
        }
    }
}
