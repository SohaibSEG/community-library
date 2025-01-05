<?php

namespace App\Http\Controllers\Api;

use App\Services\LendingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @group Book Lending Management
 * 
 * APIs for managing book lending operations including requests, approvals, and tracking
 */
class LendingController extends Controller
{
    protected $lendingService;

    public function __construct(LendingService $lendingService)
    {
        $this->lendingService = $lendingService;
    }

    /**
     * Request to Borrow a Book
     * 
     * Creates a new borrowing request for a specific book.
     * 
     * @urlParam bookId integer required The ID of the book to request. Example: 1
     * 
     */
    public function requestBook(Request $request, $bookId)
    {
        $borrower = Auth::user();
        $request = $this->lendingService->requestBook($borrower, $bookId);
        return response()->json($request, 201);
    }

    /**
     * Approve Book Request
     * 
     * Approves a pending book borrowing request.
     * 
     * @urlParam requestId integer required The ID of the lending request. Example: 1
     * 
     */
    public function approveRequest(Request $request, $requestId)
    {
        $lender = Auth::user();
        $lending = $this->lendingService->approveRequest($requestId, $lender->id);
        return response()->json($lending, 200);
    }

    /**
     * Reject Book Request
     * 
     * Rejects a pending book borrowing request.
     * 
     * @urlParam requestId integer required The ID of the lending request. Example: 1
     * 
     */
    public function rejectRequest(Request $request, $requestId)
    {
        $lender = Auth::user();
        $rejectedRequest = $this->lendingService->rejectRequest($requestId, $lender->id);
        return response()->json($rejectedRequest, 200);
    }

    /**
     * List Incoming Requests
     * 
     * Get all incoming book requests for the authenticated user.
     * 
     * @queryParam status string Filter requests by status (pending/approved/rejected). Example: pending
     * 
     */
    public function getIncomingRequests(Request $request)
    {
        $lender = Auth::user();
        $status = $request->get('status');
        $incomingRequests = $this->lendingService->getIncomingRequests($lender, $status);
        return response()->json($incomingRequests, 200);
    }

    /**
     * List Outgoing Requests
     * 
     * Get all outgoing book requests made by the authenticated user.
     * 
     * @queryParam status string Filter requests by status (pending/approved/rejected). Example: pending
     * 
     */
    public function getOutgoingRequests(Request $request)
    {
        $borrower = Auth::user();
        $status = $request->get('status');
        $outgoingRequests = $this->lendingService->getOutgoingRequests($borrower, $status);
        return response()->json($outgoingRequests, 200);
    }

    /**
     * List User's Lendings
     * 
     * Get all books currently being lent out by the authenticated user.
     * 
     */
    public function getUserLendings()
    {
        $user = Auth::user();
        $lendings = $this->lendingService->getUserLendings($user);
        return response()->json($lendings, 200);
    }

    /**
     * List User's Active Borrowings
     * 
     * Get all books currently borrowed by the authenticated user.
     * 
     */
    public function getUserBorrowings()
    {
        $user = Auth::user();
        $borrowings = $this->lendingService->getUserBorrowings($user);
        return response()->json($borrowings, 200);
    }

    /**
     * Mark Book as Returned
     * 
     * Mark a lending as returned by the book owner.
     * 
     * @urlParam lendingId integer required The ID of the lending. Example: 1
     * 
     */
    public function setLendingAsReturned(Request $request, int $lendingId)
    {
        $lender = Auth::user();
        $lending = $this->lendingService->setLendingAsReturned($lendingId, $lender->id);
        return response()->json($lending, 200);
    }
}
