@extends('layouts.admin')
@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')
@section('breadcrumb', 'Reports / Revenue')

@section('content')
<div class="space-y-5">

    {{-- Year picker --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Year</label>
            <select name="year" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <div class="ml-auto text-right">
            <div class="text-2xl font-bold font-display text-[#1e3a2f]">KES {{ number_format($total) }}</div>
            <div class="text-xs text-gray-400 uppercase tracking-wide">Total {{ $year }}</div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Monthly Revenue — {{ $year }}</h3>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    {{-- Monthly breakdown table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Month</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Rooms</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">F&B</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tickets</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($data as $row)
                @php $rowTotal = $row['rooms'] + $row['fnb'] + $row['tickets'] @endphp
                <tr class="hover:bg-gray-50 {{ $row['month'] === now()->format('M') ? 'bg-amber-50/40' : '' }}">
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $row['month'] }}</td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $row['rooms'] > 0 ? 'KES ' . number_format($row['rooms']) : '—' }}</td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $row['fnb'] > 0 ? 'KES ' . number_format($row['fnb']) : '—' }}</td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $row['tickets'] > 0 ? 'KES ' . number_format($row['tickets']) : '—' }}</td>
                    <td class="px-5 py-3.5 text-right font-bold text-gray-800">{{ $rowTotal > 0 ? 'KES ' . number_format($rowTotal) : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-[#1e3a2f]">
                <tr>
                    <td class="px-5 py-3.5 text-white font-bold text-sm">Total {{ $year }}</td>
                    <td class="px-5 py-3.5 text-right text-white font-semibold text-sm">KES {{ number_format($data->sum('rooms')) }}</td>
                    <td class="px-5 py-3.5 text-right text-white font-semibold text-sm">KES {{ number_format($data->sum('fnb')) }}</td>
                    <td class="px-5 py-3.5 text-right text-white font-semibold text-sm">KES {{ number_format($data->sum('tickets')) }}</td>
                    <td class="px-5 py-3.5 text-right text-white font-bold text-sm">KES {{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const data = @json($data);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: data.map(d => d.month),
        datasets: [
            { label: 'Rooms',   data: data.map(d => d.rooms),   backgroundColor: '#1e3a2f' },
            { label: 'F&B',     data: data.map(d => d.fnb),     backgroundColor: '#7aaa8a' },
            { label: 'Tickets', data: data.map(d => d.tickets), backgroundColor: '#c8974a' },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { stacked: true, ticks: { callback: v => 'KES ' + (v/1000).toFixed(0) + 'k' }, grid: { color: '#f5f5f5' } }
        }
    }
});
</script>
@endpush
