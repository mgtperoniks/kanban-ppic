@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800">Input Data: {{ ucfirst($dept) }}</h1>
        <button onclick="submitData()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded text-sm">
            Simpan Data
        </button>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        <p><strong>Panduan:</strong> Copy & Paste data dari Excel (Format: Heat No | Item Name | Qty | Weight). Data akan masuk ke Line 1.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300 text-sm" id="inputTable">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-2 py-1">Heat Number</th>
                    <th class="border border-gray-300 px-2 py-1">Item Name</th>
                    <th class="border border-gray-300 px-2 py-1 w-24">Qty (PCS)</th>
                    <th class="border border-gray-300 px-2 py-1 w-24">Weight (KG)</th>
                    <th class="border border-gray-300 px-2 py-1 w-12">Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 p-0"><input type="text" class="w-full h-full px-2 py-1 focus:outline-none" placeholder="Paste here..."></td>
                    <td class="border border-gray-300 p-0"><input type="text" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                    <td class="border border-gray-300 p-0"><input type="number" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                    <td class="border border-gray-300 p-0"><input type="number" step="0.01" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                    <td class="border border-gray-300 p-0 text-center"><button class="text-red-500 font-bold" onclick="removeRow(this)">×</button></td>
                </tr>
            </tbody>
        </table>
        <button onclick="addRow()" class="mt-2 text-blue-600 text-sm font-semibold">+ Tambah Baris</button>
    </div>
</div>

<script>
    const tableBody = document.getElementById('tableBody');

    // Handle Paste logic
    tableBody.addEventListener('paste', function(e) {
        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text');
        
        if (!pastedData) return;

        const rows = pastedData.split(/\r\n|\n|\r/);
        
        // Remove empty last row if exists
        if (rows.length > 0 && rows[rows.length - 1].trim() === '') {
            rows.pop();
        }

        // Clear only if pasting on the first empty row, otherwise append?
        // Let's just append for now, or replace current row if it's empty
        
        // Simplest: Just append new rows
        rows.forEach(row => {
            const cols = row.split(/\t/);
            if (cols.length < 1) return;

            const newRow = document.createElement('tr');
            newRow.classList.add('hover:bg-gray-50');
            newRow.innerHTML = `
                <td class="border border-gray-300 p-0"><input type="text" value="${cols[0] || ''}" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                <td class="border border-gray-300 p-0"><input type="text" value="${cols[1] || ''}" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                <td class="border border-gray-300 p-0"><input type="number" value="${cols[2] || ''}" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                <td class="border border-gray-300 p-0"><input type="number" step="0.01" value="${cols[3] || ''}" class="w-full h-full px-2 py-1 focus:outline-none"></td>
                <td class="border border-gray-300 p-0 text-center"><button class="text-red-500 font-bold" onclick="removeRow(this)">×</button></td>
            `;
            tableBody.appendChild(newRow);
        });

        // Remove the initial empty row if it's still empty and we pasted stuff
        const firstRow = tableBody.querySelector('tr');
        if (firstRow) {
             const inputs = firstRow.querySelectorAll('input');
             let isEmpty = true;
             inputs.forEach(input => { if(input.value) isEmpty = false; });
             if (isEmpty && rows.length > 0) firstRow.remove();
        }
    });

    function addRow() {
        const newRow = document.createElement('tr');
        newRow.classList.add('hover:bg-gray-50');
        newRow.innerHTML = `
            <td class="border border-gray-300 p-0"><input type="text" class="w-full h-full px-2 py-1 focus:outline-none"></td>
            <td class="border border-gray-300 p-0"><input type="text" class="w-full h-full px-2 py-1 focus:outline-none"></td>
            <td class="border border-gray-300 p-0"><input type="number" class="w-full h-full px-2 py-1 focus:outline-none"></td>
            <td class="border border-gray-300 p-0"><input type="number" step="0.01" class="w-full h-full px-2 py-1 focus:outline-none"></td>
            <td class="border border-gray-300 p-0 text-center"><button class="text-red-500 font-bold" onclick="removeRow(this)">×</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
    }

    function submitData() {
        const rows = tableBody.querySelectorAll('tr');
        const items = [];

        rows.forEach(row => {
            const inputs = row.querySelectorAll('input');
            const heat = inputs[0].value.trim();
            const name = inputs[1].value.trim();
            const qty = inputs[2].value.trim();
            const weight = inputs[3].value.trim();

            if (heat && name && qty && weight) {
                items.push({
                    heat_number: heat,
                    item_name: name,
                    qty_pcs: parseInt(qty),
                    weight_kg: parseFloat(weight)
                });
            }
        });

        if (items.length === 0) {
            alert('Please input at least one item.');
            return;
        }

        axios.post('{{ route('input.store', $dept) }}', { items: items })
            .then(res => {
                alert(res.data.message);
                if (res.data.redirect) {
                    window.location.href = res.data.redirect;
                } else {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error submitting data. Check console.');
            });
    }
</script>
@endsection
