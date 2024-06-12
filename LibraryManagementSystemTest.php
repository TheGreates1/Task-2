<?php

use PHPUnit\Framework\TestCase;

// Ensure the class is included
require_once 'LibraryManagementSystem.php';

class LibraryManagementSystemTest extends TestCase
{
    private $library;

    protected function setUp(): void
    {
        // Clear data in books.json and resources.json before each test
        file_put_contents('books.json', json_encode([]));
        file_put_contents('resources.json', json_encode([]));
        
        $this->library = new LibraryManagementSystem();
    }

    public function testAddNewBook()
    {
        // Arrange
        $id = 1;
        $book_name = "Test Book";
        $book_isbn = "123-456-789";
        $book_publisher = "Test Publisher";
        $author_id = 1;
        $author_name = "Test Author";

        // Act
        $this->library->addNewBook($id, $book_name, $book_isbn, $book_publisher, $author_id, $author_name);
        $books = $this->library->generateBookList();

        // Assert
        $this->assertCount(1, $books);
        $this->assertEquals($id, $books[0]->id);
        $this->assertEquals($book_name, $books[0]->book_name);
        $this->assertEquals($book_isbn, $books[0]->book_isbn);
        $this->assertEquals($book_publisher, $books[0]->book_publisher);
        $this->assertEquals($author_id, $books[0]->author->author_id);
        $this->assertEquals($author_name, $books[0]->author->author_name);
    }

    public function testDeleteBook()
    {
        // Arrange
        $id = 1;
        $book_name = "Test Book";
        $book_isbn = "123-456-789";
        $book_publisher = "Test Publisher";
        $author_id = 1;
        $author_name = "Test Author";

        // Add a book first to delete it later
        $this->library->addNewBook($id, $book_name, $book_isbn, $book_publisher, $author_id, $author_name);

        // Act
        $this->library->deleteBook($id);
        $books = $this->library->generateBookList();

        // Assert
        $this->assertCount(0, $books);
    }
}
