<?php
// Database connection
$servername = "localhost";
$username = "root";  // Replace with your database username
$password = "";  // Replace with your database password
$dbname = "travelDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input data
$name = filter_var($_POST['name']);
$email = filter_var($_POST['email']);
$phone = filter_var($_POST['phone']);
$address = filter_var($_POST['address']);
$location = filter_var($_POST['location']);
$guests = filter_var($_POST['guests']);
$arrivals = $_POST['arrivals'];
$leaving = $_POST['leaving'];
$package = filter_var($_POST['package']);
$price = filter_var($_POST['price']);

if (!$email) {
    echo "<script>alert('Invalid email address.'); window.location.href = 'book.php';</script>";
    exit();
}

if (!$guests || $guests < 1) {
    echo "<script>alert('Number of guests must be a positive integer.'); window.location.href = 'book.php';</script>";
    exit();
}

// Check if arrival date is before leaving date
if (strtotime($arrivals) > strtotime($leaving)) {
    echo "<script>alert('Arrival date must be before leaving date.'); window.location.href = 'book.php';</script>";
    exit();
}

// Generate a unique booking number
$bookingNumber = strtoupper(bin2hex(random_bytes(6)));  // 12-character unique code

// Calculate total price
$totalPrice = $price * $guests;

// Insert data into the database
$stmt = $conn->prepare("INSERT INTO bookings (booking_number, name, email, phone, address, location, guests, arrivals, leaving, package, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssd", $bookingNumber, $name, $email, $phone, $address, $location, $guests, $arrivals, $leaving, $package, $totalPrice);

if ($stmt->execute()) {
    // Prepare the PDF invoice
    require('fpdf186/fpdf.php'); // Ensure this path is correct

    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Local Duluas Travel Agency', 0, 1, 'C');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 10, '123 Travel Lane, City, Country', 0, 1, 'C');
            $this->Cell(0, 10, 'Phone: +123 456 7890 | Email: contact@localduluas.com', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function ChapterBody($title, $details)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, $title, 0, 1, 'L');
            $this->Ln(5);
            $this->SetFont('Arial', '', 12);
            foreach ($details as $key => $value) {
                $this->Cell(0, 10, "$key: $value", 0, 1);
            }
            $this->Ln(10);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
    $pdf->Ln(10);

    $details = [
        'Booking Number' => $bookingNumber,
        'Name' => $name,
        'Email' => $email,
        'Phone' => $phone,
        'Address' => $address,
        'Location' => $location,
        'Guests' => $guests,
        'Arrival Date' => $arrivals,
        'Leaving Date' => $leaving,
        'Package' => $package,
        'Total Price' => "$$totalPrice"
    ];

    $pdf->ChapterBody('Booking Details', $details);

    // Define the path to the 'temp' directory
    $tempDir = __DIR__ . '/temp/';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true); // Create directory with permissions if it doesn't exist
    }

    $pdfFileName = "invoice_$bookingNumber.pdf";
    $pdfPath = $tempDir . $pdfFileName;

    try {
        $pdf->Output('F', $pdfPath);

        // Provide download link or redirect with a modal
        echo "
        <script>
        function downloadPDF() {
            window.location.href = 'temp/$pdfFileName';
        }
        window.onload = function() {
            document.getElementById('modal').style.display = 'block';
        }
        </script>
        <style>
        #modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        #modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        </style>
        <div id='modal'>
            <div id='modal-content'>
                <h2>Congratulations!</h2>
                <p>You have successfully booked a package.</p>
                <button onclick='downloadPDF()'>Download Invoice</button>
            </div>
        </div>
        ";
        exit();
    } catch (Exception $e) {
        echo "Error generating PDF: " . $e->getMessage();
    }
} else {
    echo "<script>alert('There was an error processing your booking. Please try again.'); window.location.href = 'book.php';</script>";
}

$stmt->close();
$conn->close();
?>
