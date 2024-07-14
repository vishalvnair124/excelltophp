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
        <form action="newtest.php" method="post" enctype="multipart/form-data">
            Select file: <input type="file" name="file_upload" />
            <input type="submit" value="Upload" />
        </form>
    </center>

    <?php
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

            for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
                $worksheet = $excel_Obj->getSheet($sheetIndex);
                $colomncount = $worksheet->getHighestDataColumn();
                $rowcount = $worksheet->getHighestRow();
                $colomncount_number = PHPExcel_Cell::columnIndexFromString($colomncount);

                echo "<center><h3>Sheet " . ($sheetIndex + 1) . "</h3></center>";
                echo '<table>';
                echo '<tr>';
                for ($col = 0; $col < $colomncount_number; $col++) {
                    $header = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . '1')->getValue();
                    echo '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
                }
                echo '</tr>';

                for ($row = 2; $row <= $rowcount; $row++) {
                    echo '<tr>';
                    for ($col = 0; $col < $colomncount_number; $col++) {
                        $cellValue = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                        if (is_numeric($cellValue)) {
                            $cellValue = (string) $cellValue;
                        }
                        echo '<td>' . htmlspecialchars($cellValue, ENT_QUOTES, 'UTF-8') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            }
        } else {
            echo '<center>Error uploading file.</center>';
        }
    } else {
        echo '<center>No file selected.</center>';
    }
    ?>
</body>

</html>