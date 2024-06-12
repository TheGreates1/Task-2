<?php
// Define the abstract parent class Library
abstract class Library {
    protected $id;
    
    // Abstract method to be implemented by child classes
    abstract public function add();
}

// Define the Author class
class Author {
    public $author_id;
    public $author_name;

    // Constructor to initialize author details
    public function __construct($author_id, $author_name) {
        $this->author_id = $author_id;
        $this->author_name = $author_name;
    }

    // Static method to create a new Author instance
    public static function CreateAuthor($author_id, $author_name) {
        return new self($author_id, $author_name);
    }
}

// Define the base class for library resources
class LibraryResource {
    public $resource_category;

    // Constructor to initialize resource category
    public function __construct($resource_category) {
        $this->resource_category = $resource_category;
    }
}

// Define the BookClass extending LibraryResource
class BookClass extends LibraryResource {
    public $id;
    public $book_name;
    public $book_isbn;
    public $book_publisher;
    public $author;

    // Constructor to initialize book details
    public function __construct($id, $book_name, $book_isbn, $book_publisher, $author) {
        parent::__construct("Book"); // Set resource category to "Book"
        $this->id = $id;
        $this->book_name = $book_name;
        $this->book_isbn = $book_isbn;
        $this->book_publisher = $book_publisher;
        $this->author = $author;
    }

    // Static method to create a new BookClass instance
    public static function Add($id, $book_name, $book_isbn, $book_publisher, $author) {
        return new self($id, $book_name, $book_isbn, $book_publisher, $author);
    }
}

// Define the OtherResource class extending LibraryResource
class OtherResource extends LibraryResource {
    public $id;
    public $res_name;
    public $res_des;
    public $res_brand;

    // Constructor to initialize other resource details
    public function __construct($id, $res_name, $res_des, $res_brand) {
        parent::__construct("Resource"); // Set resource category to "Resource"
        $this->id = $id;
        $this->res_name = $res_name;
        $this->res_des = $res_des;
        $this->res_brand = $res_brand;
    }

    // Static method to create a new OtherResource instance
    public static function Add($id, $res_name, $res_des, $res_brand) {
        return new self($id, $res_name, $res_des, $res_brand);
    }
}

// Define the main class for the library management system
class LibraryManagementSystem {
    private $booksFile = 'books.json'; // File to store books data
    private $resourcesFile = 'resources.json'; // File to store resources data
    private $books = []; // Array to store book objects
    private $resources = []; // Array to store resource objects

    // Constructor to load data from files
    public function __construct() {
        $this->loadData();
    }

    // Private method to load data from JSON files
    private function loadData() {
        if (file_exists($this->booksFile)) {
            $this->books = json_decode(file_get_contents($this->booksFile), true) ?? [];
            // Convert each book array to a BookClass object
            $this->books = array_map(function($book) {
                $author = new Author($book['author']['author_id'], $book['author']['author_name']);
                return new BookClass($book['id'], $book['book_name'], $book['book_isbn'], $book['book_publisher'], $author);
            }, $this->books);
        }
        if (file_exists($this->resourcesFile)) {
            $this->resources = json_decode(file_get_contents($this->resourcesFile), true) ?? [];
            // Convert each resource array to an OtherResource object
            $this->resources = array_map(function($resource) {
                return new OtherResource($resource['id'], $resource['res_name'], $resource['res_des'], $resource['res_brand']);
            }, $this->resources);
        }
    }

    // Private method to save data to JSON files
    private function saveData() {
        file_put_contents($this->booksFile, json_encode($this->books, JSON_PRETTY_PRINT));
        file_put_contents($this->resourcesFile, json_encode($this->resources, JSON_PRETTY_PRINT));
    }

    // Method to generate the list of books
    public function generateBookList() {
        return $this->books;
    }

    // Method to generate the list of resources
    public function generateResourceList() {
        return $this->resources;
    }

    // Method to add a new book
    public function addNewBook($id, $book_name, $book_isbn, $book_publisher, $author_id, $author_name) {
        $author = Author::CreateAuthor($author_id, $author_name);
        var_dump($author); // Debugging output
        $book = BookClass::Add($id, $book_name, $book_isbn, $book_publisher, $author);
        var_dump($book); // Debugging output
        $this->books[] = $book;
        $this->saveData();
    }

