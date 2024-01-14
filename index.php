<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel 변환기</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 500px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="file"] {
            border: 1px solid #ccc;
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        #dataResult {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 500px;
            margin: 0 auto;
        }

        #dataResult h2 {
            color: #333;
        }
    </style>
</head>
<body>
<h1>Excel 변환기</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <label>Excel 파일 선택:</label>
    <input type="file" name="excel_file" accept=".xls, .xlsx">
    <br>
    <label>변환 옵션 선택:</label>
    <select name="conversion_option" id="conversion_option">
        <option value="convert_txt_view">텍스트로 변환</option>
        <option value="convert_txt_file">텍스트 파일로 저장</option>
    </select>

    <div id="space_option_container" style="display: none;">
        <label>옵션 숫자 입력:</label>
        <input type="number" name="space_option" id="space_option" min="1">
    </div>

    <script>
        document.getElementById("conversion_option").addEventListener("change", function() {
            var selectedOption = this.value;
            var spaceOptionContainer = document.getElementById("space_option_container");

            if (selectedOption === "convert_txt_file") {
                spaceOptionContainer.style.display = "block";
            } else {
                spaceOptionContainer.style.display = "none";
            }
        });
    </script>

    <br>
    <input type="submit" value="업로드 및 변환">
</form>
<br>

<div id="dataResult">
    <h2>결과</h2>
    <?php
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    if(isset($_FILES['excel_file']) && isset($_POST['conversion_option'])){
        $conversion_option = $_POST['conversion_option'];
        $space_option = $_POST['space_option'];

        if ($conversion_option === 'convert_txt_view') {
            $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);

            $worksheet = $spreadsheet->getActiveSheet();

            $data = $worksheet->toArray();

            echo "<table>";
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . $cell . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }

        elseif ($conversion_option === 'convert_txt_file') {
            $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);

            $worksheet = $spreadsheet->getActiveSheet();

            $data = $worksheet->toArray();

            $output_file = date("Y-m-d") . '.txt';

            $output_text = "";
            $columnCount = count($data[0]);

            for ($i = 0; $i < $columnCount; $i++) {
                foreach ($data as $row) {
                    $output_text .= $row[$i] . "\n";
                }
                $output_text .= str_repeat("\n", $space_option);
            }
            file_put_contents($output_file, $output_text);

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . basename($output_file) . '"');
            header('Content-Length: ' . filesize($output_file));
            readfile($output_file);
            exit;
        }
    }
    ?>
</div>
<br>
<footer>
    &copy; 2024 github.com/julie-min
</footer>
</body>
</html>