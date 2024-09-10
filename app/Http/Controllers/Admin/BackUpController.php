<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use phpDocumentor\Reflection\Types\Null_;

class BackUpController extends Controller
{

    public function index()
    {

        $files = Storage::files('backups');


        $backups = [];

        foreach ($files as $file) {
            $size = Storage::size($file);
            $name = explode('/', $file)[1];
            $lastmodified = Storage::lastModified($file);
            $lastmodified = date("Y-m-d H:i:s", $lastmodified);
            $backups[] = [
                'file_path' => $file,
                'file_name' => $name,
                'file_size' =>$this->humanReadable($size)  ,
                'last_modified' =>  $lastmodified,
            ];
        }
        $backups = array_reverse($backups);
        return view("admin.backup.backup", compact('backups'));
    }

    public function create()
    {
        $this->make_database_backup();
        return redirect()->back()->with('success','You have successfully created the backup');
    }


    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    public function download($file_name)
    {

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $pathToFile = $disk->getDriver()->getAdapter()->getPathPrefix();

        if ($disk->exists($file_name)) {

            $pathToFile .= "/" . $file_name;
            return response()->download($pathToFile);
        } else {
            abort(404, "The backup file doesn't exist.");
        }
    }

    /**
     * Deletes a backup file.
     */
    public function delete($file_name)
    {
        Storage::disk('local')->delete('backups/' . $file_name);
        return redirect()->back()->with('success','You have successfully deleted the backup');
    }


    private function humanReadable($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function make_database_backup()
    {

        //ENTER THE RELEVANT INFO BELOW
        $mysqlHostName      = Config::get('database.connections.mysql.host');
        $mysqlUserName      = Config::get('database.connections.mysql.username');
        $mysqlPassword      =Config::get('database.connections.mysql.password');
        $DbName             =Config::get('database.connections.mysql.database');

        $file_name = Carbon::now()->format('Y-m-d-H-i-s');
        $tempPath = base_path('storage\app\backup-temp\\') .  $file_name;
        $backPath = base_path('storage\app\backups\\') . $file_name;
        ini_set('memory_limit', '-1');


        $DBH = new \PDO("mysql:host=$mysqlHostName;dbname=$DbName;charset=utf8", "$mysqlUserName", "$mysqlPassword", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $DBH->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
       

        $tables = array();


        $file_handle = fopen($tempPath, 'w+');



        //array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');

        //get all of the tables
        if (empty($tables)) {
            $statement = $DBH->query('SHOW TABLES');
            while ($row = $statement->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        //cycle through the table(s)

        

        foreach ($tables as $table) {
            $result = $DBH->query("SELECT * FROM $table");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();

            $return = "";
            //uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            $return.= 'DROP TABLE IF EXISTS `'.$table.'`;';

            //table structure
            $pstm2 = $DBH->query("SHOW CREATE TABLE $table");
            $row2 = $pstm2->fetch(\PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";

          

            fwrite($file_handle, $return);
         
            $return = "";

            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . $table . '` (';
                $pstm3 = $DBH->query("SHOW COLUMNS FROM $table");
                $count = 0;
                $type = array();

                while ($rows = $pstm3->fetch(\PDO::FETCH_NUM)) {
                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } else {
                        $type[$table][] = $rows[1];
                    }
                    $return .= "`" . $rows[0] . "`";
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return .= ", ";
                    }
                }

                $return .= ")" . ' VALUES';

             

                fwrite($file_handle, $return);
                $return = "";
            }
            $count = 0;
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $return = "\n\t(";

                for ($j = 0; $j < $num_fields; $j++) {

                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);

                    if (isset($row[$j])) {

                        //if number, take away "". else leave as string
                        if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) {
                            $return .= $row[$j];
                        } else {
                            $return .= $DBH->quote($row[$j]);
                        }
                    } else {
                        $return .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) {
                    $return .= "),";
                } else {
                    $return .= ");";
                }
                fwrite($file_handle, $return);
                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            fwrite($file_handle, $return);
            $return = "";
        }


        fclose($file_handle);

        // zipping 
        $zip = new ZipArchive;
        if ($zip->open($backPath . '.zip', ZipArchive::CREATE) === TRUE) {
            $zip->addFile($tempPath, $file_name.'.sql');
            $zip->close();
        }
        // clear temp folder
        $this->clearTempFolder();
    }

    private function clearTempFolder()
    {
        //The name of the folder.
        $folder = base_path('storage\app\backup-temp\\');

        //Get a list of all of the file names in the folder.
        $files = glob($folder . '/*');

        //Loop through the file list.
        foreach ($files as $file) {
            //Make sure that this is a file and not a directory.
            if (is_file($file)) {
                //Use the unlink function to delete the file.
                unlink($file);
            }
        }
    }
}
