<?php
namespace App\Http\Controllers\Api;

use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @group Book Management
 * 
 * APIs for managing books including CRUD operations and search functionality
 */
class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * List Books
     * 
     * Display a listing of all available books.
     */
    public function index()
    {
        $books = $this->bookService->getAllBooks();
        return response()->json($books);
    }

    /**
     * Create Book
     * 
     * Store a newly created book in the system.
     * 
     * @bodyParam title string required The title of the book. Example: The Great Gatsby
     * @bodyParam author string The author of the book. Example: F. Scott Fitzgerald
     * @bodyParam isbn string unique The ISBN of the book (max 20 chars). Example: 978-0743273565
     * @bodyParam cover_image string URL of the book cover image. Example: https://example.com/cover.jpg
     * @bodyParam description string A description of the book.
     * @bodyParam genre string The genre of the book (max 100 chars). Example: Fiction
     * @bodyParam published_year integer The year the book was published (between 1000 and current year). Example: 1925
     * @bodyParam condition string required The condition of the book (New/Good/Fair). Example: Good
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn|max:20',
            'cover_image' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:100',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'condition' => 'required|in:New,Good,Fair',
        ]);

        $book = $this->bookService->createBook($data, Auth::user()->id);
        return response()->json($book, 201);
    }

    /**
     * Get Book Details
     * 
     * Display the details of a specific book.
     * 
     * @urlParam id integer required The ID of the book. Example: 1
     */
    public function show($id)
    {
        $book = $this->bookService->getBookById($id);
        return $book ? response()->json($book) : response()->json(['error' => 'Book not found'], 404);
    }

    /**
     * Update Book
     * 
     * Update the details of a specific book.
     * 
     * @urlParam id integer required The ID of the book. Example: 1
     * @bodyParam title string The title of the book. Example: The Great Gatsby
     * @bodyParam author string The author of the book. Example: F. Scott Fitzgerald
     * @bodyParam isbn string unique The ISBN of the book (max 20 chars). Example: 978-0743273565
     * @bodyParam cover_image string URL of the book cover image. Example: https://example.com/cover.jpg
     * @bodyParam description string A description of the book.
     * @bodyParam genre string The genre of the book (max 100 chars). Example: Fiction
     * @bodyParam published_year integer The year the book was published (between 1000 and current year). Example: 1925
     * @bodyParam condition string The condition of the book (New/Good/Fair). Example: Good
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $id . '|max:20',
            'cover_image' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:100',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'condition' => 'nullable|in:New,Good,Fair',
        ]);

        $book = $this->bookService->updateBook($id, $data);
        return $book ? response()->json($book) : response()->json(['error' => 'Book not found'], 404);
    }

    /**
     * Delete Book
     * 
     * Remove a specific book from the system.
     * 
     * @urlParam id integer required The ID of the book to delete. Example: 1
     */
    public function destroy($id)
    {
        $deleted = $this->bookService->deleteBook($id);
        return $deleted ? response()->json(['message' => 'Book deleted successfully']) : response()->json(['error' => 'Book not found'], 404);
    }

    /**
     * Search Books
     * 
     * Search for books using a keyword.
     * 
     * @bodyParam keyword string required The search keyword (max 255 chars). Example: Gatsby
     */
    public function search(Request $request)
    {
        $keyword = $request->validate([
            'keyword' => 'required|string|max:255',
        ])['keyword'];

        $books = $this->bookService->searchBooks($keyword);
        return response()->json($books);
    }

    /**
     * List User's Books
     * 
     * Get all books owned by the authenticated user.
     */
    public function getUserBooks()
    {
        $user = Auth::user();
        $books = $this->bookService->getUserBooks($user->id);
        return response()->json($books);
    }
}