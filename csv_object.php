<?php

//Response array declaration
$response = array(
    'status' => 0,
    'message' => 'File submission failed, please try again',
    'data' => '',
);

if (isset($_FILES['csv_file']))
{
    // Upload file
    if (!empty($_FILES["csv_file"]["name"]))
    {
        // Get File type config
        $fileName = basename($_FILES["csv_file"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('csv');

        if (in_array($fileType, $allowTypes))
        {
            $response['status'] = 1;
            $response['message'] = 'File Processed Successfully';

            // Pass file to function for processing
            $csv_obj = new CsvFile($_FILES["csv_file"]["tmp_name"]);
            $response['data'] = $csv_obj->csv_file_read();
        }
        else
        {
            $response['message'] = 'Sorry, only CSV files are allowed to upload.';
        }
    }
}

// Return response
echo json_encode($response);

class CsvFile
{
    private $filename;

    //Constructor to setting $filename
    function __construct($filename)
    {
        $this->filename = $filename;
    }

    //Function to read CSV file contents
    function csv_file_read(): string
    {
        $data = $fields = array();
        $i = 0;

        //Open Csv file in read mode
        $handle = @fopen($this->filename, "r");

        if ($handle)
        {
            //Read each row of CSV file
            while (($row = fgetcsv($handle, 4096)) !== false)
            {
                if (empty($fields))
                {
                    $fields = $row;
                    continue;
                }

                foreach ($row as $k => $value)
                {
                    $data[$i][$fields[$k]] = $value;
                }
                $i++;
            }

            if (!feof($handle))
            {
                return "Error: unexpected fgets() fail\n";
            }

            // Close file handler
            fclose($handle);
        }

        //Sorting data in ascending order of date
        usort($data, array(new DateCompare($fields[0]), 'call'));

        //Return CSV file in the form table
        return $this->print_html_table($data, $fields);
    }

    //Put csv data into table
    function print_html_table($data, $fields): string
    {
        $tran_verify_obj = new TransactionVerify();
        $table = '<div class="table-responsive"><table class="table table-striped table-hover"><thead><tr><th scope="col">Date</th><th scope="col">Transaction Code</th><th scope="col">Valid Transaction?</th><th scope="col">Customer Number</th><th scope="col">Reference</th><th scope="col">Amount</th></tr></thead><tbody>';

        for ($i = 0; $i < count($data); $i++)
        {
            $table .= '<tr><td>' . $data[$i][$fields[0]] . '</td><td>' . $data[$i][$fields[1]] . '</td>';

            if ($tran_verify_obj->verify_key($data[$i][$fields[1]]))
            {
                $table .= '<td class="text-success">Yes</td>';
            }
            else
            {
                $table .= '<td class="text-danger">No</td>';
            }

            $table .= '<td>' . $data[$i][$fields[2]] . '</td><td>' . $data[$i][$fields[3]] . '</td>';
            $amount = ($data[$i][$fields[4]] / 100);

            if ($amount < 0)
            {
                $table .= '<td class="text-danger">-$' . ltrim($amount, '-') . '</td>';
            }
            else
            {
                $table .= '<td class="text-success">$' . $amount . '</td>';
            }

            $table .= '</tr>';
        }

        $table .= '</tbody></table></div>';
        return $table;
    }
}

//classback to pass 'Date' key to compare method as it is not recognizing "Date" key
class DateCompare
{
    private $date;

    function __construct($date)
    {
        $this->date = $date;
    }

    function call($a, $b)
    {
        return $this->date_compare($a, $b, $this->date);
    }

    // Comparison function for date and time
    function date_compare($element1, $element2, $date)
    {
        $datetime1 = strtotime($element1[$date]);
        $datetime2 = strtotime($element2[$date]);

        return $datetime1 - $datetime2;
    }
}

//validating transaction number using algorithm
class TransactionVerify
{
    private $validChars = ['2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    function verify_key($key): bool
    {
        if (strlen($key) != 10)
        {
            return false;
        }

        $input = substr(strtoupper($key), 0, 9);
        $checkDigit = $this->generate_check_character($input);

        return $key[9] == $checkDigit;
    }

    // Implementation of algorithm for check digit.
    function generate_check_character($input): string
    {
        $factor = 2;
        $sum = 0;
        $n = count($this->validChars);

        // Starting from the right and working leftwards is easier since
        // the initial "factor" will always be "2"
        for ($i = (strlen($input) - 1); $i >= 0; $i--)
        {
            $codePoint = array_search($input[$i], $this->validChars);
            $addend = $factor * $codePoint;

            // Alternate the "factor" that each "codePoint" is multiplied by
            $factor = ($factor == 2) ? 1 : 2;

            // Sum the digits of the "addend" as expressed in base "n"
            $addend = ($addend / $n) + ($addend % $n);
            $sum += $addend;
        }

        // Calculate the number that must be added to the "sum"
        // to make it divisible by "n"
        $remainder = $sum % $n;
        $checkCodePoint = ($n - $remainder) % $n;

        return $this->validChars[$checkCodePoint];
    }
}
