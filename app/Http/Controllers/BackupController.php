<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function downloadBackup()
    {
        // 1. Setup Paths (Using Windows Temp)
        $filename = 'clinic_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $tempPath = sys_get_temp_dir() . '\\' . $filename; 
        $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

        // 2. Database Config
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        // 3. Build the Base Command
        // We use --result-file="path" instead of > "path"
        // This solves the quoting/redirection issues on Windows
        $baseCommand = "\"$mysqldumpPath\" --user=\"$dbUser\"";
        
        if (!empty($dbPassword)) {
            $baseCommand .= " --password=\"$dbPassword\"";
        }
        
        $baseCommand .= " --result-file=\"$tempPath\"";

        // 4. Attempt 1: Try WITH column-statistics (Modern XAMPP/MariaDB)
        $command = $baseCommand . " --column-statistics=0 \"$dbName\" 2>&1";

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        // 5. Attempt 2: If it failed, Try WITHOUT column-statistics (Older XAMPP/MySQL)
        if ($exitCode !== 0) {
            // Clear errors and try again
            $output = [];
            $command = $baseCommand . " \"$dbName\" 2>&1";
            exec($command, $output, $exitCode);
        }

        // 6. Final Validation
        if ($exitCode !== 0 || !file_exists($tempPath) || filesize($tempPath) === 0) {
            // If both attempts failed, show the error
            dd([
                'STATUS' => 'BACKUP FAILED',
                'Exit Code' => $exitCode,
                'Error Output' => $output, // This should now contain the actual error text
                'Command' => $command
            ]);
        }

        // 7. Download and Delete
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}