<?php
/**
 * API Routes Definition
 * Format: [METHOD, PATH, CONTROLLER, ACTION]
 */

return [
    // ============ Root ============
    ['GET', '/api', 'ApiController', 'index'],
    ['GET', '/api/', 'ApiController', 'index'],
    ['GET', '/api/v1', 'ApiController', 'index'],

    // ============ GUESTS ============
    ['GET', '/api/v1/guests', 'GuestController', 'index'],
    ['POST', '/api/v1/guests', 'GuestController', 'store'],
    ['GET', '/api/v1/guests/:id', 'GuestController', 'show'],
    ['PUT', '/api/v1/guests/:id', 'GuestController', 'update'],
    ['DELETE', '/api/v1/guests/:id', 'GuestController', 'destroy'],

    // ============ BOOKINGS ============
    ['GET', '/api/v1/bookings', 'BookingController', 'index'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store'],
    ['GET', '/api/v1/bookings/:id', 'BookingController', 'show'],
    ['PUT', '/api/v1/bookings/:id', 'BookingController', 'update'],
    ['DELETE', '/api/v1/bookings/:id', 'BookingController', 'destroy'],

    // ============ ROOMS ============
    ['GET', '/api/v1/rooms', 'RoomController', 'index'],
    ['POST', '/api/v1/rooms', 'RoomController', 'store'],
    ['GET', '/api/v1/rooms/:id', 'RoomController', 'show'],
    ['PUT', '/api/v1/rooms/:id', 'RoomController', 'update'],
    ['DELETE', '/api/v1/rooms/:id', 'RoomController', 'destroy'],

    // ============ ROOM TYPES ============
    ['GET', '/api/v1/room-types', 'RoomTypeController', 'index'],
    ['POST', '/api/v1/room-types', 'RoomTypeController', 'store'],
    ['GET', '/api/v1/room-types/:id', 'RoomTypeController', 'show'],
    ['PUT', '/api/v1/room-types/:id', 'RoomTypeController', 'update'],
    ['DELETE', '/api/v1/room-types/:id', 'RoomTypeController', 'destroy'],

    // ============ SERVICES ============
    ['GET', '/api/v1/services', 'ServiceController', 'index'],
    ['POST', '/api/v1/services', 'ServiceController', 'store'],
    ['GET', '/api/v1/services/:id', 'ServiceController', 'show'],
    ['PUT', '/api/v1/services/:id', 'ServiceController', 'update'],
    ['DELETE', '/api/v1/services/:id', 'ServiceController', 'destroy'],

    // ============ DEPARTMENTS ============
    ['GET', '/api/v1/departments', 'DepartmentController', 'index'],
    ['POST', '/api/v1/departments', 'DepartmentController', 'store'],
    ['GET', '/api/v1/departments/:id', 'DepartmentController', 'show'],
    ['PUT', '/api/v1/departments/:id', 'DepartmentController', 'update'],
    ['DELETE', '/api/v1/departments/:id', 'DepartmentController', 'destroy'],

    // ============ EMPLOYEES ============
    ['GET', '/api/v1/employees', 'EmployeeController', 'index'],
    ['POST', '/api/v1/employees', 'EmployeeController', 'store'],
    ['GET', '/api/v1/employees/:id', 'EmployeeController', 'show'],
    ['PUT', '/api/v1/employees/:id', 'EmployeeController', 'update'],
    ['DELETE', '/api/v1/employees/:id', 'EmployeeController', 'destroy'],

    // ============ PAYMENTS ============
    ['GET', '/api/v1/payments', 'PaymentController', 'index'],
    ['POST', '/api/v1/payments', 'PaymentController', 'store'],
    ['GET', '/api/v1/payments/:id', 'PaymentController', 'show'],
    ['PUT', '/api/v1/payments/:id', 'PaymentController', 'update'],
    ['DELETE', '/api/v1/payments/:id', 'PaymentController', 'destroy'],

    // ============ DISCOUNTS ============
    ['GET', '/api/v1/discounts', 'DiscountController', 'index'],
    ['POST', '/api/v1/discounts', 'DiscountController', 'store'],
    ['GET', '/api/v1/discounts/:id', 'DiscountController', 'show'],
    ['PUT', '/api/v1/discounts/:id', 'DiscountController', 'update'],
    ['DELETE', '/api/v1/discounts/:id', 'DiscountController', 'destroy'],

    // ============ ACCOUNTS ============
    ['GET', '/api/v1/accounts', 'AccountController', 'index'],
    ['POST', '/api/v1/accounts', 'AccountController', 'store'],
    ['GET', '/api/v1/accounts/:id', 'AccountController', 'show'],
    ['PUT', '/api/v1/accounts/:id', 'AccountController', 'update'],
    ['DELETE', '/api/v1/accounts/:id', 'AccountController', 'destroy'],
];
?>
