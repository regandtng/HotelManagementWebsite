<?php
namespace Api\Resources;

class RoomResource {
    
    public static function collection($rooms) {
        return array_map([self::class, 'transform'], $rooms);
    }
    
    public static function transform($room) {
        if (!$room) return null;
        
        return [
            'id' => $room['id'] ?? null,
            'room_number' => $room['room_number'] ?? null,
            'room_type_id' => $room['room_type_id'] ?? null,
            'floor' => $room['floor'] ?? null,
            'status' => $room['status'] ?? null,
            'created_at' => $room['created_at'] ?? null,
            'updated_at' => $room['updated_at'] ?? null,
        ];
    }
}
?>
