<?php

namespace App\Services;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookService
{

    /**
     * Search for books by a given keyword.
     *
     * @param string $keyword
     * @return Collection
     */
    public function searchBooks(string $keyword): Collection
    {
        return Book::where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('isbn', 'LIKE', "%{$keyword}%")
                    ->get();
    }

    /**
     * Get all books.
     *
     * @return Collection
     */
    public function getAllBooks(): Collection
    {
        return Book::all();
    }

    /**
     * Get a book by its ID.
     *
     * @param int $id
     * @return Book|null
     */
    public function getBookById(int $id): ?Book
    {
        return Book::find($id);
    }

    /**
     * Create a new book.
     *
     * @param array $data
     * @return Book
     */
    public function createBook(array $data, int $owner_id): Book
    {
        return Book::create(array_merge($data, ['owner_id' => $owner_id]));
    }

    /**
     * Update an existing book.
     *
     * @param int $id
     * @param array $data
     * @return Book|null
     */
    public function updateBook(int $id, array $data): ?Book
    {
        $book = Book::find($id);
        if ($book) {
            $book->update($data);
        }
        return $book;
    }

    /**
     * Delete a book by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteBook(int $id): bool
    {
        $book = Book::find($id);
        if ($book) {
            return $book->delete();
        }
        return false;
    }


    public function getUserBooks(int $owner_id)
    {
        return Book::where('owner_id', $owner_id)->get();
    }
}