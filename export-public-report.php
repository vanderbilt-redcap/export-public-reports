<?php

$row = $module->query(
	'select * from redcap_reports where is_public = 1 and hash = ?',
	$_GET['reportHash']
)->fetch_assoc();

if ($row === null) {
	die('A public report for this hash was not found.');
}

$filename = str_replace('"', "'", $row['title']) . ' - ' . date('Y-m-d H-i-s') . '.csv';

header("Content-Disposition: attachment; filename=\"$filename\"");
header('Expires: 0');

echo REDCap::getReport($row['report_id'], 'csv');
