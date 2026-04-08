<?php
namespace Api\Resources;

class BookingResource {
    
    public static function collection($bookings) {
        return array_map([self::class, 'transform'], $bookings);
    }
    
    public static function transform($booking) {
        if (!$booking) return null;
        
        return [
            'id' => $booking['id'] ?? null,
            'guest_id' => $booking['guest_id'] ?? null,
            'room_id' => $booking['room_id'] ?? null,
            'check_in' => $booking['check_in'] ?? null,
            'check_out' => $booking['check_out'] ?? null,
            'total_price' => $booking['total_price'] ?? null,
            'status' => $booking['status'] ?? null,
            'created_at' => $booking['created_at'] ?? null,
            'updated_at' => $booking['updated_at'] ?? null,
        ];
    }
}
?>
