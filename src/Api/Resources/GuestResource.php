<?php
namespace Api\Resources;

class GuestResource {
    
    /**
     * Transform collection
     */
    public static function collection($guests) {
        return array_map([self::class, 'transform'], $guests);
    }
    
    /**
     * Transform single guest
     */
    public static function transform($guest) {
        if (!$guest) return null;
        
        return [
            'id' => $guest['id'] ?? null,
            'name' => $guest['name'] ?? null,
            'email' => $guest['email'] ?? null,
            'phone' => $guest['phone'] ?? null,
            'address' => $guest['address'] ?? null,
            'national_id' => $guest['national_id'] ?? null,
            'created_at' => $guest['created_at'] ?? null,
            'updated_at' => $guest['updated_at'] ?? null,
        ];
    }
}
?>
