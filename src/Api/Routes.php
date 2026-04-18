<?php
/**
 * API Routes Definition
 * Format: [METHOD, PATH, CONTROLLER, ACTION, MIDDLEWARE]
 * Middleware: 'auth' for authentication required, 'admin' for admin only
 */

return [
    // ============ AUTHENTICATION ============
    ['POST', '/api/v1/auth/login', 'AuthController', 'login'],
    ['GET', '/api/v1/auth/me', 'AuthController', 'me', 'auth'],
    ['POST', '/api/v1/auth/logout', 'AuthController', 'logout', 'auth'],

    // ============ Root ============
    ['GET', '/api', 'ApiController', 'index'],
    ['GET', '/api/', 'ApiController', 'index'],
    ['GET', '/api/v1', 'ApiController', 'index'],

    // ============ GUESTS ============
    ['GET', '/api/v1/guests', 'GuestController', 'index', 'auth'],
    ['POST', '/api/v1/guests', 'GuestController', 'store', 'auth'],
    ['GET', '/api/v1/guests/:id', 'GuestController', 'show', 'auth'],
    ['PUT', '/api/v1/guests/:id', 'GuestController', 'update', 'auth'],
    ['DELETE', '/api/v1/guests/:id', 'GuestController', 'destroy', 'auth'],

    // ============ BOOKINGS ============
    ['GET', '/api/v1/bookings', 'BookingController', 'index', 'auth'],
    ['POST', '/api/v1/bookings', 'BookingController', 'store', 'auth'],
    ['GET', '/api/v1/bookings/:id', 'BookingController', 'show', 'auth'],
    ['PUT', '/api/v1/bookings/:id', 'BookingController', 'update', 'auth'],
    ['DELETE', '/api/v1/bookings/:id', 'BookingController', 'destroy', 'auth'],

    // ============ ROOMS ============
    ['GET', '/api/v1/rooms', 'RoomController', 'index', 'auth'],
    ['POST', '/api/v1/rooms', 'RoomController', 'store', 'auth'],
    ['GET', '/api/v1/rooms/:id', 'RoomController', 'show', 'auth'],
    ['PUT', '/api/v1/rooms/:id', 'RoomController', 'update', 'auth'],
    ['DELETE', '/api/v1/rooms/:id', 'RoomController', 'destroy', 'auth'],

    // ============ ROOM TYPES ============
    ['GET', '/api/v1/room-types', 'RoomTypeController', 'index', 'auth'],
    ['POST', '/api/v1/room-types', 'RoomTypeController', 'store', 'auth'],
    ['GET', '/api/v1/room-types/:id', 'RoomTypeController', 'show', 'auth'],
    ['PUT', '/api/v1/room-types/:id', 'RoomTypeController', 'update', 'auth'],
    ['DELETE', '/api/v1/room-types/:id', 'RoomTypeController', 'destroy', 'auth'],

    // ============ SERVICES ============
    ['GET', '/api/v1/services', 'ServiceController', 'index', 'auth'],
    ['POST', '/api/v1/services', 'ServiceController', 'store', 'auth'],
    ['GET', '/api/v1/services/:id', 'ServiceController', 'show', 'auth'],
    ['PUT', '/api/v1/services/:id', 'ServiceController', 'update', 'auth'],
    ['DELETE', '/api/v1/services/:id', 'ServiceController', 'destroy', 'auth'],

    // ============ DEPARTMENTS ============
    ['GET', '/api/v1/departments', 'DepartmentController', 'index', 'auth'],
    ['POST', '/api/v1/departments', 'DepartmentController', 'store', 'auth'],
    ['GET', '/api/v1/departments/:id', 'DepartmentController', 'show', 'auth'],
    ['PUT', '/api/v1/departments/:id', 'DepartmentController', 'update', 'auth'],
    ['DELETE', '/api/v1/departments/:id', 'DepartmentController', 'destroy', 'auth'],

    // ============ EMPLOYEES ============
    ['GET', '/api/v1/employees', 'EmployeeController', 'index', 'auth'],
    ['POST', '/api/v1/employees', 'EmployeeController', 'store', 'auth'],
    ['GET', '/api/v1/employees/:id', 'EmployeeController', 'show', 'auth'],
    ['PUT', '/api/v1/employees/:id', 'EmployeeController', 'update', 'auth'],
    ['DELETE', '/api/v1/employees/:id', 'EmployeeController', 'destroy', 'auth'],

    // ============ PAYMENTS ============
    ['GET', '/api/v1/payments', 'PaymentController', 'index', 'auth'],
    ['POST', '/api/v1/payments', 'PaymentController', 'store', 'auth'],
    ['GET', '/api/v1/payments/:id', 'PaymentController', 'show', 'auth'],
    ['PUT', '/api/v1/payments/:id', 'PaymentController', 'update', 'auth'],
    ['DELETE', '/api/v1/payments/:id', 'PaymentController', 'destroy', 'auth'],

    // ============ DISCOUNTS ============
    ['GET', '/api/v1/discounts', 'DiscountController', 'index', 'auth'],
    ['POST', '/api/v1/discounts', 'DiscountController', 'store', 'auth'],
    ['GET', '/api/v1/discounts/:id', 'DiscountController', 'show', 'auth'],
    ['PUT', '/api/v1/discounts/:id', 'DiscountController', 'update', 'auth'],
    ['DELETE', '/api/v1/discounts/:id', 'DiscountController', 'destroy', 'auth'],

    // ============ ACCOUNTS ============
    ['GET', '/api/v1/accounts', 'AccountController', 'index', 'auth'],
    ['POST', '/api/v1/accounts', 'AccountController', 'store', 'auth'],
    ['GET', '/api/v1/accounts/:id', 'AccountController', 'show', 'auth'],
    ['PUT', '/api/v1/accounts/:id', 'AccountController', 'update', 'auth'],
    ['DELETE', '/api/v1/accounts/:id', 'AccountController', 'destroy', 'auth'],
];
?>
