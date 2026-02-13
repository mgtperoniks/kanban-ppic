<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionDefect;
use App\Models\DefectType;
use App\Models\ProductionItem;
use Illuminate\Support\Facades\Response;

class DefectReportController extends Controller
{
    public function index(Request $request)
    {
        $departments = ['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'];
        $defectTypes = [];

        if ($request->department) {
            $defectTypes = DefectType::where('department', $request->department)->active()->get();
        }

        $results = null;
        $totalQty = 0;

        if ($request->has('generate')) {
            $request->validate([
                'date' => 'required|date',
                'department' => 'required|string',
                'defect_type_id' => 'nullable|exists:defect_types,id',
                'count' => 'required|integer|min:1|max:50',
            ]);

            $query = ProductionItem::whereDate('production_date', $request->date)
                ->whereHas('defects.defectType', function ($q) use ($request) {
                    $q->where('department', $request->department);
                    if ($request->defect_type_id) {
                        $q->where('id', $request->defect_type_id);
                    }
                })
                ->with([
                    'defects' => function ($q) use ($request) {
                        $q->whereHas('defectType', function ($sq) use ($request) {
                            $sq->where('department', $request->department);
                        })->with('defectType');
                        if ($request->defect_type_id) {
                            $q->where('defect_type_id', $request->defect_type_id);
                        }
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->limit($request->count);

            $results = $query->get()->map(function ($item) {
                // Pre-calculate aggregated summary for the view
                $details = $item->defects->groupBy('defect_type_id')->map(function ($group) {
                    $type = $group->first()->defectType->name;
                    $qty = $group->sum('qty');
                    return "{$type} {$qty}";
                })->implode(', ');

                $item->total_defect_qty = $item->defects->sum('qty');
                $item->defect_summary = $details;
                return $item;
            });

            $totalQty = $results->sum('total_defect_qty');
        }

        $defectType = $request->defect_type_id ? DefectType::find($request->defect_type_id) : null;

        return view('report.defects', [
            'departments' => $departments,
            'defectTypes' => $defectTypes,
            'results' => $results,
            'totalQty' => $totalQty,
            'selectedDate' => $request->date ?? date('Y-m-d'),
            'selectedDept' => $request->department,
            'selectedDefectType' => $request->defect_type_id,
            'defectType' => $defectType,
            'selectedCount' => $request->count ?? 10
        ]);
    }

    public function export(Request $request, $type)
    {
        $request->validate([
            'date' => 'required|date',
            'department' => 'required|string',
            'defect_type_id' => 'nullable|exists:defect_types,id',
            'count' => 'required|integer|min:1|max:50',
        ]);

        $query = ProductionItem::whereDate('production_date', $request->date)
            ->whereHas('defects.defectType', function ($q) use ($request) {
                $q->where('department', $request->department);
                if ($request->defect_type_id) {
                    $q->where('id', $request->defect_type_id);
                }
            })
            ->with([
                'defects' => function ($q) use ($request) {
                    $q->whereHas('defectType', function ($sq) use ($request) {
                        $sq->where('department', $request->department);
                    })->with('defectType');
                    if ($request->defect_type_id) {
                        $q->where('defect_type_id', $request->defect_type_id);
                    }
                }
            ])
            ->orderBy('created_at', 'desc')
            ->limit($request->count);

        $results = $query->get()->map(function ($item) {
            $details = $item->defects->groupBy('defect_type_id')->map(function ($group) {
                $type = $group->first()->defectType->name;
                $qty = $group->sum('qty');
                return "{$type} {$qty}";
            })->implode(', ');

            $item->total_defect_qty = $item->defects->sum('qty');
            $item->defect_summary = $details;
            return $item;
        });

        $totalQty = $results->sum('total_defect_qty');
        $defectType = $request->defect_type_id ? DefectType::find($request->defect_type_id) : null;

        $data = [
            'date' => $request->date,
            'department' => $request->department,
            'defectType' => $defectType,
            'results' => $results,
            'totalQty' => $totalQty
        ];

        if ($type === 'pdf') {
            return view('report.defects_print', $data);
        } elseif ($type === 'excel') {
            $typeName = $defectType ? $defectType->name : 'Semua';
            $filename = "Report_Kerusakan_{$request->department}_{$typeName}_{$request->date}.xls";

            return Response::make(view('report.defects_excel', $data), 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return back();
    }
}
