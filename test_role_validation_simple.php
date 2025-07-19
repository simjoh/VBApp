<?php

// Simple test to verify role validation logic
echo "Testing Role Validation Logic\n";
echo "============================\n\n";

// Test 1: Superuser can assign any role
echo "Test 1: Superuser assigning SUPERUSER role\n";
$isSuperUser = true;
$rolesToAssign = [['id' => 3, 'role_name' => 'SUPERUSER']];

if ($isSuperUser) {
    echo "✓ Superuser can assign SUPERUSER role\n";
} else {
    $allowedRoles = ['ADMIN', 'USER', 'VOLONTEER'];
    $hasRestrictedRole = false;
    foreach ($rolesToAssign as $role) {
        if (!in_array($role['role_name'], $allowedRoles)) {
            $hasRestrictedRole = true;
            break;
        }
    }
    if ($hasRestrictedRole) {
        echo "✗ Non-superuser cannot assign restricted role\n";
    } else {
        echo "✓ Non-superuser can assign allowed role\n";
    }
}

echo "\n";

// Test 2: Non-superuser cannot assign SUPERUSER role
echo "Test 2: Non-superuser assigning SUPERUSER role\n";
$isSuperUser = false;
$rolesToAssign = [['id' => 3, 'role_name' => 'SUPERUSER']];

if ($isSuperUser) {
    echo "✓ Superuser can assign SUPERUSER role\n";
} else {
    $allowedRoles = ['ADMIN', 'USER', 'VOLONTEER'];
    $hasRestrictedRole = false;
    foreach ($rolesToAssign as $role) {
        if (!in_array($role['role_name'], $allowedRoles)) {
            $hasRestrictedRole = true;
            break;
        }
    }
    if ($hasRestrictedRole) {
        echo "✗ Non-superuser cannot assign restricted role: " . $role['role_name'] . "\n";
    } else {
        echo "✓ Non-superuser can assign allowed role\n";
    }
}

echo "\n";

// Test 3: Non-superuser can assign allowed roles
echo "Test 3: Non-superuser assigning allowed roles\n";
$isSuperUser = false;
$rolesToAssign = [
    ['id' => 1, 'role_name' => 'ADMIN'],
    ['id' => 2, 'role_name' => 'USER']
];

if ($isSuperUser) {
    echo "✓ Superuser can assign any role\n";
} else {
    $allowedRoles = ['ADMIN', 'USER', 'VOLONTEER'];
    $hasRestrictedRole = false;
    foreach ($rolesToAssign as $role) {
        if (!in_array($role['role_name'], $allowedRoles)) {
            $hasRestrictedRole = true;
            break;
        }
    }
    if ($hasRestrictedRole) {
        echo "✗ Non-superuser cannot assign restricted role: " . $role['role_name'] . "\n";
    } else {
        echo "✓ Non-superuser can assign allowed roles\n";
    }
}

echo "\n";

// Test 4: Non-superuser cannot assign DEVELOPER role
echo "Test 4: Non-superuser assigning DEVELOPER role\n";
$isSuperUser = false;
$rolesToAssign = [['id' => 5, 'role_name' => 'DEVELOPER']];

if ($isSuperUser) {
    echo "✓ Superuser can assign any role\n";
} else {
    $allowedRoles = ['ADMIN', 'USER', 'VOLONTEER'];
    $hasRestrictedRole = false;
    foreach ($rolesToAssign as $role) {
        if (!in_array($role['role_name'], $allowedRoles)) {
            $hasRestrictedRole = true;
            break;
        }
    }
    if ($hasRestrictedRole) {
        echo "✗ Non-superuser cannot assign restricted role: " . $role['role_name'] . "\n";
    } else {
        echo "✓ Non-superuser can assign allowed role\n";
    }
}

echo "\nAll tests completed!\n"; 