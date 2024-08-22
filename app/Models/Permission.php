<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

     // user Permissions
         const ADD_USER = ['ACTION' => 'add', 'SUBJECT' => 'users'];
         const LIST_USER = ['ACTION' => 'list', 'SUBJECT' => 'users'];
         const EDIT_USER = ['ACTION' => 'edit', 'SUBJECT' => 'users'];
         const DELETE_USER = ['ACTION' => 'delete', 'SUBJECT' => 'users'];

     //client permissions
        const ADD_CLIENT = ['ACTION' => 'add', 'SUBJECT' => 'clients'];
        const LIST_CLIENT = ['ACTION' => 'list', 'SUBJECT' => 'clients'];
        const EDIT_CLIENT = ['ACTION' => 'edit', 'SUBJECT' => 'clients'];
        const DELETE_CLIENT = ['ACTION' => 'delete', 'SUBJECT' => 'clients'];

     //supplier permissions
        const ADD_SUPPLIER = ['ACTION' => 'add', 'SUBJECT' => 'suppliers'];
        const LIST_SUPPLIER = ['ACTION' => 'list', 'SUBJECT' => 'suppliers'];
        const EDIT_SUPPLIER = ['ACTION' => 'edit', 'SUBJECT' => 'suppliers'];
        const DELETE_SUPPLIER = ['ACTION' => 'delete', 'SUBJECT' => 'suppliers'];

    //employee permissions
        const ADD_EMPLOYEE = ['ACTION' => 'add', 'SUBJECT' => 'employees'];
        const LIST_EMPLOYEE = ['ACTION' => 'list', 'SUBJECT' => 'employees'];
        const EDIT_EMPLOYEE = ['ACTION' => 'edit', 'SUBJECT' => 'employees'];
        const DELETE_EMPLOYEE = ['ACTION' => 'delete', 'SUBJECT' => 'employees'];

    //product permissions
        const ADD_PRODUCT = ['ACTION' => 'add', 'SUBJECT' => 'products'];
        const LIST_PRODUCT = ['ACTION' => 'list', 'SUBJECT' => 'products'];
        const EDIT_PRODUCT = ['ACTION' => 'edit', 'SUBJECT' => 'products'];
        const DELETE_PRODUCT = ['ACTION' => 'delete', 'SUBJECT' => 'products'];

    //certify invoices permissions
        const ADD_CERTIFY_INVOICE = ['ACTION' => 'add', 'SUBJECT' => 'certify_invoices'];
        const LIST_CERTIFY_INVOICE = ['ACTION' => 'list', 'SUBJECT' => 'certify_invoices'];
        const EDIT_CERTIFY_INVOICE = ['ACTION' => 'edit', 'SUBJECT' => 'certify_invoices'];
        const DELETE_CERTIFY_INVOICE = ['ACTION' => 'delete', 'SUBJECT' => 'certify_invoices'];

    //sale permissions
        const ADD_SALE = ['ACTION' => 'add', 'SUBJECT' => 'sales'];
        const LIST_SALE = ['ACTION' => 'list', 'SUBJECT' => 'sales'];
        const EDIT_SALE = ['ACTION' => 'edit', 'SUBJECT' => 'sales'];
        const DELETE_SALE = ['ACTION' => 'delete', 'SUBJECT' => 'sales'];
    //return permissions
        const ADD_RETURN = ['ACTION' => 'add', 'SUBJECT' => 'returns'];
        const LIST_RETURN = ['ACTION' => 'list', 'SUBJECT' => 'returns'];
        const EDIT_RETURN = ['ACTION' => 'edit', 'SUBJECT' => 'returns'];
        const DELETE_RETURN = ['ACTION' => 'delete', 'SUBJECT' => 'returns'];
    //payment permissions
        const ADD_PAYMENT = ['ACTION' => 'add', 'SUBJECT' => 'payments'];
        const LIST_PAYMENT = ['ACTION' => 'list', 'SUBJECT' => 'payments'];
        const EDIT_PAYMENT = ['ACTION' => 'edit', 'SUBJECT' => 'payments'];
        const DELETE_PAYMENT = ['ACTION' => 'delete', 'SUBJECT' => 'payments'];

    //benefits permissions
        const ADD_BENEFIT = ['ACTION' => 'add', 'SUBJECT' => 'benefits'];
        const LIST_BENEFIT = ['ACTION' => 'list', 'SUBJECT' => 'benefits'];
        const EDIT_BENEFIT = ['ACTION' => 'edit', 'SUBJECT' => 'benefits'];
        const DELETE_BENEFIT = ['ACTION' => 'delete', 'SUBJECT' => 'benefits'];
    //vacation permissions
        const ADD_VACATION = ['ACTION' => 'add', 'SUBJECT' => 'vacations'];
        const LIST_VACATION = ['ACTION' => 'list', 'SUBJECT' => 'vacations'];
        const EDIT_VACATION = ['ACTION' => 'edit', 'SUBJECT' => 'vacations'];
        const DELETE_VACATION = ['ACTION' => 'delete', 'SUBJECT' => 'vacations'];
    //attendance permissions
        const ADD_ATTENDANCE = ['ACTION' => 'add', 'SUBJECT' => 'attendances'];
        const LIST_ATTENDANCE = ['ACTION' => 'list', 'SUBJECT' => 'attendances'];
        const EDIT_ATTENDANCE = ['ACTION' => 'edit', 'SUBJECT' => 'attendances'];
        const DELETE_ATTENDANCE = ['ACTION' => 'delete', 'SUBJECT' => 'attendances'];

    //role permissions
        const ADD_ROLE = ['ACTION' => 'add', 'SUBJECT' => 'roles'];
        const LIST_ROLE = ['ACTION' => 'list', 'SUBJECT' => 'roles'];
        const EDIT_ROLE = ['ACTION' => 'edit', 'SUBJECT' => 'roles'];
        const DELETE_ROLE = ['ACTION' => 'delete', 'SUBJECT' => 'roles'];

    //permissions
        const ADD_PERMISSION = ['ACTION' => 'add', 'SUBJECT' => 'permissions'];
        const LIST_PERMISSION = ['ACTION' => 'list', 'SUBJECT' => 'permissions'];
        const EDIT_PERMISSION = ['ACTION' => 'edit', 'SUBJECT' => 'permissions'];
        const DELETE_PERMISSION = ['ACTION' => 'delete', 'SUBJECT' => 'permissions'];

    //assets
        const ADD_ASSETS = ['ACTION' => 'add', 'SUBJECT' => 'assets'];
        const LIST_ASSETS = ['ACTION' => 'list', 'SUBJECT' => 'assets'];
        const EDIT_ASSETS = ['ACTION' => 'edit', 'SUBJECT' => 'assets'];
        const DELETE_ASSETS = ['ACTION' => 'delete', 'SUBJECT' => 'assets'];

    //components
        const ADD_COMPONENT = ['ACTION' => 'add', 'SUBJECT' => 'components'];
        const LIST_COMPONENT = ['ACTION' => 'list', 'SUBJECT' => 'components'];
        const EDIT_COMPONENT = ['ACTION' => 'edit', 'SUBJECT' => 'components'];
        const DELETE_COMPONENT = ['ACTION' => 'delete', 'SUBJECT' => 'components'];

    //maintenance
        const ADD_MAINTENANCE = ['ACTION' => 'add', 'SUBJECT' => 'maintenance'];
        const LIST_MAINTENANCE = ['ACTION' => 'list', 'SUBJECT' => 'maintenance'];
        const EDIT_MAINTENANCE = ['ACTION' => 'edit', 'SUBJECT' => 'maintenance'];
        const DELETE_MAINTENANCE = ['ACTION' => 'delete', 'SUBJECT' => 'maintenance'];
        const UPDATE_MAINTENANCE = ['ACTION' => 'update', 'SUBJECT' => 'maintenance'];
        const ASSIGN_MAINTENANCE = ['ACTION' => 'assign', 'SUBJECT' => 'maintenance'];

    //dashboard
        const ADMIN_DASHBOARD = ['ACTION' => 'admin', 'SUBJECT' => 'dashboard'];

    protected $fillable = [
        'action',
        'subject'
    ];

    // Consolidate all permissions into one constant
    const PERMISSIONS = [
        self::ADD_USER, self::LIST_USER, self::EDIT_USER, self::DELETE_USER,
        self::ADD_CLIENT, self::LIST_CLIENT, self::EDIT_CLIENT, self::DELETE_CLIENT,
        self::ADD_SUPPLIER, self::LIST_SUPPLIER, self::EDIT_SUPPLIER, self::DELETE_SUPPLIER,
        self::ADD_EMPLOYEE, self::LIST_EMPLOYEE, self::EDIT_EMPLOYEE, self::DELETE_EMPLOYEE,
        self::ADD_PRODUCT, self::LIST_PRODUCT, self::EDIT_PRODUCT, self::DELETE_PRODUCT,
        self::ADD_CERTIFY_INVOICE, self::LIST_CERTIFY_INVOICE, self::EDIT_CERTIFY_INVOICE, self::DELETE_CERTIFY_INVOICE,
        self::ADD_SALE, self::LIST_SALE, self::EDIT_SALE, self::DELETE_SALE,
        self::ADD_RETURN, self::LIST_RETURN, self::EDIT_RETURN, self::DELETE_RETURN,
        self::ADD_PAYMENT, self::LIST_PAYMENT, self::EDIT_PAYMENT, self::DELETE_PAYMENT,
        self::ADD_BENEFIT, self::LIST_BENEFIT, self::EDIT_BENEFIT, self::DELETE_BENEFIT,
        self::ADD_VACATION, self::LIST_VACATION, self::EDIT_VACATION, self::DELETE_VACATION,
        self::ADD_ATTENDANCE, self::LIST_ATTENDANCE, self::EDIT_ATTENDANCE, self::DELETE_ATTENDANCE,
        self::ADD_ROLE, self::LIST_ROLE, self::EDIT_ROLE, self::DELETE_ROLE,
        self::ADD_PERMISSION, self::LIST_PERMISSION, self::EDIT_PERMISSION, self::DELETE_PERMISSION,
        self::ADD_ASSETS, self::LIST_ASSETS, self::EDIT_ASSETS, self::DELETE_ASSETS,
        self::ADD_COMPONENT, self::LIST_COMPONENT, self::EDIT_COMPONENT, self::DELETE_COMPONENT,
        self::ADD_MAINTENANCE, self::LIST_MAINTENANCE, self::EDIT_MAINTENANCE, self::DELETE_MAINTENANCE,self::UPDATE_MAINTENANCE

    ];

    const ADMIN_PERMISSIONS = [
        self::ADD_USER, self::LIST_USER, self::EDIT_USER, self::DELETE_USER,
        self::ADD_CLIENT, self::LIST_CLIENT, self::EDIT_CLIENT, self::DELETE_CLIENT,
        self::ADD_SUPPLIER, self::LIST_SUPPLIER, self::EDIT_SUPPLIER, self::DELETE_SUPPLIER,
        self::ADD_EMPLOYEE, self::LIST_EMPLOYEE, self::EDIT_EMPLOYEE, self::DELETE_EMPLOYEE,
        self::ADD_PRODUCT, self::LIST_PRODUCT, self::EDIT_PRODUCT, self::DELETE_PRODUCT,
        self::ADD_CERTIFY_INVOICE, self::LIST_CERTIFY_INVOICE, self::EDIT_CERTIFY_INVOICE, self::DELETE_CERTIFY_INVOICE,
        self::ADD_SALE, self::LIST_SALE, self::EDIT_SALE, self::DELETE_SALE,
        self::ADD_RETURN, self::LIST_RETURN, self::EDIT_RETURN, self::DELETE_RETURN,
        self::ADD_PAYMENT, self::LIST_PAYMENT, self::EDIT_PAYMENT, self::DELETE_PAYMENT,
        self::ADD_BENEFIT, self::LIST_BENEFIT, self::EDIT_BENEFIT, self::DELETE_BENEFIT,
        self::ADD_VACATION, self::LIST_VACATION, self::EDIT_VACATION, self::DELETE_VACATION,
        self::ADD_ATTENDANCE, self::LIST_ATTENDANCE, self::EDIT_ATTENDANCE, self::DELETE_ATTENDANCE,
        self::ADD_ROLE, self::LIST_ROLE, self::EDIT_ROLE, self::DELETE_ROLE,
        self::ADD_PERMISSION, self::LIST_PERMISSION, self::EDIT_PERMISSION, self::DELETE_PERMISSION,
        self::ADD_ASSETS, self::LIST_ASSETS, self::EDIT_ASSETS, self::DELETE_ASSETS,
        self::ADD_COMPONENT, self::LIST_COMPONENT, self::EDIT_COMPONENT, self::DELETE_COMPONENT,
        self::ADD_MAINTENANCE, self::LIST_MAINTENANCE, self::EDIT_MAINTENANCE, self::DELETE_MAINTENANCE,self::UPDATE_MAINTENANCE, self::ASSIGN_MAINTENANCE,
        self::ADMIN_DASHBOARD
    ];
}
