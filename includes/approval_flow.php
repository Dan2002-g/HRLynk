<?php
if (!function_exists('getApprovalFlow')) {
    function getApprovalFlow($userRoleId, $userOffice) {
        $supervisorRoles = [1, 4, 5, 7, 9, 10, 12];
        $hrmdRoles = [3, 4];

        $approvalFlow = [];
        
        // First approver: College Dean (supervisor from same office)
        $approvalFlow[] = [
            'role' => 9, // College Dean role ID
            'office' => $userOffice,
            'order' => 1
        ];
        
        // ...existing code for other approvers...
        
        return $approvalFlow;
    }
}

if (!function_exists('initializeApprovalFlow')) {
    function initializeApprovalFlow($trainingId, $userRoleId, $userOffice, $conn) {
        $approvalFlow = getApprovalFlow($userRoleId, $userOffice);
        
        foreach ($approvalFlow as $step) {
            $stmt = $conn->prepare("
                INSERT INTO training_approvals 
                (training_id, approver_role, approver_office, approval_order) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $trainingId,
                $step['role'],
                $step['office'],
                $step['order']
            ]);
        }
    }
}

if (!function_exists('getCurrentApprover')) {
    function getCurrentApprover($trainingId, $conn) {
        $stmt = $conn->prepare("
            SELECT * FROM training_approvals 
            WHERE training_id = ? 
            AND status = 'pending' 
            ORDER BY approval_order ASC 
            LIMIT 1
        ");
        
        $stmt->execute([$trainingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('canUserApprove')) {
    function canUserApprove($userRoleId, $userOffice, $trainingId, $conn) {
        $currentApprover = getCurrentApprover($trainingId, $conn);
        
        if (!$currentApprover) {
            return false;
        }
        
        return $userRoleId == $currentApprover['approver_role'] && 
               $userOffice == $currentApprover['approver_office'];
    }
}