    // Method to delete a book by ID1
    public function deleteBook($id) {
        $this->books = array_filter($this->books, function($book) use ($id) {
            return $book->id != $id;
        });
        $this->saveData();
    }

    // Method to add a new resource
    public function addNewResource($id, $res_name, $res_des, $res_brand) {
        $resource = OtherResource::Add($id, $res_name, $res_des, $res_brand);
        $this->resources[] = $resource;
        $this->saveData();
    }

    // Method to delete a resource by ID
    public function deleteResource($id) {
        $this->resources = array_filter($this->resources, function($resource) use ($id) {
            return $resource->id != $id;
        });
        $this->saveData();
    }

    // Method to search for a book by ID
    public function searchBookById($id) {
        foreach ($this->books as $book) {
            var_dump($book); // Debugging output
            if ($book->id == $id) {
                return $book;
            }
        }
        return null;
    }

    // Method to sort books by name
    public function sortBooks($order = 'asc') {
        usort($this->books, function($a, $b) use ($order) {
            if ($order == 'asc') {
                return strcmp($a->book_name, $b->book_name);
            } else {
                return strcmp($b->book_name, $a->book_name);
            }
        });
        return $this->books;
    }

    // Method to exit the program
    public function exit() {
        exit("Exiting the program.\n");
    }
}

// CLI Interface for interacting with the LibraryManagementSystem
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
$library = new LibraryManagementSystem();

while (true) {
    echo "1. Generate Book list\n";
    echo "2. Generate Resource List\n";
    echo "3. Add new Book\n";
    echo "4. Delete Book\n";
    echo "5. Add new resource\n";
    echo "6. Delete Resource\n";
    echo "7. Search Book with ID\n";
    echo "8. Sort Book in Ascending order\n";
    echo "9. Sort Book Descending order\n";
    echo "10. Exit the program\n";
    echo "Enter your choice: ";
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case 1:
            print_r($library->generateBookList());
            break;
        case 2:
            print_r($library->generateResourceList());
            break;
        case 3:
            echo "Enter Book ID: ";
            $id = trim(fgets(STDIN));
            echo "Enter Book Name: ";
            $book_name = trim(fgets(STDIN));
            echo "Enter Book ISBN: ";
            $book_isbn = trim(fgets(STDIN));
            echo "Enter Book Publisher: ";
            $book_publisher = trim(fgets(STDIN));
            echo "Enter Author ID: ";
            $author_id = trim(fgets(STDIN));
            echo "Enter Author Name: ";
            $author_name = trim(fgets(STDIN));
            $library->addNewBook($id, $book_name, $book_isbn, $book_publisher, $author_id, $author_name);
            break;
        case 4:
            echo "Enter Book ID to delete: ";
            $id = trim(fgets(STDIN));
            $library->deleteBook($id);
            break;
        case 5:
            echo "Enter Resource ID: ";
            $id = trim(fgets(STDIN));
            echo "Enter Resource Name: ";
            $res_name = trim(fgets(STDIN));
            echo "Enter Resource Description: ";
            $res_des = trim(fgets(STDIN));
            echo "Enter Resource Brand: ";
            $res_brand = trim(fgets(STDIN));
            $library->addNewResource($id, $res_name, $res_des, $res_brand);
            break;
        case 6:
            echo "Enter Resource ID to delete: ";
            $id = trim(fgets(STDIN));
            $library->deleteResource($id);
            break;
        case 7:
            echo "Enter Book ID to search: ";
            $id = trim(fgets(STDIN));
            $result = $library->searchBookById($id);
            if ($result) {
                print_r($result);
            } else {
                echo "Book not found.\n";
            }
            break;
        case 8:
            print_r($library->sortBooks('asc'));
            break;
        case 9:
            print_r($library->sortBooks('desc'));
            break;
        case 10:
            $library->exit();
            break;
        default:
            echo "Invalid choice. Please try again.\n";
            break;
    }
}
}
?>
