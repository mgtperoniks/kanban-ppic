<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionDefect;
use App\Models\DefectType;
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
                'defect_type_id' => 'required|exists:defect_types,id',
                'count' => 'required|integer|min:1|max:50',
            ]);

            $query = ProductionDefect::whereHas('item', function ($q) use ($request) {
                $q->where('current_dept', $request->department);
            })
                ->where('defect_type_id', $request->defect_type_id)
                ->with('item')
                ->orderBy('created_at', 'desc')
                ->limit($request->count);

            $results = $query->get();
            $totalQty = $results->sum('qty');
        }

        return view('report.defects', [
            'departments' => $departments,
            'defectTypes' => $defectTypes,
            'results' => $results,
            'totalQty' => $totalQty,
            'selectedDate' => $request->date ?? date('Y-m-d'),
            'selectedDept' => $request->department,
            'selectedDefectType' => $request->defect_type_id,
            'selectedCount' => $request->count ?? 10
        ]);
    }

    public function export(Request $request, $type)
    {
        $request->validate([
            'date' => 'required|date',
            'department' => 'required|string',
            'defect_type_id' => 'required|exists:defect_types,id',
            'count' => 'required|integer|min:1|max:50',
        ]);

        $results = ProductionDefect::whereHas('item', function ($q) use ($request) {
            $q->where('current_dept', $request->department);
        })
            ->where('defect_type_id', $request->defect_type_id)
            ->with(['item', 'defectType'])
            ->orderBy('created_at', 'desc')
            ->limit($request->count)
            ->get();

        $totalQty = $results->sum('qty');
        $defectType = DefectType::find($request->defect_type_id);

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
            $filename = "Report_Kerusakan_{$request->department}_{$defectType->name}_{$request->date}.xls";

            return Response::make(view('report.defects_excel', $data), 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return back();
    }
}
