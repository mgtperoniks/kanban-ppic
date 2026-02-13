<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 14pt; font-weight: bold;">LAPORAN KERUSAKAN PRODUKSI
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 12pt; font-weight: bold;">DEPARTEMEN
                {{ strtoupper(str_replace('_', ' ', $department)) }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Tanggal: {{ date('d F Y', strtotime($date)) }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold;">JENIS:
                {{ $defectType ? strtoupper($defectType->name) : 'SEMUA JENIS' }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #cccccc;">
            <th style="border: 1px solid #000000; font-weight: bold;">No</th>
            <th style="border: 1px solid #000000; font-weight: bold;">Heat Number</th>
            <th style="border: 1px solid #000000; font-weight: bold;">Nama Item</th>
            <th style="border: 1px solid #000000; font-weight: bold;">Qty (pcs)</th>
            <th style="border: 1px solid #000000; font-weight: bold;">Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; font-weight: bold;">{{ $item->heat_number }}</td>
                <td style="border: 1px solid #000000;">{{ $item->item_name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->total_defect_qty }}</td>
                <td style="border: 1px solid #000000; font-weight: bold;">{{ strtoupper($item->defect_summary) ?: '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $totalQty }}</td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
    </tfoot>
</table>