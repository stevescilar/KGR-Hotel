<?php

namespace App\Services;

use App\Models\{GateTicket, TicketType};

class GateTicketService
{
    /**
     * Purchase tickets and return the created GateTicket record.
     */
    public function purchaseTickets(array $data): GateTicket
    {
        $ticketType = TicketType::findOrFail($data['ticket_type_id']);
        $quantity   = (int) $data['quantity'];
        $total      = $ticketType->price * $quantity;

        return GateTicket::create([
            'ticket_type_id' => $ticketType->id,
            'guest_id'       => $data['guest_id'] ?? null,
            'guest_name'     => $data['guest_name'],
            'guest_phone'    => $data['guest_phone'],
            'guest_email'    => $data['guest_email'] ?? null,
            'visit_date'     => $data['visit_date'],
            'quantity'       => $quantity,
            'unit_price'     => $ticketType->price,
            'total_price'    => $total,
            'status'         => 'active',
        ]);
    }

    /**
     * Validate and scan a ticket at the gate by QR code string.
     *
     * @return array{success: bool, message: string, ticket?: GateTicket}
     */
    public function scanTicket(string $qrCode, int $staffId): array
    {
        $ticket = GateTicket::where('qr_code', $qrCode)->first();

        if (!$ticket) {
            return ['success' => false, 'message' => 'Invalid QR code — ticket not found.'];
        }

        if ($ticket->status === 'used') {
            return [
                'success' => false,
                'message' => "Ticket already used on {$ticket->scanned_at?->format('D M j, g:i A')}.",
            ];
        }

        if ($ticket->status === 'cancelled') {
            return ['success' => false, 'message' => 'This ticket has been cancelled.'];
        }

        if ($ticket->status === 'expired') {
            return ['success' => false, 'message' => 'This ticket has expired.'];
        }

        if (!$ticket->visit_date->isToday()) {
            $formatted = $ticket->visit_date->format('l, M j Y');
            return [
                'success' => false,
                'message' => "This ticket is valid for {$formatted} only.",
            ];
        }

        // Mark as used
        $ticket->update([
            'status'     => 'used',
            'scanned_at' => now(),
            'scanned_by' => $staffId,
        ]);

        return [
            'success' => true,
            'message' => "✓ Welcome! {$ticket->guest_name} · {$ticket->quantity} pax · {$ticket->ticketType->name}",
            'ticket'  => $ticket->load('ticketType'),
        ];
    }

    /**
     * Mark expired tickets (run via scheduler).
     */
    public function expireOldTickets(): int
    {
        return GateTicket::where('status', 'active')
            ->where('visit_date', '<', today())
            ->update(['status' => 'expired']);
    }
}
