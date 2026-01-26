<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ ucfirst($department) }} Line {{ $line }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-gray-100 p-8 print:bg-white print:p-0" onload="window.print()">

    <div class="max-w-4xl mx-auto bg-white p-8 shadow-sm print:shadow-none print:w-full">
        <!-- Header -->
        <div class="text-center mb-6 border-b-2 border-black pb-4">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Surat Perintah Kerja Produksi</h1>
            <h2 class="text-lg font-bold uppercase mt-1">Departemen {{ str_replace('_', ' ', $department) }}</h2>
        </div>

        <!-- Info -->
        <div class="flex justify-between mb-6 text-sm font-medium">
            <div>
                <p>Tanggal: <span class="font-normal">{{ date('d F Y', strtotime($date)) }}</span></p>
                <p>Line Produksi: <span class="font-bold text-lg">LINE {{ $line }}</span></p>
            </div>
            <div class="text-right">
                <p>No. Dokumen: SPK/{{ strtoupper(substr($department, 0, 3)) }}/{{ date('Ymd') }}/L{{ $line }}</p>
                <p>Dicetak Oleh: {{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full border-collapse border border-black mb-8 text-sm">
            <thead>
                <tr class="bg-gray-200 text-black">
                    <th class="border border-black px-2 py-1.5 text-center w-10">No</th>
                    <th class="border border-black px-2 py-1.5 text-left">Heat Number</th>
                    <th class="border border-black px-2 py-1.5 text-left">Nama Item</th>
                    <th class="border border-black px-2 py-1.5 text-center">Jumlah (pcs)</th>
                    <th class="border border-black px-2 py-1.5 text-center">Berat (kg)</th>
                    <th class="border border-black px-2 py-1.5 text-center">Antri (Hari)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $index => $item)
                <tr>
                    <td class="border border-black px-2 py-1.5 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black px-2 py-1.5 font-bold">{{ $item->heat_number }}</td>
                    <td class="border border-black px-2 py-1.5">{{ $item->item_name }}</td>
                    <td class="border border-black px-2 py-1.5 text-center">{{ number_format($item->qty_pcs) }}</td>
                    <td class="border border-black px-2 py-1.5 text-center">{{ number_format($item->weight_kg, 1) }}</td>
                    <td class="border border-black px-2 py-1.5 text-center">{{ number_format($item->aging_days, 1) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold bg-gray-100">
                    <td colspan="3" class="border border-black px-2 py-1.5 text-right">TOTAL</td>
                    <td class="border border-black px-2 py-1.5 text-center">{{ number_format($totalPcs) }}</td>
                    <td class="border border-black px-2 py-1.5 text-center">{{ number_format($totalKg, 1) }}</td>
                    <td class="border border-black px-2 py-1.5"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="flex justify-between mt-16 px-16">
            <div class="text-center">
                <p class="mb-20">Diterima (SPV)</p>
                <div class="border-t border-black w-40 mx-auto"></div>
            </div>
            <div class="text-center">
                <p class="mb-20">Dibuat Oleh (Admin PPIC)</p>
                <div class="border-t border-black w-40 mx-auto"></div>
                <p class="text-xs mt-1">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>

</body>
</html>
