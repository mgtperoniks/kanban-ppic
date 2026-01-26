<table border="1">
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 16px; font-weight: bold;">SURAT PERINTAH KERJA PRODUKSI</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 14px; font-weight: bold;">DEPARTEMEN {{ strtoupper(str_replace('_', ' ', $department)) }}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <td colspan="2">Tanggal: {{ date('d F Y', strtotime($date)) }}</td>
            <td colspan="4" style="text-align: right;">Line: {{ $line }}</td>
        </tr>
        <tr>
            <th style="background-color: #f0f0f0;">No</th>
            <th style="background-color: #f0f0f0;">Heat Number</th>
            <th style="background-color: #f0f0f0;">Nama Item</th>
            <th style="background-color: #f0f0f0;">Jumlah (pcs)</th>
            <th style="background-color: #f0f0f0;">Berat (kg)</th>
            <th style="background-color: #f0f0f0;">Antri (Hari)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $item)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td style="text-align: left; font-weight: bold;">{{ $item->heat_number }}</td>
            <td style="text-align: left;">{{ $item->item_name }}</td>
            <td style="text-align: center;">{{ $item->qty_pcs }}</td>
            <td style="text-align: center;">{{ $item->weight_kg }}</td>
            <td style="text-align: center;">{{ number_format($item->aging_days, 1) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL</td>
            <td style="text-align: center; font-weight: bold;">{{ $totalPcs }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $totalKg }}</td>
            <td></td>
        </tr>
    </tbody>
    <tfoot>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: center;">Diterima (SPV)</td>
            <td colspan="2"></td>
            <td colspan="2" style="text-align: center;">Dibuat Oleh (Admin)</td>
        </tr>
        <tr>
            <td colspan="6" height="50"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">( ......................... )</td>
            <td colspan="2"></td>
            <td colspan="2" style="text-align: center;">( {{ Auth::user()->name }} )</td>
        </tr>
    </tfoot>
</table>
