<?php
namespace Api\Resources;

class EmployeeResource {
    public static function collection($employees) {
        return array_map([self::class, 'transform'], $employees);
    }
    public static function transform($employee) {
        if (!$employee) return null;
        return [
            'id' => $employee['id'] ?? null,
            'name' => $employee['name'] ?? null,
            'email' => $employee['email'] ?? null,
            'phone' => $employee['phone'] ?? null,
            'department_id' => $employee['department_id'] ?? null,
            'position' => $employee['position'] ?? null,
            'created_at' => $employee['created_at'] ?? null,
            'updated_at' => $employee['updated_at'] ?? null,
        ];
    }
}
?>
