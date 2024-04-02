<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    // Check if file is uploaded successfully
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $temp_file = $_FILES["file"]["tmp_name"];
        
        // Perform OCR
        $api_key = 'AIzaSyBHKC5ByGiE_nmW7sBXgCYb5sWJukVk-fM'; // Replace with your API key
        $endpoint_url = 'https://vision.googleapis.com/v1/images:annotate';
        
        $image_data = base64_encode(file_get_contents($temp_file));
        $request_data = array(
            'requests' => array(
                array(
                    'image' => array(
                        'content' => $image_data
                    ),
                    'features' => array(
                        array(
                            'type' => 'DOCUMENT_TEXT_DETECTION',
                            'maxResults' => 1
                        )
                    )
                )
            )
        );
        
        $json_data = json_encode($request_data);
        
        $curl = curl_init($endpoint_url . '?key=' . $api_key);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        
        $response = curl_exec($curl);
        
        if ($response === false) {
            echo "Error: cURL error: " . curl_error($curl);
        } else {
            // Process OCR result
            $result = json_decode($response, true);
            if ($result && isset($result['responses'][0]['textAnnotations'])) {
                $ocr_annotations = $result['responses'][0]['textAnnotations'];
                
                // Flag to track if "5" is encountered
                $five_flag = false;
                
                // Iterate over OCR annotations to find the first suitable number
                $first_number = null;
                foreach ($ocr_annotations as $annotation) {
                    $description = $annotation['description'];
                    
                    // Debugging: Print each annotation for inspection
                    echo "Annotation: " . $description . "<br>";
                    
                    // Check if description is "5"
                    if ($description === '5') {
                        $five_flag = true;
                        continue; // Skip this annotation and move to the next one
                    }
                    
                    // Check if description is a number (with or without decimal)
                    if (!$five_flag && is_numeric($description)) {
                        $first_number = $description;
                        break; // Stop iteration once the first number is found
                    }
                }
                
                // Return the first number found with a copy button and edit option
                if ($first_number !== null) {
                    echo "OCR Result: <span id='output' ondblclick='makeEditable()'>" . $first_number . "</span> ";
                    echo "<button onclick='copyOutput()'>Copy</button>";
                } else {
                    echo "Please Select Manually or crop digital image screen and try again";
                }
            } else {
                echo "Please Select Manually or crop digital image screen and try again";
            }
        }
        
        curl_close($curl);
    } else {
        echo "Error: File upload failed";
    }
} else {
    echo "Error: No file uploaded";
}
?>

<script>
function copyOutput() {
    var output = document.getElementById('output');
    var range = document.createRange();
    range.selectNode(output);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    alert("Copied to clipboard: " + output.innerText);
}

function makeEditable() {
    var output = document.getElementById('output');
    output.contentEditable = true;
    output.focus();
}
</script>
