<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Lending;
use App\Models\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LendingService
{

    public function requestBook(User $borrower, int $book_id)
    {
        return DB::transaction(function () use ($borrower, $book_id) {
            $book = Book::where('id', $book_id)->lockForUpdate()->first();

            if ($book->isAvailable()) {
                $request = new Request();
                $request->borrower_id = $borrower->id;
                $request->lender_id = $book->owner_id;
                $request->no_of_days = 7;
                $request->book_id = $book->id;
                $request->status = Request::STATUS_PENDING;
                $request->save();

                return $request;
            } else {
                throw new NotFoundHttpException('Book is not available for lending.');
            }
        });
    }

    public function approveRequest(int $request_id, int $lender_id)
    {
        return DB::transaction(function () use ($request_id, $lender_id) {
            $request = Request::where('id', $request_id)
                ->where('lender_id', $lender_id)
                ->where('status', Request::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if ($request) {
                $request->status = Request::STATUS_APPROVED;
                $request->save();

                $lending = new Lending();
                $lending->book_id = $request->book_id;
                $lending->borrower_id = $request->borrower_id;
                $lending->lender_id = $request->lender_id;
                $lending->borrow_date = now();
                $lending->due_date = now()->addDays($request->no_of_days);
                $lending->save();

                $book = Book::where('id', $lending->book_id)->lockForUpdate()->first();
                $book->status = Book::STATUS_LENT;
                $book->save();

                return $lending;
            } else {
                throw new NotFoundHttpException('Request not found.');
            }
        });
    }


    public function rejectRequest(int $request_id, int $lender_id)
    {
        return DB::transaction(function () use ($request_id, $lender_id) {
            $request = Request::where('id', $request_id)
                ->where('lender_id', $lender_id)
                ->where('status', Request::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if ($request) {
                $request->status = Request::STATUS_REJECTED;
                $request->save();

                return $request;
            } else {
                throw new NotFoundHttpException('Request not found.');
            }
        });
    }

    public function getIncomingRequests(User $lender, $status = null)
    {
        $query = Request::where('lender_id', $lender->id);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->get();
    }

    public function getOutgoingRequests(User $borrower, $status = null)
    {
        $query = Request::where('borrower_id', $borrower->id);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->get();
    }

    public function getUserLendings(User $user)
    {
        return Lending::where('lender_id', $user->id)->with('book', 'lender', 'borrower')->get();
    }

    public function getUserBorrowings(User $user)
    {
        return Lending::where('borrower_id', $user->id)->get();
    }

    public function setLendingAsReturned(int $lending_id, int $user_id)
    {
        return DB::transaction(function () use ($lending_id, $user_id) {
            $lending = Lending::where('id', $lending_id)
                ->where('lender_id', $user_id)
                ->whereNull('return_date')
                ->lockForUpdate()
                ->first();

            if ($lending) {
                $lending->returned_at = now();
                $lending->save();

                $book = Book::where('id', $lending->book_id)->lockForUpdate()->first();
                $book->status = Book::STATUS_AVAILABLE;
                $book->save();

                return $lending;
            } else {
                throw new NotFoundHttpException('Lending not found.');
            }
        });
    }
}
