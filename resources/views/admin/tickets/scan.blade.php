@extends('layouts.admin')
@section('title', 'Gate Scanner')
@section('page-title', 'Gate Scanner')
@section('breadcrumb', 'Tickets / QR Scanner')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    {{-- Scanner card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center" id="scannerCard">
        <div class="text-5xl mb-4">📷</div>
        <h2 class="font-display text-2xl text-[#1e3a2f] mb-2">Scan Ticket QR Code</h2>
        <p class="text-sm text-gray-400 mb-6">Point the camera at the guest's QR code, or paste the code manually below.</p>

        {{-- Manual entry --}}
        <div class="flex gap-2 mb-4">
            <input type="text" id="qrInput" placeholder="Paste QR code / ticket number…"
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-[#4a8060] font-mono-kgr text-center tracking-widest"
                   autofocus>
            <button onclick="submitScan()" id="scanBtn"
                    class="bg-[#1e3a2f] text-white px-5 py-3 rounded-xl font-semibold text-sm hover:bg-[#2e5c42] transition-colors">
                Verify
            </button>
        </div>

        <p class="text-xs text-gray-400">Press Enter to scan. Works with a USB barcode scanner too.</p>
    </div>

    {{-- Result card (hidden by default) --}}
    <div id="resultCard" class="hidden rounded-2xl border-2 p-6">
        <div class="text-center mb-4">
            <div id="resultIcon" class="text-6xl mb-3"></div>
            <h3 id="resultTitle" class="font-display text-2xl mb-1"></h3>
            <p id="resultMsg" class="text-sm text-gray-600"></p>
        </div>
        <div id="ticketDetails" class="hidden bg-gray-50 rounded-xl p-4 text-sm space-y-2">
        </div>
        <button onclick="resetScanner()"
                class="mt-5 w-full border border-gray-200 text-gray-600 py-3 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-colors">
            Scan Next Ticket
        </button>
    </div>

    {{-- Today's stats --}}
    <div class="bg-[#f0e9d8] rounded-xl border border-amber-100 p-5 text-center">
        <div class="font-display text-3xl font-bold text-[#1e3a2f]" id="todayCount">—</div>
        <div class="text-xs text-[#c8974a] uppercase tracking-wide font-semibold mt-1">Guests Admitted Today</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let scanCount = 0;

document.getElementById('qrInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') submitScan();
});

async function submitScan() {
    const qrCode = document.getElementById('qrInput').value.trim();
    if (!qrCode) return;

    const btn = document.getElementById('scanBtn');
    btn.textContent = '…'; btn.disabled = true;

    try {
        const res = await fetch('{{ route("admin.tickets.scan.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({ qr_code: qrCode }),
        });
        const data = await res.json();
        showResult(data);
    } catch(e) {
        showResult({ success: false, message: 'Network error. Try again.' });
    }

    btn.textContent = 'Verify'; btn.disabled = false;
}

function showResult(data) {
    document.getElementById('scannerCard').classList.add('hidden');
    const card = document.getElementById('resultCard');
    card.classList.remove('hidden', 'border-green-300', 'bg-green-50', 'border-red-300', 'bg-red-50');

    if (data.success) {
        card.classList.add('border-green-300', 'bg-green-50');
        document.getElementById('resultIcon').textContent = '✅';
        document.getElementById('resultTitle').textContent = 'Admitted!';
        document.getElementById('resultTitle').className = 'font-display text-2xl mb-1 text-green-700';
        document.getElementById('resultMsg').textContent = data.message;
        scanCount++;
        document.getElementById('todayCount').textContent = scanCount;

        if (data.ticket) {
            const t = data.ticket;
            const details = document.getElementById('ticketDetails');
            details.classList.remove('hidden');
            details.innerHTML = `
                <div class="flex justify-between"><span class="text-gray-500">Ticket</span><span class="font-mono font-bold">${t.ticket_number}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Guest</span><span class="font-semibold">${t.guest_name}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Type</span><span>${t.ticket_type?.name ?? ''}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Quantity</span><span class="font-bold">${t.quantity} pax</span></div>
            `;
        }

        // Auto reset after 4 seconds on success
        setTimeout(resetScanner, 4000);
    } else {
        card.classList.add('border-red-300', 'bg-red-50');
        document.getElementById('resultIcon').textContent = '❌';
        document.getElementById('resultTitle').textContent = 'Entry Denied';
        document.getElementById('resultTitle').className = 'font-display text-2xl mb-1 text-red-700';
        document.getElementById('resultMsg').textContent = data.message;
        document.getElementById('ticketDetails').classList.add('hidden');
    }
}

function resetScanner() {
    document.getElementById('scannerCard').classList.remove('hidden');
    document.getElementById('resultCard').classList.add('hidden');
    document.getElementById('qrInput').value = '';
    document.getElementById('qrInput').focus();
}
</script>
@endpush
