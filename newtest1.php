<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP Upload Excel File and Display Data</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <center>
        <h2>PHP Upload Excel file with multiple Sheets and Display Data</h2>
        <form action="newtest1.php" method="post" enctype="multipart/form-data">
            Select file: <input type="file" name="file_upload" />
            <input type="submit" value="Upload" />
        </form>
    </center>

    <?php
    // Ensure error reporting settings are appropriate for production
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

    if (isset($_FILES['file_upload'])) {
        require_once "Classes/PHPExcel.php";

        $dir = 'uploads/';
        $file_name = $_FILES['file_upload']['name'];
        $tmp_file_name = $_FILES['file_upload']['tmp_name'];

        if (move_uploaded_file($tmp_file_name, $dir . $file_name)) {
            echo '<center>File uploaded successfully.<br></center>';

            $path = $dir . $file_name;
            $reader = PHPExcel_IOFactory::createReaderForFile($path);
            $excel_Obj = $reader->load($path);
            $sheetCount = $excel_Obj->getSheetCount();

            // Initialize an array to store all sheet data
            $allData = [];

            for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
                $worksheet = $excel_Obj->getSheet($sheetIndex);
                $colomncount = $worksheet->getHighestDataColumn();
                $rowcount = $worksheet->getHighestRow();
                $colomncount_number = PHPExcel_Cell::columnIndexFromString($colomncount);

                // Initialize an array to store data for the current sheet
                $sheetData = [];

                // Iterate through rows and columns to fetch data
                for ($row = 1; $row <= $rowcount; $row++) {
                    $rowData = [];
                    for ($col = 0; $col < $colomncount_number; $col++) {
                        $cellValue = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                        if (is_numeric($cellValue)) {
                            $cellValue = (string) $cellValue;
                        }
                        $rowData[] = $cellValue;
                    }
                    $sheetData[] = $rowData;
                }

                // Add sheet data to the allData array
                $allData["Sheet " . ($sheetIndex + 1)] = $sheetData;
            }

            // Convert PHP array to JSON format
            $jsonData = json_encode($allData, JSON_PRETTY_PRINT);

            // Output JSON data
            echo '<pre>';
            echo '<h2>JSON Data:</h2>';
            echo htmlspecialchars($jsonData, ENT_QUOTES, 'UTF-8');
            echo '</pre>';
        } else {
            echo '<center>Error uploading file.</center>';
        }
    } else {
        echo '<center>No file selected.</center>';
    }
    ?>


</body>

</html>