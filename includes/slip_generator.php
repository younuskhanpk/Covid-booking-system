<?php
/**
 * Generate booking slip as PNG (PHP GD). No JavaScript.
 */
function slip_upload_dir(): string
{
    $dir = dirname(__DIR__) . '/uploads/slips';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

function slip_public_path(string $filename): string
{
    return '/eproject/uploads/slips/' . $filename;
}

/**
 * @return string|null Relative public path e.g. /eproject/uploads/slips/slip-12.png
 */
function generate_booking_slip_png(array $row, int $appointmentId): ?string
{
    if (!function_exists('imagecreatetruecolor')) {
        return null;
    }

    $w = 800;
    $h = 520;
    $im = imagecreatetruecolor($w, $h);
    if (!$im) {
        return null;
    }

    $bg = imagecolorallocate($im, 15, 23, 42);
    $white = imagecolorallocate($im, 255, 255, 255);
    $muted = imagecolorallocate($im, 148, 163, 184);
    $accent = imagecolorallocate($im, 14, 165, 233);
    $green = imagecolorallocate($im, 52, 211, 153);
    imagefill($im, 0, 0, $bg);

    imagefilledrectangle($im, 0, 0, $w, 90, $accent);
    imagestring($im, 5, 24, 28, 'VaxiCare Booking Slip', $white);

    $ref = 'VC-' . str_pad((string) $appointmentId, 8, '0', STR_PAD_LEFT);
    $lines = [
        'Reference: ' . $ref,
        'Patient: ' . ($row['patient_name'] ?? ''),
        'Hospital: ' . ($row['hospital_name'] ?? ''),
        'Location: ' . ($row['location'] ?? ''),
        'Service: ' . (($row['type'] ?? '') === 'Vaccination' ? 'COVID-19 Vaccination' : 'COVID-19 Test'),
        'Date: ' . date('F j, Y', strtotime($row['appointment_date'] ?? 'now')),
        'Status: ' . ($row['status'] ?? 'Pending'),
    ];
    if (!empty($row['vaccine_name'])) {
        $lines[] = 'Vaccine: ' . $row['vaccine_name'];
    }

    $y = 120;
    foreach ($lines as $line) {
        imagestring($im, 4, 32, $y, substr($line, 0, 70), $white);
        $y += 36;
    }

    imagestring($im, 3, 32, $h - 48, 'Present this slip at your appointment.', $muted);
    imagestring($im, 2, 32, $h - 28, date('Y-m-d H:i'), $green);

    $filename = 'slip-' . $appointmentId . '.png';
    $full = slip_upload_dir() . '/' . $filename;
    imagepng($im, $full);
    imagedestroy($im);

    return slip_public_path($filename);
}

function save_appointment_slip(PDO $conn, int $appointmentId, string $publicPath): void
{
    $stmt = $conn->prepare('UPDATE appointments SET slip_image = :p WHERE id = :id');
    $stmt->execute([':p' => $publicPath, ':id' => $appointmentId]);
}
