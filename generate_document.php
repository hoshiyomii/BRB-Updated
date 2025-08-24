<?php
require 'vendor/autoload.php'; // Include PHPWord library

use PhpOffice\PhpWord\TemplateProcessor;

include 'db.php';

// Get the ID and document type from the request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'repair_and_construction') {
    // Fetch the data for the selected ID
    $sql = "SELECT 
                rc.id, 
                u.last_name, 
                u.first_name, 
                rc.contractor_name,
                rc.construction_address, 
                rc.contractor_contact,
                u.phone_number, 
                rc.activity_nature, 
                rc.created_at 
            FROM repair_and_construction rc
            JOIN users u ON rc.user_id = u.id
            WHERE rc.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/repair-and-construction.docx');

        // Replace placeholders with actual data
        $template->setValue('last_name', $row['last_name']);
        $template->setValue('first_name', $row['first_name']);
        $template->setValue('phone_number', $row['phone_number']);
        $template->setValue('contractor_name', $row['contractor_name']);
        $template->setValue('contractor_contact', $row['contractor_contact']);
        $template->setValue('construction_address', htmlspecialchars($row['construction_address'], ENT_QUOTES, 'UTF-8')); 
        $template->setValue('date', date("F j, Y, g:i a", strtotime($row['created_at']))); 

        $template->setValue('activity_nature_repairs', $row['activity_nature'] === 'Repairs' ? '☑' : '☐');
        $template->setValue('activity_nature_minor_construction', $row['activity_nature'] === 'Minor Construction' ? '☑' : '☐');
        $template->setValue('activity_nature_construction', $row['activity_nature'] === 'Construction' ? '☑' : '☐');
        $template->setValue('activity_nature_demolition', $row['activity_nature'] === 'Demolition' ? '☑' : '☐');
        
        // Save the document and output it to the browser
        $fileName = "Repair_and_Construction_Request_{$row['id']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} elseif ($type === 'work_permit_utilities') {
    $sql = "SELECT 
                wp.id, 
                u.last_name, 
                u.first_name, 
                wp.address, 
                wp.contact_no, 
                wp.nature_of_work, 
                wp.service_provider,
                wp.other_service_provider,
                wp.date_of_work, 
                wp.date_of_request,
                wp.created_at 
            FROM work_permit_utilities wp
            JOIN users u ON wp.user_id = u.id
            WHERE wp.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/work-permit-for-utilities.docx');

        // Replace placeholders with actual data
        $template->setValue('id', $row['id']);
        $template->setValue('last_name', $row['last_name']);
        $template->setValue('first_name', $row['first_name']);
        $template->setValue('address', htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('contact_no', htmlspecialchars($row['contact_no'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('other_service_provider', htmlspecialchars($row['other_service_provider'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('date_of_work', date("F j, Y", strtotime($row['date_of_work'])));
        $template->setValue('date_of_request', date("F j, Y", strtotime($row['date_of_request'])));

        $template->setValue('service_meralco', $row['service_provider'] === 'Meralco' ? '☑' : '☐');
        $template->setValue('service_globe', $row['service_provider'] === 'Globe' ? '☑' : '☐');
        $template->setValue('service_pldt', $row['service_provider'] === 'PLDT' ? '☑' : '☐');
        $template->setValue('service_skycable', $row['service_provider'] === 'Sky Cable' ? '☑' : '☐');
        $template->setValue('service_cignal', $row['service_provider'] === 'CIGNAL' ? '☑' : '☐');
        $template->setValue('service_manila_water', $row['service_provider'] === 'Manila Water' ? '☑' : '☐');
        $template->setValue('service_smart', $row['service_provider'] === 'Smart' ? '☑' : '☐');
        $template->setValue('service_bayantel', $row['service_provider'] === 'Bayantel' ? '☑' : '☐');
        $template->setValue('service_destiny', $row['service_provider'] === 'Destiny' ? '☑' : '☐');
        $template->setValue('service_others', $row['service_provider'] === 'Others' ? '☑' : '☐');

        $template->setValue('nature_new_installation', $row['nature_of_work'] === 'New installation' ? '☑' : '☐');
        $template->setValue('nature_repair_maintenance', $row['nature_of_work'] === 'Repair/Maintenance' ? '☑' : '☐');
        $template->setValue('nature_permanent_disconnection', $row['nature_of_work'] === 'Permanent Disconnection' ? '☑' : '☐');
        $template->setValue('nature_reconnection', $row['nature_of_work'] === 'Reconnection' ? '☑' : '☐');


        $fileName = "Work_Permit_Utilities_{$row['id']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} elseif ($type === 'certificate_of_residency') {
    $sql = "SELECT 
                u.last_name, 
                u.first_name, 
                u.house_number, 
                u.street,
                u.birthdate, 
                cr.resident_since,  
                cr.created_at 
            FROM certificate_of_residency cr
            JOIN users u ON cr.user_id = u.id
            WHERE cr.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/certificate-of-residency.docx');

        // Replace placeholders with actual data
        $template->setValue('last_name', htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('first_name', htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('street', htmlspecialchars($row['street'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('house_number', htmlspecialchars($row['house_number'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('birthdate', date("F j, Y", strtotime($row['birthdate'])));
        $template->setValue('resident_since', date("F j, Y", strtotime($row['resident_since'])));
        $template->setValue('date', date("F j, Y"));

        // Save the document and output it to the browser
        $fileName = "Certificate_of_Residency_{$row['last_name']}_{$row['first_name']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} elseif ($type === 'certificate_of_indigency') {
    $sql = "SELECT 
                u.last_name, 
                u.first_name, 
                u.house_number, 
                u.street, 
                ci.id, 
                ci.created_at 
            FROM certificate_of_indigency ci
            JOIN users u ON ci.user_id = u.id
            WHERE ci.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/certificate-of-indigency.docx');

        // Replace placeholders with actual data
        $template->setValue('last_name', htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('first_name', htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('id', $row['id']);
        $template->setValue('street', htmlspecialchars($row['street'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('house_number', htmlspecialchars($row['house_number'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('date', date("F j, Y"));

        // Save the document and output it to the browser
        $fileName = "Certificate_of_Indigency_{$row['last_name']}_{$row['first_name']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} elseif ($type === 'new_business_permit') {
    $sql = "SELECT 
                nbp.id, 
                nbp.owner, 
                nbp.location, 
                nbp.business_name, 
                nbp.nature_of_business, 
                nbp.created_at 
            FROM new_business_permit nbp
            WHERE nbp.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/new-business-permit.docx');

        // Replace placeholders with actual data
        $template->setValue('date', date("F j, Y", strtotime($row['created_at'])));
        $template->setValue('location', htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('nature_of_business', htmlspecialchars($row['nature_of_business'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('id', $row['id']);
        $template->setValue('owner', htmlspecialchars($row['owner'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('business_name', htmlspecialchars($row['business_name'], ENT_QUOTES, 'UTF-8'));

        // Save the document and output it to the browser
        $fileName = "New_Business_Permit_{$row['business_name']}_{$row['id']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} elseif ($type === 'clearance_major_construction') {
    $sql = "SELECT 
                cmc.id, 
                u.last_name, 
                u.first_name, 
                cmc.construction_address, 
                cmc.infrastructures, 
                cmc.created_at 
            FROM clearance_major_construction cmc
            JOIN users u ON cmc.user_id = u.id
            WHERE cmc.id = $id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Load the Word template
        $template = new TemplateProcessor('documents/clearance-for-major-construction.docx');

        // Replace placeholders with actual data
        $template->setValue('id', $row['id']);
        $template->setValue('date', date("F j, Y", strtotime($row['created_at'])));
        $template->setValue('last_name', htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('first_name', htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('construction_address', htmlspecialchars($row['construction_address'], ENT_QUOTES, 'UTF-8'));
        $template->setValue('infrastructures', htmlspecialchars($row['infrastructures'], ENT_QUOTES, 'UTF-8'));

        // Save the document and output it to the browser
        $fileName = "Clearance_for_Major_Construction_{$row['id']}.docx";
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        $template->saveAs('php://output');
        exit();
    } else {
        die("Record not found.");
    }
} else {
    die("Invalid document type.");
}
?>