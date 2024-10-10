<?php
session_start();

class Book {
    protected $title = [];
    protected $author = [];
    protected $publicationYear = [];

    public function __construct(array $title, array $author, array $publicationYear) {
        $this->title = $title;
        $this->author = $author;
        $this->publicationYear = $publicationYear;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setPublicationYear($publicationYear) {
        $this->publicationYear = $publicationYear;
    }

    public function getPublicationYear() {
        return $this->publicationYear;
    }
}

class Ebook extends Book {
    private $filesize = [];

    public function __construct(array $title, array $author, array $publicationYear, array $filesize) {
        parent::__construct($title, $author, $publicationYear);
        $this->filesize = $filesize;
    }

    public function setFileSize($filesize) {
        $this->filesize = $filesize;
    }

    public function getFileSize() {
        return $this->filesize;
    }

    public function getDetails() {
        $details = [];
        foreach ($this->title as $index => $title) {
            $details[] = $title . " " . $this->author[$index] . " " . $this->publicationYear[$index] . " " . $this->filesize[$index];
        }
        return $details;
    }
}

class Printedbook extends Book {
    private $numberpages = [];

    public function __construct(array $title, array $author, array $publicationYear, array $numberpages) {
        parent::__construct($title, $author, $publicationYear);
        $this->numberpages = $numberpages;
    }

    public function setNumberPages($numberpages) {
        $this->numberpages = $numberpages;
    }

    public function getNumberPages() {
        return $this->numberpages;
    }

    public function getDetails() {
        $details = [];
        foreach ($this->title as $index => $title) {
            $details[] = $title . " " . $this->author[$index] . " " . $this->publicationYear[$index] . " " . $this->numberpages[$index];
        }
        return $details;
    }
}

// Initialize session array if not already set
if (!isset($_SESSION['bookDetails'])) {
    $_SESSION['bookDetails'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = isset($_POST['formType']) ? $_POST['formType'] : '';

    if ($formType === 'ebook') {
        // Akses data dari form sebagai array
        $titles = $_POST['title'];
        $authors = $_POST['author'];
        $publicationYears = $_POST['publicationYear'];
        $filesizes = $_POST['filesize'];

        $ebook = new Ebook($titles, $authors, $publicationYears, $filesizes);
        $details = $ebook->getDetails();

        $_SESSION['bookDetails'] = array_merge($_SESSION['bookDetails'], $details);

    } elseif ($formType === 'printedbook') {
        // Akses data dari form sebagai array
        $titles = $_POST['title'];
        $authors = $_POST['author'];
        $publicationYears = $_POST['publicationYear'];
        $numberpages = $_POST['numberpages'];

        $printedbook = new Printedbook($titles, $authors, $publicationYears, $numberpages);
        $details = $printedbook->getDetails();

        $_SESSION['bookDetails'] = array_merge($_SESSION['bookDetails'], $details);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ebook & Printedbook Input Form</title>
    <script>
        let ebookCount = 0;
        let printedbookCount = 0;
        const maxForms = 100;

        function showForm(type) {
            document.getElementById('ebookForm').style.display = type === 'ebook' ? 'block' : 'none';
            document.getElementById('printedbookForm').style.display = type === 'printedbook' ? 'block' : 'none';
        }

        function addBook(type) {
            let count = type === 'ebook' ? ebookCount : printedbookCount;
            if (count >= maxForms) {
                alert("You've reached the maximum number of books.");
                return;
            }

            const container = document.getElementById(`${type}Container`);
            const div = document.createElement('div');
            div.innerHTML = `
                <h4>${type === 'ebook' ? 'Ebook' : 'Printed Book'} ${count + 1}</h4>
                <input type="text" name="title[]" required pattern="[A-Za-z ]+" maxlength="100" placeholder="Title">
                <input type="text" name="author[]" required pattern="[A-Za-z ]+" maxlength="100" placeholder="Author">
                <input type="number" name="publicationYear[]" required min="1500" max="2024" placeholder="Publication Year">
                <input type="number" name="${type === 'ebook' ? 'filesize[]' : 'numberpages[]'}" required min="1" max="10000" placeholder="${type === 'ebook' ? 'Filesize (MB)' : 'Number of Pages'}">
                <br><br>
            `;
            container.appendChild(div);

            if (type === 'ebook') {
                ebookCount++;
            } else {
                printedbookCount++;
            }
        }
    </script>
</head>
<body>
    <h2>Input Ebook or Printedbook Details</h2>

    <button onclick="showForm('ebook')">Ebook Form</button>
    <button onclick="showForm('printedbook')">Printedbook Form</button>

    <div id="ebookForm" style="display: none;">
        <form action="" method="POST">
            <input type="hidden" name="formType" value="ebook">
            <h3>Ebook</h3>
            <div id="ebookContainer"></div>
            <button type="button" onclick="addBook('ebook')">Add Another Ebook</button>
            <br><br>
            <input type="submit" value="Submit Ebooks">
        </form>
    </div>

    <div id="printedbookForm" style="display: none;">
        <form action="" method="POST">
            <input type="hidden" name="formType" value="printedbook">
            <h3>Printedbook</h3>
            <div id="printedbookContainer"></div>
            <button type="button" onclick="addBook('printedbook')">Add Another Printed Book</button>
            <br><br>
            <input type="submit" value="Submit Printed Books">
        </form>
    </div>

    <!-- Show book details if available -->
    <?php if (!empty($_SESSION['bookDetails'])): ?>
        <h3>Book Details:</h3>
        <ul>
        <?php foreach ($_SESSION['bookDetails'] as $detail): ?>
            <li><?php echo htmlspecialchars($detail); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
