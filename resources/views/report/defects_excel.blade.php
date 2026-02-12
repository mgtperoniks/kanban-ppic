<table>
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 14pt; font-weight: bold;">LAPORAN KERUSAKAN PRODUKSI
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-size: 12pt; font-weight: bold;">DEPARTEMEN
                {{ strtoupper(str_replace('_', ' ', $department)) }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Tanggal: {{ date('d F Y', strtotime($date)) }}</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold;">JENIS: {{ strtoupper($defectType->name) }}
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
        @foreach($results as $index => $defect)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000; font-weight: bold;">{{ $defect->item->heat_number }}</td>
                <td style="border: 1px solid #000000;">{{ $defect->item->item_name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $defect->qty }}</td>
                <td style="border: 1px solid #000000; font-style: italic;">{{ $defect->notes ?? '-' }}</td>
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