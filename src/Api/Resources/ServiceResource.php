<?php
namespace Api\Resources;

class ServiceResource {
    
    public static function collection($services) {
        return array_map([self::class, 'transform'], $services);
    }
    
    public static function transform($service) {
        if (!$service) return null;
        
        return [
            'id' => $service['id'] ?? null,
            'name' => $service['name'] ?? null,
            'description' => $service['description'] ?? null,
            'price' => $service['price'] ?? null,
            'created_at' => $service['created_at'] ?? null,
            'updated_at' => $service['updated_at'] ?? null,
        ];
    }
}
?>
